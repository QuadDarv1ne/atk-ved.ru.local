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
                        <span class="badge">🏆 <?php _e('5+ лет на рынке', 'atk-ved'); ?></span>
                        <span class="badge">💰 <?php _e('Цены от производителя', 'atk-ved'); ?></span>
                        <span class="badge">📦 <?php _e('От 1 кг без минималки', 'atk-ved'); ?></span>
                    </div>

                    <h1 class="hero-title">
                        <?php _e('Поставки товаров из Китая', 'atk-ved'); ?><br>
                        <span class="highlight"><?php _e('для маркетплейсов под ключ', 'atk-ved'); ?></span>
                    </h1>

                    <p class="hero-subtitle">
                        <?php _e('Находим поставщиков, проверяем качество, доставляем на ваш склад. Работаем с Wildberries, Ozon, Яндекс.Маркет', 'atk-ved'); ?>
                    </p>

                    <div class="hero-cta">
                        <a href="#contact" class="btn btn-primary btn-lg">
                            <?php _e('Получить расчёт', 'atk-ved'); ?>
                        </a>
                        <a href="#services" class="btn btn-outline btn-lg">
                            <?php _e('Наши услуги', 'atk-ved'); ?>
                        </a>
                    </div>

                    <div class="hero-stats">
                        <div class="hero-stat">
                            <span class="stat-number">500<span class="stat-suffix">+</span></span>
                            <span class="stat-label"><?php _e('Довольных клиентов', 'atk-ved'); ?></span>
                        </div>
                        <div class="hero-stat">
                            <span class="stat-number">15<span class="stat-suffix"> дней</span></span>
                            <span class="stat-label"><?php _e('Средний срок доставки', 'atk-ved'); ?></span>
                        </div>
                        <div class="hero-stat">
                            <span class="stat-number">24<span class="stat-suffix">/7</span></span>
                            <span class="stat-label"><?php _e('Поддержка клиентов', 'atk-ved'); ?></span>
                        </div>
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
                    ['n' => '01', 'title' => __('Поиск поставщиков', 'atk-ved'), 'desc' => __('Находим надёжных производителей на 1688.com, Taobao, Alibaba. Проверяем репутацию, сертификаты, отзывы', 'atk-ved'), 'icon' => '🔍'],
                    ['n' => '02', 'title' => __('Контроль качества', 'atk-ved'), 'desc' => __('Инспекция товара на фабрике перед отправкой. Фото/видео отчёты, проверка по чек-листу', 'atk-ved'), 'icon' => '✓'],
                    ['n' => '03', 'title' => __('Доставка из Китая', 'atk-ved'), 'desc' => __('Авиа 7-10 дней, ЖД 20-25 дней, море 35-45 дней. Страхование груза, трекинг 24/7', 'atk-ved'), 'icon' => '🚢'],
                    ['n' => '04', 'title' => __('Таможня под ключ', 'atk-ved'), 'desc' => __('Оформление ВЭД, декларирование, сертификация. Работаем с любыми категориями товаров', 'atk-ved'), 'icon' => '📋'],
                    ['n' => '05', 'title' => __('Склад в Китае', 'atk-ved'), 'desc' => __('Бесплатное хранение 30 дней, консолидация грузов, упаковка, маркировка для маркетплейсов', 'atk-ved'), 'icon' => '🏭'],
                    ['n' => '06', 'title' => __('Выкуп товаров', 'atk-ved'), 'desc' => __('Покупаем товары с китайских площадок, ведём переговоры, контролируем оплату и отгрузку', 'atk-ved'), 'icon' => '💬'],
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
                    ['icon' => '🏆', 'title' => __('5+ лет опыта', 'atk-ved'), 'desc' => __('Более 500 успешных поставок для селлеров маркетплейсов', 'atk-ved')],
                    ['icon' => '💰', 'title' => __('Цены ниже на 20-30%', 'atk-ved'), 'desc' => __('Прямые контракты с фабриками без посредников', 'atk-ved')],
                    ['icon' => '🛡️', 'title' => __('100% гарантия', 'atk-ved'), 'desc' => __('Договор, страхование груза, возврат при браке', 'atk-ved')],
                    ['icon' => '⚡', 'title' => __('Быстрый старт', 'atk-ved'), 'desc' => __('Расчёт за 1 час, отправка груза за 3-5 дней', 'atk-ved')],
                    ['icon' => '📍', 'title' => __('Полный контроль', 'atk-ved'), 'desc' => __('Онлайн-трекинг, фото/видео отчёты на каждом этапе', 'atk-ved')],
                    ['icon' => '🤝', 'title' => __('Личный менеджер', 'atk-ved'), 'desc' => __('Один специалист ведёт ваш заказ от А до Я', 'atk-ved')],
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

    <!-- КАК МЫ РАБОТАЕМ -->
    <section class="process-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php _e('Как мы работаем', 'atk-ved'); ?></h2>
                <p class="section-subtitle"><?php _e('Простой процесс от заявки до получения товара', 'atk-ved'); ?></p>
            </div>

            <div class="process-timeline">
                <?php
                $steps = [
                    ['num' => '1', 'title' => __('Заявка', 'atk-ved'), 'desc' => __('Вы оставляете заявку или присылаете ссылки на товары', 'atk-ved'), 'time' => __('1 час', 'atk-ved')],
                    ['num' => '2', 'title' => __('Расчёт', 'atk-ved'), 'desc' => __('Рассчитываем стоимость товара, доставки и таможни', 'atk-ved'), 'time' => __('1-2 часа', 'atk-ved')],
                    ['num' => '3', 'title' => __('Договор', 'atk-ved'), 'desc' => __('Заключаем договор, вы вносите предоплату 50%', 'atk-ved'), 'time' => __('1 день', 'atk-ved')],
                    ['num' => '4', 'title' => __('Закупка', 'atk-ved'), 'desc' => __('Выкупаем товар у поставщика, проверяем качество', 'atk-ved'), 'time' => __('3-7 дней', 'atk-ved')],
                    ['num' => '5', 'title' => __('Отправка', 'atk-ved'), 'desc' => __('Отправляем груз выбранным способом доставки', 'atk-ved'), 'time' => __('7-45 дней', 'atk-ved')],
                    ['num' => '6', 'title' => __('Получение', 'atk-ved'), 'desc' => __('Вы получаете товар на свой склад или адрес', 'atk-ved'), 'time' => __('1 день', 'atk-ved')],
                ];
                foreach ($steps as $i => $step):
                ?>
                <div class="process-step">
                    <div class="step-number"><?php echo esc_html($step['num']); ?></div>
                    <div class="step-content">
                        <h3 class="step-title"><?php echo esc_html($step['title']); ?></h3>
                        <p class="step-desc"><?php echo esc_html($step['desc']); ?></p>
                        <span class="step-time">⏱ <?php echo esc_html($step['time']); ?></span>
                    </div>
                    <?php if ($i < count($steps) - 1): ?>
                    <div class="step-arrow">→</div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="faq-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php _e('Частые вопросы', 'atk-ved'); ?></h2>
                <p class="section-subtitle"><?php _e('Ответы на популярные вопросы о работе с нами', 'atk-ved'); ?></p>
            </div>

            <div class="faq-grid">
                <?php
                $faqs = [
                    ['q' => __('Какой минимальный заказ?', 'atk-ved'), 'a' => __('Минимального заказа нет. Работаем с любыми объёмами от 1 кг. Но экономически выгоднее заказывать от 50 кг.', 'atk-ved')],
                    ['q' => __('Сколько стоит доставка?', 'atk-ved'), 'a' => __('Стоимость зависит от веса, объёма и способа доставки. Авиа от $5/кг, ЖД от $3/кг, море от $1.5/кг. Точный расчёт делаем индивидуально.', 'atk-ved')],
                    ['q' => __('Как долго идёт доставка?', 'atk-ved'), 'a' => __('Авиа 7-10 дней, ЖД 20-25 дней, море 35-45 дней. Сроки указаны от склада в Китае до склада в России.', 'atk-ved')],
                    ['q' => __('Нужно ли мне открывать ИП?', 'atk-ved'), 'a' => __('Для коммерческих поставок нужно ИП или ООО. Для личных покупок до 1000 евро в месяц можно без регистрации.', 'atk-ved')],
                    ['q' => __('Вы помогаете с таможней?', 'atk-ved'), 'a' => __('Да, берём на себя всё таможенное оформление под ключ. Декларирование, сертификация, уплата пошлин.', 'atk-ved')],
                    ['q' => __('Что если товар придёт бракованный?', 'atk-ved'), 'a' => __('Мы проверяем качество перед отправкой. Если брак обнаружен при получении — возвращаем деньги или меняем товар.', 'atk-ved')],
                ];
                foreach ($faqs as $faq):
                ?>
                <div class="faq-item accordion-item">
                    <button class="faq-question accordion-header">
                        <span><?php echo esc_html($faq['q']); ?></span>
                        <span class="faq-icon accordion-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 9l-7 7-7-7"/>
                            </svg>
                        </span>
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
                    <h2 class="contact-title"><?php _e('Начните зарабатывать на маркетплейсах', 'atk-ved'); ?></h2>
                    <p class="contact-subtitle"><?php _e('Оставьте заявку — рассчитаем стоимость и сроки за 1 час', 'atk-ved'); ?></p>
                    
                    <ul class="contact-benefits">
                        <li>✓ <?php _e('Бесплатный расчёт стоимости и сроков', 'atk-ved'); ?></li>
                        <li>✓ <?php _e('Скидка 10% на первую поставку', 'atk-ved'); ?></li>
                        <li>✓ <?php _e('Помощь в подборе товаров-хитов', 'atk-ved'); ?></li>
                        <li>✓ <?php _e('Консультация по работе с маркетплейсами', 'atk-ved'); ?></li>
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

