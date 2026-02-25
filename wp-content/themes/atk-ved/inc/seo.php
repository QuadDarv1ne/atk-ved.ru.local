<?php
/**
 * SEO оптимизация
 */

// Добавление Open Graph мета-тегов
function atk_ved_add_og_tags() {
    if (is_singular()) {
        global $post;
        
        $title = get_the_title();
        $description = get_the_excerpt() ?: wp_trim_words(strip_tags($post->post_content), 30);
        $url = get_permalink();
        $image = get_the_post_thumbnail_url($post->ID, 'large') ?: get_template_directory_uri() . '/images/hero-containers.jpg';
        
        echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
        echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";
        echo '<meta property="og:type" content="article">' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
    } else {
        echo '<meta property="og:title" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr(get_bloginfo('description')) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url(home_url('/')) . '">' . "\n";
        echo '<meta property="og:type" content="website">' . "\n";
    }
}
add_action('wp_head', 'atk_ved_add_og_tags');

// Добавление Twitter Card мета-тегов
function atk_ved_add_twitter_cards() {
    if (is_singular()) {
        global $post;
        
        $title = get_the_title();
        $description = get_the_excerpt() ?: wp_trim_words(strip_tags($post->post_content), 30);
        $image = get_the_post_thumbnail_url($post->ID, 'large') ?: get_template_directory_uri() . '/images/hero-containers.jpg';
        
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";
        echo '<meta name="twitter:image" content="' . esc_url($image) . '">' . "\n";
    }
}
add_action('wp_head', 'atk_ved_add_twitter_cards');

// Добавление Schema.org разметки
function atk_ved_add_schema_markup() {
    if (is_front_page()) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => get_bloginfo('name'),
            'url' => home_url('/'),
            'logo' => get_template_directory_uri() . '/images/logo.png',
            'description' => get_bloginfo('description'),
            'address' => array(
                '@type' => 'PostalAddress',
                'addressCountry' => 'RU',
                'addressLocality' => get_theme_mod('atk_ved_address', 'Москва')
            ),
            'contactPoint' => array(
                '@type' => 'ContactPoint',
                'telephone' => get_theme_mod('atk_ved_phone', '+7 (XXX) XXX-XX-XX'),
                'contactType' => 'customer service',
                'email' => get_theme_mod('atk_ved_email', 'info@atk-ved.ru')
            ),
            'sameAs' => array_filter(array(
                get_theme_mod('atk_ved_vk'),
                get_theme_mod('atk_ved_telegram'),
                get_theme_mod('atk_ved_whatsapp')
            ))
        );
        
        echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
}
add_action('wp_head', 'atk_ved_add_schema_markup');

// Улучшение мета-описания
function atk_ved_add_meta_description() {
    if (is_singular()) {
        global $post;
        $description = get_the_excerpt() ?: wp_trim_words(strip_tags($post->post_content), 30);
    } elseif (is_category()) {
        $description = category_description();
    } elseif (is_tag()) {
        $description = tag_description();
    } else {
        $description = get_bloginfo('description');
    }
    
    if ($description) {
        echo '<meta name="description" content="' . esc_attr(strip_tags($description)) . '">' . "\n";
    }
}
add_action('wp_head', 'atk_ved_add_meta_description');

// Canonical URL
function atk_ved_add_canonical() {
    if (is_singular()) {
        echo '<link rel="canonical" href="' . esc_url(get_permalink()) . '">' . "\n";
    } elseif (is_front_page()) {
        echo '<link rel="canonical" href="' . esc_url(home_url('/')) . '">' . "\n";
    }
}
add_action('wp_head', 'atk_ved_add_canonical');

// Robots meta tag
function atk_ved_add_robots_meta() {
    if (is_search() || is_404()) {
        echo '<meta name="robots" content="noindex, nofollow">' . "\n";
    } elseif (is_archive() && get_query_var('paged') > 1) {
        echo '<meta name="robots" content="noindex, follow">' . "\n";
    }
}
add_action('wp_head', 'atk_ved_add_robots_meta');

