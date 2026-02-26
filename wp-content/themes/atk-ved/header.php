<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <meta name="format-detection" content="telephone=no">
    <meta name="theme-color" content="#e31e24">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main-content"><?php esc_html_e('Перейти к содержимому','atk-ved'); ?></a>

<header class="site-header" role="banner">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <?php if(has_custom_logo()){the_custom_logo();}else{?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="logo-link" rel="home">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/logo/logo.png" alt="<?php bloginfo('name'); ?>" width="240" height="60" loading="eager">
                </a>
                <?php }?>
            </div>

            <button class="menu-toggle" aria-label="<?php esc_attr_e('Меню','atk-ved'); ?>" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>

            <nav class="main-nav" role="navigation" aria-label="<?php esc_attr_e('Главное меню','atk-ved'); ?>">
                <?php wp_nav_menu(['theme_location'=>'primary','container'=>false,'menu_id'=>'primary-menu','fallback_cb'=>function(){echo '<ul><li><a href="'.esc_url(home_url('/')).'">'.__('Главная','atk-ved').'</a></li></ul>';}]); ?>
            </nav>

            <div class="header-actions">
                <button class="theme-toggle" type="button" aria-label="Переключить тему">
                    <span class="theme-icon"></span>
                </button>
                <button class="cta-button" onclick="document.getElementById('contact')?.scrollIntoView({behavior:'smooth'})" type="button">
                    <?php esc_html_e('Оставить заявку','atk-ved'); ?>
                </button>
            </div>
        </div>
    </div>
</header>

<main id="main-content" role="main"><?php
