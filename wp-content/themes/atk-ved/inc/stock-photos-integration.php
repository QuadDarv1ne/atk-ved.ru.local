<?php
/**
 * Интеграция стоковых фотографий в секции сайта
 *
 * @package ATK_VED
 * @since 3.6.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Получение случайной фотографии из категории
 */
function atk_ved_get_stock_photo(string $category = 'china', string $size = 'large'): string {
    $args = [
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'post_status' => 'inherit',
        'posts_per_page' => 1,
        'meta_key' => '_stock_photo_category',
        'meta_value' => $category,
        'orderby' => 'rand',
    ];
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        $query->the_post();
        $url = wp_get_attachment_image_url(get_the_ID(), $size);
        wp_reset_postdata();
        
        if ($url) {
            return $url;
        }
    }
    
    // Возвращаем placeholder если нет фото
    return atk_ved_get_photo_placeholder($category);
}

/**
 * Placeholder для фотографий
 */
function atk_ved_get_photo_placeholder(string $category): string {
    $placeholders = [
        'china' => 'https://images.unsplash.com/photo-1548266652-99cf277df1ca?w=1200',
        'factory' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=1200',
        'shipping' => 'https://images.unsplash.com/photo-1494412574643-35d324698420?w=1200',
        'office' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=1200',
        'products' => 'https://images.unsplash.com/photo-1556740738-b6a63e27c4df?w=1200',
    ];
    
    return $placeholders[$category] ?? $placeholders['china'];
}

/**
 * Шорткод: Hero секция с фото Китая
 */
function atk_ved_hero_with_china_photo_shortcode(array $atts): string {
    $atts = shortcode_atts([
        'title' => __('Доставка товаров из Китая', 'atk-ved'),
        'subtitle' => __('Надёжный партнёр для вашего бизнеса', 'atk-ved'),
        'cta_text' => __('Оставить заявку', 'atk-ved'),
        'photo_category' => 'china',
    ], $atts);
    
    $background_image = atk_ved_get_stock_photo($atts['photo_category'], 'full');
    
    ob_start();
    ?>
    <section class="hero-section-china" style="background-image: url('<?php echo esc_url($background_image); ?>');">
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1><?php echo esc_html($atts['title']); ?></h1>
                    <p class="hero-subtitle"><?php echo esc_html($atts['subtitle']); ?></p>
                    
                    <ul class="hero-features">
                        <li><?php _e('Доставка от 5 дней', 'atk-ved'); ?></li>
                        <li><?php _e('Таможенное оформление', 'atk-ved'); ?></li>
                        <li><?php _e('Страхование грузов', 'atk-ved'); ?></li>
                        <li><?php _e('Отслеживание 24/7', 'atk-ved'); ?></li>
                    </ul>
                    
                    <button class="cta-button" onclick="document.getElementById('contact').scrollIntoView({behavior: 'smooth'})">
                        <?php echo esc_html($atts['cta_text']); ?>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <style>
    .hero-section-china {
        position: relative;
        min-height: 100vh;
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        display: flex;
        align-items: center;
        padding: 80px 0;
    }

    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(
            135deg,
            rgba(0, 0, 0, 0.7) 0%,
            rgba(0, 0, 0, 0.5) 50%,
            rgba(0, 0, 0, 0.7) 100%
        );
    }

    .hero-section-china .hero-content {
        position: relative;
        z-index: 2;
        color: #fff;
    }

    .hero-section-china h1 {
        font-size: 56px;
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 20px;
        color: #fff;
    }

    .hero-section-china .hero-subtitle {
        font-size: 20px;
        margin-bottom: 30px;
        opacity: 0.9;
    }

    .hero-section-china .hero-features {
        list-style: none;
        padding: 0;
        margin-bottom: 40px;
    }

    .hero-section-china .hero-features li {
        position: relative;
        padding-left: 25px;
        margin-bottom: 12px;
        font-size: 16px;
    }

    .hero-section-china .hero-features li::before {
        content: '✓';
        position: absolute;
        left: 0;
        color: #e31e24;
        font-weight: bold;
        font-size: 18px;
    }

    @media (max-width: 768px) {
        .hero-section-china h1 {
            font-size: 32px;
        }

        .hero-section-china .hero-subtitle {
            font-size: 16px;
        }

        .hero-section-china .hero-features li {
            font-size: 14px;
        }
    }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode('hero_china', 'atk_ved_hero_with_china_photo_shortcode');

/**
 * Шорткод: Галерея производства
 */
