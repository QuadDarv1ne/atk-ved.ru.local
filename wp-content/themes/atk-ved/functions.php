<?php declare(strict_types=1);
/**
 * ATK VED Theme Functions
 *
 * @package ATKVed
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define constants first
if ( ! defined( 'ATK_VED_DIR' ) ) {
    define( 'ATK_VED_DIR', get_template_directory() );
}
if ( ! defined( 'ATK_VED_URI' ) ) {
    define( 'ATK_VED_URI', get_template_directory_uri() );
}

// Load Composer autoloader for theme
$composer_autoload = get_template_directory() . '/vendor/autoload.php';
if ( file_exists( $composer_autoload ) ) {
    require_once $composer_autoload;
} else {
    // Fallback: manually include version.php if Composer autoloader doesn't exist
    $version_file = get_template_directory() . '/version.php';
    if ( file_exists( $version_file ) ) {
        require_once $version_file;
    }
}

// Check PHP version
if ( defined( 'ATK_VED_MIN_PHP_VERSION' ) && version_compare( PHP_VERSION, ATK_VED_MIN_PHP_VERSION, '<' ) ) {
    add_action( 'admin_notice', function() {
        echo '<div class="notice notice-error"><p>PHP ' . esc_html( ATK_VED_MIN_PHP_VERSION ) . '+ required. Current: ' . esc_html( PHP_VERSION ) . '</p></div>';
    } );
    return;
}

// Initialize theme loader if class exists
if ( class_exists( '\ATKVed\Loader' ) ) {
    \ATKVed\Loader::init();
} else {
    // Fallback: manually include necessary files if autoloader is not available
    $fallback_includes = [
        '/src/Base.php',
        '/src/Theme.php',
        '/src/Loader.php',
    ];
    
    foreach ( $fallback_includes as $file ) {
        $path = get_template_directory() . $file;
        if ( file_exists( $path ) ) {
            require_once $path;
        }
    }
    
    // Initialize theme if loader class exists after manual includes
    if ( class_exists( '\ATKVed\Loader' ) ) {
        \ATKVed\Loader::init();
    }
}

$atk_includes = [
    '/inc/custom-post-types.php',
    '/inc/helpers.php',
    '/inc/ajax-handlers.php',
    '/inc/translations.php',
    '/inc/security.php',
    '/inc/security-advanced.php',
    '/inc/recaptcha.php',
    '/inc/cookie-banner.php',
    '/inc/logger.php',
    '/inc/pwa.php',
    '/inc/seo.php',
    '/inc/sitemap.php',
    '/inc/breadcrumbs.php',
    '/inc/calculator.php',
    '/inc/shipment-tracking.php',
    '/inc/ui-components.php',
    '/inc/acf-field-groups.php',
    '/inc/acf-options.php',
    '/inc/acf-blocks.php',
    '/inc/rest-api.php',
    '/inc/rest-cache.php',
    '/inc/callback-widget.php',
    '/inc/chat-widget.php',
    '/inc/woocommerce.php',
    '/inc/amocrm.php',
    '/inc/conversion.php',
    '/inc/email-templates.php',
    '/inc/admin-dashboard.php',
    '/inc/notifications.php',
    '/inc/image-manager.php',
    '/inc/enhanced-ui-components.php',
    '/inc/advanced-ui-components.php',
    '/inc/accessibility-enhancements.php',
    '/inc/health-check.php',
    '/inc/demo-import.php',
    // Восстановленные секции
    '/inc/new-sections.php',
    '/inc/cta-section.php',
    '/inc/faq-section.php',
    '/inc/reviews-section.php',
    '/inc/process-section.php',
    // Фотостоки
    '/inc/stock-photos.php',
    '/inc/stock-photos-integration.php',
    // Доставка
    '/inc/delivery-map.php',
    // Производительность
    '/inc/performance-cdn.php',
    // Улучшенная производительность
    '/inc/enhanced-performance.php',
    // Улучшенная система PWA
    '/inc/enhanced-pwa.php',
    // Улучшенная оптимизация изображений
    '/inc/enhanced-image-optimization.php',
];

foreach ( $atk_includes as $file ) {
    $path = ATK_VED_DIR . $file;
    if ( file_exists( $path ) ) {
        require_once $path;
    }
}

add_action( 'admin_enqueue_scripts', function( $hook ) {
    global $post_type;
    if ( ! in_array( $post_type, [ 'testimonial_file' ], true ) ) {
        return;
    }
    if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_style(  'atk-admin', ATK_VED_URI . '/admin/admin-styles.css', [], ATK_VED_VERSION );
    wp_enqueue_script( 'atk-admin', ATK_VED_URI . '/admin/admin-enhancements.js', [ 'jquery' ], ATK_VED_VERSION, true );
    wp_localize_script( 'atk-admin', 'atkAdmin', [
        'nonce'   => wp_create_nonce( 'atk_ved_admin_nonce' ),
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
    ] );
} );

add_filter( 'manage_testimonial_file_posts_columns', function( $cols ) {
    return [
        'cb'        => $cols['cb'],
        'thumbnail' => __( 'Preview', 'atk-ved' ),
        'title'     => __( 'Title', 'atk-ved' ),
        'company'   => __( 'Company', 'atk-ved' ),
        'file_type' => __( 'Type', 'atk-ved' ),
        'date'      => $cols['date'],
    ];
} );

add_action( 'manage_testimonial_file_posts_custom_column', function( $col, $id ) {
    switch ( $col ) {
        case 'thumbnail':
            echo has_post_thumbnail( $id ) ? get_the_post_thumbnail( $id, [ 60, 60 ] ) : '—';
            break;
        case 'company':
            echo get_post_meta( $id, '_company_name', true ) ?: '—';
            break;
        case 'file_type':
            echo strtoupper( sanitize_key( get_post_meta( $id, '_file_type', true ) ) ) ?: '—';
            break;
    }
}, 10, 2 );