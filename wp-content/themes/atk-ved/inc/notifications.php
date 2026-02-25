<?php
/**
 * Internal Notifications System
 * Система внутренних уведомлений для админки
 * 
 * @package ATK_VED
 * @since 2.7.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Класс для работы с уведомлениями
 */
class ATK_VED_Notifications {
    
    private string $table_name;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'atk_ved_notifications';
        
        add_action('admin_init', array($this, 'create_table'));
        add_action('admin_bar_menu', array($this, 'admin_bar_notification'), 100);
        add_action('wp_ajax_atk_ved_get_notifications', array($this, 'ajax_get_notifications'));
        add_action('wp_ajax_atk_ved_mark_read', array($this, 'ajax_mark_read'));
        add_action('wp_ajax_atk_ved_mark_all_read', array($this, 'ajax_mark_all_read'));
        add_action('wp_ajax_atk_ved_delete_notification', array($this, 'ajax_delete_notification'));
    }
    
    /**
     * Создание таблицы
     */
    public function create_table(): void {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT 0,
            title varchar(255) NOT NULL,
            message text,
            type varchar(50) DEFAULT 'info',
            link varchar(255) DEFAULT '',
            is_read tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY is_read (is_read),
            KEY created_at (created_at)
        ) {$charset_collate};";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Создание уведомления
     */
    public function create(string $title, string $message = '', string $type = 'info', string $link = '', ?int $user_id = null): int {
        global $wpdb;
        
        $wpdb->insert($this->table_name, array(
            'user_id' => $user_id ?? get_current_user_id(),
            'title' => sanitize_text_field($title),
            'message' => wp_kses_post($message),
            'type' => sanitize_text_field($type),
            'link' => esc_url_raw($link),
        ));
        
        return (int) $wpdb->insert_id;
    }
    
    /**
     * Получение уведомлений
     */
    public function get_notifications(int $limit = 10, bool $unread_only = false): array {
        global $wpdb;
        
        $user_id = get_current_user_id();
        $where = $wpdb->prepare('user_id = %d OR user_id = 0', $user_id);
        
        if ($unread_only) {
            $where .= ' AND is_read = 0';
        }
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE {$where} ORDER BY created_at DESC LIMIT %d",
            $limit
        ));
    }
    
    /**
     * Количество непрочитанных
     */
    public function get_unread_count(): int {
        global $wpdb;
        
        $user_id = get_current_user_id();
        
        return (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table_name} WHERE (user_id = %d OR user_id = 0) AND is_read = 0",
            $user_id
        ));
    }
    
    /**
     * Отметить как прочитанное
     */
    public function mark_read(int $id): bool {
        global $wpdb;
        
        return $wpdb->update($this->table_name, array('is_read' => 1), array('id' => $id)) !== false;
    }
    
    /**
     * Отметить все как прочитанные
     */
    public function mark_all_read(): bool {
        global $wpdb;
        
        $user_id = get_current_user_id();
        
        return $wpdb->update(
            $this->table_name,
            array('is_read' => 1),
            array('user_id' => $user_id, 'is_read' => 0)
        ) !== false;
    }
    
    /**
     * Удалить уведомление
     */
    public function delete(int $id): bool {
        global $wpdb;
        
        return $wpdb->delete($this->table_name, array('id' => $id)) !== false;
    }
    
    /**
     * Уведомление в админ-баре
     */
    public function admin_bar_notification(): void {
        if (!is_admin() && !current_user_can('manage_options')) {
            return;
        }
        
        global $wp_admin_bar;
        
        $unread_count = $this->get_unread_count();
        $notifications = $this->get_notifications(5, true);
        
        $wp_admin_bar->add_menu(array(
            'id' => 'atk-ved-notifications',
            'title' => '<span class="ab-icon dashicons dashicons-bell"></span>' .
                       '<span class="ab-label">' . __('Уведомления', 'atk-ved') . '</span>' .
                       ($unread_count > 0 ? '<span class="notification-count">' . $unread_count . '</span>' : ''),
            'href' => '#',
        ));
        
        if ($notifications) {
            foreach ($notifications as $notification) {
                $wp_admin_bar->add_menu(array(
                    'parent' => 'atk-ved-notifications',
                    'id' => 'atk-notification-' . $notification->id,
                    'title' => '<div class="notification-item ' . esc_attr($notification->type) . '">' .
                               '<strong>' . esc_html($notification->title) . '</strong>' .
                               '<p>' . esc_html($notification->message) . '</p>' .
                               '<span class="notification-time">' . human_time_diff(strtotime($notification->created_at)) . ' ' . __('назад', 'atk-ved') . '</span>' .
                               '</div>',
                    'href' => $notification->link ?: '#',
                ));
            }
            
            $wp_admin_bar->add_menu(array(
                'parent' => 'atk-ved-notifications',
                'id' => 'atk-mark-all-read',
                'title' => '<div class="mark-all-read">' . __('Отметить все как прочитанные', 'atk-ved') . '</div>',
                'href' => '#',
            ));
        } else {
            $wp_admin_bar->add_menu(array(
                'parent' => 'atk-ved-notifications',
                'id' => 'atk-no-notifications',
                'title' => '<div class="no-notifications">' . __('Нет новых уведомлений', 'atk-ved') . '</div>',
                'href' => '#',
            ));
        }
        ?>
        <style>
            #wpadminbar #wp-admin-bar-atk-ved-notifications .notification-count {
                background: #f44336;
                color: #fff;
                border-radius: 10px;
                padding: 2px 8px;
                font-size: 11px;
                font-weight: 600;
                margin-left: 5px;
            }
            #wpadminbar #wp-admin-bar-atk-ved-notifications .notification-item {
                padding: 10px;
                border-bottom: 1px solid rgba(255,255,255,0.1);
                max-width: 300px;
            }
            #wpadminbar #wp-admin-bar-atk-ved-notifications .notification-item:last-child {
                border-bottom: none;
            }
            #wpadminbar #wp-admin-bar-atk-ved-notifications .notification-item p {
                font-size: 12px;
                margin: 5px 0;
                opacity: 0.9;
            }
            #wpadminbar #wp-admin-bar-atk-ved-notifications .notification-time {
                font-size: 11px;
                opacity: 0.7;
            }
            #wpadminbar #wp-admin-bar-atk-ved-notifications .notification-item.info { border-left: 3px solid #2196F3; }
            #wpadminbar #wp-admin-bar-atk-ved-notifications .notification-item.success { border-left: 3px solid #4CAF50; }
            #wpadminbar #wp-admin-bar-atk-ved-notifications .notification-item.warning { border-left: 3px solid #FF9800; }
            #wpadminbar #wp-admin-bar-atk-ved-notifications .notification-item.error { border-left: 3px solid #F44336; }
            #wpadminbar #wp-admin-bar-atk-ved-notifications .mark-all-read,
            #wpadminbar #wp-admin-bar-atk-ved-notifications .no-notifications {
                padding: 10px;
                text-align: center;
                font-size: 13px;
            }
        </style>
        <?php
    }
    
    /**
     * AJAX: Получить уведомления
     */
    public function ajax_get_notifications(): void {
        check_ajax_referer('atk_ved_nonce', 'nonce');
        
        $notifications = $this->get_notifications(10);
        $unread_count = $this->get_unread_count();
        
        wp_send_json_success(array(
            'notifications' => $notifications,
            'unread_count' => $unread_count,
        ));
    }
    
    /**
     * AJAX: Отметить как прочитанное
     */
    public function ajax_mark_read(): void {
        check_ajax_referer('atk_ved_nonce', 'nonce');
        
        $id = intval($_POST['id'] ?? 0);
        
        if ($this->mark_read($id)) {
            wp_send_json_success();
        }
        
        wp_send_json_error();
    }
    
    /**
     * AJAX: Отметить все как прочитанные
     */
    public function ajax_mark_all_read(): void {
        check_ajax_referer('atk_ved_nonce', 'nonce');
        
        if ($this->mark_all_read()) {
            wp_send_json_success();
        }
        
        wp_send_json_error();
    }
    
    /**
     * AJAX: Удалить уведомление
     */
    public function ajax_delete_notification(): void {
        check_ajax_referer('atk_ved_nonce', 'nonce');
        
        $id = intval($_POST['id'] ?? 0);
        
        if ($this->delete($id)) {
            wp_send_json_success();
        }
        
        wp_send_json_error();
    }
}

// Инициализация
$GLOBALS['atk_ved_notifications'] = new ATK_VED_Notifications();

/**
 * Helper функции
 */

/**
 * Создать уведомление
 */
function atk_ved_create_notification(string $title, string $message = '', string $type = 'info', string $link = ''): int {
    return $GLOBALS['atk_ved_notifications']->create($title, $message, $type, $link);
}

/**
 * Примеры использования:
 * 
 * atk_ved_create_notification('Новая заявка', 'Поступила новая заявка с сайта', 'info', admin_url('edit.php?post_type=lead'));
 * atk_ved_create_notification('Заказ оформлен', 'Заказ #123 ожидает обработки', 'success', admin_url('edit.php?post_type=shop_order'));
 * atk_ved_create_notification('Ошибка оплаты', 'Не удалось обработать платёж', 'error');
 */
