<?php
/**
 * Система темной темы v3.3
 * 
 * @package ATK_VED
 * @subpackage Dark_Mode
 */

if (!defined('ABSPATH')) {
    exit;
}

class ATK_VED_Dark_Mode {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'add_toggle_button'));
        add_action('customize_register', array($this, 'add_customizer_options'));
    }
    
    /**
     * Подключение стилей и скриптов
     */
    public function enqueue_scripts() {
        // CSS для темной темы
        wp_enqueue_style('atk-ved-dark-mode', get_template_directory_uri() . '/css/dark-mode.css', array(), '3.3');
        
        // JavaScript для переключения тем
        wp_enqueue_script('atk-ved-dark-mode-js', get_template_directory_uri() . '/js/dark-mode.js', array('jquery'), '3.3', true);
        
        // Локализация
        wp_localize_script('atk-ved-dark-mode-js', 'darkModeData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('dark_mode_nonce')
        ));
    }
    
    /**
     * Добавление кнопки переключения темы
     */
    public function add_toggle_button() {
        $theme_mod = get_theme_mod('atk_ved_dark_mode_default', 'auto');
        $show_toggle = get_theme_mod('atk_ved_show_dark_toggle', true);
        
        if (!$show_toggle) {
            return;
        }
        
        ?>
        <div class="dark-mode-toggle" data-default-theme="<?php echo esc_attr($theme_mod); ?>">
            <button class="theme-toggle-btn" aria-label="Переключить тему" title="Переключить тему">
                <span class="theme-icon light-icon">☀️</span>
                <span class="theme-icon dark-icon">🌙</span>
                <span class="theme-icon auto-icon">🌓</span>
            </button>
            <div class="theme-tooltip">
                <span class="light-text">Светлая тема</span>
                <span class="dark-text">Темная тема</span>
                <span class="auto-text">Авто тема</span>
            </div>
        </div>
        <?php
    }
    
    /**
     * Опции в Customizer
     */
    public function add_customizer_options($wp_customize) {
        // Секция темной темы
        $wp_customize->add_section('atk_ved_dark_mode', array(
            'title' => 'Темная тема',
            'priority' => 30,
        ));
        
        // Включить темную тему
        $wp_customize->add_setting('atk_ved_enable_dark_mode', array(
            'default' => true,
            'sanitize_callback' => 'atk_ved_sanitize_checkbox'
        ));
        
        $wp_customize->add_control('atk_ved_enable_dark_mode', array(
            'label' => 'Включить темную тему',
            'section' => 'atk_ved_dark_mode',
            'type' => 'checkbox'
        ));
        
        // Тема по умолчанию
        $wp_customize->add_setting('atk_ved_dark_mode_default', array(
            'default' => 'auto',
            'sanitize_callback' => 'atk_ved_sanitize_select'
        ));
        
        $wp_customize->add_control('atk_ved_dark_mode_default', array(
            'label' => 'Тема по умолчанию',
            'section' => 'atk_ved_dark_mode',
            'type' => 'select',
            'choices' => array(
                'light' => 'Светлая',
                'dark' => 'Темная',
                'auto' => 'Авто (системная)'
            )
        ));
        
        // Показывать кнопку переключения
        $wp_customize->add_setting('atk_ved_show_dark_toggle', array(
            'default' => true,
            'sanitize_callback' => 'atk_ved_sanitize_checkbox'
        ));
        
        $wp_customize->add_control('atk_ved_show_dark_toggle', array(
            'label' => 'Показывать кнопку переключения',
            'section' => 'atk_ved_dark_mode',
            'type' => 'checkbox'
        ));
        
        // Переходы между темами
        $wp_customize->add_setting('atk_ved_dark_mode_transition', array(
            'default' => true,
            'sanitize_callback' => 'atk_ved_sanitize_checkbox'
        ));
        
        $wp_customize->add_control('atk_ved_dark_mode_transition', array(
            'label' => 'Плавные переходы',
            'section' => 'atk_ved_dark_mode',
            'type' => 'checkbox'
        ));
    }
    
    /**
     * AJAX обработчик переключения темы
     */
    public static function handle_theme_switch() {
        check_ajax_referer('dark_mode_nonce', 'nonce');
        
        $theme = sanitize_text_field($_POST['theme'] ?? 'auto');
        $valid_themes = array('light', 'dark', 'auto');
        
        if (!in_array($theme, $valid_themes)) {
            wp_send_json_error('Invalid theme');
        }
        
        // Сохраняем выбор пользователя
        setcookie('atk_ved_theme', $theme, time() + (86400 * 30), '/'); // 30 дней
        
        wp_send_json_success(array(
            'theme' => $theme,
            'message' => 'Theme switched successfully'
        ));
    }
    
    /**
     * Получение текущей темы
     */
    public static function get_current_theme(): string {
        // Проверяем cookie
        if (isset($_COOKIE['atk_ved_theme'])) {
            $theme = sanitize_text_field($_COOKIE['atk_ved_theme']);
            if (in_array($theme, array('light', 'dark', 'auto'))) {
                return $theme;
            }
        }
        
        // Возвращаем тему по умолчанию
        return get_theme_mod('atk_ved_dark_mode_default', 'auto');
    }
    
    /**
     * Добавление класса темы к body
     */
    public static function add_body_class($classes) {
        if (!get_theme_mod('atk_ved_enable_dark_mode', true)) {
            return $classes;
        }
        
        $theme = self::get_current_theme();
        $classes[] = 'theme-' . $theme;
        
        if (get_theme_mod('atk_ved_dark_mode_transition', true)) {
            $classes[] = 'theme-transition';
        }
        
        return $classes;
    }
}

// Инициализация
function atk_ved_init_dark_mode() {
    $dark_mode = ATK_VED_Dark_Mode::get_instance();
    
    // Добавляем классы к body
    add_filter('body_class', array('ATK_VED_Dark_Mode', 'add_body_class'));
    
    // AJAX обработчики
    add_action('wp_ajax_atk_ved_switch_theme', array('ATK_VED_Dark_Mode', 'handle_theme_switch'));
    add_action('wp_ajax_nopriv_atk_ved_switch_theme', array('ATK_VED_Dark_Mode', 'handle_theme_switch'));
}
add_action('after_setup_theme', 'atk_ved_init_dark_mode');

// Санитизация функции
function atk_ved_sanitize_checkbox($checked) {
    return ((isset($checked) && true == $checked) ? true : false);
}

function atk_ved_sanitize_select($input, $setting) {
    $input = sanitize_key($input);
    $choices = $setting->manager->get_control($setting->id)->choices;
    return (array_key_exists($input, $choices) ? $input : $setting->default);
}