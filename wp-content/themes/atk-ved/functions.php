<?php
declare(strict_types=1);

/**
 * ATK VED Theme Functions
 *
 * @package ATKVed
 * @version 3.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ============================================
// 1. Constants & Version Check (Critical First)
// ============================================

if ( ! defined( 'ATK_VED_DIR' ) ) {
    define( 'ATK_VED_DIR', get_template_directory() );
}

if ( ! defined( 'ATK_VED_URI' ) ) {
    define( 'ATK_VED_URI', get_template_directory_uri() );
}

// Load version config early
 $version_file = ATK_VED_DIR . '/version.php';
if ( file_exists( $version_file ) ) {
    require_once $version_file;
}

// PHP Version Check (Must be before loading theme classes)
if ( defined( 'ATK_VED_MIN_PHP_VERSION' ) && version_compare( PHP_VERSION, ATK_VED_MIN_PHP_VERSION, '<' ) ) {
    add_action( 'admin_notices', function() {
        printf(
            '<div class="notice notice-error"><p><strong>ATK VED Theme Error:</strong> PHP version %1$s or higher is required. You are running version %2$s.</p></div>',
            esc_html( ATK_VED_MIN_PHP_VERSION ),
            esc_html( PHP_VERSION )
        );
    } );
    return; // Stop theme execution
}

// ============================================
// 2. Autoloading & Initialization
// ============================================

// Try Composer autoload first
 $composer_autoload = ATK_VED_DIR . '/vendor/autoload.php';
if ( file_exists( $composer_autoload ) ) {
    require_once $composer_autoload;
} else {
    // Fallback: Manual class mapping (Optimized)
    // Note: Ideally, structure classes to match PSR-4 to avoid this map
    spl_autoload_register( function ( $class ) {
        $prefix = 'ATKVed\\';
        $base_dir = ATK_VED_DIR . '/src/';

        // Does the class use the namespace prefix?
        $len = strlen( $prefix );
        if ( strncmp( $prefix, $class, $len ) !== 0 ) {
            return;
        }

        $relative_class = substr( $class, $len );
        $file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

        if ( file_exists( $file ) ) {
            require_once $file;
        }
    } );
}

// Initialize Theme Core
if ( class_exists( '\ATKVed\Theme' ) ) {
    \ATKVed\Theme::getInstance();
}

// Load Module Loader
 $module_loader = ATK_VED_DIR . '/inc/module-loader.php';
if ( file_exists( $module_loader ) ) {
    require_once $module_loader;
}

// Load SEO Functions
$seo_file = ATK_VED_DIR . '/inc/seo.php';
if ( file_exists( $seo_file ) ) {
    require_once $seo_file;
}

// Load Contact Form Handler
$contact_form_file = ATK_VED_DIR . '/inc/contact-form.php';
if ( file_exists( $contact_form_file ) ) {
    require_once $contact_form_file;
}

// Load Image Optimization
$image_opt_file = ATK_VED_DIR . '/inc/image-optimization.php';
if ( file_exists( $image_opt_file ) ) {
    require_once $image_opt_file;
}

// Load Performance Optimization
$performance_file = ATK_VED_DIR . '/inc/performance.php';
if ( file_exists( $performance_file ) ) {
    require_once $performance_file;
}

// ============================================
// 3. Admin Features (Refactored)
// ============================================

/**
 * Setup Admin assets and columns for 'testimonial_file' CPT
 */
add_action( 'admin_init', function() {
    global $pagenow;

    // We only need this logic in admin area
    if ( ! is_admin() ) {
        return;
    }

    // Define the post type once
    $target_post_type = 'testimonial_file';

    // Enqueue Scripts/Styles
    add_action( 'admin_enqueue_scripts', function( $hook ) use ( $target_post_type ) {
        $screen = get_current_screen();

        // Check screen post_type and hook
        if ( ! $screen || $screen->post_type !== $target_post_type ) {
            return;
        }
        
        if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_style( 
            'atk-admin', 
            ATK_VED_URI . '/admin/admin-styles.css', 
            [], 
            defined('ATK_VED_VERSION') ? ATK_VED_VERSION : '1.0.0' 
        );
        wp_enqueue_script( 
            'atk-admin', 
            ATK_VED_URI . '/admin/admin-enhancements.js', 
            [ 'jquery' ], 
            defined('ATK_VED_VERSION') ? ATK_VED_VERSION : '1.0.0', 
            true 
        );
        wp_localize_script( 'atk-admin', 'atkAdmin', [
            'nonce'   => wp_create_nonce( 'atk_ved_admin_nonce' ),
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        ] );
    } );

    // Customize Admin Columns
    add_filter( "manage_{$target_post_type}_posts_columns", function( $cols ) {
        return [
            'cb'        => $cols['cb'] ?? '<input type="checkbox" />',
            'thumbnail' => __( 'Preview', 'atk-ved' ),
            'title'     => __( 'Title', 'atk-ved' ),
            'company'   => __( 'Company', 'atk-ved' ),
            'file_type' => __( 'Type', 'atk-ved' ),
            'date'      => $cols['date'] ?? __( 'Date', 'atk-ved' ),
        ];
    } );

    add_action( "manage_{$target_post_type}_posts_custom_column", function( $col, $id ) {
        switch ( $col ) {
            case 'thumbnail':
                echo has_post_thumbnail( $id ) 
                    ? get_the_post_thumbnail( $id, [ 60, 60 ], [ 'class' => 'attachment-60x60 size-60x60 wp-post-image' ] ) 
                    : '—';
                break;
            case 'company':
                $company = get_post_meta( $id, '_company_name', true );
                echo esc_html( $company ?: '—' );
                break;
            case 'file_type':
                $type = get_post_meta( $id, '_file_type', true );
                echo $type ? '<span class="atk-badge">' . esc_html( strtoupper( sanitize_key( $type ) ) ) . '</span>' : '—';
                break;
        }
    }, 10, 2 );
} );
