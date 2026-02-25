<?php
/**
 * Email Templates for ATK VED Theme
 * Красивые HTML шаблоны для email уведомлений
 * 
 * @package ATK_VED
 * @since 2.7.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Базовый шаблон email
 */
function atk_ved_email_get_template(string $content, string $title = ''): string {
    $logo_url = get_custom_logo() ? wp_get_attachment_image_url(get_custom_logo()->attachment_id, 'medium') : get_template_directory_uri() . '/images/logo/logo.png';
    $site_url = home_url();
    $site_name = get_bloginfo('name');
    $primary_color = '#e31e24';
    
    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo esc_html($title); ?></title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; background: #f5f5f5; }
            .email-wrapper { max-width: 600px; margin: 0 auto; background: #fff; }
            .email-header { background: linear-gradient(135deg, <?php echo $primary_color; ?>, #c01a1f); padding: 30px; text-align: center; }
            .email-logo { max-width: 180px; height: auto; }
            .email-body { padding: 40px 30px; }
            .email-title { font-size: 24px; font-weight: 700; margin-bottom: 20px; color: #2c2c2c; }
            .email-content { font-size: 16px; line-height: 1.8; color: #666; margin-bottom: 30px; }
            .email-button { display: inline-block; padding: 14px 35px; background: <?php echo $primary_color; ?>; color: #fff; text-decoration: none; border-radius: 6px; font-weight: 600; margin: 20px 0; }
            .email-button:hover { background: #c01a1f; }
            .email-info { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }
            .email-info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e0e0e0; }
            .email-info-row:last-child { border-bottom: none; }
            .email-info-label { color: #666; }
            .email-info-value { font-weight: 600; color: #2c2c2c; }
            .email-footer { background: #2c2c2c; color: #fff; padding: 30px; text-align: center; font-size: 14px; }
            .email-footer a { color: #fff; text-decoration: none; }
            .email-footer-links { margin: 15px 0; }
            .email-footer-links a { margin: 0 10px; color: #ccc; }
            .email-highlight { background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0; }
            .email-success { background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 20px 0; }
            .email-status { display: inline-block; padding: 6px 15px; border-radius: 20px; font-size: 13px; font-weight: 600; }
            .status-new { background: #e3f2fd; color: #1976d2; }
            .status-processing { background: #fff3e0; color: #f57c00; }
            .status-completed { background: #e8f5e9; color: #2e7d32; }
            .status-cancelled { background: #ffebee; color: #c62828; }
            @media (max-width: 600px) {
                .email-body { padding: 30px 20px; }
                .email-info-row { flex-direction: column; gap: 5px; }
            }
        </style>
    </head>
    <body>
        <div class="email-wrapper">
            <div class="email-header">
                <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($site_name); ?>" class="email-logo">
            </div>
            <div class="email-body">
                <?php echo $content; ?>
            </div>
            <div class="email-footer">
                <p>&copy; <?php echo date('Y'); ?> <?php echo esc_html($site_name); ?>. Все права защищены.</p>
                <div class="email-footer-links">
                    <a href="<?php echo esc_url($site_url); ?>">Сайт</a> |
                    <a href="<?php echo esc_url($site_url . '/contacts'); ?>">Контакты</a> |
                    <a href="<?php echo esc_url(get_privacy_policy_url()); ?>">Конфиденциальность</a>
                </div>
                <p style="color: #888; font-size: 12px; margin-top: 15px;">
                    Это письмо отправлено автоматически. Пожалуйста, не отвечайте на него.
                </p>
            </div>
        </div>
    </body>
    </html>
    <?php
    return ob_get_clean();
}

/**
 * Шаблон: Новая заявка (для администратора)
 */
function atk_ved_email_new_lead(array $data): string {
    $content = '
    <h1 class="email-title">📬 Новая заявка</h1>
    <div class="email-content">
        <p>Здравствуйте!</p>
        <p>Поступила новая заявка с сайта.</p>
    </div>
    <div class="email-info">
        <div class="email-info-row">
            <span class="email-info-label">Имя:</span>
            <span class="email-info-value">' . esc_html($data['name'] ?? '') . '</span>
        </div>
        <div class="email-info-row">
            <span class="email-info-label">Телефон:</span>
            <span class="email-info-value">' . esc_html($data['phone'] ?? '') . '</span>
        </div>
        <div class="email-info-row">
            <span class="email-info-label">Email:</span>
            <span class="email-info-value">' . esc_html($data['email'] ?? '') . '</span>
        </div>
        <div class="email-info-row">
            <span class="email-info-label">Источник:</span>
            <span class="email-info-value">' . esc_html($data['source'] ?? 'Сайт') . '</span>
        </div>
        ' . (!empty($data['message']) ? '<div class="email-info-row"><span class="email-info-label">Сообщение:</span><span class="email-info-value">' . esc_html($data['message']) . '</span></div>' : '') . '
        <div class="email-info-row">
            <span class="email-info-label">Дата:</span>
            <span class="email-info-value">' . current_time('d.m.Y H:i') . '</span>
        </div>
    </div>
    <div style="text-align: center; margin-top: 30px;">
        <a href="' . esc_url(admin_url()) . '" class="email-button">Перейти в админку</a>
    </div>
    ';
    
    return atk_ved_email_get_template($content, 'Новая заявка');
}

/**
 * Шаблон: Подтверждение получения заявки (для клиента)
 */
function atk_ved_email_lead_confirmation(array $data): string {
    $content = '
    <h1 class="email-title">✅ Заявка принята</h1>
    <div class="email-success">
        <strong>Спасибо, ' . esc_html($data['name'] ?? '') . '!</strong><br>
        Ваша заявка успешно отправлена.
    </div>
    <div class="email-content">
        <p>Мы получили вашу заявку и свяжемся с вами в ближайшее время.</p>
        <p><strong>Что дальше?</strong></p>
        <ul style="margin: 20px 0; padding-left: 20px;">
            <li>Наш менеджер позвонит вам в течение 15 минут</li>
            <li>Ответим на все вопросы</li>
            <li>Рассчитаем стоимость доставки</li>
        </ul>
    </div>
    <div class="email-info">
        <div class="email-info-row">
            <span class="email-info-label">Номер заявки:</span>
            <span class="email-info-value">#' . esc_html($data['order_id'] ?? rand(1000, 9999)) . '</span>
        </div>
        <div class="email-info-row">
            <span class="email-info-label">Дата:</span>
            <span class="email-info-value">' . current_time('d.m.Y H:i') . '</span>
        </div>
    </div>
    <div class="email-highlight">
        <strong>📞 Есть вопросы?</strong><br>
        Позвоните нам: <a href="tel:' . esc_attr(get_theme_mod('atk_ved_phone', '')) . '">' . esc_html(get_theme_mod('atk_ved_phone', '')) . '</a>
    </div>
    ';
    
    return atk_ved_email_get_template($content, 'Заявка принята');
}

/**
 * Шаблон: Подтверждение заказа (WooCommerce)
 */
function atk_ved_email_order_confirmation(WC_Order $order): string {
    $content = '
    <h1 class="email-title">🛒 Заказ подтверждён</h1>
    <div class="email-success">
        <strong>Спасибо за заказ!</strong><br>
        Ваш заказ #' . $order->get_id() . ' успешно оформлен.
    </div>
    <div class="email-content">
        <p>Мы получили ваш заказ и скоро начнём обработку.</p>
    </div>
    <div class="email-info">
        <div class="email-info-row">
            <span class="email-info-label">Номер заказа:</span>
            <span class="email-info-value">#' . $order->get_id() . '</span>
        </div>
        <div class="email-info-row">
            <span class="email-info-label">Дата:</span>
            <span class="email-info-value">' . $order->get_date_created()->format('d.m.Y H:i') . '</span>
        </div>
        <div class="email-info-row">
            <span class="email-info-label">Статус:</span>
            <span class="email-status status-' . esc_attr($order->get_status()) . '">' . wc_get_order_status_name($order->get_status()) . '</span>
        </div>
        <div class="email-info-row">
            <span class="email-info-label">Сумма:</span>
            <span class="email-info-value">' . $order->get_formatted_order_total() . '</span>
        </div>
    </div>
    <h2 style="font-size: 18px; margin: 25px 0 15px;">Состав заказа</h2>
    <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
        <thead>
            <tr style="background: #f8f9fa;">
                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e0e0e0;">Товар</th>
                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #e0e0e0;">Кол-во</th>
                <th style="padding: 12px; text-align: right; border-bottom: 2px solid #e0e0e0;">Цена</th>
            </tr>
        </thead>
        <tbody>
    ';
    
    foreach ($order->get_items() as $item) {
        $content .= '
        <tr>
            <td style="padding: 12px; border-bottom: 1px solid #e0e0e0;">' . esc_html($item->get_name()) . '</td>
            <td style="padding: 12px; text-align: center; border-bottom: 1px solid #e0e0e0;">' . esc_html($item->get_quantity()) . '</td>
            <td style="padding: 12px; text-align: right; border-bottom: 1px solid #e0e0e0;">' . wc_price($item->get_total()) . '</td>
        </tr>
        ';
    }
    
    $content .= '
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="padding: 12px; text-align: right; font-weight: 600; border-top: 2px solid #2c2c2c;">Итого:</td>
                <td style="padding: 12px; text-align: right; font-weight: 600; border-top: 2px solid #2c2c2c;">' . $order->get_formatted_order_total() . '</td>
            </tr>
        </tfoot>
    </table>
    <div style="text-align: center; margin-top: 30px;">
        <a href="' . esc_url($order->get_view_order_url()) . '" class="email-button">Отследить заказ</a>
    </div>
    ';
    
    return atk_ved_email_get_template($content, 'Заказ #' . $order->get_id());
}

/**
 * Шаблон: Статус заказа изменён
 */
function atk_ved_email_order_status_changed(WC_Order $order, string $old_status, string $new_status): string {
    $status_names = array(
        'pending' => 'Ожидает оплаты',
        'processing' => 'В обработке',
        'on-hold' => 'Приостановлен',
        'completed' => 'Выполнен',
        'cancelled' => 'Отменён',
        'refunded' => 'Возвращён',
        'failed' => 'Ошибка',
    );
    
    $content = '
    <h1 class="email-title">📦 Статус заказа изменён</h1>
    <div class="email-info">
        <div class="email-info-row">
            <span class="email-info-label">Номер заказа:</span>
            <span class="email-info-value">#' . $order->get_id() . '</span>
        </div>
        <div class="email-info-row">
            <span class="email-info-label">Новый статус:</span>
            <span class="email-status status-' . esc_attr($new_status) . '">' . ($status_names[$new_status] ?? $new_status) . '</span>
        </div>
    </div>
    <div class="email-content">
        <p>Статус вашего заказа #' . $order->get_id() . ' изменён.</p>
        <p><strong>Предыдущий статус:</strong> ' . ($status_names[$old_status] ?? $old_status) . '</p>
        <p><strong>Новый статус:</strong> ' . ($status_names[$new_status] ?? $new_status) . '</p>
    </div>
    <div style="text-align: center; margin-top: 30px;">
        <a href="' . esc_url($order->get_view_order_url()) . '" class="email-button">Подробнее о заказе</a>
    </div>
    ';
    
    return atk_ved_email_get_template($content, 'Статус заказа #' . $order->get_id());
}

/**
 * Шаблон: Сброс пароля
 */
function atk_ved_email_password_reset(WP_User $user, string $reset_key): string {
    $reset_url = network_site_url("wp-login.php?action=rp&key=$reset_key&login=" . rawurlencode($user->user_login), 'login');
    
    $content = '
    <h1 class="email-title">🔑 Сброс пароля</h1>
    <div class="email-content">
        <p>Здравствуйте, ' . esc_html($user->display_name) . '!</p>
        <p>Вы запросили сброс пароля для вашего аккаунта.</p>
        <p>Нажмите на кнопку ниже, чтобы установить новый пароль:</p>
    </div>
    <div style="text-align: center; margin: 30px 0;">
        <a href="' . esc_url($reset_url) . '" class="email-button">Сбросить пароль</a>
    </div>
    <div class="email-highlight">
        <strong>⚠️ Важно:</strong><br>
        Ссылка действительна в течение 24 часов.<br>
        Если вы не запрашивали сброс пароля, просто проигнорируйте это письмо.
    </div>
    <div class="email-info">
        <div class="email-info-row">
            <span class="email-info-label">IP адрес:</span>
            <span class="email-info-value">' . esc_html($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . '</span>
        </div>
        <div class="email-info-row">
            <span class="email-info-label">Время:</span>
            <span class="email-info-value">' . current_time('d.m.Y H:i') . '</span>
        </div>
    </div>
    ';
    
    return atk_ved_email_get_template($content, 'Сброс пароля');
}

/**
 * Отправка email через WordPress
 */
function atk_ved_send_email(string $to, string $subject, string $html_content): bool {
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        'Reply-To: ' . get_option('admin_email'),
    );
    
    // Логирование
    if (function_exists('atk_ved_log')) {
        atk_ved_log('Email sent', 'info', array(
            'to' => $to,
            'subject' => $subject,
        ));
    }
    
    return wp_mail($to, $subject, $html_content, $headers);
}

/**
 * Интеграция с WooCommerce email
 */
function atk_ved_woocommerce_email_init(): void {
    if (!class_exists('WooCommerce')) {
        return;
    }
    
    // Переопределение шаблонов WooCommerce
    add_filter('woocommerce_email_styles', function($css) {
        $css .= '
        #wrapper { background-color: #f5f5f5; }
        #template_header { background-color: #e31e24; }
        #template_body { background-color: #ffffff; }
        #template_footer { background-color: #2c2c2c; }
        .button { background-color: #e31e24; }
        ';
        return $css;
    });
}
add_action('init', 'atk_ved_woocommerce_email_init');
