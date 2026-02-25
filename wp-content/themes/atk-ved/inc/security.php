<?php
/**
 * Безопасность
 */

// Скрытие версии WordPress
function atk_ved_remove_wp_version() {
    return '';
}
add_filter('the_generator', 'atk_ved_remove_wp_version');

// Отключение редактирования файлов из админки
if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}

// Защита от SQL инъекций
function atk_ved_sanitize_sql($input) {
    global $wpdb;
    return $wpdb->prepare('%s', $input);
}

// Ограничение попыток входа
function atk_ved_limit_login_attempts() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $attempts = get_transient('login_attempts_' . $ip);
    
    if ($attempts && $attempts >= 5) {
        wp_die('Слишком много попыток входа. Попробуйте через 15 минут.');
    }
}
add_action('wp_login_failed', function() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $attempts = get_transient('login_attempts_' . $ip) ?: 0;
    set_transient('login_attempts_' . $ip, $attempts + 1, 15 * MINUTE_IN_SECONDS);
});

add_action('wp_login', function() {
    $ip = $_SERVER['REMOTE_ADDR'];
    delete_transient('login_attempts_' . $ip);
});

// Защита от XSS
function atk_ved_sanitize_output($content) {
    return htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
}

// Отключение отображения ошибок входа
function atk_ved_login_errors() {
    return 'Неверные учетные данные';
}
add_filter('login_errors', 'atk_ved_login_errors');

// Изменение URL входа (опционально)
// Требует настройки .htaccess

// Защита wp-config.php
function atk_ved_protect_wp_config() {
    if (strpos($_SERVER['REQUEST_URI'], 'wp-config.php') !== false) {
        header('HTTP/1.0 403 Forbidden');
        exit;
    }
}
add_action('init', 'atk_ved_protect_wp_config');

// Отключение просмотра директорий
// Добавить в .htaccess: Options -Indexes

// Защита от hotlinking изображений
function atk_ved_prevent_hotlinking() {
    if (!is_admin()) {
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $site_url = home_url();
        
        if ($referer && strpos($referer, $site_url) === false) {
            // Проверка на изображения
            $request_uri = $_SERVER['REQUEST_URI'];
            if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $request_uri)) {
                header('HTTP/1.0 403 Forbidden');
                exit;
            }
        }
    }
}
// add_action('init', 'atk_ved_prevent_hotlinking');

// Добавление заголовков безопасности
function atk_ved_security_headers() {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
}
add_action('send_headers', 'atk_ved_security_headers');

// Отключение XML-RPC для предотвращения DDoS
add_filter('xmlrpc_enabled', '__return_false');

// Удаление информации о пользователях из REST API
function atk_ved_remove_user_info_rest() {
    if (!is_user_logged_in()) {
        return new WP_Error('rest_user_cannot_view', 'Доступ запрещен', array('status' => 403));
    }
}
add_filter('rest_authentication_errors', 'atk_ved_remove_user_info_rest');

// Защита от перебора паролей
function atk_ved_add_login_delay() {
    sleep(2); // Задержка 2 секунды
}
add_action('wp_login_failed', 'atk_ved_add_login_delay');

// Скрытие логина администратора
function atk_ved_hide_admin_login($login) {
    if ($login === 'admin') {
        return 'Пользователь';
    }
    return $login;
}
add_filter('the_author', 'atk_ved_hide_admin_login');

// Отключение отображения версий плагинов
function atk_ved_remove_plugin_versions() {
    global $wp_scripts, $wp_styles;
    
    if (!is_admin()) {
        foreach ($wp_scripts->registered as $handle => $script) {
            $wp_scripts->registered[$handle]->ver = null;
        }
        
        foreach ($wp_styles->registered as $handle => $style) {
            $wp_styles->registered[$handle]->ver = null;
        }
    }
}
add_action('wp_enqueue_scripts', 'atk_ved_remove_plugin_versions', 999);

// Защита от SQL инъекций в URL
function atk_ved_sanitize_url_params() {
    if (isset($_GET)) {
        foreach ($_GET as $key => $value) {
            if (is_string($value)) {
                $_GET[$key] = sanitize_text_field($value);
            }
        }
    }
}
add_action('init', 'atk_ved_sanitize_url_params');

// Блокировка подозрительных запросов
function atk_ved_block_suspicious_requests() {
    $request_uri = $_SERVER['REQUEST_URI'];
    
    $suspicious_patterns = array(
        '/eval\(/i',
        '/base64_decode/i',
        '/gzinflate/i',
        '/\.\.\//i',
        '/<script/i',
        '/union.*select/i',
        '/concat.*\(/i'
    );
    
    foreach ($suspicious_patterns as $pattern) {
        if (preg_match($pattern, $request_uri)) {
            header('HTTP/1.0 403 Forbidden');
            exit('Подозрительный запрос заблокирован');
        }
    }
}
add_action('init', 'atk_ved_block_suspicious_requests');

// Защита от CSRF
function atk_ved_add_csrf_token() {
    if (!session_id()) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
add_action('init', 'atk_ved_add_csrf_token');

function atk_ved_verify_csrf_token() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }
    
    return true;
}

// Логирование подозрительной активности
function atk_ved_log_security_event($event, $details = '') {
    $log_file = WP_CONTENT_DIR . '/security.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    
    $log_entry = sprintf(
        "[%s] IP: %s | Event: %s | Details: %s | User Agent: %s\n",
        $timestamp,
        $ip,
        $event,
        $details,
        $user_agent
    );
    
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

// Блокировка доступа к важным файлам
function atk_ved_protect_important_files() {
    $protected_files = array(
        'wp-config.php',
        '.htaccess',
        'error_log',
        'debug.log'
    );
    
    $request_uri = $_SERVER['REQUEST_URI'];
    
    foreach ($protected_files as $file) {
        if (strpos($request_uri, $file) !== false) {
            header('HTTP/1.0 403 Forbidden');
            atk_ved_log_security_event('Попытка доступа к защищенному файлу', $file);
            exit;
        }
    }
}
add_action('init', 'atk_ved_protect_important_files');

// Отключение отображения ошибок PHP на продакшене
if (!defined('WP_DEBUG') || !WP_DEBUG) {
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', WP_CONTENT_DIR . '/php-errors.log');
}

// Автоматический выход при неактивности
function atk_ved_auto_logout() {
    $timeout = 30 * MINUTE_IN_SECONDS; // 30 минут
    
    if (is_user_logged_in()) {
        $last_activity = get_user_meta(get_current_user_id(), 'last_activity', true);
        
        if ($last_activity && (time() - $last_activity) > $timeout) {
            wp_logout();
            wp_redirect(home_url());
            exit;
        }
        
        update_user_meta(get_current_user_id(), 'last_activity', time());
    }
}
add_action('init', 'atk_ved_auto_logout');
