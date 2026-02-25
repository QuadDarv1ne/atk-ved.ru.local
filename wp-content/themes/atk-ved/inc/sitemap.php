<?php
/**
 * XML Sitemap для поисковых систем
 * 
 * @package ATK_VED
 * @since 1.9.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Генерация XML Sitemap
 */
function atk_ved_generate_sitemap_xml(): void {
    // Проверяем запрос sitemap
    if (!isset($_GET['sitemap']) || $_GET['sitemap'] !== 'xml') {
        return;
    }
    
    // Устанавливаем заголовки
    header('Content-Type: application/xml; charset=utf-8');
    
    // Кэширование на 1 час
    header('Cache-Control: public, max-age=3600');
    
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<?xml-stylesheet type="text/xsl" href="' . esc_url(get_template_directory_uri()) . '/sitemap.xsl"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">' . "\n";
    
    // Главная страница
    echo_sitemap_url(home_url('/'), '1.0', 'daily', 'now');
    
    // Страницы
    $pages = get_pages([
        'post_type' => 'page',
        'post_status' => 'publish',
        'exclude' => get_option('page_on_front') // Исключаем главную
    ]);
    
    foreach ($pages as $page) {
        echo_sitemap_url(get_permalink($page->ID), '0.8', 'weekly', $page->post_modified);
    }
    
    // Записи блога
    $posts = get_posts([
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => 100,
        'orderby' => 'modified',
        'order' => 'DESC'
    ]);
    
    foreach ($posts as $post) {
        echo_sitemap_url(get_permalink($post->ID), '0.6', 'monthly', $post->post_modified);
    }
    
    // Категории
    $categories = get_categories([
        'hide_empty' => true
    ]);
    
    foreach ($categories as $category) {
        echo_sitemap_url(get_category_link($category->term_id), '0.5', 'weekly');
    }
    
    // Метки
    $tags = get_tags([
        'hide_empty' => true
    ]);
    
    foreach ($tags as $tag) {
        echo_sitemap_url(get_tag_link($tag->term_id), '0.4', 'monthly');
    }
    
    // Товары/услуги (если есть custom post types)
    $custom_post_types = get_post_types(['public' => true, '_builtin' => false]);
    
    foreach ($custom_post_types as $cpt) {
        $cpt_posts = get_posts([
            'post_type' => $cpt,
            'post_status' => 'publish',
            'posts_per_page' => 100
        ]);
        
        foreach ($cpt_posts as $cpt_post) {
            echo_sitemap_url(get_permalink($cpt_post->ID), '0.7', 'weekly', $cpt_post->post_modified);
        }
    }
    
    // Изображения из медиа
    if (get_theme_mod('atk_ved_sitemap_include_images', true)) {
        $images = get_posts([
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'post_status' => 'inherit',
            'posts_per_page' => 50
        ]);
        
        foreach ($images as $image) {
            $image_url = wp_get_attachment_url($image->ID);
            $parent_url = get_permalink($image->post_parent);
            
            if ($parent_url && $image_url) {
                echo_sitemap_url_with_image($parent_url, '0.6', 'weekly', $image->post_modified, $image_url);
            }
        }
    }
    
    echo '</urlset>' . "\n";
    exit;
}
add_action('init', 'atk_ved_generate_sitemap_xml', 1);

/**
 * Вывод URL элемента sitemap
 */
function echo_sitemap_url(string $loc, string $priority, string $changefreq, string $lastmod = 'now'): void {
    echo '<url>' . "\n";
    echo '  <loc>' . esc_url($loc) . '</loc>' . "\n";
    echo '  <lastmod>' . date('Y-m-d\TH:i:s+00:00', strtotime($lastmod)) . '</lastmod>' . "\n";
    echo '  <changefreq>' . esc_html($changefreq) . '</changefreq>' . "\n";
    echo '  <priority>' . esc_html($priority) . '</priority>' . "\n";
    echo '</url>' . "\n";
}

