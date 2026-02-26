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
        add_action( 'wp_footer', [ $this, 'add_web_share_api' ] );
        add_action( 'wp_footer', [ $this, 'add_background_sync_js' ] );
    }

    /**
     * Подключение скриптов и стилей.
     *
     * @return void
     */
    public function enqueue_scripts(): void {
        $v = Base::VERSION;

        // === Стили ===

        // Дизайн-система
        wp_enqueue_style( 'atk-design-tokens', Base::uri() . '/css/design-tokens.css', [], '3.3' );
        wp_enqueue_style( 'atk-modern-design', Base::uri() . '/css/modern-design.css', [ 'atk-design-tokens' ], '3.3' );
        wp_enqueue_style( 'atk-style', get_stylesheet_uri(), [ 'atk-modern-design' ], $v );
        wp_enqueue_style( 'atk-ui', Base::uri() . '/css/ui-components.css', [ 'atk-modern-design' ], $v );
        wp_enqueue_style( 'atk-ui-extra', Base::uri() . '/css/additional-components.css', [ 'atk-ui' ], $v );
        wp_enqueue_style( 'atk-animations', Base::uri() . '/css/animations-enhanced.css', [], '3.3' );
        wp_enqueue_style( 'atk-dark-mode', Base::uri() . '/css/dark-mode-toggle.css', [], '3.3' );
        wp_enqueue_style( 'atk-a11y', Base::uri() . '/css/accessibility.css', [], $v );
        wp_enqueue_style( 'atk-ui-enhanced', Base::uri() . '/css/ui-enhancements.css', [ 'atk-ui' ], $v );

        // Критический CSS inline
        $this->enqueue_critical_css();

        // Условные стили для главной
        if ( is_front_page() ) {
            wp_enqueue_style( 'atk-landing', Base::uri() . '/css/landing-sections.css', [], '3.3' );
            wp_enqueue_style( 'atk-hero', Base::uri() . '/css/hero-counters.css', [], $v );
            wp_enqueue_style( 'atk-stats', Base::uri() . '/css/statistics.css', [], $v );
            wp_enqueue_style( 'atk-stats-carousel', Base::uri() . '/css/statistics-carousel.css', [], '3.3' );
            wp_enqueue_style( 'atk-animations-adv', Base::uri() . '/css/advanced-animations.css', [], '3.3' );
        }

        // Общие компоненты
        wp_enqueue_style( 'atk-modal', Base::uri() . '/css/modal.css', [], $v );
        wp_enqueue_style( 'atk-form', Base::uri() . '/css/form-loader.css', [], $v );
        wp_enqueue_style( 'atk-callback', Base::uri() . '/css/callback-modal.css', [], $v );
        wp_enqueue_style( 'atk-reviews', Base::uri() . '/css/reviews-slider.css', [], $v );
        wp_enqueue_style( 'atk-gallery', Base::uri() . '/css/gallery.css', [], $v );

        // Страницы калькулятора/трекинга
        if ( $this->is_calc_page() ) {
            wp_enqueue_style( 'atk-calculator', Base::uri() . '/css/calculator.css', [], $v );
            wp_enqueue_style( 'atk-tracking', Base::uri() . '/css/shipment-tracking.css', [], $v );
        }

        // 404
        if ( is_404() ) {
            wp_enqueue_style( 'atk-404', Base::uri() . '/css/404.css', [], $v );
        }

        // === Скрипты ===

        // Лоадер в head
        wp_enqueue_script( 'atk-loader', Base::uri() . '/js/loader.js', [], $v, false );

        // Основные скрипты
        wp_enqueue_script( 'atk-main', Base::uri() . '/js/main.js', [ 'jquery' ], $v, true );
        wp_enqueue_script( 'atk-ui', Base::uri() . '/js/ui-components.js', [ 'jquery' ], $v, true );
        wp_enqueue_script( 'atk-ui-extra', Base::uri() . '/js/additional-components.js', [ 'jquery' ], $v, true );
        wp_enqueue_script( 'atk-modal', Base::uri() . '/js/modal.js', [ 'jquery' ], $v, true );
        wp_enqueue_script( 'atk-callback', Base::uri() . '/js/callback-modal.js', [ 'jquery' ], $v, true );
        wp_enqueue_script( 'atk-reviews', Base::uri() . '/js/reviews-slider.js', [ 'jquery' ], $v, true );
        wp_enqueue_script( 'atk-gallery', Base::uri() . '/js/gallery.js', [ 'jquery' ], $v, true );
        wp_enqueue_script( 'atk-enhance', Base::uri() . '/js/enhancements.js', [ 'jquery' ], $v, true );
        wp_enqueue_script( 'atk-tracking-js', Base::uri() . '/js/tracking.js', [ 'jquery' ], $v, true );
        wp_enqueue_script( 'atk-ui-enhanced', Base::uri() . '/js/ui-components-enhanced.js', [ 'jquery', 'atk-main' ], $v, true );

        // Главная страница
        if ( is_front_page() ) {
            wp_enqueue_script( 'atk-counters', Base::uri() . '/js/hero-counters.js', [ 'jquery' ], $v, true );
            wp_enqueue_script( 'atk-stats', Base::uri() . '/js/statistics.js', [ 'jquery' ], $v, true );
        }

        // Калькулятор и трекинг
        if ( $this->is_calc_page() ) {
            wp_enqueue_script( 'atk-calc', Base::uri() . '/js/calculator.js', [ 'jquery' ], $v, true );
            wp_enqueue_script( 'atk-calc-fe', Base::uri() . '/js/calculator-frontend.js', [ 'jquery', 'atk-calc' ], $v, true );
            wp_enqueue_script( 'atk-ship', Base::uri() . '/js/shipment-tracking.js', [ 'jquery' ], $v, true );
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
        $transient_key = 'atk_ved_critical_css';
        $critical_css  = get_transient( $transient_key );

        if ( false === $critical_css ) {
            $critical_file = Base::dir() . '/css/critical.css';
            $critical_css  = file_exists( $critical_file ) ? file_get_contents( $critical_file ) : '';
            set_transient( $transient_key, $critical_css, DAY_IN_SECONDS );
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
        wp_localize_script( 'atk-main', 'atkVed', [
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
                esc_url( Base::uri() . '/css/modern-design.css' )
            );
            printf(
                '<link rel="preload" href="%s" as="script">' . "\n",
                esc_url( Base::uri() . '/js/main.js' )
            );
        }

        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
        echo '<link rel="dns-prefetch" href="//www.google-analytics.com">' . "\n";
        echo '<link rel="dns-prefetch" href="//mc.yandex.ru">' . "\n";
        
        // Additional resource hints for performance
        echo '<link rel="preconnect" href="https://www.googletagmanager.com">' . "\n";
        echo '<link rel="preconnect" href="https://connect.facebook.net">' . "\n";
        echo '<link rel="dns-prefetch" href="//yastatic.net">' . "\n";
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
    
    /**
     * Add Web Share API functionality.
     * 
     * @return void
     */
    public function add_web_share_api(): void {
        if (is_front_page()) {
            $site_description = get_bloginfo('description');
            echo '<script>
                function sharePage() {
                    if (navigator.share) {
                        navigator.share({
                            title: document.title,
                            text: "' . esc_js($site_description) . '",
                            url: window.location.href
                        }).catch(console.error);
                    } else {
                        // Fallback to clipboard API
                        navigator.clipboard.writeText(window.location.href);
                        alert("Ссылка скопирована в буфер обмена!");
                    }
                }
            </script>';
        }
    }
    
    /**
     * Add background sync functionality for forms.
     * 
     * @return void
     */
    public function add_background_sync_js(): void {
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                // Handle form submissions for background sync
                const forms = document.querySelectorAll("form");
                
                forms.forEach(function(form) {
                    form.addEventListener("submit", function(e) {
                        // Check if form is an AJAX form
                        if (form.classList.contains("ajax-form") || form.classList.contains("contact-form") || form.classList.contains("newsletter-form")) {
                            // Allow AJAX forms to handle themselves
                            return;
                        }
                        
                        // For regular forms, we can still register background sync capability
                        if ("serviceWorker" in navigator && "SyncManager" in window) {
                            navigator.serviceWorker.ready.then(function(reg) {
                                // Register background sync for form submission
                                reg.sync.register("contact-form-sync").catch(function(error) {
                                    console.log("Background sync registration failed:", error);
                                });
                            });
                        }
                    });
                });
                
                // Specifically handle contact forms with AJAX
                const contactForms = document.querySelectorAll(".contact-form, .quick-search-form, #calculator-form");
                
                contactForms.forEach(function(form) {
                    form.addEventListener("submit", function(e) {
                        e.preventDefault();
                        
                        // Collect form data
                        const formData = new FormData(form);
                        const serializedData = new URLSearchParams(formData).toString();
                        
                        // Try to submit via fetch
                        fetch(form.action || "./", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded",
                            },
                            body: serializedData
                        })
                        .then(function(response) {
                            if (response.ok) {
                                return response.json();
                            }
                            throw new Error("Network response was not ok");
                        })
                        .then(function(data) {
                            // Handle success
                            if (data.queued) {
                                alert("Форма добавлена в очередь для отправки");
                            } else {
                                alert("Форма успешно отправлена");
                            }
                            form.reset();
                        })
                        .catch(function(error) {
                            console.error("Form submission error:", error);
                            
                            // If offline, queue for background sync
                            if ("serviceWorker" in navigator && "SyncManager" in window) {
                                navigator.serviceWorker.ready.then(function(reg) {
                                    // Store form data for sync
                                    if (typeof localStorage !== "undefined") {
                                        const queuedForms = JSON.parse(localStorage.getItem("queued_forms") || "[]");
                                        queuedForms.push({
                                            id: Date.now(),
                                            url: form.action || "./",
                                            payload: serializedData,
                                            timestamp: Date.now()
                                        });
                                        localStorage.setItem("queued_forms", JSON.stringify(queuedForms));
                                    }
                                    
                                    reg.sync.register("contact-form-sync");
                                    alert("Форма будет отправлена при восстановлении соединения");
                                });
                            }
                        });
                    });
                });
            });
        </script>';
    }
}
