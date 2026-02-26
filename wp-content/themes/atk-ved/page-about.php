<?php
/**
 * Template Name: О компании
 *
 * @package ATK_VED
 */

defined('ABSPATH') || exit;

get_header();
?>

<main id="main-content" class="about-page">
    
    <!-- Hero Section -->
    <section class="about-hero">
        <div class="container">
            <div class="about-hero__content">
                <h1 class="about-hero__title">О компании АТК ВЭД</h1>
                <p class="about-hero__subtitle">Профессиональная логистика из Китая с 2018 года</p>
            </div>
        </div>
    </section>

    <!-- Company Info -->
    <section class="about-info">
        <div class="container">
            <div class="about-info__grid">
                <div class="about-info__text">
                    <h2>Кто мы</h2>
                    <p>АТК ВЭД — это команда профессионалов в области международной логистики и внешнеэкономической деятельности. Мы специализируемся на доставке грузов из Китая в Россию всеми видами транспорта.</p>
                    <p>За <?php echo date('Y') - 2018; ?> лет работы мы помогли более 500 компаниям наладить стабильные поставки товаров из Китая. Наш опыт и знание всех тонкостей таможенного оформления позволяют клиентам экономить время и деньги.</p>
                </div>
                <div class="about-info__stats">
                    <div class="stat-card">
                        <div class="stat-card__number"><?php echo date('Y') - 2018; ?>+</div>
                        <div class="stat-card__label">лет на рынке</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card__number">500+</div>
                        <div class="stat-card__label">довольных клиентов</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card__number">15</div>
                        <div class="stat-card__label">дней средняя доставка</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card__number">99%</div>
                        <div class="stat-card__label">грузов без проблем</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission -->
    <section class="about-mission">
        <div class="container">
            <div class="about-mission__content">
                <h2>Наша миссия</h2>
                <p>Делать международную логистику простой и понятной для каждого бизнеса. Мы берём на себя все сложности таможенного оформления и доставки, чтобы вы могли сосредоточиться на развитии своего дела.</p>
            </div>
        </div>
    </section>

    <!-- Values -->
    <section class="about-values">
        <div class="container">
            <h2>Наши ценности</h2>
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-card__icon">🎯</div>
                    <h3>Надёжность</h3>
                    <p>Выполняем обязательства в срок. Каждый груз застрахован и отслеживается на всех этапах.</p>
                </div>
                <div class="value-card">
                    <div class="value-card__icon">💡</div>
                    <h3>Прозрачность</h3>
                    <p>Честные цены без скрытых комиссий. Вы всегда знаете, за что платите.</p>
                </div>
                <div class="value-card">
                    <div class="value-card__icon">⚡</div>
                    <h3>Скорость</h3>
                    <p>Оптимизированные маршруты и быстрое таможенное оформление. Ваш груз не простаивает.</p>
                </div>
                <div class="value-card">
                    <div class="value-card__icon">🤝</div>
                    <h3>Партнёрство</h3>
                    <p>Мы не просто перевозчики — мы ваши партнёры в развитии бизнеса.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team -->
    <section class="about-team">
        <div class="container">
            <h2>Наша команда</h2>
            <p class="about-team__intro">Профессионалы с многолетним опытом в логистике и ВЭД</p>
            <div class="team-grid">
                <div class="team-member">
                    <div class="team-member__photo">
                        <div class="team-member__placeholder">👤</div>
                    </div>
                    <h3>Отдел логистики</h3>
                    <p>Планирование маршрутов и контроль доставки</p>
                </div>
                <div class="team-member">
                    <div class="team-member__photo">
                        <div class="team-member__placeholder">👤</div>
                    </div>
                    <h3>Таможенные брокеры</h3>
                    <p>Оформление документов и прохождение таможни</p>
                </div>
                <div class="team-member">
                    <div class="team-member__photo">
                        <div class="team-member__placeholder">👤</div>
                    </div>
                    <h3>Менеджеры</h3>
                    <p>Консультации и поддержка клиентов 24/7</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="about-why">
        <div class="container">
            <h2>Почему выбирают нас</h2>
            <div class="why-grid">
                <div class="why-item">
                    <div class="why-item__number">01</div>
                    <h3>Собственные склады в Китае</h3>
                    <p>Консолидация грузов и проверка качества перед отправкой</p>
                </div>
                <div class="why-item">
                    <div class="why-item__number">02</div>
                    <h3>Прямые договоры с перевозчиками</h3>
                    <p>Лучшие тарифы без посредников</p>
                </div>
                <div class="why-item">
                    <div class="why-item__number">03</div>
                    <h3>Опытные таможенные брокеры</h3>
                    <p>Быстрое оформление и минимум рисков</p>
                </div>
                <div class="why-item">
                    <div class="why-item__number">04</div>
                    <h3>Страхование грузов</h3>
                    <p>Полная компенсация в случае утери или повреждения</p>
                </div>
                <div class="why-item">
                    <div class="why-item__number">05</div>
                    <h3>Онлайн-отслеживание</h3>
                    <p>Контроль груза в режиме реального времени</p>
                </div>
                <div class="why-item">
                    <div class="why-item__number">06</div>
                    <h3>Гибкие условия оплаты</h3>
                    <p>Отсрочка платежа для постоянных клиентов</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="about-cta">
        <div class="container">
            <div class="about-cta__content">
                <h2>Готовы начать сотрудничество?</h2>
                <p>Получите бесплатную консультацию и расчёт стоимости доставки</p>
                <a href="<?php echo esc_url(home_url('/contacts/')); ?>" class="btn btn-primary btn-lg">
                    Связаться с нами
                </a>
            </div>
        </div>
    </section>

</main>

<?php
get_footer();
