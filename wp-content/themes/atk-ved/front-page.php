<?php
/**
 * Оптимизированная главная страница
 *
 * @package ATK_VED
 * @since 3.4.0
 */

defined('ABSPATH') || exit;

get_header();
?>

<main id="main-content">

    <!-- HERO -->
    <section class="hero-section-enhanced">
        <div class="container">
            <div class="hero-content-enhanced">
                <div class="hero-text-enhanced">
                    <div class="hero-features">
                        <div class="hero-feature">
                            <span class="feature-icon">👥</span>
                            <span class="feature-text"><?php _e('Опытные менеджеры', 'atk-ved'); ?></span>
                        </div>
                        <div class="hero-feature">
                            <span class="feature-icon">💰</span>
                            <span class="feature-text"><?php _e('Прозрачные цены', 'atk-ved'); ?></span>
                        </div>
                        <div class="hero-feature">
                            <span class="feature-icon">📦</span>
                            <span class="feature-text"><?php _e('Без минимальной цены', 'atk-ved'); ?></span>
                        </div>
                        <div class="hero-feature">
                            <span class="feature-icon">🏭</span>
                            <span class="feature-text"><?php _e('База поставщиков', 'atk-ved'); ?></span>
                        </div>
                    </div>

                    <h1 class="hero-title">
                        <?php _e('ТОВАРЫ', 'atk-ved'); ?><br>
                        <?php _e('ДЛЯ МАРКЕТПЛЕЙСОВ', 'atk-ved'); ?><br>
                        <span class="highlight"><?php _e('ИЗ КИТАЯ', 'atk-ved'); ?></span> <?php _e('ОПТОМ', 'atk-ved'); ?>
                    </h1>

                    <div class="hero-cta">
                        <a href="#contact" class="btn-hero btn-primary">
                            <?php _e('Получить расчет', 'atk-ved'); ?>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </a>
                        <a href="#services" class="btn-hero btn-secondary">
                            <?php _e('Наши услуги', 'atk-ved'); ?>
                        </a>
                    </div>

                    <div class="hero-marketplaces">
                        <div class="marketplace-badge">
                            <span class="mp-icon">🛒</span>
                            <span class="mp-name">МЕГАМАРКЕТ</span>
                        </div>
                        <div class="marketplace-badge">
                            <span class="mp-icon">🅰️</span>
                            <span class="mp-name">Alibaba</span>
                        </div>
                        <div class="marketplace-badge">
                            <span class="mp-icon">🅱️</span>
                            <span class="mp-name">WILDBERRIES</span>
                        </div>
                        <div class="marketplace-badge">
                            <span class="mp-icon">🛍️</span>
                            <span class="mp-name">AliExpress</span>
                        </div>
                        <div class="marketplace-badge">
                            <span class="mp-icon">🔷</span>
                            <span class="mp-name">OZON</span>
                        </div>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/png/logistics.png" alt="<?php _e('Товары из Китая', 'atk-ved'); ?>" class="hero-img">
                </div>
            </div>
        </div>
    </section>

    <!-- УСЛУГИ -->
    <section class="services-section-enhanced" id="services">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php _e('НАШИ УСЛУГИ', 'atk-ved'); ?></h2>
                <p class="section-subtitle"><?php _e('Полный спектр услуг для работы с Китаем', 'atk-ved'); ?></p>
            </div>

            <?php atk_ved_render_services(); ?>
        </div>
    </section>

    <!-- СРАВНЕНИЕ ДОСТАВКИ -->
    <section class="delivery-comparison-section" id="delivery">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php _e('СПОСОБЫ И СРОКИ ДОСТАВКИ ГРУЗОВ', 'atk-ved'); ?></h2>
                <p class="section-subtitle"><?php _e('Выберите оптимальный вариант для вашего груза', 'atk-ved'); ?></p>
            </div>
            
            <div class="delivery-table-wrapper">
                <table class="delivery-table">
                    <thead>
                        <tr>
                            <th><?php _e('Способ доставки', 'atk-ved'); ?></th>
                            <th><?php _e('Срок доставки', 'atk-ved'); ?></th>
                            <th><?php _e('Стоимость за кг', 'atk-ved'); ?></th>
                            <th><?php _e('Минимальный вес', 'atk-ved'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong><?php _e('Авиа экспресс', 'atk-ved'); ?></strong></td>
                            <td>7-10 <?php _e('дней', 'atk-ved'); ?></td>
                            <td><?php _e('от $5', 'atk-ved'); ?></td>
                            <td>1 <?php _e('кг', 'atk-ved'); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php _e('Железная дорога', 'atk-ved'); ?></strong></td>
                            <td>20-25 <?php _e('дней', 'atk-ved'); ?></td>
                            <td><?php _e('от $3', 'atk-ved'); ?></td>
                            <td>50 <?php _e('кг', 'atk-ved'); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php _e('Морская доставка', 'atk-ved'); ?></strong></td>
                            <td>35-45 <?php _e('дней', 'atk-ved'); ?></td>
                            <td><?php _e('от $1.5', 'atk-ved'); ?></td>
                            <td>100 <?php _e('кг', 'atk-ved'); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php _e('Автомобильная', 'atk-ved'); ?></strong></td>
                            <td>15-20 <?php _e('дней', 'atk-ved'); ?></td>
                            <td><?php _e('от $2.5', 'atk-ved'); ?></td>
                            <td>30 <?php _e('кг', 'atk-ved'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- ЭТАПЫ СОТРУДНИЧЕСТВА -->
    <section class="process-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php _e('ЭТАПЫ СОТРУДНИЧЕСТВА', 'atk-ved'); ?></h2>
                <p class="section-subtitle"><?php _e('Простой процесс от заявки до получения товара', 'atk-ved'); ?></p>
            </div>

            <?php atk_ved_render_process_steps(); ?>
        </div>
    </section>

    <!-- FAQ -->
    <section class="faq-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php _e('ЧАСТО ЗАДАВАЕМЫЕ ВОПРОСЫ', 'atk-ved'); ?></h2>
                <p class="section-subtitle"><?php _e('Ответы на популярные вопросы о работе с нами', 'atk-ved'); ?></p>
            </div>

            <?php atk_ved_render_faq(); ?>
        </div>
    </section>

    <!-- ОТЗЫВЫ -->
    <?php if (function_exists('atk_ved_reviews_slider_shortcode')): ?>
    <section class="reviews-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php _e('ОТЗЫВЫ О СОТРУДНИЧЕСТВЕ', 'atk-ved'); ?></h2>
            </div>
            <?php echo do_shortcode('[reviews_slider]'); ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- КОНТАКТЫ И КАРТА -->
    <section class="contact-section-enhanced" id="contact">
        <div class="container">
            <div class="contact-inner">
                <div class="contact-text">
                    <h2 class="contact-title"><?php _e('ВЫ ГОТОВЫ ОТКРЫТЬ СВОЙ БИЗНЕС?', 'atk-ved'); ?></h2>
                    <p class="contact-subtitle"><?php _e('Оставьте заявку и получите бесплатную консультацию', 'atk-ved'); ?></p>
                    
                    <div class="contact-info">
                        <div class="contact-item">
                            <span class="contact-icon">📍</span>
                            <div>
                                <strong><?php _e('Адрес:', 'atk-ved'); ?></strong>
                                <p><?php _e('г. Алматы, ул. Примерная, 123', 'atk-ved'); ?></p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <span class="contact-icon">📞</span>
                            <div>
                                <strong><?php _e('Телефон:', 'atk-ved'); ?></strong>
                                <p>+7 (777) 123-45-67</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <span class="contact-icon">✉️</span>
                            <div>
                                <strong><?php _e('Email:', 'atk-ved'); ?></strong>
                                <p>info@atk-ved.kz</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="contact-form-wrapper">
                    <?php
                    if (function_exists('atk_ved_render_lead_form')) {
                        atk_ved_render_lead_form('contact', 'atk_ved_contact');
                    } else {
                        echo do_shortcode('[enhanced_contact_form]');
                    }
                    ?>
                </div>
            </div>
            
            <div class="map-wrapper">
                <div id="contact-map" class="contact-map">
                    <!-- Карта будет здесь -->
                </div>
            </div>
        </div>
    </section>

</main>

<?php
get_footer();

