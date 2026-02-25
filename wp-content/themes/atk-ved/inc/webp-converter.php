<?php
/**
 * WebP конвертер изображений
 * 
 * @package ATK_VED
 * @since 2.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Автоматическая конвертация загружаемых изображений в WebP
 */
function atk_ved_convert_to_webp($metadata, $attachment_id): array {
    // Проверяем, включена ли конвертация
    if (!get_theme_mod('atk_ved_enable_webp', true)) {
        return $metadata;
    }
    
    // Проверяем поддержку WebP
    if (!function_exists('imagewebp')) {
        return $metadata;
    }
    
    $file = get_attached_file($attachment_id);
    
    if (!$file || !file_exists($file)) {
        return $metadata;
    }
    
    $mime_type = get_post_mime_type($attachment_id);
    
    // Конвертируем только JPEG и PNG
    if (!in_array($mime_type, ['image/jpeg', 'image/png'])) {
        return $metadata;
    }
    
    // Конвертируем оригинал
    atk_ved_create_webp_image($file, $mime_type);
    
    // Конвертируем все размеры
    if (isset($metadata['sizes']) && is_array($metadata['sizes'])) {
        $upload_dir = wp_upload_dir();
        $base_dir = dirname($file);
        
        foreach ($metadata['sizes'] as $size => $size_data) {
            $size_file = $base_dir . '/' . $size_data['file'];
            if (file_exists($size_file)) {
                atk_ved_create_webp_image($size_file, $mime_type);
            }
        }
    }
    
    return $metadata;
}
add_filter('wp_generate_attachment_metadata', 'atk_ved_convert_to_webp', 10, 2);

/**
 * Создание WebP версии изображения
 */
