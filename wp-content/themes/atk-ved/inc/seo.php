<?php
/**
 * SEO оптимизация
 * 
 * @package ATK_VED
 * @since 1.0.0
 */

declare(strict_types=1);

// Добавление Open Graph мета-тегов
function atk_ved_add_og_tags(): void {
    if (is_singular()) {
        global $post;

        $title = get_the_title();
        $description = get_the_excerpt() ?: wp_trim_words(strip_tags($post->post_content), 30);
        $url = get_permalink();
        $image = get_the_post_thumbnail_url($post->ID, 'large') ?: get_template_directory_uri() . '/images/hero/hero-boxes.jpg';
        $author = get_the_author();

        echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
        echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";
        echo '<meta property="og:image:width" content="1200">' . "\n";
        echo '<meta property="og:image:height" content="630">' . "\n";
        echo '<meta property="og:type" content="article">' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
        echo '<meta property="og:locale" content="ru_RU">' . "\n";
        echo '<meta property="article:author" content="' . esc_attr($author) . '">' . "\n";
        echo '<meta property="article:published_time" content="' . get_the_date('c') . '">' . "\n";
        echo '<meta property="article:modified_time" content="' . get_the_modified_date('c') . '">' . "\n";
    } else {
        echo '<meta property="og:title" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr(get_bloginfo('description')) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url(home_url('/')) . '">' . "\n";
        echo '<meta property="og:type" content="website">' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
        echo '<meta property="og:locale" content="ru_RU">' . "\n";
    }
}
add_action('wp_head', 'atk_ved_add_og_tags');

// Добавление Twitter Card мета-тегов
function atk_ved_add_twitter_cards(): void {
    if (is_singular()) {
        global $post;

        $title = get_the_title();
        $description = get_the_excerpt() ?: wp_trim_words(strip_tags($post->post_content), 30);
        $image = get_the_post_thumbnail_url($post->ID, 'large') ?: get_template_directory_uri() . '/images/hero/hero-boxes.jpg';
        $site_username = get_theme_mod('atk_ved_twitter_username', '@atkved');

        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
        echo '<meta name="twitter:site" content="' . esc_attr($site_username) . '">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";
        echo '<meta name="twitter:image" content="' . esc_url($image) . '">' . "\n";
        echo '<meta name="twitter:image:alt" content="' . esc_attr($title) . '">' . "\n";
    } else {
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
        echo '<meta name="twitter:site" content="' . esc_attr(get_theme_mod('atk_ved_twitter_username', '@atkved')) . '">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr(get_bloginfo('description')) . '">' . "\n";
    }
}
add_action('wp_head', 'atk_ved_add_twitter_cards');

