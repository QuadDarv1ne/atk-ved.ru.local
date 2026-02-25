<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Preloader -->
<div class="preloader">
    <div class="spinner"></div>
</div>

<header class="site-header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <?php
                if (has_custom_logo()) {
                    the_custom_logo();
                } else {
                    echo '<a href="' . esc_url(home_url('/')) . '">АТК ВЭД</a>';
                }
                ?>
            </div>
            
            <button class="menu-toggle" aria-label="Меню">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <nav class="main-nav">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container' => false,
                    'fallback_cb' => function() {
                        echo '<ul>
                            <li><a href="#services">Услуги</a></li>
                            <li><a href="#delivery">Доставка</a></li>
                            <li><a href="#steps">Этапы</a></li>
                            <li><a href="#faq">FAQ</a></li>
                            <li><a href="#reviews">Отзывы</a></li>
                            <li><a href="#contact">Контакты</a></li>
                        </ul>';
                    }
                ));
                ?>
            </nav>
            
            <div class="header-contacts">
                <span class="phone"><?php echo get_theme_mod('atk_ved_phone', '+7 (XXX) XXX-XX-XX'); ?></span>
                <button class="cta-button" onclick="document.getElementById('contact').scrollIntoView({behavior: 'smooth'})">
                    ЗАКАЗАТЬ ЗВОНОК
                </button>
            </div>
        </div>
    </div>
</header>
