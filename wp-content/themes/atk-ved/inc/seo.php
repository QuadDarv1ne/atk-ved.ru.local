<?php
/**
 * SEO Functions
 *
 * @package ATK_VED
 */

defined('ABSPATH') || exit;

/**
 * Генерация SEO meta description
 */
function atk_ved_get_meta_description(): string {
    $description = '';

    // Главная страница
    if (is_front_page()) {
        $description = get_theme_mod('atk_ved_seo_description', get_bloginfo('description'));
        if (empty($description)) {
            $description = 'АТК ВЭД — доставка грузов из Китая для маркетплейсов. Полный цикл: от поиска поставщика до доставки на склад. Авиа, Ж/Д, море, авто. Рассчитайте стоимость онлайн!';
        }
    }
    // Страницы
    elseif (is_singular()) {
        global $post;
        
        // Приоритет: SEO поле → краткое описание → первые 160 символов контента
        $description = get_post_meta($post->ID, '_atk_ved_meta_description', true);
        
        if (empty($description)) {
            $description = get_the_excerpt();
        }
        
        if (empty($description) && !empty($post->post_content)) {
            $description = wp_trim_words(strip_tags($post->post_content), 30);
        }
    }
    // Записи
    elseif (is_single()) {
        $description = get_the_excerpt();
    }
    // Архивы
    elseif (is_archive()) {
        $description = get_the_archive_description();
        if (empty($description)) {
            $description = get_the_archive_title();
        }
    }
    // Поиск
    elseif (is_search()) {
        $description = 'Результаты поиска по запросу: ' . get_search_query();
    }
    // 404
    elseif (is_404()) {
        $description = 'Страница не найдена. Воспользуйтесь поиском или навигацией.';
    }

    // Очистка и нормализация
    $description = strip_tags($description);
    $description = strip_shortcodes($description);
    $description = trim(preg_replace('/\s+/', ' ', $description));
    
    // Ограничение длины (150-160 символов для сниппета)
    if (mb_strlen($description) > 160) {
        $description = mb_substr($description, 0, 157) . '...';
    }

    return esc_html($description);
}

/**
 * Добавляем meta description
 */
function atk_ved_add_meta_description() {
    $description = atk_ved_get_meta_description();
    
    if (!empty($description)) {
        echo '<meta name="description" content="' . $description . '">' . "\n";
    }
}
add_action('wp_head', 'atk_ved_add_meta_description', 1);

/**
 * Добавляем Open Graph и Twitter Card мета-теги
 */
function atk_ved_add_meta_tags() {
    if (is_singular()) {
        global $post;

        $title = get_the_title();
        $description = atk_ved_get_meta_description();
        $url = get_permalink();
        $image = get_the_post_thumbnail_url($post->ID, 'large') ?: get_template_directory_uri() . '/images/og-default.jpg';
        $site_name = get_bloginfo('name');

    } else {
        $title = get_bloginfo('name');
        $description = atk_ved_get_meta_description();
        $url = home_url('/');
        $image = get_template_directory_uri() . '/images/og-default.jpg';
        $site_name = get_bloginfo('name');
    }
    
    // Тип объекта
    $og_type = 'website';
    if (is_singular('post')) {
        $og_type = 'article';
    } elseif (is_singular('page')) {
        $og_type = 'website';
    }
    ?>

    <!-- Open Graph -->
    <meta property="og:type" content="<?php echo esc_attr($og_type); ?>">
    <meta property="og:title" content="<?php echo esc_attr($title); ?>">
    <meta property="og:description" content="<?php echo esc_attr($description); ?>">
    <meta property="og:url" content="<?php echo esc_url($url); ?>">
    <meta property="og:image" content="<?php echo esc_url($image); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="<?php echo esc_attr($site_name); ?>">
    <meta property="og:locale" content="ru_RU">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc_attr($title); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr($description); ?>">
    <meta name="twitter:image" content="<?php echo esc_url($image); ?>">
    <?php
    $twitter_handle = get_theme_mod('atk_ved_twitter_handle', '');
    if (!empty($twitter_handle)): ?>
    <meta name="twitter:site" content="@<?php echo esc_attr($twitter_handle); ?>">
    <meta name="twitter:creator" content="@<?php echo esc_attr($twitter_handle); ?>">
    <?php endif; ?>

    <?php
}
add_action('wp_head', 'atk_ved_add_meta_tags', 5);

/**
 * Добавляем hreflang теги для мультиязычности
 */