// Добавление расширенной Schema.org разметки
function atk_ved_add_schema_markup(): void {
    $schema = array();
    
    // Organization schema для главной страницы
    if (is_front_page()) {
        $schema['organization'] = array(
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => get_bloginfo('name'),
            'url' => home_url('/'),
            'logo' => get_template_directory_uri() . '/images/logo/logo.png',
            'description' => get_bloginfo('description'),
            'address' => array(
                '@type' => 'PostalAddress',
                'addressCountry' => 'RU',
                'addressLocality' => get_theme_mod('atk_ved_address', 'Москва'),
                'streetAddress' => get_theme_mod('atk_ved_street_address', '')
            ),
            'contactPoint' => array(
                '@type' => 'ContactPoint',
                'telephone' => get_theme_mod('atk_ved_phone', '+7 (XXX) XXX-XX-XX'),
                'contactType' => 'customer service',
                'areaServed' => 'RU',
                'availableLanguage' => 'ru'
            ),
            'sameAs' => array_filter(array(
                get_theme_mod('atk_ved_vk'),
                get_theme_mod('atk_ved_telegram'),
                get_theme_mod('atk_ved_whatsapp')
            ))
        );
    }
    
    // Article schema для записей
    if (is_single()) {
        global $post;
        $schema['article'] = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'mainEntityOfPage' => array(
                '@type' => 'WebPage',
                '@id' => get_permalink()
            ),
            'headline' => get_the_title(),
            'description' => get_the_excerpt() ?: wp_trim_words(strip_tags($post->post_content), 20),
            'image' => get_the_post_thumbnail_url($post->ID, 'large') ?: get_template_directory_uri() . '/images/hero/hero-boxes.jpg',
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author()
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => get_template_directory_uri() . '/images/logo/logo.png'
                )
            ),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c')
        );
    }
    
    // BreadcrumbList schema для навигации
    if (is_single() || is_page() || is_category() || is_tag()) {
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Главная',
            'item' => home_url('/')
        );
        
        if (is_category()) {
            $category = get_queried_object();
            $breadcrumbs[] = array(
                '@type' => 'ListItem',
                'position' => 2,
                'name' => $category->name,
                'item' => get_category_link($category->term_id)
            );
        } elseif (is_tag()) {
            $tag = get_queried_object();
            $breadcrumbs[] = array(
                '@type' => 'ListItem',
                'position' => 2,
                'name' => $tag->name,
                'item' => get_tag_link($tag->term_id)
            );
        } elseif (is_single() || is_page()) {
            $post_type = get_post_type();
            if ($post_type !== 'post' && $post_type !== 'page') {
                $post_type_obj = get_post_type_object($post_type);
                if ($post_type_obj) {
                    $breadcrumbs[] = array(
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => $post_type_obj->labels->singular_name,
                        'item' => get_post_type_archive_link($post_type)
                    );
                }
            }
            
            $breadcrumbs[] = array(
                '@type' => 'ListItem',
                'position' => count($breadcrumbs) + 1,
                'name' => get_the_title(),
                'item' => get_permalink()
            );
        }
        
        $schema['breadcrumb'] = array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbs
        );
    }
    
    // FAQ Schema для главной страницы
    if (is_front_page()) {
        $schema['faq'] = array(
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array(
                array(
                    '@type' => 'Question',
                    'name' => 'Какой минимальный объем заказа?',
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text' => 'Мы работаем с заказами от 100 кг. Однако, для некоторых категорий товаров возможны исключения. Уточняйте у наших менеджеров.'
                    )
                ),
                array(
                    '@type' => 'Question',
                    'name' => 'Сколько времени занимает доставка?',
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text' => 'Сроки доставки зависят от выбранного способа: авиа — 5-10 дней, ж/д — 20-30 дней, море — 35-45 дней. Также учитывайте время на выкуп и таможенное оформление.'
                    )
                ),
                array(
                    '@type' => 'Question',
                    'name' => 'Как происходит оплата?',
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text' => 'Оплата производится в два этапа: 70% предоплата за товар и услуги, 30% после получения и проверки товара перед окончательной отгрузкой.'
                    )
                ),
                array(
                    '@type' => 'Question',
                    'name' => 'Работаете ли вы с юридическими лицами?',
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text' => 'Да, мы работаем как с юридическими, так и с физическими лицами. Предоставляем полный пакет документов для бухгалтерии.'
                    )
                )
            )
        );
    }
    
    // LocalBusiness schema
    if (is_front_page()) {
        $schema['localbusiness'] = array(
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => get_bloginfo('name'),
            'image' => get_template_directory_uri() . '/images/hero/hero-boxes.jpg',
            'telephone' => get_theme_mod('atk_ved_phone', '+7 (XXX) XXX-XX-XX'),
            'email' => get_theme_mod('atk_ved_email', 'info@atk-ved.ru'),
            'address' => array(
                '@type' => 'PostalAddress',
                'addressCountry' => 'RU',
                'addressLocality' => get_theme_mod('atk_ved_address', 'Москва'),
                'streetAddress' => get_theme_mod('atk_ved_street_address', '')
            ),
            'openingHoursSpecification' => array(
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'),
                'opens' => '09:00',
                'closes' => '18:00'
            ),
            'priceRange' => '$$',
            'areaServed' => array(
                '@type' => 'AdministrativeArea',
                'name' => 'Россия'
            ),
            'availableLanguage' => 'ru'
        );
    }
    
    // Выводим все схемы
    foreach ($schema as $schema_item) {
        echo '<script type="application/ld+json">' . json_encode($schema_item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
}
add_action('wp_head', 'atk_ved_add_schema_markup');

// Улучшение мета-описания
function atk_ved_add_meta_description() {
    if (is_home() || is_front_page()) {
        $description = get_bloginfo('description');
    } elseif (is_singular()) {
        global $post;
        $description = get_the_excerpt() ?: wp_trim_words(strip_tags($post->post_content), 30);
    } elseif (is_category()) {
        $description = category_description();
    } elseif (is_tag()) {
        $description = tag_description();
    } elseif (is_author()) {
        $description = get_the_author_meta('description');
    } else {
        $description = get_bloginfo('description');
    }
    
    if ($description) {
        // Ограничиваем длину описания до 160 символов
        $description = wp_trim_words(strip_tags($description), 22, '...');
        echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    }
}
add_action('wp_head', 'atk_ved_add_meta_description');

// Добавление мета-тегов keywords (для совместимости)
function atk_ved_add_meta_keywords() {
    if (is_singular()) {
        global $post;
        $tags = get_the_tags($post->ID);
        if ($tags) {
            $keywords = array();
            foreach ($tags as $tag) {
                $keywords[] = $tag->name;
            }
            echo '<meta name="keywords" content="' . esc_attr(implode(', ', $keywords)) . '">' . "\n";
        }
    } elseif (is_category()) {
        $cat = get_category(get_query_var('cat'));
        echo '<meta name="keywords" content="' . esc_attr($cat->name) . '">' . "\n";
    } elseif (is_tag()) {
        $tag = get_term_by('slug', get_query_var('tag'), 'post_tag');
        echo '<meta name="keywords" content="' . esc_attr($tag->name) . '">' . "\n";
    }
}
add_action('wp_head', 'atk_ved_add_meta_keywords');

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
    } elseif (is_author()) {
        return 'Автор: ' . get_the_author() . ' - ' . get_bloginfo('name');
    } elseif (is_archive()) {
        return $title . ' - Архив - ' . get_bloginfo('name');
    }
    
    return $title;
}
add_filter('pre_get_document_title', 'atk_ved_custom_title');

// Добавление структурированной навигации
function atk_ved_add_structured_navigation() {
    if (is_front_page()) {
        echo '<script type="application/ld+json">';
        echo json_encode(array(
            '@context' => 'https://schema.org',
            '@type' => 'SiteNavigationElement',
            'name' => array(
                'Главная',
                'Услуги',
                'Доставка',
                'Калькулятор',
                'Контакты'
            ),
            'url' => array(
                home_url('/'),
                home_url('/#services'),
                home_url('/#delivery'),
                home_url('/#calculator'),
                home_url('/#contacts')
            )
        ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        echo '</script>';
    }
}
add_action('wp_head', 'atk_ved_add_structured_navigation');

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
