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
                    ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="logo-link">
                        <img src="<?php echo get_template_directory_uri(); ?>/images/logo/logo.png" alt="АТК-ВЭД" width="240" height="60">
                    </a>
                    <?php
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
                            <li><a href="' . esc_url(home_url('/')) . '">Главная</a></li>
                            <li><a href="#services">О компании</a></li>
                            <li><a href="#delivery">Блог</a></li>
                            <li><a href="#faq">Новости</a></li>
                            <li><a href="#contact">Контакты</a></li>
                        </ul>';
                    }
                ));
                ?>
            </nav>
            
            <div class="header-actions">
                <a href="https://t.me/yourcompany" class="header-icon" target="_blank" rel="noopener" aria-label="Telegram">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm4.744 6.617l-1.59 7.5c-.119.547-.434.681-.878.424l-2.426-1.787-1.17 1.126c-.13.13-.238.238-.488.238l.174-2.468 4.494-4.06c.195-.174-.043-.27-.303-.096l-5.555 3.497-2.393-.748c-.52-.163-.53-.52.108-.77l9.36-3.607c.433-.163.812.096.667.77z"/>
                    </svg>
                </a>
                <a href="https://wa.me/yourphone" class="header-icon" target="_blank" rel="noopener" aria-label="WhatsApp">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 0C4.477 0 0 4.477 0 10c0 1.89.525 3.66 1.438 5.168L0 20l5.016-1.316A9.959 9.959 0 0010 20c5.523 0 10-4.477 10-10S15.523 0 10 0zm5.003 14.125c-.214.602-1.072 1.104-1.763 1.245-.463.094-1.068.17-3.103-.666-2.601-1.07-4.278-3.698-4.408-3.87-.13-.17-1.062-1.413-1.062-2.698 0-1.284.672-1.916.91-2.178.238-.262.52-.327.693-.327.173 0 .346.002.498.009.16.007.374-.061.585.446.214.516.73 1.782.794 1.912.065.13.108.282.022.455-.087.173-.13.282-.26.433-.13.152-.272.338-.39.454-.13.13-.265.27-.114.53.152.26.673 1.11 1.445 1.798.993.885 1.83 1.16 2.09 1.29.26.13.412.108.563-.065.152-.173.65-.758.823-1.02.173-.26.346-.217.585-.13.238.087 1.515.714 1.775.844.26.13.433.195.498.303.065.108.065.628-.15 1.23z"/>
                    </svg>
                </a>
                <button class="header-icon" onclick="sharePage()" aria-label="Поделиться">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="18" cy="5" r="3"></circle>
                        <circle cx="6" cy="12" r="3"></circle>
                        <circle cx="18" cy="19" r="3"></circle>
                        <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                        <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                    </svg>
                </button>
                <button class="cta-button" onclick="document.getElementById('contact').scrollIntoView({behavior: 'smooth'})">
                    Оставить заявку
                </button>
            </div>
        </div>
    </div>
</header>
