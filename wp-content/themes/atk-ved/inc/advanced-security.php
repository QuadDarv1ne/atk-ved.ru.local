<?php
/**
 * Advanced Security System
 * Расширенная система безопасности
 * 
 * @package ATK_VED
 * @since 2.9.1
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Класс для расширенной безопасности
 */
class ATK_VED_Advanced_Security {
    
    /**
     * Инициализация системы безопасности
     */
    public static function init(): void {
        // Базовые меры безопасности
        self::implement_basic_security();
        
        // Расширенные меры
        self::implement_advanced_security();
        
        // AJAX обработчики
        add_action('wp_ajax_atk_ved_security_scan', array(__CLASS__, 'ajax_security_scan'));
        add_action('wp_ajax_atk_ved_security_report', array(__CLASS__, 'ajax_security_report'));
        add_action('wp_ajax_atk_ved_security_fix', array(__CLASS__, 'ajax_security_fix'));
        
        // Планирование регулярных проверок
        if (!wp_next_scheduled('atk_ved_daily_security_check')) {
            wp_schedule_event(time(), 'daily', 'atk_ved_daily_security_check');
        }
        add_action('atk_ved_daily_security_check', array(__CLASS__, 'scheduled_security_check'));
    }
    
    /**
     * Реализация базовых мер безопасности
     */
    private static function implement_basic_security(): void {
        // Скрытие версии WordPress
        remove_action('wp_head', 'wp_generator');
        add_filter('the_generator', '__return_empty_string');
        
        // Отключение редактирования файлов
        if (!defined('DISALLOW_FILE_EDIT')) {
            define('DISALLOW_FILE_EDIT', true);
        }
        
        // Защита wp-config.php
        add_action('init', function() {
            if (strpos($_SERVER['REQUEST_URI'] ?? '', 'wp-config.php') !== false) {
                status_header(403);
                wp_die('Access forbidden', 'Forbidden', array('response' => 403));
            }
        });
        
        // Отключение XML-RPC
        add_filter('xmlrpc_enabled', '__return_false');
        
        // Скрытие информации о пользователях в REST API
        add_filter('rest_authentication_errors', function($result) {
            if (!is_user_logged_in() && !is_wp_error($result)) {
                return new WP_Error('rest_forbidden', 'REST API access restricted', array('status' => 403));
            }
            return $result;
        });
    }
    
    /**
     * Реализация расширенных мер безопасности
     */
    private static function implement_advanced_security(): void {
        // Защита от брутфорса
        add_action('wp_login_failed', array(__CLASS__, 'handle_login_failure'));
        add_action('wp_login', array(__CLASS__, 'handle_successful_login'));
        
        // Защита от SQL инъекций
        add_filter('query', array(__CLASS__, 'sanitize_sql_queries'));
        
        // Защита от XSS
        add_filter('wp_kses_allowed_html', array(__CLASS__, 'restrict_allowed_html'), 10, 2);
        
        // Защита заголовков
        add_action('send_headers', array(__CLASS__, 'add_security_headers'));
        
        // Защита от hotlinking
        add_action('init', array(__CLASS__, 'prevent_hotlinking'));
        
        // Мониторинг подозрительной активности
        add_action('init', array(__CLASS__, 'monitor_suspicious_activity'));
    }
    
    /**
     * Обработка неудачной попытки входа
     */
    public static function handle_login_failure(string $username): void {
        $ip = self::get_client_ip();
        $attempts = get_transient("login_attempts_{$ip}") ?: 0;
        
        // Увеличиваем счетчик попыток
        $attempts++;
        set_transient("login_attempts_{$ip}", $attempts, 15 * MINUTE_IN_SECONDS);
        
        // Блокировка при превышении лимита
        if ($attempts >= 5) {
            self::log_security_event('Brute force attempt blocked', array(
                'ip' => $ip,
                'username' => $username,
                'attempts' => $attempts
            ));
            
            // Добавляем IP в список заблокированных
            $blocked_ips = get_option('atk_blocked_ips', array());
            $blocked_ips[$ip] = time() + (24 * HOUR_IN_SECONDS); // Блокировка на 24 часа
            update_option('atk_blocked_ips', $blocked_ips);
        }
    }
    
    /**
     * Обработка успешного входа
     */
    public static function handle_successful_login(string $user_login): void {
        $ip = self::get_client_ip();
        
        // Очищаем счетчик попыток
        delete_transient("login_attempts_{$ip}");
        
        // Логируем успешный вход
        self::log_security_event('Successful login', array(
            'username' => $user_login,
            'ip' => $ip
        ));
    }
    
