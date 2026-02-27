<?php
/**
 * The Footer Template
 * Optimized with Schema.org markup and Accessibility improvements
 *
 * @package ATK_VED
 */

// Получаем настройки темы
 $phone      = get_theme_mod( 'atk_ved_phone', '' );
 $email      = get_theme_mod( 'atk_ved_email', '' );
 $address    = get_theme_mod( 'atk_ved_address', '' );
 $whatsapp   = get_theme_mod( 'atk_ved_whatsapp', '' );
 $telegram   = get_theme_mod( 'atk_ved_telegram', '' );
 $vk         = get_theme_mod( 'atk_ved_vk', '' );
 $logo_id    = get_theme_mod( 'atk_ved_logo_white' ); // Получаем ID вложения для лучшей оптимизации
 $logo_url   = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : get_template_directory_uri() . '/images/logo-white.png';

// Проверяем, нужно ли показывать блок рассылки
 $show_newsletter = get_theme_mod( 'atk_ved_show_newsletter', true );
?>


<?php
/**
 * The Footer Template
 * Optimized with Schema.org markup and Accessibility improvements
 *
 * @package ATK_VED
 */

// Получаем настройки темы
 $phone      = get_theme_mod( 'atk_ved_phone', '' );
 $email      = get_theme_mod( 'atk_ved_email', '' );
 $address    = get_theme_mod( 'atk_ved_address', '' );
 $whatsapp   = get_theme_mod( 'atk_ved_whatsapp', '' );
 $telegram   = get_theme_mod( 'atk_ved_telegram', '' );
 $vk         = get_theme_mod( 'atk_ved_vk', '' );
 $logo_id    = get_theme_mod( 'atk_ved_logo_white' ); // Получаем ID вложения для лучшей оптимизации
 $logo_url   = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : get_template_directory_uri() . '/images/logo-white.png';

// Проверяем, нужно ли показывать блок рассылки
 $show_newsletter = get_theme_mod( 'atk_ved_show_newsletter', true );
?>

</main><!-- #main-content -->

