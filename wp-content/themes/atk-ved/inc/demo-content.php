<?php
/**
 * Demo Content Data
 * Данные для демонстрационного контента
 *
 * @package ATK_VED
 * @since 2.9.0
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Получение демонстрационных данных
 *
 * @return array
 */
function atk_ved_get_demo_data(): array {
	return array(
		'services'     => array(
			array(
				'title'       => 'Проверка и контроль качества',
				'description' => 'Профессиональная проверка товаров на соответствие стандартам качества перед отправкой',
				'icon'        => 'quality',
				'price'       => 'от 500 ₽ за единицу',
				'features'    => array(
					'Визуальный осмотр',
					'Проверка на дефекты',
					'Тестирование функциональности',
					'Фотоотчетность',
				),
			),
			array(
				'title'       => 'Консолидация грузов',
				'description' => 'Сбор и объединение товаров от разных поставщиков в один груз',
				'icon'        => 'consolidation',
				'price'       => 'от 1000 ₽ за партию',
				'features'    => array(
					'Сборка на складе',
					'Упаковка и маркировка',
					'Инвентаризация',
					'Документация',
				),
			),
			array(
				'title'       => 'Маркировка и упаковка',
				'description' => 'Профессиональная упаковка и маркировка товаров для безопасной доставки',
				'icon'        => 'packaging',
				'price'       => 'от 300 ₽ за единицу',
				'features'    => array(
					'Подбор упаковки',
					'Защитная упаковка',
					'Маркировка по ГОСТ',
					'Этикетирование',
				),
			),
			array(
				'title'       => 'Доставка до двери',
				'description' => 'Полная логистика от склада в Китае до вашего адреса в РФ',
				'icon'        => 'delivery',
				'price'       => 'от 2000 ₽ за кг',
				'features'    => array(
					'Доставка до порта',
					'Таможенное оформление',
					'Внутренняя логистика',
					'Доставка до двери',
				),
			),
		),
		'testimonials' => array(
			array(
				'text'     => 'Работаем с АТК ВЭД уже больше года. Качество услуг на высоте, доставка всегда вовремя. Особенно радует прозрачность в работе - все этапы видны в личном кабинете.',
				'author'   => 'Иван Петров',
				'position' => 'Директор',
				'company'  => 'OZON Marketplace',
				'rating'   => 5,
			),
			array(
				'text'     => 'Отличный сервис! Помогли с импортом товаров из Китая под ключ. Все документы в порядке, товары пришли в отличном состоянии. Спасибо за профессионализм!',
				'author'   => 'Мария Сидорова',
				'position' => 'Основатель',
				'company'  => 'Wildberries Store',
				'rating'   => 5,
			),
			array(
				'text'     => 'Сотрудничаем с этой компанией уже 2 года. Надежные партнеры, всегда выполняют свои обязательства. Рекомендую всем, кто работает с импортом из Китая.',
				'author'   => 'Алексей Козлов',
				'position' => 'CEO',
				'company'  => 'AliExpress Business',
				'rating'   => 4,
			),
		),
		'team'         => array(
			array(
				'name'         => 'Анна Волкова',
				'position'     => 'Руководитель отдела импорта',
				'photo'        => '',
				'bio'          => 'Опыт работы в логистике более 8 лет. Специализируется на импорте товаров из Китая.',
				'social_links' => array(
					array(
						'name' => 'LinkedIn',
						'url'  => '#',
						'icon' => 'linkedin',
					),
					array(
						'name' => 'Email',
						'url'  => 'mailto:anna@atk-ved.ru',
						'icon' => 'email',
					),
				),
			),
			array(
				'name'         => 'Дмитрий Смирнов',
				'position'     => 'Главный логист',
				'photo'        => '',
				'bio'          => 'Эксперт в международных перевозках. Оптимизирует логистические процессы компании.',
				'social_links' => array(
					array(
						'name' => 'LinkedIn',
						'url'  => '#',
						'icon' => 'linkedin',
					),
					array(
						'name' => 'Email',
						'url'  => 'mailto:dmitry@atk-ved.ru',
						'icon' => 'email',
					),
				),
			),
			array(
				'name'         => 'Елена Петрова',
				'position'     => 'Специалист по таможне',
				'photo'        => '',
				'bio'          => 'Сертифицированный специалист по таможенному оформлению. Обеспечивает легальность всех операций.',
				'social_links' => array(
					array(
						'name' => 'LinkedIn',
						'url'  => '#',
						'icon' => 'linkedin',
					),
					array(
						'name' => 'Email',
						'url'  => 'mailto:elena@atk-ved.ru',
						'icon' => 'email',
					),
				),
			),
		),
		'statistics'   => array(
			array(
				'number'      => 1500,
				'label'       => 'Довольных клиентов',
				'description' => 'Работаем с 2018 года',
				'icon'        => 'users',
			),
			array(
				'number'      => 50000,
				'label'       => 'Отправленных посылок',
				'description' => 'Без возвратов и жалоб',
				'icon'        => 'packages',
			),
			array(
				'number'      => 98,
				'label'       => '% клиентов возвращаются',
				'description' => 'Качество и надежность',
				'icon'        => 'satisfaction',
			),
			array(
				'number'      => 24,
				'label'       => 'Часа доставка',
				'description' => 'Среднее время доставки',
				'icon'        => 'clock',
			),
		),
		'faq'          => array(
			array(
				'question' => 'Как быстро происходит доставка товаров из Китая?',
				'answer'   => 'Среднее время доставки составляет 15-25 дней от момента оплаты до получения товара в РФ. Точное время зависит от типа товара и выбранного способа доставки.',
			),
			array(
				'question' => 'Какие документы необходимы для импорта?',
				'answer'   => 'Для импорта необходимы: паспорт, ИНН, договор с поставщиком, инвойс, упаковочный лист, сертификаты соответствия (при необходимости). Мы помогаем в оформлении всех документов.',
			),
			array(
				'question' => 'Предоставляете ли вы гарантию на товары?',
				'answer'   => 'Да, мы предоставляем гарантию на все услуги. В случае обнаружения брака или несоответствия товара спецификации, мы организуем возврат или замену за свой счет.',
			),
			array(
				'question' => 'Какие способы оплаты вы принимаете?',
				'answer'   => 'Мы работаем с безналичной оплатой по договору. Возможна оплата через расчетный счет, а также через популярные платежные системы.',
			),
			array(
				'question' => 'Можно ли отследить груз в пути?',
				'answer'   => 'Да, каждый клиент получает доступ к личному кабинету, где в режиме реального времени можно отслеживать местоположение груза и этапы его доставки.',
			),
		),
	);
}

