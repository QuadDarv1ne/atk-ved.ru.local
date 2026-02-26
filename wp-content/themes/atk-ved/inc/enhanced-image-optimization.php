<?php
/**
 * Улучшенная система оптимизации изображений
 *
 * @package ATK_VED
 * @since 3.5.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Класс для улучшенной оптимизации изображений
 */
class ATK_VED_Enhanced_Image_Optimization {
    
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
        // Добавление WebP поддержки
        add_filter('wp_handle_upload', [$this, 'convert_to_webp']);
        
        // Оптимизация изображений при выводе
        add_filter('wp_get_attachment_image_attributes', [$this, 'add_responsive_attributes'], 10, 3);
        
        // Оптимизация HTML-кода изображений
        add_filter('the_content', [$this, 'optimize_content_images'], 25);
        
        // Добавление srcset для адаптивных изображений
        add_filter('wp_calculate_image_srcset', [$this, 'enhance_srcset'], 10, 5);
        
        // Поддержка современных форматов
        add_filter('image_editor_output_format', [$this, 'support_modern_formats']);
    }
    
    /**
     * Преобразование изображений в WebP формат
     */
    public function convert_to_webp(array $upload): array {
        if ($this->is_image_file($upload['file'])) {
            $this->generate_webp_version($upload['file']);
        }
        
        return $upload;
    }
    
    /**
     * Проверка, является ли файл изображением
     */
    private function is_image_file(string $file): bool {
        $image_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        
        return in_array($extension, $image_extensions, true);
    }
    
    /**
     * Генерация WebP версии изображения
     */
    private function generate_webp_version(string $file): void {
        if (!function_exists('imagewebp')) {
            return;
        }
        
        $image_info = getimagesize($file);
        if (!$image_info) {
            return;
        }
        
        $webp_file = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $file);
        
        switch ($image_info['mime']) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($file);
                break;
            case 'image/png':
                $image = imagecreatefrompng($file);
                imagesavealpha($image, true);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($file);
                imagesavealpha($image, true);
                break;
            default:
                return;
        }
        
        if ($image) {
            imagewebp($image, $webp_file, 85);
            imagedestroy($image);
        }
    }
    
    /**
     * Добавление атрибутов для адаптивных изображений
     */
    public function add_responsive_attributes(array $attr, $attachment = null, $size = null): array {
        if (!is_admin() && isset($attachment)) {
            // Добавляем loading="lazy" для изображений, кроме LCP
            if (!isset($attr['loading']) && !is_admin()) {
                global $wp_query;
                
                // Для первого изображения на странице используем eager
                if ($wp_query->current_post === 0) {
                    $attr['loading'] = 'eager';
                } else {
                    $attr['loading'] = 'lazy';
                }
            }
            
            // Добавляем decoding для улучшения производительности
            if (!isset($attr['decoding'])) {
                $attr['decoding'] = 'async';
            }
        }
        
        return $attr;
    }
    
    /**
     * Оптимизация изображений в контенте
     */
    public function optimize_content_images(string $content): string {
        if (empty($content)) {
            return $content;
        }
        
        // Находим все изображения в контенте
        if (preg_match_all('/<img[^>]+src="([^"]+)"[^>]*>/i', $content, $matches)) {
            foreach ($matches[0] as $index => $img) {
                $new_img = $img;
                
                // Добавляем loading="lazy" если отсутствует
                if (!preg_match('/loading=["\']lazy["\']/i', $new_img) && 
                    !preg_match('/loading=["\']eager["\']/i', $new_img)) {
                    $new_img = str_replace('<img', '<img loading="lazy"', $new_img);
                }
                
                // Добавляем decoding="async" если отсутствует
                if (!preg_match('/decoding=["\']async["\']/i', $new_img)) {
                    $new_img = str_replace('<img', '<img decoding="async"', $new_img);
                }
                
                // Для первых изображений добавляем fetchpriority
                if ($index === 0 && !preg_match('/fetchpriority=/i', $new_img)) {
                    $new_img = str_replace('<img', '<img fetchpriority="high"', $new_img);
                }
                
                $content = str_replace($img, $new_img, $content);
            }
        }
        
        return $content;
    }
    
    /**
     * Улучшение srcset для адаптивных изображений
     */
    public function enhance_srcset(array $sources, array $size_array, string $src, array $image_meta, int $attachment_id): array {
        // Добавляем WebP версии в srcset если они существуют
        foreach ($sources as $width => &$source) {
            $webp_path = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $source['url']);
            
            // Проверяем, существует ли WebP версия
            $webp_file = str_replace(home_url('/'), ABSPATH, $webp_path);
            if (file_exists($webp_file)) {
                // Добавляем WebP версию с тем же размером
                $sources[$width . '_webp'] = [
                    'url' => $webp_path,
                    'descriptor' => 'w',
                    'value' => $width,
                ];
            }
        }
        
        return $sources;
    }
    
    /**
     * Поддержка современных форматов изображений
     */
    public function support_modern_formats(array $formats): array {
        $formats[] = 'webp';
        
        // Если поддерживается AVIF, добавляем его
        if (function_exists('imageavif')) {
            $formats[] = 'avif';
        }
        
        return $formats;
    }
    
    /**
     * Генерация SVG placeholder для изображений
     */
    public function generate_svg_placeholder(int $width = 800, int $height = 600, string $color = '#f0f0f0'): string {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '">';
        $svg .= '<rect fill="' . $color . '" width="' . $width . '" height="' . $height . '"/>';
        $svg .= '</svg>';
        
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
    
    /**
     * Lazy loading для iframe (видео)
     */
    public function lazy_load_iframes(string $content): string {
        if (empty($content)) {
            return $content;
        }
        
        // Находим все iframe
        if (preg_match_all('/<iframe(.*?)src="(.*?)"(.*?)><\/iframe>/is', $content, $matches)) {
            foreach ($matches[0] as $index => $iframe) {
                $new_iframe = $iframe;
                
                // Добавляем loading="lazy" если отсутствует
                if (!preg_match('/loading=["\']lazy["\']/i', $new_iframe)) {
                    $new_iframe = str_replace('<iframe', '<iframe loading="lazy"', $new_iframe);
                }
                
                // Для YouTube добавляем placeholder
                if (preg_match('/youtube\.com|youtu\.be/', $matches[2][$index])) {
                    if (preg_match('/embed\/([a-zA-Z0-9_-]+)/', $matches[2][$index], $video_id_match)) {
                        $video_id = $video_id_match[1];
                        $thumbnail = 'https://img.youtube.com/vi/' . $video_id . '/hqdefault.jpg';
                        
                        // Добавляем data-атрибуты для JS lazy loading
                        if (!preg_match('/data-thumbnail=/i', $new_iframe)) {
                            $new_iframe = str_replace('<iframe', '<iframe data-thumbnail="' . esc_url($thumbnail) . '"', $new_iframe);
                        }
                    }
                }
                
                $content = str_replace($iframe, $new_iframe, $content);
            }
        }
        
        return $content;
    }
}

