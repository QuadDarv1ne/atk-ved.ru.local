<?php
/**
 * АТК ВЭД Theme Functions
 *
 * @package ATK_VED
 * @since   1.0.0
 * @version 3.0.0
 */

declare( strict_types=1 );

// Защита от прямого доступа
defined( 'ABSPATH' ) || exit;

// ============================================================================
// КОНСТАНТЫ
// ============================================================================

define( 'ATK_VED_VERSION', '3.0.0' );
define( 'ATK_VED_DIR',     get_template_directory() );
define( 'ATK_VED_URI',     get_template_directory_uri() );


// ============================================================================
// ПОДКЛЮЧЕНИЕ ФАЙЛОВ
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
    '/inc/rest-cache.php',   // ← убран дубль
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
        // Логируем только в режиме отладки, не роняем сайт
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'ATK VED: missing include — ' . $path );
        }
    }
}


// ============================================================================
// ПОДДЕРЖКА ТЕМЫ
// ============================================================================

add_action( 'after_setup_theme', function () {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'editor-styles' );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'html5', [
        'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
    ] );

    // Меню
    register_nav_menus( [
        'primary'         => __( 'Главное меню', 'atk-ved' ),
        'footer-services' => __( 'Футер — Услуги', 'atk-ved' ),
        'footer-company'  => __( 'Футер — Компания', 'atk-ved' ),
    ] );

    // Размеры изображений
    add_image_size( 'atk-ved-hero',    800, 600, true );
    add_image_size( 'atk-ved-service', 400, 300, true );
} );


// ============================================================================
// ВИДЖЕТЫ
// ============================================================================

