<?php
/**
 * Template Name: Контакты
 *
 * @package ATK_VED
 */

defined('ABSPATH') || exit;

$company = atk_ved_get_company_info();

get_header();
?>

<main id="main-content" class="contacts-page">
    
    <!-- Hero -->
    <section class="contacts-hero">
        <div class="container">
            <h1>Контакты</h1>
            <p>Свяжитесь с нами удобным способом</p>
        </div>
    </section>

    <!-- Contact Info -->
    <section class="contacts-info">
        <div class="container">
            <div class="contacts-grid">
                
                <!-- Contact Methods -->
                <div class="contacts-methods">
                    <h2>Как с нами связаться</h2>
                    
                    <div class="contact-method">
                        <div class="contact-method__icon">📞</div>
                        <div class="contact-method__content">
                            <h3>Телефон</h3>
                            <a href="tel:<?php echo esc_attr(str_replace([' ', '(', ')', '-'], '', $company['phone'])); ?>">
                                <?php echo esc_html($company['phone']); ?>
                            </a>
                            <p>Пн-Пт: 9:00 - 18:00 (МСК)</p>
                        </div>
                    </div>

                    <div class="contact-method">
                        <div class="contact-method__icon">✉️</div>
                        <div class="contact-method__content">
                            <h3>Email</h3>
                            <a href="mailto:<?php echo esc_attr($company['email']); ?>">
                                <?php echo esc_html($company['email']); ?>
                            </a>
                            <p>Ответим в течение 1 часа</p>
                        </div>
                    </div>

                    <?php if (!empty($company['whatsapp'])): ?>
                    <div class="contact-method">
                        <div class="contact-method__icon">💬</div>
                        <div class="contact-method__content">
                            <h3>WhatsApp</h3>
                            <a href="<?php echo esc_url($company['whatsapp']); ?>" target="_blank" rel="noopener">
                                Написать в WhatsApp
                            </a>
                            <p>Быстрые ответы 24/7</p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($company['telegram'])): ?>
                    <div class="contact-method">
                        <div class="contact-method__icon">✈️</div>
                        <div class="contact-method__content">
                            <h3>Telegram</h3>
                            <a href="<?php echo esc_url($company['telegram']); ?>" target="_blank" rel="noopener">
                                Написать в Telegram
                            </a>
                            <p>Онлайн-консультации</p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="contact-method">
                        <div class="contact-method__icon">📍</div>
                        <div class="contact-method__content">
                            <h3>Адрес офиса</h3>
                            <p><?php echo esc_html($company['address']); ?></p>
                            <p><?php echo esc_html($company['city']); ?></p>
                        </div>
                    </div>

                </div>

                <!-- Contact Form -->
                <div class="contacts-form">
                    <h2>Оставьте заявку</h2>
                    <p>Мы свяжемся с вами в течение 15 минут</p>
                    
                    <form id="contact-form" class="contact-form" method="post">
                        <?php wp_nonce_field('atk_contact_form', 'contact_nonce'); ?>
                        
                        <!-- Honeypot field -->
                        <input type="text" name="website" style="display:none" tabindex="-1" autocomplete="off">
                        
                        <div class="form-group">
                            <label for="contact-name">Ваше имя *</label>
                            <input type="text" id="contact-name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="contact-phone">Телефон *</label>
                            <input type="tel" id="contact-phone" name="phone" required>
                        </div>

                        <div class="form-group">
                            <label for="contact-email">Email</label>
                            <input type="email" id="contact-email" name="email">
                        </div>

                        <div class="form-group">
                            <label for="contact-message">Сообщение</label>
                            <textarea id="contact-message" name="message" rows="5"></textarea>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="agree" required>
                                <span>Согласен с <a href="<?php echo esc_url(home_url('/privacy/')); ?>">политикой конфиденциальности</a></span>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg">
                            Отправить заявку
                        </button>

                        <div class="form-message" style="display: none;"></div>
                    </form>
                </div>

            </div>
        </div>
    </section>

    <!-- Map -->
    <section class="contacts-map">
        <div class="map-placeholder">
            <p>Здесь будет карта с местоположением офиса</p>
            <p class="map-placeholder__note">Для подключения карты используйте Яндекс.Карты или Google Maps API</p>
        </div>
    </section>

    <!-- Working Hours -->
    <section class="contacts-hours">
        <div class="container">
            <h2>Режим работы</h2>
            <div class="hours-grid">
                <div class="hours-item">
                    <h3>Офис</h3>
                    <p>Понедельник - Пятница: 9:00 - 18:00</p>
                    <p>Суббота - Воскресенье: Выходной</p>
                </div>
                <div class="hours-item">
                    <h3>Онлайн-поддержка</h3>
                    <p>WhatsApp / Telegram: 24/7</p>
                    <p>Email: Ответ в течение 1 часа</p>
                </div>
                <div class="hours-item">
                    <h3>Склад в Китае</h3>
                    <p>Работает круглосуточно</p>
                    <p>Приём грузов 24/7</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="contacts-faq">
        <div class="container">
            <h2>Частые вопросы</h2>
            <div class="faq-list">
                <div class="faq-item">
                    <h3>Как быстро вы отвечаете на заявки?</h3>
                    <p>В рабочее время (9:00-18:00 МСК) мы отвечаем в течение 15 минут. В нерабочее время — на следующий рабочий день. Через WhatsApp/Telegram можем ответить круглосуточно.</p>
                </div>
                <div class="faq-item">
                    <h3>Можно ли приехать в офис без записи?</h3>
                    <p>Да, но лучше предварительно позвонить, чтобы убедиться, что нужный специалист будет на месте.</p>
                </div>
                <div class="faq-item">
                    <h3>Работаете ли вы с физическими лицами?</h3>
                    <p>Да, мы работаем как с юридическими, так и с физическими лицами. Условия сотрудничества одинаковые.</p>
                </div>
                <div class="faq-item">
                    <h3>Как получить коммерческое предложение?</h3>
                    <p>Оставьте заявку на сайте или напишите нам на email с описанием груза. Мы подготовим КП в течение 2 часов.</p>
                </div>
            </div>
        </div>
    </section>

</main>

<?php
get_footer();
