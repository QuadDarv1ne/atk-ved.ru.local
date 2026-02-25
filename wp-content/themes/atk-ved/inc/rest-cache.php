<?php
/**
 * REST API Caching & Optimization
 * Кэширование и оптимизация REST API запросов
 * 
 * @package ATK_VED
 * @since 2.8.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Кэширование ответов REST API
 * 
 * @param WP_REST_Response $response
 * @param WP_REST_Server $server
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 */
function atk_ved_cache_rest_response($response, $server, $request): WP_REST_Response {
    // Кэшируем только GET запросы
    if ($request->get_method() !== 'GET') {
        return $response;
    }
    
    // Список endpoints для кэширования
    $cacheable_endpoints = array(
        '/atk-ved/v1/services',
        '/atk-ved/v1/faq',
        '/atk-ved/v1/team',
        '/atk-ved/v1/partners',
        '/atk-ved/v1/health',
    );
    
    $route = $request->get_route();
    $should_cache = false;
    
    foreach ($cacheable_endpoints as $endpoint) {
        if (strpos($route, $endpoint) !== false) {
            $should_cache = true;
            break;
        }
    }
    
    if (!$should_cache) {
        return $response;
    }
    
    // Генерация ключа кэша
    $cache_key = atk_ved_get_rest_cache_key($request);
    $cached = get_transient($cache_key);
    
    if ($cached !== false) {
        // Возвращаем закэшированный ответ
        return new WP_REST_Response($cached['data'], $cached['status'], $cached['headers']);
    }
    
    // Сохраняем в кэш
    $cache_data = array(
        'data' => $response->get_data(),
        'status' => $response->get_status(),
        'headers' => $response->get_headers(),
    );
    
    // Время кэширования в зависимости от endpoint
    $cache_time = atk_ved_get_cache_time_for_route($route);
    set_transient($cache_key, $cache_data, $cache_time);
    
    // Добавляем заголовок о кэше
    $response->header('X-ATK-Cache', 'MISS');
    
    return $response;
}
add_filter('rest_post_dispatch', 'atk_ved_cache_rest_response', 10, 3);

/**
 * Генерация уникального ключа кэша для запроса
 * 
 * @param WP_REST_Request $request
 * @return string
 */
function atk_ved_get_rest_cache_key(WP_REST_Request $request): string {
    $route = $request->get_route();
    $params = $request->get_params();
    
    // Сортируем параметры для консистентности
    ksort($params);
    
    return 'atk_rest_cache_' . md5($route . '_' . wp_json_encode($params));
}

/**
 * Время кэширования для разных routes
 * 
 * @param string $route
 * @return int Время в секундах
 */
function atk_ved_get_cache_time_for_route(string $route): int {
    $cache_times = array(
        '/health' => 60,           // 1 минута
        '/services' => 3600,       // 1 час
        '/faq' => 7200,            // 2 часа
        '/team' => 86400,          // 24 часа
        '/partners' => 86400,      // 24 часа
        '/calculator' => 300,      // 5 минут
        '/tracking' => 60,         // 1 минута
    );
    
    foreach ($cache_times as $endpoint => $time) {
        if (strpos($route, $endpoint) !== false) {
            return $time;
        }
    }
    
    // По умолчанию 15 минут
    return 900;
}

/**
 * Очистка кэша REST API при обновлении контента
 * 
 * @param int $post_id
 * @return void
 */
function atk_ved_flush_rest_cache_on_post_update(int $post_id): void {
    global $wpdb;
    
    // Очищаем все transient с префиксом atk_rest_cache
    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
            '_transient_atk_rest_cache_%',
            '_transient_timeout_atk_rest_cache_%'
        )
    );
    
    // Очищаем object cache
    wp_cache_flush();
}
add_action('save_post', 'atk_ved_flush_rest_cache_on_post_update');
add_action('deleted_post', 'atk_ved_flush_rest_cache_on_post_update');

/**
 * Оптимизация SQL запросов
 * 
 * @param string $sql
 * @return string
 */
