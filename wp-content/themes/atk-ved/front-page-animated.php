<?php
/**
 * Главная страница с анимациями
 * Пример использования новой системы анимаций
 *
 * @package ATK_VED
 * @since 3.6.0
 */

defined('ABSPATH') || exit;

get_header();
?>

<main id="main-content">

    <!-- HERO с анимациями -->
    <section class="hero-section-enhanced">
        <div class="container">
            <div class="hero-content-enhanced">
                <div class="hero-text-enhanced">
                    <!-- Анимация features с задержкой -->
                    <div class="hero-features" data-stagger data-stagger-delay="100">
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

                    <!-- Заголовок с анимацией -->
                    <h1 class="hero-title" data-animate="fade-up" data-animation-delay="200">
                        <?php _e('ТОВАРЫ', 'atk-ved'); ?><br>
                        <?php _e('ДЛЯ МАРКЕТПЛЕЙСОВ', 'atk-ved'); ?><br>
                        <span class="highlight"><?php _e('ИЗ КИТАЯ', 'atk-ved'); ?></span> <?php _e('ОПТОМ', 'atk-ved'); ?>
                    </h1>

                    <!-- CTA кнопки с анимацией -->
                    <div class="hero-cta" data-animate="fade-up" data-animation-delay="400">
                        <a href="#contact" class="btn-hero btn-primary" data-ripple>
                            <?php _e('Получить расчет', 'atk-ved'); ?>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </a>
                        <a href="#services" class="btn-hero btn-secondary">
                            <?php _e('Наши услуги', 'atk-ved'); ?>
                        </a>
                    </div>

                    <!-- Маркетплейсы с stagger анимацией -->
                    <div class="hero-marketplaces" data-stagger data-stagger-delay="80">
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
                
                <!-- Изображение с плавным появлением и параллаксом -->
                <div class="hero-image" data-animate="fade-left" data-animation-delay="300">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/png/logistics.png" 
                         alt="<?php _e('Товары из Китая', 'atk-ved'); ?>" 
                         class="hero-img"
                         data-reveal
                         data-parallax="0.3">
                </div>
            </div>
        </div>
    </section>

    <!-- УСЛУГИ с анимациями -->
    <section class="services-section-enhanced" id="services">
        <div class="container">
            <!-- Заголовок секции -->
            <div class="section-header" data-animate="fade-up">
                <h2 class="section-title"><?php _e('НАШИ УСЛУГИ', 'atk-ved'); ?></h2>
                <p class="section-subtitle"><?php _e('Полный спектр услуг для работы с Китаем', 'atk-ved'); ?></p>
            </div>

            <!-- Карточки услуг с stagger анимацией -->
            <div class="services-grid-enhanced" data-stagger data-stagger-delay="100">
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
                    <div class="service-icon" data-morph><?php echo esc_html($s['icon']); ?></div>
                    <h3 class="service-title"><?php echo esc_html($s['title']); ?></h3>
                    <p class="service-desc"><?php echo esc_html($s['desc']); ?></p>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- СТАТИСТИКА с счетчиками -->
    <section class="stats-section" data-animate="fade-up">
        <div class="container">
            <div class="stats-grid" data-stagger data-stagger-delay="150">
                <div class="stat-card">
                    <div class="stat-number" data-counter="500" data-counter-duration="2000">0</div>
                    <div class="stat-label"><?php _e('Довольных клиентов', 'atk-ved'); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" data-counter="1000" data-counter-duration="2000">0</div>
                    <div class="stat-label"><?php _e('Успешных доставок', 'atk-ved'); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" data-counter="5" data-counter-duration="1500">0</div>
                    <div class="stat-label"><?php _e('Лет на рынке', 'atk-ved'); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" data-counter="99" data-counter-duration="2000">0</div>
                    <div class="stat-label"><?php _e('% грузов без проблем', 'atk-ved'); ?></div>
                </div>
            </div>
        </div>
    </section>

    <!-- СРАВНЕНИЕ ДОСТАВКИ -->
    <section class="delivery-comparison-section" id="delivery">
        <div class="container">
            <div class="section-header" data-animate="fade-up">
                <h2 class="section-title"><?php _e('СПОСОБЫ И СРОКИ ДОСТАВКИ ГРУЗОВ', 'atk-ved'); ?></h2>
                <p class="section-subtitle"><?php _e('Выберите оптимальный вариант для вашего груза', 'atk-ved'); ?></p>
            </div>
            
            <div class="delivery-table-wrapper" data-animate="fade-up" data-animation-delay="200">
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
            <div class="section-header" data-animate="fade-up">
                <h2 class="section-title"><?php _e('ЭТАПЫ СОТРУДНИЧЕСТВА', 'atk-ved'); ?></h2>
                <p class="section-subtitle"><?php _e('Простой процесс от заявки до получения товара', 'atk-ved'); ?></p>
            </div>

            <div class="process-grid" data-stagger data-stagger-delay="120">
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
                    <div class="step-icon-large" data-morph><?php echo esc_html($step['icon']); ?></div>
                    <h3 class="step-title"><?php echo esc_html($step['title']); ?></h3>
                    <p class="step-desc"><?php echo esc_html($step['desc']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- FAQ с аккордеоном -->
    <section class="faq-section">
        <div class="container">
            <div class="section-header" data-animate="fade-up">
                <h2 class="section-title"><?php _e('ЧАСТО ЗАДАВАЕМЫЕ ВОПРОСЫ', 'atk-ved'); ?></h2>
                <p class="section-subtitle"><?php _e('Ответы на популярные вопросы о работе с нами', 'atk-ved'); ?></p>
            </div>

            <div class="faq-grid" data-stagger data-stagger-delay="100">
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
            <div class="section-header" data-animate="fade-up">
                <h2 class="section-title"><?php _e('ОТЗЫВЫ О СОТРУДНИЧЕСТВЕ', 'atk-ved'); ?></h2>
            </div>
            <div data-animate="fade-up" data-animation-delay="200">
                <?php echo do_shortcode('[reviews_slider]'); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- КОНТАКТЫ И КАРТА -->
    <section class="contact-section-enhanced" id="contact">
        <div class="container">
            <div class="contact-inner">
                <div class="contact-text" data-animate="fade-right">
                    <h2 class="contact-title"><?php _e('ВЫ ГОТОВЫ ОТКРЫТЬ СВОЙ БИЗНЕС?', 'atk-ved'); ?></h2>
                    <p class="contact-subtitle"><?php _e('Оставьте заявку и получите бесплатную консультацию', 'atk-ved'); ?></p>
                    
                    <div class="contact-info" data-stagger data-stagger-delay="100">
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

                <div class="contact-form-wrapper" data-animate="fade-left" data-animation-delay="200">
                    <?php
                    if (function_exists('atk_ved_render_lead_form')) {
                        atk_ved_render_lead_form('contact', 'atk_ved_contact');
                    } else {
                        echo do_shortcode('[enhanced_contact_form]');
                    }
                    ?>
                </div>
            </div>
            
            <div class="map-wrapper" data-animate="fade-up" data-animation-delay="400">
                <div id="contact-map" class="contact-map">
                    <!-- Карта будет здесь -->
                </div>
            </div>
        </div>
    </section>

</main>

<?php
get_footer();
