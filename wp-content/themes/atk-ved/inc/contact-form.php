<?php
/**
 * Contact Form Handler
 *
 * @package ATK_VED
 */

defined('ABSPATH') || exit;

/**
 * Обработка формы обратной связи
 */
function atk_ved_handle_contact_form() {
    // Проверка nonce
    if (!isset($_POST['contact_nonce']) || !wp_verify_nonce($_POST['contact_nonce'], 'atk_contact_form')) {
        wp_send_json_error(['message' => 'Ошибка безопасности']);
    }

    // Получение данных
    $name = sanitize_text_field($_POST['name'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $message = sanitize_textarea_field($_POST['message'] ?? '');

    // Валидация
    if (empty($name) || empty($phone)) {
        wp_send_json_error(['message' => 'Заполните обязательные поля']);
    }

    // Проверка email если указан
    if (!empty($email) && !is_email($email)) {
        wp_send_json_error(['message' => 'Некорректный email']);
    }

    // Honeypot защита
    if (!empty($_POST['website'])) {
        wp_send_json_error(['message' => 'Спам обнаружен']);
    }

    // Подготовка письма
    $to = get_option('admin_email');
    $subject = 'Новая заявка с сайта ' . get_bloginfo('name');
    
    $body = "Новая заявка с формы обратной связи:\n\n";
    $body .= "Имя: {$name}\n";
    $body .= "Телефон: {$phone}\n";
    if (!empty($email)) {
        $body .= "Email: {$email}\n";
    }
    if (!empty($message)) {
        $body .= "\nСообщение:\n{$message}\n";
    }
    $body .= "\n---\n";
    $body .= "Отправлено: " . current_time('d.m.Y H:i:s') . "\n";
    $body .= "IP: " . $_SERVER['REMOTE_ADDR'] . "\n";

    $headers = ['Content-Type: text/plain; charset=UTF-8'];
    if (!empty($email)) {
        $headers[] = "Reply-To: {$email}";
    }

    // Отправка письма
    $sent = wp_mail($to, $subject, $body, $headers);

    if ($sent) {
        // Сохранение в БД (опционально)
        atk_ved_save_contact_form_submission($name, $phone, $email, $message);
        
        wp_send_json_success(['message' => 'Спасибо! Мы свяжемся с вами в ближайшее время.']);
    } else {
        wp_send_json_error(['message' => 'Ошибка отправки. Попробуйте позже или позвоните нам.']);
    }
}
add_action('wp_ajax_atk_contact_form', 'atk_ved_handle_contact_form');
add_action('wp_ajax_nopriv_atk_contact_form', 'atk_ved_handle_contact_form');

/**
 * Сохранение заявки в БД
 */
function atk_ved_save_contact_form_submission($name, $phone, $email, $message) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'atk_contact_forms';
    
    // Создание таблицы если не существует
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        phone varchar(50) NOT NULL,
        email varchar(255) DEFAULT NULL,
        message text DEFAULT NULL,
        ip varchar(45) DEFAULT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // Вставка данных
    $wpdb->insert(
        $table_name,
        [
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'message' => $message,
            'ip' => $_SERVER['REMOTE_ADDR'],
        ],
        ['%s', '%s', '%s', '%s', '%s']
    );
}

/**
 * Добавление nonce в форму
 */
function atk_ved_contact_form_nonce() {
    return wp_create_nonce('atk_contact_form');
}