/**
 * Получение демонстрационного контента для страниц
 *
 * @return array
 */
function atk_ved_get_demo_pages_content(): array {
	return array(
		'home'     => array(
			'title'   => 'Главная',
			'content' => '
                <!-- Hero Section -->
                <section class="hero-section">
                    <div class="container">
                        <div class="hero-content">
                            <h1>Товары из Китая для маркетплейсов</h1>
                            <p>Импорт товаров из Китая под ключ. Проверка качества, консолидация, доставка до двери.</p>
                            <div class="hero-cta">
                                <a href="#contact" class="cta-button">Получить консультацию</a>
                                <a href="#services" class="secondary-button">Наши услуги</a>
                            </div>
                        </div>
                        <div class="hero-image">
                            <img src="' . get_template_directory_uri() . '/images/hero/hero-boxes.jpg" alt="Товары из Китая" loading="eager">
                            <img src="' . get_template_directory_uri() . '/images/png/logistics.png" alt="Логистика" class="logistics-overlay" loading="lazy">
                        </div>
                    </div>
                </section>
                
                <!-- Services Section -->
                <section id="services" class="services-section">
                    <div class="container">
                        <h2>Наши услуги</h2>
                        <div class="services-grid">
                            <!-- Services will be added dynamically -->
                        </div>
                    </div>
                </section>
                
                <!-- Statistics Section -->
                <section class="statistics-section">
                    <div class="container">
                        <!-- Statistics will be added dynamically -->
                    </div>
                </section>
                
                <!-- Testimonials Section -->
                <section class="testimonials-section">
                    <div class="container">
                        <!-- Testimonials will be added dynamically -->
                    </div>
                </section>
                
                <!-- FAQ Section -->
                <section id="faq" class="faq-section">
                    <div class="container">
                        <!-- FAQ will be added dynamically -->
                    </div>
                </section>
                
                <!-- Contact Section -->
                <section id="contact" class="contact-section">
                    <div class="container">
                        <h2>Связаться с нами</h2>
                        <!-- Contact form will be added dynamically -->
                    </div>
                </section>
            ',
		),
		'about'    => array(
			'title'   => 'О компании',
			'content' => '
                <div class="about-page">
                    <div class="container">
                        <h1>О компании АТК ВЭД</h1>
                        
                        <div class="about-content">
                            <div class="about-text">
                                <h2>Наша миссия</h2>
                                <p>Мы помогаем российским предпринимателям успешно импортировать товары из Китая, обеспечивая полный контроль качества и надежную логистику.</p>
                                
                                <h2>История компании</h2>
                                <p>Компания АТК ВЭД была основана в 2018 году группой специалистов с многолетним опытом в международной торговле. За эти годы мы стали надежным партнером для более чем 1500 клиентов.</p>
                                
                                <h2>Наши преимущества</h2>
                                <ul>
                                    <li>Более 5 лет опыта в импорте из Китая</li>
                                    <li>Собственный склад в Гуанчжоу</li>
                                    <li>Сертифицированные специалисты по таможне</li>
                                    <li>Полная прозрачность на всех этапах</li>
                                    <li>Гарантия качества услуг</li>
                                </ul>
                            </div>
                            
                            <div class="about-image">
                                <img src="' . get_template_directory_uri() . '/images/about/team.jpg" alt="Наша команда">
                            </div>
                        </div>
                    </div>
                </div>
            ',
		),
		'services' => array(
			'title'   => 'Услуги',
			'content' => '
                <div class="services-page">
                    <div class="container">
                        <h1>Наши услуги</h1>
                        <p class="page-description">Полный спектр услуг по импорту товаров из Китая</p>
                        
                        <div class="services-list">
                            <!-- Services will be added dynamically -->
                        </div>
                        
                        <div class="services-process">
                            <h2>Как мы работаем</h2>
                            <div class="process-steps">
                                <div class="step">
                                    <div class="step-number">1</div>
                                    <h3>Консультация</h3>
                                    <p>Обсуждаем ваши потребности и подбираем оптимальное решение</p>
                                </div>
                                <div class="step">
                                    <div class="step-number">2</div>
                                    <h3>Поиск поставщика</h3>
                                    <p>Находим надежных поставщиков в Китае по вашим требованиям</p>
                                </div>
                                <div class="step">
                                    <div class="step-number">3</div>
                                    <h3>Проверка качества</h3>
                                    <p>Профессионально проверяем товары перед отправкой</p>
                                </div>
                                <div class="step">
                                    <div class="step-number">4</div>
                                    <h3>Доставка</h3>
                                    <p>Организуем быструю и надежную доставку до вашего адреса</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            ',
		),
	);
}
