<?php
/**
 * Дополнительная оптимизация темы
 * 
 * @package ATK_VED
 * @since 1.0.0
 * @version 1.8.0
 */

declare(strict_types=1);

// Отключение ненужных функций WordPress
add_action('init', 'atk_ved_disable_unused_features');
function atk_ved_disable_unused_features(): void {
    // Отключаем emoji
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

    // Отключаем embeds
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');

    // Отключаем REST API для неавторизованных
    if (!is_user_logged_in()) {
        remove_action('wp_head', 'rest_output_link_wp_head');
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('template_redirect', 'rest_output_link_header', 11);
    }
}

// Отключение XML-RPC
add_filter('xmlrpc_enabled', '__return_false');

// Удаление версии WordPress из head
remove_action('wp_head', 'wp_generator');

// Отключение RSS feeds (если не используются)
// add_action('do_feed', 'atk_ved_disable_feed', 1);
// add_action('do_feed_rdf', 'atk_ved_disable_feed', 1);
// add_action('do_feed_rss', 'atk_ved_disable_feed', 1);
// add_action('do_feed_rss2', 'atk_ved_disable_feed', 1);
// add_action('do_feed_atom', 'atk_ved_disable_feed', 1);
// function atk_ved_disable_feed() {
//     wp_die('RSS feeds отключены');
// }

// Оптимизация загрузки скриптов
add_action('wp_enqueue_scripts', 'atk_ved_optimize_scripts', 100);
function atk_ved_optimize_scripts() {
    // Удаляем jQuery Migrate
    if (!is_admin()) {
        wp_deregister_script('jquery');
        wp_register_script('jquery', includes_url('/js/jquery/jquery.min.js'), false, null, true);
        wp_enqueue_script('jquery');
    }
    
    // Перемещаем скрипты в footer
    if (!is_admin()) {
        wp_scripts()->add_data('jquery', 'group', 1);
        wp_scripts()->add_data('jquery-core', 'group', 1);
    }
}

// Отложенная загрузка изображений
add_filter('the_content', 'atk_ved_add_lazy_loading');
add_filter('post_thumbnail_html', 'atk_ved_add_lazy_loading');
function atk_ved_add_lazy_loading($content) {
    if (is_admin() || is_feed()) {
        return $content;
    }
    
    // Добавляем loading="lazy" к изображениям
    $content = preg_replace('/<img(.*?)src=/i', '<img$1loading="lazy" src=', $content);
    
    return $content;
}

// Предзагрузка критических ресурсов
add_action('wp_head', 'atk_ved_preload_resources', 1);
function atk_ved_preload_resources() {
    // Предзагрузка шрифтов (если используются)
    // echo '<link rel="preload" href="' . get_template_directory_uri() . '/fonts/font.woff2" as="font" type="font/woff2" crossorigin>';
    
    // Предзагрузка критических CSS
    echo '<link rel="preload" href="' . get_stylesheet_uri() . '" as="style">';
    
    // DNS prefetch для внешних ресурсов
    echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">';
    echo '<link rel="dns-prefetch" href="//ajax.googleapis.com">';
}

// Минификация HTML
add_action('template_redirect', 'atk_ved_minify_html_output');
function atk_ved_minify_html_output() {
    if (!is_admin()) {
        ob_start('atk_ved_minify_html');
    }
}

function atk_ved_minify_html($html) {
    // Удаляем комментарии HTML
    $html = preg_replace('/<!--(?!<!)[^\[>].*?-->/s', '', $html);
    
    // Удаляем пробелы между тегами
    $html = preg_replace('/>\s+</s', '><', $html);
    
    // Удаляем лишние пробелы
    $html = preg_replace('/\s+/', ' ', $html);
    
    return $html;
}

// Оптимизация базы данных
add_action('wp_scheduled_delete', 'atk_ved_cleanup_database');
function atk_ved_cleanup_database(): void {
    global $wpdb;

    // Удаляем старые ревизии (старше 30 дней)
    $wpdb->query("DELETE FROM $wpdb->posts WHERE post_type = 'revision' AND post_modified < DATE_SUB(NOW(), INTERVAL 30 DAY)");

    // Удаляем автосохранения
    $wpdb->query("DELETE FROM $wpdb->posts WHERE post_status = 'auto-draft'");

    // Удаляем мусор из корзины (старше 7 дней)
    $wpdb->query("DELETE FROM $wpdb->posts WHERE post_status = 'trash' AND post_modified < DATE_SUB(NOW(), INTERVAL 7 DAY)");

    // Оптимизируем таблицы
    $wpdb->query("OPTIMIZE TABLE $wpdb->posts");
    $wpdb->query("OPTIMIZE TABLE $wpdb->postmeta");
    $wpdb->query("OPTIMIZE TABLE $wpdb->comments");
    $wpdb->query("OPTIMIZE TABLE $wpdb->commentmeta");
}

/**
 * WebP поддержка изображений
 * Добавляет picture элемент с WebP источником
 */
