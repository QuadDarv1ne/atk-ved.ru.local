<?php
/**
 * Health Check & System Monitoring
 * Проверка состояния системы и мониторинг
 * 
 * @package ATK_VED
 * @since 2.8.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Регистрация REST API endpoint для health check
 */
function atk_ved_register_health_check_route(): void {
    register_rest_route('atk-ved/v1', '/health', array(
        'methods' => 'GET',
        'callback' => 'atk_ved_health_check',
        'permission_callback' => '__return_true',
    ));
    
    register_rest_route('atk-ved/v1', '/status', array(
        'methods' => 'GET',
        'callback' => 'atk_ved_system_status',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        },
    ));
}
add_action('rest_api_init', 'atk_ved_register_health_check_route');

/**
 * Health Check endpoint
 * 
 * @return WP_REST_Response
 */
function atk_ved_health_check(): WP_REST_Response {
    global $wpdb;
    
    $checks = array(
        'status' => 'healthy',
        'timestamp' => current_time('mysql'),
        'version' => array(
            'theme' => wp_get_theme()->get('Version'),
            'wordpress' => get_bloginfo('version'),
            'php' => phpversion(),
        ),
        'checks' => array(
            'database' => true,
            'disk_space' => true,
            'memory' => true,
            'wp_config' => true,
        ),
    );
    
    // Проверка базы данных
    try {
        $wpdb->get_var('SELECT 1');
    } catch (Exception $e) {
        $checks['checks']['database'] = false;
        $checks['status'] = 'unhealthy';
    }
    
    // Проверка дискового пространства
    $disk_free = disk_free_space(ABSPATH);
    $disk_total = disk_total_space(ABSPATH);
    $disk_percent = ($disk_free / $disk_total) * 100;
    
    if ($disk_percent < 10) {
        $checks['checks']['disk_space'] = false;
        $checks['status'] = 'warning';
    }
    
    $checks['disk'] = array(
        'free' => size_format($disk_free),
        'total' => size_format($disk_total),
        'percent' => round($disk_percent, 2),
    );
    
    // Проверка памяти
    $memory_limit = wp_convert_hr_to_bytes(WP_MEMORY_LIMIT);
    $memory_used = memory_get_usage(true);
    $memory_percent = ($memory_used / $memory_limit) * 100;
    
    if ($memory_percent > 90) {
        $checks['checks']['memory'] = false;
        $checks['status'] = 'warning';
    }
    
    $checks['memory'] = array(
        'limit' => size_format($memory_limit),
        'used' => size_format($memory_used),
        'percent' => round($memory_percent, 2),
    );
    
    // Проверка WP_CONFIG
    if (!defined('ABSPATH')) {
        $checks['checks']['wp_config'] = false;
        $checks['status'] = 'unhealthy';
    }
    
    $http_code = 200;
    if ($checks['status'] === 'unhealthy') {
        $http_code = 503;
    } elseif ($checks['status'] === 'warning') {
        $http_code = 200;
    }
    
    return new WP_REST_Response($checks, $http_code);
}

/**
 * Полная проверка системы (для админов)
 * 
 * @return WP_REST_Response
 */
function atk_ved_system_status(): WP_REST_Response {
    global $wpdb;
    
    $status = array(
        'environment' => array(
            'home_url' => home_url(),
            'site_url' => site_url(),
            'is_ssl' => is_ssl(),
            'timezone' => get_option('timezone_string'),
            'language' => get_locale(),
        ),
        'server' => array(
            'software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'php_version' => phpversion(),
            'php_sapi' => php_sapi_name(),
            'php_max_input_vars' => ini_get('max_input_vars'),
            'php_upload_max_filesize' => ini_get('upload_max_filesize'),
            'php_post_max_size' => ini_get('post_max_size'),
            'php_max_execution_time' => ini_get('max_execution_time'),
            'php_memory_limit' => ini_get('memory_limit'),
            'mysql_version' => $wpdb->db_version(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
        ),
        'wordpress' => array(
            'version' => get_bloginfo('version'),
            'multisite' => is_multisite(),
            'site_url' => site_url(),
            'debug_mode' => defined('WP_DEBUG') && WP_DEBUG,
            'memory_limit' => WP_MEMORY_LIMIT,
        ),
        'theme' => array(
            'name' => wp_get_theme()->get('Name'),
            'version' => wp_get_theme()->get('Version'),
            'template' => wp_get_theme()->get_template(),
            'parent' => wp_get_theme()->parent() ? wp_get_theme()->parent()->get('Name') : 'None',
        ),
        'plugins' => array(
            'total' => count(get_option('active_plugins')),
            'list' => array(),
        ),
        'database' => array(
            'size' => atk_ved_get_database_size(),
            'tables' => $wpdb->get_col('SHOW TABLES'),
            'post_count' => wp_count_posts()->publish,
            'page_count' => wp_count_posts('page')->publish,
            'user_count' => count_users()['total_users'],
            'comment_count' => wp_count_comments()->total_comments,
        ),
        'security' => array(
            'ssl_enabled' => is_ssl(),
            'file_edit' => defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT,
            'debug_display' => !(defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY),
            'php_error_display' => !ini_get('display_errors'),
        ),
        'performance' => array(
            'object_cache' => wp_using_ext_object_cache(),
            'persistent_cache' => wp_cache_is_supported(),
        ),
    );
    
    // Активные плагины
    foreach (get_option('active_plugins') as $plugin) {
        $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin, false, false);
        $status['plugins']['list'][] = array(
            'name' => $plugin_data['Name'],
            'version' => $plugin_data['Version'],
            'author' => $plugin_data['Author'],
        );
    }
    
    // Проверки безопасности
    $security_issues = array();
    
    if (!defined('DISALLOW_FILE_EDIT')) {
        $security_issues[] = 'DISALLOW_FILE_EDIT not defined';
    }
    
    if (defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY) {
        $security_issues[] = 'WP_DEBUG_DISPLAY enabled';
    }
    
    if (ini_get('display_errors')) {
        $security_issues[] = 'PHP errors displayed';
    }
    
    $status['security']['issues'] = $security_issues;
    $status['security']['score'] = max(0, 100 - (count($security_issues) * 10));
    
    return new WP_REST_Response($status, 200);
}

