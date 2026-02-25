<?php
/**
 * АТК ВЭД Theme Functions
 *
 * @package ATK_VED
 * @since 1.0.0
 * @version 2.8.0
 *
 * @phpstan-ignore-file
 */

declare(strict_types=1);

// ============================================================================
// ПОДКЛЮЧЕНИЕ ФАЙЛОВ
// ============================================================================

// Ядро темы
require_once get_template_directory() . '/inc/custom-post-types.php';
require_once get_template_directory() . '/inc/helpers.php';
require_once get_template_directory() . '/inc/ajax-handlers.php';
require_once get_template_directory() . '/inc/translations.php';

// Безопасность
require_once get_template_directory() . '/inc/security.php';
require_once get_template_directory() . '/inc/security-advanced.php';
require_once get_template_directory() . '/inc/recaptcha.php';
require_once get_template_directory() . '/inc/cookie-banner.php';

// Оптимизация
require_once get_template_directory() . '/inc/logger.php';
require_once get_template_directory() . '/inc/pwa.php';

// SEO
require_once get_template_directory() . '/inc/seo.php';
require_once get_template_directory() . '/inc/sitemap.php';
require_once get_template_directory() . '/inc/breadcrumbs.php';

// Функциональность
require_once get_template_directory() . '/inc/calculator.php';
require_once get_template_directory() . '/inc/shipment-tracking.php';
require_once get_template_directory() . '/inc/ui-components.php';

// ACF
require_once get_template_directory() . '/inc/acf-field-groups.php';
require_once get_template_directory() . '/inc/acf-options.php';
require_once get_template_directory() . '/inc/acf-blocks.php';

// REST API
require_once get_template_directory() . '/inc/rest-api.php';
require_once get_template_directory() . '/inc/rest-cache.php';

// Виджеты
require_once get_template_directory() . '/inc/callback-widget.php';
require_once get_template_directory() . '/inc/chat-widget.php';

// E-commerce
require_once get_template_directory() . '/inc/woocommerce.php';
require_once get_template_directory() . '/inc/amocrm.php';

// Конверсия
require_once get_template_directory() . '/inc/conversion.php';

// Email
require_once get_template_directory() . '/inc/email-templates.php';

// Админка
require_once get_template_directory() . '/inc/admin-dashboard.php';
require_once get_template_directory() . '/inc/notifications.php';

// UI улучшения
require_once get_template_directory() . '/inc/enhanced-ui-components.php';
require_once get_template_directory() . '/inc/advanced-ui-components.php';
require_once get_template_directory() . '/inc/accessibility-enhancements.php';
require_once get_template_directory() . '/inc/health-check.php';
require_once get_template_directory() . '/inc/rest-cache.php';
require_once get_template_directory() . '/inc/demo-import.php';
require_once get_template_directory() . '/inc/demo-content.php';
require_once get_template_directory() . '/inc/welcome-page.php';
require_once get_template_directory() . '/inc/performance-analytics.php';
require_once get_template_directory() . '/inc/database-optimization.php';
require_once get_template_directory() . '/inc/advanced-security.php';

