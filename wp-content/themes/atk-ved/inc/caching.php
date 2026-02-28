<?php
/**
 * Caching Functions - Object Cache & Transients
 *
 * @package ATK_VED
 * @since 3.6.0
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

/**
 * Получение постов с кэшированием
 *
 * @param array<string, mixed> $args Аргументы WP_Query
 * @param int $cache_time Время кэширования в секундах
 * @return array<int, WP_Post> Массив постов
 */
function atk_ved_get_cached_posts(array $args, int $cache_time = HOUR_IN_SECONDS): array {
    $cache_key = 'atk_posts_' . md5(serialize($args));
    
    // Пробуем получить из object cache
    $posts = wp_cache_get($cache_key, 'atk_ved');
    
    if (false === $posts) {
        // Если нет в object cache, пробуем transient
        $posts = get_transient($cache_key);
        
        if (false === $posts) {
            // Если нет нигде, делаем запрос
            $query = new WP_Query($args);
            $posts = $query->posts;
            
            // Сохраняем в оба кэша
            set_transient($cache_key, $posts, $cache_time);
            wp_cache_set($cache_key, $posts, 'atk_ved', $cache_time);
        } else {
            // Есть в transient, сохраняем в object cache
            wp_cache_set($cache_key, $posts, 'atk_ved', $cache_time);
        }
    }
    
    return $posts;
}

/**
 * Получение мета-данных с кэшированием
 *
 * @param int $post_id ID поста
 * @param string $meta_key Ключ мета-поля
 * @param bool $single Возвращать одно значение
 * @return mixed Значение мета-поля
 */
function atk_ved_get_cached_post_meta(int $post_id, string $meta_key, bool $single = true) {
    $cache_key = "atk_meta_{$post_id}_{$meta_key}";
    
    $value = wp_cache_get($cache_key, 'atk_ved');
    
    if (false === $value) {
        $value = get_post_meta($post_id, $meta_key, $single);
        wp_cache_set($cache_key, $value, 'atk_ved', HOUR_IN_SECONDS);
    }
    
    return $value;
}

/**
 * Получение термов с кэшированием
 *
 * @param array<string, mixed> $args Аргументы get_terms
 * @param int $cache_time Время кэширования
 * @return array<int, WP_Term> Массив термов
 */
function atk_ved_get_cached_terms(array $args, int $cache_time = HOUR_IN_SECONDS): array {
    $cache_key = 'atk_terms_' . md5(serialize($args));
    
    $terms = wp_cache_get($cache_key, 'atk_ved');
    
    if (false === $terms) {
        $terms = get_terms($args);
        
        if (!is_wp_error($terms)) {
            wp_cache_set($cache_key, $terms, 'atk_ved', $cache_time);
        } else {
            $terms = [];
        }
    }
    
    return $terms;
}

/**
 * Кэширование результатов внешних API запросов
 *
 * @param string $api_url URL API
 * @param array<string, mixed> $args Аргументы запроса
 * @param int $cache_time Время кэширования
 * @return mixed Результат API запроса
 */
function atk_ved_cached_api_request(string $api_url, array $args = [], int $cache_time = HOUR_IN_SECONDS) {
    $cache_key = 'atk_api_' . md5($api_url . serialize($args));
    
    // Пробуем получить из transient
    $response = get_transient($cache_key);
    
    if (false === $response) {
        // Делаем запрос к API
        $response = wp_remote_get($api_url, $args);
        
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            
            // Сохраняем в transient
            set_transient($cache_key, $data, $cache_time);
            
            return $data;
        }
        
        return null;
    }
    
    return $response;
}

/**
 * Очистка кэша при обновлении поста
 *
 * @param int $post_id ID поста
 * @return void
 */
function atk_ved_clear_post_cache(int $post_id): void {
    // Очищаем кэш постов
    wp_cache_delete('atk_posts_*', 'atk_ved');
    
    // Очищаем кэш мета-данных
    $meta_keys = ['_review_author_name', '_review_author_position', '_review_rating', '_company_name', '_file_type'];
    foreach ($meta_keys as $key) {
        wp_cache_delete("atk_meta_{$post_id}_{$key}", 'atk_ved');
    }
    
    // Очищаем transients
    global $wpdb;
    $wpdb->query(
        "DELETE FROM {$wpdb->options} 
        WHERE option_name LIKE '_transient_atk_posts_%' 
        OR option_name LIKE '_transient_timeout_atk_posts_%'"
    );
}
add_action('save_post', 'atk_ved_clear_post_cache');
add_action('delete_post', 'atk_ved_clear_post_cache');

