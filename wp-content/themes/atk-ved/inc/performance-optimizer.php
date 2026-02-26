<?php
/**
 * Performance Optimizer - Оптимизация производительности
 *
 * @package ATK_VED
 * @since 3.2.0
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Класс для оптимизации производительности
 */
class ATK_VED_Performance_Optimizer {

    private static ?self $instance = null;

    public static function get_instance(): self {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Lazy loading изображений
        add_filter( 'wp_get_attachment_image_attributes', [ $this, 'add_lazy_loading_to_images' ], 10, 3 );
        
        // Оптимизация шрифтов
        add_action( 'wp_enqueue_scripts', [ $this, 'optimize_fonts_loading' ], 20 );
        
        // Удаление ненужных скриптов и стилей
        add_action( 'wp_enqueue_scripts', [ $this, 'dequeue_unnecessary_assets' ], 999 );
        
        // Оптимизация HTML
        add_action( 'template_redirect', [ $this, 'init_html_optimization' ] );
        
        // Подключение асинхронных скриптов
        add_filter( 'script_loader_tag', [ $this, 'make_scripts_async_defer' ], 10, 3 );
        
        // Lazy loading iframe
        add_filter( 'embed_oembed_html', [ $this, 'lazy_load_iframes' ], 10, 4 );
        
        // Оптимизация базы данных (кэширование)
        add_action( 'init', [ $this, 'enable_object_caching' ] );
    }

    /**
     * Добавление lazy loading к изображениям
     */
    public function add_lazy_loading_to_images( array $attr, WP_Post $attachment, string $size ): array {
        // Не применять lazy loading к логотипу и другим важным изображениям
        if ( isset( $attr['class'] ) && strpos( $attr['class'], 'skip-lazy' ) !== false ) {
            return $attr;
        }

        // Добавляем lazy loading ко всем изображениям
        $attr['loading'] = 'lazy';
        $attr['decoding'] = 'async';

        return $attr;
    }

    /**
     * Оптимизация загрузки шрифтов
     */
    public function optimize_fonts_loading(): void {
        // Удаляем стандартные шрифты WordPress
        wp_dequeue_style( 'wp-block-library' );
        wp_dequeue_style( 'wp-block-library-theme' );

        // Добавляем предварительную загрузку шрифтов
        add_action( 'wp_head', function() {
            $font_preloads = [
                get_template_directory_uri() . '/fonts/Inter-Regular.woff2',
                get_template_directory_uri() . '/fonts/Inter-Medium.woff2',
                get_template_directory_uri() . '/fonts/Inter-Bold.woff2',
            ];

            foreach ( $font_preloads as $font_url ) {
                printf(
                    '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
                    esc_url( $font_url )
                );
            }
        }, 1 );
    }

    /**
     * Удаление ненужных скриптов и стилей
     */
    public function dequeue_unnecessary_assets(): void {
        // Удаляем эмодзи
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'admin_print_styles', 'print_emoji_styles' );