function atk_ved_factory_gallery_shortcode(array $atts): string {
    $atts = shortcode_atts([
        'title' => __('Производство и контроль качества', 'atk-ved'),
        'subtitle' => __('Работаем только с проверенными фабриками', 'atk-ved'),
        'limit' => '8',
        'columns' => '4',
    ], $atts);

    ob_start();
    ?>
    <section class="factory-gallery-section">
        <div class="container">
            <div class="section-header">
                <h2><?php echo esc_html($atts['title']); ?></h2>
                <p><?php echo esc_html($atts['subtitle']); ?></p>
            </div>

            <div class="factory-gallery" style="display: grid; grid-template-columns: repeat(<?php echo esc_attr($atts['columns']); ?>, 1fr); gap: 15px;">
                <?php
                $args = [
                    'post_type' => 'attachment',
                    'post_mime_type' => 'image',
                    'post_status' => 'inherit',
                    'posts_per_page' => (int)$atts['limit'],
                    'meta_key' => '_stock_photo_category',
                    'meta_value' => 'factory',
                    'orderby' => 'rand',
                ];

                $query = new WP_Query($args);

                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        ?>
                        <div class="factory-gallery-item">
                            <?php echo wp_get_attachment_image(get_the_ID(), 'medium_large', false, [
                                'loading' => 'lazy',
                                'decoding' => 'async',
                                'class' => 'gallery-image',
                            ]); ?>
                            
                            <?php 
                            $photographer = get_post_meta(get_the_ID(), '_stock_photographer', true);
                            if ($photographer):
                            ?>
                            <div class="photo-credit">
                                📷 <?php echo esc_html($photographer); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php
                    }
                    wp_reset_postdata();
                } else {
                    // Показываем placeholder если нет фото
                    for ($i = 0; $i < 4; $i++) {
                        echo '<div class="factory-gallery-item"><img src="' . esc_url(atk_ved_get_photo_placeholder('factory')) . '" alt="Factory" loading="lazy"></div>';
                    }
                }
                ?>
            </div>
        </div>
    </section>

    <style>
    .factory-gallery-section {
        padding: 80px 0;
        background: #f8f9fa;
    }

    .section-header {
        text-align: center;
        margin-bottom: 50px;
    }

    .section-header h2 {
        font-size: 36px;
        font-weight: 700;
        color: #2c2c2c;
        margin-bottom: 15px;
    }

    .section-header p {
        font-size: 18px;
        color: #666;
    }

    .factory-gallery-item {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
        background: #fff;
    }

    .factory-gallery-item:hover {
        transform: translateY(-5px);
    }

    .factory-gallery-item img {
        width: 100%;
        height: 250px;
        object-fit: cover;
        display: block;
        transition: transform 0.5s ease;
    }

    .factory-gallery-item:hover img {
        transform: scale(1.05);
    }

    .photo-credit {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.7);
        color: #fff;
        padding: 8px 12px;
        font-size: 12px;
        transform: translateY(100%);
        transition: transform 0.3s ease;
    }

    .factory-gallery-item:hover .photo-credit {
        transform: translateY(0);
    }

    @media (max-width: 1024px) {
        .factory-gallery {
            grid-template-columns: repeat(3, 1fr) !important;
        }
    }

    @media (max-width: 768px) {
        .factory-gallery-section {
            padding: 40px 0;
        }

        .factory-gallery {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 10px;
        }

        .factory-gallery-item img {
            height: 180px;
        }

        .section-header h2 {
            font-size: 28px;
        }

        .section-header p {
            font-size: 16px;
        }
    }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode('factory_gallery', 'atk_ved_factory_gallery_shortcode');

/**
 * Шорткод: Галерея доставки
 */
function atk_ved_shipping_gallery_shortcode(array $atts): string {
    $atts = shortcode_atts([
        'title' => __('Логистика и доставка', 'atk-ved'),
        'subtitle' => __('Надёжные маршруты по всему миру', 'atk-ved'),
        'limit' => '6',
    ], $atts);

    ob_start();
    ?>
    <section class="shipping-gallery-section">
        <div class="container">
            <div class="section-header">
                <h2><?php echo esc_html($atts['title']); ?></h2>
                <p><?php echo esc_html($atts['subtitle']); ?></p>
            </div>

            <div class="shipping-grid">
                <?php
                $args = [
                    'post_type' => 'attachment',
                    'post_mime_type' => 'image',
                    'post_status' => 'inherit',
                    'posts_per_page' => (int)$atts['limit'],
                    'meta_key' => '_stock_photo_category',
                    'meta_value' => 'shipping',
                    'orderby' => 'rand',
                ];

                $query = new WP_Query($args);

                if ($query->have_posts()) {
                    $first = true;
                    while ($query->have_posts()) {
                        $query->the_post();
                        $attachment_id = get_the_ID();
                        $image_url = wp_get_attachment_url($attachment_id);
                        $alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
                        
                        if ($first) {
                            // Большое фото слева
                            ?>
                            <div class="shipping-item shipping-large">
                                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($alt); ?>" loading="lazy">
                                <div class="shipping-overlay">
                                    <h3><?php _e('Морские перевозки', 'atk-ved'); ?></h3>
                                    <p><?php _e('От 35 дней', 'atk-ved'); ?></p>
                                </div>
                            </div>
                            <?php
                            $first = false;
                        } else {
                            // Маленькие фото справа
                            ?>
                            <div class="shipping-item shipping-small">
                                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($alt); ?>" loading="lazy">
                            </div>
                            <?php
                        }
                    }
                    wp_reset_postdata();
                }
                ?>
            </div>
        </div>
    </section>

    <style>
    .shipping-gallery-section {
        padding: 80px 0;
        background: #fff;
    }

    .shipping-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .shipping-large {
        grid-row: span 2;
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        min-height: 500px;
    }

    .shipping-large img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .shipping-small {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        height: 240px;
    }

    .shipping-small img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .shipping-small:hover img {
        transform: scale(1.05);
    }

    .shipping-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        padding: 40px 30px 30px;
        color: #fff;
    }

    .shipping-overlay h3 {
        font-size: 28px;
        margin-bottom: 10px;
    }

    .shipping-overlay p {
        font-size: 18px;
        opacity: 0.9;
    }

    @media (max-width: 768px) {
        .shipping-grid {
            grid-template-columns: 1fr;
        }

        .shipping-large {
            min-height: 300px;
        }

        .shipping-small {
            height: 200px;
        }

        .shipping-overlay h3 {
            font-size: 22px;
        }

        .shipping-overlay p {
            font-size: 16px;
        }
    }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode('shipping_gallery', 'atk_ved_shipping_gallery_shortcode');

