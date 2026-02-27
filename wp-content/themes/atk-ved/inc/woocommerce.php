<?php
/**
 * WooCommerce Integration for ATK VED Theme
 * 
 * @package ATK_VED
 * @since 2.6.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

// Проверка наличия WooCommerce
if (!class_exists('WooCommerce')) {
    add_action('admin_notices', function() {
        echo '<div class="notice notice-warning"><p>';
        echo __('Для работы интернет-магазина требуется плагин <strong>WooCommerce</strong>.', 'atk-ved');
        echo '</p></div>';
    });
    return;
}

/**
 * WooCommerce поддержка темы
 */
function atk_ved_woocommerce_support(): void {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'atk_ved_woocommerce_support');

/**
 * Изменение количества товаров на странице
 */
function atk_ved_products_per_page(): int {
    return 12;
}
add_filter('loop_shop_per_page', 'atk_ved_products_per_page', 20);

/**
 * Изменение количества колонок в каталоге
 */
function atk_ved_loop_columns(): int {
    return 3;
}
add_filter('loop_shop_columns', 'atk_ved_loop_columns');

/**
 * Обёртка для контента WooCommerce
 */
function atk_ved_woocommerce_wrapper_before(): void {
    ?>
    <div class="container">
    <main class="site-main" id="main">
    <?php
}
add_action('woocommerce_before_main_content', 'atk_ved_woocommerce_wrapper_before');

function atk_ved_woocommerce_wrapper_after(): void {
    ?>
    </main>
    </div>
    <?php
}
add_action('woocommerce_after_main_content', 'atk_ved_woocommerce_wrapper_after');

/**
 * Обёртка для товаров
 */
function atk_ved_woocommerce_product_wrapper_before(): void {
    echo '<li class="product-item">';
}
add_action('woocommerce_before_shop_loop_item', 'atk_ved_woocommerce_product_wrapper_before');

function atk_ved_woocommerce_product_wrapper_after(): void {
    echo '</li>';
}
add_action('woocommerce_after_shop_loop_item', 'atk_ved_woocommerce_product_wrapper_after');

/**
 * Изменение текста кнопки "Добавить в корзину"
 */
function atk_ved_add_to_cart_text(string $text): string {
    return __('В корзину', 'atk-ved');
}
add_filter('woocommerce_product_add_to_cart_text', 'atk_ved_add_to_cart_text');
add_filter('woocommerce_product_single_add_to_cart_text', 'atk_ved_add_to_cart_text');

/**
 * AJAX добавление в корзину без перезагрузки
 */
