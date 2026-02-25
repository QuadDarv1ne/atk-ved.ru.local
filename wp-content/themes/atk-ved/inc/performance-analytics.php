<?php
/**
 * Performance Monitoring & Analytics
 * Мониторинг производительности и аналитика
 * 
 * @package ATK_VED
 * @since 2.9.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Класс для мониторинга производительности
 */
class ATK_VED_Performance_Monitor {
    
    /**
     * Инициализация мониторинга
     */
    public static function init(): void {
        add_action('wp_loaded', array(__CLASS__, 'start_monitoring'));
        add_action('shutdown', array(__CLASS__, 'end_monitoring'));
        add_action('wp_ajax_atk_ved_get_performance_data', array(__CLASS__, 'ajax_get_performance_data'));
    }
    
    /**
     * Начало мониторинга
     */
    public static function start_monitoring(): void {
        if (!defined('ATK_PERFORMANCE_START')) {
            define('ATK_PERFORMANCE_START', microtime(true));
        }
        
        // Записываем начальные данные
        if (!isset($_SESSION['atk_performance'])) {
            $_SESSION['atk_performance'] = array();
        }
        
        $_SESSION['atk_performance']['start_time'] = microtime(true);
        $_SESSION['atk_performance']['memory_start'] = memory_get_usage();
        $_SESSION['atk_performance']['queries_start'] = get_num_queries();
    }
    