function atk_ved_create_webp_image(string $file, string $mime_type): bool {
    $webp_file = preg_replace('/\.(jpe?g|png)$/i', '.webp', $file);
    
    // Если WebP уже существует, пропускаем
    if (file_exists($webp_file)) {
        return true;
    }
    
    $quality = get_theme_mod('atk_ved_webp_quality', 85);
    
    try {
        $image = null;
        
        switch ($mime_type) {
            case 'image/jpeg':
                $image = @imagecreatefromjpeg($file);
                break;
            case 'image/png':
                $image = @imagecreatefrompng($file);
                // Сохраняем прозрачность
                if ($image) {
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                }
                break;
        }
        
        if (!$image) {
            return false;
        }
        
        // Создаем WebP
        $result = imagewebp($image, $webp_file, $quality);
        imagedestroy($image);
        
        return $result;
        
    } catch (Exception $e) {
        error_log('WebP conversion error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Автоматическая подстановка WebP в HTML
 */
function atk_ved_replace_images_with_webp(string $content): string {
    // Проверяем, включена ли конвертация
    if (!get_theme_mod('atk_ved_enable_webp', true)) {
        return $content;
    }
    
    // Проверяем поддержку WebP в браузере через cookie
    $supports_webp = isset($_COOKIE['webp_support']) && $_COOKIE['webp_support'] === '1';
    
    if (!$supports_webp) {
        return $content;
    }
    
    // Заменяем img теги на picture с WebP
    $content = preg_replace_callback(
        '/<img([^>]+)src=["\']([^"\']+\.(jpe?g|png))["\']([^>]*)>/i',
        function($matches) {
            $img_attrs = $matches[1] . $matches[4];
            $img_src = $matches[2];
            $webp_src = preg_replace('/\.(jpe?g|png)$/i', '.webp', $img_src);
            
            // Проверяем существование WebP файла
            $webp_path = str_replace(home_url('/'), ABSPATH, $webp_src);
            
            if (!file_exists($webp_path)) {
                return $matches[0]; // Возвращаем оригинал
            }
            
            return sprintf(
                '<picture><source srcset="%s" type="image/webp"><img%ssrc="%s"%s></picture>',
                esc_url($webp_src),
                $matches[1],
                esc_url($img_src),
                $matches[4]
            );
        },
        $content
    );
    
    return $content;
}
add_filter('the_content', 'atk_ved_replace_images_with_webp', 100);
add_filter('post_thumbnail_html', 'atk_ved_replace_images_with_webp', 100);

/**
 * JavaScript для определения поддержки WebP
 */
function atk_ved_webp_detection_script(): void {
    if (!get_theme_mod('atk_ved_enable_webp', true)) {
        return;
    }
    ?>
    <script>
    (function() {
        // Проверяем поддержку WebP
        function checkWebPSupport(callback) {
            var webP = new Image();
            webP.onload = webP.onerror = function() {
                callback(webP.height === 2);
            };
            webP.src = 'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA';
        }
        
        // Сохраняем результат в cookie
        checkWebPSupport(function(supported) {
            document.cookie = 'webp_support=' + (supported ? '1' : '0') + '; path=/; max-age=31536000';
            
            // Добавляем класс к body
            if (supported) {
                document.documentElement.classList.add('webp-supported');
            } else {
                document.documentElement.classList.add('webp-not-supported');
            }
        });
    })();
    </script>
    <?php
}
add_action('wp_head', 'atk_ved_webp_detection_script', 1);

/**
 * Массовая конвертация существующих изображений
 */
function atk_ved_bulk_convert_to_webp(): void {
    // Проверка прав доступа
    if (!current_user_can('manage_options')) {
        wp_die('Недостаточно прав');
    }
    
    // Проверка nonce
    check_admin_referer('atk_ved_bulk_webp_convert');
    
    $args = array(
        'post_type'      => 'attachment',
        'post_mime_type' => array('image/jpeg', 'image/png'),
        'post_status'    => 'inherit',
        'posts_per_page' => -1,
    );
    
    $attachments = get_posts($args);
    $converted = 0;
    $errors = 0;
    
    foreach ($attachments as $attachment) {
        $file = get_attached_file($attachment->ID);
        $mime_type = get_post_mime_type($attachment->ID);
        
        if ($file && file_exists($file)) {
            if (atk_ved_create_webp_image($file, $mime_type)) {
                $converted++;
                
                // Конвертируем размеры
                $metadata = wp_get_attachment_metadata($attachment->ID);
                if (isset($metadata['sizes']) && is_array($metadata['sizes'])) {
                    $base_dir = dirname($file);
                    foreach ($metadata['sizes'] as $size_data) {
                        $size_file = $base_dir . '/' . $size_data['file'];
                        if (file_exists($size_file)) {
                            atk_ved_create_webp_image($size_file, $mime_type);
                        }
                    }
                }
            } else {
                $errors++;
            }
        }
    }
    
    wp_redirect(add_query_arg(array(
        'page' => 'atk-ved-webp',
        'converted' => $converted,
        'errors' => $errors
    ), admin_url('admin.php')));
    exit;
}
add_action('admin_post_atk_ved_bulk_webp_convert', 'atk_ved_bulk_convert_to_webp');

/**
 * Страница настроек WebP в админке
 */
function atk_ved_webp_admin_page(): void {
    add_submenu_page(
        'themes.php',
        'WebP Конвертер',
        'WebP Конвертер',
        'manage_options',
        'atk-ved-webp',
        'atk_ved_webp_admin_page_content'
    );
}
add_action('admin_menu', 'atk_ved_webp_admin_page');

function atk_ved_webp_admin_page_content(): void {
    $webp_supported = function_exists('imagewebp');
    
    // Подсчет изображений
    $total_images = wp_count_posts('attachment')->inherit ?? 0;
    
    $args = array(
        'post_type'      => 'attachment',
        'post_mime_type' => array('image/jpeg', 'image/png'),
        'post_status'    => 'inherit',
        'posts_per_page' => -1,
        'fields'         => 'ids'
    );
    $convertible_images = count(get_posts($args));
    
    ?>
    <div class="wrap">
        <h1>WebP Конвертер</h1>
        
        <?php if (isset($_GET['converted'])): ?>
            <div class="notice notice-success is-dismissible">
                <p>Конвертировано изображений: <?php echo intval($_GET['converted']); ?></p>
                <?php if (isset($_GET['errors']) && $_GET['errors'] > 0): ?>
                    <p>Ошибок: <?php echo intval($_GET['errors']); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <h2>Статус</h2>
            <table class="form-table">
                <tr>
                    <th>Поддержка WebP:</th>
                    <td>
                        <?php if ($webp_supported): ?>
                            <span style="color: green;">✓ Поддерживается</span>
                        <?php else: ?>
                            <span style="color: red;">✗ Не поддерживается (требуется GD библиотека с WebP)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Всего изображений:</th>
                    <td><?php echo esc_html($total_images); ?></td>
                </tr>
                <tr>
                    <th>Доступно для конвертации:</th>
                    <td><?php echo esc_html($convertible_images); ?> (JPEG/PNG)</td>
                </tr>
            </table>
        </div>
        
        <?php if ($webp_supported): ?>
            <div class="card">
                <h2>Массовая конвертация</h2>
                <p>Конвертировать все существующие JPEG и PNG изображения в WebP формат.</p>
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                    <?php wp_nonce_field('atk_ved_bulk_webp_convert'); ?>
                    <input type="hidden" name="action" value="atk_ved_bulk_webp_convert">
                    <p>
                        <button type="submit" class="button button-primary" onclick="return confirm('Начать конвертацию? Это может занять некоторое время.');">
                            Конвертировать все изображения
                        </button>
                    </p>
                    <p class="description">
                        <strong>Внимание:</strong> Процесс может занять продолжительное время в зависимости от количества изображений.
                        Оригинальные файлы не будут удалены.
                    </p>
                </form>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <h2>Настройки</h2>
            <p>Настройки WebP доступны в разделе <a href="<?php echo admin_url('customize.php'); ?>">Внешний вид → Настроить → WebP</a></p>
        </div>
    </div>
    <?php
}

/**
 * Настройки WebP в Customizer
 */
function atk_ved_webp_customizer($wp_customize): void {
    // Секция WebP
    $wp_customize->add_section('atk_ved_webp', array(
        'title'    => __('WebP Изображения', 'atk-ved'),
        'priority' => 37,
        'description' => __('Настройка автоматической конвертации изображений в WebP', 'atk-ved')
    ));

    // Включить WebP
    $wp_customize->add_setting('atk_ved_enable_webp', array(
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));

    $wp_customize->add_control('atk_ved_enable_webp', array(
        'label'       => __('Включить WebP', 'atk-ved'),
        'section'     => 'atk_ved_webp',
        'type'        => 'checkbox',
        'description' => __('Автоматически конвертировать изображения в WebP формат', 'atk-ved')
    ));

    // Качество WebP
    $wp_customize->add_setting('atk_ved_webp_quality', array(
        'default'           => 85,
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control('atk_ved_webp_quality', array(
        'label'       => __('Качество WebP', 'atk-ved'),
        'section'     => 'atk_ved_webp',
        'type'        => 'number',
        'description' => __('От 1 до 100. Рекомендуется 80-90', 'atk-ved'),
        'input_attrs' => array(
            'min'  => 1,
            'max'  => 100,
            'step' => 1,
        ),
    ));
}
add_action('customize_register', 'atk_ved_webp_customizer');