add_action( 'widgets_init', function () {
    register_sidebar( [
        'name'          => __( 'Сайдбар', 'atk-ved' ),
        'id'            => 'sidebar-1',
        'description'   => __( 'Добавьте виджеты сюда', 'atk-ved' ),
        'before_widget' => '<div class="widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ] );
} );


// ============================================================================
// СКРИПТЫ И СТИЛИ
// ============================================================================

add_action( 'wp_enqueue_scripts', 'atk_ved_enqueue_scripts' );

function atk_ved_enqueue_scripts(): void {
    $v = ATK_VED_VERSION;

    // ── Стили ────────────────────────────────────────────────────────────────

    // Дизайн-система (всегда)
    wp_enqueue_style( 'atk-design-tokens',  ATK_VED_URI . '/css/design-tokens.css',  [],                        '3.3' );
    wp_enqueue_style( 'atk-modern-design',  ATK_VED_URI . '/css/modern-design.css',  [ 'atk-design-tokens' ],   '3.3' );
    wp_enqueue_style( 'atk-style',          get_stylesheet_uri(),                     [ 'atk-modern-design' ],   $v   );
    wp_enqueue_style( 'atk-ui',             ATK_VED_URI . '/css/ui-components.css',  [ 'atk-modern-design' ],   $v   );
    wp_enqueue_style( 'atk-ui-extra',       ATK_VED_URI . '/css/additional-components.css', [ 'atk-ui' ],       $v   );
    wp_enqueue_style( 'atk-animations',     ATK_VED_URI . '/css/animations-enhanced.css',   [],                  '3.3' );
    wp_enqueue_style( 'atk-dark-mode',      ATK_VED_URI . '/css/dark-mode-toggle.css',      [],                  '3.3' );

    // Критический CSS inline (с кешированием через transient)
    $critical_css = get_transient( 'atk_ved_critical_css' );
    if ( false === $critical_css ) {
        $critical_file = ATK_VED_DIR . '/css/critical.css';
        $critical_css  = file_exists( $critical_file ) ? file_get_contents( $critical_file ) : '';
        set_transient( 'atk_ved_critical_css', $critical_css, DAY_IN_SECONDS );
    }
    if ( $critical_css ) {
        wp_add_inline_style( 'atk-style', $critical_css );
    }

    // Условные стили
    if ( is_front_page() ) {
        wp_enqueue_style( 'atk-landing',    ATK_VED_URI . '/css/landing-sections.css',   [], '3.3' );
        wp_enqueue_style( 'atk-hero',       ATK_VED_URI . '/css/hero-counters.css',       [], $v   );
        wp_enqueue_style( 'atk-stats',      ATK_VED_URI . '/css/statistics.css',          [], $v   );
        wp_enqueue_style( 'atk-animations-adv', ATK_VED_URI . '/css/advanced-animations.css', [], '3.3' );
    }

    wp_enqueue_style( 'atk-modal',      ATK_VED_URI . '/css/modal.css',          [], $v );
    wp_enqueue_style( 'atk-form',       ATK_VED_URI . '/css/form-loader.css',     [], $v );
    wp_enqueue_style( 'atk-callback',   ATK_VED_URI . '/css/callback-modal.css',  [], $v );
    wp_enqueue_style( 'atk-reviews',    ATK_VED_URI . '/css/reviews-slider.css',  [], $v );
    wp_enqueue_style( 'atk-gallery',    ATK_VED_URI . '/css/gallery.css',         [], $v );

    if ( atk_ved_is_calc_page() ) {
        wp_enqueue_style( 'atk-calculator', ATK_VED_URI . '/css/calculator.css', [], $v );
        wp_enqueue_style( 'atk-tracking',   ATK_VED_URI . '/css/shipment-tracking.css', [], $v );
    }

    if ( is_404() ) {
        wp_enqueue_style( 'atk-404', ATK_VED_URI . '/css/404.css', [], $v );
    }

    // ── Скрипты ──────────────────────────────────────────────────────────────

    // Лоадер — в <head>, остальные в footer
    wp_enqueue_script( 'atk-loader', ATK_VED_URI . '/js/loader.js', [], $v, false );

    // Ядро (без jQuery — используем нативный JS где можно)
    wp_enqueue_script( 'atk-main',       ATK_VED_URI . '/js/main.js',              [ 'jquery' ], $v, true );
    wp_enqueue_script( 'atk-ui',         ATK_VED_URI . '/js/ui-components.js',     [ 'jquery' ], $v, true );
    wp_enqueue_script( 'atk-ui-extra',   ATK_VED_URI . '/js/additional-components.js', [ 'jquery' ], $v, true );
    wp_enqueue_script( 'atk-modal',      ATK_VED_URI . '/js/modal.js',             [ 'jquery' ], $v, true );
    wp_enqueue_script( 'atk-callback',   ATK_VED_URI . '/js/callback-modal.js',    [ 'jquery' ], $v, true );
    wp_enqueue_script( 'atk-reviews',    ATK_VED_URI . '/js/reviews-slider.js',    [ 'jquery' ], $v, true );
    wp_enqueue_script( 'atk-gallery',    ATK_VED_URI . '/js/gallery.js',           [ 'jquery' ], $v, true );
    wp_enqueue_script( 'atk-enhance',    ATK_VED_URI . '/js/enhancements.js',      [ 'jquery' ], $v, true );
    wp_enqueue_script( 'atk-tracking-js', ATK_VED_URI . '/js/tracking.js',         [ 'jquery' ], $v, true );

    if ( is_front_page() ) {
        wp_enqueue_script( 'atk-counters', ATK_VED_URI . '/js/hero-counters.js',  [ 'jquery' ], $v, true );
        wp_enqueue_script( 'atk-stats',    ATK_VED_URI . '/js/statistics.js',     [ 'jquery' ], $v, true );
    }

    if ( atk_ved_is_calc_page() ) {
        wp_enqueue_script( 'atk-calc',    ATK_VED_URI . '/js/calculator.js',         [ 'jquery' ], $v, true );
        wp_enqueue_script( 'atk-calc-fe', ATK_VED_URI . '/js/calculator-frontend.js', [ 'jquery', 'atk-calc' ], $v, true );
        wp_enqueue_script( 'atk-ship',    ATK_VED_URI . '/js/shipment-tracking.js',  [ 'jquery' ], $v, true );
    }

    // Локализация — один объект для всего JS
    wp_localize_script( 'atk-main', 'atkVed', [
        'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
        'nonce'      => wp_create_nonce( 'atk_ved_nonce' ),
        'siteUrl'    => home_url( '/' ),
        'metrikaId'  => (int) get_theme_mod( 'atk_ved_metrika_id', 0 ),
        'gaId'       => sanitize_text_field( get_theme_mod( 'atk_ved_ga_id', '' ) ),
        'newsletter' => [
            'nonce'   => wp_create_nonce( 'atk_newsletter_nonce' ),
            'action'  => 'atk_ved_newsletter_subscribe',
        ],
        'i18n' => [
            'sending'   => __( 'Отправка…', 'atk-ved' ),
            'success'   => __( 'Спасибо! Вы подписались.', 'atk-ved' ),
            'error'     => __( 'Ошибка. Попробуйте ещё раз.', 'atk-ved' ),
            'badEmail'  => __( 'Введите корректный email.', 'atk-ved' ),
        ],
    ] );
}

/**
 * Определяет, нужны ли компоненты калькулятора/трекинга на текущей странице.
 */
function atk_ved_is_calc_page(): bool {
    return is_page( [ 'calculator', 'tracking', 'калькулятор', 'отслеживание' ] )
        || has_shortcode( get_post()->post_content ?? '', 'atk_calculator' )
        || has_shortcode( get_post()->post_content ?? '', 'atk_tracking' );
}


// ============================================================================
// ОПТИМИЗАЦИЯ <HEAD>
// ============================================================================

// Чистим мусорные мета-теги WordPress
add_action( 'init', function () {
    remove_action( 'wp_head', 'wp_generator' );
    remove_action( 'wp_head', 'wlwmanifest_link' );
    remove_action( 'wp_head', 'rsd_link' );
    remove_action( 'wp_head', 'wp_shortlink_wp_head' );
    remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
} );

// Preload + preconnect
add_action( 'wp_head', function () {
    if ( is_front_page() ) {
        printf(
            '<link rel="preload" href="%s" as="style">' . "\n",
            esc_url( ATK_VED_URI . '/css/modern-design.css' )
        );
        printf(
            '<link rel="preload" href="%s" as="script">' . "\n",
            esc_url( ATK_VED_URI . '/js/main.js' )
        );
    }
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    echo '<link rel="dns-prefetch" href="//www.google-analytics.com">' . "\n";
    echo '<link rel="dns-prefetch" href="//mc.yandex.ru">' . "\n";
}, 1 );


// ============================================================================
// МИНИФИКАЦИЯ HTML
// Только фронтенд, только вне AJAX/Cron/REST — и только без WP_DEBUG
// ============================================================================

add_action( 'template_redirect', function () {
    if (
        is_admin()
        || ( defined( 'DOING_AJAX' )  && DOING_AJAX )
        || ( defined( 'DOING_CRON' )  && DOING_CRON )
        || ( defined( 'REST_REQUEST' ) && REST_REQUEST )
        || ( defined( 'WP_DEBUG' )    && WP_DEBUG )
    ) {
        return;
    }

    ob_start( function ( string $buf ): string {
        // Сохраняем содержимое <pre> и <script>/<style> от сжатия
        $preserved = [];
        $buf = preg_replace_callback(
            '#(<(?:pre|script|style)[^>]*>)(.*?)(</(?:pre|script|style)>)#si',
            function ( $m ) use ( &$preserved ): string {
                $key = '<!--PRESERVE_' . count( $preserved ) . '-->';
                $preserved[ $key ] = $m[0];
                return $key;
            },
            $buf
        );

        // Убираем HTML-комментарии (кроме IE-условных)
        $buf = preg_replace( '/<!--(?!\[if)(.|\s)*?-->/', '', $buf );
        // Сжимаем пробелы
        $buf = preg_replace( [ '/\s{2,}/', '/>\s+</' ], [ ' ', '><' ], $buf );

        // Восстанавливаем сохранённые блоки
        return strtr( $buf, $preserved );
    } );
} );


// ============================================================================
// ОПТИМИЗАЦИЯ ИЗОБРАЖЕНИЙ
// ============================================================================

add_filter( 'wp_get_attachment_image_attributes', function ( array $attr ): array {
    $attr['loading']  ??= 'lazy';
    $attr['decoding'] ??= 'async';
    return $attr;
} );


// ============================================================================
// CUSTOMIZER
// ============================================================================

add_action( 'customize_register', 'atk_ved_customize_register' );

function atk_ved_customize_register( WP_Customize_Manager $wp_customize ): void {

    // ── Контакты ─────────────────────────────────────────────────────────────
    $wp_customize->add_section( 'atk_ved_contacts', [
        'title'    => __( 'Контакты', 'atk-ved' ),
        'priority' => 30,
    ] );

    atk_ved_customizer_field( $wp_customize, 'atk_ved_contacts', [
        [ 'id' => 'atk_ved_phone',   'label' => __( 'Телефон', 'atk-ved' ), 'default' => '+7 (XXX) XXX-XX-XX', 'sanitize' => 'sanitize_text_field', 'type' => 'text'  ],
        [ 'id' => 'atk_ved_email',   'label' => __( 'Email',   'atk-ved' ), 'default' => 'info@atk-ved.ru',    'sanitize' => 'sanitize_email',      'type' => 'email' ],
        [ 'id' => 'atk_ved_address', 'label' => __( 'Адрес',   'atk-ved' ), 'default' => 'Москва, Россия',     'sanitize' => 'sanitize_text_field', 'type' => 'text'  ],
    ] );

    // ── Соцсети ───────────────────────────────────────────────────────────────
    $wp_customize->add_section( 'atk_ved_social', [
        'title'    => __( 'Социальные сети', 'atk-ved' ),
        'priority' => 31,
    ] );

    atk_ved_customizer_field( $wp_customize, 'atk_ved_social', [
        [ 'id' => 'atk_ved_whatsapp',  'label' => 'WhatsApp',  'default' => '', 'sanitize' => 'esc_url_raw', 'type' => 'url' ],
        [ 'id' => 'atk_ved_telegram',  'label' => 'Telegram',  'default' => '', 'sanitize' => 'esc_url_raw', 'type' => 'url' ],
        [ 'id' => 'atk_ved_vk',        'label' => 'VK',        'default' => '', 'sanitize' => 'esc_url_raw', 'type' => 'url' ],
        [ 'id' => 'atk_ved_instagram', 'label' => 'Instagram', 'default' => '', 'sanitize' => 'esc_url_raw', 'type' => 'url' ],
        [ 'id' => 'atk_ved_youtube',   'label' => 'YouTube',   'default' => '', 'sanitize' => 'esc_url_raw', 'type' => 'url' ],
    ] );

    // ── Главный экран ─────────────────────────────────────────────────────────
    $wp_customize->add_section( 'atk_ved_hero', [
        'title'    => __( 'Главный экран', 'atk-ved' ),
        'priority' => 32,
    ] );

    atk_ved_customizer_field( $wp_customize, 'atk_ved_hero', [
        [ 'id' => 'atk_ved_hero_title',   'label' => __( 'Заголовок', 'atk-ved' ),           'default' => 'ТОВАРЫ ДЛЯ МАРКЕТПЛЕЙСОВ ИЗ КИТАЯ ОПТОМ', 'sanitize' => 'sanitize_text_field', 'type' => 'text' ],
        [ 'id' => 'atk_ved_stat1_number', 'label' => __( 'Статистика 1 — число', 'atk-ved' ), 'default' => '500+',     'sanitize' => 'sanitize_text_field', 'type' => 'text' ],
        [ 'id' => 'atk_ved_stat1_label',  'label' => __( 'Статистика 1 — подпись', 'atk-ved' ), 'default' => 'КЛИЕНТОВ', 'sanitize' => 'sanitize_text_field', 'type' => 'text' ],
    ] );

    // ── Аналитика ─────────────────────────────────────────────────────────────
    $wp_customize->add_section( 'atk_ved_analytics', [
        'title'    => __( 'Аналитика', 'atk-ved' ),
        'priority' => 40,
    ] );

    atk_ved_customizer_field( $wp_customize, 'atk_ved_analytics', [
        [ 'id' => 'atk_ved_metrika_id', 'label' => __( 'Яндекс.Метрика ID', 'atk-ved' ), 'default' => '', 'sanitize' => 'absint',              'type' => 'text' ],
        [ 'id' => 'atk_ved_ga_id',      'label' => __( 'Google Analytics ID', 'atk-ved' ), 'default' => '', 'sanitize' => 'sanitize_text_field', 'type' => 'text' ],
    ] );
}

/**
 * Хелпер: регистрирует несколько настроек + контролов одной секции.
 *
 * @param WP_Customize_Manager $mgr
 * @param string               $section
 * @param array<int, array>    $fields
 */
function atk_ved_customizer_field( WP_Customize_Manager $mgr, string $section, array $fields ): void {
    foreach ( $fields as $f ) {
        $mgr->add_setting( $f['id'], [
            'default'           => $f['default'],
            'sanitize_callback' => $f['sanitize'],
            'transport'         => 'refresh',
        ] );
        $mgr->add_control( $f['id'], [
            'label'   => $f['label'],
            'section' => $section,
            'type'    => $f['type'],
        ] );
    }
}


// ============================================================================
// NEWSLETTER AJAX
// ============================================================================

add_action( 'wp_ajax_atk_ved_newsletter_subscribe',        'atk_ved_handle_newsletter_subscription' );
add_action( 'wp_ajax_nopriv_atk_ved_newsletter_subscribe', 'atk_ved_handle_newsletter_subscription' );

function atk_ved_handle_newsletter_subscription(): void {
    // Специфичный nonce — не общий atk_ved_nonce
    check_ajax_referer( 'atk_newsletter_nonce', 'nonce' );

    $email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

    if ( ! is_email( $email ) ) {
        wp_send_json_error( [ 'message' => __( 'Введите корректный email.', 'atk-ved' ) ] );
    }

    $subscribers = get_option( 'atk_ved_subscribers', [] );

    if ( in_array( $email, $subscribers, true ) ) {
        wp_send_json_success( [ 'message' => __( 'Вы уже подписаны.', 'atk-ved' ) ] );
    }

    $subscribers[] = $email;
    update_option( 'atk_ved_subscribers', $subscribers, false );

    // TODO: интеграция с Mailchimp / SendPulse / UniSender

    wp_send_json_success( [
        'message' => __( 'Спасибо! Первое письмо придёт в ближайшее время.', 'atk-ved' ),
    ] );
}


// ============================================================================
// ДАННЫЕ КОМПАНИИ (используются в footer.php)
// ============================================================================

function atk_ved_get_company_info(): array {
    static $info = null;
    if ( null === $info ) {
        $founded = 2018;
        $years   = (int) date( 'Y' ) - $founded;
        $info    = [
            'name'        => 'АТК ВЭД',
            'description' => __( 'Товары для маркетплейсов из Китая оптом. Полный цикл работы от поиска до доставки с гарантией качества.', 'atk-ved' ),
            'founded'     => $founded,
            'years'       => $years,
            'deliveries'  => 1000,
            'rating'      => 4.9,
        ];
    }
    return $info;
}

function atk_ved_get_social_links(): array {
    static $links = null;
    if ( null === $links ) {
        $keys  = [ 'whatsapp', 'telegram', 'vk', 'instagram', 'youtube' ];
        $links = [];
        foreach ( $keys as $key ) {
            $val = get_theme_mod( 'atk_ved_' . $key, '' );
            if ( $val ) {
                $links[ $key ] = esc_url( $val );
            }
        }
    }
    return $links;
}

/**
 * Trust-badges с динамическим числом лет.
 */
function atk_ved_get_trust_badges(): array {
    $info = atk_ved_get_company_info();
    return [
        [ 'icon' => 'trophy', 'text' => $info['years'] . ' ' . atk_ved_pluralize( $info['years'], 'год', 'года', 'лет' ) . ' ' . __( 'на рынке', 'atk-ved' ) ],
        [ 'icon' => 'truck',  'text' => $info['deliveries'] . '+ ' . __( 'доставок', 'atk-ved' ) ],
        [ 'icon' => 'star',   'text' => $info['rating'] . '/5 ' . __( 'рейтинг', 'atk-ved' ) ],
        [ 'icon' => 'shield', 'text' => __( 'Гарантия качества', 'atk-ved' ) ],
    ];
}

/**
 * Универсальная функция склонения числительных.
 */
function atk_ved_pluralize( int $n, string $one, string $few, string $many ): string {
    $mod10  = $n % 10;
    $mod100 = $n % 100;
    if ( $mod100 >= 11 && $mod100 <= 19 ) return $many;
    if ( $mod10 === 1 )                   return $one;
    if ( $mod10 >= 2 && $mod10 <= 4 )     return $few;
    return $many;
}

/**
 * SVG-иконки (без эмодзи, доступно для скринридеров).
 */
function atk_ved_icon( string $name ): string {
    static $icons = [
        'trophy'   => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M6 2h12v6a6 6 0 0 1-12 0V2z"/><path d="M6 8a6 6 0 0 1-4-5.66V2h4"/><path d="M18 8a6 6 0 0 0 4-5.66V2h-4"/><line x1="12" y1="14" x2="12" y2="20"/><line x1="8" y1="20" x2="16" y2="20"/></svg>',
        'truck'    => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
        'star'     => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
        'shield'   => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
        'phone'    => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8 8.09a16 16 0 0 0 7.91 7.91l.61-.77a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
        'mail'     => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
        'map-pin'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>',
        'whatsapp' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>',
        'telegram' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>',
        'vk'       => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M15.684 0H8.316C1.592 0 0 1.592 0 8.316v7.368C0 22.408 1.592 24 8.316 24h7.368C22.408 24 24 22.408 24 15.684V8.316C24 1.592 22.408 0 15.684 0zm3.692 17.123h-1.744c-.66 0-.864-.525-2.05-1.727-1.033-1-1.49-1.135-1.744-1.135-.356 0-.458.102-.458.593v1.575c0 .424-.135.678-1.253.678-1.846 0-3.896-1.118-5.335-3.202C5.051 11.607 4.36 9.6 4.36 9.142c0-.254.102-.491.593-.491h1.744c.44 0 .61.203.78.677.863 2.49 2.303 4.675 2.896 4.675.22 0 .322-.102.322-.66V11.16c-.068-1.186-.695-1.287-.695-1.71 0-.203.17-.407.44-.407h2.743c.373 0 .508.203.508.643v3.473c0 .372.17.508.271.508.22 0 .407-.136.813-.542 1.254-1.406 2.151-3.574 2.151-3.574.119-.254.322-.491.762-.491h1.744c.525 0 .643.27.525.643-.22 1.017-2.354 4.031-2.354 4.031-.186.305-.254.44 0 .78.186.254.796.779 1.203 1.253.745.847 1.32 1.558 1.473 2.05.17.49-.085.745-.576.745z"/></svg>',
    ];
    return $icons[ $name ] ?? '';
}

/**
 * Проверка безопасности URL — запрет javascript: / data: схем.
 */
function atk_ved_is_safe_url( string $url ): bool {
    $scheme = parse_url( $url, PHP_URL_SCHEME );
    return in_array( strtolower( (string) $scheme ), [ 'https', 'http', 'tg' ], true );
}


// ============================================================================
// ШОРТКОДЫ
// ============================================================================

add_shortcode( 'button', function ( $atts, ?string $content = null ): string {
    $a = shortcode_atts( [ 'url' => '#', 'style' => 'primary', 'target' => '_self' ], $atts );
    return sprintf(
        '<a href="%s" class="cta-button %s" target="%s" rel="%s">%s</a>',
        esc_url( $a['url'] ),
        esc_attr( $a['style'] ),
        esc_attr( $a['target'] ),
        $a['target'] === '_blank' ? 'noopener noreferrer' : '',
        esc_html( $content ?? '' )
    );
} );

add_shortcode( 'icon', function ( $atts ): string {
    $a = shortcode_atts( [ 'name' => 'check', 'size' => '24' ], $atts );
    $svg = atk_ved_icon( sanitize_key( $a['name'] ) );
    return $svg ?: sprintf(
        '<span class="icon icon-%s" style="font-size:%spx" aria-hidden="true"></span>',
        esc_attr( $a['name'] ),
        (int) $a['size']
    );
} );


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