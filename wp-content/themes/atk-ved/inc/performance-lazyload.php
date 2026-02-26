<?php
/**
 * Lazy Loading и оптимизация изображений
 *
 * @package ATK_VED
 * @since 3.5.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Класс для оптимизации изображений
 */
class ATK_VED_Image_Optimizer {
    
    private static $instance = null;
    
    public static function get_instance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Получение оптимизированного URL изображения
     */
    public function get_optimized_url(string $url): string {
        // Если используется CDN
        $cdn_url = get_theme_mod('atk_ved_cdn_url', '');
        
        if ($cdn_url) {
            $cdn_url = rtrim($cdn_url, '/');
            $uploads_dir = wp_upload_dir()['baseurl'];
            $url = str_replace($uploads_dir, $cdn_url, $url);
        }
        
        return $url;
    }
    
    /**
     * Генерация srcset для responsive изображений
     */
    public function generate_srcset(int $attachment_id): string {
        $srcset = [];
        
        $sizes = [
            'thumbnail' => 150,
            'medium' => 300,
            'large' => 1024,
            'full' => 2048,
        ];
        
        foreach ($sizes as $size => $width) {
            $image = wp_get_attachment_image_src($attachment_id, $size);
            
            if ($image) {
                $url = $this->get_optimized_url($image[0]);
                $srcset[] = $url . ' ' . $width . 'w';
            }
        }
        
        return implode(', ', $srcset);
    }
    