    /**
     * Санитизация SQL запросов
     */
    public static function sanitize_sql_queries(string $query): string {
        // Проверка на опасные паттерны
        $dangerous_patterns = array(
            '/\b(UNION|SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER)\b/i',
            '/(\.\.\/)+/',
            '/(;|--|#)/'
        );
        
        foreach ($dangerous_patterns as $pattern) {
            if (preg_match($pattern, $query)) {
                self::log_security_event('Suspicious SQL query detected', array(
                    'query' => $query,
                    'pattern' => $pattern
                ));
                
                // Возвращаем безопасный запрос
                return "SELECT 1";
            }
        }
        
        return $query;
    }
    
    /**
     * Ограничение разрешенного HTML
     */
    public static function restrict_allowed_html(array $tags, string $context): array {
        if ($context === 'post') {
            // Удаляем потенциально опасные теги
            unset($tags['script'], $tags['iframe'], $tags['object'], $tags['embed']);
            
            // Ограничиваем атрибуты
            if (isset($tags['a'])) {
                $tags['a'] = array('href' => true, 'title' => true);
            }
        }
        
        return $tags;
    }
    
    /**
     * Добавление заголовков безопасности
     */
    public static function add_security_headers(): void {
        $headers = array(
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains; preload'
        );
        
        foreach ($headers as $header => $value) {
            header("{$header}: {$value}");
        }
    }
    
    /**
     * Предотвращение hotlinking
     */
    public static function prevent_hotlinking(): void {
        if (!is_admin() && isset($_SERVER['HTTP_REFERER'])) {
            $referer = $_SERVER['HTTP_REFERER'];
            $site_url = home_url();
            
            // Проверяем, что запрос идет не с нашего сайта
            if (strpos($referer, $site_url) === false) {
                $request_uri = $_SERVER['REQUEST_URI'] ?? '';
                
                // Проверка на изображения
                if (preg_match('/\.(jpg|jpeg|png|gif|webp|pdf|zip)$/i', $request_uri)) {
                    status_header(403);
                    wp_die('Direct access forbidden', 'Forbidden', array('response' => 403));
                }
            }
        }
    }
    
    /**
     * Мониторинг подозрительной активности
     */
    public static function monitor_suspicious_activity(): void {
        // Проверка заблокированных IP
        $blocked_ips = get_option('atk_blocked_ips', array());
        $current_ip = self::get_client_ip();
        
        if (isset($blocked_ips[$current_ip]) && $blocked_ips[$current_ip] > time()) {
            status_header(403);
            wp_die('Your IP has been temporarily blocked due to security concerns', 'Access Denied', array('response' => 403));
        }
        
        // Очистка устаревших записей
        foreach ($blocked_ips as $ip => $expire_time) {
            if ($expire_time < time()) {
                unset($blocked_ips[$ip]);
            }
        }
        update_option('atk_blocked_ips', $blocked_ips);
    }
    
