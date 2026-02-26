<?php
/**
 * Wishlist & Compare System
 * Система избранных товаров и сравнение
 * 
 * @package ATK_VED
 * @since 3.2.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Инициализация сессии для wishlist
 */
function atk_ved_init_wishlist_session(): void {
    if (!session_id()) {
        if (!headers_sent()) {
            session_start();
        }
    }
    
    if (!isset($_SESSION['atk_ved_wishlist'])) {
        $_SESSION['atk_ved_wishlist'] = array();
    }
    
    if (!isset($_SESSION['atk_ved_compare'])) {
        $_SESSION['atk_ved_compare'] = array();
    }
}
add_action('init', 'atk_ved_init_wishlist_session', 1);

/**
 * AJAX: Добавить в wishlist
 */
function atk_ved_ajax_add_to_wishlist(): void {
    check_ajax_referer('atk_ved_nonce', 'nonce');
    
    $product_id = intval($_POST['product_id'] ?? 0);
    
    if (!$product_id) {
        wp_send_json_error(array('message' => __('Неверный ID товара', 'atk-ved')));
    }
    
    if (!in_array($product_id, $_SESSION['atk_ved_wishlist'])) {
        $_SESSION['atk_ved_wishlist'][] = $product_id;
        
        wp_send_json_success(array(
            'message' => __('Добавлено в избранное', 'atk-ved'),
            'count' => count($_SESSION['atk_ved_wishlist']),
        ));
    }
    
    wp_send_json_error(array('message' => __('Уже в избранном', 'atk-ved')));
}
add_action('wp_ajax_atk_ved_add_to_wishlist', 'atk_ved_ajax_add_to_wishlist');
add_action('wp_ajax_nopriv_atk_ved_add_to_wishlist', 'atk_ved_ajax_add_to_wishlist');

/**
 * AJAX: Удалить из wishlist
 */
function atk_ved_ajax_remove_from_wishlist(): void {
    check_ajax_referer('atk_ved_nonce', 'nonce');
    
    $product_id = intval($_POST['product_id'] ?? 0);
    
    $key = array_search($product_id, $_SESSION['atk_ved_wishlist']);
    
    if ($key !== false) {
        unset($_SESSION['atk_ved_wishlist'][$key]);
        $_SESSION['atk_ved_wishlist'] = array_values($_SESSION['atk_ved_wishlist']);
        
        wp_send_json_success(array(
            'message' => __('Удалено из избранного', 'atk-ved'),
            'count' => count($_SESSION['atk_ved_wishlist']),
        ));
    }
    
    wp_send_json_error(array('message' => __('Товар не найден', 'atk-ved')));
}
add_action('wp_ajax_atk_ved_remove_from_wishlist', 'atk_ved_ajax_remove_from_wishlist');
add_action('wp_ajax_nopriv_atk_ved_remove_from_wishlist', 'atk_ved_ajax_remove_from_wishlist');

/**
 * AJAX: Добавить к сравнению
 */
function atk_ved_ajax_add_to_compare(): void {
    check_ajax_referer('atk_ved_nonce', 'nonce');
    
    $product_id = intval($_POST['product_id'] ?? 0);
    
    if (!$product_id) {
        wp_send_json_error(array('message' => __('Неверный ID товара', 'atk-ved')));
    }
    
    // Максимум 4 товара для сравнения
    if (count($_SESSION['atk_ved_compare']) >= 4) {
        wp_send_json_error(array('message' => __('Максимум 4 товара для сравнения', 'atk-ved')));
    }
    
    if (!in_array($product_id, $_SESSION['atk_ved_compare'])) {
        $_SESSION['atk_ved_compare'][] = $product_id;
        
        wp_send_json_success(array(
            'message' => __('Добавлено к сравнению', 'atk-ved'),
            'count' => count($_SESSION['atk_ved_compare']),
        ));
    }
    
    wp_send_json_error(array('message' => __('Уже в сравнении', 'atk-ved')));
}
add_action('wp_ajax_atk_ved_add_to_compare', 'atk_ved_ajax_add_to_compare');
add_action('wp_ajax_nopriv_atk_ved_add_to_compare', 'atk_ved_ajax_add_to_compare');