/**
 * Шорткод: Фоновое изображение для секции
 */
function atk_ved_section_background_shortcode(array $atts, string $content): string {
    $atts = shortcode_atts([
        'category' => 'china',
        'overlay' => 'dark',
        'parallax' => 'true',
    ], $atts);

    $image_url = atk_ved_get_stock_photo($atts['category'], 'full');
    
    $overlay_styles = [
        'dark' => 'rgba(0, 0, 0, 0.7)',
        'light' => 'rgba(255, 255, 255, 0.8)',
        'gradient' => 'linear-gradient(135deg, rgba(0,0,0,0.7), rgba(227,30,36,0.5))',
    ];

    $overlay = $overlay_styles[$atts['overlay']] ?? $overlay_styles['dark'];

    $parallax = $atts['parallax'] === 'true' ? 'background-attachment: fixed;' : '';

    ob_start();
    ?>
    <section style="
        background-image: url('<?php echo esc_url($image_url); ?>');
        background-size: cover;
        background-position: center;
        <?php echo $parallax; ?>
        position: relative;
        padding: 80px 0;
    ">
        <div style="
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: <?php echo esc_html($overlay); ?>;
        "></div>
        
        <div style="position: relative; z-index: 2;">
            <?php echo do_shortcode($content); ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('section_bg', 'atk_ved_section_background_shortcode');

/**
 * Автоматическая замена placeholder изображений в существующих секциях
 */
function atk_ved_replace_placeholder_images(array $atts, string $content = ''): string {
    // Если есть категория в атрибутах
    if (!empty($atts['category'])) {
        $photo_url = atk_ved_get_stock_photo($atts['category'], 'large');
        
        // Заменяем placeholder в content
        if ($content) {
            $content = str_replace('[stock_photo]', $photo_url, $content);
        }
        
        return $photo_url;
    }
    
    return '';
}
add_shortcode('stock_photo', 'atk_ved_replace_placeholder_images');

/**
 * Добавление стоковых фото в медиабиблиотеку при активации темы
 */
function atk_ved_import_default_photos(): void {
    // Проверяем, были ли уже загружены фото
    if (get_option('atk_ved_default_photos_imported')) {
        return;
    }

    $stock = ATK_VED_Stock_Photos::get_instance();
    
    // Проверяем подключение API
    $api_status = $stock->check_api_connection();
    
    if (!$api_status['unsplash'] && !$api_status['pexels']) {
        return;
    }

    // Загружаем по 5 фотографий из каждой категории
    $categories = ['china', 'factory', 'shipping', 'office'];
    
    foreach ($categories as $category) {
        $stock->bulk_download($category, 5);
    }

    update_option('atk_ved_default_photos_imported', true);
}
// Не вызываем автоматически, только по запросу

/**
 * Шорткод для ручной загрузки фото
 */
function atk_ved_import_photos_shortcode(array $atts): string {
    if (!current_user_can('manage_options')) {
        return '<p class="error">Недостаточно прав</p>';
    }

    $category = sanitize_text_field($atts['category'] ?? 'china');
    $limit = absint($atts['limit'] ?? 5);

    $stock = ATK_VED_Stock_Photos::get_instance();
    $downloaded = $stock->bulk_download($category, $limit);

    if (!empty($downloaded)) {
        return '<p class="success">' . sprintf(__('Загружено %d фотографий из категории "%s"', 'atk-ved'), count($downloaded), $category) . '</p>';
    }

    return '<p class="error">' . __('Не удалось загрузить фотографии. Проверьте API ключи.', 'atk-ved') . '</p>';
}
add_shortcode('import_photos', 'atk_ved_import_photos_shortcode');
