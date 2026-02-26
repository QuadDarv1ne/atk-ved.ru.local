<?php
/**
 * Template Name: Услуги
 *
 * @package ATK_VED
 */

defined('ABSPATH') || exit;

get_header();
?>

<main id="main-content" class="services-page">
    
    <!-- Hero -->
    <section class="services-hero">
        <div class="container">
            <h1>Наши услуги</h1>
            <p>Полный спектр логистических услуг для вашего бизнеса</p>
        </div>
    </section>

    <!-- Main Services -->
    <section class="services-main">
        <div class="container">
            
            <div class="service-detail">
                <div class="service-detail__content">
                    <div class="service-detail__icon"><i class="bi bi-airplane"></i></div>
                    <h2>Авиадоставка из Китая</h2>
                    <p class="service-detail__lead">Самый быстрый способ доставки грузов — от 5 до 10 дней</p>
                    <div class="service-detail__features">
                        <div class="feature-item">
                            <span class="feature-item__icon"><i class="bi bi-lightning-charge-fill"></i></span>
                            <div>
                                <h4>Скорость</h4>
                                <p>5-10 дней от склада в Китае до вашего склада</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <span class="feature-item__icon"><i class="bi bi-box-seam"></i></span>
                            <div>
                                <h4>Подходит для</h4>
                                <p>Срочные грузы, дорогостоящие товары, малые партии</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <span class="feature-item__icon"><i class="bi bi-cash-stack"></i></span>
                            <div>
                                <h4>Стоимость</h4>
                                <p>От 8 $/кг, зависит от объёма и веса груза</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="service-detail service-detail--reverse">
                <div class="service-detail__content">
                    <div class="service-detail__icon"><i class="bi bi-ship"></i></div>
                    <h2>Морская доставка</h2>
                    <p class="service-detail__lead">Экономичный вариант для крупных партий — 30-45 дней</p>
                    <div class="service-detail__features">
                        <div class="feature-item">
                            <span class="feature-item__icon"><i class="bi bi-currency-dollar"></i></span>
                            <div>
                                <h4>Экономия</h4>
                                <p>Самый выгодный тариф — от 2 $/кг</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <span class="feature-item__icon"><i class="bi bi-box-seam"></i></span>
                            <div>
                                <h4>Подходит для</h4>
                                <p>Крупные партии, негабаритные грузы, контейнерные перевозки</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <span class="feature-item__icon"><i class="bi bi-ship"></i></span>
                            <div>
                                <h4>Варианты</h4>
                                <p>FCL (полный контейнер) или LCL (сборный груз)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="service-detail">
                <div class="service-detail__content">
                    <div class="service-detail__icon"><i class="bi bi-train-front"></i></div>
                    <h2>Железнодорожная доставка</h2>
                    <p class="service-detail__lead">Оптимальное соотношение цены и скорости — 15-20 дней</p>
                    <div class="service-detail__features">
                        <div class="feature-item">
                            <span class="feature-item__icon"><i class="bi bi-balance-scale"></i></span>
                            <div>
                                <h4>Баланс</h4>
                                <p>Быстрее моря, дешевле авиа — от 4 $/кг</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <span class="feature-item__icon"><i class="bi bi-box-seam"></i></span>
                            <div>
                                <h4>Подходит для</h4>
                                <p>Средние партии, регулярные поставки</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <span class="feature-item__icon"><i class="bi bi-globe"></i></span>
                            <div>
                                <h4>Маршруты</h4>
                                <p>Через Казахстан или Монголию</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Additional Services -->
    <section class="services-additional">
        <div class="container">
            <h2>Дополнительные услуги</h2>
            <div class="services-grid">
                
                <div class="service-card">
                    <div class="service-card__icon"><i class="bi bi-file-text"></i></div>
                    <h3>Таможенное оформление</h3>
                    <p>Полное сопровождение груза через таможню. Подготовка документов, декларирование, уплата пошлин.</p>
                    <ul>
                        <li>Расчёт таможенных платежей</li>
                        <li>Подготовка ГТД</li>
                        <li>Сертификация товаров</li>
                        <li>Консультации по ВЭД</li>
                    </ul>
                </div>

                <div class="service-card">
                    <div class="service-card__icon"><i class="bi bi-building"></i></div>
                    <h3>Поиск поставщиков</h3>
                    <p>Помощь в поиске надёжных производителей в Китае. Проверка фабрик, переговоры, контроль качества.</p>
                    <ul>
                        <li>Поиск по вашим требованиям</li>
                        <li>Проверка репутации</li>
                        <li>Переговоры на китайском</li>
                        <li>Контроль производства</li>
                    </ul>
                </div>

                <div class="service-card">
                    <div class="service-card__icon"><i class="bi bi-box-seam"></i></div>
                    <h3>Складское хранение</h3>
                    <p>Собственные склады в Китае и России. Консолидация грузов, упаковка, маркировка.</p>
                    <ul>
                        <li>Бесплатное хранение 7 дней</li>
                        <li>Консолидация партий</li>
                        <li>Переупаковка</li>
                        <li>Маркировка по FBS</li>
                    </ul>
                </div>

                <div class="service-card">
                    <div class="service-card__icon"><i class="bi bi-search"></i></div>
                    <h3>Инспекция товаров</h3>
                    <p>Проверка качества продукции перед отправкой. Фото и видео отчёты, замеры, тестирование.</p>
                    <ul>
                        <li>Проверка по чек-листу</li>
                        <li>Фото/видео отчёт</li>
                        <li>Замеры и взвешивание</li>
                        <li>Тестирование образцов</li>
                    </ul>
                </div>

                <div class="service-card">
                    <div class="service-card__icon"><i class="bi bi-shield-fill-check"></i></div>
                    <h3>Страхование грузов</h3>
                    <p>Защита вашего груза от рисков утери или повреждения. Полная компенсация по страховке.</p>
                    <ul>
                        <li>Страхование от 0.5%</li>
                        <li>Покрытие всех рисков</li>
                        <li>Быстрая выплата</li>
                        <li>Работа с крупными СК</li>
                    </ul>
                </div>

                <div class="service-card">
                    <div class="service-card__icon"><i class="bi bi-truck"></i></div>
                    <h3>Доставка по России</h3>
                    <p>Развозка грузов по всей России после таможенного оформления. До двери или до склада.</p>
                    <ul>
                        <li>Доставка в любой город</li>
                        <li>До двери или терминала</li>
                        <li>Экспресс-доставка</li>
                        <li>Отслеживание груза</li>
                    </ul>
                </div>

            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="services-process">
        <div class="container">
            <h2>Как мы работаем</h2>
            <div class="process-steps">
                <div class="process-step">
                    <div class="process-step__number">1</div>
                    <h3>Заявка</h3>
                    <p>Вы оставляете заявку на сайте или связываетесь с менеджером</p>
                </div>
                <div class="process-step">
                    <div class="process-step__number">2</div>
                    <h3>Расчёт</h3>
                    <p>Рассчитываем стоимость и сроки доставки под ваш груз</p>
                </div>
                <div class="process-step">
                    <div class="process-step__number">3</div>
                    <h3>Договор</h3>
                    <p>Заключаем договор и согласовываем все детали</p>
                </div>
                <div class="process-step">
                    <div class="process-step__number">4</div>
                    <h3>Забор груза</h3>
                    <p>Забираем груз у поставщика в Китае</p>
                </div>
                <div class="process-step">
                    <div class="process-step__number">5</div>
                    <h3>Доставка</h3>
                    <p>Везём груз выбранным способом с отслеживанием</p>
                </div>
                <div class="process-step">
                    <div class="process-step__number">6</div>
                    <h3>Таможня</h3>
                    <p>Оформляем груз на таможне в России</p>
                </div>
                <div class="process-step">
                    <div class="process-step__number">7</div>
                    <h3>Получение</h3>
                    <p>Доставляем груз на ваш склад или терминал</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="services-cta">
        <div class="container">
            <h2>Нужна консультация?</h2>
            <p>Оставьте заявку, и мы рассчитаем стоимость доставки вашего груза</p>
            <a href="<?php echo esc_url(home_url('/contacts/')); ?>" class="btn btn-primary btn-lg">
                Получить расчёт
            </a>
        </div>
    </section>

</main>

<?php
get_footer();
