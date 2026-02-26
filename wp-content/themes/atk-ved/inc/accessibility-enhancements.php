<?php
/**
 * Accessibility Enhancements - Дополнительные улучшения доступности
 *
 * @package ATK_VED
 * @since 3.2.0
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Класс для дополнительных улучшений доступности
 */
class ATK_VED_Accessibility_Enhancements {

    private static ?self $instance = null;

    public static function get_instance(): self {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Улучшенная навигация с клавиатуры
        add_action( 'wp_footer', [ $this, 'output_enhanced_keyboard_navigation' ], 30 );
        
        // Улучшенная ARIA разметка
        add_filter( 'body_class', [ $this, 'add_enhanced_a11y_classes' ] );
        
        // Улучшения для вспомогательных технологий
        add_action( 'wp_head', [ $this, 'output_a11y_improvements' ], 20 );
    }

    /**
     * Добавление улучшенных классов доступности
     */
    public function add_enhanced_a11y_classes( array $classes ): array {
        $classes[] = 'a11y-ready';
        
        // Проверка, включена ли навигация с клавиатуры
        if ( isset( $_GET['keyboard-navigation'] ) || $this->is_keyboard_navigation_active() ) {
            $classes[] = 'keyboard-navigation-active';
        }
        
        return $classes;
    }

    /**
     * Проверка активности навигации с клавиатуры
     */
    private function is_keyboard_navigation_active(): bool {
        // Проверяем, был ли последний ввод с клавиатуры
        return isset( $_COOKIE['keyboard-navigation'] ) && $_COOKIE['keyboard-navigation'] === 'true';
    }

    /**
     * Вывод улучшенной навигации с клавиатуры
     */
    public function output_enhanced_keyboard_navigation(): void {
        ?>
        <script>
        (function() {
            'use strict';
            
            // Обнаружение навигации с клавиатуры
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Tab' || e.keyCode === 9) {
                    // Устанавливаем cookie для отслеживания навигации с клавиатуры
                    document.body.classList.add('keyboard-navigation');
                    document.documentElement.classList.add('keyboard-navigation');
                    
                    // Устанавливаем cookie на 30 минут
                    var date = new Date();
                    date.setTime(date.getTime() + (30 * 60 * 1000));
                    document.cookie = "keyboard-navigation=true; expires=" + date.toUTCString() + "; path=/";
                }
            });
            