// Инициализация улучшенной системы оптимизации изображений
ATK_VED_Enhanced_Image_Optimization::get_instance();

/**
 * Функция для получения адаптивного изображения
 */
function atk_ved_get_responsive_image(int $attachment_id, string $size = 'full', array $additional_classes = []): string {
    $classes = ['responsive-image'];
    $classes = array_merge($classes, $additional_classes);
    
    $image_attrs = [
        'class' => implode(' ', $classes),
        'loading' => 'lazy',
        'decoding' => 'async'
    ];
    
    return wp_get_attachment_image($attachment_id, $size, false, $image_attrs);
}

/**
 * Функция для получения WebP версии изображения
 */
function atk_ved_get_webp_version(int $attachment_id): ?string {
    $image_path = get_attached_file($attachment_id);
    
    if (!$image_path) {
        return null;
    }
    
    $webp_path = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $image_path);
    
    if (file_exists($webp_path)) {
        return str_replace(ABSPATH, home_url('/'), $webp_path);
    }
    
    return null;
}

// Подключаем функции оптимизации к различным хукам
add_filter('the_content', [ATK_VED_Enhanced_Image_Optimization::get_instance(), 'lazy_load_iframes'], 25);

// Обеспечиваем, что компоненты оптимизации изображений подключаются
add_action('after_setup_theme', function() {
    if (get_template() === 'atk-ved') {
        require_once get_template_directory() . '/inc/enhanced-image-optimization.php';
    }
}, 12);