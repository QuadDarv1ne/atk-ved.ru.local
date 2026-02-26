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
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="hero-content-enhanced">
                <div class="hero-text-enhanced">
                    <div class="hero-badges">
                        <span class="badge">🏆 <?php _e('Опытные менеджеры', 'atk-ved'); ?></span>
                        <span class="badge">💰 <?php _e('Прозрачные цены', 'atk-ved'); ?></span>
                        <span class="badge">📦 <?php _e('Без минимального заказа', 'atk-ved'); ?></span>
                    </div>

                    <h1 class="hero-title">
                        <?php _e('Товары для маркетплейсов', 'atk-ved'); ?><br>
                        <span class="highlight"><?php _e('из Китая оптом', 'atk-ved'); ?></span>
                    </h1>

                    <p class="hero-subtitle">
                        <?php _e('Полный цикл работы: от поиска поставщика до доставки на ваш склад', 'atk-ved'); ?>
                    </p>

                    <div class="hero-cta">
                        <a href="#contact" class="btn btn-primary btn-lg">
                            <?php _e('Оставить заявку', 'atk-ved'); ?>
                        </a>
                        <a href="#calculator" class="btn btn-outline btn-lg">
                            <?php _e('Рассчитать доставку', 'atk-ved'); ?>
                        </a>
                    </div>

                    <div class="marketplaces-hero">
                        <span class="mp-label"><?php _e('Работаем с:', 'atk-ved'); ?></span>
                        <div class="marketplace-logos">
                            <div class="mp-logo">Wildberries</div>
                            <div class="mp-logo">Ozon</div>
                            <div class="mp-logo">Мегамаркет</div>
                            <div class="mp-logo">AliExpress</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="scroll-indicator">
            <span class="scroll-text"><?php _e('Листайте вниз', 'atk-ved'); ?></span>
            <div class="scroll-arrow">↓</div>
        </div>
    </section>

    <!-- УСЛУГИ -->
    <section class="services-section-enhanced" id="services">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php _e('Наши услуги', 'atk-ved'); ?></h2>
                <p class="section-subtitle"><?php _e('Полный спектр услуг для работы с Китаем', 'atk-ved'); ?></p>
            </div>

            <div class="services-grid-enhanced">
                <?php
                $services = [
                    ['n' => '01', 'title' => __('Поиск товаров', 'atk-ved'), 'desc' => __('Находим нужные товары на китайских площадках по вашим требованиям', 'atk-ved'), 'icon' => '🔍'],
                    ['n' => '02', 'title' => __('Проверка качества', 'atk-ved'), 'desc' => __('Контролируем качество до отправки, делаем фото и видеоотчёты', 'atk-ved'), 'icon' => '✓'],
                    ['n' => '03', 'title' => __('Доставка грузов', 'atk-ved'), 'desc' => __('Организуем доставку авиа, морем, ЖД или авто', 'atk-ved'), 'icon' => '🚢'],
                    ['n' => '04', 'title' => __('Таможенное оформление', 'atk-ved'), 'desc' => __('Берём на себя таможенное оформление и сертификацию', 'atk-ved'), 'icon' => '📋'],
                    ['n' => '05', 'title' => __('Складская логистика', 'atk-ved'), 'desc' => __('Хранение и обработка грузов на наших складах в Китае', 'atk-ved'), 'icon' => '🏭'],
                    ['n' => '06', 'title' => __('Консультации', 'atk-ved'), 'desc' => __('Консультируем по всем вопросам работы с Китаем', 'atk-ved'), 'icon' => '💬'],
                ];
                foreach ($services as $i => $s):
                ?>
                <article class="service-card-enhanced" style="--delay: <?php echo ($i + 1) * 100; ?>ms">
                    <div class="service-icon"><?php echo esc_html($s['icon']); ?></div>
                    <span class="service-number"><?php echo esc_html($s['n']); ?></span>
                    <h3 class="service-title"><?php echo esc_html($s['title']); ?></h3>
                    <p class="service-desc"><?php echo esc_html($s['desc']); ?></p>
                    <a href="#contact" class="service-btn">
                        <?php _e('Заказать', 'atk-ved'); ?>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- СРАВНЕНИЕ ДОСТАВКИ -->
    <section class="delivery-comparison-section" id="calculator">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php _e('Сравнение способов доставки', 'atk-ved'); ?></h2>
                <p class="section-subtitle"><?php _e('Выберите оптимальный вариант для вашего груза', 'atk-ved'); ?></p>
            </div>
            <?php echo do_shortcode('[delivery_comparison]'); ?>
        </div>
    </section>

    <!-- ПРЕИМУЩЕСТВА -->
    <section class="advantages-section-enhanced">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php _e('Почему выбирают нас', 'atk-ved'); ?></h2>
                <p class="section-subtitle"><?php _e('Наши ключевые преимущества', 'atk-ved'); ?></p>
            </div>

            <div class="advantages-grid-enhanced">
                <?php
                $advs = [
                    ['icon' => '🏆', 'title' => __('Опыт работы', 'atk-ved'), 'desc' => __('Более 5 лет успешной работы', 'atk-ved')],
                    ['icon' => '💰', 'title' => __('Выгодные цены', 'atk-ved'), 'desc' => __('Прямые контракты без посредников', 'atk-ved')],
                    ['icon' => '🛡️', 'title' => __('Гарантии', 'atk-ved'), 'desc' => __('Официальный договор и сопровождение', 'atk-ved')],
                    ['icon' => '⚡', 'title' => __('Быстрая работа', 'atk-ved'), 'desc' =>__('Оперативная обработка заказов', 'atk-ved')],
                    ['icon' => '📍', 'title' => __('Отслеживание', 'atk-ved'), 'desc' => __('Контроль груза 24/7', 'atk-ved')],
                    ['icon' => '🤝', 'title' => __('Поддержка', 'atk-ved'), 'desc' => __('Персональный менеджер', 'atk-ved')],
                ];
                foreach ($advs as $a):
                ?>
                <div class="advantage-card-enhanced">
                    <span class="adv-icon"><?php echo esc_html($a['icon']); ?></span>
                    <h3><?php echo esc_html($a['title']); ?></h3>
                    <p><?php echo esc_html($a['desc']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ОТЗЫВЫ -->
    <?php if (function_exists('atk_ved_reviews_slider_shortcode')): ?>
    <section class="reviews-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php _e('Отзывы клиентов', 'atk-ved'); ?></h2>
                <p class="section-subtitle"><?php _e('Что говорят о нас', 'atk-ved'); ?></p>
            </div>
            <?php echo do_shortcode('[reviews_slider]'); ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- КОНТАКТЫ -->
    <section class="contact-section-enhanced" id="contact">
        <div class="container">
            <div class="contact-inner">
                <div class="contact-text">
                    <h2 class="contact-title"><?php _e('Готовы начать работу?', 'atk-ved'); ?></h2>
                    <p class="contact-subtitle"><?php _e('Оставьте заявку и получите бесплатную консультацию', 'atk-ved'); ?></p>
                    
                    <ul class="contact-benefits">
                        <li>✓ <?php _e('Расчёт стоимости за 15 минут', 'atk-ved'); ?></li>
                        <li>✓ <?php _e('Скидка 10% на первую доставку', 'atk-ved'); ?></li>
                        <li>✓ <?php _e('Персональный менеджер', 'atk-ved'); ?></li>
                        <li>✓ <?php _e('Полная поддержка 24/7', 'atk-ved'); ?></li>
                    </ul>
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
        </div>
    </section>

</main>

<?php
get_footer();