// Генерация sitemap.xml
function atk_ved_generate_sitemap() {
    if (isset($_GET['sitemap']) && $_GET['sitemap'] === 'xml') {
        header('Content-Type: application/xml; charset=utf-8');
        
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Главная страница
        echo '<url>' . "\n";
        echo '<loc>' . esc_url(home_url('/')) . '</loc>' . "\n";
        echo '<changefreq>daily</changefreq>' . "\n";
        echo '<priority>1.0</priority>' . "\n";
        echo '</url>' . "\n";
        
        // Страницы
        $pages = get_pages();
        foreach ($pages as $page) {
            echo '<url>' . "\n";
            echo '<loc>' . esc_url(get_permalink($page->ID)) . '</loc>' . "\n";
            echo '<lastmod>' . date('Y-m-d', strtotime($page->post_modified)) . '</lastmod>' . "\n";
            echo '<changefreq>weekly</changefreq>' . "\n";
            echo '<priority>0.8</priority>' . "\n";
            echo '</url>' . "\n";
        }
        
        // Записи
        $posts = get_posts(array('numberposts' => -1));
        foreach ($posts as $post) {
            echo '<url>' . "\n";
            echo '<loc>' . esc_url(get_permalink($post->ID)) . '</loc>' . "\n";
            echo '<lastmod>' . date('Y-m-d', strtotime($post->post_modified)) . '</lastmod>' . "\n";
            echo '<changefreq>monthly</changefreq>' . "\n";
            echo '<priority>0.6</priority>' . "\n";
            echo '</url>' . "\n";
        }
        
        echo '</urlset>';
        exit;
    }
}
add_action('init', 'atk_ved_generate_sitemap');

// Добавление alt к изображениям
function atk_ved_add_image_alt($content) {
    global $post;
    
    if (!$post) {
        return $content;
    }
    
    $pattern = '/<img(.*?)src=[\'"]([^\'"]+)[\'"]([^>]*)>/i';
    $replacement = '<img$1src="$2" alt="' . esc_attr(get_the_title()) . '"$3>';
    
    $content = preg_replace($pattern, $replacement, $content);
    
    return $content;
}
add_filter('the_content', 'atk_ved_add_image_alt');

// Улучшение заголовков страниц
function atk_ved_custom_title($title) {
    if (is_front_page()) {
        return get_bloginfo('name') . ' - ' . get_bloginfo('description');
    } elseif (is_singular()) {
        return $title . ' - ' . get_bloginfo('name');
    } elseif (is_category()) {
        return 'Категория: ' . $title . ' - ' . get_bloginfo('name');
    } elseif (is_tag()) {
        return 'Тег: ' . $title . ' - ' . get_bloginfo('name');
    } elseif (is_search()) {
        return 'Поиск: ' . get_search_query() . ' - ' . get_bloginfo('name');
    } elseif (is_404()) {
        return 'Страница не найдена - ' . get_bloginfo('name');
    }
    
    return $title;
}
add_filter('pre_get_document_title', 'atk_ved_custom_title');

// Добавление hreflang для мультиязычности (если нужно)
function atk_ved_add_hreflang() {
    if (is_singular() || is_front_page()) {
        $url = is_front_page() ? home_url('/') : get_permalink();
        echo '<link rel="alternate" hreflang="ru" href="' . esc_url($url) . '">' . "\n";
    }
}
add_action('wp_head', 'atk_ved_add_hreflang');

// Оптимизация заголовков для SEO
function atk_ved_optimize_headings($content) {
    // Замена h1 на h2 в контенте (h1 должен быть только один - заголовок страницы)
    $content = preg_replace('/<h1([^>]*)>/', '<h2$1>', $content);
    $content = preg_replace('/<\/h1>/', '</h2>', $content);
    
    return $content;
}
add_filter('the_content', 'atk_ved_optimize_headings');

// Добавление nofollow к внешним ссылкам
function atk_ved_add_nofollow_external($content) {
    $pattern = '/<a(.*?)href=[\'"]([^\'"]+)[\'"]([^>]*)>/i';
    
    $content = preg_replace_callback($pattern, function($matches) {
        $url = $matches[2];
        $home_url = home_url();
        
        // Если ссылка внешняя
        if (strpos($url, $home_url) === false && strpos($url, 'http') === 0) {
            // Добавляем rel="nofollow noopener"
            if (strpos($matches[0], 'rel=') === false) {
                return '<a' . $matches[1] . 'href="' . $url . '" rel="nofollow noopener"' . $matches[3] . '>';
            }
        }
        
        return $matches[0];
    }, $content);
    
    return $content;
}
add_filter('the_content', 'atk_ved_add_nofollow_external');
