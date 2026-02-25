<?php
/**
 * Оптимизация производительности
 */

// Отключение эмодзи
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_styles', 'print_emoji_styles');
remove_filter('the_content_feed', 'wp_staticize_emoji');
remove_filter('comment_text_rss', 'wp_staticize_emoji');
remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

// Отключение встроенных стилей блоков
function atk_ved_disable_block_styles() {
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('wc-blocks-style');
    wp_dequeue_style('global-styles');
}
add_action('wp_enqueue_scripts', 'atk_ved_disable_block_styles', 100);

// Отключение jQuery Migrate
function atk_ved_remove_jquery_migrate($scripts) {
    if (!is_admin() && isset($scripts->registered['jquery'])) {
        $script = $scripts->registered['jquery'];
        if ($script->deps) {
            $script->deps = array_diff($script->deps, array('jquery-migrate'));
        }
    }
}
add_action('wp_default_scripts', 'atk_ved_remove_jquery_migrate');

// Отложенная загрузка скриптов
function atk_ved_defer_scripts($tag, $handle, $src) {
    $defer_scripts = array('atk-ved-script', 'atk-ved-ui');
    
    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }
    
    return $tag;
}
add_filter('script_loader_tag', 'atk_ved_defer_scripts', 10, 3);

// Preload критических ресурсов
function atk_ved_preload_resources() {
    echo '<link rel="preload" href="' . get_stylesheet_uri() . '" as="style">';
    echo '<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>';
    echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">';
}
add_action('wp_head', 'atk_ved_preload_resources', 1);

// Отключение REST API для неавторизованных пользователей
function atk_ved_disable_rest_api($access) {
    if (!is_user_logged_in()) {
        return new WP_Error('rest_disabled', 'REST API отключен', array('status' => 403));
    }
    return $access;
}
// add_filter('rest_authentication_errors', 'atk_ved_disable_rest_api');

