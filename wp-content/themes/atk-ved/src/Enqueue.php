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

        // 1. CSS Variables (первым делом)
        wp_enqueue_style( 'atk-variables', get_template_directory_uri() . '/css/variables.css', [], $v );
        
        // 2. Reset & Fixes
        wp_enqueue_style( 'atk-fixes', get_template_directory_uri() . '/css/fixes.css', [ 'atk-variables' ], $v );
        
        // 3. Bootstrap Icons (только иконки, легковесно)
        wp_enqueue_style( 'bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css', [], '1.11.3' );
        
        // 4. Main Theme Style
        wp_enqueue_style( 'atk-style', get_stylesheet_uri(), [ 'atk-variables', 'atk-fixes' ], $v );
        
        // 5. Utilities
        wp_enqueue_style( 'atk-utilities', get_template_directory_uri() . '/css/utilities.css', [ 'atk-style' ], $v );
        
        // 6. Base Styles
        wp_enqueue_style( 'atk-base', get_template_directory_uri() . '/css/base.css', [ 'atk-utilities' ], $v );
        
        // 7. Animations
        wp_enqueue_style( 'atk-animations', get_template_directory_uri() . '/css/animations.css', [ 'atk-base' ], $v );
        
        // 8. Layout Components
        wp_enqueue_style( 'atk-header', get_template_directory_uri() . '/css/layout/header.css', [ 'atk-base' ], $v );
        wp_enqueue_style( 'atk-menu', get_template_directory_uri() . '/css/layout/menu.css', [ 'atk-header' ], $v );
        wp_enqueue_style( 'atk-main', get_template_directory_uri() . '/css/layout/main.css', [ 'atk-base' ], $v );
        wp_enqueue_style( 'atk-footer', get_template_directory_uri() . '/css/layout/footer.css', [ 'atk-base' ], $v );
        
        // 9. UI Components
        wp_enqueue_style( 'atk-ui', get_template_directory_uri() . '/css/ui.css', [ 'atk-base' ], $v );
        
        // 10. Feature Components
        wp_enqueue_style( 'atk-components', get_template_directory_uri() . '/css/components.css', [ 'atk-ui' ], $v );
        wp_enqueue_style( 'atk-carousel', get_template_directory_uri() . '/css/components/carousel.css', [ 'atk-components' ], $v );
        wp_enqueue_style( 'atk-contacts', get_template_directory_uri() . '/css/components/contacts.css', [ 'atk-components' ], $v );
        wp_enqueue_style( 'atk-faq', get_template_directory_uri() . '/css/components/faq.css', [ 'atk-components' ], $v );
        
        // 11. Accessibility
        wp_enqueue_style( 'atk-a11y', get_template_directory_uri() . '/css/a11y.css', [ 'atk-base' ], $v );
        
        // 12. Theme Toggle
        wp_enqueue_style( 'atk-theme-toggle', get_template_directory_uri() . '/css/theme-toggle.css', [ 'atk-base' ], $v );
        
        // 13. Dark Theme
        wp_enqueue_style( 'atk-theme-dark', get_template_directory_uri() . '/css/theme-dark.css', [ 'atk-variables' ], $v );
        
        // 14. Back to Top
        wp_enqueue_style( 'atk-back-to-top', get_template_directory_uri() . '/css/back-to-top.css', [ 'atk-base' ], $v );
        
        // Критический CSS inline
        $this->enqueue_critical_css();

        // 12. Page-specific styles
        if ( is_front_page() ) {
            wp_enqueue_style( 'atk-hero-section', get_template_directory_uri() . '/css/sections/hero.css', [ 'atk-base' ], $v );
            wp_enqueue_style( 'atk-front-page', get_template_directory_uri() . '/css/front-page.css', [ 'atk-hero-section' ], $v );
        }

        // Blog and archives
        if ( is_home() || is_archive() || is_search() ) {
            wp_enqueue_style( 'atk-blog', get_template_directory_uri() . '/css/blog.css', [ 'atk-base' ], $v );
        }

        // Single Post
        if ( is_single() ) {
            wp_enqueue_style( 'atk-single', get_template_directory_uri() . '/css/single.css', [ 'atk-base' ], $v );
        }

        // Страницы калькулятора/трекинга
        if ( $this->is_calc_page() ) {
            wp_enqueue_style( 'atk-calculator', get_template_directory_uri() . '/css/calculator.css', [ 'atk-base' ], $v );
            wp_enqueue_style( 'atk-tracking', get_template_directory_uri() . '/css/shipment-tracking.css', [ 'atk-base' ], $v );
            wp_enqueue_script( 'atk-calc', get_template_directory_uri() . '/js/calculator-vanilla.js', [], $v, true );
            wp_enqueue_script( 'atk-ship', get_template_directory_uri() . '/js/shipment-tracking-vanilla.js', [], $v, true );
        }

        // 404
        if ( is_404() ) {
            wp_enqueue_style( 'atk-404', get_template_directory_uri() . '/css/404.css', [], $v );
        }

        // Privacy Policy
        if ( is_page_template( 'page-privacy.php' ) ) {
            wp_enqueue_style( 'atk-privacy', get_template_directory_uri() . '/css/privacy.css', [ 'atk-base' ], $v );
        }

        // About Page
        if ( is_page_template( 'page-about.php' ) ) {
            wp_enqueue_style( 'atk-about', get_template_directory_uri() . '/css/about.css', [ 'atk-base' ], $v );
        }

        // Services Page
        if ( is_page_template( 'page-services.php' ) ) {
            wp_enqueue_style( 'atk-services', get_template_directory_uri() . '/css/services.css', [ 'atk-base' ], $v );
        }

        // Contacts Page
        if ( is_page_template( 'page-contacts.php' ) ) {
            wp_enqueue_style( 'atk-contacts', get_template_directory_uri() . '/css/contacts.css', [ 'atk-base' ], $v );
            wp_enqueue_script( 'atk-contact-form', get_template_directory_uri() . '/js/contact-form.js', [], $v, true );
        }

        // Thank You Page
        if ( is_page_template( 'page-thank-you.php' ) ) {
            wp_enqueue_style( 'atk-thank-you', get_template_directory_uri() . '/css/thank-you.css', [ 'atk-base' ], $v );
        }

        // === Скрипты ===

        // Theme Toggle (загружается первым в head для предотвращения мигания)
        wp_enqueue_script( 'atk-theme-toggle', get_template_directory_uri() . '/js/theme-toggle.js', [], $v, false );

        // Лоадер в head
        wp_enqueue_script( 'atk-loader', get_template_directory_uri() . '/js/loader.js', [], $v, false );

        // Service Worker для PWA
        if ( ! is_admin() ) {
            wp_enqueue_script( 'atk-sw', get_template_directory_uri() . '/js/sw-register.js', [], $v, true );
        }

        // Основные скрипты (без jQuery)
        wp_enqueue_script( 'atk-core', get_template_directory_uri() . '/js/core.js', [], $v, true );
        wp_enqueue_script( 'atk-performance', get_template_directory_uri() . '/js/performance.js', [], $v, true );
        wp_enqueue_script( 'atk-components', get_template_directory_uri() . '/js/components.js', [], $v, true );
        wp_enqueue_script( 'atk-interactions', get_template_directory_uri() . '/js/interactions.js', [], $v, true );
        wp_enqueue_script( 'atk-back-to-top', get_template_directory_uri() . '/js/back-to-top.js', [], $v, true );

        // Главная страница
        if ( is_front_page() ) {
            wp_enqueue_script( 'atk-landing-premium', get_template_directory_uri() . '/js/landing-premium.js', [], $v, true );
            wp_enqueue_script( 'atk-counters', get_template_directory_uri() . '/js/counters.js', [], $v, true );
            wp_enqueue_script( 'atk-share', get_template_directory_uri() . '/js/share.js', [], $v, true );
            wp_enqueue_script( 'atk-map', get_template_directory_uri() . '/js/map.js', [], $v, true );
        }

        // Формы с background sync
        wp_enqueue_script( 'atk-forms', get_template_directory_uri() . '/js/forms.js', [], $v, true );

        // Bootstrap JS (только если нужны модальные окна, дропдауны и т.д.)
        wp_enqueue_script( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', [], '5.3.2', true );

        // Lazy loading изображений
        if ( ! is_admin() ) {
            wp_enqueue_script( 'atk-lazy-images', get_template_directory_uri() . '/js/lazy-images.js', [], $v, true );
            
            // Web Vitals мониторинг (только на продакшене)
            if ( ! WP_DEBUG ) {
                wp_enqueue_script( 'atk-performance-metrics', get_template_directory_uri() . '/js/performance-metrics.js', [], $v, true );
            }
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
        $transient_key = 'atk_ved_critical_css_v3';
        $critical_css  = wp_cache_get( $transient_key, 'atk_ved' );

        if ( false === $critical_css ) {
            $critical_file = get_template_directory() . '/css/critical-inline.css';
            if ( file_exists( $critical_file ) ) {
                $critical_css = file_get_contents( $critical_file );
                $critical_css = preg_replace( '/\s+/', ' ', $critical_css );
                $critical_css = str_replace( [ ' {', '{ ', ' }', '} ', ': ', ' :', '; ', ' ;' ], [ '{', '{', '}', '}', ':', ':', ';', ';' ], $critical_css );
                wp_cache_set( $transient_key, $critical_css, 'atk_ved', WEEK_IN_SECONDS );
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
        $theme_uri = get_template_directory_uri();
        
        // Preload критических ресурсов
        echo '<link rel="preload" href="' . esc_url( $theme_uri . '/css/variables.css' ) . '" as="style">' . "\n";
        echo '<link rel="preload" href="' . esc_url( get_stylesheet_uri() ) . '" as="style">' . "\n";
        echo '<link rel="preload" href="' . esc_url( $theme_uri . '/js/core.js' ) . '" as="script">' . "\n";

        // Preconnect для внешних ресурсов
        echo '<link rel="preconnect" href="https://cdn.jsdelivr.net">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
        echo '<link rel="dns-prefetch" href="//mc.yandex.ru">' . "\n";
        
        // Prefetch для главной страницы
        if ( is_front_page() ) {
            echo '<link rel="prefetch" href="' . esc_url( $theme_uri . '/css/landing.css' ) . '">' . "\n";
        }
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
