<?php
/**
 * WebP Image Converter
 * @package ATK_VED
 */

declare(strict_types=1);

if (!defined('ABSPATH')) exit;

class ATK_VED_WebP_Converter {
    
    public static function init(): void {
        add_filter('wp_generate_attachment_metadata', [__CLASS__, 'generate_webp'], 10, 2);
        add_filter('wp_get_attachment_image_src', [__CLASS__, 'replace_with_webp'], 10, 4);
    }
    
    public static function generate_webp(array $metadata, int $attachment_id): array {
        $file = get_attached_file($attachment_id);
        
        if (!$file || !file_exists($file)) return $metadata;
        
        $mime = get_post_mime_type($attachment_id);
        if (!in_array($mime, ['image/jpeg', 'image/png'], true)) return $metadata;
        
        self::convert_to_webp($file);
        
        if (!empty($metadata['sizes'])) {
            $upload_dir = wp_upload_dir();
            $base_dir = dirname($file);
            
            foreach ($metadata['sizes'] as $size) {
                $size_file = $base_dir . '/' . $size['file'];
                if (file_exists($size_file)) {
                    self::convert_to_webp($size_file);
                }
            }
        }
        
        return $metadata;
    }
    
    private static function convert_to_webp(string $file): bool {
        $webp_file = preg_replace('/\.(jpe?g|png)$/i', '.webp', $file);
        
        if (file_exists($webp_file)) return true;
        
        $image = null;
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        
        if ($ext === 'jpg' || $ext === 'jpeg') {
            $image = @imagecreatefromjpeg($file);
        } elseif ($ext === 'png') {
            $image = @imagecreatefrompng($file);
            if ($image) {
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
            }
        }
        
        if (!$image) return false;
        
        $result = @imagewebp($image, $webp_file, 85);
        imagedestroy($image);
        
        return $result;
    }
    
    public static function replace_with_webp($image, int $attachment_id, $size, bool $icon) {
        if (!$image || !is_array($image)) return $image;
        
        $webp_url = preg_replace('/\.(jpe?g|png)$/i', '.webp', $image[0]);
        $webp_path = str_replace(wp_upload_dir()['baseurl'], wp_upload_dir()['basedir'], $webp_url);
        
        if (file_exists($webp_path)) {
            $image[0] = $webp_url;
        }
        
        return $image;
    }
}

ATK_VED_WebP_Converter::init();
