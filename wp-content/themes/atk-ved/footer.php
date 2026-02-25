<?php
/**
 * Footer Template
 * Требует регистрации настроек в functions.php (см. комментарий внизу)
 */

$phone      = get_theme_mod( 'atk_ved_phone', '' );
$email      = get_theme_mod( 'atk_ved_email', '' );
$address    = get_theme_mod( 'atk_ved_address', '' );
$whatsapp   = get_theme_mod( 'atk_ved_whatsapp', '' );
$telegram   = get_theme_mod( 'atk_ved_telegram', '' );
$vk         = get_theme_mod( 'atk_ved_vk', '' );
$logo       = get_theme_mod( 'atk_ved_logo_white', get_template_directory_uri() . '/images/logo-white.png' );
?>

<footer class="site-footer" role="contentinfo">

    <!-- Рассылка -->
    <div class="footer-newsletter">
        <div class="container">
            <div class="newsletter-inner">
                <div class="newsletter-text">
                    <h2 class="newsletter-title"><?php _e( 'Получайте лучшие предложения', 'atk-ved' ); ?></h2>
                    <p><?php _e( 'Подпишитесь и получайте эксклюзивные скидки и новости', 'atk-ved' ); ?></p>
                </div>
                <div class="newsletter-form-wrap">
                    <label for="footer-email" class="screen-reader-text">
                        <?php _e( 'Ваш email адрес', 'atk-ved' ); ?>
                    </label>
                    <div class="newsletter-form" role="form" aria-label="<?php esc_attr_e( 'Форма подписки', 'atk-ved' ); ?>">
                        <input
                            type="email"
                            id="footer-email"
                            name="email"
                            placeholder="<?php esc_attr_e( 'Ваш email', 'atk-ved' ); ?>"
                            required
                            autocomplete="email"
                        >
                        <button type="button" class="btn btn-primary js-newsletter-submit">
                            <?php _e( 'Подписаться', 'atk-ved' ); ?>
                        </button>
                    </div>
                    <div class="newsletter-response" aria-live="polite" hidden></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Основная часть -->
    <div class="footer-main">
        <div class="container">
            <div class="footer-grid">

                <!-- О компании -->
                <div class="footer-col footer-col--about">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-logo" aria-label="<?php esc_attr_e( 'АТК ВЭД — на главную', 'atk-ved' ); ?>">
                        <img
                            src="<?php echo esc_url( $logo ); ?>"
                            alt="<?php esc_attr_e( 'АТК ВЭД', 'atk-ved' ); ?>"
                            width="160"
                            height="48"
                            loading="lazy"
                        >
                    </a>
                    <p class="footer-desc">
                        <?php _e( 'Товары для маркетплейсов из Китая оптом. Полный цикл работы от поиска до доставки с гарантией качества.', 'atk-ved' ); ?>
                    </p>
                    <ul class="trust-badges" aria-label="<?php esc_attr_e( 'Наши достижения', 'atk-ved' ); ?>">
                        <li class="badge">
                            <span class="badge-icon" aria-hidden="true">
                                <?php echo atk_ved_icon( 'trophy' ); ?>
                            </span>
                            <span><?php _e( '5 лет на рынке', 'atk-ved' ); ?></span>
                        </li>
                        <li class="badge">
                            <span class="badge-icon" aria-hidden="true">
                                <?php echo atk_ved_icon( 'truck' ); ?>
                            </span>
                            <span><?php _e( '1000+ доставок', 'atk-ved' ); ?></span>
                        </li>
                        <li class="badge">
                            <span class="badge-icon" aria-hidden="true">
                                <?php echo atk_ved_icon( 'star' ); ?>
                            </span>
                            <span><?php _e( '4.9/5 рейтинг', 'atk-ved' ); ?></span>
                        </li>
                    </ul>
                </div>

                <!-- Услуги -->
                <div class="footer-col footer-col--services">
                    <h3 class="footer-col__title"><?php _e( 'Услуги', 'atk-ved' ); ?></h3>
                    <nav aria-label="<?php esc_attr_e( 'Услуги', 'atk-ved' ); ?>">
                        <?php
                        wp_nav_menu( [
                            'theme_location' => 'footer-services',
                            'container'      => false,
                            'menu_class'     => 'footer-links',
                            'depth'          => 1,
                            'fallback_cb'    => 'atk_ved_footer_services_fallback',
                        ] );
                        ?>
                    </nav>
                </div>

                <!-- Компания -->
                <div class="footer-col footer-col--company">
                    <h3 class="footer-col__title"><?php _e( 'Компания', 'atk-ved' ); ?></h3>
                    <nav aria-label="<?php esc_attr_e( 'Компания', 'atk-ved' ); ?>">
                        <?php
                        wp_nav_menu( [
                            'theme_location' => 'footer-company',
                            'container'      => false,
                            'menu_class'     => 'footer-links',
                            'depth'          => 1,
                            'fallback_cb'    => 'atk_ved_footer_company_fallback',
                        ] );
                        ?>
                    </nav>
                </div>

                <!-- Контакты -->
                <div class="footer-col footer-col--contacts">
                    <h3 class="footer-col__title"><?php _e( 'Контакты', 'atk-ved' ); ?></h3>

                    <address class="contact-list">
                        <?php if ( $phone ) : ?>
                        <div class="contact-item">
                            <span class="contact-icon" aria-hidden="true"><?php echo atk_ved_icon( 'phone' ); ?></span>
                            <div>
                                <span class="contact-label"><?php _e( 'Телефон', 'atk-ved' ); ?></span>
                                <a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ); ?>">
                                    <?php echo esc_html( $phone ); ?>
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ( $email ) : ?>
                        <div class="contact-item">
                            <span class="contact-icon" aria-hidden="true"><?php echo atk_ved_icon( 'mail' ); ?></span>
                            <div>
                                <span class="contact-label"><?php _e( 'Email', 'atk-ved' ); ?></span>
                                <a href="mailto:<?php echo esc_attr( sanitize_email( $email ) ); ?>">
                                    <?php echo esc_html( $email ); ?>
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ( $address ) : ?>
                        <div class="contact-item">
                            <span class="contact-icon" aria-hidden="true"><?php echo atk_ved_icon( 'map-pin' ); ?></span>
                            <div>
                                <span class="contact-label"><?php _e( 'Адрес', 'atk-ved' ); ?></span>
                                <span><?php echo esc_html( $address ); ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </address>

                    <?php if ( $whatsapp || $telegram || $vk ) : ?>
                    <div class="social-section">
                        <h4 class="social-title"><?php _e( 'Мы в соцсетях', 'atk-ved' ); ?></h4>
                        <div class="social-links">
                            <?php if ( $whatsapp && atk_ved_is_safe_url( $whatsapp ) ) : ?>
                            <a href="<?php echo esc_url( $whatsapp ); ?>" target="_blank" rel="noopener noreferrer" class="social-link social-link--whatsapp" aria-label="<?php esc_attr_e( 'Написать в WhatsApp', 'atk-ved' ); ?>">
                                <span aria-hidden="true"><?php echo atk_ved_icon( 'whatsapp' ); ?></span>
                            </a>
                            <?php endif; ?>

                            <?php if ( $telegram && atk_ved_is_safe_url( $telegram ) ) : ?>
                            <a href="<?php echo esc_url( $telegram ); ?>" target="_blank" rel="noopener noreferrer" class="social-link social-link--telegram" aria-label="<?php esc_attr_e( 'Написать в Telegram', 'atk-ved' ); ?>">
                                <span aria-hidden="true"><?php echo atk_ved_icon( 'telegram' ); ?></span>
                            </a>
                            <?php endif; ?>

                            <?php if ( $vk && atk_ved_is_safe_url( $vk ) ) : ?>
                            <a href="<?php echo esc_url( $vk ); ?>" target="_blank" rel="noopener noreferrer" class="social-link social-link--vk" aria-label="<?php esc_attr_e( 'Мы ВКонтакте', 'atk-ved' ); ?>">
                                <span aria-hidden="true"><?php echo atk_ved_icon( 'vk' ); ?></span>
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
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>">АТК ВЭД</a>.
                    <?php _e( 'Все права защищены.', 'atk-ved' ); ?>
                </p>
                <nav class="legal-links" aria-label="<?php esc_attr_e( 'Юридические документы', 'atk-ved' ); ?>">
                    <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>">
                        <?php _e( 'Политика конфиденциальности', 'atk-ved' ); ?>
                    </a>
                    <a href="<?php echo esc_url( home_url( '/terms-of-service/' ) ); ?>">
                        <?php _e( 'Условия использования', 'atk-ved' ); ?>
                    </a>
                </nav>
                <div class="payment-methods" aria-label="<?php esc_attr_e( 'Способы оплаты', 'atk-ved' ); ?>">
                    <span class="payment-label"><?php _e( 'Оплата:', 'atk-ved' ); ?></span>
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/images/payments/visa.svg"    alt="Visa"           width="38" height="24" loading="lazy">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/images/payments/mir.svg"     alt="МИР"            width="38" height="24" loading="lazy">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/images/payments/swift.svg"   alt="SWIFT / Wire"   width="38" height="24" loading="lazy">
                </div>
            </div>
        </div>
    </div>

</footer>

<!-- Кнопка "Наверх" -->
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
