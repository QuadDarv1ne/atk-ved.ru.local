<?php
/**
 * Чистый минималистичный лендинг
 * Соответствует дизайну из макета
 * 
 * @package ATK_VED
 */

defined('ABSPATH') || exit;
?>

<!-- HERO СЕКЦИЯ -->
<section class="hero-clean">
    <div class="container">
        <h1 class="hero-title-clean">
            Товары для маркетплейсов<br>
            <span class="highlight">из Китая оптом</span>
        </h1>
        <p class="hero-subtitle-clean">
            Полный цикл работы: от поиска поставщика до доставки на ваш склад
        </p>
        <div class="hero-cta">
            <a href="#contact" class="btn-primary-clean">Оставить заявку</a>
        </div>
    </div>
</section>

<!-- УСЛУГИ -->
<section class="services-clean" id="services">
    <div class="container">
        <div class="services-header-clean text-center">
            <h2 class="section-title-clean">Наши услуги</h2>
            <p class="section-subtitle-clean">
                Полный спектр услуг для работы с Китаем
            </p>
        </div>
        
        <div class="services-grid-clean">
            <?php
            $services = [
                [
                    'icon' => '<i class="bi bi-search"></i>',
                    'title' => 'Поиск товаров',
                    'desc' => 'Находим нужные товары на китайских площадках по вашим требованиям'
                ],
                [
                    'icon' => '<i class="bi bi-check-circle-fill"></i>',
                    'title' => 'Проверка качества',
                    'desc' => 'Контролируем качество до отправки, делаем фото и видеоотчёты'
                ],
                [
                    'icon' => '<i class="bi bi-truck"></i>',
                    'title' => 'Доставка грузов',
                    'desc' => 'Организуем доставку авиа, морем, ЖД или авто'
                ],
                [
                    'icon' => '<i class="bi bi-file-text"></i>',
                    'title' => 'Таможенное оформление',
                    'desc' => 'Берём на себя таможенное оформление и сертификацию'
                ],
                [
                    'icon' => '<i class="bi bi-building"></i>',
                    'title' => 'Складская логистика',
                    'desc' => 'Хранение и обработка грузов на наших складах в Китае'
                ],
                [
                    'icon' => '<i class="bi bi-chat-dots"></i>',
                    'title' => 'Консультации',
                    'desc' => 'Консультируем по всем вопросам работы с Китаем'
                ]
            ];
            
            foreach ($services as $service): ?>
            <div class="service-card-clean">
                <div class="service-icon-clean"><?php echo $service['icon']; ?></div>
                <h3 class="service-title-clean"><?php echo $service['title']; ?></h3>
                <p class="service-desc-clean"><?php echo $service['desc']; ?></p>
                <a href="#contact" class="service-link-clean">
                    Подробнее
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- СПОСОБЫ ДОСТАВКИ -->
<section class="delivery-clean" id="delivery">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title-clean">Способы и сроки доставки</h2>
            <p class="section-subtitle-clean">
                Выберите оптимальный вариант для вашего груза
            </p>
        </div>
        
        <div class="delivery-grid-clean">
            <div class="delivery-card-clean">
                <div class="delivery-icon-clean"><i class="bi bi-airplane"></i></div>
                <h3 class="delivery-title-clean">Авиа</h3>
                <p class="delivery-time-clean">7-10 дней</p>
                <p class="delivery-price-clean">от $5/кг</p>
            </div>
            
            <div class="delivery-card-clean">
                <div class="delivery-icon-clean"><i class="bi bi-train-front"></i></div>
                <h3 class="delivery-title-clean">Ж/Д</h3>
                <p class="delivery-time-clean">20-25 дней</p>
                <p class="delivery-price-clean">от $2/кг</p>
            </div>
            
            <div class="delivery-card-clean">
                <div class="delivery-icon-clean"><i class="bi bi-ship"></i></div>
                <h3 class="delivery-title-clean">Море</h3>
                <p class="delivery-time-clean">35-45 дней</p>
                <p class="delivery-price-clean">от $1/кг</p>
            </div>
        </div>
    </div>
</section>

<!-- ЭТАПЫ СОТРУДНИЧЕСТВА -->
<section class="steps-clean" id="steps">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title-clean">Этапы сотрудничества</h2>
            <p class="section-subtitle-clean">
                Простой и понятный процесс работы
            </p>
        </div>
        
        <div class="steps-grid-clean">
            <?php
            $steps = [
                ['title' => 'Заявка', 'desc' => 'Оставьте заявку на сайте или свяжитесь с нами'],
                ['title' => 'Расчёт', 'desc' => 'Рассчитаем стоимость и сроки доставки'],
                ['title' => 'Договор', 'desc' => 'Заключаем официальный договор'],
                ['title' => 'Поиск', 'desc' => 'Находим товары и поставщиков'],
                ['title' => 'Контроль', 'desc' => 'Проверяем качество перед отправкой'],
                ['title' => 'Доставка', 'desc' => 'Доставляем груз на ваш склад']
            ];
            
            foreach ($steps as $index => $step): ?>
            <div class="step-card-clean">
                <div class="step-number-clean"><?php echo $index + 1; ?></div>
                <h3 class="step-title-clean"><?php echo $step['title']; ?></h3>
                <p class="step-desc-clean"><?php echo $step['desc']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="faq-clean" id="faq">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title-clean">Часто задаваемые вопросы</h2>
            <p class="section-subtitle-clean">
                Ответы на популярные вопросы
            </p>
        </div>
        
        <div class="faq-list-clean">
            <?php
            $faqs = [
                [
                    'q' => 'Какой минимальный заказ?',
                    'a' => 'У нас нет минимального заказа. Работаем с любыми объёмами.'
                ],
                [
                    'q' => 'Сколько стоит доставка?',
                    'a' => 'Стоимость зависит от веса, объёма и способа доставки. Рассчитаем индивидуально.'
                ],
                [
                    'q' => 'Как отследить груз?',
                    'a' => 'Предоставляем трек-номер для отслеживания на всех этапах доставки.'
                ],
                [
                    'q' => 'Есть ли гарантии?',
                    'a' => 'Да, работаем по официальному договору с полным юридическим сопровождением.'
                ]
            ];
            
            foreach ($faqs as $faq): ?>
            <div class="faq-item-clean">
                <button class="faq-question-clean" type="button">
                    <?php echo $faq['q']; ?>
                    <svg class="faq-icon-clean" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-answer-clean">
                    <div class="faq-answer-content-clean">
                        <?php echo $faq['a']; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ОТЗЫВЫ -->
