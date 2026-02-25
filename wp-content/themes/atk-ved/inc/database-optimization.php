<?php
/**
 * Advanced Database Optimization & Caching System
 * Расширенная система оптимизации базы данных и кэширования
 * 
 * @package ATK_VED
 * @since 2.9.1
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Класс для оптимизации базы данных
 */
class ATK_VED_Database_Optimizer {
    
    /**
     * Инициализация оптимизатора
     */
    public static function init(): void {
        add_action('wp_ajax_atk_ved_optimize_database', array(__CLASS__, 'ajax_optimize_database'));
        add_action('wp_ajax_atk_ved_clean_database', array(__CLASS__, 'ajax_clean_database'));
        add_action('wp_ajax_atk_ved_analyze_database', array(__CLASS__, 'ajax_analyze_database'));
        
        // Автоматическая оптимизация по расписанию
        if (!wp_next_scheduled('atk_ved_weekly_database_optimization')) {
            wp_schedule_event(time(), 'weekly', 'atk_ved_weekly_database_optimization');
        }
        add_action('atk_ved_weekly_database_optimization', array(__CLASS__, 'scheduled_optimization'));
    }
    
    /**
     * Оптимизация базы данных через AJAX
     */
    public static function ajax_optimize_database(): void {
        check_ajax_referer('atk_ved_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $result = self::optimize_database();
        wp_send_json_success($result);
    }
    
    /**
     * Очистка базы данных через AJAX
     */
    public static function ajax_clean_database(): void {
        check_ajax_referer('atk_ved_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $result = self::clean_database();
        wp_send_json_success($result);
    }
    
    /**
     * Анализ базы данных через AJAX
     */
    public static function ajax_analyze_database(): void {
        check_ajax_referer('atk_ved_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $result = self::analyze_database();
        wp_send_json_success($result);
    }
    
    /**
     * Оптимизация базы данных
     */
    public static function optimize_database(): array {
        global $wpdb;
        
        $start_time = microtime(true);
        $optimized_tables = 0;
        $errors = array();
        
        // Получаем все таблицы WordPress
        $tables = $wpdb->get_results("SHOW TABLES", ARRAY_N);
        $tables = array_column($tables, 0);
        
        foreach ($tables as $table) {
            // Проверяем, что это таблица WordPress
            if (strpos($table, $wpdb->prefix) === 0) {
                $result = $wpdb->query("OPTIMIZE TABLE `{$table}`");
                if ($result !== false) {
                    $optimized_tables++;
                } else {
                    $errors[] = "Failed to optimize table: {$table}";
                }
            }
        }
        
        $end_time = microtime(true);
        
        return array(
            'status' => 'success',
            'tables_optimized' => $optimized_tables,
            'errors' => $errors,
            'execution_time' => round(($end_time - $start_time) * 1000, 2) . 'ms'
        );
    }
    
    /**
     * Очистка базы данных
     */
    public static function clean_database(): array {
        global $wpdb;
        
        $start_time = microtime(true);
        $cleaned_items = array();
        $total_cleaned = 0;
        
        // Очистка ревизий
        $revisions_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'revision'");
        if ($revisions_count > 0) {
            $deleted = $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_type = 'revision'");
            $cleaned_items['revisions'] = $deleted;
            $total_cleaned += $deleted;
        }
        
        // Очистка авто-черновиков
        $auto_drafts_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = 'auto-draft'");
        if ($auto_drafts_count > 0) {
            $deleted = $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_status = 'auto-draft'");
            $cleaned_items['auto_drafts'] = $deleted;
            $total_cleaned += $deleted;
        }
        
        // Очистка спам комментариев
        $spam_comments_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_approved = 'spam'");
        if ($spam_comments_count > 0) {
            $deleted = $wpdb->query("DELETE FROM {$wpdb->comments} WHERE comment_approved = 'spam'");
            $cleaned_items['spam_comments'] = $deleted;
            $total_cleaned += $deleted;
        }
        
        // Очистка мета данных отсутствующих записей
        $orphaned_postmeta = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->postmeta} pm LEFT JOIN {$wpdb->posts} p ON pm.post_id = p.ID WHERE p.ID IS NULL"
        );
        if ($orphaned_postmeta > 0) {
            $deleted = $wpdb->query(
                "DELETE pm FROM {$wpdb->postmeta} pm LEFT JOIN {$wpdb->posts} p ON pm.post_id = p.ID WHERE p.ID IS NULL"
            );
            $cleaned_items['orphaned_postmeta'] = $deleted;
            $total_cleaned += $deleted;
        }
        
        // Очистка транзиентов
        $transients_count = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE '_transient_%' OR option_name LIKE '_site_transient_%'"
        );
        if ($transients_count > 0) {
            $deleted = $wpdb->query(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%' OR option_name LIKE '_site_transient_%'"
            );
            $cleaned_items['transients'] = $deleted;
            $total_cleaned += $deleted;
        }
        
        $end_time = microtime(true);
        
        return array(
            'status' => 'success',
            'cleaned_items' => $cleaned_items,
            'total_cleaned' => $total_cleaned,
            'execution_time' => round(($end_time - $start_time) * 1000, 2) . 'ms'
        );
    }
    
