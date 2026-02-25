<?php
/**
 * Система оптимизации производительности v3.3
 * 
 * @package ATK_VED
 * @subpackage Performance_Optimization
 */

if (!defined('ABSPATH')) {
    exit;
}

class ATK_VED_Performance_Optimizer {
    
    private static $instance = null;
    private $cache_enabled = false;
    private $minification_enabled = false;
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->cache_enabled = get_theme_mod('atk_ved_enable_cache', true);
        $this->minification_enabled = get_theme_mod('atk_ved_enable_minification', true);
        
        add_action('init', array($this, 'init_optimizations'));
        add_action('wp_enqueue_scripts', array($this, 'optimize_assets'), 999);
        add_action('wp_head', array($this, 'add_performance_headers'), 1);
        add_action('wp_footer', array($this, 'add_performance_analytics'));
        add_filter('wp_resource_hints', array($this, 'add_resource_hints'), 10, 2);
    }
    
    /**
     * Инициализация оптимизаций
     */
    public function init_optimizations() {
        // Оптимизация базы данных
        add_action('wp_scheduled_delete', array($this, 'cleanup_database'));
        
        // Оптимизация изображений
        add_filter('wp_generate_attachment_metadata', array($this, 'optimize_image_metadata'), 10, 2);
        
        // Кэширование запросов
        add_action('save_post', array($this, 'clear_post_cache'));
        add_action('deleted_post', array($this, 'clear_post_cache'));
        add_action('trashed_post', array($this, 'clear_post_cache'));
        
        // Оптимизация заголовков
        if ($this->cache_enabled) {
            add_action('send_headers', array($this, 'add_cache_headers'));
        }
    }
    
    /**
     * Оптимизация подключаемых ресурсов
     */
    public function optimize_assets() {
        if (is_admin() || defined('DOING_AJAX')) {
            return;
        }
        
        // Отложенная загрузка не критичных скриптов
        add_filter('script_loader_tag', array($this, 'add_async_defer_attributes'), 10, 2);
        
        // Предзагрузка критичных ресурсов
        add_action('wp_head', array($this, 'preload_critical_resources'), 2);
        
        // Удаление неиспользуемых стилей
        if (get_theme_mod('atk_ved_remove_unused_styles', true)) {
            add_action('wp_print_styles', array($this, 'remove_unused_styles'), 100);
        }
        
        // Минификация CSS и JS
        if ($this->minification_enabled) {
            add_filter('style_loader_src', array($this, 'minify_css_url'), 10, 2);
            add_filter('script_loader_src', array($this, 'minify_js_url'), 10, 2);
        }
    }
    
    /**
     * Добавление атрибутов async/defer
     */
    public function add_async_defer_attributes($tag, $handle) {
        $async_scripts = array(
            'atk-ved-enhancements',
            'atk-ved-statistics',
            'atk-ved-tracking'
        );
        
        $defer_scripts = array(
            'atk-ved-loader',
            'atk-ved-gallery',
            'atk-ved-reviews-slider'
        );
        
        if (in_array($handle, $async_scripts)) {
            return str_replace(' src', ' async src', $tag);
        }
        
        if (in_array($handle, $defer_scripts)) {
            return str_replace(' src', ' defer src', $tag);
        }
        
        return $tag;
    }
    
    /**
     * Предзагрузка критичных ресурсов
     */
    public function preload_critical_resources() {
        $resources = array(
            array(
                'url' => get_template_directory_uri() . '/css/modern-design.css',
                'as' => 'style',
                'type' => 'text/css'
            ),
            array(
                'url' => get_template_directory_uri() . '/js/main.js',
                'as' => 'script',
                'type' => 'text/javascript'
            )
        );
        
        foreach ($resources as $resource) {
            printf(
                '<link rel="preload" href="%s" as="%s" type="%s">' . "\n",
                esc_url($resource['url']),
                esc_attr($resource['as']),
                esc_attr($resource['type'])
            );
        }
    }
    
    /**
     * Добавление resource hints
     */
    public function add_resource_hints($hints, $relation_type) {
        if ('preconnect' === $relation_type) {
            $hints[] = 'https://fonts.googleapis.com';
            $hints[] = 'https://fonts.gstatic.com';
        }
        
        if ('dns-prefetch' === $relation_type) {
            $hints[] = '//www.google-analytics.com';
            $hints[] = '//stats.g.doubleclick.net';
        }
        
        return $hints;
    }
    
    /**
     * Добавление заголовков производительности
     */
    public function add_performance_headers() {
        if (is_admin() || defined('DOING_AJAX')) {
            return;
        }
        
        // Удаление ненужных заголовков
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'wp_shortlink_wp_head');
        remove_action('wp_head', 'feed_links_extra', 3);
        remove_action('wp_head', 'index_rel_link');
        remove_action('wp_head', 'parent_post_rel_link', 10);
        remove_action('wp_head', 'start_post_rel_link', 10);
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('wp_head', 'wp_oembed_add_host_js');
        
        // Добавление preload мета
        echo '<meta http-equiv="X-UA-Compatible" content="IE=edge">' . "\n";
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">' . "\n";
        echo '<meta name="HandheldFriendly" content="true">' . "\n";
        echo '<meta name="MobileOptimized" content="320">' . "\n";
    }
    
    /**
     * Оптимизация метаданных изображений
     */
    public function optimize_image_metadata($metadata, $attachment_id) {
        // Оптимизация размеров изображений
        $sizes = get_intermediate_image_sizes();
        $max_width = get_theme_mod('atk_ved_max_image_width', 1920);
        $quality = get_theme_mod('atk_ved_image_quality', 85);
        
        // Добавляем WebP версии
        if (function_exists('imagewebp')) {
            $this->generate_webp_versions($attachment_id, $metadata);
        }
        
        return $metadata;
    }
    
    /**
     * Генерация WebP версий
     */
    private function generate_webp_versions($attachment_id, $metadata) {
        $upload_dir = wp_upload_dir();
        $file_path = get_attached_file($attachment_id);
        $file_info = pathinfo($file_path);
        
        if (!in_array(strtolower($file_info['extension']), array('jpg', 'jpeg', 'png'))) {
            return;
        }
        
        $image = null;
        switch (strtolower($file_info['extension'])) {
            case 'jpg':
            case 'jpeg':
                $image = imagecreatefromjpeg($file_path);
                break;
            case 'png':
                $image = imagecreatefrompng($file_path);
                break;
        }
        
        if ($image) {
            $webp_path = $file_info['dirname'] . '/' . $file_info['filename'] . '.webp';
            imagewebp($image, $webp_path, 85);
            imagedestroy($image);
        }
    }
    
    /**
     * Очистка кэша поста
     */
    public function clear_post_cache($post_id) {
        if (wp_is_post_revision($post_id)) {
            return;
        }
        
        // Очистка объектного кэша WordPress
        wp_cache_flush();
        
        // Очистка кэша страниц (если используется плагин кэширования)
        if (function_exists('wp_cache_clear_cache')) {
            wp_cache_clear_cache();
        }
    }
    
    /**
     * Очистка базы данных
     */
    public function cleanup_database() {
        global $wpdb;
        
        // Очистка ревизий
        $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_type = 'revision'");
        
        // Очистка автоматических черновиков
        $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_status = 'auto-draft'");
        
        // Очистка спам-комментариев
        $wpdb->query("DELETE FROM {$wpdb->comments} WHERE comment_approved = 'spam'");
        
        // Оптимизация таблиц
        $tables = $wpdb->get_results("SHOW TABLES LIKE '{$wpdb->prefix}%'", ARRAY_N);
        foreach ($tables as $table) {
            $wpdb->query("OPTIMIZE TABLE `{$table[0]}`");
        }
    }
    
    /**
     * Удаление неиспользуемых стилей
     */
    public function remove_unused_styles() {
        global $wp_styles;
        
        $used_styles = $this->get_used_styles();
        $remove_styles = get_theme_mod('atk_ved_remove_styles', array());
        
        foreach ($remove_styles as $handle) {
            if (!in_array($handle, $used_styles) && isset($wp_styles->registered[$handle])) {
                wp_deregister_style($handle);
            }
        }
    }
    
    /**
     * Получение используемых стилей
     */
    private function get_used_styles() {
        return array(
            'atk-ved-modern-design',
            'atk-ved-style',
            'atk-ved-ui-components',
            'atk-ved-animations-enhanced'
        );
    }
    
    /**
     * Минификация CSS URL
     */
    public function minify_css_url($src, $handle) {
        if (strpos($src, '.min.css') !== false || !$this->minification_enabled) {
            return $src;
        }
        
        return add_query_arg('minify', 'css', $src);
    }
    
    /**
     * Минификация JS URL
     */
    public function minify_js_url($src, $handle) {
        if (strpos($src, '.min.js') !== false || !$this->minification_enabled) {
            return $src;
        }
        
        return add_query_arg('minify', 'js', $src);
    }
    
    /**
     * Добавление заголовков кэширования
     */
    public function add_cache_headers() {
        if (!is_admin() && !defined('DOING_AJAX')) {
            header('Cache-Control: public, max-age=31536000');
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        }
    }
    
    /**
     * Добавление аналитики производительности
     */
    public function add_performance_analytics() {
        if (!get_theme_mod('atk_ved_enable_performance_analytics', true)) {
            return;
        }
        
        ?>
        <script>
        // Производительность загрузки страницы
        window.addEventListener('load', function() {
            const perfData = performance.getEntriesByType('navigation')[0];
            if (perfData) {
                console.log('Page Load Time:', perfData.loadEventEnd - perfData.fetchStart + 'ms');
                console.log('DOM Content Loaded:', perfData.domContentLoadedEventEnd - perfData.fetchStart + 'ms');
            }
        });
        </script>
        <?php
    }
}

