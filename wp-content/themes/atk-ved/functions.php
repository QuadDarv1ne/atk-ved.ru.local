<?php
/**
 * АТК ВЭД Theme Functions
 */

// Подключение стилей и скриптов
function atk_ved_enqueue_scripts() {
    wp_enqueue_style('atk-ved-style', get_stylesheet_uri());
    wp_enqueue_script('atk-ved-script', get_template_directory_uri() . '/js/main.js', array('jquery'), '1.0', true);
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

// Кастомные настройки темы
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
}
add_action('customize_register', 'atk_ved_customize_register');
