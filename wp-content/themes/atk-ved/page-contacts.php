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
            <div class="hero-content">
                <h1><?php _e('Свяжитесь с нами', 'atk-ved'); ?></h1>
                <p><?php _e('Мы всегда на связи и готовы помочь с вашими вопросами', 'atk-ved'); ?></p>
                <div class="hero-stats">
                    <div class="stat">
                        <span class="stat-value">15 <?php _e('мин', 'atk-ved'); ?></span>
                        <span class="stat-label"><?php _e('Время ответа', 'atk-ved'); ?></span>
                    </div>
                    <div class="stat">
                        <span class="stat-value">24/7</span>
                        <span class="stat-label"><?php _e('Онлайн-поддержка', 'atk-ved'); ?></span>
                    </div>
                    <div class="stat">
                        <span class="stat-value">5+</span>
                        <span class="stat-label"><?php _e('Способов связи', 'atk-ved'); ?></span>
                    </div>
                </div>
            </div>
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
                        <div class="contact-method__icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                        </div>
                        <div class="contact-method__content">
                            <h3><?php _e('Телефон', 'atk-ved'); ?></h3>
                            <a href="tel:<?php echo esc_attr(str_replace([' ', '(', ')', '-'], '', $company['phone'])); ?>" class="contact-link">
                                <?php echo esc_html($company['phone']); ?>
                            </a>
                            <p class="contact-note"><?php _e('Пн-Пт: 9:00 - 18:00 (МСК)', 'atk-ved'); ?></p>
                        </div>
                    </div>

                    <div class="contact-method">
                        <div class="contact-method__icon"><i class="bi bi-envelope-fill"></i></div>
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
                        <div class="contact-method__icon"><i class="bi bi-whatsapp"></i></div>
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
                        <div class="contact-method__icon"><i class="bi bi-telegram"></i></div>
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
                        <div class="contact-method__icon"><i class="bi bi-geo-alt-fill"></i></div>
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
                        <input type="text" name="website" style="position:absolute;left:-9999px;width:1px;height:1px;" tabindex="-1" autocomplete="off" aria-hidden="true">
                        
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
