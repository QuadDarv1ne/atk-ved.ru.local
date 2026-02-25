<?php
/**
 * АТК ВЭД Theme Functions
 * 
 * @package ATK_VED
 * @since 1.0.0
 * @version 1.7.0
 */

declare(strict_types=1);

// Подключение дополнительных файлов
require_once get_template_directory() . '/inc/custom-post-types.php';
require_once get_template_directory() . '/inc/helpers.php';
require_once get_template_directory() . '/inc/ajax-handlers.php';
require_once get_template_directory() . '/inc/performance.php';
require_once get_template_directory() . '/inc/seo.php';
require_once get_template_directory() . '/inc/security.php';
// require_once get_template_directory() . '/inc/optimization.php'; // Отключено - дублирует performance.php
require_once get_template_directory() . '/inc/translations.php';
require_once get_template_directory() . '/inc/analytics.php';
require_once get_template_directory() . '/inc/cookie-banner.php';
require_once get_template_directory() . '/inc/pwa.php';
require_once get_template_directory() . '/inc/logger.php';

// Подключение стилей и скриптов
function atk_ved_enqueue_scripts() {
    // Критический CSS inline
    wp_add_inline_style('atk-ved-style', file_get_contents(get_template_directory() . '/css/critical.css'));

    // Стили
    wp_enqueue_style('atk-ved-style', get_stylesheet_uri(), array(), '1.9');
    wp_enqueue_style('atk-ved-modal', get_template_directory_uri() . '/css/modal.css', array(), '1.9');
    wp_enqueue_style('atk-ved-calculator', get_template_directory_uri() . '/css/calculator.css', array(), '1.9');
    wp_enqueue_style('atk-ved-enhancements', get_template_directory_uri() . '/css/enhancements.css', array(), '1.9');
    wp_enqueue_style('atk-ved-statistics', get_template_directory_uri() . '/css/statistics.css', array(), '1.9');
    wp_enqueue_style('atk-ved-tracking', get_template_directory_uri() . '/css/tracking.css', array(), '1.9');
    wp_enqueue_style('atk-ved-gallery', get_template_directory_uri() . '/css/gallery.css', array(), '1.9');
    wp_enqueue_style('atk-ved-404', get_template_directory_uri() . '/css/404.css', array(), '1.9');
    wp_enqueue_style('atk-ved-thank-you', get_template_directory_uri() . '/css/thank-you.css', array(), '1.9');

    // UX Enhancements v1.9
    wp_enqueue_style('atk-ved-reviews-slider', get_template_directory_uri() . '/css/reviews-slider.css', array(), '1.9');
    wp_enqueue_style('atk-ved-callback-modal', get_template_directory_uri() . '/css/callback-modal.css', array(), '1.9');
    wp_enqueue_style('atk-ved-hero-counters', get_template_directory_uri() . '/css/hero-counters.css', array(), '1.9');
    wp_enqueue_style('atk-ved-form-loader', get_template_directory_uri() . '/css/form-loader.css', array(), '1.9');

    // Скрипты
    wp_enqueue_script('atk-ved-loader', get_template_directory_uri() . '/js/loader.js', array(), '1.9', false);
    wp_enqueue_script('atk-ved-script', get_template_directory_uri() . '/js/main.js', array('jquery'), '1.9', true);
    wp_enqueue_script('atk-ved-modal', get_template_directory_uri() . '/js/modal.js', array('jquery'), '1.9', true);
    wp_enqueue_script('atk-ved-calculator', get_template_directory_uri() . '/js/calculator.js', array('jquery'), '1.9', true);
    wp_enqueue_script('atk-ved-enhancements', get_template_directory_uri() . '/js/enhancements.js', array('jquery'), '1.9', true);
    wp_enqueue_script('atk-ved-statistics', get_template_directory_uri() . '/js/statistics.js', array('jquery'), '1.9', true);
    wp_enqueue_script('atk-ved-tracking', get_template_directory_uri() . '/js/tracking.js', array('jquery'), '1.9', true);
    wp_enqueue_script('atk-ved-gallery', get_template_directory_uri() . '/js/gallery.js', array('jquery'), '1.9', true);
    
    // UX Enhancements v1.9
    wp_enqueue_script('atk-ved-reviews-slider', get_template_directory_uri() . '/js/reviews-slider.js', array('jquery'), '1.9', true);
    wp_enqueue_script('atk-ved-callback-modal', get_template_directory_uri() . '/js/callback-modal.js', array('jquery'), '1.9', true);
    wp_enqueue_script('atk-ved-hero-counters', get_template_directory_uri() . '/js/hero-counters.js', array('jquery'), '1.9', true);
    wp_enqueue_script('atk-ved-form-loader', get_template_directory_uri() . '/js/form-loader.js', array('jquery'), '1.9', true);

    // Локализация скриптов
    wp_localize_script('atk-ved-script', 'atkVedData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('atk_ved_nonce'),
        'siteUrl' => home_url('/'),
    ));
}
add_action('wp_enqueue_scripts', 'atk_ved_enqueue_scripts');