    /**
     * Получение placeholder изображения (SVG)
     */
    public function get_placeholder(int $width = 800, int $height = 600, string $color = '#f0f0f0'): string {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '">';
        $svg .= '<rect fill="' . $color . '" width="' . $width . '" height="' . $height . '"/>';
        $svg .= '</svg>';
        
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}

/**
 * Добавление атрибутов loading и decoding к изображениям
 */
function atk_ved_add_image_attributes(array $attr): array {
    $attr['loading'] = 'lazy';
    $attr['decoding'] = 'async';
    
    // Добавляем fetchpriority для LCP изображения
    if (is_singular()) {
        global $wp_query;
        
        if ($wp_query->current_post === 0) {
            $attr['fetchpriority'] = 'high';
            $attr['loading'] = 'eager';
        }
    }
    
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'atk_ved_add_image_attributes', 10, 1);

/**
 * Замена изображений на оптимизированные в контенте
 */
function atk_ved_optimize_content_images(string $content): string {
    if (empty($content)) {
        return $content;
    }
    
    // Находим все изображения
    if (preg_match_all('/<img[^>]+src="([^"]+)"[^>]*>/i', $content, $matches)) {
        foreach ($matches[0] as $index => $img) {
            $new_img = $img;
            
            // Добавляем loading="lazy" если нет
            if (!preg_match('/loading=["\']lazy["\']/i', $new_img)) {
                $new_img = str_replace('<img', '<img loading="lazy"', $new_img);
            }
            
            // Добавляем decoding="async" если нет
            if (!preg_match('/decoding=["\']async["\']/i', $new_img)) {
                $new_img = str_replace('<img', '<img decoding="async"', $new_img);
            }
            
            // Добавляем placeholder для non-LCP изображений
            if ($index > 0 && !preg_match('/data-src=/i', $new_img)) {
                $placeholder = ATK_VED_Image_Optimizer::get_instance()->get_placeholder();
                
                // Извлекаем размеры если есть
                if (preg_match('/width="(\d+)"/', $new_img, $width_match) &&
                    preg_match('/height="(\d+)"/', $new_img, $height_match)) {
                    $placeholder = ATK_VED_Image_Optimizer::get_instance()->get_placeholder(
                        (int)$width_match[1],
                        (int)$height_match[1]
                    );
                }
                
                $new_img = str_replace('src=', 'data-src=', $new_img);
                $new_img = str_replace('<img', '<img src="' . $placeholder . '"', $new_img);
                
                // Добавляем класс для JS lazy loading
                if (!preg_match('/class="[^"]*lazy[^"]*"/i', $new_img)) {
                    $new_img = str_replace('class="', 'class="lazy ', $new_img);
                } else {
                    $new_img = str_replace('class="', 'class="lazy ', $new_img);
                }
            }
            
            $content = str_replace($img, $new_img, $content);
        }
    }
    
    return $content;
}
add_filter('the_content', 'atk_ved_optimize_content_images', 25);
add_filter('widget_text', 'atk_ved_optimize_content_images', 25);

/**
 * Оптимизация миниатюры записи
 */
function atk_ved_optimize_post_thumbnail(string $html, int $post_id, int $attachment_id): string {
    // Добавляем srcset
    $srcset = ATK_VED_Image_Optimizer::get_instance()->generate_srcset($attachment_id);
    
    if ($srcset) {
        $html = str_replace('<img', '<img srcset="' . esc_attr($srcset) . '" sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 800px"', $html);
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'atk_ved_optimize_post_thumbnail', 10, 3);

/**
 * Предзагрузка LCP изображения для главной страницы
 */
function atk_ved_preload_hero_image(): void {
    if (!is_front_page()) {
        return;
    }
    
    // Получаем URL главного изображения из опций темы
    $hero_image = get_theme_mod('atk_ved_hero_image', '');
    
    if ($hero_image) {
        echo '<link rel="preload" as="image" href="' . esc_url($hero_image) . '" fetchpriority="high">' . "\n";
    }
}
add_action('wp_head', 'atk_ved_preload_hero_image', 3);

/**
 * WebP поддержка через фильтр
 */
function atk_ved_webp_support(array $formats): array {
    $formats[] = 'webp';
    return $formats;
}
add_filter('image_editor_output_format', 'atk_ved_webp_support');

add_filter('image_editor_save_pre', function($image, $filename, $mime_type) {
    if ($mime_type === 'image/jpeg' || $mime_type === 'image/png') {
        // Генерируем WebP версию
        $webp_filename = preg_replace('/\.[^.]+$/', '', $filename) . '.webp';
        
        if (function_exists('imagewebp')) {
            $image->save($webp_filename, 'image/webp');
        }
    }
    
    return $image;
}, 10, 3);

/**
 * Добавление WebP в srcset
 */
function atk_ved_webp_srcset(array $sources, array $size_array, string $image_src, array $image_meta, int $attachment_id): array {
    foreach ($sources as $width => &$source) {
        $webp_path = preg_replace('/\.[^.]+$/', '.webp', $source['url']);
        
        if (file_exists(ABSPATH . parse_url($webp_path, PHP_URL_PATH))) {
            $sources[$width . '_webp'] = [
                'url' => $webp_path,
                'descriptor' => 'w',
                'value' => $width,
            ];
        }
    }
    
    return $sources;
}
add_filter('wp_calculate_image_srcset', 'atk_ved_webp_srcset', 10, 5);

/**
 * Lazy Loading для iframe (YouTube, Vimeo)
 */
function atk_ved_lazy_iframes(string $content): string {
    if (empty($content)) {
        return $content;
    }
    
    // Находим все iframe
    if (preg_match_all('/<iframe(.*?)src="(.*?)"(.*?)><\/iframe>/is', $content, $matches)) {
        foreach ($matches[0] as $index => $iframe) {
            $new_iframe = $iframe;
            
            // Добавляем loading="lazy" если нет
            if (!preg_match('/loading=["\']lazy["\']/i', $new_iframe)) {
                $new_iframe = str_replace('<iframe', '<iframe loading="lazy"', $new_iframe);
            }
            
            // Добавляем decoding="async" если нет
            if (!preg_match('/decoding=["\']async["\']/i', $new_iframe)) {
                $new_iframe = str_replace('<iframe', '<iframe decoding="async"', $new_iframe);
            }
            
            // Для YouTube добавляем thumbnail placeholder
            if (preg_match('/youtube\.com|youtu\.be/', $matches[2][$index])) {
                // Извлекаем ID видео
                if (preg_match('/embed\/([a-zA-Z0-9_-]+)/', $matches[2][$index], $video_id_match)) {
                    $video_id = $video_id_match[1];
                    $thumbnail = 'https://img.youtube.com/vi/' . $video_id . '/hqdefault.jpg';
                    
                    // Добавляем data-атрибуты для JS lazy loading
                    if (!preg_match('/data-thumbnail=/i', $new_iframe)) {
                        $new_iframe = str_replace('<iframe', '<iframe data-thumbnail="' . esc_attr($thumbnail) . '"', $new_iframe);
                    }
                }
            }
            
            $content = str_replace($iframe, $new_iframe, $content);
        }
    }
    
    return $content;
}
add_filter('the_content', 'atk_ved_lazy_iframes', 25);

/**
 * JavaScript для lazy loading изображений
 */
function atk_ved_lazy_load_script(): void {
    ?>
    <script>
    (function($) {
        'use strict';
        
        // Lazy Loading изображений
        function initLazyLoading() {
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            
                            if (img.dataset.src) {
                                img.src = img.dataset.src;
                                img.removeAttribute('data-src');
                                
                                img.addEventListener('load', () => {
                                    img.classList.add('lazy-loaded');
                                });
                                
                                observer.unobserve(img);
                            }
                        }
                    });
                }, {
                    rootMargin: '50px 0px',
                    threshold: 0.01
                });
                
