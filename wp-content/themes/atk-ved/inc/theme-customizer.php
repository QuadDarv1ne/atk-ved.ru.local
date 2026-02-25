<?php
/**
 * Theme Customizer Components
 * Компоненты переключателя темы и панели настроек
 * 
 * @package ATK_VED
 * @since 3.3.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Добавление переключателя темы в footer
 */
function atk_ved_theme_switcher_html(): void {
    ?>
    <!-- Theme Switcher -->
    <div class="theme-switcher">
        <button type="button" class="theme-toggle-btn" data-tooltip="Переключить тему" aria-label="Переключить тёмную тему">
            <div class="theme-toggle-icon">
                <div class="sun">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="5"></circle>
                        <line x1="12" y1="1" x2="12" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="23"></line>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                        <line x1="1" y1="12" x2="3" y2="12"></line>
                        <line x1="21" y1="12" x2="23" y2="12"></line>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                    </svg>
                </div>
                <div class="moon">
                    <svg viewBox="0 0 24 24">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                </div>
            </div>
        </button>
        
        <!-- Theme Panel -->
        <div class="theme-panel">
            <div class="theme-panel-header">
                <h4 class="theme-panel-title">⚙️ Настройки темы</h4>
                <button type="button" class="theme-panel-close" aria-label="Закрыть панель">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            
            <div class="theme-options">
                <!-- Primary Color -->
                <div class="theme-option">
                    <label class="theme-option-label">Цвет бренда</label>
                    <div class="theme-colors">
                        <button type="button" class="theme-color-btn color-red active" data-color="red" aria-label="Красный"></button>
                        <button type="button" class="theme-color-btn color-blue" data-color="blue" aria-label="Синий"></button>
                        <button type="button" class="theme-color-btn color-green" data-color="green" aria-label="Зелёный"></button>
                        <button type="button" class="theme-color-btn color-purple" data-color="purple" aria-label="Фиолетовый"></button>
                        <button type="button" class="theme-color-btn color-orange" data-color="orange" aria-label="Оранжевый"></button>
                        <button type="button" class="theme-color-btn color-teal" data-color="teal" aria-label="Бирюзовый"></button>
                    </div>
                </div>
                
                <!-- Font Size -->
                <div class="theme-option">
                    <label class="theme-option-label">Размер шрифта</label>
                    <div class="font-size-options">
                        <button type="button" class="font-size-btn" data-size="small">A-</button>
                        <button type="button" class="font-size-btn active" data-size="base">A</button>
                        <button type="button" class="font-size-btn" data-size="large">A+</button>
                        <button type="button" class="font-size-btn" data-size="xlarge">A++</button>
                    </div>
                </div>
                
                <!-- Contrast -->
                <div class="theme-option">
                    <label class="theme-option-label">Контрастность</label>
                    <div class="contrast-options">
                        <button type="button" class="contrast-btn active" data-contrast="normal">Обычная</button>
                        <button type="button" class="contrast-btn" data-contrast="high">Высокая</button>
                    </div>
                </div>
            </div>
            
            <button type="button" class="theme-reset">
                🔄 Сбросить настройки
            </button>
        </div>
    </div>
    
    <style>
        /* Quick styles for toggle button visibility */
        .theme-switcher {
            position: fixed;
            bottom: 30px;
            left: 30px;
            z-index: 9999;
        }
        
        @media (max-width: 768px) {
            .theme-switcher {
                bottom: 20px;
                left: 20px;
            }
        }
    </style>
    <?php
}
add_action('wp_footer', 'atk_ved_theme_switcher_html', 100);

/**
 * Подключение скриптов и стилей
 */
function atk_ved_theme_customizer_scripts(): void {
    wp_enqueue_style('atk-ved-dark-mode-toggle', get_template_directory_uri() . '/css/dark-mode-toggle.css', array(), '3.3');
    wp_enqueue_script('atk-ved-theme-customizer', get_template_directory_uri() . '/js/theme-customizer.js', array('jquery'), '3.3', true);
    
    // Локализация для аналитики
    wp_localize_script('atk-ved-theme-customizer', 'atkVedData', array(
        'metrikaId' => get_theme_mod('atk_ved_metrika_id', 0),
        'gaId' => get_theme_mod('atk_ved_ga_id', ''),
    ));
}
add_action('wp_enqueue_scripts', 'atk_ved_theme_customizer_scripts');

/**
 * Настройки в Customizer
 */
function atk_ved_theme_appearance_customizer($wp_customize): void {
    $wp_customize->add_section('atk_ved_theme_appearance', array(
        'title'    => __('Внешний вид темы', 'atk-ved'),
        'priority' => 46,
    ));
    
    // Default Dark Mode
    $wp_customize->add_setting('atk_ved_default_dark_mode', array(
        'default'           => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));
    
    $wp_customize->add_control('atk_ved_default_dark_mode', array(
        'label'   => __('Тёмная тема по умолчанию', 'atk-ved'),
        'section' => 'atk_ved_theme_appearance',
        'type'    => 'checkbox',
    ));
    
    // Default Primary Color
    $wp_customize->add_setting('atk_ved_default_primary_color', array(
        'default'           => 'red',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('atk_ved_default_primary_color', array(
        'label'   => __('Цвет бренда по умолчанию', 'atk-ved'),
        'section' => 'atk_ved_theme_appearance',
        'type'    => 'select',
        'choices' => array(
            'red'    => 'Красный',
            'blue'   => 'Синий',
            'green'  => 'Зелёный',
            'purple' => 'Фиолетовый',
            'orange' => 'Оранжевый',
            'teal'   => 'Бирюзовый',
        ),
    ));
    
    // Enable Theme Switcher
    $wp_customize->add_setting('atk_ved_enable_theme_switcher', array(
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));
    
    $wp_customize->add_control('atk_ved_enable_theme_switcher', array(
        'label'   => __('Включить переключатель темы', 'atk-ved'),
        'section' => 'atk_ved_theme_appearance',
        'type'    => 'checkbox',
    ));
}
add_action('customize_register', 'atk_ved_theme_appearance_customizer');

/**
 * Server-side dark mode detection (for initial render)
 */
function atk_ved_body_classes($classes): array {
    // Check if user has dark mode saved in localStorage (requires JS)
    // This is a fallback for initial page load
    if (get_theme_mod('atk_ved_default_dark_mode', false)) {
        $classes[] = 'dark-mode';
    }
    
    return $classes;
}
add_filter('body_class', 'atk_ved_body_classes');
