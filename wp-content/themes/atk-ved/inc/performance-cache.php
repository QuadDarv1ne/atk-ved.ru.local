<?php
/**
 * Система кэширования и оптимизации производительности
 *
 * @package ATK_VED
 * @since 3.5.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Класс для управления кэшем
 */
class ATK_VED_Cache {
    
    private static $instance = null;
    private $cache_dir;
    private $cache_time = 3600; // 1 час по умолчанию
    
    public static function get_instance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->cache_dir = WP_CONTENT_DIR . '/cache/atk-ved/';
        $this->init_cache_dir();
    }
    
    /**
     * Инициализация директории кэша
     */
    private function init_cache_dir(): void {
        if (!file_exists($this->cache_dir)) {
            wp_mkdir_p($this->cache_dir);
            // Добавляем .htaccess для защиты
            file_put_contents($this->cache_dir . '.htaccess', 'deny from all');
            // Добавляем index.php для защиты
            file_put_contents($this->cache_dir . 'index.php', '<?php // Silence is golden');
        }
    }
    
    /**
     * Получение данных из кэша
     */
    public function get(string $key, $default = null) {
        $file = $this->get_cache_file($key);
        
        if (!file_exists($file)) {
            return $default;
        }
        
        // Проверяем время жизни кэша
        if (filemtime($file) < (time() - $this->cache_time)) {
            $this->delete($key);
            return $default;
        }
        
        $data = file_get_contents($file);
        return maybe_unserialize($data);
    }
    
    /**
     * Сохранение данных в кэш
     */
    public function set(string $key, $value, int $time = null): bool {
        $file = $this->get_cache_file($key);
        $data = maybe_serialize($value);
        
        return file_put_contents($file, $data) !== false;
    }
    
    /**
     * Удаление данных из кэша
     */
    public function delete(string $key): bool {
        $file = $this->get_cache_file($key);
        
        if (file_exists($file)) {
            return unlink($file);
        }
        
        return true;
    }
    
    /**
     * Очистка всего кэша
     */
    public function flush(): bool {
        $files = glob($this->cache_dir . '*.php');
        
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        
        return true;
    }
    
    /**
     * Получение пути к файлу кэша
     */
    private function get_cache_file(string $key): string {
        return $this->cache_dir . md5($key) . '.php';
    }
}

/**
 * Оптимизация загрузки скриптов
 */
function atk_ved_optimize_scripts(): void {
    // Отключаем emojis для производительности
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    
    // Отключаем embeds
    remove_action('rest_api_init', 'wp_oembed_register_route');
    remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
    
    // Отключаем WP-JSON API ссылки
    remove_action('wp_head', 'rest_output_link_wp_head', 10);
    remove_action('wp_head', 'wp_shortlink_wp_head', 10);
    
    // Отключаем DNS Prefetch для s.w.org
    remove_action('wp_head', 'wp_resource_hints', 2);
}
add_action('init', 'atk_ved_optimize_scripts');

/**
 * Отложенная загрузка скриптов (defer/async)
 */
function atk_ved_script_loader_tag(string $tag, string $handle, string $src): string {
    // Скрипты для отложенной загрузки
    $defer_handles = [
        'comment-reply',
        'wp-embed',
    ];
    
    // Скрипты для асинхронной загрузки
    $async_handles = [
        'google-analytics',
        'yandex-metrika',
    ];
    
    if (in_array($handle, $defer_handles, true)) {
        return str_replace(' src', ' defer="defer" src', $tag);
    }
    
    if (in_array($handle, $async_handles, true)) {
        return str_replace(' src', ' async="async" src', $tag);
    }
    
    return $tag;
}
add_filter('script_loader_tag', 'atk_ved_script_loader_tag', 10, 3);

/**
 * Предзагрузка критических ресурсов
 */
function atk_ved_preload_critical_resources(): void {
    // Предзагрузка шрифтов
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    
    // Предзагрузка критических скриптов
    $theme_uri = get_template_directory_uri();
    echo '<link rel="preload" href="' . esc_url($theme_uri) . '/css/critical.css" as="style">' . "\n";
    echo '<link rel="preload" href="' . esc_url($theme_uri) . '/js/main.js" as="script">' . "\n";
}
add_action('wp_head', 'atk_ved_preload_critical_resources', 1);

