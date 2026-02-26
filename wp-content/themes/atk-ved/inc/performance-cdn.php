<?php
/**
 * Минификация и CDN
 *
 * @package ATK_VED
 * @since 3.5.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Минификация HTML
 */
function atk_ved_minify_html(string $buffer): string {
    // Не минифицируем для админов и в режиме отладки
    if (is_user_logged_in() || defined('WP_DEBUG') && WP_DEBUG) {
        return $buffer;
    }
    
    // Удаляем лишние пробелы и переносы строк
    $search = [
        '/\>[^\S ]+/s',     // Удаляем пробелы после закрывающих тегов
        '/[^\S ]+\</s',     // Удаляем пробелы перед открывающими тегами
        '/(\s)+/s',         // Сжимаем множественные пробелы
        '/<!--(.*?)-->)/s', // Удаляем HTML комментарии (кроме conditional)
    ];
    
    $replace = [
        '>',
        '<',
        '\\1',
        '',
    ];
    
    $buffer = preg_replace($search, $replace, $buffer);
    
    // Удаляем пустые строки
    $buffer = preg_replace('/^\s+|\s+$/m', '', $buffer);
    
    return $buffer;
}

// Включаем минификацию HTML
if (get_theme_mod('atk_ved_minify_html', true)) {
    add_action('wp_footer', function() {
        ob_start('atk_ved_minify_html');
    }, 0);
}

/**
 * Минификация CSS
 */
function atk_ved_minify_css(string $css): string {
    // Удаляем комментарии
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    
    // Удаляем пробелы
    $css = str_replace(["\r\n", "\r", "\n", "\t", '  ', '    '], '', $css);
    
    // Удаляем пробелы вокруг символов
    $css = preg_replace('/\s*([{}:;,])\s*/', '$1', $css);
    
    // Удаляем конечные точки с запятой перед }
    $css = preg_replace('/;}/', '}', $css);
    
    return $css;
}

/**
 * Минификация JS
 */
function atk_ved_minify_js(string $js): string {
    // Удаляем однострочные комментарии (кроме тех, что начинаются с //! и /*)
    $js = preg_replace('/\/\/[^\n]*/', '', $js);
    
    // Удаляем многострочные комментарии (кроме тех, что начинаются с /*!)
    $js = preg_replace('!/\*[^!*]*\*+([^/][^*]*\*+)*/!', '', $js);
    
    // Удаляем лишние пробелы
    $js = preg_replace('/\s+/', ' ', $js);
    
    // Удаляем пробелы вокруг символов
    $js = preg_replace('/\s*([{};:,()+\-*/%])\s*/', '$1', $js);
    
    return $js;
}

/**
 * CDN для статических ресурсов
 */
function atk_ven_cdn_rewrite(string $url): string {
    $cdn_url = get_theme_mod('atk_ved_cdn_url', '');
    
    if (empty($cdn_url)) {
        return $url;
    }
    
    $cdn_url = rtrim($cdn_url, '/');
    $uploads_dir = wp_upload_dir()['baseurl'];
    $includes_dir = includes_url();
    $content_dir = content_url();
    $theme_dir = get_template_directory_uri();
    
    // Заменяем URL загрузок
    if (strpos($url, $uploads_dir) === 0) {
        $url = str_replace($uploads_dir, $cdn_url . '/uploads', $url);
    }
    // Заменяем URL темы
    elseif (strpos($url, $theme_dir) === 0) {
        $url = str_replace($theme_dir, $cdn_url . '/theme', $url);
    }
    // Заменяем URL контента
    elseif (strpos($url, $content_dir) === 0) {
        $url = str_replace($content_dir, $cdn_url . '/wp-content', $url);
    }
    
    return $url;
}

// Применяем CDN к различным типам URL
if (get_theme_mod('atk_ved_cdn_enabled', false)) {
    add_filter('wp_get_attachment_url', 'atk_ven_cdn_rewrite', 10, 1);
    add_filter('wp_get_attachment_image_src', function($image, $attachment_id) {
        if ($image) {
            $image[0] = atk_ven_cdn_rewrite($image[0]);
        }
        return $image;
    }, 10, 2);
    add_filter('theme_mod_background_image', 'atk_ven_cdn_rewrite', 10, 1);
    add_filter('wp_calculate_image_srcset', function($sources) {
        foreach ($sources as &$source) {
            $source['url'] = atk_ven_cdn_rewrite($source['url']);
        }
        return $sources;
    }, 10, 1);
}

/**
 * Предзагрузка критических скриптов
 */
function atk_ved_preload_scripts(): void {
    $theme_uri = get_template_directory_uri();
    
    // Критические скрипты для предзагрузки
    $scripts = [
        $theme_uri . '/js/main.js',
        $theme_uri . '/js/modal.js',
    ];
    
    foreach ($scripts as $script) {
        echo '<link rel="preload" href="' . esc_url($script) . '" as="script">' . "\n";
    }
}
add_action('wp_head', 'atk_ved_preload_scripts', 2);

/**
 * Асинхронная загрузка некритичных скриптов
 */