// Удаление версий из URL
function atk_ved_remove_version_strings($src) {
    if (strpos($src, 'ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('style_loader_src', 'atk_ved_remove_version_strings', 9999);
add_filter('script_loader_src', 'atk_ved_remove_version_strings', 9999);

// Отключение XML-RPC
add_filter('xmlrpc_enabled', '__return_false');

// Удаление ненужных мета-тегов
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);

// Оптимизация базы данных
function atk_ved_optimize_database() {
    global $wpdb;
    
    // Очистка ревизий старше 30 дней
    $wpdb->query("DELETE FROM $wpdb->posts WHERE post_type = 'revision' AND post_modified < DATE_SUB(NOW(), INTERVAL 30 DAY)");
    
    // Очистка автосохранений
    $wpdb->query("DELETE FROM $wpdb->posts WHERE post_status = 'auto-draft'");
    
    // Очистка корзины
    $wpdb->query("DELETE FROM $wpdb->posts WHERE post_status = 'trash'");
    
    // Очистка спама
    $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = 'spam'");
    
    // Оптимизация таблиц
    $tables = $wpdb->get_results('SHOW TABLES', ARRAY_N);
    foreach ($tables as $table) {
        $wpdb->query("OPTIMIZE TABLE {$table[0]}");
    }
}

// Запуск оптимизации раз в неделю
if (!wp_next_scheduled('atk_ved_weekly_optimization')) {
    wp_schedule_event(time(), 'weekly', 'atk_ved_weekly_optimization');
}
add_action('atk_ved_weekly_optimization', 'atk_ved_optimize_database');

// Ограничение ревизий
if (!defined('WP_POST_REVISIONS')) {
    define('WP_POST_REVISIONS', 3);
}

// Увеличение времени автосохранения
if (!defined('AUTOSAVE_INTERVAL')) {
    define('AUTOSAVE_INTERVAL', 300); // 5 минут
}

// Сжатие HTML
function atk_ved_compress_html($html) {
    if (!is_admin()) {
        $html = preg_replace('/<!--(?!<!)[^\[>].*?-->/', '', $html);
        $html = preg_replace('/\s+/', ' ', $html);
        $html = preg_replace('/>\s+</', '><', $html);
    }
    return $html;
}
// add_action('wp_loaded', function() {
//     ob_start('atk_ved_compress_html');
// });

// Lazy loading для изображений
function atk_ved_add_lazy_loading($content) {
    if (is_admin()) {
        return $content;
    }
    
    $content = preg_replace_callback('/<img([^>]+?)src=/i', function($matches) {
        // Проверяем, есть ли уже loading атрибут
        if (strpos($matches[0], 'loading=') === false) {
            return '<img' . $matches[1] . 'loading="lazy" src=';
        }
        return $matches[0];
    }, $content);
    
    return $content;
}
add_filter('the_content', 'atk_ved_add_lazy_loading', 99);
add_filter('post_thumbnail_html', 'atk_ved_add_lazy_loading', 99);

// Native lazy loading для всех изображений
function atk_ved_native_lazy_load_images($image, $attachment_id, $size, $icon, $attr) {
    if (!isset($attr['loading'])) {
        $attr['loading'] = 'lazy';
    }
    return wp_get_attachment_image($attachment_id, $size, $icon, $attr);
}
add_filter('wp_get_attachment_image', 'atk_ved_native_lazy_load_images', 10, 5);

// Отключение Heartbeat API
function atk_ved_disable_heartbeat() {
    wp_deregister_script('heartbeat');
}
add_action('wp_enqueue_scripts', 'atk_ved_disable_heartbeat', 1);

// Ограничение Heartbeat в админке
function atk_ved_heartbeat_settings($settings) {
    $settings['interval'] = 60; // 60 секунд
    return $settings;
}
add_filter('heartbeat_settings', 'atk_ved_heartbeat_settings');

// Отключение встроенных шрифтов
function atk_ved_disable_dashicons() {
    if (!is_user_logged_in()) {
        wp_deregister_style('dashicons');
    }
}
add_action('wp_enqueue_scripts', 'atk_ved_disable_dashicons');

// Кэширование запросов
function atk_ved_cache_query($query_key, $callback, $expiration = 3600) {
    $cached = get_transient($query_key);
    
    if (false === $cached) {
        $cached = $callback();
        set_transient($query_key, $cached, $expiration);
    }
    
    return $cached;
}

// Улучшенное кэширование с поддержкой объектов
function atk_ved_object_cache($cache_key, $callback, $expiration = 3600) {
    // Сначала проверяем объектный кэш WordPress
    $cached = wp_cache_get($cache_key, 'atk_ved');
    
    if (false === $cached) {
        $cached = $callback();
        wp_cache_set($cache_key, $cached, 'atk_ved', $expiration);
    }
    
    return $cached;
}

// Кэширование HTML-блока
function atk_ved_cache_html_block($block_name, $callback, $expiration = 1800) {
    $cache_key = 'atk_ved_block_' . $block_name . '_' . md5(serialize(get_queried_object()));
    $cached = get_transient($cache_key);
    
    if (false === $cached) {
        $cached = $callback();
        set_transient($cache_key, $cached, $expiration);
    }
    
    return $cached;
}

// Очистка кэша при обновлении контента
function atk_ved_clear_cache($post_id) {
    delete_transient('atk_ved_services');
    delete_transient('atk_ved_reviews');
    delete_transient('atk_ved_faq');
}
add_action('save_post', 'atk_ved_clear_cache');
add_action('deleted_post', 'atk_ved_clear_cache');

// Оптимизация изображений при загрузке
function atk_ved_optimize_image_quality($quality, $mime_type) {
    return 85; // Качество 85%
}
add_filter('jpeg_quality', 'atk_ved_optimize_image_quality', 10, 2);
add_filter('wp_editor_set_quality', 'atk_ved_optimize_image_quality', 10, 2);

// Поддержка WebP
function atk_ved_support_webp($filters) {
    $filters[] = 'image/webp';
    return $filters;
}
add_filter('wp_image_editors', 'atk_ved_support_webp');

// Оптимизация размеров изображений
function atk_ved_additional_image_sizes() {
    add_image_size('atk-ved-large', 1200, 800, true);
    add_image_size('atk-ved-medium', 800, 600, true);
    add_image_size('atk-ved-small', 400, 300, true);
    add_image_size('atk-ved-tiny', 200, 150, true);
}
add_action('after_setup_theme', 'atk_ved_additional_image_sizes');

// Отключение srcset для экономии ресурсов (опционально)
// add_filter('max_srcset_image_width', '__return_false');

// Prefetch DNS для внешних ресурсов
function atk_ved_dns_prefetch() {
    echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">';
    echo '<link rel="dns-prefetch" href="//www.google-analytics.com">';
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
    echo '<link rel="preload" as="font" type="font/woff2" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" crossorigin>';
}
add_action('wp_head', 'atk_ved_dns_prefetch', 0);

// Оптимизация шрифтов
function atk_ved_optimize_fonts() {
    // Отложенная загрузка Google Fonts
    $font_families = array(
        'Inter:300,400,500,600,700'
    );
    
    $query_args = array(
        'family' => implode('|', $font_families),
        'subset' => 'cyrillic,cyrillic-ext,latin',
        'display' => 'swap'
    );
    
    $fonts_url = add_query_arg($query_args, 'https://fonts.googleapis.com/css');
    
    echo '<link rel="preload" href="' . esc_url($fonts_url) . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
    echo '<noscript><link rel="stylesheet" href="' . esc_url($fonts_url) . '"></noscript>';
}
// add_action('wp_head', 'atk_ved_optimize_fonts', 1);

// Отключение встроенных стилей Gutenberg
function atk_ved_remove_gutenberg_styles() {
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
}
add_action('wp_print_styles', 'atk_ved_remove_gutenberg_styles', 100);

// Минимизация запросов к базе данных
function atk_ved_limit_post_revisions($num, $post) {
    return 3;
}
add_filter('wp_revisions_to_keep', 'atk_ved_limit_post_revisions', 10, 2);