/**
 * Получение размера базы данных
 * 
 * @return array
 */
function atk_ved_get_database_size(): array {
    global $wpdb;
    
    $result = $wpdb->get_row("
        SELECT 
            SUM(data_length + index_length) AS total_size,
            SUM(data_length) AS data_size,
            SUM(index_length) AS index_size
        FROM information_schema.TABLES 
        WHERE table_schema = '{$wpdb->dbname}'
    ", ARRAY_A);
    
    return array(
        'total' => size_format((float) ($result['total_size'] ?? 0)),
        'data' => size_format((float) ($result['data_size'] ?? 0)),
        'index' => size_format((float) ($result['index_size'] ?? 0)),
    );
}

/**
 * Проверка критических функций темы
 * 
 * @return array
 */
function atk_ved_check_theme_functions(): array {
    $required_functions = array(
        'atk_ved_enqueue_scripts',
        'atk_ved_register_menus',
        'atk_ved_theme_support',
    );
    
    $missing = array();
    
    foreach ($required_functions as $function) {
        if (!function_exists($function)) {
            $missing[] = $function;
        }
    }
    
    return array(
        'status' => empty($missing) ? 'ok' : 'error',
        'missing' => $missing,
    );
}

/**
 * Мониторинг производительности
 * 
 * @return array
 */
function atk_ved_performance_monitor(): array {
    $start_time = microtime(true);
    $start_memory = memory_get_usage();
    
    // Симуляция нагрузки (можно заменить на реальные метрики)
    $queries = get_num_queries();
    $query_time = timer_stop();
    
    return array(
        'load_time' => round(microtime(true) - $start_time, 4),
        'memory_used' => size_format(memory_get_usage() - $start_memory),
        'memory_peak' => size_format(memory_get_peak_usage()),
        'queries_count' => $queries,
        'queries_time' => $query_time,
    );
}

/**
 * Логирование проблем со здоровьем
 * 
 * @param string $message
 * @param string $level
 * @return void
 */
function atk_ved_log_health_issue(string $message, string $level = 'warning'): void {
    if (function_exists('atk_ved_log')) {
        atk_ved_log($message, $level, array('source' => 'health_check'));
    }
}

/**
 * Автоматическая проверка здоровья (по расписанию)
 * 
 * @return void
 */
function atk_ved_scheduled_health_check(): void {
    $health = atk_ved_health_check();
    $data = $health->get_data();
    
    if ($data['status'] === 'unhealthy') {
        // Отправка уведомления администратору
        $admin_email = get_option('admin_email');
        $subject = sprintf('[%s] Critical Health Issue', get_bloginfo('name'));
        $message = "Critical health issues detected:\n\n";
        
        foreach ($data['checks'] as $check => $status) {
            if (!$status) {
                $message .= "- {$check}: FAILED\n";
            }
        }
        
        wp_mail($admin_email, $subject, $message);
        
        // Логирование
        atk_ved_log_health_issue('Critical health check failed', 'error');
    }
}

// Планирование регулярных проверок
if (!wp_next_scheduled('atk_ved_hourly_health_check')) {
    wp_schedule_event(time(), 'hourly', 'atk_ved_hourly_health_check');
}
add_action('atk_ved_hourly_health_check', 'atk_ved_scheduled_health_check');
