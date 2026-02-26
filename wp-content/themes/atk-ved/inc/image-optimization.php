<?php
/**
 * Оптимизация изображений — WebP, lazy loading, адаптивные изображения
 *
 * @package ATK_VED
 * @since   3.1.0
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Класс для оптимизации изображений
 */
class ATK_VED_Image_Optimizer {

    private static ?self $instance = null;

    public static function get_instance(): self {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_filter( 'wp_generate_attachment_metadata', [ $this, 'generate_webp_on_upload' ], 10, 2 );
        add_filter( 'wp_get_attachment_image_src', [ $this, 'use_webp_if_exists' ], 10, 4 );
        add_filter( 'wp_get_attachment_image_attributes', [ $this, 'add_lazy_loading' ], 10, 3 );
        add_filter( 'wp_get_attachment_image_attributes', [ $this, 'add_srcset_webp' ], 20, 3 );
        add_filter( 'post_thumbnail_html', [ $this, 'optimize_post_thumbnail' ], 10, 5 );
        add_filter( 'content_img_tag', [ $this, 'optimize_content_images' ], 10, 3 );
        add_action( 'wp_head', [ $this, 'add_preload_hero_images' ], 10 );
        add_filter( 'wp_lazy_loading_enabled', [ $this, 'selective_lazy_loading' ], 10, 3 );
    }

    /**
     * Генерация WebP при загрузке изображения
     */
    public function generate_webp_on_upload( array $metadata, int $attachment_id ): array {
        $file_path = get_attached_file( $attachment_id );
        
        if ( ! $file_path || ! file_exists( $file_path ) ) {
            return $metadata;
        }

        $file_info = pathinfo( $file_path );
        $extension = strtolower( $file_info['extension'] ?? '' );

        // Пропускаем если уже WebP или не поддерживаемый формат
        if ( $extension === 'webp' || ! in_array( $extension, [ 'jpg', 'jpeg', 'png', 'gif' ], true ) ) {
            return $metadata;
        }

        // Генерируем WebP для всех размеров
        $this->generate_webp_sizes( $attachment_id, $metadata );

        return $metadata;
    }

    /**
     * Генерация WebP для всех размеров изображения
     */
    private function generate_webp_sizes( int $attachment_id, array $metadata ): void {
        $file_path = get_attached_file( $attachment_id );
        $file_info = pathinfo( $file_path );
        
        // Основной файл
        $this->convert_to_webp( $file_path );

        // Размеры
        if ( ! empty( $metadata['sizes'] ) ) {
            foreach ( $metadata['sizes'] as $size => $size_data ) {
                $size_file = $file_info['dirname'] . '/' . $size_data['file'];
                if ( file_exists( $size_file ) ) {
                    $this->convert_to_webp( $size_file );
                }
            }
        }
    }

    /**
     * Конвертация файла в WebP
     */
    private function convert_to_webp( string $file_path ): void {
        if ( ! function_exists( 'imagewebp' ) ) {
            return;
        }

        $file_info = pathinfo( $file_path );
        $extension = strtolower( $file_info['extension'] ?? '' );
        $webp_path = $file_info['dirname'] . '/' . $file_info['filename'] . '.webp';

        // Не создаём если уже существует
        if ( file_exists( $webp_path ) ) {
            return;
        }

        $image = null;

        switch ( $extension ) {
            case 'jpg':
            case 'jpeg':
                $image = imagecreatefromjpeg( $file_path );
                break;
            case 'png':
                $image = imagecreatefrompng( $file_path );
                imagesavealpha( $image, true );
                break;
            case 'gif':
                $image = imagecreatefromgif( $file_path );
                break;
        }

        if ( $image ) {
            imagewebp( $image, $webp_path, 85 );
            imagedestroy( $image );
        }
    }

    /**
     * Использование WebP если существует
     */
    public function use_webp_if_exists( $image, $attachment_id, $size, $icon ): ?array {
        if ( ! $image ) {
            return $image;
        }

        $image_path = $image[0];
        $webp_path = $this->get_webp_path( $image_path );

        if ( $webp_path && file_exists( $webp_path ) ) {
            $image[0] = $this->get_webp_url( $image_path );
        }

        return $image;
    }

    /**
     * Добавление lazy loading
     */
    public function add_lazy_loading( array $attr, WP_Post $attachment, array $size ): array {
        // Не добавляем lazy для above-the-fold изображений
        if ( $this->is_above_fold( $attr ) ) {
            $attr['loading'] = 'eager';
            $attr['fetchpriority'] = 'high';
        } else {
            $attr['loading'] = 'lazy';
            $attr['decoding'] = 'async';
        }

        return $attr;
    }

