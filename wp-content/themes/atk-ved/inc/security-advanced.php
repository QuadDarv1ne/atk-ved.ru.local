<?php
/**
 * Security Features: 2FA, Audit Logging
 * 
 * @package ATK_VED
 * @since 2.6.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/* ==========================================================================
   TWO-FACTOR AUTHENTICATION (2FA)
   ========================================================================== */

/**
 * Класс для работы с 2FA
 */
class ATK_VED_2FA {
    
    /**
     * Генерация секрета для TOTP
     */
    public static function generate_secret(): string {
        $secret = '';
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        
        for ($i = 0; $i < 32; $i++) {
            $secret .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        return $secret;
    }
    
    /**
     * Проверка TOTP кода
     */
    public static function verify_code(string $secret, string $code): bool {
        if (empty($secret) || empty($code)) {
            return false;
        }
        
        // Текущее время окно (30 секунд)
        $time_window = floor(time() / 30);
        
        // Проверяем текущее и соседние окна (для учёта рассинхронизации времени)
        for ($i = -1; $i <= 1; $i++) {
            $calc_code = self::generate_totp_code($secret, $time_window + $i);
            if (hash_equals($calc_code, $code)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Генерация TOTP кода
     */
    private static function generate_totp_code(string $secret, int $time_window): string {
        // Base32 decode
        $secret_binary = self::base32_decode($secret);
        
        // Pack time into binary
        $time_packed = pack('N', 0) . pack('N', $time_window);
        
        // HMAC-SHA1
        $hash = hash_hmac('sha1', $time_packed, $secret_binary, true);
        
        // Dynamic truncation
        $offset = ord($hash[19]) & 0x0F;
        $code = unpack('N', substr($hash, $offset, 4));
        $code = ($code[1] & 0x7FFFFFFF) % 1000000;
        
        return str_pad((string) $code, 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Base32 decode
     */
    private static function base32_decode(string $secret): string {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $binary = '';
        
        $secret = strtoupper(str_replace('=', '', $secret));
        
        for ($i = 0; $i < strlen($secret); $i++) {
            $pos = strpos($chars, $secret[$i]);
            if ($pos !== false) {
                $binary .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
            }
        }
        
        $length = strlen($binary);
        $hex = '';
        
        for ($i = 0; $i <= $length - 8; $i += 8) {
            $hex .= chr(bindec(substr($binary, $i, 8)));
        }
        
        return $hex;
    }
    
    /**
     * Генерация QR кода URL
     */
    public static function get_qr_code_url(string $secret, string $account_name, string $issuer): string {
        $url = 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=';
        $otpauth = urlencode(sprintf('otpauth://totp/%s:%s?secret=%s&issuer=%s', 
            urlencode($issuer),
            urlencode($account_name),
            $secret,
            urlencode($issuer)
        ));
        
        return $url . $otpauth;
    }
}

/**
 * 2FA для админов
 */
class ATK_VED_2FA_Admin {
    
    public function __construct() {
        add_action('wp_login', array($this, 'check_2fa'), 10, 2);
        add_action('admin_init', array($this, 'verify_2fa_code'));
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_ajax_atk_ved_2fa_verify', array($this, 'ajax_verify'));
        add_action('wp_ajax_atk_ved_2fa_enable', array($this, 'ajax_enable'));
        add_action('wp_ajax_atk_ved_2fa_disable', array($this, 'ajax_disable'));
    }
    
    /**
     * Проверка 2FA при входе
     */
    public function check_2fa(string $user_login, WP_User $user): void {
        if (!user_can($user, 'manage_options')) {
            return;
        }
        
        $secret = get_user_meta($user->ID, 'atk_ved_2fa_secret', true);
        
        if ($secret) {
            set_transient('atk_ved_2fa_pending_user', $user->ID, 300);
            wp_redirect(admin_url('admin.php?page=atk-ved-2fa-verify'));
            exit;
        }
    }
    
    /**
     * Верификация кода 2FA
     */
    public function verify_2fa_code(): void {
        $page = $GLOBALS['pagenow'];
        
        if ($page !== 'admin.php' || !isset($_GET['page']) || $_GET['page'] !== 'atk-ved-2fa-verify') {
            return;
        }
        
        $user_id = get_transient('atk_ved_2fa_pending_user');
        
        if (!$user_id) {
            return;
        }
        
        $user = get_user_by('ID', $user_id);
        
        if (!$user) {
            return;
        }
        
        $secret = get_user_meta($user_id, 'atk_ved_2fa_secret', true);
        
        if (empty($secret)) {
            delete_transient('atk_ved_2fa_pending_user');
            return;
        }
        
        if (isset($_POST['atk_ved_2fa_code'])) {
            $code = sanitize_text_field($_POST['atk_ved_2fa_code']);
            
            if (ATK_VED_2FA::verify_code($secret, $code)) {
                delete_transient('atk_ved_2fa_pending_user');
                wp_set_auth_cookie($user_id);
                do_action('wp_login', $user->user_login, $user);
                wp_redirect(admin_url());
                exit;
            } else {
                add_action('admin_notices', function() {
                    echo '<div class="notice notice-error"><p>' . __('Неверный код 2FA', 'atk-ved') . '</p></div>';
                });
            }
        }
    }
    
    /**
     * Страница верификации 2FA
     */
    public function verify_page(): void {
        ?>
        <div class="wrap">
            <h1><?php _e('Двухфакторная аутентификация', 'atk-ved'); ?></h1>
            <form method="post">
                <p><?php _e('Введите код из вашего приложения аутентификации:', 'atk-ved'); ?></p>
                <input type="text" name="atk_ved_2fa_code" maxlength="6" pattern="[0-9]{6}" required autofocus>
                <?php submit_button(__('Проверить', 'atk-ved')); ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Страница настроек 2FA
     */
    public function settings_page(): void {
        $user_id = get_current_user_id();
        $secret = get_user_meta($user_id, 'atk_ved_2fa_secret', true);
        $enabled = !empty($secret);
        ?>
        <div class="wrap">
            <h1><?php _e('Настройки 2FA', 'atk-ved'); ?></h1>
            
            <?php if (!$enabled): ?>
                <div class="card" style="max-width: 500px;">
                    <h2><?php _e('Включить 2FA', 'atk-ved'); ?></h2>
                    <p><?php _e('1. Установите приложение аутентификации (Google Authenticator, Authy)', 'atk-ved'); ?></p>
                    <p><?php _e('2. Отсканируйте QR код:', 'atk-ved'); ?></p>
                    <p>
                        <img src="<?php echo ATK_VED_2FA::get_qr_code_url(
                            get_user_meta($user_id, 'atk_ved_2fa_temp_secret', true) ?: ATK_VED_2FA::generate_secret(),
                            wp_get_current_user()->user_email,
                            'ATK VED'
                        ); ?>" alt="QR Code">
                    </p>
                    <p><?php _e('Или введите ключ вручную:', 'atk-ved'); ?></p>
                    <p><code><?php echo get_user_meta($user_id, 'atk_ved_2fa_temp_secret', true) ?: ATK_VED_2FA::generate_secret(); ?></code></p>
                    <button type="button" class="button button-primary" id="enable2FA">
                        <?php _e('Включить 2FA', 'atk-ved'); ?>
                    </button>
                </div>
            <?php else: ?>
                <div class="card" style="max-width: 500px;">
                    <h2><?php _e('2FA включена', 'atk-ved'); ?></h2>
                    <p class="success"><?php _e('Двухфакторная аутентификация активна', 'atk-ved'); ?></p>
                    <button type="button" class="button button-danger" id="disable2FA">
                        <?php _e('Отключить 2FA', 'atk-ved'); ?>
                    </button>
                </div>
            <?php endif; ?>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#enable2FA').on('click', function() {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'atk_ved_2fa_enable',
                        nonce: '<?php echo wp_create_nonce('atk_ved_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert(response.data.message);
                        }
                    }
                });
            });
            
            $('#disable2FA').on('click', function() {
                if (confirm('<?php _e('Вы уверены?', 'atk-ved'); ?>')) {
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'atk_ved_2fa_disable',
                            nonce: '<?php echo wp_create_nonce('atk_ved_nonce'); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            }
                        }
                    });
                }
            });
        });
        </script>
        <?php
    }
    
    /**
     * AJAX включение 2FA
     */
    public function ajax_enable(): void {
        check_ajax_referer('atk_ved_nonce', 'nonce');
        
        $user_id = get_current_user_id();
        $secret = ATK_VED_2FA::generate_secret();
        
        update_user_meta($user_id, 'atk_ved_2fa_temp_secret', $secret);
        
        wp_send_json_success(array('message' => __('2FA включена', 'atk-ved')));
    }
    
    /**
     * AJAX отключение 2FA
     */
    public function ajax_disable(): void {
        check_ajax_referer('atk_ved_nonce', 'nonce');
        
        $user_id = get_current_user_id();
        
        delete_user_meta($user_id, 'atk_ved_2fa_secret');
        delete_user_meta($user_id, 'atk_ved_2fa_temp_secret');
        
        wp_send_json_success(array('message' => __('2FA отключена', 'atk-ved')));
    }
    
    /**
     * AJAX верификация
     */
    public function ajax_verify(): void {
        check_ajax_referer('atk_ved_nonce', 'nonce');
        
        $user_id = get_current_user_id();
        $secret = get_user_meta($user_id, 'atk_ved_2fa_secret', true);
        $code = sanitize_text_field($_POST['code'] ?? '');
        
        if (ATK_VED_2FA::verify_code($secret, $code)) {
            wp_send_json_success(array('message' => __('Код верен', 'atk-ved')));
        } else {
            wp_send_json_error(array('message' => __('Неверный код', 'atk-ved')));
        }
    }
    
    /**
     * Добавление страницы настроек
     */
    public function add_settings_page(): void {
        add_submenu_page(
            'options-general.php',
            __('2FA Настройки', 'atk-ved'),
            __('2FA', 'atk-ved'),
            'manage_options',
            'atk-ved-2fa',
            array($this, 'settings_page')
        );
        
        add_submenu_page(
            null,
            __('2FA Верификация', 'atk-ved'),
            '',
            'read',
            'atk-ved-2fa-verify',
            array($this, 'verify_page')
        );
    }
    
    /**
     * Регистрация настроек
     */
    public function register_settings(): void {
        // Настройки регистрируются в user_meta
    }
}

