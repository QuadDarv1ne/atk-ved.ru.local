<?php
/**
 * Front Page Template
 *
 * @package ATK_VED
 */

defined( 'ABSPATH' ) || exit;

get_header();

// Получаем данные компании один раз
$company = atk_ved_get_company_info();
?>

<main id="main-content">

    <!-- ====================================================
         HERO
    ===================================================== -->
    <section
        class="hero"
        aria-labelledby="hero-heading"
    >
        <div class="container">
            <div class="hero__inner">

                <div class="hero__text">

                    <ul class="hero__badges" aria-label="<?php esc_attr_e( 'Наши преимущества', 'atk-ved' ); ?>">
                        <?php
                        $badges = [
                            __( 'Опытные менеджеры',    'atk-ved' ),
                            __( 'Прозрачные цены',       'atk-ved' ),
                            __( 'Без минимального заказа', 'atk-ved' ),
                            __( 'База поставщиков',      'atk-ved' ),
                        ];
                        foreach ( $badges as $badge ) : ?>
                            <li><?php echo esc_html( $badge ); ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <h1 id="hero-heading" class="hero__title">
                        <?php _e( 'Товары', 'atk-ved' ); ?><br>
                        <?php _e( 'для маркетплейсов', 'atk-ved' ); ?><br>
                        <mark><?php _e( 'из Китая', 'atk-ved' ); ?></mark>
                        <?php _e( 'оптом', 'atk-ved' ); ?>
                    </h1>

                    <div class="hero__cta">
                        <a href="#contact" class="btn btn--primary">
                            <?php _e( 'Оставить заявку', 'atk-ved' ); ?>
                        </a>
                        <a href="#calculator" class="btn btn--outline">
                            <?php _e( 'Рассчитать доставку', 'atk-ved' ); ?>
                        </a>
                    </div>

                    <!-- Маркетплейсы -->
                    <div class="marketplaces" aria-label="<?php esc_attr_e( 'Работаем с маркетплейсами', 'atk-ved' ); ?>">
                        <?php
                        $mp = [
                            [ 'label' => 'Мегамаркет',  'color' => '#FF6B00', 'letter' => 'М'  ],
                            [ 'label' => 'Alibaba',     'color' => '#FF6A00', 'letter' => 'A'  ],
                            [ 'label' => 'Wildberries', 'color' => '#CB11AB', 'letter' => 'WB' ],
                            [ 'label' => 'AliExpress',  'color' => '#E62E04', 'letter' => 'A'  ],
                            [ 'label' => 'Ozon',        'color' => '#005BFF', 'letter' => 'O'  ],
                        ];
                        foreach ( $mp as $item ) : ?>
                        <div class="marketplace">
                            <span
                                class="marketplace__icon"
                                style="background:<?php echo esc_attr( $item['color'] ); ?>"
                                aria-hidden="true"
                            ><?php echo esc_html( $item['letter'] ); ?></span>
                            <span class="marketplace__name"><?php echo esc_html( $item['label'] ); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                </div><!-- /.hero__text -->

                <div class="hero__media" aria-hidden="true">
                    <?php
                    $hero_img = get_theme_mod( 'atk_ved_hero_image', ATK_VED_URI . '/images/hero/hero-boxes.jpg' );
                    ?>
                    <img
                        src="<?php echo esc_url( $hero_img ); ?>"
                        alt="<?php esc_attr_e( 'Товары из Китая для маркетплейсов', 'atk-ved' ); ?>"
                        width="640"
                        height="560"
                        loading="eager"
                        fetchpriority="high"
                        decoding="sync"
                    >
                </div>

            </div>
        </div>
    </section>


    <!-- ====================================================
         УСЛУГИ
    ===================================================== -->
    <section class="services" id="services" aria-labelledby="services-heading">
        <div class="container">
            <h2 id="services-heading" class="section-title">
                <?php _e( 'Наши услуги', 'atk-ved' ); ?>
            </h2>
            <div class="services__grid">
                <?php
                $services = [
                    [ 'n' => '01', 'title' => __( 'Поиск товаров',          'atk-ved' ), 'desc' => __( 'Находим нужные товары на китайских площадках по вашим требованиям и бюджету', 'atk-ved' ) ],
                    [ 'n' => '02', 'title' => __( 'Проверка качества',       'atk-ved' ), 'desc' => __( 'Контролируем качество до отправки, делаем фото и видеоотчёты', 'atk-ved' ) ],
                    [ 'n' => '03', 'title' => __( 'Доставка грузов',         'atk-ved' ), 'desc' => __( 'Организуем доставку авиа, морем, ЖД или авто — на выбор', 'atk-ved' ) ],
                    [ 'n' => '04', 'title' => __( 'Таможенное оформление',   'atk-ved' ), 'desc' => __( 'Берём на себя таможенное оформление и сертификацию', 'atk-ved' ) ],
                    [ 'n' => '05', 'title' => __( 'Складская логистика',     'atk-ved' ), 'desc' => __( 'Хранение и обработка грузов на наших складах', 'atk-ved' ) ],
                    [ 'n' => '06', 'title' => __( 'Консультации',            'atk-ved' ), 'desc' => __( 'Консультируем по всем вопросам работы с Китаем и маркетплейсами', 'atk-ved' ) ],
                ];
                foreach ( $services as $i => $s ) :
                    $delay = ( $i + 1 ) * 100;
                ?>
                <article
                    class="service-card js-reveal"
                    style="--delay:<?php echo $delay; ?>ms"
                    aria-label="<?php echo esc_attr( $s['title'] ); ?>"
                >
                    <span class="service-card__num" aria-hidden="true"><?php echo esc_html( $s['n'] ); ?></span>
                    <h3 class="service-card__title"><?php echo esc_html( $s['title'] ); ?></h3>
                    <p class="service-card__desc"><?php echo esc_html( $s['desc'] ); ?></p>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>


    <!-- ====================================================
         ЗАЯВКА (быстрая форма)
    ===================================================== -->
    <section class="lead-section" id="search" aria-labelledby="lead-heading">
        <div class="container">
            <div class="lead-section__inner">

                <div class="lead-section__text">
                    <h2 id="lead-heading">
                        <?php _e( 'Найдём товар в Китае и получим самое выгодное предложение от поставщика', 'atk-ved' ); ?>
                    </h2>
                    <p><?php _e( 'Помогаем найти, выкупить и доставить товары из Китая на самых выгодных условиях', 'atk-ved' ); ?></p>
                </div>

                <div class="lead-section__form">
                    <?php atk_ved_render_lead_form( 'quick-lead', 'atk_ved_quick_lead' ); ?>
                </div>

            </div>
        </div>
    </section>


    <!-- ====================================================
         ДОСТАВКА
    ===================================================== -->
    <section class="delivery" id="delivery" aria-labelledby="delivery-heading">
        <div class="container">
            <h2 id="delivery-heading" class="section-title">
                <?php _e( 'Способы и сроки доставки', 'atk-ved' ); ?>
            </h2>

            <div class="delivery__intro">
                <p><?php _e( 'Предлагаем различные варианты в зависимости от ваших потребностей, сроков и бюджета.', 'atk-ved' ); ?></p>
                <ul class="delivery__features">
                    <?php
                    $features = [
                        __( 'Полное таможенное оформление',    'atk-ved' ),
                        __( 'Страхование грузов',              'atk-ved' ),
                        __( 'Отслеживание в реальном времени', 'atk-ved' ),
                        __( 'Доставка до двери',               'atk-ved' ),
                    ];
                    foreach ( $features as $f ) : ?>
                        <li><?php echo esc_html( $f ); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="delivery__methods" role="list">
                <?php
                $methods = [
                    [
                        'icon'  => 'plane',
                        'title' => __( 'Авиа',  'atk-ved' ),
                        'days'  => __( '5–10 дней', 'atk-ved' ),
                        'desc'  => __( 'Быстрая доставка для срочных грузов', 'atk-ved' ),
                    ],
                    [
                        'icon'  => 'ship',
                        'title' => __( 'Море',  'atk-ved' ),
                        'days'  => __( '30–45 дней', 'atk-ved' ),
                        'desc'  => __( 'Экономичный вариант для крупных партий', 'atk-ved' ),
                    ],
                    [
                        'icon'  => 'train',
                        'title' => __( 'ЖД', 'atk-ved' ),
                        'days'  => __( '15–20 дней', 'atk-ved' ),
                        'desc'  => __( 'Оптимальное соотношение цены и скорости', 'atk-ved' ),
                    ],
                ];
                foreach ( $methods as $m ) : ?>
                <article class="method-card js-reveal" role="listitem">
                    <div class="method-card__icon" aria-hidden="true">
                        <?php echo atk_ved_icon( $m['icon'] ); ?>
                    </div>
                    <h3 class="method-card__title"><?php echo esc_html( $m['title'] ); ?></h3>
                    <p class="method-card__days"><?php echo esc_html( $m['days'] ); ?></p>
                    <p class="method-card__desc"><?php echo esc_html( $m['desc'] ); ?></p>
                </article>
                <?php endforeach; ?>
            </div>

        </div>
    </section>


    <!-- ====================================================
         КАЛЬКУЛЯТОР
    ===================================================== -->
    <section class="calculator" id="calculator" aria-labelledby="calc-heading">
        <div class="container">
            <h2 id="calc-heading" class="section-title">
                <?php _e( 'Калькулятор стоимости доставки', 'atk-ved' ); ?>
            </h2>

            <div class="calculator__inner">

                <div class="calculator__info">
                    <h3><?php _e( 'Рассчитайте стоимость доставки вашего груза', 'atk-ved' ); ?></h3>
                    <p><?php _e( 'Укажите параметры груза и выберите способ доставки — получите предварительный расчёт за секунды.', 'atk-ved' ); ?></p>
                    <ul class="calculator__features" aria-label="<?php esc_attr_e( 'Что учитывается в расчёте', 'atk-ved' ); ?>">
                        <?php
                        $features = [
                            __( 'Объёмный вес',          'atk-ved' ),
                            __( 'Страхование груза',      'atk-ved' ),
                            __( 'Таможенное оформление',  'atk-ved' ),
                            __( 'Услуги компании',        'atk-ved' ),
                        ];
                        foreach ( $features as $f ) : ?>
                            <li><?php echo esc_html( $f ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="calculator__form-wrap">
                    <form
                        id="calculator-form"
                        class="calculator__form"
                        novalidate
                        aria-label="<?php esc_attr_e( 'Форма расчёта доставки', 'atk-ved' ); ?>"
                    >
                        <?php wp_nonce_field( 'atk_calculator', 'calc_nonce' ); ?>

                        <div class="form-row">
                            <div class="form-field">
                                <label for="calc-weight">
                                    <?php _e( 'Вес груза', 'atk-ved' ); ?>
                                    <button
                                        type="button"
                                        class="tooltip-trigger"
                                        aria-label="<?php esc_attr_e( 'Подсказка: фактический вес в кг', 'atk-ved' ); ?>"
                                        data-tooltip="<?php esc_attr_e( 'Фактический вес груза в килограммах', 'atk-ved' ); ?>"
                                    >?</button>
                                </label>
                                <div class="input-wrap">
                                    <input
                                        type="number"
                                        id="calc-weight"
                                        name="weight"
                                        placeholder="100"
                                        min="0.1"
                                        step="0.1"
                                        required
                                        aria-describedby="calc-weight-hint"
                                    >
                                    <span class="input-suffix" aria-hidden="true"><?php _e( 'кг', 'atk-ved' ); ?></span>
                                </div>
                                <span id="calc-weight-hint" class="field-hint sr-only">
                                    <?php _e( 'Введите вес в килограммах', 'atk-ved' ); ?>
                                </span>
                            </div>

                            <div class="form-field">
                                <label for="calc-volume">
                                    <?php _e( 'Объём груза', 'atk-ved' ); ?>
                                    <button
                                        type="button"
                                        class="tooltip-trigger"
                                        aria-label="<?php esc_attr_e( 'Подсказка: объём в кубометрах', 'atk-ved' ); ?>"
                                        data-tooltip="<?php esc_attr_e( 'Длина × ширина × высота в метрах', 'atk-ved' ); ?>"
                                    >?</button>
                                </label>
                                <div class="input-wrap">
                                    <input
                                        type="number"
                                        id="calc-volume"
                                        name="volume"
                                        placeholder="1"
                                        min="0.001"
                                        step="0.001"
                                        required
                                    >
                                    <span class="input-suffix" aria-hidden="true">м³</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-field">
                                <label for="calc-cost">
                                    <?php _e( 'Стоимость товара', 'atk-ved' ); ?>
                                    <button
                                        type="button"
                                        class="tooltip-trigger"
                                        aria-label="<?php esc_attr_e( 'Подсказка: для расчёта страховки', 'atk-ved' ); ?>"
                                        data-tooltip="<?php esc_attr_e( 'Используется для расчёта страховки и таможенных пошлин', 'atk-ved' ); ?>"
                                    >?</button>
                                </label>
                                <div class="input-wrap">
                                    <input
                                        type="number"
                                        id="calc-cost"
                                        name="cost"
                                        placeholder="50000"
                                        min="1"
                                        required
                                    >
                                    <span class="input-suffix" aria-hidden="true">₽</span>
                                </div>
                            </div>

                            <div class="form-field">
                                <label for="calc-method"><?php _e( 'Способ доставки', 'atk-ved' ); ?></label>
                                <select id="calc-method" name="method" required>
                                    <option value="air"><?php _e( 'Авиа (5–10 дней)',   'atk-ved' ); ?></option>
                                    <option value="rail" selected><?php _e( 'ЖД (15–20 дней)', 'atk-ved' ); ?></option>
                                    <option value="sea"><?php _e( 'Море (30–45 дней)', 'atk-ved' ); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="calculator__actions">
                            <button type="submit" class="btn btn--primary" id="calc-submit">
                                <?php _e( 'Рассчитать', 'atk-ved' ); ?>
                            </button>
                            <button type="button" class="btn btn--outline" id="calc-compare">
                                <?php _e( 'Сравнить все способы', 'atk-ved' ); ?>
                            </button>
                        </div>

                        <div
                            id="calc-result"
                            role="region"
                            aria-live="polite"
                            aria-label="<?php esc_attr_e( 'Результат расчёта', 'atk-ved' ); ?>"
                            hidden
                        ></div>

                    </form>
                </div>

            </div>
        </div>
    </section>


    <!-- ====================================================
         ПРЕИМУЩЕСТВА
    ===================================================== -->
    <section class="advantages" aria-labelledby="adv-heading">
        <div class="container">
            <h2 id="adv-heading" class="section-title">
                <?php _e( 'Почему выбирают нас', 'atk-ved' ); ?>
            </h2>
            <div class="advantages__grid">
                <?php
                $advs = [
                    [ 'icon' => 'trophy', 'title' => __( 'Опыт работы',     'atk-ved' ), 'desc' => __( 'Более 5 лет успешной работы на рынке импорта из Китая',          'atk-ved' ) ],
                    [ 'icon' => 'coin',   'title' => __( 'Выгодные цены',   'atk-ved' ), 'desc' => __( 'Прямые контракты с производителями без посредников',             'atk-ved' ) ],
                    [ 'icon' => 'shield', 'title' => __( 'Гарантии',        'atk-ved' ), 'desc' => __( 'Официальный договор и полное юридическое сопровождение',         'atk-ved' ) ],
                    [ 'icon' => 'bolt',   'title' => __( 'Быстрая работа',  'atk-ved' ), 'desc' => __( 'Оперативная обработка заказов и доставка в срок',                'atk-ved' ) ],
                ];
                foreach ( $advs as $a ) : ?>
                <article class="advantage-card js-reveal">
                    <div class="advantage-card__icon" aria-hidden="true">
                        <?php echo atk_ved_icon( $a['icon'] ); ?>
                    </div>
                    <h3><?php echo esc_html( $a['title'] ); ?></h3>
                    <p><?php echo esc_html( $a['desc'] ); ?></p>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>


    <!-- ====================================================
         СТАТИСТИКА
    ===================================================== -->
    <section class="stats" aria-labelledby="stats-heading">
        <h2 id="stats-heading" class="sr-only"><?php _e( 'Наши показатели', 'atk-ved' ); ?></h2>
        <div class="container">
            <div class="stats__grid" role="list">
                <?php
                $stats = [
                    [ 'target' => 1500, 'suffix' => '+', 'label' => __( 'Довольных клиентов', 'atk-ved' ), 'sub' => __( 'Работаем с 2018 года', 'atk-ved' ) ],
                    [ 'target' => $company['years'], 'suffix' => '+', 'label' => __( 'Лет на рынке',      'atk-ved' ), 'sub' => __( 'Опыт и надёжность',  'atk-ved' ) ],
                    [ 'target' => 1000, 'suffix' => '+', 'label' => __( 'Контейнеров доставлено', 'atk-ved' ), 'sub' => __( 'Объёмные перевозки', 'atk-ved' ) ],
                    [ 'target' => 15,   'suffix' => '',  'label' => __( 'Городов доставки', 'atk-ved' ), 'sub' => __( 'По всей России', 'atk-ved' ) ],
                ];
                foreach ( $stats as $stat ) : ?>
                <div class="stat" role="listitem">
                    <p
                        class="stat__number js-counter"
                        data-target="<?php echo (int) $stat['target']; ?>"
                        aria-label="<?php echo esc_attr( $stat['target'] . $stat['suffix'] . ' — ' . $stat['label'] ); ?>"
                    >0</p>
                    <span class="stat__suffix" aria-hidden="true"><?php echo esc_html( $stat['suffix'] ); ?></span>
                    <h3 class="stat__label"><?php echo esc_html( $stat['label'] ); ?></h3>
                    <p class="stat__sub"><?php echo esc_html( $stat['sub'] ); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>


    <!-- ====================================================
         ЭТАПЫ РАБОТЫ
    ===================================================== -->
    <section class="steps" id="steps" aria-labelledby="steps-heading">
        <div class="container">
            <h2 id="steps-heading" class="section-title">
                <?php _e( 'Этапы сотрудничества', 'atk-ved' ); ?>
            </h2>
            <ol class="steps__list">
                <?php
                $steps = [
                    [ 'title' => __( 'Заявка',           'atk-ved' ), 'desc' => __( 'Оставляете заявку на сайте или связываетесь с нами любым удобным способом', 'atk-ved' ) ],
                    [ 'title' => __( 'Консультация',      'atk-ved' ), 'desc' => __( 'Менеджер связывается с вами и уточняет все детали заказа', 'atk-ved' ) ],
                    [ 'title' => __( 'Поиск товара',      'atk-ved' ), 'desc' => __( 'Находим нужные товары, согласовываем цены и условия с поставщиками', 'atk-ved' ) ],
                    [ 'title' => __( 'Оплата',            'atk-ved' ), 'desc' => __( 'Вы вносите предоплату, мы выкупаем товар у поставщика', 'atk-ved' ) ],
                    [ 'title' => __( 'Контроль качества', 'atk-ved' ), 'desc' => __( 'Проверяем товар на складе в Китае, делаем фото- и видеоотчёт', 'atk-ved' ) ],
                    [ 'title' => __( 'Доставка',          'atk-ved' ), 'desc' => __( 'Организуем доставку выбранным способом и таможенное оформление', 'atk-ved' ) ],
                ];
                foreach ( $steps as $i => $step ) : ?>
                <li class="step js-reveal" style="--delay:<?php echo $i * 80; ?>ms">
                    <span class="step__num" aria-hidden="true">
                        <?php echo str_pad( $i + 1, 2, '0', STR_PAD_LEFT ); ?>
                    </span>
                    <div class="step__body">
                        <h3 class="step__title"><?php echo esc_html( $step['title'] ); ?></h3>
                        <p class="step__desc"><?php echo esc_html( $step['desc'] ); ?></p>
                    </div>
                </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>


    <!-- ====================================================
         FAQ
    ===================================================== -->
    <section class="faq" id="faq" aria-labelledby="faq-heading">
        <div class="container">
            <h2 id="faq-heading" class="section-title">
                <?php _e( 'Частые вопросы', 'atk-ved' ); ?>
            </h2>
            <dl class="faq__list">
                <?php
                $faqs = [
                    [ 'q' => __( 'Какой минимальный заказ?',           'atk-ved' ), 'a' => __( 'Минимальный заказ зависит от типа товара и способа доставки — обычно от $1 000. Работаем как с крупными, так и с небольшими партиями.', 'atk-ved' ) ],
                    [ 'q' => __( 'Сколько стоят ваши услуги?',         'atk-ved' ), 'a' => __( 'Стоимость рассчитывается индивидуально по объёму и сложности заказа. Воспользуйтесь калькулятором для предварительного расчёта.', 'atk-ved' ) ],
                    [ 'q' => __( 'Как происходит оплата?',             'atk-ved' ), 'a' => __( 'Работаем по предоплате 50%, остаток — после получения товара на складе в России. Принимаем безналичный расчёт.', 'atk-ved' ) ],
                    [ 'q' => __( 'Какие гарантии вы даёте?',           'atk-ved' ), 'a' => __( 'Заключаем официальный договор, предоставляем все необходимые документы и отчёты. Страхуем грузы и несём ответственность за их сохранность.', 'atk-ved' ) ],
                    [ 'q' => __( 'Сколько времени займёт доставка?',   'atk-ved' ), 'a' => __( 'Авиа — 5–10 дней, ЖД — 15–20 дней, Море — 30–45 дней. Точные сроки зависят от таможенного оформления.', 'atk-ved' ) ],
                    [ 'q' => __( 'Можно ли отследить посылку?',        'atk-ved' ), 'a' => __( 'Да, мы предоставляем полную отчётность и возможность отслеживания груза на всех этапах через личный кабинет.', 'atk-ved' ) ],
                ];
                foreach ( $faqs as $idx => $item ) :
                    $id = 'faq-' . $idx;
                ?>
                <div class="faq__item js-faq-item">
                    <dt>
                        <button
                            type="button"
                            class="faq__question"
                            aria-expanded="false"
                            aria-controls="<?php echo esc_attr( $id ); ?>"
                        >
                            <span><?php echo esc_html( $item['q'] ); ?></span>
                            <span class="faq__icon" aria-hidden="true"></span>
                        </button>
                    </dt>
                    <dd
                        id="<?php echo esc_attr( $id ); ?>"
                        class="faq__answer"
                        hidden
                    >
                        <p><?php echo esc_html( $item['a'] ); ?></p>
                    </dd>
                </div>
                <?php endforeach; ?>
            </dl>
        </div>
    </section>


    <!-- ====================================================
         ДОКУМЕНТЫ И ОТЗЫВЫ
    ===================================================== -->
    <section class="testimonials" id="testimonial-files" aria-labelledby="testimonials-heading">
        <div class="container">
            <h2 id="testimonials-heading" class="section-title">
                <?php _e( 'Документы и отзывы', 'atk-ved' ); ?>
            </h2>
            <p class="testimonials__sub">
                <?php _e( 'Официальные благодарственные письма и отзывы наших клиентов', 'atk-ved' ); ?>
            </p>

            <?php
            $files = function_exists( 'atk_ved_get_testimonial_files' )
                ? atk_ved_get_testimonial_files()
                : [];
            ?>

            <?php if ( $files ) : ?>
            <ul class="testimonials__grid" role="list">
                <?php foreach ( $files as $file ) : ?>
                <li class="testimonial-card js-reveal">
                    <?php if ( ! empty( $file['thumbnail'] ) ) : ?>
                        <div class="testimonial-card__thumb" aria-hidden="true">
                            <img
                                src="<?php echo esc_url( $file['thumbnail'] ); ?>"
                                alt=""
                                width="280"
                                height="200"
                                loading="lazy"
                                decoding="async"
                            >
                        </div>
                    <?php else : ?>
                        <div class="testimonial-card__icon" aria-hidden="true">
                            <?php echo atk_ved_icon( $file['file_type'] === 'pdf' ? 'file-pdf' : 'file' ); ?>
                        </div>
                    <?php endif; ?>

                    <div class="testimonial-card__body">
                        <h3><?php echo esc_html( $file['title'] ); ?></h3>
                        <?php if ( ! empty( $file['company'] ) ) : ?>
                            <p class="testimonial-card__company"><?php echo esc_html( $file['company'] ); ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="testimonial-card__footer">
                        <time datetime="<?php echo esc_attr( $file['date_iso'] ?? '' ); ?>">
                            <?php echo esc_html( $file['date'] ); ?>
                        </time>
                        <a
                            href="<?php echo esc_url( $file['file_url'] ); ?>"
                            class="btn btn--sm btn--outline"
                            target="_blank"
                            rel="noopener noreferrer"
                            download
                            aria-label="<?php echo esc_attr( sprintf( __( 'Скачать: %s', 'atk-ved' ), $file['title'] ) ); ?>"
                        >
                            <?php echo atk_ved_icon( 'download' ); ?>
                            <?php _e( 'Скачать', 'atk-ved' ); ?>
                        </a>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else : ?>
            <p class="testimonials__empty" role="status">
                <?php _e( 'Файлы отзывов скоро появятся', 'atk-ved' ); ?>
            </p>
            <?php endif; ?>

        </div>
    </section>


    <!-- ====================================================
         ТЕКСТОВЫЕ ОТЗЫВЫ
    ===================================================== -->
    <section class="reviews" id="reviews" aria-labelledby="reviews-heading">
        <div class="container">
            <h2 id="reviews-heading" class="section-title">
                <?php _e( 'Отзывы о сотрудничестве', 'atk-ved' ); ?>
            </h2>
            <ul class="reviews__grid" role="list">
                <?php
                // TODO: заменить на данные из CPT / ACF
                $reviews = [
                    [ 'initials' => 'ИП', 'name' => 'Иван П.',    'text' => __( 'Отличная работа, всё чётко и в срок! Рекомендую.', 'atk-ved' ) ],
                    [ 'initials' => 'МС', 'name' => 'Мария С.',   'text' => __( 'Помогли найти качественный товар по хорошей цене.', 'atk-ved' ) ],
                    [ 'initials' => 'АК', 'name' => 'Алексей К.', 'text' => __( 'Работаем уже 2 года, всем доволен.', 'atk-ved' ) ],
                    [ 'initials' => 'ОД', 'name' => 'Ольга Д.',   'text' => __( 'Профессиональный подход к делу.', 'atk-ved' ) ],
                ];
                foreach ( $reviews as $r ) : ?>
                <li class="review-card js-reveal" role="listitem">
                    <div class="review-card__avatar" aria-hidden="true">
                        <?php echo esc_html( $r['initials'] ); ?>
                    </div>
                    <blockquote>
                        <p><?php echo esc_html( $r['text'] ); ?></p>
                        <footer>
                            <cite><?php echo esc_html( $r['name'] ); ?></cite>
                        </footer>
                    </blockquote>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>


    <!-- ====================================================
         КОНТАКТЫ
    ===================================================== -->
    <section class="contact" id="contact" aria-labelledby="contact-heading">
        <div class="container">
            <div class="contact__inner">

                <div class="contact__text">
                    <h2 id="contact-heading">
                        <?php _e( 'Не нашли ответ на свой вопрос?', 'atk-ved' ); ?>
                    </h2>
                    <p><?php _e( 'Оставьте контакты — менеджер свяжется с вами в течение 15 минут.', 'atk-ved' ); ?></p>
                </div>

                <div class="contact__form">
                    <?php atk_ved_render_contact_form(); ?>
                </div>

            </div>
        </div>
    </section>


    <!-- ====================================================
         КАРТА
    ===================================================== -->
    <?php
    $map_url = get_theme_mod( 'atk_ved_map_embed', '' );
    if ( $map_url ) :
    ?>
    <section class="map" aria-label="<?php esc_attr_e( 'Карта офиса', 'atk-ved' ); ?>">
        <iframe
            src="<?php echo esc_url( $map_url ); ?>"
            title="<?php esc_attr_e( 'Расположение офиса АТК ВЭД на карте', 'atk-ved' ); ?>"
            width="100%"
            height="400"
            style="border:0"
            allowfullscreen
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
        ></iframe>
    </section>
    <?php endif; ?>

</main>

<?php get_footer(); ?>