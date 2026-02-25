<?php get_header(); ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <ul class="hero-features">
                    <li>Опытные менеджеры</li>
                    <li>Прозрачные цены</li>
                    <li>Без минимальной цены</li>
                    <li>База поставщиков</li>
                </ul>
                <h1>
                    ТОВАРЫ<br>
                    ДЛЯ МАРКЕТПЛЕЙСОВ<br>
                    <span class="highlight">ИЗ КИТАЯ</span> ОПТОМ
                </h1>
                <div class="marketplaces">
                    <div class="marketplace-item">
                        <div class="marketplace-logo">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                                <rect width="32" height="32" rx="6" fill="#FF6B00"/>
                                <text x="16" y="22" font-family="Arial" font-size="18" font-weight="bold" fill="white" text-anchor="middle">М</text>
                            </svg>
                        </div>
                        <span>МЕГАМАРКЕТ</span>
                    </div>
                    <div class="marketplace-item">
                        <div class="marketplace-logo">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                                <rect width="32" height="32" rx="6" fill="#FF6A00"/>
                                <text x="16" y="22" font-family="Arial" font-size="18" font-weight="bold" fill="white" text-anchor="middle">A</text>
                            </svg>
                        </div>
                        <span>Alibaba</span>
                    </div>
                    <div class="marketplace-item">
                        <div class="marketplace-logo">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                                <rect width="32" height="32" rx="6" fill="#CB11AB"/>
                                <text x="16" y="22" font-family="Arial" font-size="16" font-weight="bold" fill="white" text-anchor="middle">WB</text>
                            </svg>
                        </div>
                        <span>WILDBERRIES</span>
                    </div>
                    <div class="marketplace-item">
                        <div class="marketplace-logo">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                                <rect width="32" height="32" rx="6" fill="#E62E04"/>
                                <text x="16" y="22" font-family="Arial" font-size="18" font-weight="bold" fill="white" text-anchor="middle">A</text>
                            </svg>
                        </div>
                        <span>AliExpress</span>
                    </div>
                    <div class="marketplace-item">
                        <div class="marketplace-logo">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                                <rect width="32" height="32" rx="6" fill="#005BFF"/>
                                <text x="16" y="22" font-family="Arial" font-size="18" font-weight="bold" fill="white" text-anchor="middle">O</text>
                            </svg>
                        </div>
                        <span>OZON</span>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <img src="<?php echo get_template_directory_uri(); ?>/images/hero/hero-boxes.jpg" alt="Товары из Китая" loading="eager">
                <img src="<?php echo get_template_directory_uri(); ?>/images/png/logistics.png" alt="Логистика" class="logistics-overlay" loading="lazy">
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section" id="services">
    <div class="container">
        <h2 class="section-title reveal">НАШИ УСЛУГИ</h2>
        <div class="services-grid">
            <div class="service-card animate-on-scroll delay-100" data-number="01">
                <h3>Поиск товаров</h3>
                <p>Помогаем найти нужные товары на китайских площадках по вашим требованиям и бюджету</p>
            </div>
            <div class="service-card animate-on-scroll delay-200" data-number="02">
                <h3>Проверка качества</h3>
                <p>Контролируем качество продукции перед отправкой, делаем фото и видео отчеты</p>
            </div>
            <div class="service-card animate-on-scroll delay-300" data-number="03">
                <h3>Доставка грузов</h3>
                <p>Организуем доставку любым удобным способом: авиа, море, ж/д, авто</p>
            </div>
            <div class="service-card animate-on-scroll delay-400" data-number="04">
                <h3>Таможенное оформление</h3>
                <p>Берем на себя все вопросы таможенного оформления и сертификации</p>
            </div>
            <div class="service-card animate-on-scroll delay-500" data-number="05">
                <h3>Складская логистика</h3>
                <p>Предоставляем услуги хранения и обработки грузов на наших складах</p>
            </div>
            <div class="service-card animate-on-scroll delay-600" data-number="06">
                <h3>Консультации</h3>
                <p>Консультируем по всем вопросам работы с Китаем и маркетплейсами</p>
            </div>
        </div>
    </div>
</section>

