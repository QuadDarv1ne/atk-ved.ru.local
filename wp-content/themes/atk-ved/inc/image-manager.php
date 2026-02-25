<?php
/**
 * Система управления изображениями v3.1
 * 
 * @package ATK_VED
 * @subpackage Image_Manager
 * @version 3.1
 */

if (!defined('ABSPATH')) {
    exit;
}

class ATK_VED_Image_Manager {
    
    // Конфигурация изображений
    private static $image_config = array(
        'hero' => array(
            'main' => array(
                'name' => 'hero-main',
                'size' => '1920x1080',
                'quality' => 90,
                'placeholder' => 'images/placeholders/hero-placeholder.jpg'
            ),
            'mobile' => array(
                'name' => 'hero-mobile',
                'size' => '768x1024',
                'quality' => 85,
                'placeholder' => 'images/placeholders/hero-mobile-placeholder.jpg'
            )
        ),
        'services' => array(
            'logistics' => array(
                'name' => 'logistics-service',
                'size' => '800x600',
                'quality' => 85,
                'placeholder' => 'images/placeholders/logistics-placeholder.jpg'
            ),
            'delivery' => array(
                'name' => 'delivery-service',
                'size' => '800x600',
                'quality' => 85,
                'placeholder' => 'images/placeholders/delivery-placeholder.jpg'
            ),
            'quality' => array(
                'name' => 'quality-service',
                'size' => '800x600',
                'quality' => 85,
                'placeholder' => 'images/placeholders/quality-placeholder.jpg'
            )
        ),
        'sections' => array(
            'china' => array(
                'name' => 'china-section',
                'size' => '1200x800',
                'quality' => 90,
                'placeholder' => 'images/placeholders/china-placeholder.jpg'
            ),
            'logistics' => array(
                'name' => 'logistics-section',
                'size' => '1200x800',
                'quality' => 90,
                'placeholder' => 'images/placeholders/logistics-section-placeholder.jpg'
            ),
            'delivery' => array(
                'name' => 'delivery-section',
                'size' => '1200x800',
                'quality' => 90,
                'placeholder' => 'images/placeholders/delivery-section-placeholder.jpg'
            )
        )
    );
    
    // Пути к изображениям
    private static $image_paths = array(
        'base' => 'images/',
        'hero' => 'images/hero/',
        'sections' => 'images/sections/',
        'services' => 'images/services/',
        'placeholders' => 'images/placeholders/',
        'gallery' => 'images/gallery/'
    );
    
    /**
     * Получение URL изображения с fallback на placeholder
     */
    public static function get_image_url(string $section, string $image_name, string $type = 'webp'): string {
        $theme_url = get_template_directory_uri();
        $base_path = self::$image_paths['base'];
        
        // Проверяем существование изображения
        $image_path = self::$image_paths[$section] ?? $base_path;
        $image_file = $image_name . '.' . $type;
        $full_path = get_template_directory() . '/' . $image_path . $image_file;
        
        if (file_exists($full_path)) {
            return $theme_url . '/' . $image_path . $image_file;
        }
        
        // Проверяем JPG fallback
        $jpg_file = $image_name . '.jpg';
        $jpg_path = get_template_directory() . '/' . $image_path . $jpg_file;
        
        if (file_exists($jpg_path)) {
            return $theme_url . '/' . $image_path . $jpg_file;
        }
        
        // Возвращаем placeholder
        $config = self::$image_config[$section][$image_name] ?? null;
        if ($config && isset($config['placeholder'])) {
            return $theme_url . '/' . $config['placeholder'];
        }
        
        return $theme_url . '/images/placeholders/default-placeholder.jpg';
    }
    
