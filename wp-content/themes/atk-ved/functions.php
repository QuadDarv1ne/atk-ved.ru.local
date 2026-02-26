<?php declare(strict_types=1);
/**
 * ATK VED Theme Functions
 *
 * @package ATKVed
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'ATK_VED_VERSION', '3.3.0' );
define( 'ATK_VED_DIR', get_template_directory() );
define( 'ATK_VED_URI', get_template_directory_uri() );

if ( version_compare( PHP_VERSION, '8.1', '<' ) ) {
    add_action( 'admin_notice', function() {
        echo '<div class="notice notice-error"><p>PHP 8.1+ required. Current: ' . esc_html( PHP_VERSION ) . '</p></div>';
    } );
    return;
}

require_once get_template_directory() . '/src/Loader.php';
\ATKVed\Loader::init();

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
    '/inc/demo-content.php',
    '/inc/welcome-page.php',
    '/inc/performance-analytics.php',
    '/inc/database-optimization.php',
    '/inc/advanced-security.php',
    '/inc/ajax-search.php',
    '/inc/wishlist-compare.php',
    '/inc/stock-notifications.php',
    '/inc/theme-customizer.php',
    '/inc/coupons-system.php',
    '/inc/loyalty-system.php',
    '/inc/dark-mode.php',
    '/inc/performance-optimizer.php',
    '/inc/delivery-map.php',
    '/inc/performance-cache.php',
    '/inc/performance-lazyload.php',
    '/inc/performance-cdn.php',
    '/inc/new-sections.php',
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