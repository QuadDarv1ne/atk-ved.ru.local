<?php
/**
 * Шаблон 404 страницы
 * 
 * @package ATK_VED
 * @since 1.0.0
 * @version 1.8.0
 */

get_header();
?>

<section class="error-404-section">
    <div class="container">
        <div class="error-404-content">
            <div class="error-404-icon" aria-hidden="true">
                <svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>
            
            <div class="error-404-number">404</div>
            
            <h1 class="error-404-title"><?php echo esc_html__('Страница не найдена', 'atk-ved'); ?></h1>
            
            <p class="error-404-text">
                <?php echo esc_html__('К сожалению, запрашиваемая страница не существует или была удалена.', 'atk-ved'); ?>
            </p>

            <div class="error-404-actions">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="cta-button primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    <?php echo esc_html__('Вернуться на главную', 'atk-ved'); ?>
                </a>
                <a href="<?php echo esc_url(home_url('/#contact')); ?>" class="cta-button secondary">
                    <?php echo esc_html__('Связаться с нами', 'atk-ved'); ?>
                </a>
            </div>

            <div class="error-404-search">
                <h3><?php echo esc_html__('Попробуйте найти нужную информацию:', 'atk-ved'); ?></h3>
                <?php get_search_form(); ?>
            </div>

            <div class="error-404-links">
                <h3><?php echo esc_html__('Полезные ссылки:', 'atk-ved'); ?></h3>
                <ul>
                    <li><a href="<?php echo esc_url(home_url('/#services')); ?>"><?php echo esc_html__('Наши услуги', 'atk-ved'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/#delivery')); ?>"><?php echo esc_html__('Доставка', 'atk-ved'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/#faq')); ?>"><?php echo esc_html__('Часто задаваемые вопросы', 'atk-ved'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/#contact')); ?>"><?php echo esc_html__('Контакты', 'atk-ved'); ?></a></li>
                </ul>
            </div>
            
            <div class="error-404-contact">
                <h3><?php echo esc_html__('Или свяжитесь с нами напрямую:', 'atk-ved'); ?></h3>
                <div class="contact-methods">
                    <?php if (get_theme_mod('atk_ved_phone')): ?>
                    <a href="tel:<?php echo esc_attr(str_replace([' ', '(', ')', '-'], '', get_theme_mod('atk_ved_phone'))); ?>" class="contact-method">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                        <span><?php echo esc_html(get_theme_mod('atk_ved_phone')); ?></span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (get_theme_mod('atk_ved_email')): ?>
                    <a href="mailto:<?php echo esc_attr(get_theme_mod('atk_ved_email')); ?>" class="contact-method">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <span><?php echo esc_html(get_theme_mod('atk_ved_email')); ?></span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