    /**
     * AJAX сканирование безопасности
     */
    public static function ajax_security_scan(): void {
        check_ajax_referer('atk_ved_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $scan_results = self::perform_security_scan();
        wp_send_json_success($scan_results);
    }
    
    /**
     * AJAX отчет по безопасности
     */
    public static function ajax_security_report(): void {
        check_ajax_referer('atk_ved_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $report = self::generate_security_report();
        wp_send_json_success($report);
    }
    
    /**
     * AJAX исправление проблем безопасности
     */
    public static function ajax_security_fix(): void {
        check_ajax_referer('atk_ved_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $fix_type = sanitize_text_field($_POST['fix_type'] ?? '');
        $result = self::apply_security_fix($fix_type);
        
        wp_send_json_success($result);
    }
    
    /**
     * Планирование регулярной проверки безопасности
     */
    public static function scheduled_security_check(): void {
        $scan_results = self::perform_security_scan();
        
        // Отправка уведомления при обнаружении проблем
        if (!empty($scan_results['issues'])) {
            $admin_email = get_option('admin_email');
            $subject = '[' . get_bloginfo('name') . '] Security Alert';
            $message = "Security issues detected:\n\n";
            
            foreach ($scan_results['issues'] as $issue) {
                $message .= "- {$issue['title']}: {$issue['description']}\n";
            }
            
            wp_mail($admin_email, $subject, $message);
        }
        
        // Логирование
        self::log_security_event('Scheduled security check completed', array(
            'issues_found' => count($scan_results['issues'] ?? [])
        ));
    }
    
    /**
     * Выполнение сканирования безопасности
     */
    public static function perform_security_scan(): array {
        $issues = array();
        $checks = array();
        
        // Проверка базовых настроек
        $checks['wp_version'] = array(
            'title' => 'WordPress Version',
            'status' => self::check_wp_version(),
            'description' => 'Check if WordPress version is hidden'
        );
        
        $checks['file_permissions'] = array(
            'title' => 'File Permissions',
            'status' => self::check_file_permissions(),
            'description' => 'Check file and directory permissions'
        );
        
        $checks['admin_user'] = array(
            'title' => 'Admin User',
            'status' => self::check_admin_user(),
            'description' => 'Check for default admin username'
        );
        
        $checks['debug_mode'] = array(
            'title' => 'Debug Mode',
            'status' => self::check_debug_mode(),
            'description' => 'Check if debug mode is disabled in production'
        );
        
        $checks['file_edit'] = array(
            'title' => 'File Editing',
            'status' => self::check_file_editing(),
            'description' => 'Check if file editing is disabled'
        );
        
        // Сбор проблем
        foreach ($checks as $check_key => $check) {
            if ($check['status'] === 'warning' || $check['status'] === 'error') {
                $issues[] = array(
                    'type' => $check['status'],
                    'title' => $check['title'],
                    'description' => $check['description'],
                    'check_key' => $check_key
                );
            }
        }
        
        return array(
            'status' => empty($issues) ? 'secure' : 'issues_found',
            'issues' => $issues,
            'checks' => $checks,
            'scan_time' => current_time('mysql')
        );
    }
    
    /**
     * Генерация отчета по безопасности
     */
    public static function generate_security_report(): array {
        $scan_results = self::perform_security_scan();
        $recent_events = self::get_recent_security_events(50);
        
        return array(
            'scan_results' => $scan_results,
            'recent_events' => $recent_events,
            'blocked_ips' => get_option('atk_blocked_ips', array()),
            'security_score' => self::calculate_security_score($scan_results),
            'report_time' => current_time('mysql')
        );
    }
    
    /**
     * Применение исправлений безопасности
     */
    public static function apply_security_fix(string $fix_type): array {
        $result = array('status' => 'error', 'message' => 'Unknown fix type');
        
        switch ($fix_type) {
            case 'hide_wp_version':
                remove_action('wp_head', 'wp_generator');
                add_filter('the_generator', '__return_empty_string');
                $result = array('status' => 'success', 'message' => 'WordPress version hidden');
                break;
                
            case 'disable_file_edit':
                if (!defined('DISALLOW_FILE_EDIT')) {
                    // Для уже работающего сайта нужно добавить в wp-config.php
                    $result = array('status' => 'manual', 'message' => 'Add define(\'DISALLOW_FILE_EDIT\', true); to wp-config.php');
                } else {
                    $result = array('status' => 'success', 'message' => 'File editing already disabled');
                }
                break;
                
            case 'secure_admin_user':
                // Проверка существования пользователя 'admin'
                $admin_user = get_user_by('login', 'admin');
                if ($admin_user) {
                    $result = array('status' => 'manual', 'message' => 'Admin user found. Please rename manually for security.');
                } else {
                    $result = array('status' => 'success', 'message' => 'No default admin user found');
                }
                break;
        }
        
        return $result;
    }
    
    // Вспомогательные методы проверок
    private static function check_wp_version(): string {
        return has_action('wp_head', 'wp_generator') ? 'warning' : 'ok';
    }
    
    private static function check_file_permissions(): string {
        $uploads_dir = wp_upload_dir();
        $perms = fileperms($uploads_dir['basedir']);
        return ($perms & 0007) ? 'warning' : 'ok';
    }
    
    private static function check_admin_user(): string {
        return get_user_by('login', 'admin') ? 'warning' : 'ok';
    }
    
    private static function check_debug_mode(): string {
        return (defined('WP_DEBUG') && WP_DEBUG && !defined('WP_DEBUG_DISPLAY')) ? 'warning' : 'ok';
    }
    
    private static function check_file_editing(): string {
        return defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT ? 'ok' : 'warning';
    }
    
    /**
     * Получение IP клиента
     */
    private static function get_client_ip(): string {
        $ip_keys = array('HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR');
        
        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Логирование событий безопасности
     */
    public static function log_security_event(string $message, array $data = array()): void {
        $log_entry = array(
            'timestamp' => current_time('mysql'),
            'message' => $message,
            'data' => $data,
            'ip' => self::get_client_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        );
        
        $security_log = get_option('atk_security_log', array());
        
        // Ограничение размера лога
        if (count($security_log) > 1000) {
            array_shift($security_log);
        }
        
        $security_log[] = $log_entry;
        update_option('atk_security_log', $security_log, false);
    }
    
    /**
     * Получение последних событий безопасности
     */
    public static function get_recent_security_events(int $limit = 50): array {
        $log = get_option('atk_security_log', array());
        return array_slice($log, -$limit);
    }
    
    /**
     * Расчет оценки безопасности
     */
    private static function calculate_security_score(array $scan_results): int {
        $total_checks = count($scan_results['checks']);
        $issues_count = count($scan_results['issues']);
        
        if ($total_checks === 0) return 100;
        
        $score = 100 - (($issues_count / $total_checks) * 100);
        return max(0, (int) $score);
    }
}

// Инициализация
ATK_VED_Advanced_Security::init();