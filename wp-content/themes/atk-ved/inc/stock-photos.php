<?php
/**
 * Интеграция с фотостоками (Unsplash, Pexels)
 * Загрузка качественных фотографий Китая, производств и логистики
 *
 * @package ATK_VED
 * @since 3.6.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Класс для работы с фотостоками
 */
class ATK_VED_Stock_Photos {
    
    private static $instance = null;
    
    // API ключи (можно настроить в админке)
    private $unsplash_key;
    private $pexels_key;
    
    // Категории фотографий для сайта
    private $categories = [
        'china' => [
            'name' => 'Китай',
            'keywords' => ['china', 'beijing', 'shanghai', 'chinese city', 'asia'],
        ],
        'factory' => [
            'name' => 'Производство',
            'keywords' => ['factory', 'manufacturing', 'production line', 'warehouse', 'industry'],
        ],
        'shipping' => [
            'name' => 'Доставка и логистика',
            'keywords' => ['shipping', 'cargo', 'logistics', 'container', 'freight', 'delivery'],
        ],
        'office' => [
            'name' => 'Офис и команда',
            'keywords' => ['office', 'team', 'business meeting', 'coworkers', 'workplace'],
        ],
        'products' => [
            'name' => 'Товары',
            'keywords' => ['products', 'goods', 'merchandise', 'retail', 'wholesale'],
        ],
    ];
    
    public static function get_instance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->unsplash_key = get_option('atk_ved_unsplash_key', '');
        $this->pexels_key = get_option('atk_ved_pexels_key', '');
    }
    
    /**
     * Поиск фотографий на Unsplash
     */
    public function search_unsplash(string $query, int $per_page = 20): array {
        if (empty($this->unsplash_key)) {
            return [];
        }
        
        $url = add_query_arg([
            'query' => urlencode($query),
            'per_page' => $per_page,
            'orientation' => 'landscape',
            'content_filter' => 'high',
        ], 'https://api.unsplash.com/search/photos');
        
        $response = wp_remote_get($url, [
            'headers' => [
                'Authorization' => 'Client-ID ' . $this->unsplash_key,
            ],
            'timeout' => 15,
        ]);
        
        if (is_wp_error($response)) {
            return [];
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (empty($body['results'])) {
            return [];
        }
        
        return array_map(function($photo) {
            return [
                'id' => $photo['id'],
                'url' => $photo['urls']['regular'],
                'full_url' => $photo['urls']['full'],
                'thumb_url' => $photo['urls']['thumb'],
                'small_url' => $photo['urls']['small'],
                'width' => $photo['width'],
                'height' => $photo['height'],
                'alt' => $photo['alt_description'] ?? '',
                'photographer' => $photo['user']['name'] ?? '',
                'photographer_url' => $photo['user']['links']['html'] ?? '',
                'source' => 'unsplash',
                'download_url' => $photo['links']['download'] ?? '',
            ];
        }, $body['results']);
    }
    
    /**
     * Поиск фотографий на Pexels
     */
    public function search_pexels(string $query, int $per_page = 20): array {
        if (empty($this->pexels_key)) {
            return [];
        }
        
        $url = add_query_arg([
            'query' => urlencode($query),
            'per_page' => $per_page,
        ], 'https://api.pexels.com/v1/search');
        
        $response = wp_remote_get($url, [
            'headers' => [
                'Authorization' => $this->pexels_key,
            ],
            'timeout' => 15,
        ]);
        
        if (is_wp_error($response)) {
            return [];
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (empty($body['photos'])) {
            return [];
        }
        
        return array_map(function($photo) {
            return [
                'id' => $photo['id'],
                'url' => $photo['src']['large'],
                'full_url' => $photo['src']['original'],
                'thumb_url' => $photo['src']['thumbnail'],
                'small_url' => $photo['src']['medium'],
                'width' => $photo['width'],
                'height' => $photo['height'],
                'alt' => $photo['alt'] ?? '',
                'photographer' => $photo['photographer'] ?? '',
                'photographer_url' => '',
                'source' => 'pexels',
                'download_url' => $photo['src']['download'] ?? '',
            ];
        }, $body['photos']);
    }
    
    /**
     * Поиск по категории
     */
    public function search_by_category(string $category, int $per_page = 20): array {
        if (!isset($this->categories[$category])) {
            return [];
        }
        
        $cat = $this->categories[$category];
        $query = $cat['keywords'][0]; // Используем первое ключевое слово
        
        $unsplash_results = $this->search_unsplash($query, $per_page);
        $pexels_results = $this->search_pexels($query, $per_page);
        
        // Объединяем результаты
        $all_results = array_merge($unsplash_results, $pexels_results);
        
        // Перемешиваем и возвращаем нужное количество
        shuffle($all_results);
        
        return array_slice($all_results, 0, $per_page);
    }
    
    /**
     * Загрузка фотографии в медиабиблиотеку
     */
    public function download_photo(array $photo_data, string $category = ''): int {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        $url = $photo_data['url'];
        $filename = sanitize_file_name($photo_data['alt'] ?: 'photo-' . $photo_data['id']);
        
        // Скачиваем файл
        $download = download_url($url);
        
        if (is_wp_error($download)) {
            return 0;
        }
        
        $file_array = [
            'name' => $filename . '.jpg',
            'tmp_name' => $download,
        ];
        
        // Добавляем в медиабиблиотеку
        $attachment_id = media_handle_sideload($file_array, 0);
        
        if (is_wp_error($attachment_id)) {
            @unlink($download);
            return 0;
        }
        
        // Обновляем мета-данные
        update_post_meta($attachment_id, '_stock_photo_source', $photo_data['source']);
        update_post_meta($attachment_id, '_stock_photo_id', $photo_data['id']);
        update_post_meta($attachment_id, '_stock_photo_category', $category);
        update_post_meta($attachment_id, '_stock_photographer', $photo_data['photographer']);
        update_post_meta($attachment_id, '_stock_photographer_url', $photo_data['photographer_url']);
        update_post_meta($attachment_id, '_wp_attachment_image_alt', $photo_data['alt']);
        
        return $attachment_id;
    }
    
    /**
     * Массовая загрузка фотографий
     */
    public function bulk_download(string $category, int $limit = 10): array {
        $photos = $this->search_by_category($category, $limit);
        $downloaded = [];
        
        foreach ($photos as $photo) {
            $attachment_id = $this->download_photo($photo, $category);
            
            if ($attachment_id) {
                $downloaded[] = [
                    'id' => $attachment_id,
                    'url' => wp_get_attachment_url($attachment_id),
                    'thumb' => wp_get_attachment_thumb_url($attachment_id),
                    'photo_data' => $photo,
                ];
            }
        }
        
        return $downloaded;
    }
    
    /**
     * Получение категорий
     */
    public function get_categories(): array {
        return $this->categories;
    }
    
    /**
     * Проверка подключения API
     */
    public function check_api_connection(): array {
        $result = [
            'unsplash' => false,
            'pexels' => false,
        ];
        
        if (!empty($this->unsplash_key)) {
            $response = wp_remote_get('https://api.unsplash.com/photos', [
                'headers' => [
                    'Authorization' => 'Client-ID ' . $this->unsplash_key,
                ],
                'timeout' => 5,
            ]);
            
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                $result['unsplash'] = true;
            }
        }
        
        if (!empty($this->pexels_key)) {
            $response = wp_remote_get('https://api.pexels.com/v1/curated', [
                'headers' => [
                    'Authorization' => $this->pexels_key,
                ],
                'timeout' => 5,
            ]);
            
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                $result['pexels'] = true;
            }
        }
        
        return $result;
    }
}