// Поддержка меню
function atk_ved_register_menus() {
    register_nav_menus(array(
        'primary' => __('Главное меню', 'atk-ved'),
        'footer' => __('Меню в подвале', 'atk-ved')
    ));
}
add_action('init', 'atk_ved_register_menus');

// Поддержка возможностей темы
function atk_ved_theme_support() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
}
add_action('after_setup_theme', 'atk_ved_theme_support');

// Регистрация виджетов
function atk_ved_widgets_init() {
    register_sidebar(array(
        'name'          => __('Сайдбар', 'atk-ved'),
        'id'            => 'sidebar-1',
        'description'   => __('Добавьте виджеты сюда', 'atk-ved'),
        'before_widget' => '<div class="widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'atk_ved_widgets_init');
function atk_ved_customize_register($wp_customize) {
    // Секция контактов
    $wp_customize->add_section('atk_ved_contacts', array(
        'title' => __('Контакты', 'atk-ved'),
        'priority' => 30,
    ));
    
    // Телефон
    $wp_customize->add_setting('atk_ved_phone', array(
        'default' => '+7 (XXX) XXX-XX-XX',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('atk_ved_phone', array(
        'label' => __('Телефон', 'atk-ved'),
        'section' => 'atk_ved_contacts',
        'type' => 'text',
    ));
    
    // Email
    $wp_customize->add_setting('atk_ved_email', array(
        'default' => 'info@atk-ved.ru',
        'sanitize_callback' => 'sanitize_email',
    ));
    
    $wp_customize->add_control('atk_ved_email', array(
        'label' => __('Email', 'atk-ved'),
        'section' => 'atk_ved_contacts',
        'type' => 'email',
    ));
    
    // Адрес
    $wp_customize->add_setting('atk_ved_address', array(
        'default' => 'Москва, Россия',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('atk_ved_address', array(
        'label' => __('Адрес', 'atk-ved'),
        'section' => 'atk_ved_contacts',
        'type' => 'text',
    ));
    
    // Секция социальных сетей
    $wp_customize->add_section('atk_ved_social', array(
        'title' => __('Социальные сети', 'atk-ved'),
        'priority' => 31,
    ));
    
    // WhatsApp
    $wp_customize->add_setting('atk_ved_whatsapp', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('atk_ved_whatsapp', array(
        'label' => __('WhatsApp (ссылка)', 'atk-ved'),
        'section' => 'atk_ved_social',
        'type' => 'url',
    ));
    
    // Telegram
    $wp_customize->add_setting('atk_ved_telegram', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('atk_ved_telegram', array(
        'label' => __('Telegram (ссылка)', 'atk-ved'),
        'section' => 'atk_ved_social',
        'type' => 'url',
    ));
    
    // VK
    $wp_customize->add_setting('atk_ved_vk', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('atk_ved_vk', array(
        'label' => __('VK (ссылка)', 'atk-ved'),
        'section' => 'atk_ved_social',
        'type' => 'url',
    ));
    
    // Секция Hero
    $wp_customize->add_section('atk_ved_hero', array(
        'title' => __('Главный экран', 'atk-ved'),
        'priority' => 32,
    ));
    
    // Заголовок Hero
    $wp_customize->add_setting('atk_ved_hero_title', array(
        'default' => 'ТОВАРЫ ДЛЯ МАРКЕТПЛЕЙСОВ ИЗ КИТАЯ ОПТОМ',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('atk_ved_hero_title', array(
        'label' => __('Заголовок', 'atk-ved'),
        'section' => 'atk_ved_hero',
        'type' => 'text',
    ));
    
    // Статистика 1
    $wp_customize->add_setting('atk_ved_stat1_number', array(
        'default' => '500+',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('atk_ved_stat1_number', array(
        'label' => __('Статистика 1 - Число', 'atk-ved'),
        'section' => 'atk_ved_hero',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('atk_ved_stat1_label', array(
        'default' => 'КЛИЕНТОВ',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('atk_ved_stat1_label', array(
        'label' => __('Статистика 1 - Подпись', 'atk-ved'),
        'section' => 'atk_ved_hero',
        'type' => 'text',
    ));
}
add_action('customize_register', 'atk_ved_customize_register');

// Добавление размеров изображений
add_image_size('atk-ved-hero', 800, 600, true);
add_image_size('atk-ved-service', 400, 300, true);

// Шорткод для кнопки
function atk_ved_button_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'url' => '#',
        'style' => 'primary',
        'target' => '_self'
    ), $atts);
    
    return '<a href="' . esc_url($atts['url']) . '" class="cta-button ' . esc_attr($atts['style']) . '" target="' . esc_attr($atts['target']) . '">' . esc_html($content) . '</a>';
}
add_shortcode('button', 'atk_ved_button_shortcode');

// Шорткод для иконки
function atk_ved_icon_shortcode($atts) {
    $atts = shortcode_atts(array(
        'name' => 'check',
        'size' => '24'
    ), $atts);
    
    return '<span class="icon icon-' . esc_attr($atts['name']) . '" style="font-size: ' . esc_attr($atts['size']) . 'px;"></span>';
}
add_shortcode('icon', 'atk_ved_icon_shortcode');
