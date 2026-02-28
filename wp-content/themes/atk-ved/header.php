<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="format-detection" content="telephone=no">
    <meta name="theme-color" content="#e31e24">
    
    <!-- Preload критических ресурсов -->
    <link rel="preload" href="<?php echo esc_url(get_template_directory_uri() . '/css/variables.css'); ?>" as="style">
    <link rel="preload" href="<?php echo esc_url(get_stylesheet_uri()); ?>" as="style">
    <link rel="preload" href="<?php echo esc_url(get_template_directory_uri() . '/js/core.js'); ?>" as="script">
    <?php if (is_front_page()): ?>
    <link rel="preload" href="<?php echo esc_url(get_template_directory_uri() . '/images/png/logistics.png'); ?>" as="image">
    <?php endif; ?>
    
    <!-- Resource Hints для ускорения загрузки -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//www.google-analytics.com">
    <link rel="dns-prefetch" href="//mc.yandex.ru">
    <link rel="dns-prefetch" href="//maps.googleapis.com">
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo esc_url(get_template_directory_uri()); ?>/images/favicon/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="<?php echo esc_url(get_template_directory_uri()); ?>/images/favicon/favicon.ico" sizes="any">
    <link rel="apple-touch-icon" href="<?php echo esc_url(get_template_directory_uri()); ?>/images/favicon/favicon-180x180.png">
    
    <?php 
    // Google Analytics (раскомментируйте и замените G-XXXXXXXXXX на ваш ID)
    /*
    if (!is_user_logged_in() && !is_admin()) : ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-XXXXXXXXXX');
    </script>
    <?php endif;
    */
    ?>
    
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main-content"><?php esc_html_e('Перейти к содержимому', 'atk-ved'); ?></a>

<header class="site-header" role="banner">
    <div class="container">
        <div class="header-content">
            
            <!-- Логотип -->
            <div class="logo">
                <?php 
                if (has_custom_logo()) {
                    the_custom_logo();
                } else { 
                ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="logo-link" rel="home">
                    <?php if (file_exists(get_template_directory() . '/images/logo/logo.png')) : ?>
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/images/logo/logo.png'); ?>" 
                             alt="<?php echo esc_attr(get_bloginfo('name')); ?>" 
                             width="240" 
                             height="60" 
                             loading="eager">
                    <?php else : ?>
                        <span style="font-size: 24px; font-weight: bold; color: #e31e24;">
                            <?php echo esc_html(get_bloginfo('name')); ?>
                        </span>
                    <?php endif; ?>
                </a>
                <?php } ?>
            </div>

            <!-- Мобильное меню -->
            <button class="menu-toggle" 
                    aria-label="<?php esc_attr_e('Открыть меню', 'atk-ved'); ?>" 
                    aria-expanded="false"
                    aria-controls="primary-menu">
                <span></span><span></span><span></span>
            </button>

            <!-- Навигация -->
            <nav class="main-nav" role="navigation" aria-label="<?php esc_attr_e('Главное меню', 'atk-ved'); ?>">
                <?php 
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_id'        => 'primary-menu',
                    'fallback_cb'    => function() {
                        echo '<ul><li><a href="' . esc_url(home_url('/')) . '">' . __('Главная', 'atk-ved') . '</a></li></ul>';
                    }
                ]); 
                ?>
            </nav>

            <!-- Действия -->
            <div class="header-actions">
                <button class="theme-toggle" type="button" aria-label="<?php esc_attr_e('Переключить тему', 'atk-ved'); ?>">
                    <span class="theme-icon"></span>
                </button>

                <?php 
                // Улучшение: Кнопка работает как ссылка.
                // Если мы на главной - скролл к #contact.
                // Если на внутренней - переход на главную к #contact.
                $cta_link = is_front_page() ? '#contact' : home_url('/#contact');
                ?>
                <a href="<?php echo esc_url($cta_link); ?>" class="cta-button">
                    <?php esc_html_e('Оставить заявку', 'atk-ved'); ?>
                </a>
            </div>

        </div>
    </div>
</header>

<main id="main-content" role="main">