/**
 * Страница импорта фотографий в админке
 */
function atk_ved_add_stock_photos_page(): void {
    add_theme_page(
        __('Стоковые фото', 'atk-ved'),
        __('Стоковые фото', 'atk-ved'),
        'manage_options',
        'atk-stock-photos',
        'atk_ved_stock_photos_page'
    );
}
add_action('admin_menu', 'atk_ved_add_stock_photos_page');

/**
 * HTML страницы импорта
 */
function atk_ved_stock_photos_page(): void {
    $stock = ATK_VED_Stock_Photos::get_instance();
    $categories = $stock->get_categories();
    $api_status = $stock->check_api_connection();
    
    // Обработка загрузки
    $downloaded = [];
    if (isset($_POST['atk_download_photos']) && check_admin_referer('atk_download_photos_nonce')) {
        $category = sanitize_text_field($_POST['category']);
        $limit = absint($_POST['limit']);
        
        if ($category && $limit > 0) {
            $downloaded = $stock->bulk_download($category, $limit);
        }
    }
    
    // Обработка поиска
    $search_results = [];
    if (isset($_POST['atk_search_photos']) && check_admin_referer('atk_search_photos_nonce')) {
        $query = sanitize_text_field($_POST['search_query']);
        $source = sanitize_text_field($_POST['source']);
        
        if ($query) {
            if ($source === 'unsplash') {
                $search_results = $stock->search_unsplash($query, 20);
            } else {
                $search_results = $stock->search_pexels($query, 20);
            }
        }
    }
    ?>
    <div class="wrap atk-stock-photos-page">
        <h1><?php _e('Импорт стоковых фотографий', 'atk-ved'); ?></h1>
        
        <div class="atk-stock-notice">
            <h3><?php _e('📸 Источники фотографий', 'atk-ved'); ?></h3>
            <p><?php _e('Используйте бесплатные стоковые фотографии с Unsplash и Pexels для вашего сайта.', 'atk-ved'); ?></p>
            <ul>
                <li><strong>Unsplash:</strong> <?php echo $api_status['unsplash'] ? '✅' : '❌'; ?> 
                    (<?php _e('Настройте ключ в настройках темы', 'atk-ved'); ?>)
                </li>
                <li><strong>Pexels:</strong> <?php echo $api_status['pexels'] ? '✅' : '❌'; ?>
                    (<?php _e('Настройте ключ в настройках темы', 'atk-ved'); ?>)
                </li>
            </ul>
        </div>
        
        <?php if (!empty($downloaded)): ?>
        <div class="notice notice-success">
            <p><?php printf(__('Загружено %d фотографий!', 'atk-ved'), count($downloaded)); ?></p>
        </div>
        <?php endif; ?>
        
        <div class="atk-stock-grid">
            <!-- Быстрая загрузка по категориям -->
            <div class="atk-stock-card">
                <h2><?php _e('📥 Быстрая загрузка', 'atk-ved'); ?></h2>
                <form method="post" class="atk-stock-form">
                    <?php wp_nonce_field('atk_download_photos_nonce'); ?>
                    
                    <div class="form-group">
                        <label><?php _e('Категория', 'atk-ved'); ?></label>
                        <select name="category" required>
                            <?php foreach ($categories as $key => $cat): ?>
                            <option value="<?php echo esc_attr($key); ?>">
                                <?php echo esc_html($cat['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><?php _e('Количество', 'atk-ved'); ?></label>
                        <input type="number" name="limit" value="10" min="1" max="50">
                    </div>
                    
                    <button type="submit" name="atk_download_photos" class="button button-primary">
                        <?php _e('Загрузить фотографии', 'atk-ved'); ?>
                    </button>
                </form>
                
                <div class="atk-stock-info">
                    <h4><?php _e('Доступные категории:', 'atk-ved'); ?></h4>
                    <ul>
                        <?php foreach ($categories as $cat): ?>
                        <li><?php echo esc_html($cat['name']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            
            <!-- Поиск фотографий -->
            <div class="atk-stock-card">
                <h2><?php _e('🔍 Поиск фотографий', 'atk-ved'); ?></h2>
                <form method="post" class="atk-stock-form">
                    <?php wp_nonce_field('atk_search_photos_nonce'); ?>
                    
                    <div class="form-group">
                        <label><?php _e('Поисковый запрос', 'atk-ved'); ?></label>
                        <input type="text" name="search_query" placeholder="china factory, shipping, logistics..." required>
                    </div>
                    
                    <div class="form-group">
                        <label><?php _e('Источник', 'atk-ved'); ?></label>
                        <select name="source">
                            <option value="unsplash">Unsplash</option>
                            <option value="pexels">Pexels</option>
                        </select>
                    </div>
                    
                    <button type="submit" name="atk_search_photos" class="button">
                        <?php _e('Найти', 'atk-ved'); ?>
                    </button>
                </form>
                
                <?php if (!empty($search_results)): ?>
                <div class="atk-search-results">
                    <h4><?php printf(__('Найдено: %d', 'atk-ved'), count($search_results)); ?></h4>
                    <div class="atk-photo-grid">
                        <?php foreach ($search_results as $photo): ?>
                        <div class="atk-photo-item">
                            <img src="<?php echo esc_url($photo['thumb_url']); ?>" alt="<?php echo esc_attr($photo['alt']); ?>" loading="lazy">
                            <div class="atk-photo-info">
                                <p><strong><?php echo esc_html($photo['photographer']); ?></strong></p>
                                <p><?php echo esc_html($photo['width']); ?>x<?php echo esc_html($photo['height']); ?></p>
                                <a href="<?php echo esc_url($photo['url']); ?>" target="_blank" class="button button-small">
                                    <?php _e('Скачать', 'atk-ved'); ?>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Настройки API ключей -->
        <div class="atk-stock-card atk-settings-card">
            <h2><?php _e('⚙️ Настройки API', 'atk-ved'); ?></h2>
            <form method="post" action="options.php">
                <?php settings_fields('atk_ved_stock_settings'); ?>
                <?php do_settings_sections('atk_ved_stock_settings'); ?>
                
                <table class="form-table">
                    <tr>
                        <th><?php _e('Unsplash API Key', 'atk-ved'); ?></th>
                        <td>
                            <input type="text" name="atk_ved_unsplash_key" value="<?php echo esc_attr(get_option('atk_ved_unsplash_key')); ?>" class="regular-text">
                            <p class="description">
                                <?php _e('Получите ключ на', 'atk-ved'); ?> 
                                <a href="https://unsplash.com/developers" target="_blank">Unsplash Developers</a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Pexels API Key', 'atk-ved'); ?></th>
                        <td>
                            <input type="text" name="atk_ved_pexels_key" value="<?php echo esc_attr(get_option('atk_ved_pexels_key')); ?>" class="regular-text">
                            <p class="description">
                                <?php _e('Получите ключ на', 'atk-ved'); ?> 
                                <a href="https://www.pexels.com/api/" target="_blank">Pexels API</a>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(__('Сохранить настройки', 'atk-ved')); ?>
            </form>
        </div>
        
        <style>
        .atk-stock-photos-page {
            max-width: 1400px;
        }
        
        .atk-stock-notice {
            background: #fff;
            border-left: 4px solid #e31e24;
            padding: 15px 20px;
            margin: 20px 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .atk-stock-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .atk-stock-card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .atk-stock-card h2 {
            margin-top: 0;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .atk-stock-form .form-group {
            margin-bottom: 15px;
        }
        
        .atk-stock-form label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .atk-stock-form input,
        .atk-stock-form select {
            width: 100%;
            max-width: 400px;
        }
        
        .atk-stock-info {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
        }
        
        .atk-stock-info ul {
            list-style: disc;
            margin-left: 20px;
        }
        
        .atk-search-results {
            margin-top: 20px;
        }
        
        .atk-photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .atk-photo-item {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .atk-photo-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        
        .atk-photo-info {
            padding: 10px;
        }
        
        .atk-photo-info p {
            margin: 5px 0;
            font-size: 12px;
            color: #666;
        }
        
        .atk-settings-card {
            margin-top: 30px;
        }
        </style>
    </div>
    <?php
}

/**
 * Регистрация настроек
 */
function atk_ved_register_stock_settings(): void {
    register_setting('atk_ved_stock_settings', 'atk_ved_unsplash_key');
    register_setting('atk_ved_stock_settings', 'atk_ved_pexels_key');
}
add_action('admin_init', 'atk_ved_register_stock_settings');

/**
 * Шорткод для отображения галереи
 */
function atk_ved_stock_gallery_shortcode(array $atts): string {
    $atts = shortcode_atts([
        'category' => '',
        'limit' => '12',
        'columns' => '4',
        'size' => 'large',
    ], $atts);
    
    $args = [
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'post_status' => 'inherit',
        'posts_per_page' => (int)$atts['limit'],
        'meta_key' => '_stock_photo_category',
    ];
    
    if ($atts['category']) {
        $args['meta_value'] = sanitize_text_field($atts['category']);
    }
    
    $query = new WP_Query($args);
    
    if (!$query->have_posts()) {
        return '<p>' . __('Нет фотографий', 'atk-ved') . '</p>';
    }
    
    ob_start();
    ?>
    <div class="atk-stock-gallery" style="display: grid; grid-template-columns: repeat(<?php echo esc_attr($atts['columns']); ?>, 1fr); gap: 20px;">
        <?php while ($query->have_posts()): $query->the_post(); ?>
        <div class="atk-gallery-item">
            <?php echo wp_get_attachment_image(get_the_ID(), $atts['size'], false, ['loading' => 'lazy']); ?>
        </div>
        <?php endwhile; ?>
    </div>
    <?php
    wp_reset_postdata();
    
    return ob_get_clean();
}
add_shortcode('stock_gallery', 'atk_ved_stock_gallery_shortcode');

/**
 * AJAX загрузка фотографий
 */
add_action('wp_ajax_atk_download_photo', 'atk_ved_ajax_download_photo');
add_action('wp_ajax_nopriv_atk_download_photo', 'atk_ved_ajax_download_photo');

function atk_ved_ajax_download_photo(): void {
    check_ajax_referer('atk_download_photo_nonce', 'nonce');
    
    $stock = ATK_VED_Stock_Photos::get_instance();
    
    $photo_data = [
        'id' => sanitize_text_field($_POST['photo_id']),
        'url' => esc_url_raw($_POST['photo_url']),
        'alt' => sanitize_text_field($_POST['photo_alt']),
        'photographer' => sanitize_text_field($_POST['photographer']),
        'source' => sanitize_text_field($_POST['source']),
        'width' => absint($_POST['width']),
        'height' => absint($_POST['height']),
    ];
    
    $category = sanitize_text_field($_POST['category']);
    
    $attachment_id = $stock->download_photo($photo_data, $category);
    
    if ($attachment_id) {
        wp_send_json_success([
            'id' => $attachment_id,
            'url' => wp_get_attachment_url($attachment_id),
        ]);
    }
    
    wp_send_json_error(['message' => __('Ошибка загрузки', 'atk-ved')]);
}
