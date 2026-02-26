<?php
/**
 * АТК ВЭД Theme Functions
 *
 * @package ATKVed
 * @since   1.0.0
 * @version 3.0.0
 */

declare( strict_types=1 );

// Защита от прямого доступа
defined( 'ABSPATH' ) || exit;

// ============================================================================
// КОНСТАНТЫ (для обратной совместимости)
// ============================================================================

define( 'ATK_VED_VERSION', '3.0.0' );
define( 'ATK_VED_DIR', get_template_directory() );
define( 'ATK_VED_URI', get_template_directory_uri() );

// ============================================================================
// ИНИЦИАЛИЗАЦИЯ (PSR-4 + Composer Autoloader)
// ============================================================================

// Проверка PHP version
if ( version_compare( PHP_VERSION, '8.1', '<' ) ) {
    add_action( 'admin_notice', function() {
        ?>
        <div class="notice notice-error">
            <p>Требуется PHP 8.1 или выше. Текущая версия: <?php echo esc_html( PHP_VERSION ); ?></p>
        </div>
        <?php
    } );
    return;
}

// Подключаем загрузчик
require_once get_template_directory() . '/src/Loader.php';

\ATKVed\Loader::init();

// ============================================================================
// ПОДКЛЮЧЕНИЕ ФАЙЛОВ ИЗ /inc/ (обратная совместимость)
// ============================================================================

$atk_includes = [
    // Ядро
    '/inc/custom-post-types.php',
    '/inc/helpers.php',
    '/inc/ajax-handlers.php',
    '/inc/translations.php',
    // Безопасность
    '/inc/security.php',
    '/inc/security-advanced.php',
    '/inc/recaptcha.php',
    '/inc/cookie-banner.php',
    // Оптимизация
    '/inc/logger.php',
    '/inc/pwa.php',
    // SEO
    '/inc/seo.php',
    '/inc/sitemap.php',
    '/inc/breadcrumbs.php',
    // Функциональность
    '/inc/calculator.php',
    '/inc/shipment-tracking.php',
    '/inc/ui-components.php',
    // ACF
    '/inc/acf-field-groups.php',
    '/inc/acf-options.php',
    '/inc/acf-blocks.php',
    // REST API
    '/inc/rest-api.php',
    '/inc/rest-cache.php',
    // Виджеты
    '/inc/callback-widget.php',
    '/inc/chat-widget.php',
    // E-commerce
    '/inc/woocommerce.php',
    '/inc/amocrm.php',
    // Конверсия / Email
    '/inc/conversion.php',
    '/inc/email-templates.php',
    // Админка
    '/inc/admin-dashboard.php',
    '/inc/notifications.php',
    // UI
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
];

foreach ( $atk_includes as $file ) {
    $path = ATK_VED_DIR . $file;
    if ( file_exists( $path ) ) {
        require_once $path;
    } else {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'ATK VED: missing include — ' . $path );
        }
    }
}

// ============================================================================
// ADMIN: стили, скрипты, колонки CPT
// ============================================================================

add_action( 'admin_enqueue_scripts', function ( string $hook ) {
    global $post_type;
    $is_cpt_screen = in_array( $post_type, [ 'testimonial_file' ], true )
        && in_array( $hook, [ 'post.php', 'post-new.php' ], true );

    if ( ! $is_cpt_screen ) return;

    wp_enqueue_media();
    wp_enqueue_style(  'atk-admin', ATK_VED_URI . '/admin/admin-styles.css',       [], ATK_VED_VERSION );
    wp_enqueue_script( 'atk-admin', ATK_VED_URI . '/admin/admin-enhancements.js',  [ 'jquery' ], ATK_VED_VERSION, true );
    wp_localize_script( 'atk-admin', 'atkAdmin', [
        'nonce'   => wp_create_nonce( 'atk_ved_admin_nonce' ),
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
    ] );
} );

add_filter( 'manage_testimonial_file_posts_columns', function ( array $cols ): array {
    return [
        'cb'        => $cols['cb'],
        'thumbnail' => __( 'Превью', 'atk-ved' ),
        'title'     => __( 'Название', 'atk-ved' ),
        'company'   => __( 'Компания', 'atk-ved' ),
        'file_type' => __( 'Тип', 'atk-ved' ),
        'date'      => $cols['date'],
    ];
} );

add_action( 'manage_testimonial_file_posts_custom_column', function ( string $col, int $id ): void {
    switch ( $col ) {
        case 'thumbnail':
            echo has_post_thumbnail( $id )
                ? get_the_post_thumbnail( $id, [ 60, 60 ] )
                : '<span aria-hidden="true" style="color:#ccc">—</span>';
            break;
        case 'company':
            $company = get_post_meta( $id, '_company_name', true );
            echo $company
                ? esc_html( $company )
                : '<span style="color:#ccc">—</span>';
            break;
        case 'file_type':
            $type  = sanitize_key( (string) get_post_meta( $id, '_file_type', true ) );
            $label = strtoupper( $type );
            echo $label ? esc_html( $label ) : '—';
            break;
    }
}, 10, 2 );