// Инициализация
function atk_ved_init_performance_optimizer() {
    ATK_VED_Performance_Optimizer::get_instance();
}
add_action('after_setup_theme', 'atk_ved_init_performance_optimizer');

// Добавление опций в Customizer
function atk_ved_performance_customizer($wp_customize) {
    // Секция производительности
    $wp_customize->add_section('atk_ved_performance', array(
        'title' => 'Производительность',
        'priority' => 40,
    ));
    
    // Включить кэширование
    $wp_customize->add_setting('atk_ved_enable_cache', array(
        'default' => true,
        'sanitize_callback' => 'atk_ved_sanitize_checkbox'
    ));
    
    $wp_customize->add_control('atk_ved_enable_cache', array(
        'label' => 'Включить кэширование',
        'section' => 'atk_ved_performance',
        'type' => 'checkbox'
    ));
    
    // Включить минификацию
    $wp_customize->add_setting('atk_ved_enable_minification', array(
        'default' => true,
        'sanitize_callback' => 'atk_ved_sanitize_checkbox'
    ));
    
    $wp_customize->add_control('atk_ved_enable_minification', array(
        'label' => 'Включить минификацию',
        'section' => 'atk_ved_performance',
        'type' => 'checkbox'
    ));
    
    // Удаление неиспользуемых стилей
    $wp_customize->add_setting('atk_ved_remove_unused_styles', array(
        'default' => true,
        'sanitize_callback' => 'atk_ved_sanitize_checkbox'
    ));
    
    $wp_customize->add_control('atk_ved_remove_unused_styles', array(
        'label' => 'Удалять неиспользуемые стили',
        'section' => 'atk_ved_performance',
        'type' => 'checkbox'
    ));
    
    // Аналитика производительности
    $wp_customize->add_setting('atk_ved_enable_performance_analytics', array(
        'default' => true,
        'sanitize_callback' => 'atk_ved_sanitize_checkbox'
    ));
    
    $wp_customize->add_control('atk_ved_enable_performance_analytics', array(
        'label' => 'Аналитика производительности',
        'section' => 'atk_ved_performance',
        'type' => 'checkbox'
    ));
}
add_action('customize_register', 'atk_ved_performance_customizer');