function atk_ved_async_scripts(): void {
    // Скрипты для асинхронной загрузки
    $async_scripts = [
        'google-analytics',
        'yandex-metrika',
        'facebook-pixel',
    ];
    
    foreach ($async_scripts as $handle) {
        add_filter("script_loader_tag", function($tag, $h) use ($handle) {
            if ($h === $handle) {
                return str_replace(' src', ' async="async" src', $tag);
            }
            return $tag;
        }, 10, 2);
    }
}
add_action('wp_enqueue_scripts', 'atk_ved_async_scripts');

/**
 * Отложенная загрузка скриптов
 */
function atk_ved_defer_scripts(): void {
    // Скрипты для отложенной загрузки
    $defer_scripts = [
        'comment-reply',
        'wp-embed',
        'jquery-migrate',
    ];
    
    foreach ($defer_scripts as $handle) {
        add_filter("script_loader_tag", function($tag, $h) use ($handle) {
            if ($h === $handle) {
                return str_replace(' src', ' defer="defer" src', $tag);
            }
            return $tag;
        }, 10, 2);
    }
}
add_action('wp_enqueue_scripts', 'atk_ved_defer_scripts');

/**
 * Удаление ненужных скриптов
 */
function atk_ved_remove_unnecessary_scripts(): void {
    // Удаляем dashicons если не админка
    if (!is_user_logged_in()) {
        wp_deregister_style('dashicons');
    }
    
    // Удаляем wp-embed если не сингл
    if (!is_singular()) {
        wp_deregister_script('wp-embed');
    }
    
    // Удаляем comment-reply если не сингл и комментарии закрыты
    if (!is_singular() || !comments_open()) {
        wp_deregister_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'atk_ved_remove_unnecessary_scripts', 100);

/**
 * Объединение Google Fonts
 */
function atk_ved_combine_google_fonts(string $tag, string $handle, string $src): string {
    if (strpos($src, 'fonts.googleapis.com') !== false) {
        // Собираем все Google Fonts запросы
        static $google_fonts = [];
        $google_fonts[] = $src;
        
        // Если это последний скрипт, объединяем
        add_action('wp_footer', function() use (&$google_fonts) {
            if (empty($google_fonts)) {
                return;
            }
            
            $families = [];
            foreach ($google_fonts as $url) {
                parse_str(parse_url($url, PHP_URL_QUERY), $params);
                if (!empty($params['family'])) {
                    $families[] = $params['family'];
                }
            }
            
            if (!empty($families)) {
                $combined = implode('|', array_unique($families));
                $combined_url = 'https://fonts.googleapis.com/css2?family=' . $combined . '&display=swap';
                
                echo '<link rel="preload" href="' . esc_url($combined_url) . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n";
                echo '<noscript><link rel="stylesheet" href="' . esc_url($combined_url) . '"></noscript>' . "\n";
            }
        }, 1);
        
        // Возвращаем пустую строку чтобы дублировать загрузку
        return '';
    }
    
    return $tag;
}
add_filter('style_loader_tag', 'atk_ved_combine_google_fonts', 10, 3);

/**
 * Настройки в Customizer
 */
function atk_ved_performance_customizer($wp_customize): void {
    $wp_customize->add_section('atk_ved_performance', [
        'title' => __('Производительность', 'atk-ved'),
        'priority' => 100,
    ]);
    
    // Минификация HTML
    $wp_customize->add_setting('atk_ved_minify_html', [
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ]);
    
    $wp_customize->add_control('atk_ved_minify_html', [
        'label' => __('Минификация HTML', 'atk-ved'),
        'section' => 'atk_ved_performance',
        'type' => 'checkbox',
        'description' => __('Удаляет лишние пробелы и комментарии из HTML', 'atk-ved'),
    ]);
    
    // CDN URL
    $wp_customize->add_setting('atk_ved_cdn_url', [
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    
    $wp_customize->add_control('atk_ved_cdn_url', [
        'label' => __('CDN URL', 'atk-ved'),
        'section' => 'atk_ved_performance',
        'type' => 'url',
        'description' => __('Например: https://cdn.example.com', 'atk-ved'),
    ]);
    
    // Включение CDN
    $wp_customize->add_setting('atk_ved_cdn_enabled', [
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ]);
    
    $wp_customize->add_control('atk_ved_cdn_enabled', [
        'label' => __('Включить CDN', 'atk-ved'),
        'section' => 'atk_ved_performance',
        'type' => 'checkbox',
    ]);
}
add_action('customize_register', 'atk_ved_performance_customizer');

/**
 * Генерация критического CSS
 */
function atk_ved_generate_critical_css(): void {
    $critical_css = get_transient('atk_ved_critical_css');
    
    if (empty($critical_css)) {
        // Генерируем критический CSS
        $critical_css = '
            /* Critical CSS */
            body{margin:0;padding:0}
            .site-header{background:#fff}
            .hero-section{min-height:100vh}
        ';
        
        set_transient('atk_ved_critical_css', $critical_css, WEEK_IN_SECONDS);
    }
    
    echo '<style>' . $critical_css . '</style>' . "\n";
}
add_action('wp_head', 'atk_ved_generate_critical_css', 0);

/**
 * Очистка кэша при обновлении темы
 */
function atk_ved_clear_cache_on_update(): void {
    delete_transient('atk_ved_critical_css');
    ATK_VED_Cache::get_instance()->flush();
}
add_action('after_switch_theme', 'atk_ved_clear_cache_on_update');