    /**
     * Окончание мониторинга
     */
    public static function end_monitoring(): void {
        if (!defined('ATK_PERFORMANCE_START')) {
            return;
        }
        
        $end_time = microtime(true);
        $memory_end = memory_get_usage();
        $queries_end = get_num_queries();
        
        $performance_data = array(
            'load_time' => round(($end_time - ATK_PERFORMANCE_START) * 1000, 2),
            'memory_used' => size_format($memory_end - $_SESSION['atk_performance']['memory_start']),
            'memory_peak' => size_format(memory_get_peak_usage()),
            'queries_count' => $queries_end - $_SESSION['atk_performance']['queries_start'],
            'timestamp' => current_time('mysql'),
            'url' => $_SERVER['REQUEST_URI'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        );
        
        // Сохраняем данные
        self::save_performance_data($performance_data);
        
        // Добавляем в заголовки для отладки
        if (defined('WP_DEBUG') && WP_DEBUG) {
            header('X-ATK-Load-Time: ' . $performance_data['load_time'] . 'ms');
            header('X-ATK-Memory: ' . $performance_data['memory_used']);
            header('X-ATK-Queries: ' . $performance_data['queries_count']);
        }
    }
    
    /**
     * Сохранение данных производительности
     */
    private static function save_performance_data(array $data): void {
        $performance_log = get_option('atk_performance_log', array());
        
        // Ограничиваем количество записей
        if (count($performance_log) > 100) {
            array_shift($performance_log);
        }
        
        $performance_log[] = $data;
        update_option('atk_performance_log', $performance_log, false);
    }
    
    /**
     * Получение данных производительности через AJAX
     */
    public static function ajax_get_performance_data(): void {
        check_ajax_referer('atk_ved_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $performance_log = get_option('atk_performance_log', array());
        
        // Рассчитываем средние значения
        $stats = self::calculate_performance_stats($performance_log);
        
        wp_send_json_success(array(
            'stats' => $stats,
            'recent_data' => array_slice($performance_log, -20)
        ));
    }
    
    /**
     * Расчет статистики производительности
     */
    private static function calculate_performance_stats(array $data): array {
        if (empty($data)) {
            return array();
        }
        
        $load_times = array_column($data, 'load_time');
        $memory_usage = array_column($data, 'memory_used');
        $query_counts = array_column($data, 'queries_count');
        
        return array(
            'avg_load_time' => round(array_sum($load_times) / count($load_times), 2),
            'max_load_time' => max($load_times),
            'min_load_time' => min($load_times),
            'avg_memory' => self::format_bytes(array_sum(array_map('self::parse_bytes', $memory_usage)) / count($memory_usage)),
            'avg_queries' => round(array_sum($query_counts) / count($query_counts), 0),
            'total_requests' => count($data)
        );
    }
    
    /**
     * Конвертация размера в байты
     */
    private static function parse_bytes(string $size): int {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        $size = preg_replace('/[^0-9\.]/', '', $size);
        
        if ($unit) {
            return (int) ($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }
        
        return (int) $size;
    }
    
    /**
     * Форматирование байтов
     */
    private static function format_bytes(int $bytes): string {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    /**
     * Получение рекомендаций по оптимизации
     */
    public static function get_optimization_recommendations(): array {
        $performance_log = get_option('atk_performance_log', array());
        $stats = self::calculate_performance_stats($performance_log);
        
        $recommendations = array();
        
        // Рекомендации по времени загрузки
        if (isset($stats['avg_load_time']) && $stats['avg_load_time'] > 2000) {
            $recommendations[] = array(
                'type' => 'warning',
                'message' => 'Среднее время загрузки страницы высокое (' . $stats['avg_load_time'] . 'ms). Рекомендуется оптимизировать базу данных и кэширование.'
            );
        }
        
        // Рекомендации по памяти
        if (isset($stats['avg_memory']) && self::parse_bytes($stats['avg_memory']) > 64 * 1024 * 1024) {
            $recommendations[] = array(
                'type' => 'warning',
                'message' => 'Высокое потребление памяти (' . $stats['avg_memory'] . '). Проверьте плагины и темы на утечки памяти.'
            );
        }
        
        // Рекомендации по количеству запросов
        if (isset($stats['avg_queries']) && $stats['avg_queries'] > 100) {
            $recommendations[] = array(
                'type' => 'warning',
                'message' => 'Высокое количество SQL-запросов (' . $stats['avg_queries'] . '). Рекомендуется оптимизировать запросы и использовать кэширование объектов.'
            );
        }
        
        return $recommendations;
    }
}

/**
 * Класс для аналитики пользовательского поведения
 */
class ATK_VED_User_Analytics {
    
    /**
     * Инициализация аналитики
     */
    public static function init(): void {
        add_action('wp_head', array(__CLASS__, 'add_analytics_tracking'));
        add_action('wp_ajax_atk_ved_track_event', array(__CLASS__, 'ajax_track_event'));
        add_action('wp_ajax_nopriv_atk_ved_track_event', array(__CLASS__, 'ajax_track_event'));
    }
    
    /**
     * Добавление кода отслеживания
     */
    public static function add_analytics_tracking(): void {
        if (is_admin() || is_customize_preview()) {
            return;
        }
        ?>
        <script>
        // ATK VED Analytics
        window.atkAnalytics = {
            events: [],
            track: function(category, action, label, value) {
                this.events.push({
                    category: category,
                    action: action,
                    label: label,
                    value: value,
                    timestamp: Date.now()
                });
                
                // Отправка через AJAX
                if (typeof atkVedData !== 'undefined') {
                    fetch(atkVedData.ajaxUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            action: 'atk_ved_track_event',
                            category: category,
                            action_name: action,
                            label: label,
                            value: value,
                            nonce: atkVedData.nonce
                        })
                    });
                }
            }
        };
        
        // Автоматическое отслеживание
        document.addEventListener('DOMContentLoaded', function() {
            // Отслеживание кликов по кнопкам
            document.addEventListener('click', function(e) {
                if (e.target.matches('button, a, .cta-button')) {
                    const label = e.target.textContent.trim() || e.target.getAttribute('aria-label') || 'Unknown';
                    atkAnalytics.track('interaction', 'click', label);
                }
            });
            
            // Отслеживание прокрутки
            let scrollTracked = false;
            window.addEventListener('scroll', function() {
                if (!scrollTracked && window.scrollY > 300) {
                    atkAnalytics.track('engagement', 'scroll', '300px');
                    scrollTracked = true;
                }
            });
        });
        </script>
        <?php
    }
    
    /**
     * AJAX обработчик отслеживания событий
     */
    public static function ajax_track_event(): void {
        check_ajax_referer('atk_ved_nonce', 'nonce');
        
        $event_data = array(
            'category' => sanitize_text_field($_POST['category'] ?? ''),
            'action' => sanitize_text_field($_POST['action_name'] ?? ''),
            'label' => sanitize_text_field($_POST['label'] ?? ''),
            'value' => intval($_POST['value'] ?? 0),
            'user_id' => get_current_user_id(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'timestamp' => current_time('mysql')
        );
        
        // Сохраняем событие
        $events = get_option('atk_analytics_events', array());
        
        // Ограничиваем количество записей
        if (count($events) > 1000) {
            array_shift($events);
        }
        
        $events[] = $event_data;
        update_option('atk_analytics_events', $events, false);
        
        wp_send_json_success();
    }
    
    /**
     * Получение аналитических данных
     */
    public static function get_analytics_data(int $days = 30): array {
        $events = get_option('atk_analytics_events', array());
        
        // Фильтруем по дате
        $cutoff_date = strtotime("-{$days} days");
        $filtered_events = array_filter($events, function($event) use ($cutoff_date) {
            return strtotime($event['timestamp']) > $cutoff_date;
        });
        
        // Агрегируем данные
        $data = array(
            'total_events' => count($filtered_events),
            'unique_visitors' => count(array_unique(array_column($filtered_events, 'ip'))),
            'popular_events' => self::get_popular_events($filtered_events),
            'conversion_events' => self::get_conversion_events($filtered_events)
        );
        
        return $data;
    }
    
    /**
     * Получение популярных событий
     */
    private static function get_popular_events(array $events): array {
        $event_counts = array();
        
        foreach ($events as $event) {
            $key = $event['category'] . ':' . $event['action'] . ':' . $event['label'];
            $event_counts[$key] = ($event_counts[$key] ?? 0) + 1;
        }
        
        arsort($event_counts);
        
        return array_slice($event_counts, 0, 10, true);
    }
    
    /**
     * Получение событий конверсии
     */
    private static function get_conversion_events(array $events): array {
        $conversion_events = array_filter($events, function($event) {
            return in_array($event['action'], ['submit', 'purchase', 'contact']);
        });
        
        return array(
            'total_conversions' => count($conversion_events),
            'conversion_rate' => count($events) > 0 ? round((count($conversion_events) / count($events)) * 100, 2) : 0
        );
    }
}

// Инициализация
ATK_VED_Performance_Monitor::init();
ATK_VED_User_Analytics::init();