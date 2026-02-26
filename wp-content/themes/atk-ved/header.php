<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="format-detection" content="telephone=no">
    <meta name="theme-color" content="#e31e24">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?php echo get_template_directory_uri(); ?>/images/icons/favicon.svg">
    <link rel="alternate icon" href="<?php echo get_template_directory_uri(); ?>/images/icons/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_template_directory_uri(); ?>/images/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo get_template_directory_uri(); ?>/images/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo get_template_directory_uri(); ?>/images/icons/favicon-16x16.png">
    <link rel="manifest" href="<?php echo get_template_directory_uri(); ?>/manifest.json">
        
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
                    <!-- Используем esc_url для безопасности пути к изображению -->
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/images/logo/logo.png'); ?>" 
                         alt="<?php echo esc_attr(get_bloginfo('name')); ?>" 
                         width="240" 
                         height="60" 
                         loading="eager">
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
