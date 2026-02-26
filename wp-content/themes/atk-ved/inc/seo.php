<?php
/**
 * SEO Functions
 *
 * @package ATK_VED
 */

defined('ABSPATH') || exit;

/**
 * Добавляем Open Graph и Twitter Card мета-теги
 */
function atk_ved_add_meta_tags() {
    if (is_singular()) {
        global $post;
        
        $title = get_the_title();
        $description = get_the_excerpt() ?: wp_trim_words(strip_tags($post->post_content), 30);
        $url = get_permalink();
        $image = get_the_post_thumbnail_url($post->ID, 'large') ?: get_template_directory_uri() . '/images/og-default.jpg';
        $site_name = get_bloginfo('name');
        
    } else {
        $title = get_bloginfo('name');
        $description = get_bloginfo('description');
        $url = home_url('/');
        $image = get_template_directory_uri() . '/images/og-default.jpg';
        $site_name = get_bloginfo('name');
    }
    ?>
    
    <!-- Open Graph -->
    <meta property="og:type" content="<?php echo is_singular('post') ? 'article' : 'website'; ?>">
    <meta property="og:title" content="<?php echo esc_attr($title); ?>">
    <meta property="og:description" content="<?php echo esc_attr($description); ?>">
    <meta property="og:url" content="<?php echo esc_url($url); ?>">
    <meta property="og:image" content="<?php echo esc_url($image); ?>">
    <meta property="og:site_name" content="<?php echo esc_attr($site_name); ?>">
    <meta property="og:locale" content="ru_RU">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc_attr($title); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr($description); ?>">
    <meta name="twitter:image" content="<?php echo esc_url($image); ?>">
    
    <?php
}
add_action('wp_head', 'atk_ved_add_meta_tags', 5);

/**
 * Добавляем Schema.org разметку для организации
 */
function atk_ved_add_schema_org() {
    $company = atk_ved_get_company_info();
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $company['name'],
        'url' => home_url('/'),
        'logo' => get_template_directory_uri() . '/images/logo/logo.png',
        'description' => get_bloginfo('description'),
        'address' => [
            '@type' => 'PostalAddress',
            'addressCountry' => 'RU',
            'addressLocality' => $company['city'],
            'streetAddress' => $company['address'],
        ],
        'contactPoint' => [
            '@type' => 'ContactPoint',
            'telephone' => $company['phone'],
            'contactType' => 'customer service',
            'availableLanguage' => 'Russian',
        ],
        'sameAs' => array_filter([
            $company['vk'] ?? '',
            $company['telegram'] ?? '',
            $company['whatsapp'] ?? '',
        ]),
    ];
    
    if (!empty($company['email'])) {
        $schema['email'] = $company['email'];
    }
    
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}
add_action('wp_head', 'atk_ved_add_schema_org', 10);

/**
 * Добавляем Schema.org для хлебных крошек
 */
function atk_ved_breadcrumb_schema() {
    if (is_front_page()) {
        return;
    }
    
    $items = [];
    $position = 1;
    
    // Главная
    $items[] = [
        '@type' => 'ListItem',
        'position' => $position++,
        'name' => 'Главная',
        'item' => home_url('/'),
    ];
    
    // Текущая страница
    if (is_singular()) {
        $items[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => get_the_title(),
            'item' => get_permalink(),
        ];
    } elseif (is_archive()) {
        $items[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => get_the_archive_title(),
            'item' => get_permalink(),
        ];
    }
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $items,
    ];
    
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}
add_action('wp_head', 'atk_ved_breadcrumb_schema', 11);

/**
 * Генерация sitemap.xml
 */
function atk_ved_generate_sitemap() {
    if (!isset($_GET['atk_sitemap'])) {
        return;
    }
    
    header('Content-Type: application/xml; charset=utf-8');
    
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    // Главная
    echo '<url>' . "\n";
    echo '<loc>' . esc_url(home_url('/')) . '</loc>' . "\n";
    echo '<changefreq>daily</changefreq>' . "\n";
    echo '<priority>1.0</priority>' . "\n";
    echo '</url>' . "\n";
    
    // Страницы
    $pages = get_pages(['post_status' => 'publish']);
    foreach ($pages as $page) {
        echo '<url>' . "\n";
        echo '<loc>' . esc_url(get_permalink($page->ID)) . '</loc>' . "\n";
        echo '<lastmod>' . esc_html(get_the_modified_date('c', $page->ID)) . '</lastmod>' . "\n";
        echo '<changefreq>weekly</changefreq>' . "\n";
        echo '<priority>0.8</priority>' . "\n";
        echo '</url>' . "\n";
    }
    
    // Записи
    $posts = get_posts(['numberposts' => 100, 'post_status' => 'publish']);
    foreach ($posts as $post) {
        echo '<url>' . "\n";
        echo '<loc>' . esc_url(get_permalink($post->ID)) . '</loc>' . "\n";
        echo '<lastmod>' . esc_html(get_the_modified_date('c', $post->ID)) . '</lastmod>' . "\n";
        echo '<changefreq>monthly</changefreq>' . "\n";
        echo '<priority>0.6</priority>' . "\n";
        echo '</url>' . "\n";
    }
    
    echo '</urlset>';
    exit;
}
add_action('init', 'atk_ved_generate_sitemap');
