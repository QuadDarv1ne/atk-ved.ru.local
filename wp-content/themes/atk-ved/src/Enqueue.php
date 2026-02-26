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
        $v = defined( 'ATK_VED_VERSION' ) ? ATK_VED_VERSION : Base::VERSION;

        // === Стили ===

        // Базовые стили (критичные)
        wp_enqueue_style( 'atk-design-tokens', get_template_directory_uri() . '/css/design-tokens.css', [], $v );
        wp_enqueue_style( 'atk-modern-design', get_template_directory_uri() . '/css/modern-design.css', [ 'atk-design-tokens' ], $v );
        wp_enqueue_style( 'atk-style', get_stylesheet_uri(), [ 'atk-modern-design' ], $v );
        
        // Критический CSS inline
        $this->enqueue_critical_css();

        // UI компоненты
        wp_enqueue_style( 'atk-ui', get_template_directory_uri() . '/css/ui-components.css', [ 'atk-style' ], $v );
        wp_enqueue_style( 'atk-ui-enhanced', get_template_directory_uri() . '/css/ui-enhancements.css', [ 'atk-ui' ], $v );
        
        // Анимации и доступность
        wp_enqueue_style( 'atk-animations', get_template_directory_uri() . '/css/animations-enhanced.css', [], $v );
        wp_enqueue_style( 'atk-a11y', get_template_directory_uri() . '/css/accessibility.css', [], $v );

        // Главная страница
        if ( is_front_page() ) {
            wp_enqueue_style( 'atk-landing', get_template_directory_uri() . '/css/landing-sections.css', [ 'atk-ui' ], $v );
            wp_enqueue_style( 'atk-hero', get_template_directory_uri() . '/css/hero-counters.css', [], $v );
            wp_enqueue_style( 'atk-stats', get_template_directory_uri() . '/css/statistics.css', [], $v );
            wp_enqueue_style( 'atk-ux-improvements', get_template_directory_uri() . '/css/ux-improvements.css', [ 'atk-landing' ], $v );
            wp_enqueue_style( 'atk-clean-design', get_template_directory_uri() . '/css/clean-design.css', [ 'atk-ui' ], $v );
        }

        // Модальные окна и формы
        wp_enqueue_style( 'atk-modal', get_template_directory_uri() . '/css/modal.css', [], $v );
        wp_enqueue_style( 'atk-callback', get_template_directory_uri() . '/css/callback-modal.css', [ 'atk-modal' ], $v );
        wp_enqueue_style( 'atk-form', get_template_directory_uri() . '/css/form-loader.css', [], $v );
        
        // Слайдеры и галереи
        wp_enqueue_style( 'atk-reviews', get_template_directory_uri() . '/css/reviews-slider.css', [], $v );
        wp_enqueue_style( 'atk-gallery', get_template_directory_uri() . '/css/gallery.css', [], $v );

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

        // Основные скрипты
        wp_enqueue_script( 'atk-main', get_template_directory_uri() . '/js/main.js', [ 'jquery' ], $v, true );
        wp_enqueue_script( 'atk-ui', get_template_directory_uri() . '/js/ui-components.js', [ 'jquery' ], $v, true );
        wp_enqueue_script( 'atk-ui-enhanced', get_template_directory_uri() . '/js/ui-components-enhanced.js', [ 'jquery', 'atk-main' ], $v, true );
        wp_enqueue_script( 'atk-modal', get_template_directory_uri() . '/js/modal.js', [ 'jquery' ], $v, true );
        wp_enqueue_script( 'atk-callback', get_template_directory_uri() . '/js/callback-modal.js', [ 'jquery' ], $v, true );
        wp_enqueue_script( 'atk-reviews', get_template_directory_uri() . '/js/reviews-slider.js', [ 'jquery' ], $v, true );
        wp_enqueue_script( 'atk-gallery', get_template_directory_uri() . '/js/gallery.js', [ 'jquery' ], $v, true );

        // Главная страница
        if ( is_front_page() ) {
            wp_enqueue_script( 'atk-counters', get_template_directory_uri() . '/js/hero-counters.js', [ 'jquery' ], $v, true );
            wp_enqueue_script( 'atk-stats', get_template_directory_uri() . '/js/statistics.js', [ 'jquery' ], $v, true );
        }

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
        $transient_key = 'atk_ved_critical_css';
        $critical_css  = get_transient( $transient_key );

        if ( false === $critical_css ) {
            $critical_file = get_template_directory() . '/css/critical.css';
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
                esc_url( get_template_directory_uri() . '/css/modern-design.css' )
            );
            printf(
                '<link rel="preload" href="%s" as="script">' . "\n",
                esc_url( get_template_directory_uri() . '/js/main.js' )
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