// Подключение стилей и скриптов
function atk_ved_enqueue_scripts() {
    // Современная система дизайна v3.1
    wp_enqueue_style('atk-ved-modern-design', get_template_directory_uri() . '/css/modern-design.css', array(), '3.1');
    wp_enqueue_style('atk-ved-animations-enhanced', get_template_directory_uri() . '/css/animations-enhanced.css', array(), '3.1');
    wp_enqueue_style('atk-ved-advanced-animations', get_template_directory_uri() . '/css/advanced-animations.css', array(), '3.1');
    wp_enqueue_style('atk-ved-modern-ui-components', get_template_directory_uri() . '/css/modern-ui-components.css', array('atk-ved-modern-design'), '3.1');
    wp_enqueue_style('atk-ved-landing-sections', get_template_directory_uri() . '/css/landing-sections.css', array(), '3.1');

    // Критический CSS inline
    wp_add_inline_style('atk-ved-style', file_get_contents(get_template_directory() . '/css/critical.css'));

    // Основные стили
    wp_enqueue_style('atk-ved-style', get_stylesheet_uri(), array('atk-ved-modern-design'), '3.1');

    // Компоненты UI
    wp_enqueue_style('atk-ved-ui-components', get_template_directory_uri() . '/css/ui-components.css', array('atk-ved-modern-design'), '3.1');
    wp_enqueue_style('atk-ved-additional-components', get_template_directory_uri() . '/css/additional-components.css', array('atk-ved-ui-components'), '3.1');
    
    // Функциональные компоненты
    wp_enqueue_style('atk-ved-modal', get_template_directory_uri() . '/css/modal.css', array(), '3.0');
    wp_enqueue_style('atk-ved-calculator', get_template_directory_uri() . '/css/calculator.css', array(), '3.0');
    wp_enqueue_style('atk-ved-tracking', get_template_directory_uri() . '/css/shipment-tracking.css', array(), '3.0');
    wp_enqueue_style('atk-ved-gallery', get_template_directory_uri() . '/css/gallery.css', array(), '3.0');
    
    // UX улучшения
    wp_enqueue_style('atk-ved-reviews-slider', get_template_directory_uri() . '/css/reviews-slider.css', array(), '3.0');
    wp_enqueue_style('atk-ved-callback-modal', get_template_directory_uri() . '/css/callback-modal.css', array(), '3.0');
    wp_enqueue_style('atk-ved-hero-counters', get_template_directory_uri() . '/css/hero-counters.css', array(), '3.0');
    wp_enqueue_style('atk-ved-form-loader', get_template_directory_uri() . '/css/form-loader.css', array(), '3.0');
    
    // Специализированные страницы
    wp_enqueue_style('atk-ved-404', get_template_directory_uri() . '/css/404.css', array(), '3.0');
    wp_enqueue_style('atk-ved-thank-you', get_template_directory_uri() . '/css/thank-you.css', array(), '3.0');
    wp_enqueue_style('atk-ved-statistics', get_template_directory_uri() . '/css/statistics.css', array(), '3.0');
    wp_enqueue_style('atk-ved-enhancements', get_template_directory_uri() . '/css/enhancements.css', array(), '3.0');

    // Основные скрипты
    wp_enqueue_script('atk-ved-loader', get_template_directory_uri() . '/js/loader.js', array(), '3.0', false);
    wp_enqueue_script('atk-ved-script', get_template_directory_uri() . '/js/main.js', array('jquery'), '3.0', true);
    
    // Компоненты UI
    wp_enqueue_script('atk-ved-ui-components', get_template_directory_uri() . '/js/ui-components.js', array('jquery'), '3.0', true);
    wp_enqueue_script('atk-ved-additional-components', get_template_directory_uri() . '/js/additional-components.js', array('jquery'), '3.0', true);
    
    // Функциональные компоненты
    wp_enqueue_script('atk-ved-modal', get_template_directory_uri() . '/js/modal.js', array('jquery'), '3.0', true);
    wp_enqueue_script('atk-ved-calculator', get_template_directory_uri() . '/js/calculator.js', array('jquery'), '3.0', true);
    wp_enqueue_script('atk-ved-calculator-frontend', get_template_directory_uri() . '/js/calculator-frontend.js', array('jquery'), '3.0', true);
    wp_enqueue_script('atk-ved-tracking-frontend', get_template_directory_uri() . '/js/shipment-tracking.js', array('jquery'), '3.0', true);
    wp_enqueue_script('atk-ved-gallery', get_template_directory_uri() . '/js/gallery.js', array('jquery'), '3.0', true);
    
    // UX улучшения
    wp_enqueue_script('atk-ved-reviews-slider', get_template_directory_uri() . '/js/reviews-slider.js', array('jquery'), '3.0', true);
    wp_enqueue_script('atk-ved-callback-modal', get_template_directory_uri() . '/js/callback-modal.js', array('jquery'), '3.0', true);
    wp_enqueue_script('atk-ved-hero-counters', get_template_directory_uri() . '/js/hero-counters.js', array('jquery'), '3.0', true);
    wp_enqueue_script('atk-ved-form-loader', get_template_directory_uri() . '/js/form-loader.js', array('jquery'), '3.0', true);
    
    // Вспомогательные скрипты
    wp_enqueue_script('atk-ved-enhancements', get_template_directory_uri() . '/js/enhancements.js', array('jquery'), '3.0', true);
    wp_enqueue_script('atk-ved-statistics', get_template_directory_uri() . '/js/statistics.js', array('jquery'), '3.0', true);
    wp_enqueue_script('atk-ved-tracking', get_template_directory_uri() . '/js/tracking.js', array('jquery'), '3.0', true);

    // Локализация скриптов
    wp_localize_script('atk-ved-script', 'atkVedData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('atk_ved_nonce'),
        'siteUrl' => home_url('/'),
        'metrikaId' => get_theme_mod('atk_ved_metrika_id', 0),
        'gaId' => get_theme_mod('atk_ved_ga_id', ''),
    ));
    
    // Передаем ID Метрики для аналитики
    $metrika_id = get_theme_mod('atk_ved_metrika_id', '');
    if (!empty($metrika_id)) {
        wp_add_inline_script('atk-ved-script', 'window.atkVedMetrikaId = ' . intval($metrika_id) . ';', 'before');
    }
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
    add_theme_support('responsive-embeds');
    add_theme_support('disable-custom-colors');
    add_theme_support('disable-custom-font-sizes');
    add_theme_support('editor-styles');
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
}
// Оптимизация изображений
function atk_ved_optimize_images($image_data) {
    // Добавляем ленивую загрузку
    $image_data['attr']['loading'] = 'lazy';
    
    // Добавляем атрибуты для оптимизации
    if (!isset($image_data['attr']['decoding'])) {
        $image_data['attr']['decoding'] = 'async';
    }
    
    return $image_data;
}
add_filter('wp_get_attachment_image_attributes', 'atk_ved_optimize_images');