<!-- Search Section -->
<section class="search-section" id="search">
    <div class="container">
        <div class="search-content">
            <div class="search-text">
                <h2>НАЙДЕМ ТОВАР В КИТАЕ ПО ВАШЕМУ ЗАПРОСУ И ПОЛУЧИМ САМОЕ ВЫГОДНОЕ ПРЕДЛОЖЕНИЕ ОТ ПОСТАВЩИКА</h2>
                <p>Мы поможем найти, выкупить и доставить товары из Китая на самых выгодных условиях</p>
            </div>
            <div class="search-form-block">
                <form class="quick-search-form" id="quickSearchForm">
                    <input type="text" name="name" placeholder="Ваше имя" required>
                    <input type="tel" name="phone" placeholder="Ваш номер телефона" required>
                    <button type="submit" class="cta-button">ОСТАВИТЬ ЗАЯВКУ</button>
                    <label class="privacy-label">
                        <input type="checkbox" name="privacy" required>
                        <span>Отправляя ваши данные, вы соглашаетесь с политикой конфиденциальности</span>
                    </label>
                </form>
            </div>
            <div class="search-image">
                <img src="<?php echo get_template_directory_uri(); ?>/images/sections/container-search.jpg" alt="Поиск товаров в Китае">
            </div>
        </div>
    </div>
</section>

<!-- Delivery Section -->
<section class="delivery-section" id="delivery">
    <div class="container">
        <h2 class="section-title">СПОСОБЫ И СРОКИ ДОСТАВКИ ГРУЗОВ</h2>
        <div class="delivery-content">
            <div class="delivery-image">
                <img src="<?php echo get_template_directory_uri(); ?>/images/sections/container.png" alt="Контейнер">
            </div>
            <div class="delivery-info">
                <p>Мы предлагаем различные варианты доставки грузов из Китая в зависимости от ваших потребностей, сроков и бюджета.</p>
                <div class="delivery-features">
                    <div class="feature-item">
                        <span class="feature-icon">✓</span>
                        <span>Полное таможенное оформление</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">✓</span>
                        <span>Страхование грузов</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">✓</span>
                        <span>Отслеживание в реальном времени</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">✓</span>
                        <span>Доставка до двери</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="delivery-methods">
            <div class="method-card">
                <div class="method-icon">✈️</div>
                <h3>Авиа</h3>
                <p>5-10 дней</p>
                <span class="method-desc">Быстрая доставка для срочных грузов</span>
            </div>
            <div class="method-card">
                <div class="method-icon">🚢</div>
                <h3>Море</h3>
                <p>30-45 дней</p>
                <span class="method-desc">Экономичный вариант для крупных партий</span>
            </div>
            <div class="method-card">
                <div class="method-icon">🚂</div>
                <h3>ЖД</h3>
                <p>15-20 дней</p>
                <span class="method-desc">Оптимальное соотношение цены и скорости</span>
            </div>
        </div>
    </div>
</section>

<!-- Calculator Section -->
<section class="calculator-section" id="calculator">
    <div class="container">
        <h2 class="section-title">КАЛЬКУЛЯТОР СТОИМОСТИ ДОСТАВКИ</h2>
        <div class="calculator-wrapper">
            <div class="calculator-info">
                <h3>Рассчитайте стоимость доставки вашего груза</h3>
                <p>Наш калькулятор поможет вам быстро узнать приблизительную стоимость доставки груза из Китая. Укажите параметры вашего груза и выберите способ доставки.</p>
                <div class="calculator-features">
                    <div class="calculator-feature">
                        <span class="calculator-feature-icon">📦</span>
                        <span>Учет объемного веса</span>
                    </div>
                    <div class="calculator-feature">
                        <span class="calculator-feature-icon">🛡️</span>
                        <span>Страхование груза</span>
                    </div>
                    <div class="calculator-feature">
                        <span class="calculator-feature-icon">📋</span>
                        <span>Таможенное оформление</span>
                    </div>
                    <div class="calculator-feature">
                        <span class="calculator-feature-icon">💼</span>
                        <span>Услуги компании</span>
                    </div>
                </div>
            </div>
            <div class="calculator-form">
                <h3>Параметры груза</h3>
                <form id="calculator-form">
                    <div class="form-row">
                        <div class="form-field">
                            <label for="calc-weight">
                                Вес груза
                                <span class="tooltip" title="Фактический вес груза в килограммах">ℹ️</span>
                            </label>
                            <div class="input-suffix" data-suffix="кг">
                                <input type="text" id="calc-weight" placeholder="100" required>
                            </div>
                        </div>
                        <div class="form-field">
                            <label for="calc-volume">
                                Объем груза
                                <span class="tooltip" title="Объем в кубических метрах (длина × ширина × высота)">ℹ️</span>
                            </label>
                            <div class="input-suffix" data-suffix="м³">
                                <input type="text" id="calc-volume" placeholder="1" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-field">
                            <label for="calc-cost">
                                Стоимость товара
                                <span class="tooltip" title="Стоимость товара для расчета страховки и таможни">ℹ️</span>
                            </label>
                            <div class="input-suffix" data-suffix="₽">
                                <input type="text" id="calc-cost" placeholder="50000" required>
                            </div>
                        </div>
                        <div class="form-field">
                            <label for="calc-method">Способ доставки</label>
                            <select id="calc-method" required>
                                <option value="air">Авиа (5-10 дней)</option>
                                <option value="rail" selected>ЖД (15-20 дней)</option>
                                <option value="sea">Море (30-45 дней)</option>
                            </select>
                        </div>
                    </div>
                    <div class="calculator-actions">
                        <button type="button" id="calc-submit" class="cta-button">Рассчитать</button>
                        <button type="button" id="calc-compare" class="cta-button secondary">Сравнить все</button>
                    </div>
                    <div id="calc-result"></div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Advantages Section -->