function atk_ved_optimize_sql_queries(string $sql): string {
    // Добавляем SQL_NO_CACHE для часто меняющихся запросов
    if (strpos($sql, 'SELECT SQL_CALC_FOUND_ROWS') !== false) {
        // Убираем SQL_CALC_FOUND_ROWS если не нужен
        if (!did_action('wp_handle_upload')) {
            $sql = str_replace('SELECT SQL_CALC_FOUND_ROWS', 'SELECT', $sql);
        }
    }
    
    return $sql;
}
add_filter('posts_request', 'atk_ved_optimize_sql_queries');

/**
 * Отключение ненужных REST API endpoints для производительности
 * 
 * @param WP_REST_Server $server
 * @return void
 */
function atk_ved_disable_unused_rest_endpoints(WP_REST_Server $server): void {
    // Можно отключить неиспользуемые endpoints
    // remove_action('rest_api_init', 'wp_oembed_register_route');
}
add_action('rest_api_init', 'atk_ved_disable_unused_rest_endpoints');

/**
 * Rate limiting для REST API
 * 
 * @param WP_Error|true $result
 * @param WP_REST_Request $request
 * @return WP_Error|true
 */
function atk_ved_rest_rate_limit($result, WP_REST_Request $request) {
    // Не применяем к авторизованным пользователям
    if (is_user_logged_in()) {
        return $result;
    }
    
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $key = 'atk_rate_limit_' . md5($ip . '_' . $request->get_route());
    
    $count = (int) get_transient($key);
    $limit = 100; // Лимит запросов в час
    
    if ($count >= $limit) {
        return new WP_Error(
            'too_many_requests',
            __('Too many requests. Please try again later.', 'atk-ved'),
            array('status' => 429)
        );
    }
    
    set_transient($key, $count + 1, HOUR_IN_SECONDS);
    
    return $result;
}
add_filter('rest_request_before_callbacks', 'atk_ved_rest_rate_limit', 5, 2);

/**
 * Добавление мета-данных к REST API ответам
 * 
 * @param WP_REST_Response $response
 * @param WP_Post $post
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 */
function atk_ved_rest_add_meta($response, $post, $request): WP_REST_Response {
    $data = $response->get_data();
    
    // Добавляем дополнительную информацию
    if (isset($data['id'])) {
        $data['atk_meta'] = array(
            'cached' => get_transient('atk_rest_cache_' . md5($request->get_route() . '_' . wp_json_encode($request->get_params()))) !== false,
            'cache_time' => atk_ved_get_cache_time_for_route($request->get_route()),
        );
        
        $response->set_data($data);
    }
    
    return $response;
}
add_filter('rest_prepare_post', 'atk_ved_rest_add_meta', 10, 3);
add_filter('rest_prepare_page', 'atk_ved_rest_add_meta', 10, 3);

/**
 * Предзагрузка кэша для важных endpoints
 * 
 * @return void
 */
function atk_ved_preload_rest_cache(): void {
    $endpoints = array(
        '/wp-json/atk-ved/v1/services',
        '/wp-json/atk-ved/v1/faq',
        '/wp-json/atk-ved/v1/team',
    );
    
    foreach ($endpoints as $endpoint) {
        $response = wp_remote_get(home_url($endpoint));
        
        if (!is_wp_error($response)) {
            // Кэш уже установлен через фильтр
        }
    }
}

// Планирование предзагрузки кэша
if (!wp_next_scheduled('atk_ved_preload_cache')) {
    wp_schedule_event(time(), 'twicedaily', 'atk_ved_preload_cache');
}
add_action('atk_ved_preload_cache', 'atk_ved_preload_rest_cache');

/**
 * Мониторинг производительности REST API
 * 
 * @param WP_REST_Response $response
 * @param WP_REST_Server $server
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 */
function atk_ved_monitor_rest_performance($response, $server, $request): WP_REST_Response {
    $start_time = microtime(true);
    
    // Добавляем заголовок со временем выполнения
    add_filter('rest_post_dispatch', function($response, $server) use ($start_time) {
        $execution_time = round((microtime(true) - $start_time) * 1000, 2);
        $response->header('X-ATK-Execution-Time', "{$execution_time}ms");
        return $response;
    }, 100, 2);
    
    return $response;
}
add_filter('rest_request_before_callbacks', 'atk_ved_monitor_rest_performance', 1, 3);
