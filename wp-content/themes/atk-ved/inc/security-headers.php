<?php
/**
 * Security Headers
 * Дополнительные заголовки безопасности через PHP
 *
 * @package ATK_VED
 */

defined('ABSPATH') || exit;

/**
 * Установка заголовков безопасности
 */
function atk_ved_set_security_headers() {
    // Только для фронтенда
    if (is_admin()) {
        return;
    }

    // X-Content-Type-Options
    if (!headers_sent()) {
        header('X-Content-Type-Options: nosniff');
        
        // X-Frame-Options
        header('X-Frame-Options: SAMEORIGIN');
        
        // X-XSS-Protection
        header('X-XSS-Protection: 1; mode=block');
        
        // Referrer-Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Permissions-Policy
        header('Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=(), usb=(), magnetometer=(), gyroscope=(), accelerometer=(), interest-cohort=()');
        
        // Удаление X-Powered-By
        header_remove('X-Powered-By');
        
        // HTTPS Strict Transport Security (только если используется HTTPS)
        if (is_ssl()) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        }
    }
}
add_action('send_headers', 'atk_ved_set_security_headers');

/**
 * Отключение XML-RPC (защита от DDoS и брутфорса)
 */
add_filter('xmlrpc_enabled', '__return_false');

/**
 * Защита от перебора паролей - задержка после неудачной попытки
 */
function atk_ved_login_failed_delay() {
    sleep(2); // Задержка 2 секунды
}
add_action('wp_login_failed', 'atk_ved_login_failed_delay');

/**
 * Скрытие ошибок входа
 */
function atk_ved_login_errors_custom() {
    return __('Неверные учетные данные.', 'atk-ved');
}
add_filter('login_errors', 'atk_ved_login_errors_custom');

/**
 * Блокировка доступа к wp-config.php
 */
function atk_ved_block_wp_config_access() {
    // Временно отключено для отладки
    return;
    
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    if (stripos($request_uri, 'wp-config.php') !== false) {
        wp_die(__('Доступ запрещен.', 'atk-ved'), 403);
    }
}
add_action('init', 'atk_ved_block_wp_config_access');

/**
 * Защита от SQL инъекций в URL
 */
function atk_ved_block_sql_injection() {
    // Временно отключено для отладки
    return;
    
    $query_string = $_SERVER['QUERY_STRING'] ?? '';
    
    $patterns = [
        '/union.*select/i',
        '/concat.*\(/i',
        '/base64_/i',
        '/benchmark.*\(/i',
        '/sleep.*\(/i',
        '/load_file.*\(/i',
        '/into.*outfile/i',
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $query_string)) {
            atk_ved_log_security_event('sql_injection_attempt', "Query: {$query_string}");
            wp_die(__('Подозрительная активность обнаружена.', 'atk-ved'), 403);
        }
    }
}
add_action('init', 'atk_ved_block_sql_injection');

/**
 * Защита от XSS в GET параметрах
 */
function atk_ved_sanitize_get_params() {
    // Временно отключено для отладки
    return;
    
    if (!empty($_GET)) {
        foreach ($_GET as $key => $value) {
            if (is_string($value)) {
                // Проверка на подозрительные паттерны
                if (preg_match('/<script|javascript:|onerror=|onload=/i', $value)) {
                    atk_ved_log_security_event('xss_attempt', "Param: {$key}, Value: {$value}");
                    unset($_GET[$key]);
                }
            }
        }
    }
}
add_action('init', 'atk_ved_sanitize_get_params', 1);

/**
 * Ограничение размера загружаемых файлов
 */
function atk_ved_limit_upload_size($file) {
    $max_size = 10 * 1024 * 1024; // 10 MB
    
    if ($file['size'] > $max_size) {
        $file['error'] = sprintf(
            __('Файл слишком большой. Максимальный размер: %s', 'atk-ved'),
            size_format($max_size)
        );
    }
    
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'atk_ved_limit_upload_size');

/**
 * Разрешенные типы файлов для загрузки
 */
function atk_ved_allowed_upload_mimes($mimes) {
    // Удаляем потенциально опасные типы
    unset($mimes['exe']);
    unset($mimes['com']);
    unset($mimes['bat']);
    unset($mimes['cmd']);
    unset($mimes['pif']);
    unset($mimes['scr']);
    unset($mimes['vbs']);
    unset($mimes['js']);
    
    // Добавляем безопасные типы
    $mimes['svg'] = 'image/svg+xml';
    $mimes['webp'] = 'image/webp';
    
    return $mimes;
}
add_filter('upload_mimes', 'atk_ved_allowed_upload_mimes');

/**
 * Проверка загружаемых SVG файлов
 */
function atk_ved_check_svg_upload($file) {
    if ($file['type'] === 'image/svg+xml') {
        $svg_content = file_get_contents($file['tmp_name']);
        
        // Проверка на подозрительный контент
        $dangerous_patterns = [
            '/<script/i',
            '/javascript:/i',
            '/onerror=/i',
            '/onload=/i',
            '/<iframe/i',
            '/<embed/i',
            '/<object/i',
        ];
        
        foreach ($dangerous_patterns as $pattern) {
            if (preg_match($pattern, $svg_content)) {
                $file['error'] = __('SVG файл содержит потенциально опасный код.', 'atk-ved');
                atk_ved_log_security_event('malicious_svg_upload', "File: {$file['name']}");
                break;
            }
        }
    }
    
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'atk_ved_check_svg_upload');

/**
 * Защита от Directory Traversal
 */
function atk_ved_block_directory_traversal() {
    // Временно отключено для отладки
    return;
    
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    
    if (preg_match('/\.\.\/|\.\.\\\\/', $request_uri)) {
        atk_ved_log_security_event('directory_traversal_attempt', "URI: {$request_uri}");
        wp_die(__('Доступ запрещен.', 'atk-ved'), 403);
    }
}
add_action('init', 'atk_ved_block_directory_traversal');

/**
 * Установка безопасных cookie параметров
 */
function atk_ved_secure_cookies($cookie_elements) {
    $cookie_elements['secure'] = is_ssl();
    $cookie_elements['httponly'] = true;
    $cookie_elements['samesite'] = 'Strict';
    
    return $cookie_elements;
}
add_filter('wp_cookie_constants', 'atk_ved_secure_cookies');

/**
 * Логирование подозрительной активности
 */
function atk_ved_log_suspicious_activity() {
    // Временно отключено для отладки
    return;
    
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    
    // Подозрительные паттерны в User-Agent
    $suspicious_agents = [
        'nikto',
        'sqlmap',
        'nmap',
        'masscan',
        'metasploit',
        'havij',
        'acunetix',
    ];
    
    foreach ($suspicious_agents as $agent) {
        if (stripos($user_agent, $agent) !== false) {
            atk_ved_log_security_event('suspicious_user_agent', "Agent: {$user_agent}, URI: {$request_uri}");
            wp_die(__('Доступ запрещен.', 'atk-ved'), 403);
        }
    }
}
add_action('init', 'atk_ved_log_suspicious_activity');