<section class="advantages-section">
    <div class="container">
        <h2 class="section-title">ПОЧЕМУ ВЫБИРАЮТ НАС</h2>
        <div class="advantages-grid">
            <div class="advantage-card">
                <div class="advantage-icon">🏆</div>
                <h3>Опыт работы</h3>
                <p>Более 5 лет успешной работы на рынке импорта из Китая</p>
            </div>
            <div class="advantage-card">
                <div class="advantage-icon">💰</div>
                <h3>Выгодные цены</h3>
                <p>Прямые контракты с производителями без посредников</p>
            </div>
            <div class="advantage-card">
                <div class="advantage-icon">🔒</div>
                <h3>Гарантии</h3>
                <p>Официальный договор и полное юридическое сопровождение</p>
            </div>
            <div class="advantage-card">
                <div class="advantage-icon">⚡</div>
                <h3>Быстрая работа</h3>
                <p>Оперативная обработка заказов и доставка в срок</p>
            </div>
        </div>
    </div>
</section>

<!-- Steps Section -->
<section class="steps-section" id="steps">
    <div class="container">
        <h2 class="section-title">ЭТАПЫ СОТРУДНИЧЕСТВА</h2>
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">01</div>
                <h3>Заявка</h3>
                <p>Вы оставляете заявку на сайте или связываетесь с нами любым удобным способом</p>
            </div>
            <div class="step-card">
                <div class="step-number">02</div>
                <h3>Консультация</h3>
                <p>Наш менеджер связывается с вами и уточняет все детали заказа</p>
            </div>
            <div class="step-card">
                <div class="step-number">03</div>
                <h3>Поиск товара</h3>
                <p>Находим нужные товары, согласовываем цены и условия с поставщиками</p>
            </div>
            <div class="step-card">
                <div class="step-number">04</div>
                <h3>Оплата</h3>
                <p>Вы вносите предоплату, мы выкупаем товар у поставщика</p>
            </div>
            <div class="step-card">
                <div class="step-number">05</div>
                <h3>Контроль качества</h3>
                <p>Проверяем товар на нашем складе в Китае, делаем фото/видео отчет</p>
            </div>
            <div class="step-card">
                <div class="step-number">06</div>
                <h3>Доставка</h3>
                <p>Организуем доставку выбранным способом и таможенное оформление</p>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section" id="faq">
    <div class="container">
        <h2 class="section-title reveal">ЧАСТО ЗАДАВАЕМЫЕ ВОПРОСЫ</h2>
        <div class="faq-content">
            <div class="faq-image">
                <img src="<?php echo get_template_directory_uri(); ?>/images/china-map.jpg" alt="Карта Китая">
            </div>
            <div class="faq-list">
                <div class="faq-item">
                    <div class="faq-question">
                        <span class="faq-icon">💰</span>
                        <span class="faq-text">Какой минимальный заказ?</span>
                    </div>
                    <div class="faq-answer">Минимальный заказ зависит от типа товара и способа доставки. Обычно от 1000$. Мы работаем как с крупными, так и с небольшими партиями.</div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span class="faq-icon">💵</span>
                        <span class="faq-text">Сколько стоят ваши услуги?</span>
                    </div>
                    <div class="faq-answer">Стоимость услуг рассчитывается индивидуально в зависимости от объема и сложности заказа. Используйте наш калькулятор для предварительного расчета.</div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span class="faq-icon">💳</span>
                        <span class="faq-text">Как происходит оплата?</span>
                    </div>
                    <div class="faq-answer">Работаем по предоплате 50%, остаток после получения товара на складе в России. Принимаем оплату по безналичному расчету.</div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span class="faq-icon">🛡️</span>
                        <span class="faq-text">Какие гарантии вы даете?</span>
                    </div>
                    <div class="faq-answer">Заключаем официальный договор, предоставляем все необходимые документы и отчеты. Страхуем грузы и несем ответственность за сохранность.</div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span class="faq-icon">⏱️</span>
                        <span class="faq-text">Сколько времени занимает доставка?</span>
                    </div>
                    <div class="faq-answer">Авиа: 5-10 дней, ЖД: 15-20 дней, Море: 30-45 дней. Сроки зависят от выбранного способа доставки и таможенного оформления.</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonial Files Section -->