/* ==========================================================================
   AUDIT LOGGING
   ========================================================================== */

/**
 * Класс для аудита действий
 */
class ATK_VED_Audit_Log {
    
    private string $table_name;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'atk_ved_audit_log';
        
        add_action('wp_login', array($this, 'log_login'), 10, 2);
        add_action('wp_logout', array($this, 'log_logout'));
        add_action('wp_insert_post', array($this, 'log_post_update'), 10, 3);
        add_action('deleted_post', array($this, 'log_post_delete'));
        add_action('wp_update_user', array($this, 'log_user_update'));
        add_action('admin_init', array($this, 'log_admin_action'));
        
        $this->create_table();
    }
    
    /**
     * Создание таблицы
     */
    private function create_table(): void {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT 0,
            user_login varchar(60) DEFAULT '',
            action varchar(100) NOT NULL,
            object_type varchar(50) DEFAULT '',
            object_id bigint(20) DEFAULT 0,
            data text,
            ip_address varchar(45) DEFAULT '',
            user_agent text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY action (action),
            KEY created_at (created_at)
        ) {$charset_collate};";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Логирование действия
     */
    public function log(string $action, array $data = array()): void {
        global $wpdb;
        
        $user_id = get_current_user_id();
        $user = $user_id ? wp_get_current_user() : null;
        
        $wpdb->insert($this->table_name, array(
            'user_id' => $user_id,
            'user_login' => $user ? $user->user_login : 'Guest',
            'action' => sanitize_text_field($action),
            'object_type' => sanitize_text_field($data['object_type'] ?? ''),
            'object_id' => intval($data['object_id'] ?? 0),
            'data' => wp_json_encode($data),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ));
    }
    
    /**
     * Лог входа
     */
    public function log_login(string $user_login, WP_User $user): void {
        $this->log('user_login', array(
            'user_id' => $user->ID,
            'roles' => $user->roles,
        ));
    }
    
    /**
     * Лог выхода
     */
    public function log_logout(): void {
        $this->log('user_logout');
    }
    
    /**
     * Лог создания/обновления поста
     */
    public function log_post_update(int $post_id, WP_Post $post, bool $update): void {
        $action = $update ? 'post_updated' : 'post_created';
        
        $this->log($action, array(
            'object_type' => $post->post_type,
            'object_id' => $post_id,
            'title' => $post->post_title,
        ));
    }
    
    /**
     * Лог удаления поста
     */
    public function log_post_delete(int $post_id): void {
        $this->log('post_deleted', array(
            'object_id' => $post_id,
        ));
    }
    
    /**
     * Лог обновления пользователя
     */
    public function log_user_update(int $user_id): void {
        $this->log('user_updated', array(
            'object_type' => 'user',
            'object_id' => $user_id,
        ));
    }
    
    /**
     * Лог действий в админке
     */
    public function log_admin_action(): void {
        // Логирование изменений настроек
        if (isset($_POST['option_page'])) {
            $this->log('settings_updated', array(
                'option_page' => sanitize_text_field($_POST['option_page']),
            ));
        }
    }
    
    /**
     * Получение логов
     */
    public function get_logs(array $args = array()): array {
        global $wpdb;
        
        $defaults = array(
            'limit' => 100,
            'offset' => 0,
            'user_id' => null,
            'action' => null,
            'date_from' => null,
            'date_to' => null,
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = array('1=1');
        
        if ($args['user_id']) {
            $where[] = $wpdb->prepare('user_id = %d', $args['user_id']);
        }
        
        if ($args['action']) {
            $where[] = $wpdb->prepare('action = %s', $args['action']);
        }
        
        if ($args['date_from']) {
            $where[] = $wpdb->prepare('created_at >= %s', $args['date_from']);
        }
        
        if ($args['date_to']) {
            $where[] = $wpdb->prepare('created_at <= %s', $args['date_to']);
        }
        
        $where_clause = implode(' AND ', $where);
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE {$where_clause} ORDER BY created_at DESC LIMIT %d OFFSET %d",
            $args['limit'],
            $args['offset']
        ));
    }
    
    /**
     * Страница просмотра логов
     */
    public function logs_page(): void {
        $logs = $this->get_logs(array('limit' => 50));
        ?>
        <div class="wrap">
            <h1><?php _e('Audit Log', 'atk-ved'); ?></h1>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Время', 'atk-ved'); ?></th>
                        <th><?php _e('Пользователь', 'atk-ved'); ?></th>
                        <th><?php _e('Действие', 'atk-ved'); ?></th>
                        <th><?php _e('Объект', 'atk-ved'); ?></th>
                        <th><?php _e('IP', 'atk-ved'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?php echo $log->created_at; ?></td>
                        <td><?php echo esc_html($log->user_login); ?></td>
                        <td><?php echo esc_html($log->action); ?></td>
                        <td><?php echo esc_html($log->object_type . ' #' . $log->object_id); ?></td>
                        <td><?php echo esc_html($log->ip_address); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}

// Инициализация
new ATK_VED_2FA_Admin();
new ATK_VED_Audit_Log();
