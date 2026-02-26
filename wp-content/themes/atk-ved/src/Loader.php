<?php
/**
 * Загрузчик темы - инициализирует Composer autoloader и запускает тему.
 *
 * @package ATKVed
 */

declare( strict_types=1 );

namespace ATKVed;

/**
 * Загрузчик темы.
 */
final class Loader {

    /**
     * Путь к autoloader Composer.
     */
    private const COMPOSER_AUTOLOAD = '/vendor/autoload.php';

    /**
     * Инициализация загрузчика.
     *
     * @return void
     */
    public static function init(): void {
        // Защита от прямого доступа
        Base::guard();

        // Подключаем Composer autoloader
        self::load_composer_autoloader();

        // Запускаем тему
        Theme::get_instance();
    }

    /**
     * Подключение Composer autoloader.
     *
     * @return void
     */
    private static function load_composer_autoloader(): void {
        $autoload_path = Base::src_dir() . self::COMPOSER_AUTOLOAD;

        if ( file_exists( $autoload_path ) ) {
            require_once $autoload_path;
        }
    }
}