        // Удаляем лишние RSS-ссылки
        remove_action( 'wp_head', 'feed_links_extra', 3 );
        remove_action( 'wp_head', 'rsd_link' );
        remove_action( 'wp_head', 'wlwmanifest_link' );
        remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
        remove_action( 'wp_head', 'wp_generator' );
        remove_action( 'wp_head', 'rest_output_link_wp_head' );
        remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
        remove_action( 'wp_head', 'wp_shortlink_wp_head' );
    }

    /**
     * Инициализация оптимизации HTML
     */
    public function init_html_optimization(): void {
        ob_start( [ $this, 'minify_html_output' ] );
    }

    /**
     * Минификация HTML-вывода
     */
    public function minify_html_output( string $buffer ): string {
        // Не минифицировать в режиме отладки
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            return $buffer;
        }

        // Удаление комментариев HTML (кроме условных комментариев IE)
        $buffer = preg_replace( '/<!--(?!\s*(?:\[if [^\]]+]|<!|>))\s*.*?-->/s', '', $buffer );

        // Удаление лишних пробелов и переносов строк
        $buffer = preg_replace( '/\s+/', ' ', $buffer );
        $buffer = trim( $buffer );

        return $buffer;
    }

    /**
     * Сделать скрипты асинхронными или отложенными
     */
    public function make_scripts_async_defer( string $tag, string $handle, string $src ): string {
        // Не изменять inline-скрипты
        if ( empty( $src ) ) {
            return $tag;
        }

        // Добавить defer к определенным скриптам
        $defer_scripts = [
            'jquery',
            'jquery-core',
            'wp-embed',
        ];

        if ( in_array( $handle, $defer_scripts, true ) ) {
            if ( strpos( $tag, 'defer' ) === false ) {
                $tag = str_replace( ' src=', ' defer src=', $tag );
            }
        }

        // Добавить async к сторонним скриптам
        $async_scripts = [
            'google-analytics',
            'facebook-sdk',
            'twitter-widgets',
        ];

        foreach ( $async_scripts as $async_script ) {
            if ( strpos( $handle, $async_script ) !== false ) {
                if ( strpos( $tag, 'async' ) === false ) {
                    $tag = str_replace( ' src=', ' async src=', $tag );
                }
            }
        }

        return $tag;
    }

    /**
     * Lazy loading для iframe
     */
    public function lazy_load_iframes( string $html, string $url, array $attr, int $post_id ): string {
        if ( strpos( $html, '<iframe' ) !== false ) {
            // Заменяем src на data-src для lazy loading
            $html = str_replace( 'src="', 'data-src="', $html );
            $html = str_replace( '<iframe', '<iframe loading="lazy"', $html );
        }

        return $html;
    }

    /**
     * Включение объектного кэширования
     */
    public function enable_object_caching(): void {
        // Устанавливаем время жизни кэша для часто используемых данных
        if ( ! defined( 'WP_CACHE' ) ) {
            define( 'WP_CACHE', true );
        }
    }

    /**
     * Оптимизация изображений (вывод с различными размерами)
     */
    public function get_optimized_image( int $attachment_id, array $sizes, string $class = '' ): string {
        $image_src = wp_get_attachment_image_src( $attachment_id, 'full' );
        if ( ! $image_src ) {
            return '';
        }

        $srcset = [];
        foreach ( $sizes as $size ) {
            $size_src = wp_get_attachment_image_src( $attachment_id, $size );
            if ( $size_src ) {
                $srcset[] = $size_src[0] . ' ' . $size_src[1] . 'w';
            }
        }

        $srcset_str = implode( ', ', $srcset );
        $alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );

        return sprintf(
            '<img src="%s" srcset="%s" sizes="(max-width: 768px) 100vw, 50vw" alt="%s" class="%s" loading="lazy">',
            esc_url( $image_src[0] ),
            esc_attr( $srcset_str ),
            esc_attr( $alt ),
            esc_attr( $class )
        );
    }

    /**
     * Подключение оптимизированных скриптов
     */
    public function enqueue_optimized_scripts(): void {
        // Подключаем Intersection Observer для lazy loading (для старых браузеров)
        wp_enqueue_script(
            'intersection-observer',
            get_template_directory_uri() . '/js/intersection-observer.js',
            [],
            '0.10.0',
            true
        );

        // Подключаем скрипт lazy loading
        wp_enqueue_script(
            'atk-lazy-loader',
            get_template_directory_uri() . '/js/lazy-loader.js',
            [ 'jquery' ],
            ATK_VED_VERSION,
            true
        );
    }
}

// Инициализация
function atk_ved_init_performance_optimizer(): void {
    ATK_VED_Performance_Optimizer::get_instance();
}
add_action( 'after_setup_theme', 'atk_ved_init_performance_optimizer' );