// Минификация HTML вывода
function atk_ved_minify_html_output($buffer) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        return $buffer;
    }
    
    // Удаляем комментарии
    $buffer = preg_replace('/<!--(.|\s)*?-->/', '', $buffer);
    
    // Удаляем лишние пробелы
    $buffer = preg_replace('/\s+/', ' ', $buffer);
    
    // Удаляем пробелы вокруг тегов
    $buffer = preg_replace('/>\s+</', '><', $buffer);
    
    return $buffer;
}

// Добавим условие для минификации только на фронтенде
if (!is_admin() && !defined('DOING_AJAX') && !defined('DOING_CRON')) {
    ob_start('atk_ved_minify_html_output');
}

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

// Оптимизация заголовков
function atk_ved_optimize_headers() {
    // Добавляем предзагрузку критических ресурсов
    if (is_front_page()) {
        echo '<link rel="preload" href="' . get_template_directory_uri() . '/css/modern-design.css" as="style">' . "\n";
        echo '<link rel="preload" href="' . get_template_directory_uri() . '/js/main.js" as="script">' . "\n";
    }
    
    // Добавляем предконнект к внешним сервисам
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
add_action('wp_head', 'atk_ved_optimize_headers', 1);

// Оптимизация DNS prefetch
function atk_ved_dns_prefetch() {
    echo '<link rel="dns-prefetch" href="//www.google-analytics.com">' . "\n";
    echo '<link rel="dns-prefetch" href="//mc.yandex.ru">' . "\n";
}
add_action('wp_head', 'atk_ved_dns_prefetch', 0);

// Удаление ненужных мета-тегов
function atk_ved_clean_head() {
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
}
add_action('init', 'atk_ved_clean_head');
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


// Админские стили и скрипты
function atk_ved_admin_enqueue_scripts($hook) {
    global $post_type;
    
    if ($post_type === 'testimonial_file' || $hook === 'post-new.php' || $hook === 'post.php') {
        wp_enqueue_media();
        wp_enqueue_style('atk-ved-admin', get_template_directory_uri() . '/admin/admin-styles.css', array(), '2.0');
        wp_enqueue_script('atk-ved-admin', get_template_directory_uri() . '/admin/admin-enhancements.js', array('jquery'), '2.0', true);
        
        wp_localize_script('atk-ved-admin', 'atkAdminData', array(
            'nonce' => wp_create_nonce('atk_ved_admin_nonce'),
            'ajaxUrl' => admin_url('admin-ajax.php')
        ));
    }
}
add_action('admin_enqueue_scripts', 'atk_ved_admin_enqueue_scripts');

// Колонки в списке файлов отзывов
function atk_ved_testimonial_files_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['thumbnail'] = 'Превью';
    $new_columns['title'] = 'Название';
    $new_columns['company'] = 'Компания';
    $new_columns['file_type'] = 'Тип';
    $new_columns['date'] = 'Дата';
    return $new_columns;
}
add_filter('manage_testimonial_file_posts_columns', 'atk_ved_testimonial_files_columns');

function atk_ved_testimonial_files_column_content($column, $post_id) {
    switch ($column) {
        case 'thumbnail':
            if (has_post_thumbnail($post_id)) {
                echo get_the_post_thumbnail($post_id, array(60, 60));
            } else {
                echo '<span style="color: #ccc;">—</span>';
            }
            break;
        case 'company':
            $company = get_post_meta($post_id, '_company_name', true);
            echo $company ? esc_html($company) : '<span style="color: #ccc;">—</span>';
            break;
        case 'file_type':
            $type = get_post_meta($post_id, '_file_type', true);
            $icons = array(
                'pdf' => '📄',
                'image' => '🖼️',
                'doc' => '📝'
            );
            echo isset($icons[$type]) ? $icons[$type] . ' ' . strtoupper($type) : '—';
            break;
    }
}
add_action('manage_testimonial_file_posts_custom_column', 'atk_ved_testimonial_files_column_content', 10, 2);