/**
 * Вывод URL с изображением
 */
function echo_sitemap_url_with_image(string $loc, string $priority, string $changefreq, string $lastmod, string $image_url): void {
    echo '<url>' . "\n";
    echo '  <loc>' . esc_url($loc) . '</loc>' . "\n";
    echo '  <lastmod>' . date('Y-m-d\TH:i:s+00:00', strtotime($lastmod)) . '</lastmod>' . "\n";
    echo '  <changefreq>' . esc_html($changefreq) . '</changefreq>' . "\n";
    echo '  <priority>' . esc_html($priority) . '</priority>' . "\n";
    echo '  <image:image>' . "\n";
    echo '    <image:loc>' . esc_url($image_url) . '</image:loc>' . "\n";
    echo '  </image:image>' . "\n";
    echo '</url>' . "\n";
}

/**
 * Добавление rewrite правила для sitemap.xml
 */
function atk_ved_sitemap_rewrite_rules(): void {
    add_rewrite_rule('^sitemap\.xml$', 'index.php?sitemap=xml', 'top');
}
add_action('init', 'atk_ved_sitemap_rewrite_rules');

/**
 * Добавление query var для sitemap
 */
function atk_ved_sitemap_query_vars(array $vars): array {
    $vars[] = 'sitemap';
    return $vars;
}
add_filter('query_vars', 'atk_ved_sitemap_query_vars');

/**
 * Добавление ссылки на sitemap в robots.txt
 */
function atk_ved_sitemap_robots_txt(string $output): string {
    $output .= "\nSitemap: " . home_url('/sitemap.xml') . "\n";
    return $output;
}
add_filter('robots_txt', 'atk_ved_sitemap_robots_txt');

/**
 * Ping поисковых систем при обновлении контента
 */
function atk_ved_ping_search_engines(int $post_id): void {
    // Не пингуем если это автосохранение или ревизия
    if (defined('DOING_AUTOSAVE') || wp_is_post_revision($post_id)) {
        return;
    }
    
    $sitemap_url = urlencode(home_url('/sitemap.xml'));
    
    // Google
    wp_remote_get("http://www.google.com/ping?sitemap={$sitemap_url}");
    
    // Bing
    wp_remote_get("http://www.bing.com/ping?sitemap={$sitemap_url}");
    
    // Яндекс (через вебмастер)
    // wp_remote_get("https://webmaster.yandex.ru/ping?sitemap={$sitemap_url}");
}
add_action('publish_post', 'atk_ved_ping_search_engines');
add_action('publish_page', 'atk_ved_ping_search_engines');

/**
 * Настройки sitemap в Customizer
 */
function atk_ved_sitemap_customizer($wp_customize): void {
    $wp_customize->add_section('atk_ved_sitemap', array(
        'title'    => __('XML Sitemap', 'atk-ved'),
        'priority' => 39,
    ));

    // Включение изображений
    $wp_customize->add_setting('atk_ved_sitemap_include_images', array(
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));

    $wp_customize->add_control('atk_ved_sitemap_include_images', array(
        'label'       => __('Включить изображения в sitemap', 'atk-ved'),
        'section'     => 'atk_ved_sitemap',
        'type'        => 'checkbox',
        'description' => __('Добавлять изображения из медиа в sitemap', 'atk-ved')
    ));

    // Информация
    $wp_customize->add_setting('atk_ved_sitemap_info', array(
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('atk_ved_sitemap_info', array(
        'section'     => 'atk_ved_sitemap',
        'type'        => 'info',
        'description' => __('Sitemap доступен по адресу: /sitemap.xml', 'atk-ved') . "\n" . 
                        __('Отправьте sitemap в Google Search Console и Яндекс.Вебмастер', 'atk-ved')
    ));
}
add_action('customize_register', 'atk_ved_sitemap_customizer');