    /**
     * Генерация HTML для изображения с lazy loading
     */
    public static function get_image_html(string $section, string $image_name, array $attributes = array()): string {
        $src = self::get_image_url($section, $image_name, 'webp');
        $src_jpg = self::get_image_url($section, $image_name, 'jpg');
        
        $default_attrs = array(
            'loading' => 'lazy',
            'decoding' => 'async',
            'alt' => self::get_image_alt($section, $image_name)
        );
        
        $attrs = wp_parse_args($attributes, $default_attrs);
        
        $attr_string = '';
        foreach ($attrs as $key => $value) {
            $attr_string .= sprintf(' %s="%s"', esc_attr($key), esc_attr($value));
        }
        
        // Поддержка современных браузеров с WebP
        $html = sprintf(
            '<picture>
                <source srcset="%s" type="image/webp">
                <img src="%s"%s>
            </picture>',
            esc_url($src),
            esc_url($src_jpg),
            $attr_string
        );
        
        return $html;
    }
    
    /**
     * Получение alt текста для изображения
     */
    private static function get_image_alt(string $section, string $image_name): string {
        $alts = array(
            'hero' => array(
                'hero-main' => 'Китайская логистика и доставка грузов',
                'hero-mobile' => 'Мобильная версия логистического сервиса'
            ),
            'services' => array(
                'logistics' => 'Логистические услуги по Китаю',
                'delivery' => 'Доставка грузов из Китая',
                'quality' => 'Контроль качества товаров'
            ),
            'sections' => array(
                'china' => 'Китайские товары для маркетплейсов',
                'logistics' => 'Логистика между Китаем и Россией',
                'delivery' => 'Экспресс доставка посылок'
            )
        );
        
        return $alts[$section][$image_name] ?? 'Изображение';
    }
    
    /**
     * Получение галереи изображений
     */
    public static function get_gallery_images(string $gallery_name): array {
        $galleries = array(
            'china-logistics' => array(
                'china-factory-1',
                'china-factory-2', 
                'logistics-center-1',
                'logistics-center-2',
                'delivery-truck-1',
                'delivery-truck-2'
            ),
            'services' => array(
                'quality-control-1',
                'quality-control-2',
                'warehouse-1',
                'warehouse-2',
                'packaging-1',
                'packaging-2'
            )
        );
        
        $images = $galleries[$gallery_name] ?? array();
        $result = array();
        
        foreach ($images as $image_name) {
            $result[] = array(
                'name' => $image_name,
                'url' => self::get_image_url('gallery', $image_name),
                'alt' => self::get_image_alt('gallery', $image_name)
            );
        }
        
        return $result;
    }
    
    /**
     * Оптимизация изображений
     */
    public static function optimize_image(string $image_path): bool {
        if (!function_exists('wp_get_image_editor')) {
            return false;
        }
        
        $editor = wp_get_image_editor($image_path);
        
        if (is_wp_error($editor)) {
            return false;
        }
        
        // Оптимизация размера и качества
        $editor->set_quality(85);
        $editor->resize(1920, 1080, false);
        
        $result = $editor->save($image_path);
        
        return !is_wp_error($result);
    }
    
    /**
     * Создание WebP версии изображения
     */
    public static function create_webp_version(string $image_path): bool {
        if (!function_exists('imagewebp') || !file_exists($image_path)) {
            return false;
        }
        
        $info = pathinfo($image_path);
        $webp_path = $info['dirname'] . '/' . $info['filename'] . '.webp';
        
        $image = null;
        switch (strtolower($info['extension'])) {
            case 'jpg':
            case 'jpeg':
                $image = imagecreatefromjpeg($image_path);
                break;
            case 'png':
                $image = imagecreatefrompng($image_path);
                break;
        }
        
        if (!$image) {
            return false;
        }
        
        $result = imagewebp($image, $webp_path, 85);
        imagedestroy($image);
        
        return $result;
    }
}

// Инициализация системы изображений
function atk_ved_init_image_manager(): void {
    // Регистрация функций для темы
    if (!function_exists('atk_ved_get_image')) {
        function atk_ved_get_image(string $section, string $image_name, array $attributes = array()): string {
            return ATK_VED_Image_Manager::get_image_html($section, $image_name, $attributes);
        }
    }
    
    if (!function_exists('atk_ved_get_gallery')) {
        function atk_ved_get_gallery(string $gallery_name): array {
            return ATK_VED_Image_Manager::get_gallery_images($gallery_name);
        }
    }
}
add_action('after_setup_theme', 'atk_ved_init_image_manager');