<section class="testimonial-files-section" id="testimonial-files">
    <div class="container">
        <h2 class="section-title">ДОКУМЕНТЫ И ОТЗЫВЫ</h2>
        <p style="text-align: center; color: #666; margin-bottom: 40px;">Официальные благодарственные письма и отзывы наших клиентов</p>
        
        <?php
        $testimonial_files = atk_ved_get_testimonial_files();
        
        if (!empty($testimonial_files)): ?>
            <div class="testimonial-files-grid">
                <?php foreach ($testimonial_files as $file): ?>
                    <div class="testimonial-file-card">
                        <?php if ($file['thumbnail']): ?>
                            <div class="file-preview">
                                <img src="<?php echo esc_url($file['thumbnail']); ?>" alt="<?php echo esc_attr($file['title']); ?>" loading="lazy">
                            </div>
                        <?php else: ?>
                            <div class="file-icon">
                                <?php if ($file['file_type'] === 'pdf'): ?>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                        <polyline points="10 9 9 9 8 9"></polyline>
                                    </svg>
                                <?php else: ?>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                        <polyline points="13 2 13 9 20 9"></polyline>
                                    </svg>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="file-info">
                            <h3><?php echo esc_html($file['title']); ?></h3>
                            <?php if ($file['company']): ?>
                                <div class="file-company"><?php echo esc_html($file['company']); ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="file-meta">
                            <span class="file-date"><?php echo esc_html($file['date']); ?></span>
                            <a href="<?php echo esc_url($file['file_url']); ?>" class="file-download" target="_blank" download>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                                Скачать
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-files">
                <div class="no-files-icon">📄</div>
                <p>Файлы отзывов скоро появятся</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Reviews Section -->
<section class="reviews-section" id="reviews">
    <div class="container">
        <h2 class="section-title">ОТЗЫВЫ О СОТРУДНИЧЕСТВЕ</h2>
        <div class="reviews-grid">
            <div class="review-card">
                <div class="review-avatar">ИП</div>
                <h4>Иван П.</h4>
                <p>Отличная работа, все четко и в срок! Рекомендую.</p>
            </div>
            <div class="review-card">
                <div class="review-avatar">МС</div>
                <h4>Мария С.</h4>
                <p>Помогли найти качественный товар по хорошей цене</p>
            </div>
            <div class="review-card">
                <div class="review-avatar">АК</div>
                <h4>Алексей К.</h4>
                <p>Работаем уже 2 года, всем доволен</p>
            </div>
            <div class="review-card">
                <div class="review-avatar">ОД</div>
                <h4>Ольга Д.</h4>
                <p>Профессиональный подход к делу</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section" id="contact">
    <div class="container">
        <div class="contact-content">
            <div class="contact-info">
                <h2>НЕ НАШЛИ ОТВЕТ НА СВОЙ ВОПРОС?</h2>
                <p>Оставьте свои контакты или задайте вопрос в форме.<br>
                В течение 15 минут с вами свяжется наш менеджер и ответит на все ваши вопросы.</p>
                
                <form class="contact-form" id="contactForm">
                    <input type="text" name="name" placeholder="Ваше имя" required>
                    <input type="tel" name="phone" placeholder="Ваш номер телефона" required>
                    <textarea name="message" placeholder="Задайте вопрос" rows="4"></textarea>
                    <button type="submit" class="cta-button">Оставить заявку</button>
                    <label class="privacy-label">
                        <input type="checkbox" name="privacy" required>
                        <span>Отправляя ваши данные, вы соглашаетесь с политикой конфиденциальности</span>
                    </label>
                </form>
            </div>
            <div class="contact-image">
                <img src="<?php echo get_template_directory_uri(); ?>/images/sections/pagoda.png" alt="Пагода">
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="map-section">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2245.!2d37.6173!3d55.7558!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNTXCsDQ1JzIwLjkiTiAzN8KwMzcnMDIuMyJF!5e0!3m2!1sru!2sru!4v1234567890" allowfullscreen="" loading="lazy"></iframe>
</section>

<?php get_footer(); ?>
