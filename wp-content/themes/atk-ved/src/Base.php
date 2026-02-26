<?php

declare(strict_types=1);

namespace ATKVed;

/**
 * Базовый класс темы с константами и утилитами
 *
 * @package ATKVed
 * @since   3.5.0
 */
final class Base
{
    /**
     * Версия темы
     *
     * @var string
     */
    public const VERSION = '3.5.0';

    /**
     * Минимальная версия PHP
     *
     * @var string
     */
    public const MIN_PHP_VERSION = '8.1';

    /**
     * Минимальная версия WordPress
     *
     * @var string
     */
    public const MIN_WP_VERSION = '6.0';

    /**
     * Путь к директории темы
     *
     * @return string
     */
    public static function dir(): string
    {
        return get_template_directory();
    }

    /**
     * URI темы
     *
     * @return string
     */
    public static function uri(): string
    {
        return get_template_directory_uri();
    }

    /**
     * Путь к директории src
     *
     * @return string
     */
    public static function srcDir(): string
    {
        return self::dir() . '/src';
    }

    /**
     * Путь к директории views
     *
     * @return string
     */
    public static function viewsDir(): string
    {
        return self::dir() . '/views';
    }

    /**
     * Путь к директории assets
     *
     * @return string
     */
    public static function assetsDir(): string
    {
        return self::dir() . '/assets';
    }

    /**
     * Защита от прямого доступа
     *
     * @return void
     */
    public static function guard(): void
    {
        if (!defined('ABSPATH')) {
            exit;
        }
    }

    /**
     * Проверка минимальных требований
     *
     * @return bool
     */
    public static function meetsRequirements(): bool
    {
        return version_compare(PHP_VERSION, self::MIN_PHP_VERSION, '>=')
            && version_compare(get_bloginfo('version'), self::MIN_WP_VERSION, '>=');
    }

    /**
     * Получение версии темы
     *
     * @return string
     */
    public static function version(): string
    {
        return defined('ATK_VED_VERSION') ? ATK_VED_VERSION : self::VERSION;
    }
}