            // Обнаружение мышиной навигации
            document.addEventListener('mousedown', function() {
                document.body.classList.remove('keyboard-navigation');
                document.documentElement.classList.remove('keyboard-navigation');
                
                // Удаляем cookie
                document.cookie = "keyboard-navigation=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            });
            
            // Улучшенное управление фокусом для модальных окон
            function setupModalFocusManagement() {
                const modals = document.querySelectorAll('[role="dialog"], .modal[aria-modal="true"]');
                
                modals.forEach(function(modal) {
                    // Сохраняем элемент, который имел фокус до открытия модального окна
                    let previouslyFocusedElement = document.activeElement;
                    
                    // Находим все интерактивные элементы в модальном окне
                    const focusableElements = modal.querySelectorAll(
                        'a[href], button:not(:disabled), input:not(:disabled), select:not(:disabled), textarea:not(:disabled), [tabindex]:not([tabindex="-1"])'
                    );
                    
                    if (focusableElements.length === 0) return;
                    
                    const firstFocusable = focusableElements[0];
                    const lastFocusable = focusableElements[focusableElements.length - 1];
                    
                    // Фокус на первый элемент при открытии модального окна
                    if (modal.hasAttribute('aria-hidden') && modal.getAttribute('aria-hidden') === 'false') {
                        firstFocusable.focus();
                    }
                    
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
                    
                    // Возвращаем фокус на элемент, который был сфокусирован до открытия модального окна
                    modal.addEventListener('transitionend', function() {
                        if (this.classList.contains('closed')) {
                            if (previouslyFocusedElement && previouslyFocusedElement.focus) {
                                previouslyFocusedElement.focus();
                            }
                        }
                    });
                });
            }
            
            // Улучшенное объявление для вспомогательных технологий
            window.AtkA11yAnnouncer = {
                container: null,
                
                init: function() {
                    // Создаем контейнер для объявления
                    this.container = document.getElementById('a11y-announcer');
                    
                    if (!this.container) {
                        this.container = document.createElement('div');
                        this.container.id = 'a11y-announcer';
                        this.container.setAttribute('aria-live', 'polite');
                        this.container.setAttribute('aria-atomic', 'true');
                        this.container.setAttribute('aria-relevant', 'additions text');
                        this.container.style.position = 'absolute';
                        this.container.style.left = '-10000px';
                        this.container.style.top = 'auto';
                        this.container.style.width = '1px';
                        this.container.style.height = '1px';
                        this.container.style.overflow = 'hidden';
                        
                        document.body.appendChild(this.container);
                    }
                },
                
                announce: function(message, priority) {
                    if (!this.container) {
                        this.init();
                    }
                    
                    // Устанавливаем приоритет
                    if (priority === 'assertive') {
                        this.container.setAttribute('aria-live', 'assertive');
                    } else {
                        this.container.setAttribute('aria-live', 'polite');
                    }
                    
                    // Очищаем и устанавливаем новое сообщение
                    this.container.textContent = '';
                    // Даем браузеру немного времени, затем устанавливаем сообщение
                    setTimeout(function() {
                        window.AtkA11yAnnouncer.container.textContent = message;
                    }, 100);
                    
                    // Сбрасываем приоритет обратно на polite
                    if (priority === 'assertive') {
                        setTimeout(function() {
                            window.AtkA11yAnnouncer.container.setAttribute('aria-live', 'polite');
                        }, 100);
                    }
                }
            };
            
            // Инициализируем при загрузке DOM
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    setupModalFocusManagement();
                    window.AtkA11yAnnouncer.init();
                });
            } else {
                setupModalFocusManagement();
                window.AtkA11yAnnouncer.init();
            }
            
            // Улучшенная обработка ошибок форм для вспомогательных технологий
            function enhanceFormErrorHandling() {
                // Обновляем обработку ошибок форм
                document.addEventListener('invalid', function(e) {
                    const field = e.target;
                    const errorMessage = field.validationMessage;
                    
                    if (errorMessage) {
                        // Добавляем ARIA-описание ошибки
                        const errorId = field.id + '-error';
                        let errorElement = document.getElementById(errorId);
                        
                        if (!errorElement) {
                            errorElement = document.createElement('div');
                            errorElement.id = errorId;
                            errorElement.className = 'sr-only form-error-message';
                            errorElement.setAttribute('aria-live', 'assertive');
                            errorElement.setAttribute('role', 'alert');
                            document.body.appendChild(errorElement);
                        }
                        
                        errorElement.textContent = errorMessage;
                        field.setAttribute('aria-describedby', errorId);
                        
                        // Объявляем ошибку через вспомогательные технологии
                        window.AtkA11yAnnouncer.announce(errorMessage, 'assertive');
                    }
                }, true);
                
                // Обработка успешных отправок форм
                document.addEventListener('submit', function(e) {
                    const form = e.target;
                    if (form.classList.contains('ajax-form')) {
                        // При успешной отправке формы объявляем результат
                        form.addEventListener('ajaxSuccess', function() {
                            window.AtkA11yAnnouncer.announce('<?php esc_attr_e( 'Форма успешно отправлена', 'atk-ved' ); ?>', 'polite');
                        });
                    }
                });
            }
            
            // Инициализация улучшенной обработки ошибок форм
            enhanceFormErrorHandling();
            
            // Улучшенная поддержка пользовательских настроек доступности
            function applyUserAccessibilityPreferences() {
                // Проверяем сохраненные настройки пользователя
                const highContrast = localStorage.getItem('a11y-high-contrast');
                const fontSize = localStorage.getItem('a11y-font-size');
                const reducedMotion = localStorage.getItem('a11y-reduced-motion');
                
                if (highContrast === 'true') {
                    document.documentElement.classList.add('high-contrast-mode');
                }
                
                if (fontSize) {
                    document.documentElement.style.setProperty('--a11y-font-scale', fontSize);
                }
                
                if (reducedMotion === 'true') {
                    document.documentElement.classList.add('reduced-motion-mode');
                }
            }
            
            // Применяем настройки при загрузке
            applyUserAccessibilityPreferences();
        })();
        </script>
        <?php
    }

    /**
     * Вывод улучшений для вспомогательных технологий
     */
    public function output_a11y_improvements(): void {
        ?>
        <style>
        /* Улучшения для пользователей с нарушениями зрения */
        .high-contrast-mode {
            filter: contrast(1.5) brightness(1.1);
        }
        
        /* Улучшения для пользователей с нарушениями восприятия движений */
        .reduced-motion-mode * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
        
        /* Улучшенные индикаторы фокуса */
        .keyboard-navigation :focus,
        .keyboard-navigation :focus-visible {
            outline: 3px solid #005fcc;
            outline-offset: 2px;
            border-radius: 2px;
        }
        
        /* Улучшенные индикаторы фокуса для темной темы */
        .dark-mode.keyboard-navigation :focus,
        .dark-mode.keyboard-navigation :focus-visible {
            outline: 3px solid #4d9eff;
            outline-offset: 2px;
        }
        
        /* Улучшенная видимость элементов управления */
        .keyboard-navigation .control-element,
        .keyboard-navigation button,
        .keyboard-navigation a,
        .keyboard-navigation input,
        .keyboard-navigation select,
        .keyboard-navigation textarea,
        .keyboard-navigation [tabindex]:not([tabindex="-1"]) {
            position: relative;
        }
        
        .keyboard-navigation .control-element::after,
        .keyboard-navigation button::after,
        .keyboard-navigation a::after,
        .keyboard-navigation input::after,
        .keyboard-navigation select::after,
        .keyboard-navigation textarea::after,
        .keyboard-navigation [tabindex]::after {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            border: 2px solid transparent;
            pointer-events: none;
        }
        
        .keyboard-navigation .control-element:focus::after,
        .keyboard-navigation button:focus::after,
        .keyboard-navigation a:focus::after,
        .keyboard-navigation input:focus::after,
        .keyboard-navigation select:focus::after,
        .keyboard-navigation textarea:focus::after,
        .keyboard-navigation [tabindex]:focus::after {
            border-color: #005fcc;
        }
        
        /* Тема для высокой контрастности */
        .high-contrast-mode {
            background: #000 !important;
            color: #fff !important;
        }
        
        .high-contrast-mode a,
        .high-contrast-mode button,
        .high-contrast-mode input,
        .high-contrast-mode select,
        .high-contrast-mode textarea {
            background: #fff !important;
            color: #000 !important;
            border: 2px solid #fff !important;
        }
        
        .high-contrast-mode .btn,
        .high-contrast-mode .button {
            background: #fff !important;
            color: #000 !important;
            border: 2px solid #000 !important;
        }
        
        .high-contrast-mode .btn:hover,
        .high-contrast-mode .button:hover {
            background: #000 !important;
            color: #fff !important;
        }
        
        /* Скрытие вспомогательных элементов */
        .sr-only {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }
        
        .sr-only.focusable:active,
        .sr-only.focusable:focus {
            position: static !important;
            width: auto !important;
            height: auto !important;
            overflow: visible !important;
            clip: auto !important;
            white-space: normal !important;
        }
        </style>
        <?php
    }
}

// Инициализация
function atk_ved_init_accessibility_enhancements(): void {
    ATK_VED_Accessibility_Enhancements::get_instance();
}
add_action( 'after_setup_theme', 'atk_ved_init_accessibility_enhancements' );