function atk_ved_ajax_add_to_cart(): void {
    check_ajax_referer('atk_ved_nonce', 'nonce');
    
    $product_id = intval($_POST['product_id'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 1);
    
    if (!$product_id) {
        wp_send_json_error(array('message' => __('Неверный ID товара', 'atk-ved')));
    }
    
    WC()->cart->add_to_cart($product_id, $quantity);
    
    wp_send_json_success(array(
        'message' => __('Товар добавлен в корзину', 'atk-ved'),
        'cart_count' => WC()->cart->get_cart_contents_count(),
        'cart_total' => WC()->cart->get_cart_total(),
    ));
}
add_action('wp_ajax_atk_ved_add_to_cart', 'atk_ved_ajax_add_to_cart');
add_action('wp_ajax_nopriv_atk_ved_add_to_cart', 'atk_ved_ajax_add_to_cart');

/**
 * Быстрый просмотр товара
 */
function atk_ved_quick_view_modal(): void {
    ?>
    <div id="quickViewModal" class="modal modal-center modal-lg" data-static-backdrop="false">
        <div class="modal-backdrop"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><?php _e('Быстрый просмотр', 'atk-ved'); ?></h3>
                <button type="button" class="modal-close" data-modal-close="quickViewModal">×</button>
            </div>
            <div class="modal-body" id="quickViewContent">
                <div class="quick-view-loader">
                    <div class="spinner"></div>
                    <p><?php _e('Загрузка...', 'atk-ved'); ?></p>
                </div>
            </div>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'atk_ved_quick_view_modal');

/**
 * AJAX быстрый просмотр товара
 */
function atk_ved_ajax_quick_view(): void {
    check_ajax_referer('atk_ved_nonce', 'nonce');
    
    $product_id = intval($_POST['product_id'] ?? 0);
    
    if (!$product_id) {
        wp_send_json_error(array('message' => __('Неверный ID товара', 'atk-ved')));
    }
    
    $product = wc_get_product($product_id);
    
    if (!$product) {
        wp_send_json_error(array('message' => __('Товар не найден', 'atk-ved')));
    }
    
    ob_start();
    ?>
    <div class="quick-view-product">
        <div class="quick-view-image">
            <?php echo wp_kses_post($product->get_image('large')); ?>
        </div>
        <div class="quick-view-details">
            <h2 class="product-title"><?php echo esc_html($product->get_name()); ?></h2>
            <div class="product-price"><?php echo wp_kses_post($product->get_price_html()); ?></div>
            <div class="product-description"><?php echo wp_kses_post($product->get_short_description()); ?></div>
            
            <?php if ($product->is_in_stock()): ?>
                <div class="product-stock in-stock">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4CAF50" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <?php _e('В наличии', 'atk-ved'); ?>
                </div>
            <?php else: ?>
                <div class="product-stock out-of-stock">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#F44336" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    <?php _e('Нет в наличии', 'atk-ved'); ?>
                </div>
            <?php endif; ?>
            
            <div class="product-actions">
                <button type="button" class="btn btn-primary add-to-cart-quick" data-product-id="<?php echo esc_attr($product_id); ?>">
                    <?php _e('Добавить в корзину', 'atk-ved'); ?>
                </button>
                <a href="<?php echo esc_url($product->get_permalink()); ?>" class="btn btn-secondary">
                    <?php _e('Подробнее', 'atk-ved'); ?>
                </a>
            </div>
        </div>
    </div>
    <?php
    wp_send_json_success(array('html' => ob_get_clean()));
}
add_action('wp_ajax_atk_ved_quick_view', 'atk_ved_ajax_quick_view');
add_action('wp_ajax_nopriv_atk_ved_quick_view', 'atk_ved_ajax_quick_view');

/**
 * Изменение сортировки товаров по умолчанию
 */
function atk_ved_default_sorting(): string {
    return 'popularity';
}
add_filter('woocommerce_default_catalog_orderby', 'atk_ved_default_sorting');

/**
 * Добавление бейджей на товары
 */
function atk_ved_product_badges(): void {
    global $product;
    
    if (!$product) {
        return;
    }
    
    echo '<div class="product-badges">';
    
    // Sale badge
    if ($product->is_on_sale()) {
        echo '<span class="badge badge-error">' . __('-Sale', 'atk-ved') . '</span>';
    }
    
    // New badge
    $created = strtotime($product->get_date_created());
    if ((time() - $created) < (7 * DAY_IN_SECONDS)) {
        echo '<span class="badge badge-info">' . __('New', 'atk-ved') . '</span>';
    }
    
    // Out of stock badge
    if (!$product->is_in_stock()) {
        echo '<span class="badge badge-warning">' . __('Out', 'atk-ved') . '</span>';
    }
    
    echo '</div>';
}
add_action('woocommerce_before_shop_loop_item_title', 'atk_ved_product_badges', 5);

/**
 * Минимизация количества полей оформления заказа
 */
function atk_ved_checkout_fields($fields): array {
    // Убираем ненужные поля
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_address_2']);
    unset($fields['billing']['billing_fax']);
    
    return $fields;
}
add_filter('woocommerce_checkout_fields', 'atk_ved_checkout_fields');

/**
 * Добавление шага "Спасибо" после заказа
 */
function atk_ved_thank_you_page(): void {
    if (!is_order_received_page()) {
        return;
    }
    
    $order_id = apply_filters('woocommerce_thankyou_order_id', absint(get_query_var('order-received')));
    
    if (!$order_id) {
        return;
    }
    
    $order = wc_get_order($order_id);
    
    if (!$order) {
        return;
    }
    
    // Отправка события в аналитику
    ?>
    <script>
    if (typeof ym !== 'undefined') {
        ym(<?php echo esc_js(get_theme_mod('atk_ved_metrika_id', 0)); ?>, 'reachGoal', 'order_completed', {
            order_id: '<?php echo esc_js($order_id); ?>',
            order_total: <?php echo floatval($order->get_total()); ?>
        });
    }
    if (typeof gtag !== 'undefined') {
        gtag('event', 'purchase', {
            'transaction_id': '<?php echo esc_js($order_id); ?>',
            'value': <?php echo floatval($order->get_total()); ?>,
            'currency': 'RUB'
        });
    }
    </script>
    <?php
}
add_action('woocommerce_thankyou', 'atk_ved_thank_you_page');

/**
 * Кастомизация страницы магазина
 */
function atk_ved_shop_page_title(): string {
    if (is_shop()) {
        return get_the_title(get_option('woocommerce_shop_page_id'));
    }
    return '';
}
add_filter('woocommerce_page_title', 'atk_ved_shop_page_title');

/**
 * Добавление характеристик в карточку товара
 */
function atk_ved_product_meta(): void {
    global $product;
    
    $attributes = $product->get_attributes();
    
    if (!empty($attributes)) {
        echo '<div class="product-attributes">';
        echo '<h4>' . __('Характеристики', 'atk-ved') . '</h4>';
        echo '<ul class="attributes-list">';
        
        foreach ($attributes as $attribute) {
            if ($attribute->get_visible()) {
                echo '<li>';
                echo '<strong>' . wc_attribute_label($attribute->get_name()) . ':</strong> ';
                echo implode(', ', wc_get_product_attribute_values($product->get_id(), $attribute->get_name()));
                echo '</li>';
            }
        }
        
        echo '</ul>';
        echo '</div>';
    }
}
add_action('woocommerce_single_product_summary', 'atk_ved_product_meta', 25);

/**
 * Related products с кастомным заголовком
 */
function atk_ved_related_products_args($args): array {
    $args['posts_per_page'] = 4;
    $args['columns'] = 4;
    return $args;
}
add_filter('woocommerce_output_related_products_args', 'atk_ved_related_products_args');

/**
 * Upsell products с кастомным заголовком
 */
function atk_ved_upsell_display(): void {
    woocommerce_upsell_display(4, 4);
}