    /**
     * Анализ базы данных
     */
    public static function analyze_database(): array {
        global $wpdb;
        
        $analysis = array();
        
        // Размер базы данных
        $db_size = $wpdb->get_var(
            "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'DB Size in MB' 
             FROM information_schema.tables 
             WHERE table_schema = DATABASE()"
        );
        $analysis['database_size'] = $db_size . ' MB';
        
        // Количество таблиц
        $table_count = $wpdb->get_var("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE()");
        $analysis['table_count'] = $table_count;
        
        // Анализ таблиц WordPress
        $wp_tables = $wpdb->get_results(
            "SELECT 
                table_name AS 'table',
                ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'size_mb',
                table_rows AS 'rows'
             FROM information_schema.tables 
             WHERE table_schema = DATABASE() 
             AND table_name LIKE '{$wpdb->prefix}%'
             ORDER BY (data_length + index_length) DESC"
        );
        
        $analysis['wp_tables'] = $wp_tables;
        
        // Анализ постов
        $posts_analysis = $wpdb->get_results(
            "SELECT post_type, COUNT(*) as count, 
                    ROUND(AVG(LENGTH(post_content))/1024, 2) as avg_content_size_kb
             FROM {$wpdb->posts} 
             WHERE post_status = 'publish' 
             GROUP BY post_type 
             ORDER BY count DESC"
        );
        $analysis['posts_analysis'] = $posts_analysis;
        
        // Анализ мета данных
        $meta_analysis = $wpdb->get_results(
            "SELECT meta_key, COUNT(*) as count
             FROM {$wpdb->postmeta}
             WHERE meta_key NOT LIKE '\_%'
             GROUP BY meta_key
             ORDER BY count DESC
             LIMIT 20"
        );
        $analysis['meta_analysis'] = $meta_analysis;
        
        // Поиск дубликатов мета данных
        $duplicates = $wpdb->get_var(
            "SELECT COUNT(*) FROM (
                SELECT post_id, meta_key, COUNT(*) as cnt
                FROM {$wpdb->postmeta}
                GROUP BY post_id, meta_key
                HAVING cnt > 1
            ) as duplicates"
        );
        $analysis['duplicate_meta'] = $duplicates;
        
        return $analysis;
    }
    
    /**
     * Планирование оптимизации по расписанию
     */
    public static function scheduled_optimization(): void {
        // Очистка базы данных
        self::clean_database();
        
        // Оптимизация таблиц
        self::optimize_database();
        
        // Логирование
        atk_ved_log('Database optimization completed automatically', 'info');
    }
    
    /**
     * Получение статуса оптимизации
     */
    public static function get_optimization_status(): array {
        $last_optimization = get_option('atk_ved_last_db_optimization', 'Never');
        $last_cleaning = get_option('atk_ved_last_db_cleaning', 'Never');
        
        return array(
            'last_optimization' => $last_optimization,
            'last_cleaning' => $last_cleaning,
            'auto_optimization' => wp_next_scheduled('atk_ved_weekly_database_optimization') ? 'Enabled' : 'Disabled'
        );
    }
}

/**
 * Класс для расширенного кэширования
 */
class ATK_VED_Advanced_Caching {
    
    /**
     * Инициализация системы кэширования
     */
    public static function init(): void {
        add_action('wp_ajax_atk_ved_flush_cache', array(__CLASS__, 'ajax_flush_cache'));
        add_action('wp_ajax_atk_ved_cache_stats', array(__CLASS__, 'ajax_cache_stats'));
        
        // Очистка кэша при обновлении контента
        add_action('save_post', array(__CLASS__, 'flush_post_cache'));
        add_action('edit_post', array(__CLASS__, 'flush_post_cache'));
        add_action('delete_post', array(__CLASS__, 'flush_post_cache'));
        add_action('switch_theme', array(__CLASS__, 'flush_all_cache'));
        
        // Автоматическая очистка старого кэша
        if (!wp_next_scheduled('atk_ved_daily_cache_cleanup')) {
            wp_schedule_event(time(), 'daily', 'atk_ved_daily_cache_cleanup');
        }
        add_action('atk_ved_daily_cache_cleanup', array(__CLASS__, 'daily_cache_cleanup'));
    }
    
