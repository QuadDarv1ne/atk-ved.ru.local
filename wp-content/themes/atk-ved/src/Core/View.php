<?php

declare(strict_types=1);

namespace ATKVed\Core;

use ATKVed\Contracts\ViewInterface;

/**
 * Класс для работы с представлениями (views)
 *
 * @package ATKVed\Core
 * @since   3.5.0
 */
final class View implements ViewInterface
{
    /**
     * Путь к директории с шаблонами
     *
     * @var string
     */
    private string $viewsPath;

    /**
     * Конструктор
     *
     * @param string $viewsPath Путь к директории с шаблонами
     */
    public function __construct(string $viewsPath)
    {
        $this->viewsPath = rtrim($viewsPath, '/');
    }

    /**
     * Рендеринг представления
     *
     * @param array<string, mixed> $data Данные для представления
     * @return string
     */
    public function render(array $data = []): string
    {
        ob_start();
        $this->display($data);
        return ob_get_clean() ?: '';
    }

    /**
     * Отображение представления
     *
     * @param array<string, mixed> $data Данные для представления
     * @return void
     */
    public function display(array $data = []): void
    {
        // Извлекаем переменные в локальную область видимости
        extract($data, EXTR_SKIP);

        // Подключаем шаблон
        $templatePath = $this->viewsPath . '.php';
        
        if (file_exists($templatePath)) {
            include $templatePath;
        }
    }

    /**
     * Загрузка частичного представления
     *
     * @param string               $partial Имя частичного представления
     * @param array<string, mixed> $data    Данные для представления
     * @return string
     */
    public function partial(string $partial, array $data = []): string
    {
        $partialPath = get_template_directory() . '/views/partials/' . $partial . '.php';
        
        if (!file_exists($partialPath)) {
            return '';
        }

        extract($data, EXTR_SKIP);
        
        ob_start();
        include $partialPath;
        return ob_get_clean() ?: '';
    }

    /**
     * Экранирование HTML
     *
     * @param string $string Строка для экранирования
     * @return string
     */
    public static function escape(string $string): string
    {
        return esc_html($string);
    }

    /**
     * Экранирование атрибута
     *
     * @param string $string Строка для экранирования
     * @return string
     */
    public static function escapeAttr(string $string): string
    {
        return esc_attr($string);
    }

    /**
     * Экранирование URL
     *
     * @param string $url URL для экранирования
     * @return string
     */
    public static function escapeUrl(string $url): string
    {
        return esc_url($url);
    }
}