    /**
     * Проверка: изображение выше линии сгиба
     */
    private function is_above_fold( array $attr ): bool {
        // Проверяем по классам или позициям
        $above_fold_classes = [
            'hero-image',
            'header-logo',
            'above-fold',
            'first-image',
        ];

        $class = $attr['class'] ?? '';

        foreach ( $above_fold_classes as $af_class ) {
            if ( strpos( $class, $af_class ) !== false ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Добавление WebP в srcset
     */
    public function add_srcset_webp( array $attr, WP_Post $attachment, array $size ): array {
        $srcset = $attr['srcset'] ?? '';

        if ( ! $srcset ) {
            return $attr;
        }

        $webp_srcset = $this->generate_webp_srcset( $attachment->ID, $size );

        if ( $webp_srcset ) {
            $attr['type'] = 'image/webp';
            $attr['srcset'] = $webp_srcset;
        }

        return $attr;
    }

    /**
     * Генерация WebP srcset
     */
    private function generate_webp_srcset( int $attachment_id, string $size ): string {
        $metadata = wp_get_attachment_metadata( $attachment_id );
        
        if ( ! $metadata || empty( $metadata['sizes'] ) ) {
            return '';
        }

        $file_path = get_attached_file( $attachment_id );
        $file_info = pathinfo( $file_path );
        $upload_dir = wp_upload_dir();

        $srcset = [];

        // Добавляем основной размер
        $webp_path = $file_info['dirname'] . '/' . $file_info['filename'] . '.webp';
        if ( file_exists( $webp_path ) ) {
            $width = $metadata['width'];
            $url = str_replace( $upload_dir['basedir'], $upload_dir['baseurl'], $webp_path );
            $srcset[] = $url . " {$width}w";
        }

        // Добавляем размеры
        foreach ( $metadata['sizes'] as $size_name => $size_data ) {
            $webp_file = $file_info['dirname'] . '/' . $size_data['file'];
            $webp_file = preg_replace( '/\.[^.]+$/', '.webp', $webp_file );

            if ( file_exists( $webp_file ) ) {
                $url = str_replace( $upload_dir['basedir'], $upload_dir['baseurl'], $webp_file );
                $srcset[] = $url . " {$size_data['width']}w";
            }
        }

        return implode( ', ', $srcset );
    }

    /**
     * Оптимизация post thumbnail
     */
    public function optimize_post_thumbnail( string $html, int $post_id, ?int $post_thumbnail_id, string $size, array $attr ): string {
        if ( ! $post_thumbnail_id ) {
            return $html;
        }

        // Добавляем picture tag с WebP
        if ( get_theme_mod( 'atk_ved_use_picture_tag', true ) ) {
            return $this->wrap_in_picture_tag( $html, $post_thumbnail_id, $size );
        }

        return $html;
    }

    /**
     * Оптимизация изображений в контенте
     */
    public function optimize_content_images( string $filtered_image, string $context, array $attributes ): string {
        // Добавляем loading="lazy" если нет
        if ( ! isset( $attributes['loading'] ) ) {
            $filtered_image = str_replace( '<img ', '<img loading="lazy" decoding="async" ', $filtered_image );
        }

        // Добавляем fetchpriority для первого изображения
        if ( $this->is_first_content_image( $context ) ) {
            $filtered_image = str_replace( '<img ', '<img fetchpriority="high" ', $filtered_image );
        }

        return $filtered_image;
    }

    /**
     * Проверка: первое изображение в контенте
     */
    private function is_first_content_image( string $context ): bool {
        static $first_image_processed = false;

        if ( $context === 'the_content' && ! $first_image_processed ) {
            $first_image_processed = true;
            return true;
        }

        return false;
    }

    /**
     * Обёртывание в picture tag
     */
    private function wrap_in_picture_tag( string $html, int $attachment_id, string $size ): string {
        $metadata = wp_get_attachment_metadata( $attachment_id );
        $file_path = get_attached_file( $attachment_id );
        $file_info = pathinfo( $file_path );
        $upload_dir = wp_upload_dir();

        // Получаем WebP URL
        $webp_path = $file_info['dirname'] . '/' . $file_info['filename'] . '.webp';
        
        if ( ! file_exists( $webp_path ) ) {
            return $html;
        }

        $webp_url = str_replace( $upload_dir['basedir'], $upload_dir['baseurl'], $webp_path );
        
        // Извлекаем src из оригинального HTML
        preg_match( '/src="([^"]+)"/', $html, $src_matches );
        $original_src = $src_matches[1] ?? '';

        // Извлекаем классы
        preg_match( '/class="([^"]+)"/', $html, $class_matches );
        $classes = $class_matches[1] ?? '';

        // Извлекаем alt
        preg_match( '/alt="([^"]+)"/', $html, $alt_matches );
        $alt = $alt_matches[1] ?? '';

        // Извлекаем размеры
        preg_match( '/width="(\d+)"/', $html, $width_matches );
        preg_match( '/height="(\d+)"/', $html, $height_matches );
        $width = $width_matches[1] ?? $metadata['width'] ?? '';
        $height = $height_matches[1] ?? $metadata['height'] ?? '';

        // Формируем picture tag
        $picture = sprintf(
            '<picture class="%s">
                <source srcset="%s" type="image/webp">
                <img src="%s" alt="%s" width="%s" height="%s" loading="lazy" decoding="async">
            </picture>',
            esc_attr( $classes ),
            esc_url( $webp_url ),
            esc_url( $original_src ),
            esc_attr( $alt ),
            esc_attr( $width ),
            esc_attr( $height )
        );

        return $picture;
    }

    /**
     * Предзагрузка hero изображений
     */
    public function add_preload_hero_images(): void {
        if ( ! is_front_page() ) {
            return;
        }

        // Получаем hero изображение
        $hero_image_id = get_theme_mod( 'atk_ved_hero_image' );

        if ( $hero_image_id ) {
            $hero_image_url = wp_get_attachment_url( $hero_image_id );
            $hero_image_src = wp_get_attachment_image_src( $hero_image_id, 'full' );

            if ( $hero_image_url ) {
                printf(
                    '<link rel="preload" as="image" href="%s" imagesrcset="%s" imagesizes="100vw">' . "\n",
                    esc_url( $hero_image_url ),
                    esc_attr( $hero_image_src[3] ?? '' )
                );
            }
        }
    }

    /**
     * Селективный lazy loading
     */
    public function selective_lazy_loading( bool $enabled, string $tag, string $context ): bool {
        // Отключаем lazy для admin bar
        if ( is_admin() ) {
            return false;
        }

        // Отключаем для first contentful paint изображений
        if ( $context === 'the_content' && $this->is_first_content_image( $context ) ) {
            return false;
        }

        return true;
    }

    /**
     * Получение пути к WebP
     */
    private function get_webp_path( string $image_path ): ?string {
        $file_info = pathinfo( $image_path );
        
        if ( ! isset( $file_info['filename'] ) ) {
            return null;
        }

        return $file_info['dirname'] . '/' . $file_info['filename'] . '.webp';
    }

    /**
     * Получение URL к WebP
     */
    private function get_webp_url( string $image_url ): string {
        return preg_replace( '/\.(jpg|jpeg|png|gif)$/i', '.webp', $image_url );
    }

    /**
     * Очистка кэша WebP при удалении вложения
     */
    public function clean_webp_on_delete( int $post_id ): void {
        $file_path = get_attached_file( $post_id );
        
        if ( ! $file_path ) {
            return;
        }

        $file_info = pathinfo( $file_path );
        $webp_path = $file_info['dirname'] . '/' . $file_info['filename'] . '.webp';

        if ( file_exists( $webp_path ) ) {
            wp_delete_file( $webp_path );
        }
    }
}

// Инициализация
function atk_ved_init_image_optimizer(): void {
    ATK_VED_Image_Optimizer::get_instance();
}
add_action( 'after_setup_theme', 'atk_ved_init_image_optimizer' );

// Очистка WebP при удалении вложения
add_action( 'delete_attachment', [ ATK_VED_Image_Optimizer::get_instance(), 'clean_webp_on_delete' ] );


// ============================================================================
// ДОПОЛНИТЕЛЬНЫЕ ФИЛЬТРЫ
// ============================================================================

/**
 * Отключение emoji для производительности
 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

/**
 * Удаление DNS prefetch для emoji
 */
add_filter( 'emoji_svg_url', '__return_false' );

/**
 * Отключение Gutenberg CSS на фронтенде если не используется
 */
add_action( 'wp_enqueue_scripts', function() {
    if ( ! is_singular() || ! has_blocks() ) {
        wp_dequeue_style( 'wp-block-library' );
        wp_dequeue_style( 'wp-block-library-theme' );
    }
}, 100 );