    /**
     * Очистка кэша через AJAX
     */
    public static function ajax_flush_cache(): void {
        check_ajax_referer('atk_ved_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $result = self::flush_all_cache();
        wp_send_json_success($result);
    }
    
    /**
     * Статистика кэширования через AJAX
     */
    public static function ajax_cache_stats(): void {
        check_ajax_referer('atk_ved_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $stats = self::get_cache_stats();
        wp_send_json_success($stats);
    }
    
    /**
     * Очистка всего кэша
     */
    public static function flush_all_cache(): array {
        global $wpdb;
        
        $start_time = microtime(true);
        $deleted_count = 0;
        
        // Очистка WordPress объектного кэша
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        // Очистка транзиентов
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
             WHERE option_name LIKE '_transient_%' 
             OR option_name LIKE '_site_transient_%'"
        );
        $deleted_count += $wpdb->rows_affected;
        
        // Очистка пользовательского кэша темы
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
             WHERE option_name LIKE 'atk_cache_%'"
        );
        $deleted_count += $wpdb->rows_affected;
        
        // Очистка REST API кэша
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
             WHERE option_name LIKE '_transient_atk_rest_cache_%'"
        );
        $deleted_count += $wpdb->rows_affected;
        
        // Очистка кэша страниц
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
             WHERE option_name LIKE '_transient_atk_page_cache_%'"
        );
        $deleted_count += $wpdb->rows_affected;
        
        // Очистка кэша запросов
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
             WHERE option_name LIKE '_transient_atk_query_cache_%'"
        );
        $deleted_count += $wpdb->rows_affected;
        
        $end_time = microtime(true);
        
        // Обновление времени последней очистки
        update_option('atk_ved_last_cache_flush', current_time('mysql'));
        
        return array(
            'status' => 'success',
            'deleted_items' => $deleted_count,
            'execution_time' => round(($end_time - $start_time) * 1000, 2) . 'ms'
        );
    }
    
    /**
     * Очистка кэша для конкретной записи
     */
    public static function flush_post_cache(int $post_id): void {
        global $wpdb;
        
        $post = get_post($post_id);
        if (!$post) return;
        
        // Очистка кэша URL записи
        $post_url = get_permalink($post_id);
        $url_hash = md5($post_url);
        
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} 
                 WHERE option_name LIKE %s 
                 OR option_name LIKE %s",
                '_transient_atk_page_cache_' . $url_hash . '%',
                '_transient_atk_query_cache_' . $post_id . '%'
            )
        );
        
        // Очистка REST API кэша для этой записи
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} 
                 WHERE option_name LIKE %s",
                '_transient_atk_rest_cache_%' . $post_id . '%'
            )
        );
    }
    
    /**
     * Получение статистики кэша
     */
    public static function get_cache_stats(): array {
        global $wpdb;
        
        $stats = array();
        
        // Подсчет транзиентов
        $transients_count = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->options} 
             WHERE option_name LIKE '_transient_%' 
             OR option_name LIKE '_site_transient_%'"
        );
        $stats['transients_count'] = $transients_count;
        
        // Подсчет кэша темы
        $theme_cache_count = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->options} 
             WHERE option_name LIKE 'atk_cache_%'"
        );
        $stats['theme_cache_count'] = $theme_cache_count;
        
        // Подсчет REST API кэша
        $rest_cache_count = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->options} 
             WHERE option_name LIKE '_transient_atk_rest_cache_%'"
        );
        $stats['rest_cache_count'] = $rest_cache_count;
        
        // Подсчет кэша страниц
        $page_cache_count = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->options} 
             WHERE option_name LIKE '_transient_atk_page_cache_%'"
        );
        $stats['page_cache_count'] = $page_cache_count;
        
        // Общий размер кэша (приблизительный)
        $cache_size = $wpdb->get_var(
            "SELECT ROUND(SUM(LENGTH(option_value))/1024/1024, 2) 
             FROM {$wpdb->options} 
             WHERE option_name LIKE '_transient_%' 
             OR option_name LIKE 'atk_cache_%'"
        );
        $stats['cache_size_mb'] = $cache_size ?? 0;
        
        // Дата последней очистки
        $stats['last_flush'] = get_option('atk_ved_last_cache_flush', 'Never');
        
        return $stats;
    }
    
    /**
     * Ежедневная очистка кэша
     */
    public static function daily_cache_cleanup(): void {
        global $wpdb;
        
        // Очистка старых транзиентов (старше 1 недели)
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
             WHERE (option_name LIKE '_transient_%' OR option_name LIKE '_site_transient_%')
             AND option_value < DATE_SUB(NOW(), INTERVAL 7 DAY)"
        );
        
        // Очистка старого кэша темы
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
             WHERE option_name LIKE 'atk_cache_%'
             AND CAST(option_value AS CHAR) LIKE '%timestamp%'
             AND JSON_EXTRACT(option_value, '$.timestamp') < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 1 DAY))"
        );
        
        atk_ved_log('Daily cache cleanup completed', 'info');
    }
}

// Инициализация
ATK_VED_Database_Optimizer::init();
ATK_VED_Advanced_Caching::init();