function atk_ved_add_hreflang_tags(): void {
    // Получаем текущий URL
    $current_url = home_url(add_query_arg(null, null));
    
    // Определяем доступные языки
    $languages = [
        'ru' => home_url('/'),
        'en' => home_url('/en/'),
        'kk' => home_url('/kk/'),
        'zh' => home_url('/zh/'),
    ];
    
    // Определяем текущий язык
    $current_lang = 'ru'; // По умолчанию русский
    
    if (strpos($current_url, '/en/') !== false) {
        $current_lang = 'en';
    } elseif (strpos($current_url, '/kk/') !== false) {
        $current_lang = 'kk';
    } elseif (strpos($current_url, '/zh/') !== false) {
        $current_lang = 'zh';
    }
    
    // Получаем относительный путь
    $relative_path = str_replace(home_url('/'), '', $current_url);
    $relative_path = preg_replace('#^(en|kk|zh)/#', '', $relative_path);
    
    // Выводим hreflang теги
    foreach ($languages as $lang => $base_url) {
        $lang_url = $lang === 'ru' ? home_url('/' . $relative_path) : $base_url . $relative_path;
        echo '<link rel="alternate" hreflang="' . esc_attr($lang) . '" href="' . esc_url($lang_url) . '">' . "\n";
    }
    
    // x-default для основного языка
    echo '<link rel="alternate" hreflang="x-default" href="' . esc_url(home_url('/' . $relative_path)) . '">' . "\n";
}
add_action('wp_head', 'atk_ved_add_hreflang_tags', 6);

/**
 * Добавляем canonical URL
 */
function atk_ved_add_canonical_url(): void {
    if (is_singular()) {
        $canonical = get_permalink();
    } elseif (is_front_page()) {
        $canonical = home_url('/');
    } elseif (is_archive()) {
        $canonical = get_pagenum_link();
    } else {
        $canonical = home_url(add_query_arg(null, null));
    }
    
    // Удаляем параметры запроса для canonical
    $canonical = strtok($canonical, '?');
    
    echo '<link rel="canonical" href="' . esc_url($canonical) . '">' . "\n";
}
add_action('wp_head', 'atk_ved_add_canonical_url', 4);

/**
 * Добавляем поле SEO description в редактор
 */
function atk_ved_add_seo_meta_box() {
    add_meta_box(
        'atk_ved_seo_meta_box',
        __('SEO описание', 'atk-ved'),
        'atk_ved_render_seo_meta_box',
        ['post', 'page'],
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'atk_ved_add_seo_meta_box');

/**
 * Рендер поля SEO description
 */
function atk_ved_render_seo_meta_box($post) {
    wp_nonce_field('atk_ved_seo_meta_box', 'atk_ved_seo_meta_box_nonce');
    
    $value = get_post_meta($post->ID, '_atk_ved_meta_description', true);
    $current_length = mb_strlen($value);
    ?>
    <p>
        <label for="atk_ved_meta_description" style="font-weight: 600;">
            <?php _e('Meta Description:', 'atk-ved'); ?>
        </label>
        <textarea 
            id="atk_ved_meta_description" 
            name="atk_ved_meta_description" 
            rows="4" 
            class="large-text"
            maxlength="300"
            placeholder="<?php esc_attr_e('Введите описание страницы (150-160 символов)', 'atk-ved'); ?>"
        ><?php echo esc_textarea($value); ?></textarea>
    </p>
    <p class="description">
        <span id="atk_ved_seo_length"><?php echo $current_length; ?></span> <?php _e('символов', 'atk-ved'); ?>
        <br>
        <small>
            <?php _e('Рекомендуемая длина: 150-160 символов', 'atk-ved'); ?>
        </small>
    </p>
    <script>
    (function() {
        const textarea = document.getElementById('atk_ved_meta_description');
        const lengthSpan = document.getElementById('atk_ved_seo_length');
        
        if (textarea && lengthSpan) {
            textarea.addEventListener('input', function() {
                const length = this.value.length;
                lengthSpan.textContent = length;
                
                if (length > 160) {
                    lengthSpan.style.color = '#dc3232';
                } else if (length < 120) {
                    lengthSpan.style.color = '#ffb900';
                } else {
                    lengthSpan.style.color = '#4caf50';
                }
            });
        }
    })();
    </script>
    <?php
}

/**
 * Сохранение SEO description
 */
function atk_ved_save_seo_meta_box($post_id) {
    if (!isset($_POST['atk_ved_seo_meta_box_nonce'])) {
        return;
    }
    
    if (!wp_verify_nonce($_POST['atk_ved_seo_meta_box_nonce'], 'atk_ved_seo_meta_box')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_POST['atk_ved_meta_description'])) {
        $description = sanitize_textarea_field($_POST['atk_ved_meta_description']);
        update_post_meta($post_id, '_atk_ved_meta_description', $description);
    }
}
add_action('save_post', 'atk_ved_save_seo_meta_box');

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
