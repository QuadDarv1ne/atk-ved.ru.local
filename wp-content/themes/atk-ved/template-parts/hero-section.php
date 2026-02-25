<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1>
                    <?php 
                    $hero_title = get_theme_mod('atk_ved_hero_title', 'ТОВАРЫ ДЛЯ МАРКЕТПЛЕЙСОВ ИЗ КИТАЯ ОПТОМ');
                    $parts = explode(' ', $hero_title);
                    $last_word = array_pop($parts);
                    echo implode(' ', $parts) . '<br><span class="highlight">' . esc_html($last_word) . '</span>';
                    ?>
                </h1>
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo esc_html(get_theme_mod('atk_ved_stat1_number', '500+')); ?></div>
                        <div class="stat-label"><?php echo esc_html(get_theme_mod('atk_ved_stat1_label', 'КЛИЕНТОВ')); ?></div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo esc_html(get_theme_mod('atk_ved_stat2_number', '1000+')); ?></div>
                        <div class="stat-label"><?php echo esc_html(get_theme_mod('atk_ved_stat2_label', 'ТОВАРОВ')); ?></div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo esc_html(get_theme_mod('atk_ved_stat3_number', '5 ЛЕТ')); ?></div>
                        <div class="stat-label"><?php echo esc_html(get_theme_mod('atk_ved_stat3_label', 'НА РЫНКЕ')); ?></div>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <?php 
                $hero_image = get_theme_mod('atk_ved_hero_image');
                if ($hero_image) {
                    echo '<img src="' . esc_url($hero_image) . '" alt="' . esc_attr(get_bloginfo('name')) . '" loading="eager">';
                } else {
                    // Используем logistics.png как основное изображение
                    $logistics_image = get_template_directory_uri() . '/images/png/logistics.png';
                    if (file_exists(get_template_directory() . '/images/png/logistics.png')) {
                        echo '<img src="' . esc_url($logistics_image) . '" alt="Логистика из Китая" loading="eager">';
                    } else {
                        echo '<img src="' . get_template_directory_uri() . '/images/hero-containers.jpg" alt="Контейнеры" loading="eager">';
                    }
                }
                ?>
            </div>
        </div>
    </div>
</section>
