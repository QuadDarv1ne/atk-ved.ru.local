<?php
/**
 * Базовый класс темы АТК ВЭД.
 *
 * @package ATKVed
 */

declare( strict_types=1 );

namespace ATKVed;

/**
 * Базовый класс с константами и утилитами.
 */
final class Base {

    /**
     * Версия темы.
     */
    public const VERSION = '3.3.0';

    /**
     * Путь к директории темы.
     */
    public static function dir(): string {
        return get_template_directory();
    }

    /**
     * URI темы.
     */
    public static function uri(): string {
        return get_template_directory_uri();
    }

    /**
     * Путь к директории src.
     */
    public static function src_dir(): string {
        return self::dir() . '/src';
    }

    /**
     * Защита от прямого доступа.
     */
    public static function guard(): void {
        if ( ! defined( 'ABSPATH' ) ) {
            exit;
        }
    }
}
