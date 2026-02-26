<?php
/**
 * Доступность (Accessibility) — ARIA, skip links, keyboard navigation
 *
 * @package ATK_VED
 * @since   3.2.0
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Класс для управления доступностью
 */
class ATK_VED_Accessibility {

    private static ?self $instance = null;

    public static function get_instance(): self {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Skip links
        add_action( 'wp_body_open', [ $this, 'output_skip_links' ], 1 );
        
        // Улучшенное управление фокусом
        add_action( 'wp_footer', [ $this, 'output_focus_management' ], 30 );

        // ARIA landmarks
        add_filter( 'body_class', [ $this, 'add_aria_landmarks' ] );

        // ARIA для форм
        add_filter( 'wpcf7_form_elements', [ $this, 'enhance_cf7_accessibility' ] );
        
        // Улучшенная доступность форм
        add_filter( 'the_content', [ $this, 'enhance_form_accessibility' ] );

        // Подключение стилей доступности
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_accessibility_styles' ] );

        // Улучшение навигации
        add_action( 'wp_footer', [ $this, 'output_keyboard_navigation' ], 20 );
        
        // Улучшенная доступность
        add_action( 'wp_head', [ $this, 'output_aria_labels' ], 20 );
    }

    /**
     * Skip links для навигации с клавиатуры
     */
    public function output_skip_links(): void {
        ?>
        <div id="skip-links" class="skip-links">
            <a class="skip-link" href="#main-content">
                <?php esc_html_e( 'Перейти к основному содержимому', 'atk-ved' ); ?>
            </a>
            <a class="skip-link" href="#site-navigation">
                <?php esc_html_e( 'Перейти к навигации', 'atk-ved' ); ?>
            </a>
            <a class="skip-link" href="#site-footer">
                <?php esc_html_e( 'Перейти к подвалу', 'atk-ved' ); ?>
            </a>
        </div>
        <?php
    }

    /**
     * Добавление ARIA landmarks к body class
     */
    public function add_aria_landmarks( array $classes ): array {
        $classes[] = 'aria-landmarks-ready';
        return $classes;
    }

    /**
     * Улучшение доступности Contact Form 7
     */
    public function enhance_cf7_accessibility( string $content ): string {
        // Добавляем aria-label к полям
        $content = preg_replace_callback(
            '/\[text\s+([^\]]+)\]/',
            function( $matches ) {
                $field = $matches[1];
                $parts = explode( ' ', $field );
                $name = $parts[0];
                $label = $this->get_field_label( $name );
                return sprintf(
                    '[text %s aria-label="%s"]',
                    $field,
                    esc_attr( $label )
                );
            },
            $content
        );

        // Добавляем aria-required
        $content = preg_replace(
            '/\[text\s+([^\]]*class:"([^"]*)"[^\]]*)\]/',
            '[text $1 aria-required="true"]',
            $content
        );

        // Добавляем role="alert" для сообщений об ошибках
        $content = str_replace(
            '<div class="wpcf7-not-valid-tip"',
            '<div class="wpcf7-not-valid-tip" role="alert"',
            $content
        );

        // Добавляем aria-live для статусных сообщений
        $content = str_replace(
            '<div class="wpcf7-response-output"',
            '<div class="wpcf7-response-output" aria-live="polite"',
            $content
        );

        return $content;
    }

    /**
     * Подключение стилей доступности
     */
    public function enqueue_accessibility_styles(): void {
        wp_enqueue_style(
            'atk-a11y',
            get_template_directory_uri() . '/css/accessibility.css',
            [],
            ATK_VED_VERSION
        );
    }

    /**
     * Скрипт для управления фокусом с клавиатуры
     */
    public function output_keyboard_navigation(): void {
        ?>
        <script>
        (function() {
            'use strict';

            // Обнаружение навигации с клавиатуры
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Tab') {
                    document.body.classList.add('keyboard-navigation');
                }
            });

            document.addEventListener('mousedown', function() {
                document.body.classList.remove('keyboard-navigation');
            });

            // Focus trap для модальных окон
            const modals = document.querySelectorAll('[role="dialog"]');
            modals.forEach(function(modal) {
                modal.addEventListener('keydown', function(e) {
                    if (e.key !== 'Tab') return;

                    const focusableElements = modal.querySelectorAll(
                        'a[href], button, textarea, input, select, [tabindex]:not([tabindex="-1"])'
                    );

                    const firstElement = focusableElements[0];
                    const lastElement = focusableElements[focusableElements.length - 1];

                    if (e.shiftKey && document.activeElement === firstElement) {
                        e.preventDefault();
                        lastElement.focus();
                    } else if (!e.shiftKey && document.activeElement === lastElement) {
                        e.preventDefault();
                        firstElement.focus();
                    }
                });
            });

