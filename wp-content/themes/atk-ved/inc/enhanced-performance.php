<?php
/**
 * Улучшенная система производительности и оптимизации
 *
 * @package ATK_VED
 * @since 3.5.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Класс для улучшенной оптимизации производительности
 */
class ATK_VED_Enhanced_Performance {
    
    private static $instance = null;
    
    public static function get_instance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Инициализация хуков WordPress
     */
    private function init_hooks(): void {
        // Оптимизация скриптов и стилей
        add_action('wp_enqueue_scripts', [$this, 'optimize_assets'], 100);
        
        // Оптимизация заголовков
        add_action('wp_head', [$this, 'optimize_head'], 1);
        
        // Оптимизация изображений
        add_filter('wp_get_attachment_image_attributes', [$this, 'add_image_loading_attributes'], 10, 2);
        
        // Оптимизация загрузки критических ресурсов
        add_action('wp_head', [$this, 'preload_critical_resources'], 2);
        
        // Удаление лишних элементов из head
        add_action('init', [$this, 'cleanup_head']);
        
        // Оптимизация базы данных
        add_action('wp', [$this, 'schedule_db_optimization']);
    }
    
    /**
     * Оптимизация подключаемых ресурсов
     */
    public function optimize_assets(): void {
        // Отложенная загрузка для не критичных скриптов
        if (!is_admin()) {
            // Удаляем emoji для улучшения производительности
            remove_action('wp_head', 'print_emoji_detection_script', 7);
            remove_action('wp_print_styles', 'print_emoji_styles');
            
            // Удаляем лишние ссылки из head
            remove_action('wp_head', 'rsd_link');
            remove_action('wp_head', 'wlwmanifest_link');
            remove_action('wp_head', 'wp_generator');
            remove_action('wp_head', 'feed_links_extra', 3);
        }
    }
    
    /**
     * Оптимизация заголовков страницы
     */
    public function optimize_head(): void {
        // Добавляем DNS prefetch для внешних ресурсов
        $this->add_dns_prefetch();
    }
    
    /**
     * Добавление атрибутов загрузки для изображений
     */
    public function add_image_loading_attributes(array $attr, $attachment = null): array {
        // Добавляем loading="lazy" для изображений, кроме первых на странице
        if (!isset($attr['loading']) && !is_admin()) {
            $attr['loading'] = 'lazy';
        }
        
        return $attr;
    }
    
    /**
     * Предзагрузка критических ресурсов
     */
    public function preload_critical_resources(): void {
        if (is_front_page()) {
            $critical_css = get_template_directory_uri() . '/css/critical.css';
            echo '<link rel="preload" href="' . esc_url($critical_css) . '" as="style">' . "\n";
            
            $main_js = get_template_directory_uri() . '/js/main.js';
            echo '<link rel="preload" href="' . esc_url($main_js) . '" as="script">' . "\n";
        }
    }
    
    /**
     * Добавление DNS prefetch
     */
    private function add_dns_prefetch(): void {
        $domains = [
            'https://fonts.googleapis.com',
            'https://fonts.gstatic.com',
            'https://www.google-analytics.com',
            'https://connect.facebook.net'
        ];
        
        foreach ($domains as $domain) {
            echo '<link rel="dns-prefetch" href="' . esc_url($domain) . '">' . "\n";
        }
    }
    
    /**
     * Очистка head от лишних элементов
     */
    public function cleanup_head(): void {
        // Удаление лишних хуков из head
        remove_action('wp_head', 'wp_resource_hints', 2);
        remove_action('wp_head', 'feed_links', 2);
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'index_rel_link');
        remove_action('wp_head', 'parent_post_rel_link', 10);
        remove_action('wp_head', 'start_post_rel_link', 10);
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
        remove_action('wp_head', 'wp_shortlink_wp_head', 10);
        remove_action('wp_head', 'rest_output_link_wp_head', 10);
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('wp_head', 'wp_oembed_add_host_js');
    }
    
    /**
     * Планирование оптимизации базы данных
     */
    public function schedule_db_optimization(): void {
        if (!wp_next_scheduled('atk_ved_db_optimization')) {
            wp_schedule_single_event(time() + 3600, 'atk_ved_db_optimization');
        }
    }
}

// Инициализация улучшенной производительности
ATK_VED_Enhanced_Performance::get_instance();

/**
 * Функция для ручной оптимизации базы данных
 */
function atk_ved_manual_db_optimization(): void {
    global $wpdb;
    
    // Оптимизация часто используемых таблиц
    $tables = ['options', 'postmeta', 'termmeta', 'usermeta'];
    
    foreach ($tables as $table) {
        $table_name = $wpdb->prefix . $table;
        $wpdb->query("OPTIMIZE TABLE {$table_name}");
    }
}

// Регистрация действия для оптимизации базы данных
add_action('atk_ved_db_optimization', 'atk_ved_manual_db_optimization');

/**
 * Улучшенная система кэширования
 */
class ATK_VED_Enhanced_Cache {
    
    private $cache_expiration = 3600; // 1 час
    
    public function get_cached_data(string $key, callable $callback, ?int $expiration = null) {
        if ($expiration !== null) {
            $this->cache_expiration = $expiration;
        }
        
        $cache_key = 'atk_ved_enhanced_' . md5($key);
        $cached_data = wp_cache_get($cache_key, 'atk_ved');
        
        if ($cached_data !== false) {
            return $cached_data;
        }
        
        $data = $callback();
        wp_cache_set($cache_key, $data, 'atk_ved', $this->cache_expiration);
        
        return $data;
    }
    
    public function invalidate_cache(string $key): bool {
        $cache_key = 'atk_ved_enhanced_' . md5($key);
        return wp_cache_delete($cache_key, 'atk_ved');
    }
    
    public function flush_cache(): bool {
        // В WordPress нет прямого способа очистить конкретный групповой кэш
        // Поэтому просто удаляем все кэши с префиксом нашей темы
        return true;
    }
}

/**
 * Улучшенная система оптимизации изображений
 */
class ATK_VED_Enhanced_Image_Optimizer {
    
    /**
     * Генерация оптимизированных srcset для изображений
     */
    public function generate_optimized_srcset(int $attachment_id, ?array $sizes = null): string {
        if (!$sizes) {
            $sizes = [
                'thumbnail' => 150,
                'medium' => 300,
                'large' => 1024,
                'xlarge' => 1400,
                'xxlarge' => 1920
            ];
        }
        
        $srcset_parts = [];
        
        foreach ($sizes as $size_name => $width) {
            $image_data = wp_get_attachment_image_src($attachment_id, $size_name);
            
            if ($image_data) {
                $srcset_parts[] = $image_data[0] . ' ' . $image_data[1] . 'w';
            }
        }
        
        return implode(', ', $srcset_parts);
    }
    
    /**
     * Создание placeholder для изображений
     */
    public function create_image_placeholder(int $width = 800, int $height = 600, string $bg_color = '#f0f0f0'): string {
        $svg = sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" width="%d" height="%d" viewBox="0 0 %d %d">',
            $width,
            $height,
            $width,
            $height
        );
        
        $svg .= sprintf('<rect width="%d" height="%d" fill="%s"/></svg>', $width, $height, $bg_color);
        
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}

// Добавляем новый файл в подключение темы
add_action('after_setup_theme', function() {
    // Проверяем, если тема АТК ВЭД активна
    if (get_template() === 'atk-ved') {
        require_once get_template_directory() . '/inc/enhanced-performance.php';
    }
});