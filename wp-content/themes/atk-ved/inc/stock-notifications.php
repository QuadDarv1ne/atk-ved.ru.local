<?php
/**
 * Stock Notifications System
 * Уведомления о поступлении товара на склад
 * 
 * @package ATK_VED
 * @since 3.2.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Создание таблицы для уведомлений
 */
function atk_ved_create_stock_notifications_table(): void {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'atk_ved_stock_notifications';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        product_id bigint(20) NOT NULL,
        user_email varchar(100) NOT NULL,
        user_name varchar(100) DEFAULT '',
        user_phone varchar(20) DEFAULT '',
        status varchar(20) DEFAULT 'pending',
        sent_at datetime DEFAULT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY product_id (product_id),
        KEY user_email (user_email),
        KEY status (status),
        KEY created_at (created_at)
    ) {$charset_collate};";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'atk_ved_create_stock_notifications_table');
add_action('plugins_loaded', 'atk_ved_create_stock_notifications_table');

/**
 * AJAX: Подписка на уведомление о поступлении
 */
function atk_ved_ajax_subscribe_stock_notification(): void {
    check_ajax_referer('atk_ved_nonce', 'nonce');
    
    global $wpdb;
    
    $product_id = intval($_POST['product_id'] ?? 0);
    $email = sanitize_email($_POST['email'] ?? '');
    $name = sanitize_text_field($_POST['name'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    
    if (!$product_id) {
        wp_send_json_error(array('message' => __('Неверный ID товара', 'atk-ved')));
    }
    
    if (!is_email($email)) {
        wp_send_json_error(array('message' => __('Введите корректный email', 'atk-ved')));
    }
    
    // Проверка: товар существует
    $product = wc_get_product($product_id);
    if (!$product) {
        wp_send_json_error(array('message' => __('Товар не найден', 'atk-ved')));
    }
    
    // Проверка: товар действительно нет в наличии
    if ($product->is_in_stock()) {
        wp_send_json_error(array('message' => __('Товар уже в наличии', 'atk-ved')));
    }
    
    // Проверка: пользователь уже не подписан
    $table_name = $wpdb->prefix . 'atk_ved_stock_notifications';
    $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM {$table_name} WHERE product_id = %d AND user_email = %s AND status = 'pending'",
        $product_id,
        $email
    ));
    
    if ($exists) {
        wp_send_json_error(array('message' => __('Вы уже подписаны на уведомление', 'atk-ved')));
    }
    
    // Добавление подписки
    $wpdb->insert($table_name, array(
        'product_id' => $product_id,
        'user_email' => $email,
        'user_name' => $name,
        'user_phone' => $phone,
        'status' => 'pending',
    ));
    
    $notification_id = $wpdb->insert_id;
    
    if ($notification_id) {
        wp_send_json_success(array(
            'message' => __('Вы будете уведомлены о поступлении товара', 'atk-ved'),
            'id' => $notification_id,
        ));
    }
    
    wp_send_json_error(array('message' => __('Ошибка подписки', 'atk-ved')));
}
add_action('wp_ajax_atk_ved_subscribe_stock', 'atk_ved_ajax_subscribe_stock_notification');
add_action('wp_ajax_nopriv_atk_ved_subscribe_stock', 'atk_ved_ajax_subscribe_stock_notification');

/**
 * Отправка уведомлений при поступлении товара
 */
function atk_ved_send_stock_notifications(int $product_id): void {
    global $wpdb;
    
    $product = wc_get_product($product_id);
    
    if (!$product || !$product->is_in_stock()) {
        return;
    }
    
    $table_name = $wpdb->prefix . 'atk_ved_stock_notifications';
    
    // Получаем все pending подписки для этого товара
    $notifications = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$table_name} WHERE product_id = %d AND status = 'pending'",
        $product_id
    ));
    
    if (empty($notifications)) {
        return;
    }
    
    $sent_count = 0;
    $product_name = $product->get_name();
    $product_url = $product->get_permalink();
    $product_price = $product->get_price_html();
    
    foreach ($notifications as $notification) {
        $to = $notification->user_email;
        $subject = sprintf(__('Товар "%s" теперь в наличии!', 'atk-ved'), $product_name);
        
        $message = atk_ved_get_stock_notification_email_template($product, $notification);
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        );
        
        if (wp_mail($to, $subject, $message, $headers)) {
            // Обновляем статус
            $wpdb->update($table_name, array(
                'status' => 'sent',
                'sent_at' => current_time('mysql'),
            ), array('id' => $notification->id));
            
            $sent_count++;
        }
    }
    
    // Логирование
    error_log(sprintf('[ATK_VED] Отправлено %d уведомлений о поступлении товара #%d', $sent_count, $product_id));
}