                document.querySelectorAll('img[data-src], img.lazy').forEach(img => {
                    imageObserver.observe(img);
                });
            } else {
                // Fallback для старых браузеров
                document.querySelectorAll('img[data-src]').forEach(img => {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                });
            }
        }
        
        // Lazy Loading iframe для YouTube
        function initVideoLazyLoading() {
            document.querySelectorAll('iframe[data-thumbnail]').forEach(iframe => {
                const thumbnail = iframe.dataset.thumbnail;
                
                // Создаем preview
                const preview = document.createElement('div');
                preview.className = 'video-preview';
                preview.style.backgroundImage = 'url(' + thumbnail + ')';
                preview.style.backgroundSize = 'cover';
                preview.style.backgroundPosition = 'center';
                preview.style.position = 'relative';
                preview.style.cursor = 'pointer';
                
                // Добавляем кнопку play
                const playButton = document.createElement('div');
                playButton.innerHTML = '▶';
                playButton.style.position = 'absolute';
                playButton.style.top = '50%';
                playButton.style.left = '50%';
                playButton.style.transform = 'translate(-50%, -50%)';
                playButton.style.fontSize = '60px';
                playButton.style.color = '#fff';
                playButton.style.textShadow = '0 2px 10px rgba(0,0,0,0.5)';
                
                preview.appendChild(playButton);
                
                // Заменяем iframe на preview
                iframe.parentNode.replaceChild(preview, iframe);
                
                // При клике загружаем iframe
                preview.addEventListener('click', function() {
                    iframe.removeAttribute('loading');
                    this.parentNode.replaceChild(iframe, this);
                });
            });
        }
        
        // Инициализация при загрузке
        $(document).ready(function() {
            initLazyLoading();
            initVideoLazyLoading();
        });
        
        // Переинициализация после AJAX
        $(document).on('ajaxComplete', function() {
            setTimeout(() => {
                initLazyLoading();
                initVideoLazyLoading();
            }, 100);
        });
    })(jQuery);
    </script>
    <?php
}
add_action('wp_footer', 'atk_ved_lazy_load_script');

/**
 * Стили для lazy loading
 */
function atk_ved_lazy_load_styles(): void {
    ?>
    <style>
    /* Lazy loading placeholder */
    img[data-src],
    img.lazy {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }
    
    img.lazy-loaded {
        background: none;
        animation: none;
    }
    
    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    
    /* Video preview */
    .video-preview {
        border-radius: 8px;
        overflow: hidden;
        position: relative;
        aspect-ratio: 16 / 9;
    }
    
    .video-preview:hover {
        opacity: 0.9;
    }
    
    .video-preview .play-button {
        transition: transform 0.3s ease;
    }
    
    .video-preview:hover .play-button {
        transform: translate(-50%, -50%) scale(1.1);
    }
    </style>
    <?php
}
add_action('wp_head', 'atk_ved_lazy_load_styles');
