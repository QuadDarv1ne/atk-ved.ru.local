<?php
/**
 * Image Optimization Functions
 *
 * @package ATK_VED
 */

defined('ABSPATH') || exit;

/**
 * Добавляем поддержку WebP
 */
add_filter('upload_mimes', function($mimes) {
    $mimes['webp'] = 'image/webp';
    return $mimes;
});

/**
 * Добавляем размеры изображений для responsive
 */
add_action('after_setup_theme', function() {
    // Hero изображения
    add_image_size('hero-desktop', 1920, 1080, true);
    add_image_size('hero-tablet', 1024, 768, true);
    add_image_size('hero-mobile', 768, 576, true);
    
    // Карточки услуг
    add_image_size('service-card', 600, 400, true);
    add_image_size('service-card-2x', 1200, 800, true);
    
    // Миниатюры
    add_image_size('thumbnail-small', 150, 150, true);
    add_image_size('thumbnail-medium', 300, 300, true);
    
    // Open Graph
    add_image_size('og-image', 1200, 630, true);
});

/**
 * Автоматическое добавление srcset и sizes
 */
add_filter('wp_get_attachment_image_attributes', function($attr, $attachment, $size) {
    // Пропускаем SVG
    if (strpos($attachment->post_mime_type, 'svg') !== false) {
        return $attr;
    }
    
    // Добавляем loading="lazy" для всех изображений кроме hero
    if (!in_array($size, ['hero-desktop', 'hero-tablet', 'hero-mobile'])) {
        $attr['loading'] = 'lazy';
    }
    
    // Добавляем decoding="async"
    $attr['decoding'] = 'async';
    
    return $attr;
}, 10, 3);

/**
 * Генерация WebP версий при загрузке
 */
add_filter('wp_generate_attachment_metadata', function($metadata, $attachment_id) {
    $file = get_attached_file($attachment_id);
    
    // Проверяем что это изображение
    if (!preg_match('/\.(jpe?g|png)$/i', $file)) {
        return $metadata;
    }
    
    // Проверяем наличие GD или Imagick
    if (!function_exists('imagecreatefromjpeg') && !class_exists('Imagick')) {
        return $metadata;
    }
    
    // Создаём WebP версию
    $webp_file = preg_replace('/\.(jpe?g|png)$/i', '.webp', $file);
    
    if (class_exists('Imagick')) {
        try {
            $image = new Imagick($file);
            $image->setImageFormat('webp');
            $image->setImageCompressionQuality(85);
            $image->writeImage($webp_file);
            $image->destroy();
        } catch (Exception $e) {
            error_log('WebP conversion failed: ' . $e->getMessage());
        }
    } elseif (function_exists('imagewebp')) {
        $info = getimagesize($file);
        
        if ($info[2] === IMAGETYPE_JPEG) {
            $image = imagecreatefromjpeg($file);
        } elseif ($info[2] === IMAGETYPE_PNG) {
            $image = imagecreatefrompng($file);
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
        } else {
            return $metadata;
        }
        
        if ($image) {
            imagewebp($image, $webp_file, 85);
            imagedestroy($image);
        }
    }
    
    return $metadata;
}, 10, 2);

/**
 * Вспомогательная функция для получения WebP версии
 */
function atk_ved_get_webp_image($attachment_id, $size = 'full') {
    $image_url = wp_get_attachment_image_url($attachment_id, $size);
    
    if (!$image_url) {
        return false;
    }
    
    // Проверяем существование WebP версии
    $webp_url = preg_replace('/\.(jpe?g|png)$/i', '.webp', $image_url);
    $webp_path = str_replace(wp_get_upload_dir()['baseurl'], wp_get_upload_dir()['basedir'], $webp_url);
    
    if (file_exists($webp_path)) {
        return $webp_url;
    }
    
    return $image_url;
}

/**
 * Picture element с WebP fallback
 */
function atk_ved_picture_element($attachment_id, $size = 'full', $attr = []) {
    $image_url = wp_get_attachment_image_url($attachment_id, $size);
    $webp_url = atk_ved_get_webp_image($attachment_id, $size);
    $alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
    
    $class = $attr['class'] ?? '';
    $loading = $attr['loading'] ?? 'lazy';
    
    $html = '<picture>';
    
    // WebP source
    if ($webp_url !== $image_url) {
        $html .= sprintf('<source srcset="%s" type="image/webp">', esc_url($webp_url));
    }
    
    // Fallback
    $html .= sprintf(
        '<img src="%s" alt="%s" class="%s" loading="%s" decoding="async">',
        esc_url($image_url),
        esc_attr($alt),
        esc_attr($class),
        esc_attr($loading)
    );
    
    $html .= '</picture>';
    
    return $html;
}

/**
 * Оптимизация SVG при загрузке
 */
add_filter('wp_handle_upload_prefilter', function($file) {
    if ($file['type'] === 'image/svg+xml') {
        $svg_content = file_get_contents($file['tmp_name']);
        
        // Удаляем комментарии
        $svg_content = preg_replace('/<!--(.|\s)*?-->/', '', $svg_content);
        
        // Удаляем лишние пробелы
        $svg_content = preg_replace('/\s+/', ' ', $svg_content);
        
        // Сохраняем оптимизированный SVG
        file_put_contents($file['tmp_name'], $svg_content);
    }
    
    return $file;
});

/**
 * Добавляем поддержку AVIF (если PHP >= 8.1)
 */
if (PHP_VERSION_ID >= 80100 && function_exists('imageavif')) {
    add_filter('upload_mimes', function($mimes) {
        $mimes['avif'] = 'image/avif';
        return $mimes;
    });
}
