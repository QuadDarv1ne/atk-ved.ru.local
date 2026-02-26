<?php
/**
 * Улучшенная главная страница (Front Page)
 * Интеграция всех новых функций и оптимизация
 *
 * @package ATK_VED
 * @since 3.7.0
 */

defined('ABSPATH') || exit;

get_header();

// Получаем данные компании
$company = atk_ved_get_company_info();
?>

<main id="main-content">

    <!-- ====================================================
         HERO СЕКЦИЯ С ФОТО КИТАЯ
    ===================================================== -->
    <?php
    $hero_bg = atk_ved_get_stock_photo('china', 'full');
    ?>
    <section class="hero-section-enhanced" style="background-image: url('<?php echo esc_url($hero_bg); ?>');">
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

                    <!-- Статистика в Hero -->
                    <div class="hero-stats">
                        <div class="hero-stat">
                            <span class="stat-number" data-target="1500">0</span><span class="stat-suffix">+</span>
                            <span class="stat-label"><?php _e('Клиентов', 'atk-ved'); ?></span>
                        </div>
                        <div class="hero-stat">
                            <span class="stat-number" data-target="5">0</span><span class="stat-suffix">+</span>
                            <span class="stat-label"><?php _e('Лет опыта', 'atk-ved'); ?></span>
                        </div>
                        <div class="hero-stat">
                            <span class="stat-number" data-target="1000">0</span><span class="stat-suffix">+</span>
                            <span class="stat-label"><?php _e('Доставок', 'atk-ved'); ?></span>
                        </div>
                    </div>

                    <!-- Маркетплейсы -->
                    <div class="marketplaces-hero">
                        <span class="mp-label"><?php _e('Работаем с:', 'atk-ved'); ?></span>
                        <div class="marketplace-logos">
                            <div class="mp-logo">Wildberries</div>
                            <div class="mp-logo">Ozon</div>
                            <div class="mp-logo">Мегамаркет</div>
                            <div class="mp-logo">AliExpress</div>
                            <div class="mp-logo">Alibaba</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Scroll indicator -->
        <div class="scroll-indicator">
            <span class="scroll-text"><?php _e('Листайте вниз', 'atk-ved'); ?></span>
            <div class="scroll-arrow">↓</div>
        </div>
    </section>

    <!-- ====================================================
         УСЛУГИ (УЛУЧШЕННЫЕ КАРТОЧКИ)
    ===================================================== -->
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
                    $delay = ($i + 1) * 100;
                ?>
                <article class="service-card-enhanced" style="--delay: <?php echo $delay; ?>ms">
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

    <!-- ====================================================
         ТАБЛИЦА СРАВНЕНИЯ ДОСТАВКИ
    ===================================================== -->
    <section class="delivery-comparison-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php _e('Сравнение способов доставки', 'atk-ved'); ?></h2>
                <p class="section-subtitle"><?php _e('Выберите оптимальный вариант для вашего груза', 'atk-ved'); ?></p>
            </div>

            <?php echo do_shortcode('[delivery_comparison]'); ?>
        </div>
    </section>

    <!-- ====================================================
         ЛИД-КАЛЬКУЛЯТОР (2 ШАГА)
    ===================================================== -->
    <?php echo do_shortcode('[lead_calculator title="Рассчитайте стоимость доставки" subtitle="Получите расчёт и скидку 10% на первую доставку"]'); ?>

    <!-- ====================================================
         ГАЛЕРЕЯ ПРОИЗВОДСТВА
    ===================================================== -->
    <?php echo do_shortcode('[factory_gallery title="Производство и контроль качества" subtitle="Работаем только с проверенными фабриками" limit="8" columns="4"]'); ?>

    <!-- ====================================================
         ГАЛЕРЕЯ ДОСТАВКИ
    ===================================================== -->
    <?php echo do_shortcode('[shipping_gallery title="Логистика и доставка" subtitle="Надёжные маршруты по всему миру" limit="6"]'); ?>

    <!-- ====================================================
         ПРЕИМУЩЕСТВА
    ===================================================== -->
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

    <!-- ====================================================
         ВИДЕО ПРЕЗЕНТАЦИЯ
    ===================================================== -->
    <?php
    $video_id = get_theme_mod('atk_ved_video_id', '');
    if ($video_id):
    ?>
    <?php echo do_shortcode('[video_presentation video_id="' . esc_attr($video_id) . '" title="Видео о компании" subtitle="Узнайте больше о нашей работе"]'); ?>
    <?php endif; ?>

    <!-- ====================================================
         ПАРТНЁРЫ И СЕРТИФИКАТЫ
    ===================================================== -->
    <?php echo do_shortcode('[partners_certificates title="Наши партнёры и сертификаты" subtitle="Доверие клиентов и официальная сертификация"]'); ?>

    <!-- ====================================================
         КОМАНДА
    ===================================================== -->
    <?php echo do_shortcode('[team_section title="Наша команда" subtitle="Профессионалы с многолетним опытом"]'); ?>

    <!-- ====================================================
         ИНТЕРАКТИВНАЯ КАРТА
    ===================================================== -->
    <section class="map-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php _e('География доставки', 'atk-ved'); ?></h2>
                <p class="section-subtitle"><?php _e('Работаем по всему миру', 'atk-ved'); ?></p>
            </div>
            <?php echo do_shortcode('[delivery_map height="500" zoom="4"]'); ?>
        </div>
    </section>

    <!-- ====================================================
         КОНТАКТЫ (CTA)
    ===================================================== -->
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
                    <?php atk_ved_render_lead_form('contact', 'atk_ved_contact'); ?>
                </div>
            </div>
        </div>
    </section>

</main>

<?php
get_footer();
