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
            </div>

            <div class="services-grid-enhanced">
                <?php
                $services = [
                    ['title' => __('Поиск поставщиков и товаров', 'atk-ved'), 'desc' => __('Подбираем надежных производителей и качественные товары под ваши требования', 'atk-ved'), 'icon' => '🔍'],
                    ['title' => __('Контроль качества товара', 'atk-ved'), 'desc' => __('Проверяем качество продукции перед отправкой на всех этапах производства', 'atk-ved'), 'icon' => '✓'],
                    ['title' => __('Доставка грузов из Китая', 'atk-ved'), 'desc' => __('Организуем быструю и надежную доставку любым удобным способом', 'atk-ved'), 'icon' => '🚢'],
                    ['title' => __('Таможенное оформление', 'atk-ved'), 'desc' => __('Берем на себя все вопросы таможенного оформления и сертификации', 'atk-ved'), 'icon' => '📋'],
                    ['title' => __('Складские услуги', 'atk-ved'), 'desc' => __('Предоставляем складские помещения для хранения и консолидации грузов', 'atk-ved'), 'icon' => '🏭'],
                    ['title' => __('Выкуп и оплата товаров', 'atk-ved'), 'desc' => __('Выкупаем товары у поставщиков и обеспечиваем безопасные расчеты', 'atk-ved'), 'icon' => '💰'],
                ];
                foreach ($services as $i => $s):
                ?>
                <article class="service-card-enhanced">
                    <div class="service-icon"><?php echo esc_html($s['icon']); ?></div>
                    <h3 class="service-title"><?php echo esc_html($s['title']); ?></h3>
                    <p class="service-desc"><?php echo esc_html($s['desc']); ?></p>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- СРАВНЕНИЕ ДОСТАВКИ -->
    <section class="delivery-comparison-section" id="delivery">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php _e('СПОСОБЫ И СРОКИ ДОСТАВКИ ГРУЗОВ', 'atk-ved'); ?></h2>
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
            </div>

            <div class="process-grid">
                <?php
                $steps = [
                    ['num' => '01', 'icon' => '📝', 'title' => __('Заявка и консультация', 'atk-ved'), 'desc' => __('Оставьте заявку на сайте или свяжитесь с нами удобным способом', 'atk-ved')],
                    ['num' => '02', 'icon' => '🔍', 'title' => __('Поиск поставщиков', 'atk-ved'), 'desc' => __('Находим надежных производителей и лучшие предложения', 'atk-ved')],
                    ['num' => '03', 'icon' => '💰', 'title' => __('Расчет стоимости', 'atk-ved'), 'desc' => __('Рассчитываем полную стоимость с учетом всех расходов', 'atk-ved')],
                    ['num' => '04', 'icon' => '📋', 'title' => __('Заключение договора', 'atk-ved'), 'desc' => __('Подписываем договор и согласовываем условия поставки', 'atk-ved')],
                    ['num' => '05', 'icon' => '✓', 'title' => __('Контроль качества', 'atk-ved'), 'desc' => __('Проверяем товар перед отправкой и делаем фотоотчет', 'atk-ved')],
                    ['num' => '06', 'icon' => '🚢', 'title' => __('Доставка и получение', 'atk-ved'), 'desc' => __('Доставляем груз и помогаем с таможенным оформлением', 'atk-ved')],
                ];
                foreach ($steps as $step):
                ?>
                <div class="process-step-card">
                    <div class="step-number-badge"><?php echo esc_html($step['num']); ?></div>
                    <div class="step-icon-large"><?php echo esc_html($step['icon']); ?></div>
                    <h3 class="step-title"><?php echo esc_html($step['title']); ?></h3>
                    <p class="step-desc"><?php echo esc_html($step['desc']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="faq-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php _e('ЧАСТО ЗАДАВАЕМЫЕ ВОПРОСЫ', 'atk-ved'); ?></h2>
            </div>

            <div class="faq-grid">
                <?php
                $faqs = [
                    ['q' => __('Какой минимальный объем заказа?', 'atk-ved'), 'a' => __('Минимального объема нет - работаем с любыми партиями от 1 кг. Однако для оптимизации затрат рекомендуем заказы от 50 кг.', 'atk-ved')],
                    ['q' => __('Сколько времени занимает доставка?', 'atk-ved'), 'a' => __('Сроки зависят от способа доставки: авиа 7-10 дней, ЖД 20-25 дней, море 35-45 дней от склада в Китае до склада в России.', 'atk-ved')],
                    ['q' => __('Как рассчитывается стоимость доставки?', 'atk-ved'), 'a' => __('Стоимость зависит от веса, объема и способа доставки. Авиа от $5/кг, ЖД от $3/кг, море от $1.5/кг. Делаем точный расчет индивидуально.', 'atk-ved')],
                    ['q' => __('Помогаете ли вы с таможенным оформлением?', 'atk-ved'), 'a' => __('Да, мы берем на себя все вопросы таможенного оформления: декларирование, сертификация, уплата пошлин и сборов.', 'atk-ved')],
                    ['q' => __('Что делать, если товар пришел с браком?', 'atk-ved'), 'a' => __('Мы проверяем качество перед отправкой. Если брак обнаружен при получении - возвращаем деньги или меняем товар согласно договору.', 'atk-ved')],
                    ['q' => __('Нужно ли открывать ИП для заказа?', 'atk-ved'), 'a' => __('Для коммерческих поставок требуется ИП или ООО. Для личных покупок до 1000 евро в месяц регистрация не нужна.', 'atk-ved')],
                ];
                foreach ($faqs as $faq):
                ?>
                <div class="faq-item accordion-item">
                    <button class="faq-question accordion-header">
                        <span><?php echo esc_html($faq['q']); ?></span>
                        <span class="faq-icon accordion-icon">+</span>
                    </button>
                    <div class="faq-answer accordion-body">
                        <div class="faq-answer-content accordion-content">
                            <?php echo esc_html($faq['a']); ?>
                        </div>
                    </div>
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