function atk_ved_add_webp_support(string $html, int $attachment_id, array $attachment_meta): string {
    // Не обрабатываем если это не изображение
    if (!wp_attachment_is_image($attachment_id)) {
        return $html;
    }

    // Получаем URL оригинала и его размеры
    $original_url = wp_get_attachment_url($attachment_id);
    $original_meta = wp_get_attachment_metadata($attachment_id);
    
    if (!$original_url || !$original_meta) {
        return $html;
    }

    // Генерируем URL для WebP
    $upload_dir = wp_upload_dir();
    $relative_path = str_replace($upload_dir['baseurl'], '', $original_url);
    $webp_url = $upload_dir['baseurl'] . '/webp' . dirname($relative_path) . '/' . wp_basename($original_url, '.' . pathinfo($original_url, PATHINFO_EXTENSION)) . '.webp';

    // Проверяем существует ли WebP версия
    $webp_path = $upload_dir['basedir'] . '/webp' . dirname($relative_path) . '/' . wp_basename($original_url, '.' . pathinfo($original_url, PATHINFO_EXTENSION)) . '.webp';
    
    if (!file_exists($webp_path)) {
        return $html;
    }

    // Извлекаем alt, width, height из оригинального HTML
    preg_match('/alt="([^"]*)"/', $html, $alt_matches);
    $alt = $alt_matches[1] ?? '';

    preg_match('/width="(\d+)"/', $html, $width_matches);
    $width = $width_matches[1] ?? $original_meta['width'] ?? 'auto';

    preg_match('/height="(\d+)"/', $html, $height_matches);
    $height = $height_matches[1] ?? $original_meta['height'] ?? 'auto';

    // Извлекаем классы
    preg_match('/class="([^"]*)"/', $html, $class_matches);
    $class = $class_matches[1] ?? '';

    // Формируем picture элемент
    $picture_html = '<picture>';
    $picture_html .= '<source srcset="' . esc_url($webp_url) . '" type="image/webp">';
    $picture_html .= '<img src="' . esc_url($original_url) . '" width="' . esc_attr($width) . '" height="' . esc_attr($height) . '" alt="' . esc_attr($alt) . '" class="' . esc_attr($class) . '" loading="lazy">';
    $picture_html .= '</picture>';

    return $picture_html;
}
add_filter('wp_get_attachment_image', 'atk_ved_add_webp_support', 10, 3);

/**
 * Автоматическая конвертация загруженных изображений в WebP
 */
function atk_ved_convert_to_webp(array $file_data): array {
    $upload_dir = wp_upload_dir();
    $relative_path = str_replace($upload_dir['basedir'], '', dirname($file_data['file']));
    
    // Создаём директорию для WebP если не существует
    $webp_dir = $upload_dir['basedir'] . '/webp' . $relative_path;
    if (!file_exists($webp_dir)) {
        wp_mkdir_p($webp_dir);
    }

    // Генерируем имя файла WebP
    $filename = wp_basename($file_data['file']);
    $webp_filename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
    $webp_path = $webp_dir . '/' . $webp_filename;

    // Конвертируем изображение в WebP
    $image = wp_get_image_editor($file_data['file']);
    
    if (!is_wp_error($image)) {
        $image->set_quality(82);
        $image->save($webp_path, 'image/webp');
    }

    return $file_data;
}
add_filter('wp_handle_upload', 'atk_ved_convert_to_webp');

// Ограничение ревизий
if (!defined('WP_POST_REVISIONS')) {
    define('WP_POST_REVISIONS', 3);
}

// Увеличение интервала автосохранения
if (!defined('AUTOSAVE_INTERVAL')) {
    define('AUTOSAVE_INTERVAL', 300); // 5 минут
}

// Отключение Heartbeat API на фронтенде
add_action('init', 'atk_ved_disable_heartbeat', 1);
function atk_ved_disable_heartbeat() {
    if (!is_admin()) {
        wp_deregister_script('heartbeat');
    }
}

// Оптимизация запросов к базе данных
add_action('pre_get_posts', 'atk_ved_optimize_queries');
function atk_ved_optimize_queries($query) {
    if (!is_admin() && $query->is_main_query()) {
        // Отключаем подсчет найденных записей если не нужна пагинация
        if (!$query->is_paged()) {
            $query->set('no_found_rows', true);
        }
        
        // Отключаем обновление мета-данных постов
        $query->set('update_post_meta_cache', false);
        $query->set('update_post_term_cache', false);
    }
}

// Добавление expires headers для статических файлов
add_action('send_headers', 'atk_ved_add_expires_headers');
function atk_ved_add_expires_headers() {
    if (!is_admin()) {
        header('Cache-Control: public, max-age=31536000');
    }
}

// Отключение Google Fonts (если не используются)
// add_action('wp_enqueue_scripts', 'atk_ved_remove_google_fonts', 100);
// function atk_ved_remove_google_fonts() {
//     wp_dequeue_style('wp-block-library');
// }

// Defer/Async для скриптов
add_filter('script_loader_tag', 'atk_ved_defer_scripts', 10, 2);
function atk_ved_defer_scripts($tag, $handle) {
    // Список скриптов для defer
    $defer_scripts = array(
        'atk-ved-enhancements',
        'atk-ved-statistics',
        'atk-ved-tracking',
        'atk-ved-gallery'
    );
    
    // Список скриптов для async
    $async_scripts = array(
        'atk-ved-modal',
        'atk-ved-calculator'
    );
    
    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }
    
    if (in_array($handle, $async_scripts)) {
        return str_replace(' src', ' async src', $tag);
    }
    
    return $tag;
}

// Отключение dashicons на фронтенде для неавторизованных
add_action('wp_enqueue_scripts', 'atk_ved_dequeue_dashicons');
function atk_ved_dequeue_dashicons() {
    if (!is_user_logged_in()) {
        wp_dequeue_style('dashicons');
        wp_deregister_style('dashicons');
    }
}

// Сжатие вывода
if (!function_exists('atk_ved_enable_gzip')) {
    function atk_ved_enable_gzip() {
        if (!ob_start('ob_gzhandler')) {
            ob_start();
        }
    }
    add_action('template_redirect', 'atk_ved_enable_gzip');
}
