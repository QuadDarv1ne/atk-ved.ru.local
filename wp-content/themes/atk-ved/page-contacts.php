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
                        <div class="contact-method__icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                        </div>
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
                        <div class="contact-method__icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                            </svg>
                        </div>
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
                        <div class="contact-method__icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                            </svg>
                        </div>
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
                        <div class="contact-method__icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
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