/**
 * Шаблон email уведомления
 */
function atk_ved_get_stock_notification_email_template(object $product, object $notification): string {
    $product_name = $product->get_name();
    $product_url = $product->get_permalink();
    $product_image = $product->get_image('medium', array('style' => 'max-width: 300px; height: auto;'));
    $product_price = $product->get_price_html();
    $site_name = get_bloginfo('name');
    $site_url = home_url();
    
    ob_start();
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; background: #f5f5f5; margin: 0; padding: 0; }
            .email-wrapper { max-width: 600px; margin: 0 auto; background: #fff; }
            .email-header { background: linear-gradient(135deg, #e31e24, #c01a1f); padding: 30px; text-align: center; }
            .email-logo { color: #fff; font-size: 24px; font-weight: bold; }
            .email-body { padding: 40px 30px; }
            .email-greeting { font-size: 18px; margin-bottom: 20px; }
            .email-product { text-align: center; margin: 30px 0; }
            .email-product-image { margin-bottom: 20px; }
            .email-product-title { font-size: 22px; font-weight: bold; margin-bottom: 10px; }
            .email-product-price { font-size: 24px; color: #e31e24; font-weight: bold; margin-bottom: 20px; }
            .email-button { display: inline-block; padding: 16px 40px; background: #e31e24; color: #fff; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px; }
            .email-footer { background: #2c2c2c; color: #fff; padding: 30px; text-align: center; font-size: 14px; }
            .email-footer a { color: #fff; text-decoration: none; }
        </style>
    </head>
    <body>
        <div class="email-wrapper">
            <div class="email-header">
                <div class="email-logo"><?php echo esc_html($site_name); ?></div>
            </div>
            <div class="email-body">
                <div class="email-greeting">
                    <?php if ($notification->user_name): ?>
                        <?php printf(__('Здравствуйте, %s!', 'atk-ved'), esc_html($notification->user_name)); ?>
                    <?php else: ?>
                        <?php _e('Здравствуйте!', 'atk-ved'); ?>
                    <?php endif; ?>
                </div>
                
                <p><?php _e('Отличная новость! Товар, на который вы подписались, теперь доступен для заказа.', 'atk-ved'); ?></p>
                
                <div class="email-product">
                    <div class="email-product-image">
                        <?php echo $product_image; ?>
                    </div>
                    <div class="email-product-title"><?php echo esc_html($product_name); ?></div>
                    <div class="email-product-price"><?php echo $product_price; ?></div>
                    <a href="<?php echo esc_url($product_url); ?>" class="email-button">
                        <?php _e('Заказать сейчас', 'atk-ved'); ?>
                    </a>
                </div>
                
                <p style="color: #f44336; font-weight: bold;">
                    <?php _e('⚠️ Поторопитесь! Количество товара ограничено.', 'atk-ved'); ?>
                </p>
            </div>
            <div class="email-footer">
                <p>&copy; <?php echo date('Y'); ?> <?php echo esc_html($site_name); ?>. <?php _e('Все права защищены.', 'atk-ved'); ?></p>
                <p><a href="<?php echo esc_url($site_url); ?>"><?php _e('Перейти на сайт', 'atk-ved'); ?></a></p>
            </div>
        </div>
    </body>
    </html>
    <?php
    return ob_get_clean();
}

/**
 * Хук: Отправка уведомлений при изменении статуса товара
 */
function atk_ved_check_stock_notification_trigger(int $product_id): void {
    $product = wc_get_product($product_id);
    
    if (!$product) {
        return;
    }
    
    // Проверяем, был ли товар ранее нет в наличии
    $old_stock_status = get_post_meta($product_id, '_atk_old_stock_status', true);
    
    if ($old_stock_status === 'outofstock' && $product->is_in_stock()) {
        atk_ved_send_stock_notifications($product_id);
    }
    
    // Сохраняем текущий статус для следующего сравнения
    update_post_meta($product_id, '_atk_old_stock_status', $product->get_stock_status());
}
add_action('woocommerce_product_set_stock_status', 'atk_ved_check_stock_notification_trigger');

/**
 * Шорткод: Форма подписки на уведомление
 */
function atk_ved_stock_notification_form_shortcode(): string {
    if (!class_exists('WooCommerce')) {
        return '';
    }
    
    global $product;
    
    if (!$product || $product->is_in_stock()) {
        return '';
    }
    
    ob_start();
    ?>
    <div class="stock-notification-form">
        <h4><?php _e('Сообщить о поступлении', 'atk-ved'); ?></h4>
        <p><?php _e('Мы уведомим вас, когда товар появится в наличии', 'atk-ved'); ?></p>
        
        <form class="atk-stock-form">
            <input type="hidden" name="product_id" value="<?php echo $product->get_id(); ?>">
            
            <div class="form-group">
                <input type="email" name="email" placeholder="<?php esc_attr_e('Ваш Email *', 'atk-ved'); ?>" required>
            </div>
            
            <div class="form-group">
                <input type="text" name="name" placeholder="<?php esc_attr_e('Ваше имя', 'atk-ved'); ?>">
            </div>
            
            <div class="form-group">
                <input type="tel" name="phone" placeholder="<?php esc_attr_e('Ваш телефон', 'atk-ved'); ?>">
            </div>
            
            <button type="submit" class="btn-subscribe">
                <?php _e('Подписаться', 'atk-ved'); ?>
            </button>
            
            <div class="form-message"></div>
        </form>
    </div>
    
    <style>
    .stock-notification-form {
        background: #f8f9fa;
        padding: 25px;
        border-radius: 12px;
        border-left: 4px solid #FF9800;
        margin: 30px 0;
    }
    
    .stock-notification-form h4 {
        margin: 0 0 10px;
        font-size: 18px;
        color: #333;
    }
    
    .stock-notification-form p {
        margin: 0 0 20px;
        color: #666;
        font-size: 14px;
    }
    
    .atk-stock-form .form-group {
        margin-bottom: 15px;
    }
    
    .atk-stock-form input {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 15px;
    }
    
    .atk-stock-form input:focus {
        outline: none;
        border-color: #e31e24;
    }
    
    .btn-subscribe {
        width: 100%;
        padding: 14px;
        background: #e31e24;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .btn-subscribe:hover {
        background: #c01a1f;
        transform: translateY(-2px);
    }
    
    .form-message {
        margin-top: 15px;
        font-size: 14px;
        text-align: center;
    }
    
    .form-message.success { color: #4CAF50; }
    .form-message.error { color: #f44336; }
    </style>
    
    <script>
    (function($) {
        'use strict';
        
        $('.atk-stock-form').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $message = $form.find('.form-message');
            const $button = $form.find('.btn-subscribe');
            
            $button.prop('disabled', true).text('<?php _e('Отправка...', 'atk-ved'); ?>');
            
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'atk_ved_subscribe_stock',
                    nonce: '<?php echo wp_create_nonce('atk_ved_nonce'); ?>',
                    product_id: $form.find('[name="product_id"]').val(),
                    email: $form.find('[name="email"]').val(),
                    name: $form.find('[name="name"]').val(),
                    phone: $form.find('[name="phone"]').val()
                },
                success: function(response) {
                    if (response.success) {
                        $message.addClass('success').text(response.data.message);
                        $form[0].reset();
                    } else {
                        $message.addClass('error').text(response.data.message);
                    }
                },
                error: function() {
                    $message.addClass('error').text('<?php _e('Ошибка отправки', 'atk-ved'); ?>');
                },
                complete: function() {
                    $button.prop('disabled', false).text('<?php _e('Подписаться', 'atk-ved'); ?>');
                }
            });
        });
    })(jQuery);
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('stock_notification', 'atk_ved_stock_notification_form_shortcode');

/**
 * Автоматическое добавление формы на страницу товара
 */
function atk_ved_auto_add_stock_notification_form(): void {
    if (!class_exists('WooCommerce') || !is_product()) {
        return;
    }
    
    global $product;
    
    if (!$product || $product->is_in_stock()) {
        return;
    }
    
    echo do_shortcode('[stock_notification]');
}
add_action('woocommerce_single_product_summary', 'atk_ved_auto_add_stock_notification_form', 37);

/**
 * Админка: Страница управления подписками
 */
function atk_ved_stock_notifications_admin_menu(): void {
    add_submenu_page(
        'woocommerce',
        __('Уведомления о поступлении', 'atk-ved'),
        __('Уведомления', 'atk-ved'),
        'manage_woocommerce',
        'atk-ved-stock-notifications',
        'atk_ved_stock_notifications_admin_page'
    );
}
add_action('admin_menu', 'atk_ved_stock_notifications_admin_menu');

/**
 * Админка: Страница подписок
 */
function atk_ved_stock_notifications_admin_page(): void {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'atk_ved_stock_notifications';
    
    // Пагинация
    $per_page = 20;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $per_page;
    
    // Фильтры
    $status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
    $product_filter = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
    
    // Построение WHERE
    $where = array('1=1');
    
    if ($status_filter !== 'all') {
        $where[] = $wpdb->prepare('status = %s', $status_filter);
    }
    
    if ($product_filter) {
        $where[] = $wpdb->prepare('product_id = %d', $product_filter);
    }
    
    $where_clause = implode(' AND ', $where);
    
    // Получение данных
    $total = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE {$where_clause}");
    
    $notifications = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$table_name} WHERE {$where_clause} ORDER BY created_at DESC LIMIT %d OFFSET %d",
        $per_page,
        $offset
    ));
    
    $total_pages = ceil($total / $per_page);
    ?>
    <div class="wrap">
        <h1><?php _e('Уведомления о поступлении товаров', 'atk-ved'); ?></h1>
        
        <div class="stock-notifications-stats" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin: 20px 0;">
            <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="font-size: 32px; font-weight: bold; color: #2196F3;"><?php echo $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}"); ?></div>
                <div style="color: #666;"><?php _e('Всего подписок', 'atk-ved'); ?></div>
            </div>
            <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="font-size: 32px; font-weight: bold; color: #FF9800;"><?php echo $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE status = 'pending'"); ?></div>
                <div style="color: #666;"><?php _e('Ожидают', 'atk-ved'); ?></div>
            </div>
            <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="font-size: 32px; font-weight: bold; color: #4CAF50;"><?php echo $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE status = 'sent'"); ?></div>
                <div style="color: #666;"><?php _e('Отправлено', 'atk-ved'); ?></div>
            </div>
        </div>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th><?php _e('Товар', 'atk-ved'); ?></th>
                    <th><?php _e('Email', 'atk-ved'); ?></th>
                    <th><?php _e('Имя', 'atk-ved'); ?></th>
                    <th><?php _e('Телефон', 'atk-ved'); ?></th>
                    <th><?php _e('Статус', 'atk-ved'); ?></th>
                    <th><?php _e('Дата', 'atk-ved'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($notifications)): ?>
                <tr>
                    <td colspan="7"><?php _e('Нет подписок', 'atk-ved'); ?></td>
                </tr>
                <?php else: ?>
                    <?php foreach ($notifications as $notification): ?>
                    <tr>
                        <td><?php echo $notification->id; ?></td>
                        <td>
                            <?php 
                            $product = wc_get_product($notification->product_id);
                            if ($product) {
                                echo '<a href="' . get_permalink($product->get_id()) . '">' . $product->get_name() . '</a>';
                            } else {
                                _e('Товар удалён', 'atk-ved');
                            }
                            ?>
                        </td>
                        <td><?php echo esc_html($notification->user_email); ?></td>
                        <td><?php echo esc_html($notification->user_name ?? '-'); ?></td>
                        <td><?php echo esc_html($notification->user_phone ?? '-'); ?></td>
                        <td>
                            <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: bold; 
                                <?php echo $notification->status === 'sent' ? 'background: #4CAF50; color: #fff;' : 'background: #FF9800; color: #fff;'; ?>">
                                <?php echo esc_html($notification->status === 'sent' ? __('Отправлено', 'atk-ved') : __('Ожидает', 'atk-ved')); ?>
                            </span>
                        </td>
                        <td><?php echo $notification->created_at; ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <?php if ($total_pages > 1): ?>
        <div class="tablenav">
            <div class="tablenav-pages">
                <?php
                echo paginate_links(array(
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '',
                    'prev_text' => '&laquo;',
                    'next_text' => '&raquo;',
                    'total' => $total_pages,
                    'current' => $current_page,
                ));
                ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php
}
