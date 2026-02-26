<?php
/**
 * Управление скриптами и стилями.
 *
 * @package ATKVed
 */

declare( strict_types=1 );

namespace ATKVed;

/**
 * Подключение и локализация скриптов/стилей.
 */
class Enqueue {

    /**
     * Инициализация хуков.
     */
    public function init(): void {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        add_action( 'wp_head', [ $this, 'preload_resources' ], 1 );
    }

    /**
     * Подключение скриптов и стилей.
     *
     * @return void
     */
    public function enqueue_scripts(): void {
        $v = defined( 'ATK_VED_VERSION' ) ? ATK_VED_VERSION : Base::VERSION;

        // === Стили ===

        // Базовые стили (критичные)
        wp_enqueue_style( 'atk-variables', get_template_directory_uri() . '/css/variables.css', [], $v );
        wp_enqueue_style( 'atk-style', get_stylesheet_uri(), [ 'atk-variables' ], $v );
        wp_enqueue_style( 'atk-core', get_template_directory_uri() . '/css/core.css', [ 'atk-style' ], $v );
        
        // Критический CSS inline
        $this->enqueue_critical_css();

        // UI компоненты (объединенные)
        wp_enqueue_style( 'atk-ui', get_template_directory_uri() . '/css/ui.css', [ 'atk-style' ], $v );
        
        // Анимации и доступность
        wp_enqueue_style( 'atk-animations', get_template_directory_uri() . '/css/animations.css', [], $v );
        wp_enqueue_style( 'atk-a11y', get_template_directory_uri() . '/css/accessibility.css', [], $v );

        // Главная страница
        if ( is_front_page() ) {
            wp_enqueue_style( 'atk-front-page', get_template_directory_uri() . '/css/front-page.css', [ 'atk-style' ], $v );
            wp_enqueue_style( 'atk-landing', get_template_directory_uri() . '/css/landing.css', [ 'atk-ui' ], $v );
            wp_enqueue_style( 'atk-modern-clean', get_template_directory_uri() . '/css/modern-clean.css', [ 'atk-front-page' ], $v, 'all' );
        }

        // Модальные окна и формы
        wp_enqueue_style( 'atk-modals', get_template_directory_uri() . '/css/modals.css', [], $v );
        wp_enqueue_style( 'atk-form', get_template_directory_uri() . '/css/form-loader.css', [], $v );
        
        // Слайдеры и галереи
        wp_enqueue_style( 'atk-sliders', get_template_directory_uri() . '/css/sliders.css', [], $v );

        // Страницы калькулятора/трекинга
        if ( $this->is_calc_page() ) {
            wp_enqueue_style( 'atk-calculator', get_template_directory_uri() . '/css/calculator.css', [], $v );
            wp_enqueue_style( 'atk-tracking', get_template_directory_uri() . '/css/shipment-tracking.css', [], $v );
        }

        // 404
        if ( is_404() ) {
            wp_enqueue_style( 'atk-404', get_template_directory_uri() . '/css/404.css', [], $v );
        }

        // === Скрипты ===

        // Лоадер в head
        wp_enqueue_script( 'atk-loader', get_template_directory_uri() . '/js/loader.js', [], $v, false );

        // Основные скрипты (оптимизированные)
        wp_enqueue_script( 'atk-core', get_template_directory_uri() . '/js/core.js', [ 'jquery' ], $v, true );
        wp_enqueue_script( 'atk-ui', get_template_directory_uri() . '/js/ui.js', [ 'jquery' ], $v, true );
        wp_enqueue_script( 'atk-callback', get_template_directory_uri() . '/js/callback-modal.js', [ 'jquery' ], $v, true );
        wp_enqueue_script( 'atk-reviews', get_template_directory_uri() . '/js/reviews-slider.js', [ 'jquery' ], $v, true );
        wp_enqueue_script( 'atk-gallery', get_template_directory_uri() . '/js/gallery.js', [ 'jquery' ], $v, true );

        // Главная страница
        if ( is_front_page() ) {
            wp_enqueue_script( 'atk-counters', get_template_directory_uri() . '/js/hero-counters.js', [ 'jquery' ], $v, true );
            wp_enqueue_script( 'atk-stats', get_template_directory_uri() . '/js/statistics.js', [ 'jquery' ], $v, true );
            wp_enqueue_script( 'atk-share', get_template_directory_uri() . '/js/share.js', [], $v, true );
        }

        // Формы с background sync
        wp_enqueue_script( 'atk-forms', get_template_directory_uri() . '/js/forms.js', [], $v, true );

        // Калькулятор и трекинг
        if ( $this->is_calc_page() ) {
            wp_enqueue_script( 'atk-calc', get_template_directory_uri() . '/js/calculator.js', [ 'jquery' ], $v, true );
            wp_enqueue_script( 'atk-calc-fe', get_template_directory_uri() . '/js/calculator-frontend.js', [ 'jquery', 'atk-calc' ], $v, true );
            wp_enqueue_script( 'atk-ship', get_template_directory_uri() . '/js/shipment-tracking.js', [ 'jquery' ], $v, true );
        }

        // Локализация JS
        $this->localize_script();
    }