<section class="reviews-clean" id="reviews">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title-clean">Отзывы клиентов</h2>
            <p class="section-subtitle-clean">
                Что говорят о нас наши клиенты
            </p>
        </div>
        
        <div class="reviews-grid-clean">
            <?php
            $reviews = [
                [
                    'name' => 'Иван Петров',
                    'date' => '15 февраля 2026',
                    'rating' => 5,
                    'text' => 'Отличная компания! Быстро нашли нужные товары, доставили в срок. Рекомендую!'
                ],
                [
                    'name' => 'Мария Сидорова',
                    'date' => '10 февраля 2026',
                    'rating' => 5,
                    'text' => 'Работаем уже год. Всё чётко, прозрачно, без скрытых платежей. Спасибо!'
                ],
                [
                    'name' => 'Алексей Иванов',
                    'date' => '5 февраля 2026',
                    'rating' => 5,
                    'text' => 'Профессиональный подход, помогли с таможней. Очень довольны сотрудничеством.'
                ],
                [
                    'name' => 'Елена Смирнова',
                    'date' => '1 февраля 2026',
                    'rating' => 5,
                    'text' => 'Лучшие цены на рынке! Персональный менеджер всегда на связи.'
                ]
            ];
            
            foreach ($reviews as $review): ?>
            <div class="review-card-clean">
                <div class="review-header-clean">
                    <div class="review-avatar-clean">
                        <?php echo mb_substr($review['name'], 0, 1); ?>
                    </div>
                    <div class="review-author-clean">
                        <div class="review-name-clean"><?php echo $review['name']; ?></div>
                        <div class="review-date-clean"><?php echo $review['date']; ?></div>
                    </div>
                </div>
                <div class="review-rating-clean">
                    <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                        <span class="review-star-clean">★</span>
                    <?php endfor; ?>
                </div>
                <p class="review-text-clean"><?php echo $review['text']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- КОНТАКТЫ -->
<section class="contact-clean" id="contact">
    <div class="container">
        <div class="contact-grid-clean">
            <div class="contact-info-clean">
                <h2 class="section-title-clean">Свяжитесь с нами</h2>
                <p class="section-subtitle-clean">
                    Оставьте заявку и получите бесплатную консультацию
                </p>
                
                <ul class="contact-benefits">
                    <li><i class="bi bi-check-circle-fill"></i> Расчёт стоимости за 15 минут</li>
                    <li><i class="bi bi-check-circle-fill"></i> Скидка 10% на первую доставку</li>
                    <li><i class="bi bi-check-circle-fill"></i> Персональный менеджер</li>
                    <li><i class="bi bi-check-circle-fill"></i> Полная поддержка 24/7</li>
                </ul>
            </div>
            
            <div class="contact-form-clean">
                <form method="post" action="">
                    <div class="form-group-clean">
                        <label class="form-label-clean" for="name">Ваше имя</label>
                        <input type="text" id="name" name="name" class="form-input-clean" required>
                    </div>
                    
                    <div class="form-group-clean">
                        <label class="form-label-clean" for="phone">Телефон</label>
                        <input type="tel" id="phone" name="phone" class="form-input-clean" required>
                    </div>
                    
                    <div class="form-group-clean">
                        <label class="form-label-clean" for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-input-clean" required>
                    </div>
                    
                    <div class="form-group-clean">
                        <label class="form-label-clean" for="message">Сообщение</label>
                        <textarea id="message" name="message" class="form-textarea-clean"></textarea>
                    </div>
                    
                    <button type="submit" class="btn-primary-clean">Отправить заявку</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- КАРТА -->
<section class="map-clean">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title-clean">Наш офис</h2>
            <p class="section-subtitle-clean">
                Приходите к нам в гости
            </p>
        </div>
        
        <div class="map-container-clean">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2245.!2d37.6173!3d55.7558!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNTXCsDQ1JzIwLjkiTiAzN8KwMzcnMDIuMyJF!5e0!3m2!1sru!2sru!4v1234567890"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</section>

<script>
// FAQ аккордеон
document.querySelectorAll('.faq-question-clean').forEach(button => {
    button.addEventListener('click', () => {
        const item = button.parentElement;
        const isActive = item.classList.contains('active');
        
        // Закрыть все
        document.querySelectorAll('.faq-item-clean').forEach(i => {
            i.classList.remove('active');
        });
        
        // Открыть текущий
        if (!isActive) {
            item.classList.add('active');
        }
    });
});
</script>