/**
 * AJAX: Удалить из сравнения
 */
function atk_ved_ajax_remove_from_compare(): void {
    check_ajax_referer('atk_ved_nonce', 'nonce');
    
    $product_id = intval($_POST['product_id'] ?? 0);
    
    $key = array_search($product_id, $_SESSION['atk_ved_compare']);
    
    if ($key !== false) {
        unset($_SESSION['atk_ved_compare'][$key]);
        $_SESSION['atk_ved_compare'] = array_values($_SESSION['atk_ved_compare']);
        
        wp_send_json_success(array(
            'message' => __('Удалено из сравнения', 'atk-ved'),
            'count' => count($_SESSION['atk_ved_compare']),
        ));
    }
    
    wp_send_json_error(array('message' => __('Товар не найден', 'atk-ved')));
}
add_action('wp_ajax_atk_ved_remove_from_compare', 'atk_ved_ajax_remove_from_compare');
add_action('wp_ajax_nopriv_atk_ved_remove_from_compare', 'atk_ved_ajax_remove_from_compare');

/**
 * AJAX: Очистить wishlist
 */
function atk_ved_ajax_clear_wishlist(): void {
    check_ajax_referer('atk_ved_nonce', 'nonce');
    
    $_SESSION['atk_ved_wishlist'] = array();
    
    wp_send_json_success(array('message' => __('Избранное очищено', 'atk-ved')));
}
add_action('wp_ajax_atk_ved_clear_wishlist', 'atk_ved_ajax_clear_wishlist');
add_action('wp_ajax_nopriv_atk_ved_clear_wishlist', 'atk_ved_ajax_clear_wishlist');

/**
 * AJAX: Очистить сравнение
 */
function atk_ved_ajax_clear_compare(): void {
    check_ajax_referer('atk_ved_nonce', 'nonce');
    
    $_SESSION['atk_ved_compare'] = array();
    
    wp_send_json_success(array('message' => __('Сравнение очищено', 'atk-ved')));
}
add_action('wp_ajax_atk_ved_clear_compare', 'atk_ved_ajax_clear_compare');
add_action('wp_ajax_nopriv_atk_ved_clear_compare', 'atk_ved_ajax_clear_compare');

/**
 * Шорткод: Wishlist
 */
