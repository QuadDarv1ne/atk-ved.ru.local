<?php
/**
 * Performance Optimization Functions
 *
 * @package ATK_VED
 */

defined('ABSPATH') || exit;

/**
 * Добавляем Browser Cache Headers
 */
add_action('send_headers', function() {
    if (!is_admin()) {
        // Cache для статических ресурсов
        if (preg_match('/\.(css|js|jpg|jpeg|png|gif|webp|svg|woff|woff2|ttf|eot)$/i', $_SERVER['REQUEST_URI'])) {
            header('Cache-Control: public, max-age=31536000, immutable');
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        }
    }
});

/**
 * Отключаем emoji скрипты
 */
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles', 'print_emoji_styles');

/**
 * Удаляем query strings из статических ресурсов
 */
add_filter('script_loader_src', function($src) {
    if (strpos($src, '?ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}, 15);

add_filter('style_loader_src', function($src) {
    if (strpos($src, '?ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}, 15);

/**
 * Отключаем embeds
 */
add_action('init', function() {
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
    remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
});

/**
 * Отключаем REST API для неавторизованных
 */
add_filter('rest_authentication_errors', function($result) {
    if (!is_user_logged_in()) {
        return new WP_Error(
            'rest_disabled',
            __('REST API отключен для неавторизованных пользователей', 'atk-ved'),
            ['status' => 401]
        );
    }
    return $result;
});

/**
 * Оптимизация базы данных - очистка ревизий
 */
add_action('wp_scheduled_delete', function() {
    global $wpdb;
    
    // Удаляем старые ревизии (старше 30 дней)
    $wpdb->query("
        DELETE FROM {$wpdb->posts}
        WHERE post_type = 'revision'
        AND post_modified < DATE_SUB(NOW(), INTERVAL 30 DAY)
    ");
    
    // Удаляем автосохранения
    $wpdb->query("
        DELETE FROM {$wpdb->posts}
        WHERE post_status = 'auto-draft'
        AND post_modified < DATE_SUB(NOW(), INTERVAL 7 DAY)
    ");
    
    // Оптимизируем таблицы
    $wpdb->query("OPTIMIZE TABLE {$wpdb->posts}");
    $wpdb->query("OPTIMIZE TABLE {$wpdb->postmeta}");
});

/**
 * Ограничиваем количество ревизий
 */
if (!defined('WP_POST_REVISIONS')) {
    define('WP_POST_REVISIONS', 5);
}

/**
 * Увеличиваем интервал автосохранения
 */
if (!defined('AUTOSAVE_INTERVAL')) {
    define('AUTOSAVE_INTERVAL', 300); // 5 минут
}

/**
 * Отключаем XML-RPC
 */
add_filter('xmlrpc_enabled', '__return_false');

/**
 * Удаляем лишние meta теги из head
 */
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);

/**
 * Отложенная загрузка Gravatar
 */
add_filter('get_avatar', function($avatar) {
    return str_replace('src=', 'loading="lazy" src=', $avatar);
});

/**
 * Preconnect для внешних ресурсов
 */
add_action('wp_head', function() {
    echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">' . "\n";
    echo '<link rel="dns-prefetch" href="//fonts.gstatic.com">' . "\n";
    echo '<link rel="dns-prefetch" href="//cdn.jsdelivr.net">' . "\n";
}, 1);

/**
 * Defer для не критичных скриптов
 */
add_filter('script_loader_tag', function($tag, $handle) {
    // Список скриптов для defer
    $defer_scripts = [
        'atk-components',
        'atk-interactions',
        'atk-landing-premium',
        'atk-counters',
        'atk-share',
        'atk-lazy-images',
        'bootstrap'
    ];
    
    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }
    
    return $tag;
}, 10, 2);

/**
 * Кэширование меню - ВРЕМЕННО ОТКЛЮЧЕНО
 * 
 * Menu caching has been temporarily disabled to fix site loading issues.
 * The serialize($args) approach was causing hangs with complex menu objects.
 */

// Menu caching disabled - uncomment and fix if needed
/*
add_filter('pre_wp_nav_menu', function($output, $args) {
    // ... caching code here ...
}, 10, 2);

add_filter('wp_nav_menu', function($nav_menu, $args) {
    // ... caching code here ...
}, 10, 2);
*/

/**
 * Очистка кэша при обновлении меню
 */
add_action('wp_update_nav_menu', function() {
    wp_cache_flush_group('atk_ved_menus');
});

/**
 * Минификация HTML (опционально, может конфликтовать с некоторыми плагинами)
 */
if (!is_admin() && !WP_DEBUG) {
    add_action('template_redirect', function() {
        ob_start(function($html) {
            // Удаляем комментарии
            $html = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $html);
            
            // Удаляем лишние пробелы
            $html = preg_replace('/\s+/', ' ', $html);
            $html = preg_replace('/>\s+</', '><', $html);
            
            return $html;
        });
    }, 0);
}

/**
 * Heartbeat API оптимизация
 */
add_filter('heartbeat_settings', function($settings) {
    $settings['interval'] = 60; // 60 секунд вместо 15
    return $settings;
});

/**
 * Отключаем Heartbeat на фронтенде
 */
add_action('init', function() {
    if (!is_admin()) {
        wp_deregister_script('heartbeat');
    }
}, 1);