            // Улучшение доступности аккордеонов
            const accordions = document.querySelectorAll('.faq-item, .accordion');
            accordions.forEach(function(accordion) {
                const trigger = accordion.querySelector('.faq-question, .accordion-trigger');
                const content = accordion.querySelector('.faq-answer, .accordion-content');

                if (trigger && content) {
                    trigger.setAttribute('aria-expanded', 'false');
                    trigger.setAttribute('aria-controls', content.id || 'accordion-content-' + Math.random().toString(36).substr(2, 9));

                    if (!content.id) {
                        content.id = trigger.getAttribute('aria-controls');
                    }

                    trigger.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            trigger.click();
                        }
                    });

                    trigger.addEventListener('click', function() {
                        const isExpanded = trigger.getAttribute('aria-expanded') === 'true';
                        trigger.setAttribute('aria-expanded', !isExpanded);
                    });
                }
            });

            // Уведомления для скринридеров
            window.atkA11y = {
                announce: function(message, priority) {
                    priority = priority || 'polite';

                    let announcer = document.getElementById('a11y-announcer');
                    if (!announcer) {
                        announcer = document.createElement('div');
                        announcer.id = 'a11y-announcer';
                        announcer.setAttribute('role', 'status');
                        announcer.setAttribute('aria-live', priority);
                        announcer.setAttribute('aria-atomic', 'true');
                        announcer.className = 'sr-only';
                        document.body.appendChild(announcer);
                    }

                    announcer.textContent = '';
                    setTimeout(function() {
                        announcer.textContent = message;
                    }, 100);
                }
            };

            // Закрытие модальных окон по Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const openModal = document.querySelector('[role="dialog"]:not([hidden])');
                    if (openModal) {
                        const closeBtn = openModal.querySelector('[data-close]');
                        if (closeBtn) {
                            closeBtn.click();
                        }
                    }
                }
            });

            // Плавный скролл для skip links
            document.querySelectorAll('.skip-link').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    const target = document.getElementById(targetId);

                    if (target) {
                        target.setAttribute('tabindex', '-1');
                        target.scrollIntoView({ behavior: 'smooth' });
                        target.focus();
                    }
                });
            });

        })();
        </script>
        <?php
    }

    /**
     * Получение label для поля формы
     */
    private function get_field_label( string $field_name ): string {
        $labels = [
            'your-name'     => __( 'Ваше имя', 'atk-ved' ),
            'your-email'    => __( 'Ваш Email', 'atk-ved' ),
            'your-phone'    => __( 'Ваш телефон', 'atk-ved' ),
            'your-message'  => __( 'Ваше сообщение', 'atk-ved' ),
            'subject'       => __( 'Тема', 'atk-ved' ),
        ];

        return $labels[ $field_name ] ?? $field_name;
    }
    
    /**
     * Улучшение доступности форм
     */
    public function enhance_form_accessibility( $content ) {
        // Добавляем aria-invalid и aria-describedby к полям формы
        $content = preg_replace_callback(
            '/<(input|textarea|select)([^>]*?)>/i',
            function( $matches ) {
                $tag = $matches[1];
                $attrs = $matches[2];
                
                // Проверяем, есть ли у поля id
                if ( preg_match( '/id=["\']([^"\']*)["\']/', $attrs, $id_matches ) ) {
                    $field_id = $id_matches[1];
                    
                    // Добавляем aria-invalid если его нет
                    if ( strpos( $attrs, 'aria-invalid' ) === false ) {
                        $attrs = rtrim( $attrs ) . ' aria-invalid="false"';
                    }
                    
                    // Добавляем aria-describedby если его нет
                    if ( strpos( $attrs, 'aria-describedby' ) === false ) {
                        $error_id = $field_id . '-error';
                        $attrs = rtrim( $attrs ) . ' aria-describedby="' . $error_id . '"';
                    }
                }
                
                return '<' . $tag . $attrs . '>';
            },
            $content
        );
        
        return $content;
    }
    
    /**
     * Вывод ARIA меток для улучшения доступности
     */
    public function output_aria_labels(): void {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Добавляем ARIA метки к различным элементам
            const buttons = document.querySelectorAll('button:not([aria-label]):not([aria-labelledby])');
            buttons.forEach(function(button) {
                if (button.textContent.trim()) {
                    button.setAttribute('aria-label', button.textContent.trim());
                } else if (button.querySelector('svg, img')) {
                    button.setAttribute('aria-label', 'Кнопка');
                }
            });
            
            // Добавляем ARIA метки к изображениям без альтернативного текста
            const images = document.querySelectorAll('img:not([alt]):not([aria-label]):not([aria-labelledby])');
            images.forEach(function(img) {
                img.setAttribute('aria-hidden', 'true');
            });
            
            // Улучшаем доступность аккордеонов
            const accordions = document.querySelectorAll('.faq-item, .accordion');
            accordions.forEach(function(accordion, index) {
                const trigger = accordion.querySelector('.faq-question, .accordion-trigger');
                const content = accordion.querySelector('.faq-answer, .accordion-content');
                
                if (trigger && content) {
                    const id = 'accordion-' + index;
                    
                    if (!content.id) {
                        content.id = id;
                    }
                    
                    trigger.setAttribute('aria-controls', content.id);
                    trigger.setAttribute('aria-expanded', 'false');
                    
                    // Добавляем обработчик для обновления состояния
                    trigger.addEventListener('click', function() {
                        const isExpanded = trigger.getAttribute('aria-expanded') === 'true';
                        trigger.setAttribute('aria-expanded', !isExpanded);
                    });
                }
            });
            
            // Добавляем переключатель режима высокого контраста
            const highContrastToggle = document.createElement('button');
            highContrastToggle.className = 'toggle-high-contrast';
            highContrastToggle.type = 'button';
            highContrastToggle.textContent = 'Включить высокий контраст';
            highContrastToggle.setAttribute('aria-label', 'Переключить режим высокого контраста');
            
            highContrastToggle.addEventListener('click', function() {
                document.body.classList.toggle('high-contrast-mode');
                
                if (document.body.classList.contains('high-contrast-mode')) {
                    highContrastToggle.textContent = 'Выключить высокий контраст';
                    localStorage.setItem('highContrastMode', 'enabled');
                } else {
                    highContrastToggle.textContent = 'Включить высокий контраст';
                    localStorage.setItem('highContrastMode', 'disabled');
                }
            });
            
            // Проверяем сохраненное состояние режима высокого контраста
            if (localStorage.getItem('highContrastMode') === 'enabled') {
                document.body.classList.add('high-contrast-mode');
                highContrastToggle.textContent = 'Выключить высокий контраст';
            }
            
            document.body.appendChild(highContrastToggle);
        });
        </script>
        <?php
    }
    
    /**
     * Вывод JavaScript для улучшенного управления фокусом
     */
    public function output_focus_management(): void {
        ?>
        <script>
        (function() {
            'use strict';
            
            // Управление фокусом для модальных окон
            function setupModalFocusManagement() {
                const modals = document.querySelectorAll('[role="dialog"], .modal[aria-modal="true"]');
                
                modals.forEach(function(modal) {
                    // Находим все интерактивные элементы в модальном окне
                    const focusableElements = modal.querySelectorAll(
                        'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
                    );
                    
                    if (focusableElements.length === 0) return;
                    
                    const firstFocusable = focusableElements[0];
                    const lastFocusable = focusableElements[focusableElements.length - 1];
                    
                    // Обработка Tab и Shift+Tab внутри модального окна
                    modal.addEventListener('keydown', function(e) {
                        if (e.key !== 'Tab') return;
                        
                        if (e.shiftKey && document.activeElement === firstFocusable) {
                            // Если нажат Shift+Tab на первом элементе, перемещаем фокус на последний
                            e.preventDefault();
                            lastFocusable.focus();
                        } else if (!e.shiftKey && document.activeElement === lastFocusable) {
                            // Если нажат Tab на последнем элементе, перемещаем фокус на первый
                            e.preventDefault();
                            firstFocusable.focus();
                        }
                    });
                });
            }
            
            // Управление фокусом для навигации с клавиатуры
            function setupKeyboardNavigation() {
                let isUsingKeyboard = false;
                
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Tab' || e.keyCode === 9) {
                        isUsingKeyboard = true;
                        document.body.classList.add('keyboard-navigation');
                    }
                });
                
                document.addEventListener('mousedown', function() {
                    isUsingKeyboard = false;
                    document.body.classList.remove('keyboard-navigation');
                });
                
                // Добавляем класс для элементов при фокусе с клавиатуры
                document.addEventListener('focusin', function(e) {
                    if (isUsingKeyboard) {
                        e.target.classList.add('focused-by-keyboard');
                    }
                });
                
                document.addEventListener('focusout', function(e) {
                    e.target.classList.remove('focused-by-keyboard');
                });
            }
            
            // Инициализация при загрузке DOM
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    setupModalFocusManagement();
                    setupKeyboardNavigation();
                });
            } else {
                setupModalFocusManagement();
                setupKeyboardNavigation();
            }
        })();
        </script>
        <?php
    }
}

// Инициализация
function atk_ved_init_accessibility(): void {
    ATK_VED_Accessibility::get_instance();
}
add_action( 'after_setup_theme', 'atk_ved_init_accessibility' );


// ============================================================================
// HELPER FUNCTIONS
// ============================================================================

/**
 * Screen reader текст
 */
function atk_ved_sr_text( string $text ): string {
    return sprintf(
        '<span class="sr-only">%s</span>',
        esc_html( $text )
    );
}

/**
 * ARIA label для кнопок
 */
function atk_ved_aria_label( string $label ): string {
    return sprintf( 'aria-label="%s"', esc_attr( $label ) );
}