<footer class="site-footer" role="contentinfo" itemscope itemtype="https://schema.org/Organization">

        <!-- Блок Рассылки -->
        <?php if ( $show_newsletter ) : ?>
        <div class="footer-newsletter">
            <div class="container">
                <div class="newsletter-inner">
                    <div class="newsletter-text">
                        <h2 class="newsletter-title"><?php esc_html_e( 'Получайте лучшие предложения', 'atk-ved' ); ?></h2>
                        <p><?php esc_html_e( 'Подпишитесь и получайте эксклюзивные скидки и новости', 'atk-ved' ); ?></p>
                    </div>
                    
                    <!-- Обернуто в form для поддержки Enter и валидации HTML5 -->
                    <form class="newsletter-form-wrap" action="#" method="post" aria-label="<?php esc_attr_e( 'Форма подписки', 'atk-ved' ); ?>">
                        <?php wp_nonce_field('atk_newsletter_form', 'newsletter_nonce'); ?>
                        
                        <!-- Honeypot field -->
                        <input type="text" name="website" style="position:absolute;left:-9999px;width:1px;height:1px;" tabindex="-1" autocomplete="off" aria-hidden="true">
                        
                        <label for="footer-email" class="screen-reader-text"><?php esc_html_e( 'Ваш email адрес', 'atk-ved' ); ?></label>
                        <div class="newsletter-form">
                            <input 
                                type="email" 
                                id="footer-email" 
                                name="newsletter_email" 
                                placeholder="<?php esc_attr_e( 'Ваш email', 'atk-ved' ); ?>" 
                                required 
                                autocomplete="email"
                                aria-describedby="newsletter-response"
                            >
                            <button type="submit" class="btn btn-primary js-newsletter-submit">
                                <?php esc_html_e( 'Подписаться', 'atk-ved' ); ?>
                            </button>
                        </div>
                        <!-- aria-live для озвучивания ошибок скринридерами -->
                        <div id="newsletter-response" class="newsletter-response" aria-live="polite" hidden></div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Основная часть футера -->
        <div class="footer-main">
            <div class="container">
                <div class="footer-grid">

                    <!-- Колонка: О компании -->
                    <div class="footer-col footer-col--about">
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-logo" aria-label="<?php esc_attr_e( 'АТК ВЭД — на главную', 'atk-ved' ); ?>" itemprop="url">
                            <?php if (file_exists(get_template_directory() . '/images/logo-white.png')) : ?>
                                <img 
                                    src="<?php echo esc_url( $logo_url ); ?>" 
                                    alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" 
                                    width="160" 
                                    height="48" 
                                    loading="lazy"
                                    itemprop="logo"
                                >
                            <?php else : ?>
                                <span style="font-size: 20px; font-weight: bold; color: #fff;">
                                    <?php echo esc_html(get_bloginfo('name')); ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <p class="footer-desc" itemprop="description">
                            <?php esc_html_e( 'Товары для маркетплейсов из Китая оптом. Полный цикл работы от поиска до доставки с гарантией качества.', 'atk-ved' ); ?>
                        </p>
                        
                        <!-- Бейджи доверия -->
                        <ul class="trust-badges" aria-label="<?php esc_attr_e( 'Наши достижения', 'atk-ved' ); ?>">
                            <li class="badge">
                                <span class="badge-icon" aria-hidden="true"><?php echo atk_ved_icon( 'trophy' ); ?></span>
                                <span><?php esc_html_e( '5 лет на рынке', 'atk-ved' ); ?></span>
                            </li>
                            <li class="badge">
                                <span class="badge-icon" aria-hidden="true"><?php echo atk_ved_icon( 'truck' ); ?></span>
                                <span><?php esc_html_e( '1000+ доставок', 'atk-ved' ); ?></span>
                            </li>
                            <li class="badge">
                                <span class="badge-icon" aria-hidden="true"><?php echo atk_ved_icon( 'star' ); ?></span>
                                <span><?php esc_html_e( '4.9/5 рейтинг', 'atk-ved' ); ?></span>
                            </li>
                        </ul>
                    </div>

                    <!-- Колонка: Услуги -->
                    <div class="footer-col footer-col--services">
                        <h3 class="footer-col__title"><?php esc_html_e( 'Услуги', 'atk-ved' ); ?></h3>
                        <nav aria-label="<?php esc_attr_e( 'Меню услуг', 'atk-ved' ); ?>">
                            <?php 
                                wp_nav_menu( [
                                    'theme_location' => 'footer-services',
                                    'container'      => false,
                                    'menu_class'     => 'footer-links',
                                    'depth'          => 1,
                                    'fallback_cb'    => 'atk_ved_footer_services_fallback'
                                ] ); 
                            ?>
                        </nav>
                    </div>

                    <!-- Колонка: Компания -->
                    <div class="footer-col footer-col--company">
                        <h3 class="footer-col__title"><?php esc_html_e( 'Компания', 'atk-ved' ); ?></h3>
                        <nav aria-label="<?php esc_attr_e( 'Меню компании', 'atk-ved' ); ?>">
                            <?php 
                                wp_nav_menu( [
                                    'theme_location' => 'footer-company',
                                    'container'      => false,
                                    'menu_class'     => 'footer-links',
                                    'depth'          => 1,
                                    'fallback_cb'    => 'atk_ved_footer_company_fallback'
                                ] ); 
                            ?>
                        </nav>
                    </div>

                    <!-- Колонка: Контакты (Schema.org) -->
                    <div class="footer-col footer-col--contacts">
                        <h3 class="footer-col__title"><?php esc_html_e( 'Контакты', 'atk-ved' ); ?></h3>

                        <address class="contact-list">
                            <?php if ( $phone ) : ?>
                            <div class="contact-item">
                                <span class="contact-icon" aria-hidden="true"><?php echo atk_ved_icon( 'phone' ); ?></span>
                                <div>
                                    <span class="contact-label"><?php esc_html_e( 'Телефон', 'atk-ved' ); ?></span>
                                    <a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ); ?>" itemprop="telephone">
                                        <?php echo esc_html( $phone ); ?>
                                    </a>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if ( $email ) : ?>
                            <div class="contact-item">
                                <span class="contact-icon" aria-hidden="true"><?php echo atk_ved_icon( 'mail' ); ?></span>
                                <div>
                                    <span class="contact-label"><?php esc_html_e( 'Email', 'atk-ved' ); ?></span>
                                    <a href="mailto:<?php echo esc_attr( sanitize_email( $email ) ); ?>" itemprop="email">
                                        <?php echo esc_html( $email ); ?>
                                    </a>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if ( $address ) : ?>
                            <div class="contact-item">
                                <span class="contact-icon" aria-hidden="true"><?php echo atk_ved_icon( 'map-pin' ); ?></span>
                                <div>
                                    <span class="contact-label"><?php esc_html_e( 'Адрес', 'atk-ved' ); ?></span>
                                    <!-- Schema.org адрес лучше оборачивать в метку -->
                                    <span itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
                                        <span itemprop="streetAddress"><?php echo esc_html( $address ); ?></span>
                                    </span>
                                </div>
                            </div>
                            <?php endif; ?>
                        </address>

                        <?php if ( $whatsapp || $telegram || $vk ) : ?>
                        <div class="social-section">
                            <h4 class="social-title"><?php esc_html_e( 'Мы в соцсетях', 'atk-ved' ); ?></h4>
                            <div class="social-links">
                                <?php if ( $whatsapp && atk_ved_is_safe_url( $whatsapp ) ) : ?>
                                    <a href="<?php echo esc_url( $whatsapp ); ?>" target="_blank" rel="noopener noreferrer" class="social-link social-link--whatsapp" aria-label="<?php esc_attr_e( 'Написать в WhatsApp', 'atk-ved' ); ?>">
                                        <?php echo atk_ved_icon( 'whatsapp' ); ?>
                                    </a>
                                <?php endif; ?>

                                <?php if ( $telegram && atk_ved_is_safe_url( $telegram ) ) : ?>
                                    <a href="<?php echo esc_url( $telegram ); ?>" target="_blank" rel="noopener noreferrer" class="social-link social-link--telegram" aria-label="<?php esc_attr_e( 'Написать в Telegram', 'atk-ved' ); ?>">
                                        <?php echo atk_ved_icon( 'telegram' ); ?>
                                    </a>
                                <?php endif; ?>

                                <?php if ( $vk && atk_ved_is_safe_url( $vk ) ) : ?>
                                    <a href="<?php echo esc_url( $vk ); ?>" target="_blank" rel="noopener noreferrer" class="social-link social-link--vk" aria-label="<?php esc_attr_e( 'Мы ВКонтакте', 'atk-ved' ); ?>">
                                        <?php echo atk_ved_icon( 'vk' ); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                </div><!-- .footer-grid -->
            </div>
        </div>

        <!-- Нижняя полоса -->
        <div class="footer-bottom">
            <div class="container">
                <div class="footer-bottom-inner">
                    <p class="copyright">
                        &copy; <?php echo esc_html( date( 'Y' ) ); ?>
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" itemprop="name"><?php bloginfo( 'name' ); ?></a>.
                        <?php esc_html_e( 'Все права защищены.', 'atk-ved' ); ?>
                    </p>
                    
                    <nav class="legal-links" aria-label="<?php esc_attr_e( 'Юридические документы', 'atk-ved' ); ?>">
                        <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>">
                            <?php esc_html_e( 'Политика конфиденциальности', 'atk-ved' ); ?>
                        </a>
                        <a href="<?php echo esc_url( home_url( '/terms-of-service/' ) ); ?>">
                            <?php esc_html_e( 'Условия использования', 'atk-ved' ); ?>
                        </a>
                    </nav>
                    
                    <div class="payment-methods" aria-label="<?php esc_attr_e( 'Принимаемые карты', 'atk-ved' ); ?>">
                        <?php 
                        $payment_icons = [
                            'visa' => 'Visa',
                            'mir' => 'МИР',
                            'swift' => 'SWIFT Transfer'
                        ];
                        foreach ($payment_icons as $icon => $alt) {
                            $icon_path = get_template_directory() . '/images/payments/' . $icon . '.svg';
                            if (file_exists($icon_path)) {
                                echo '<img src="' . esc_url(get_template_directory_uri() . '/images/payments/' . $icon . '.svg') . '" alt="' . esc_attr($alt) . '" width="38" height="24" loading="lazy">';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

    </footer>

    <!-- Кнопка "Наверх" с прогресс-баром -->
    <button 
        class="scroll-to-top js-scroll-top" 
        id="scrollToTop" 
        aria-label="<?php esc_attr_e( 'Вернуться наверх', 'atk-ved' ); ?>"
        hidden
    >
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
            <path d="M18 15l-6-6-6 6"/>
        </svg>
        <svg class="progress-ring" viewBox="0 0 36 36" aria-hidden="true" focusable="false">
            <circle class="progress-ring__track" cx="18" cy="18" r="16" fill="none" stroke-width="2"/>
            <circle class="progress-ring__bar"   cx="18" cy="18" r="16" fill="none" stroke-width="2"
                stroke-dasharray="100 100"
                stroke-dashoffset="100"
                transform="rotate(-90 18 18)"
            />
        </svg>
    </button>

    <?php wp_footer(); ?>
</body>
</html>