    /**
     * Критический CSS inline.
     *
     * @return void
     */
    private function enqueue_critical_css(): void {
        $transient_key = 'atk_ved_critical_css_v2';
        $critical_css  = get_transient( $transient_key );

        if ( false === $critical_css ) {
            $critical_file = get_template_directory() . '/css/critical-inline.css';
            if ( file_exists( $critical_file ) ) {
                $critical_css = file_get_contents( $critical_file );
                // Минифицируем еще больше
                $critical_css = preg_replace( '/\s+/', ' ', $critical_css );
                $critical_css = str_replace( [ ' {', '{ ', ' }', '} ', ': ', ' :', '; ', ' ;' ], [ '{', '{', '}', '}', ':', ':', ';', ';' ], $critical_css );
                set_transient( $transient_key, $critical_css, WEEK_IN_SECONDS );
            } else {
                $critical_css = '';
            }
        }

        if ( $critical_css ) {
            wp_add_inline_style( 'atk-style', $critical_css );
        }
    }

    /**
     * Локализация JavaScript.
     *
     * @return void
     */
    private function localize_script(): void {
        wp_localize_script( 'atk-core', 'atkVed', [
            'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
            'nonce'     => wp_create_nonce( 'atk_ved_nonce' ),
            'siteUrl'   => home_url( '/' ),
            'metrikaId' => (int) get_theme_mod( 'atk_ved_metrika_id', 0 ),
            'gaId'      => sanitize_text_field( get_theme_mod( 'atk_ved_ga_id', '' ) ),
            'newsletter' => [
                'nonce'  => wp_create_nonce( 'atk_newsletter_nonce' ),
                'action' => 'atk_ved_newsletter_subscribe',
            ],
            'i18n' => [
                'sending'  => __( 'Отправка…', 'atk-ved' ),
                'success'  => __( 'Спасибо! Вы подписались.', 'atk-ved' ),
                'error'    => __( 'Ошибка. Попробуйте ещё раз.', 'atk-ved' ),
                'badEmail' => __( 'Введите корректный email.', 'atk-ved' ),
            ],
        ] );
    }

    /**
     * Preload и preconnect.
     *
     * @return void
     */
    public function preload_resources(): void {
        if ( is_front_page() ) {
            printf(
                '<link rel="preload" href="%s" as="style">' . "\n",
                esc_url( get_template_directory_uri() . '/css/front-page.css' )
            );
            printf(
                '<link rel="preload" href="%s" as="script">' . "\n",
                esc_url( get_template_directory_uri() . '/js/core.js' )
            );
        }

        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
        echo '<link rel="dns-prefetch" href="//mc.yandex.ru">' . "\n";
    }

    /**
     * Проверка страницы калькулятора/трекинга.
     *
     * @return bool
     */
    private function is_calc_page(): bool {
        $post = get_post();

        return is_page( [ 'calculator', 'tracking', 'калькулятор', 'отслеживание' ] )
            || (
                $post
                && (
                    has_shortcode( $post->post_content, 'atk_calculator' )
                    || has_shortcode( $post->post_content, 'atk_tracking' )
                )
            );
    }
}