/**
 * DNS Prefetch для внешних ресурсов
 */
function atk_ved_dns_prefetch(): void {
    $hosts = [
        'https://www.google-analytics.com',
        'https://www.google.com',
        'https://fonts.googleapis.com',
        'https://fonts.gstatic.com',
        'https://unpkg.com',
    ];
    
    foreach ($hosts as $host) {
        echo '<link rel="dns-prefetch" href="' . esc_url($host) . '">' . "\n";
    }
}
add_action('wp_head', 'atk_ved_dns_prefetch', 0);

/**
 * Очистка head от лишнего
 */
function atk_ved_cleanup_head(): void {
    // Удаляем лишнее из head
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
    remove_action('wp_head', 'rest_output_link_wp_head', 10);
    
    // Удаляем версию WordPress
    add_filter('the_generator', '__return_empty_string');
    
    // Удаляем версию jQuery
    add_filter('script_loader_src', function($src, $handle) {
        if ($handle === 'jquery-core') {
            return remove_query_arg('ver', $src);
        }
        return $src;
    }, 10, 2);
}
add_action('after_setup_theme', 'atk_ved_cleanup_head');

/**
 * GZIP сжатие
 */
function atk_ved_enable_gzip(): void {
    if (!ob_get_level()) {
        ob_start('ob_gzhandler');
    }
}
// Не включаем автоматически, лучше через .htaccess

/**
 * Кэширование запросов к базе данных
 */
function atk_ved_cache_database_queries(): void {
    global $wpdb;
    
    // Кэширование частых запросов
    add_filter('posts_results', function($posts, $query) {
        if ($query->is_main_query() && !is_admin()) {
            $cache_key = 'atk_ved_query_' . md5(serialize($query->query_vars));
            $cached = ATK_VED_Cache::get_instance()->get($cache_key);
            
            if ($cached) {
                return $cached;
            }
            
            ATK_VED_Cache::get_instance()->set($cache_key, $posts, HOUR_IN_SECONDS);
        }
        
        return $posts;
    }, 10, 2);
}

/**
 * Оптимизация изображений
 */
function atk_ved_optimize_images(string $content): string {
    // Добавляем width и height для предотвращения CLS
    if (preg_match_all('/<img[^>]+src="([^"]+)"[^>]*>/i', $content, $matches)) {
        foreach ($matches[0] as $img) {
            if (!preg_match('/loading=["\']lazy["\']/i', $img)) {
                $new_img = str_replace('<img', '<img loading="lazy"', $img);
                $content = str_replace($img, $new_img, $content);
            }
            
            // Добавляем decoding="async"
            if (!preg_match('/decoding=["\']async["\']/i', $img)) {
                $new_img = str_replace('<img', '<img decoding="async"', $img);
                $content = str_replace($img, $new_img, $content);
            }
        }
    }
    
    return $content;
}
add_filter('the_content', 'atk_ved_optimize_images', 20);

/**
 * Предзагрузка LCP изображения
 */
function atk_ved_preload_lcp_image(): void {
    if (!is_singular()) {
        return;
    }
    
    global $post;
    if (!$post) {
        return;
    }
    
    // Получаем первое изображение
    preg_match('/<img[^>]+src="([^"]+)"/i', $post->post_content, $matches);
    
    if (!empty($matches[1])) {
        $image_url = esc_url($matches[1]);
        echo '<link rel="preload" as="image" href="' . $image_url . '">' . "\n";
    }
}
add_action('wp_head', 'atk_ved_preload_lcp_image', 2);

/**
 * Удаление query strings из статических ресурсов
 */