function atk_ved_wishlist_shortcode(): string {
    if (!class_exists('WooCommerce')) {
        return '<p>' . __('WooCommerce не установлен', 'atk-ved') . '</p>';
    }
    
    $wishlist_ids = $_SESSION['atk_ved_wishlist'] ?? array();
    
    if (empty($wishlist_ids)) {
        return '<div class="wishlist-empty"><p>' . __('Ваше избранное пусто', 'atk-ved') . '</p></div>';
    }
    
    $products = wc_get_products(array(
        'include' => $wishlist_ids,
        'limit' => -1,
    ));
    
    ob_start();
    ?>
    <div class="wishlist-page">
        <h1><?php _e('Избранное', 'atk-ved'); ?></h1>
        
        <div class="wishlist-actions">
            <button class="btn-clear-wishlist"><?php _e('Очистить всё', 'atk-ved'); ?></button>
            <span class="wishlist-count"><?php echo count($wishlist_ids); ?> <?php _e('товаров', 'atk-ved'); ?></span>
        </div>
        
        <div class="wishlist-grid">
            <?php foreach ($products as $product): ?>
            <div class="wishlist-item" data-product-id="<?php echo $product->get_id(); ?>">
                <div class="wishlist-item-image">
                    <?php echo $product->get_image('medium'); ?>
                    <button class="wishlist-remove" data-product-id="<?php echo $product->get_id(); ?>">×</button>
                </div>
                <div class="wishlist-item-info">
                    <h3><?php echo $product->get_name(); ?></h3>
                    <div class="wishlist-item-price"><?php echo $product->get_price_html(); ?></div>
                    <div class="wishlist-item-actions">
                        <a href="<?php echo $product->get_permalink(); ?>" class="btn-view-product">
                            <?php _e('Просмотр', 'atk-ved'); ?>
                        </a>
                        <button class="btn-add-to-cart" data-product-id="<?php echo $product->get_id(); ?>">
                            <?php _e('В корзину', 'atk-ved'); ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <style>
    .wishlist-page { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
    .wishlist-actions { display: flex; justify-content: space-between; margin-bottom: 30px; }
    .btn-clear-wishlist { background: #f44336; color: #fff; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; }
    .wishlist-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; }
    .wishlist-item { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 15px rgba(0,0,0,0.1); }
    .wishlist-item-image { position: relative; }
    .wishlist-remove { position: absolute; top: 10px; right: 10px; width: 30px; height: 30px; background: rgba(255,255,255,0.9); border: none; border-radius: 50%; cursor: pointer; font-size: 18px; }
    .wishlist-item-info { padding: 20px; }
    .wishlist-item-price { font-size: 20px; font-weight: 700; color: #e31e24; margin: 10px 0; }
    .wishlist-item-actions { display: flex; gap: 10px; margin-top: 15px; }
    .btn-view-product, .btn-add-to-cart { flex: 1; padding: 12px; text-align: center; border-radius: 8px; text-decoration: none; }
    .btn-view-product { background: #f0f0f0; color: #333; }
    .btn-add-to-cart { background: #e31e24; color: #fff; border: none; cursor: pointer; }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode('wishlist', 'atk_ved_wishlist_shortcode');

/**
 * Шорткод: Compare
 */
function atk_ved_compare_shortcode(): string {
    if (!class_exists('WooCommerce')) {
        return '<p>' . __('WooCommerce не установлен', 'atk-ved') . '</p>';
    }
    
    $compare_ids = $_SESSION['atk_ved_compare'] ?? array();
    
    if (empty($compare_ids)) {
        return '<div class="compare-empty"><p>' . __('Сравнение пусто', 'atk-ved') . '</p></div>';
    }
    
    $products = wc_get_products(array(
        'include' => $compare_ids,
        'limit' => -1,
    ));
    
    ob_start();
    ?>
    <div class="compare-page">
        <h1><?php _e('Сравнение товаров', 'atk-ved'); ?></h1>
        
        <div class="compare-actions">
            <button class="btn-clear-compare"><?php _e('Очистить всё', 'atk-ved'); ?></button>
        </div>
        
        <div class="compare-table-wrapper">
            <table class="compare-table">
                <thead>
                    <tr>
                        <th class="compare-feature"><?php _e('Характеристики', 'atk-ved'); ?></th>
                        <?php foreach ($products as $product): ?>
                        <th class="compare-product">
                            <div class="compare-product-header">
                                <?php echo $product->get_image('thumbnail'); ?>
                                <h3><?php echo $product->get_name(); ?></h3>
                                <button class="compare-remove" data-product-id="<?php echo $product->get_id(); ?>">×</button>
                            </div>
                        </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php _e('Цена', 'atk-ved'); ?></td>
                        <?php foreach ($products as $product): ?>
                        <td><?php echo $product->get_price_html(); ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <td><?php _e('Наличие', 'atk-ved'); ?></td>
                        <?php foreach ($products as $product): ?>
                        <td><?php echo $product->is_in_stock() ? '<span class="in-stock">✓ ' . __('В наличии', 'atk-ved') . '</span>' : '<span class="out-stock">✕ ' . __('Нет в наличии', 'atk-ved') . '</span>'; ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <td><?php _e('Рейтинг', 'atk-ved'); ?></td>
                        <?php foreach ($products as $product): ?>
                        <td>
                            <?php 
                            $rating = $product->get_average_rating();
                            echo str_repeat('★', round($rating)) . str_repeat('☆', 5 - round($rating));
                            echo ' (' . $product->get_review_count() . ')';
                            ?>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <td><?php _e('Описание', 'atk-ved'); ?></td>
                        <?php foreach ($products as $product): ?>
                        <td><?php echo wp_trim_words($product->get_short_description(), 20); ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <td><?php _e('Действия', 'atk-ved'); ?></td>
                        <?php foreach ($products as $product): ?>
                        <td>
                            <a href="<?php echo $product->get_permalink(); ?>" class="btn-view"><?php _e('Просмотр', 'atk-ved'); ?></a>
                            <button class="btn-add-to-cart-compare" data-product-id="<?php echo $product->get_id(); ?>"><?php _e('В корзину', 'atk-ved'); ?></button>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <style>
    .compare-page { max-width: 1400px; margin: 0 auto; padding: 40px 20px; }
    .compare-table-wrapper { overflow-x: auto; }
    .compare-table { width: 100%; border-collapse: collapse; background: #fff; }
    .compare-table th, .compare-table td { padding: 20px; border: 1px solid #e0e0e0; text-align: center; }
    .compare-table th { background: #f8f9fa; font-weight: 600; }
    .compare-feature { text-align: left; background: #f8f9fa; font-weight: 600; width: 200px; }
    .compare-product-header { position: relative; }
    .compare-remove { position: absolute; top: 5px; right: 5px; width: 24px; height: 24px; background: #f44336; color: #fff; border: none; border-radius: 50%; cursor: pointer; }
    .in-stock { color: #4CAF50; }
    .out-stock { color: #f44336; }
    .btn-view, .btn-add-to-cart-compare { display: inline-block; padding: 10px 20px; margin: 5px; border-radius: 6px; text-decoration: none; }
    .btn-view { background: #f0f0f0; color: #333; }
    .btn-add-to-cart-compare { background: #e31e24; color: #fff; border: none; cursor: pointer; }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode('compare', 'atk_ved_compare_shortcode');

/**
 * Кнопка wishlist для товаров
 */
function atk_ved_wishlist_button(): void {
    if (!class_exists('WooCommerce') || !is_product()) {
        return;
    }
    
    global $product;
    $product_id = $product->get_id();
    $in_wishlist = in_array($product_id, $_SESSION['atk_ved_wishlist'] ?? array());
    ?>
    <button class="wishlist-button <?php echo $in_wishlist ? 'active' : ''; ?>" data-product-id="<?php echo $product_id; ?>">
        <span class="wishlist-icon"><?php echo $in_wishlist ? '❤️' : '🤍'; ?></span>
        <span class="wishlist-text"><?php echo $in_wishlist ? __('В избранном', 'atk-ved') : __('В избранное', 'atk-ved'); ?></span>
    </button>
    <?php
}
add_action('woocommerce_single_product_summary', 'atk_ved_wishlist_button', 35);

/**
 * Кнопка compare для товаров
 */
function atk_ved_compare_button(): void {
    if (!class_exists('WooCommerce') || !is_product()) {
        return;
    }
    
    global $product;
    $product_id = $product->get_id();
    $in_compare = in_array($product_id, $_SESSION['atk_ved_compare'] ?? array());
    
    if (count($_SESSION['atk_ved_compare'] ?? array()) >= 4 && !$in_compare) {
        return;
    }
    ?>
    <button class="compare-button <?php echo $in_compare ? 'active' : ''; ?>" data-product-id="<?php echo $product_id; ?>">
        <span class="compare-icon">⚖️</span>
        <span class="compare-text"><?php echo $in_compare ? __('В сравнении', 'atk-ved') : __('Сравнить', 'atk-ved'); ?></span>
    </button>
    <?php
}
add_action('woocommerce_single_product_summary', 'atk_ved_compare_button', 36);

/**
 * Иконка wishlist в хедере
 */
function atk_ved_wishlist_header_icon(): void {
    $count = count($_SESSION['atk_ved_wishlist'] ?? array());
    ?>
    <a href="<?php echo esc_url(get_permalink(get_page_by_path('wishlist'))); ?>" class="header-wishlist-icon">
        <span>❤️</span>
        <?php if ($count > 0): ?>
        <span class="wishlist-count-badge"><?php echo $count; ?></span>
        <?php endif; ?>
    </a>
    <?php
}
add_action('wp_footer', 'atk_ved_wishlist_header_icon');

/**
 * Иконка compare в хедере
 */
function atk_ved_compare_header_icon(): void {
    $count = count($_SESSION['atk_ved_compare'] ?? array());
    
    if ($count === 0) {
        return;
    }
    ?>
    <a href="<?php echo esc_url(get_permalink(get_page_by_path('compare'))); ?>" class="header-compare-icon">
        <span>⚖️</span>
        <span class="compare-count-badge"><?php echo $count; ?>/4</span>
    </a>
    <?php
}
add_action('wp_footer', 'atk_ved_compare_header_icon');
