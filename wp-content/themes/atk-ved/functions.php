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

// Load theme version
$version_file = get_template_directory() . '/version.php';
if ( file_exists( $version_file ) ) {
    require_once $version_file;
}

// Load Composer autoloader for theme
$composer_autoload = get_template_directory() . '/vendor/autoload.php';
if ( file_exists( $composer_autoload ) ) {
    require_once $composer_autoload;
}

// Manually include core classes (fallback if no Composer autoload)
$core_files = [
    '/src/Base.php',
    '/src/Setup.php',
    '/src/Enqueue.php',
    '/src/Ajax.php',
    '/src/Shortcodes.php',
    '/src/Customizer.php',
    '/src/Theme.php',
    '/src/Loader.php',
];

foreach ( $core_files as $file ) {
    $path = get_template_directory() . $file;
    if ( file_exists( $path ) ) {
        require_once $path;
    }
}

// Check PHP version
if ( defined( 'ATK_VED_MIN_PHP_VERSION' ) && version_compare( PHP_VERSION, ATK_VED_MIN_PHP_VERSION, '<' ) ) {
    add_action( 'admin_notice', function() {
        echo '<div class="notice notice-error"><p>PHP ' . esc_html( ATK_VED_MIN_PHP_VERSION ) . '+ required. Current: ' . esc_html( PHP_VERSION ) . '</p></div>';
    } );
    return;
}

// Initialize theme
if ( class_exists( '\ATKVed\Theme' ) ) {
    \ATKVed\Theme::getInstance();
}

// Оптимизированная загрузка модулей через Module Loader
require_once ATK_VED_DIR . '/inc/module-loader.php';

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