<?php
/**
 * Главный класс темы - объединяет все модули.
 *
 * @package ATKVed
 */

declare( strict_types=1 );

namespace ATKVed;

/**
 * Основной класс темы.
 */
final class Theme {

    /**
     * Экземпляр класса.
     */
    private static ?Theme $instance = null;

    /**
     * Модули темы.
     */
    private array $modules = [];

    /**
     * Получение экземпляра (Singleton).
     *
     * @return static
     */
    public static function get_instance(): self {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Конструктор - инициализация модулей.
     */
    private function __construct() {
        $this->init_modules();
        $this->register_hooks();
    }

    /**
     * Инициализация модулей.
     *
     * @return void
     */
    private function init_modules(): void {
        $this->modules = [
            'setup'      => new Setup(),
            'enqueue'    => new Enqueue(),
            'ajax'       => new Ajax(),
            'shortcodes' => new Shortcodes(),
            'customizer' => new Customizer(),
        ];
    }

    /**
     * Регистрация хуков WordPress.
     *
     * @return void
     */
    private function register_hooks(): void {
        // Инициализация всех модулей
        foreach ( $this->modules as $module ) {
            if ( method_exists( $module, 'init' ) ) {
                $module->init();
            }
        }

        // Дополнительные хуки
        add_action( 'init', [ $this, 'cleanup_head' ] );
        add_filter( 'wp_get_attachment_image_attributes', [ $this, 'optimize_images' ] );
        add_action( 'template_redirect', [ $this, 'minify_html' ] );
    }

    /**
     * Очистка <head>.
     *
     * @return void
     */
    public function cleanup_head(): void {
        remove_action( 'wp_head', 'wp_generator' );
        remove_action( 'wp_head', 'wlwmanifest_link' );
        remove_action( 'wp_head', 'rsd_link' );
        remove_action( 'wp_head', 'wp_shortlink_wp_head' );
        remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
    }

    /**
     * Оптимизация атрибутов изображений.
     *
     * @param array $attr Атрибуты.
     *
     * @return array
     */
    public function optimize_images( array $attr ): array {
        $attr['loading']  ??= 'lazy';
        $attr['decoding'] ??= 'async';
        return $attr;
    }

    /**
     * Минификация HTML.
     *
     * @return void
     */
    public function minify_html(): void {
        if (
            is_admin()
            || ( defined( 'DOING_AJAX' ) && DOING_AJAX )
            || ( defined( 'DOING_CRON' ) && DOING_CRON )
            || ( defined( 'REST_REQUEST' ) && REST_REQUEST )
            || ( defined( 'WP_DEBUG' ) && WP_DEBUG )
        ) {
            return;
        }

        ob_start( function( string $buffer ): string {
            // Сохраняем содержимое <pre> и <script>/<style>
            $preserved = [];
            $buffer = preg_replace_callback(
                '#(<(?:pre|script|style)[^>]*>)(.*?)(</(?:pre|script|style)>)#si',
                function( $matches ) use ( &$preserved ): string {
                    $key = '<!--PRESERVE_' . count( $preserved ) . '-->';
                    $preserved[ $key ] = $matches[0];
                    return $key;
                },
                $buffer
            );

            // Убираем HTML-комментарии (кроме IE-условных)
            $buffer = preg_replace( '/<!--(?!\[if)(.|\s)*?-->/', '', $buffer );
            // Сжимаем пробелы
            $buffer = preg_replace( [ '/\s{2,}/', '/>\s+</' ], [ ' ', '><' ], $buffer );

            // Восстанавливаем сохранённые блоки
            return strtr( $buffer, $preserved );
        } );
    }

    /**
     * Получение данных компании.
     *
     * @return array
     */
    public static function get_company_info(): array {
        static $info = null;

        if ( null === $info ) {
            $founded = 2018;
            $years   = (int) date( 'Y' ) - $founded;

            $info = [
                'name'        => 'АТК ВЭД',
                'description' => __( 'Товары для маркетплейсов из Китая оптом. Полный цикл работы от поиска до доставки с гарантией качества.', 'atk-ved' ),
                'founded'     => $founded,
                'years'       => $years,
                'deliveries'  => 1000,
                'rating'      => 4.9,
            ];
        }

        return $info;
    }

    /**
     * Получение социальных ссылок.
     *
     * @return array
     */
    public static function get_social_links(): array {
        static $links = null;

        if ( null === $links ) {
            $keys  = [ 'whatsapp', 'telegram', 'vk', 'instagram', 'youtube' ];
            $links = [];

            foreach ( $keys as $key ) {
                $val = get_theme_mod( 'atk_ved_' . $key, '' );
                if ( $val ) {
                    $links[ $key ] = esc_url( $val );
                }
            }
        }

        return $links;
    }

    /**
     * Получение trust-badges.
     *
     * @return array
     */
    public static function get_trust_badges(): array {
        $info = self::get_company_info();

        return [
            [ 'icon' => 'trophy', 'text' => $info['years'] . ' ' . self::pluralize( $info['years'], 'год', 'года', 'лет' ) . ' ' . __( 'на рынке', 'atk-ved' ) ],
            [ 'icon' => 'truck',  'text' => $info['deliveries'] . '+ ' . __( 'доставок', 'atk-ved' ) ],
            [ 'icon' => 'star',   'text' => $info['rating'] . '/5 ' . __( 'рейтинг', 'atk-ved' ) ],
            [ 'icon' => 'shield', 'text' => __( 'Гарантия качества', 'atk-ved' ) ],
        ];
    }

    /**
     * Склонение числительных.
     *
     * @param int    $n     Число.
     * @param string $one   Форма 1.
     * @param string $few   Форма 2-4.
     * @param string $many  Форма 5+.
     *
     * @return string
     */
    public static function pluralize( int $n, string $one, string $few, string $many ): string {
        $mod10  = $n % 10;
        $mod100 = $n % 100;

        if ( $mod100 >= 11 && $mod100 <= 19 ) {
            return $many;
        }

        if ( $mod10 === 1 ) {
            return $one;
        }

        if ( $mod10 >= 2 && $mod10 <= 4 ) {
            return $few;
        }

        return $many;
    }

    /**
     * Проверка URL на безопасность.
     *
     * @param string $url URL для проверки.
     *
     * @return bool
     */
    public static function is_safe_url( string $url ): bool {
        $scheme = parse_url( $url, PHP_URL_SCHEME );
        return in_array( strtolower( (string) $scheme ), [ 'https', 'http', 'tg' ], true );
    }
}