/**
 * Очистка кэша при обновлении термов
 *
 * @param int $term_id ID терма
 * @return void
 */
function atk_ved_clear_term_cache(int $term_id): void {
    wp_cache_delete('atk_terms_*', 'atk_ved');
    
    global $wpdb;
    $wpdb->query(
        "DELETE FROM {$wpdb->options} 
        WHERE option_name LIKE '_transient_atk_terms_%' 
        OR option_name LIKE '_transient_timeout_atk_terms_%'"
    );
}
add_action('edited_term', 'atk_ved_clear_term_cache');
add_action('delete_term', 'atk_ved_clear_term_cache');

/**
 * Получение настроек темы с кэшированием
 *
 * @param string $option Название опции
 * @param mixed $default Значение по умолчанию
 * @return mixed Значение опции
 */
function atk_ved_get_cached_theme_mod(string $option, $default = false) {
    $cache_key = "atk_theme_mod_{$option}";
    
    $value = wp_cache_get($cache_key, 'atk_ved');
    
    if (false === $value) {
        $value = get_theme_mod($option, $default);
        wp_cache_set($cache_key, $value, 'atk_ved', DAY_IN_SECONDS);
    }
    
    return $value;
}

/**
 * Очистка всего кэша темы
 *
 * @return void
 */
function atk_ved_clear_all_cache(): void {
    // Очищаем object cache
    wp_cache_flush();
    
    // Очищаем все transients темы
    global $wpdb;
    $wpdb->query(
        "DELETE FROM {$wpdb->options} 
        WHERE option_name LIKE '_transient_atk_%' 
        OR option_name LIKE '_transient_timeout_atk_%'"
    );
    
    // Очищаем кэш критического CSS
    delete_transient('atk_ved_critical_css_v3');
}

/**
 * Добавляем кнопку очистки кэша в админ-бар
 */
function atk_ved_add_clear_cache_button($wp_admin_bar): void {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $wp_admin_bar->add_node([
        'id' => 'atk-clear-cache',
        'title' => '<span class="ab-icon dashicons dashicons-update"></span> Очистить кэш',
        'href' => wp_nonce_url(admin_url('admin-post.php?action=atk_clear_cache'), 'atk_clear_cache'),
        'meta' => [
            'title' => 'Очистить весь кэш темы',
        ],
    ]);
}
add_action('admin_bar_menu', 'atk_ved_add_clear_cache_button', 100);

/**
 * Обработчик очистки кэша
 */
function atk_ved_handle_clear_cache(): void {
    if (!current_user_can('manage_options')) {
        wp_die('Недостаточно прав');
    }
    
    check_admin_referer('atk_clear_cache');
    
    atk_ved_clear_all_cache();
    
    wp_safe_redirect(wp_get_referer() ?: admin_url());
    exit;
}
add_action('admin_post_atk_clear_cache', 'atk_ved_handle_clear_cache');

/**
 * Кэширование меню навигации
 *
 * @param string $theme_location Расположение меню
 * @param array<string, mixed> $args Аргументы wp_nav_menu
 * @return string HTML меню
 */
function atk_ved_get_cached_menu(string $theme_location, array $args = []): string {
    $cache_key = "atk_menu_{$theme_location}_" . md5(serialize($args));
    
    $menu_html = wp_cache_get($cache_key, 'atk_ved');
    
    if (false === $menu_html) {
        $args['theme_location'] = $theme_location;
        $args['echo'] = false;
        
        $menu_html = wp_nav_menu($args);
        
        wp_cache_set($cache_key, $menu_html, 'atk_ved', DAY_IN_SECONDS);
    }
    
    return $menu_html;
}

/**
 * Очистка кэша меню при обновлении
 */
function atk_ved_clear_menu_cache(): void {
    wp_cache_delete('atk_menu_*', 'atk_ved');
}
add_action('wp_update_nav_menu', 'atk_ved_clear_menu_cache');
