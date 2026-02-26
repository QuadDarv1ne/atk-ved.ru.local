<?php

declare(strict_types=1);

namespace ATKVed;

use ATKVed\Core\Container;
use ATKVed\Services\CompanyService;

/**
 * Главный класс темы - объединяет все модули
 *
 * @package ATKVed
 * @since   3.5.0
 */
final class Theme
{
    /**
     * Экземпляр класса (Singleton)
     *
     * @var self|null
     */
    private static ?self $instance = null;

    /**
     * DI контейнер
     *
     * @var Container
     */
    private Container $container;

    /**
     * Модули темы
     *
     * @var array<string, object>
     */
    private array $modules = [];

    /**
     * Получение экземпляра (Singleton)
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Конструктор - инициализация темы
     */
    private function __construct()
    {
        $this->container = new Container();
        $this->registerServices();
        $this->initModules();
        $this->registerHooks();
    }

    /**
     * Регистрация сервисов в контейнере
     *
     * @return void
     */
    private function registerServices(): void
    {
        // Регистрация сервиса компании
        $this->container->register('company', function () {
            return new CompanyService();
        });

        // Регистрация других сервисов
        $this->container->register('enqueue', function () {
            return new Enqueue();
        });

        $this->container->register('setup', function () {
            return new Setup();
        });

        $this->container->register('ajax', function () {
            return new Ajax();
        });

        $this->container->register('shortcodes', function () {
            return new Shortcodes();
        });

        $this->container->register('customizer', function () {
            return new Customizer();
        });
    }

    /**
     * Инициализация модулей
     *
     * @return void
     */
    private function initModules(): void
    {
        $moduleIds = ['setup', 'enqueue', 'ajax', 'shortcodes', 'customizer'];

        foreach ($moduleIds as $id) {
            $this->modules[$id] = $this->container->get($id);
        }
    }

    /**
     * Регистрация хуков WordPress
     *
     * @return void
     */
    private function registerHooks(): void
    {
        // Инициализация всех модулей
        foreach ($this->modules as $module) {
            if (method_exists($module, 'init')) {
                $module->init();
            }
        }

        // Дополнительные хуки
        add_action('init', [$this, 'cleanupHead']);
        add_filter('wp_get_attachment_image_attributes', [$this, 'optimizeImages']);
        
        // Минификация HTML только на продакшене
        if (!WP_DEBUG) {
            add_action('template_redirect', [$this, 'minifyHtml']);
        }
    }

    /**
     * Очистка <head> от лишних тегов
     *
     * @return void
     */
    public function cleanupHead(): void
    {
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wp_shortlink_wp_head');
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
        remove_action('wp_head', 'feed_links_extra', 3);
    }

    /**
     * Оптимизация атрибутов изображений
     *
     * @param array<string, mixed> $attr Атрибуты изображения
     * @return array<string, mixed>
     */
    public function optimizeImages(array $attr): array
    {
        $attr['loading']  ??= 'lazy';
        $attr['decoding'] ??= 'async';
        
        return $attr;
    }

    /**
     * Минификация HTML
     *
     * @return void
     */
    public function minifyHtml(): void
    {
        if ($this->shouldSkipMinification()) {
            return;
        }

        ob_start(function (string $buffer): string {
            return $this->minifyBuffer($buffer);
        });
    }

    /**
     * Проверка, нужно ли пропустить минификацию
     *
     * @return bool
     */
    private function shouldSkipMinification(): bool
    {
        return is_admin()
            || (defined('DOING_AJAX') && DOING_AJAX)
            || (defined('DOING_CRON') && DOING_CRON)
            || (defined('REST_REQUEST') && REST_REQUEST)
            || (defined('WP_DEBUG') && WP_DEBUG);
    }

    /**
     * Минификация буфера HTML
     *
     * @param string $buffer HTML буфер
     * @return string
     */
    private function minifyBuffer(string $buffer): string
    {
        // Сохраняем содержимое <pre>, <script>, <style>
        $preserved = [];
        $buffer = preg_replace_callback(
            '#(<(?:pre|script|style)[^>]*>)(.*?)(</(?:pre|script|style)>)#si',
            function ($matches) use (&$preserved): string {
                $key = '<!--PRESERVE_' . count($preserved) . '-->';
                $preserved[$key] = $matches[0];
                return $key;
            },
            $buffer
        ) ?? $buffer;

        // Убираем HTML-комментарии (кроме IE-условных)
        $buffer = preg_replace('/<!--(?!\[if)(.|\s)*?-->/', '', $buffer) ?? $buffer;
        
        // Сжимаем пробелы
        $buffer = preg_replace(['/\s{2,}/', '/>\s+</'], [' ', '><'], $buffer) ?? $buffer;

        // Восстанавливаем сохранённые блоки
        return strtr($buffer, $preserved);
    }

    /**
     * Получение сервиса из контейнера
     *
     * @param string $id Идентификатор сервиса
     * @return mixed
     */
    public function get(string $id): mixed
    {
        return $this->container->get($id);
    }

    /**
     * Получение данных компании
     *
     * @return array<string, mixed>
     */
    public static function getCompanyInfo(): array
    {
        return self::getInstance()->get('company')->getInfo();
    }

    /**
     * Получение социальных ссылок
     *
     * @return array<string, string>
     */
    public static function getSocialLinks(): array
    {
        return self::getInstance()->get('company')->getSocialLinks();
    }

    /**
     * Получение trust badges
     *
     * @return array<int, array<string, string>>
     */
    public static function getTrustBadges(): array
    {
        return self::getInstance()->get('company')->getTrustBadges();
    }
}
