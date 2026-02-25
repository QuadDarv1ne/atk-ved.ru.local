<?php
/**
 * PWA (Progressive Web App) функциональность
 * 
 * @package ATK_VED
 * @since 1.8.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Добавление manifest.json в head
 */
function atk_ved_add_manifest(): void {
    echo '<link rel="manifest" href="' . esc_url(home_url('/manifest.json')) . '">' . "\n";
}
add_action('wp_head', 'atk_ved_add_manifest', 1);

/**
 * Добавление PWA мета-тегов
 */
function atk_ved_add_pwa_meta_tags(): void {
    // Theme color для мобильных браузеров
    echo '<meta name="theme-color" content="#e31e24">' . "\n";
    echo '<meta name="msapplication-TileColor" content="#e31e24">' . "\n";
    
    // Apple PWA настройки
    echo '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
    echo '<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">' . "\n";
    echo '<meta name="apple-mobile-web-app-title" content="АТК ВЭД">' . "\n";
    
    // Apple touch icons
    echo '<link rel="apple-touch-icon" sizes="180x180" href="' . esc_url(get_template_directory_uri()) . '/images/icons/apple-touch-icon.png">' . "\n";
    
    // Favicon
    echo '<link rel="icon" type="image/png" sizes="32x32" href="' . esc_url(get_template_directory_uri()) . '/images/icons/favicon-32x32.png">' . "\n";
    echo '<link rel="icon" type="image/png" sizes="16x16" href="' . esc_url(get_template_directory_uri()) . '/images/icons/favicon-16x16.png">' . "\n";
}
add_action('wp_head', 'atk_ved_add_pwa_meta_tags', 2);

/**
 * Регистрация Service Worker
 */
function atk_ved_register_service_worker(): void {
    if (!is_admin()) {
        ?>
        <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('<?php echo esc_url(home_url('/sw.js')); ?>')
                    .then(function(registration) {
                        console.log('ServiceWorker registration successful:', registration.scope);
                    })
                    .catch(function(err) {
                        console.log('ServiceWorker registration failed:', err);
                    });
            });
        }
        </script>
        <?php
    }
}
add_action('wp_footer', 'atk_ved_register_service_worker');

/**
 * Добавление offline страницы
 */
function atk_ved_add_offline_page(): void {
    // Создаём виртуальную страницу /offline/
    add_rewrite_rule('^offline/?$', 'index.php?offline=1', 'top');
    
    add_filter('query_vars', function($vars) {
        $vars[] = 'offline';
        return $vars;
    });
    
    add_filter('template_include', function($template) {
        if (get_query_var('offline')) {
            $offline_template = get_template_directory() . '/offline.php';
            if (file_exists($offline_template)) {
                return $offline_template;
            }
        }
        return $template;
    });
}
add_action('init', 'atk_ved_add_offline_page');

/**
 * Настройки PWA в Customizer
 */
function atk_ved_pwa_customizer($wp_customize): void {
    $wp_customize->add_section('atk_ved_pwa', array(
        'title'    => __('PWA Настройки', 'atk-ved'),
        'priority' => 37,
        'description' => __('Настройки прогрессивного веб-приложения', 'atk-ved')
    ));

    // Включение PWA
    $wp_customize->add_setting('atk_ved_pwa_enabled', array(
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));

    $wp_customize->add_control('atk_ved_pwa_enabled', array(
        'label'   => __('Включить PWA', 'atk-ved'),
        'section' => 'atk_ved_pwa',
        'type'    => 'checkbox',
    ));

    // Описание иконок
    $wp_customize->add_setting('atk_ved_pwa_icons_info', array(
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('atk_ved_pwa_icons_info', array(
        'section'     => 'atk_ved_pwa',
        'type'        => 'info',
        'description' => __('Загрузите иконки в папку wp-content/themes/atk-ved/images/icons/: icon-72x72.png, icon-96x96.png, icon-192x192.png, icon-512x512.png', 'atk-ved')
    ));
}
add_action('customize_register', 'atk_ved_pwa_customizer');