function atk_ved_remove_query_strings(string $src): string {
    if (strpos($src, '?ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('script_loader_src', 'atk_ved_remove_query_strings', 15);
add_filter('style_loader_src', 'atk_ved_remove_query_strings', 15);

/**
 * Отключение heartbeat API на фронтенде
 */
function atk_ved_disable_heartbeat_frontend(): void {
    if (!is_admin()) {
        wp_deregister_script('heartbeat');
    }
}
add_action('wp_enqueue_scripts', 'atk_ved_disable_heartbeat_frontend', 100);

/**
 * Ограничение количества ревизий
 */
function atk_ved_limit_revisions(int $num): int {
    return 5; // Максимум 5 ревизий
}
add_filter('wp_revisions_to_keep', 'atk_ved_limit_revisions', 10, 1);

/**
 * Автоматическая очистка старых ревизий
 */
function atk_ved_cleanup_revisions(): void {
    global $wpdb;
    
    // Удаляем ревизии старше 30 дней
    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->posts} 
            WHERE post_type = 'revision' 
            AND post_date < %s",
            date('Y-m-d', strtotime('-30 days'))
        )
    );
    
    // Оптимизируем таблицы
    $wpdb->query("OPTIMIZE TABLE {$wpdb->posts}");
    $wpdb->query("OPTIMIZE TABLE {$wpdb->postmeta}");
    $wpdb->query("OPTIMIZE TABLE {$wpdb->options}");
}

/**
 * Планирование очистки кэша
 */
function atk_ved_schedule_cache_cleanup(): void {
    if (!wp_next_scheduled('atk_ved_cache_cleanup')) {
        wp_schedule_event(time(), 'daily', 'atk_ved_cache_cleanup');
    }
}
add_action('init', 'atk_ved_schedule_cache_cleanup');

add_action('atk_ved_cache_cleanup', function() {
    ATK_VED_Cache::get_instance()->flush();
    atk_ved_cleanup_revisions();
});

/**
 * Шорткод для ручной очистки кэша (только для админов)
 */
function atk_ved_flush_cache_shortcode(): string {
    if (!current_user_can('manage_options')) {
        return '<p class="error">Недостаточно прав</p>';
    }
    
    ATK_VED_Cache::get_instance()->flush();
    
    return '<p class="success">Кэш успешно очищен!</p>';
}
add_shortcode('flush_cache', 'atk_ved_flush_cache_shortcode');

/**
 * Измерение времени генерации страницы
 */
function atk_ved_start_performance_timer(): void {
    global $atk_ved_start_time;
    $atk_ved_start_time = microtime(true);
}
add_action('wp_head', 'atk_ved_start_performance_timer', 0);

function atk_ved_end_performance_timer(): void {
    global $atk_ved_start_time;
    
    if (!isset($atk_ved_start_time)) {
        return;
    }
    
    $end_time = microtime(true);
    $generation_time = round(($end_time - $atk_ved_start_time) * 1000, 2);
    
    // Добавляем комментарий в HTML
    echo "\n<!-- Page generated in {$generation_time}ms -->\n";
    
    // Для админов показываем в admin bar
    if (current_user_can('manage_options')) {
        global $wp_admin_bar;
        
        if ($wp_admin_bar) {
            $wp_admin_bar->add_node([
                'id' => 'atk-performance',
                'title' => "⏱ {$generation_time}ms",
                'parent' => 'top-secondary',
            ]);
        }
    }
}
add_action('wp_footer', 'atk_ved_end_performance_timer', 1000);

/**
 * Ленивая загрузка iframe
 */
function atk_ved_lazy_iframes(string $content): string {
    return preg_replace_callback(
        '/<iframe(.*?)src="(.*?)"(.*?)><\/iframe>/is',
        function($matches) {
            $attrs = $matches[1] . $matches[3];
            
            // Добавляем loading="lazy" если нет
            if (!preg_match('/loading=["\']lazy["\']/i', $attrs)) {
                $attrs = ' loading="lazy"' . $attrs;
            }
            
            // Добавляем decoding="async" если нет
            if (!preg_match('/decoding=["\']async["\']/i', $attrs)) {
                $attrs = ' decoding="async"' . $attrs;
            }
            
            return '<iframe' . $attrs . 'src="' . $matches[2] . '"></iframe>';
        },
        $content
    );
}
add_filter('the_content', 'atk_ved_lazy_iframes', 20);
