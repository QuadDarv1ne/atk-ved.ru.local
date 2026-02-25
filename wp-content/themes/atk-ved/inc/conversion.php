<?php
/**
 * Conversion Features: Exit-Intent Popup, Live Notifications
 * 
 * @package ATK_VED
 * @since 2.6.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/* ==========================================================================
   EXIT-INTENT POPUP
   ========================================================================== */

/**
 * Exit-Intent Popup виджет
 */
function atk_ved_exit_intent_popup(): void {
    $enabled = get_theme_mod('atk_ved_exit_popup_enabled', false);
    
    if (!$enabled) {
        return;
    }
    
    $title = get_theme_mod('atk_ved_exit_popup_title', __('Подождите!', 'atk-ved'));
    $subtitle = get_theme_mod('atk_ved_exit_popup_subtitle', __('Получите скидку 10% на первую поставку', 'atk-ved'));
    $offer = get_theme_mod('atk_ved_exit_popup_offer', __('Оставьте заявку сейчас и получите консультацию бесплатно', 'atk-ved'));
    $button_text = get_theme_mod('atk_ved_exit_popup_button', __('Получить скидку', 'atk-ved'));
    $dismiss_text = get_theme_mod('atk_ved_exit_popup_dismiss', __('Нет, спасибо', 'atk-ved'));
    $show_delay = intval(get_theme_mod('atk_ved_exit_popup_delay', 5000));
    $cookie_expire = intval(get_theme_mod('atk_ved_exit_popup_cookie_expire', 7)); // дней
    ?>
    
    <div class="exit-intent-popup" id="exitIntentPopup" style="display: none;">
        <div class="exit-popup-overlay"></div>
        <div class="exit-popup-content">
            <button type="button" class="exit-popup-close" id="exitPopupClose" aria-label="<?php esc_attr_e('Закрыть', 'atk-ved'); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
            
            <div class="exit-popup-body">
                <div class="exit-popup-icon">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#e31e24" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                
                <h2 class="exit-popup-title"><?php echo esc_html($title); ?></h2>
                <p class="exit-popup-subtitle"><?php echo esc_html($subtitle); ?></p>
                <p class="exit-popup-offer"><?php echo esc_html($offer); ?></p>
                
                <form class="exit-popup-form" id="exitPopupForm">
                    <input type="text" name="name" placeholder="<?php esc_attr_e('Ваше имя', 'atk-ved'); ?>" required>
                    <input type="tel" name="phone" placeholder="<?php esc_attr_e('Ваш телефон', 'atk-ved'); ?>" required>
                    <input type="hidden" name="source" value="exit_popup">
                    <button type="submit" class="btn btn-primary btn-block">
                        <?php echo esc_html($button_text); ?>
                    </button>
                </form>
                
                <button type="button" class="exit-popup-dismiss" id="exitPopupDismiss">
                    <?php echo esc_html($dismiss_text); ?>
                </button>
            </div>
        </div>
    </div>
    
    <style>
        .exit-intent-popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 99999;
            display: none;
        }
        
        .exit-intent-popup.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .exit-popup-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
        }
        
        .exit-popup-content {
            position: relative;
            background: #fff;
            border-radius: 16px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            animation: popupSlideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        @keyframes popupSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .exit-popup-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: transparent;
            border: none;
            cursor: pointer;
            color: #666;
            padding: 5px;
            transition: color 0.3s;
        }
        
        .exit-popup-close:hover {
            color: #333;
        }
        
        .exit-popup-body {
            padding: 40px 30px;
            text-align: center;
        }
        
        .exit-popup-icon {
            margin-bottom: 20px;
        }
        
        .exit-popup-title {
            font-size: 28px;
            font-weight: 700;
            color: #2c2c2c;
            margin-bottom: 10px;
        }
        
        .exit-popup-subtitle {
            font-size: 18px;
            color: #e31e24;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .exit-popup-offer {
            font-size: 15px;
            color: #666;
            margin-bottom: 25px;
            line-height: 1.6;
        }
        
        .exit-popup-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .exit-popup-form input[type="text"],
        .exit-popup-form input[type="tel"] {
            padding: 14px 18px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s;
        }
        
        .exit-popup-form input:focus {
            outline: none;
            border-color: #e31e24;
        }
        
        .exit-popup-dismiss {
            background: transparent;
            border: none;
            color: #999;
            cursor: pointer;
            font-size: 14px;
            text-decoration: underline;
        }
        
        .exit-popup-dismiss:hover {
            color: #666;
        }
        
        @media (max-width: 768px) {
            .exit-popup-content {
                width: 95%;
            }
            
            .exit-popup-body {
                padding: 30px 20px;
            }
            
            .exit-popup-title {
                font-size: 24px;
            }
        }
    </style>
    
    <script>
    (function($) {
        'use strict';
        
        var $popup = $('#exitIntentPopup');
        var $closeBtn = $('#exitPopupClose');
        var $dismissBtn = $('#exitPopupDismiss');
        var $form = $('#exitPopupForm');
        var cookieName = 'atk_ved_exit_popup_dismissed';
        var cookieExpire = <?php echo $cookie_expire; ?>; // дней
        var showDelay = <?php echo $show_delay; ?>; // мс
        var hasShown = false;
        
        // Проверка cookie
        function getCookie(name) {
            var value = '; ' + document.cookie;
            var parts = value.split('; ' + name + '=');
            if (parts.length === 2) {
                return parts.pop().split(';').shift();
            }
            return null;
        }
        
        function setCookie(name, value, days) {
            var expires = '';
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = '; expires=' + date.toUTCString();
            }
            document.cookie = name + '=' + (value || '') + expires + '; path=/';
        }
        
        // Показ попапа
        function showPopup() {
            if (hasShown || getCookie(cookieName)) {
                return;
            }
            
            hasShown = true;
            $popup.addClass('active');
            
            // Отправка события в аналитику
            if (typeof ym !== 'undefined') {
                ym(<?php echo esc_js(get_theme_mod('atk_ved_metrika_id', 0)); ?>, 'reachGoal', 'exit_popup_shown');
            }
        }
        
        // Скрытие попапа
        function hidePopup() {
            $popup.removeClass('active');
            setCookie(cookieName, '1', cookieExpire);
        }
        
        // Обработчики
        $closeBtn.on('click', hidePopup);
        $dismissBtn.on('click', hidePopup);
        
        $popup.find('.exit-popup-overlay').on('click', hidePopup);
        
        // Отправка формы
        $form.on('submit', function(e) {
            e.preventDefault();
            
            var formData = {
                action: 'atk_ved_exit_popup_submit',
                nonce: '<?php echo wp_create_nonce('atk_ved_nonce'); ?>',
                name: $form.find('[name="name"]').val(),
                phone: $form.find('[name="phone"]').val(),
                source: 'exit_popup'
            };
            
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        // Отправка в AmoCRM
                        if (typeof atkVedData !== 'undefined') {
                            $.ajax({
                                url: atkVedData.ajaxUrl,
                                type: 'POST',
                                data: {
                                    action: 'atk_ved_amocrm_lead',
                                    nonce: atkVedData.nonce,
                                    name: formData.name,
                                    phone: formData.phone,
                                    source: 'Exit Popup'
                                }
                            });
                        }
                        
                        // Показ сообщения об успехе
                        $form.html('<div class="success-message"><svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#4CAF50" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg><p>Спасибо! Мы свяжемся с вами в ближайшее время.</p></div>');
                        
                        // Отправка в аналитику
                        if (typeof ym !== 'undefined') {
                            ym(<?php echo esc_js(get_theme_mod('atk_ved_metrika_id', 0)); ?>, 'reachGoal', 'exit_popup_converted');
                        }
                        
                        hidePopup();
                    }
                }
            });
        });
        
        // Exit intent detection
        var exitIntentDetected = false;
        
        $(document).on('mouseleave', function(e) {
            if (e.clientY < 0 && !exitIntentDetected) {
                exitIntentDetected = true;
                showPopup();
            }
        });
        
        // Показ по таймеру (альтернатива)
        setTimeout(function() {
            showPopup();
        }, showDelay);
        
        // Показ при попытке закрытия вкладки
        $(window).on('beforeunload', function() {
            if (!exitIntentDetected && !getCookie(cookieName)) {
                exitIntentDetected = true;
                showPopup();
            }
        });
        
    })(jQuery);
    </script>
    <?php
}
add_action('wp_footer', 'atk_ved_exit_intent_popup');

/**
 * Настройки Exit Popup в Customizer
 */
function atk_ved_exit_popup_customizer($wp_customize): void {
    $wp_customize->add_section('atk_ved_exit_popup', array(
        'title'    => __('Exit-Intent Popup', 'atk-ved'),
        'priority' => 44,
    ));

    $wp_customize->add_setting('atk_ved_exit_popup_enabled', array(
        'default'           => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));

    $wp_customize->add_control('atk_ved_exit_popup_enabled', array(
        'label'   => __('Включить popup', 'atk-ved'),
        'section' => 'atk_ved_exit_popup',
        'type'    => 'checkbox',
    ));

    $wp_customize->add_setting('atk_ved_exit_popup_title', array(
        'default'           => __('Подождите!', 'atk-ved'),
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('atk_ved_exit_popup_title', array(
        'label'   => __('Заголовок', 'atk-ved'),
        'section' => 'atk_ved_exit_popup',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('atk_ved_exit_popup_subtitle', array(
        'default'           => __('Получите скидку 10% на первую поставку', 'atk-ved'),
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('atk_ved_exit_popup_subtitle', array(
        'label'   => __('Подзаголовок', 'atk-ved'),
        'section' => 'atk_ved_exit_popup',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('atk_ved_exit_popup_offer', array(
        'default'           => __('Оставьте заявку сейчас и получите консультацию бесплатно', 'atk-ved'),
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('atk_ved_exit_popup_offer', array(
        'label'   => __('Предложение', 'atk-ved'),
        'section' => 'atk_ved_exit_popup',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('atk_ved_exit_popup_button', array(
        'default'           => __('Получить скидку', 'atk-ved'),
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('atk_ved_exit_popup_button', array(
        'label'   => __('Текст кнопки', 'atk-ved'),
        'section' => 'atk_ved_exit_popup',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('atk_ved_exit_popup_dismiss', array(
        'default'           => __('Нет, спасибо', 'atk-ved'),
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('atk_ved_exit_popup_dismiss', array(
        'label'   => __('Текст отказа', 'atk-ved'),
        'section' => 'atk_ved_exit_popup',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('atk_ved_exit_popup_delay', array(
        'default'           => 5000,
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control('atk_ved_exit_popup_delay', array(
        'label'       => __('Задержка показа (мс)', 'atk-ved'),
        'section'     => 'atk_ved_exit_popup',
        'type'        => 'number',
        'input_attrs' => array(
            'min' => 0,
            'max' => 30000,
            'step' => 1000,
        ),
    ));

    $wp_customize->add_setting('atk_ved_exit_popup_cookie_expire', array(
        'default'           => 7,
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control('atk_ved_exit_popup_cookie_expire', array(
        'label'       => __('Не показывать повторно (дней)', 'atk-ved'),
        'section'     => 'atk_ved_exit_popup',
        'type'        => 'number',
        'input_attrs' => array(
            'min' => 1,
            'max' => 365,
        ),
    ));
}
add_action('customize_register', 'atk_ved_exit_popup_customizer');

/**
 * Обработка формы Exit Popup
 */
function atk_ved_exit_popup_submit(): void {
    check_ajax_referer('atk_ved_nonce', 'nonce');
    
    $name = sanitize_text_field($_POST['name'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $source = sanitize_text_field($_POST['source'] ?? 'exit_popup');
    
    if (empty($name) || empty($phone)) {
        wp_send_json_error(array('message' => __('Заполните обязательные поля', 'atk-ved')));
    }
    
    // Логирование
    if (function_exists('atk_ved_log_user_action')) {
        atk_ved_log_user_action('Exit Popup Submission', array(
            'name' => $name,
            'phone' => $phone,
            'source' => $source,
        ));
    }
    
    wp_send_json_success(array('message' => __('Заявка принята', 'atk-ved')));
}
add_action('wp_ajax_atk_ved_exit_popup_submit', 'atk_ved_exit_popup_submit');
add_action('wp_ajax_nopriv_atk_ved_exit_popup_submit', 'atk_ved_exit_popup_submit');

/* ==========================================================================
   LIVE NOTIFICATIONS (SALES POPUP)
   ========================================================================== */

/**
 * Live уведомления о последних заказах
 */
function atk_ved_live_notifications(): void {
    $enabled = get_theme_mod('atk_ved_live_notifications_enabled', false);
    
    if (!$enabled) {
        return;
    }
    
    $notification_time = intval(get_theme_mod('atk_ved_live_notifications_time', 10)); // секунд между уведомлениями
    $notification_display = intval(get_theme_mod('atk_ved_live_notifications_display', 5)); // секунд показа
    $notification_max = intval(get_theme_mod('atk_ved_live_notifications_max', 5)); // макс. количество уведомлений за сессию
    ?>
    
    <div class="live-notification" id="liveNotification" style="display: none;">
        <div class="live-notification-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
            </svg>
        </div>
        <div class="live-notification-content">
            <div class="live-notification-title" id="liveNotificationTitle"></div>
            <div class="live-notification-message" id="liveNotificationMessage"></div>
            <div class="live-notification-time" id="liveNotificationTime"></div>
        </div>
        <button type="button" class="live-notification-close" id="liveNotificationClose">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
    
    <style>
        .live-notification {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            padding: 15px;
            display: none;
            align-items: center;
            gap: 15px;
            max-width: 350px;
            z-index: 9999;
            animation: notificationSlideIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            border-left: 4px solid #4CAF50;
        }
        
        @keyframes notificationSlideIn {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .live-notification.hiding {
            animation: notificationSlideOut 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        
        @keyframes notificationSlideOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(-30px);
            }
        }
        
        .live-notification-icon {
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            background: #e8f5e9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4CAF50;
        }
        
        .live-notification-content {
            flex: 1;
            min-width: 0;
        }
        
        .live-notification-title {
            font-weight: 600;
            font-size: 14px;
            color: #2c2c2c;
            margin-bottom: 4px;
        }
        
        .live-notification-message {
            font-size: 13px;
            color: #666;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .live-notification-time {
            font-size: 11px;
            color: #999;
            margin-top: 4px;
        }
        
        .live-notification-close {
            background: transparent;
            border: none;
            cursor: pointer;
            color: #999;
            padding: 5px;
            flex-shrink: 0;
        }
        
        .live-notification-close:hover {
            color: #333;
        }
        
        @media (max-width: 768px) {
            .live-notification {
                left: 10px;
                right: 10px;
                bottom: 10px;
                max-width: none;
            }
        }
    </style>
    
    <script>
    (function($) {
        'use strict';
        
        var $notification = $('#liveNotification');
        var $title = $('#liveNotificationTitle');
        var $message = $('#liveNotificationMessage');
        var $time = $('#liveNotificationTime');
        var $closeBtn = $('#liveNotificationClose');
        
        var notificationInterval = <?php echo $notification_time * 1000; ?>;
        var displayTime = <?php echo $notification_display * 1000; ?>;
        var maxNotifications = <?php echo $notification_max; ?>;
        var shownCount = 0;
        
        // Демо данные для уведомлений
        var notifications = [
            { name: 'Александр М.', city: 'Москва', action: 'оставил заявку' },
            { name: 'Елена К.', city: 'Санкт-Петербург', action: 'заказал расчёт доставки' },
            { name: 'Дмитрий В.', city: 'Екатеринбург', action: 'запросил консультацию' },
            { name: 'Ольга С.', city: 'Казань', action: 'оформил заказ' },
            { name: 'Игорь П.', city: 'Новосибирск', action: 'оставил заявку' },
            { name: 'Наталья Р.', city: 'Москва', action: 'заказал доставку' },
            { name: 'Андрей Т.', city: 'Владивосток', action: 'запросил прайс' },
        ];
        
        function showNotification() {
            if (shownCount >= maxNotifications) {
                return;
            }
            
            var random = notifications[Math.floor(Math.random() * notifications.length)];
            var cities = ['Москва', 'Санкт-Петербург', 'Екатеринбург', 'Казань', 'Новосибирск', 'Владивосток', 'Краснодар'];
            var actions = ['оставил заявку', 'заказал расчёт', 'оформил заказ', 'запросил консультацию'];
            
            // Случайные данные
            var name = random.name;
            var city = random.city;
            var action = random.action;
            var timeAgo = Math.floor(Math.random() * 5) + 1; // 1-5 минут назад
            
            $title.text(name + ' из ' + city);
            $message.text(action);
            $time.text(timeAgo + ' мин. назад');
            
            $notification.css('display', 'flex').removeClass('hiding');
            shownCount++;
            
            // Отправка события в аналитику
            if (typeof ym !== 'undefined') {
                ym(<?php echo esc_js(get_theme_mod('atk_ved_metrika_id', 0)); ?>, 'reachGoal', 'live_notification_shown');
            }
            
            // Скрытие через displayTime
            setTimeout(function() {
                hideNotification();
            }, displayTime);
        }
        
        function hideNotification() {
            $notification.addClass('hiding');
            
            setTimeout(function() {
                $notification.css('display', 'none').removeClass('hiding');
                
                // Показ следующего через interval
                setTimeout(function() {
                    showNotification();
                }, notificationInterval);
            }, 500);
        }
        
        $closeBtn.on('click', function() {
            hideNotification();
        });
        
        // Запуск первого уведомления через 10 секунд
        setTimeout(function() {
            showNotification();
        }, 10000);
        
    })(jQuery);
    </script>
    <?php
}
add_action('wp_footer', 'atk_ved_live_notifications');

/**
 * Настройки Live Notifications в Customizer
 */
function atk_ved_live_notifications_customizer($wp_customize): void {
    $wp_customize->add_section('atk_ved_live_notifications', array(
        'title'    => __('Live уведомления', 'atk-ved'),
        'priority' => 45,
    ));

    $wp_customize->add_setting('atk_ved_live_notifications_enabled', array(
        'default'           => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));

    $wp_customize->add_control('atk_ved_live_notifications_enabled', array(
        'label'   => __('Включить уведомления', 'atk-ved'),
        'section' => 'atk_ved_live_notifications',
        'type'    => 'checkbox',
    ));

    $wp_customize->add_setting('atk_ved_live_notifications_time', array(
        'default'           => 10,
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control('atk_ved_live_notifications_time', array(
        'label'       => __('Интервал между уведомлениями (сек)', 'atk-ved'),
        'section'     => 'atk_ved_live_notifications',
        'type'        => 'number',
        'input_attrs' => array('min' => 5, 'max' => 60),
    ));

    $wp_customize->add_setting('atk_ved_live_notifications_display', array(
        'default'           => 5,
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control('atk_ved_live_notifications_display', array(
        'label'       => __('Время показа (сек)', 'atk-ved'),
        'section'     => 'atk_ved_live_notifications',
        'type'        => 'number',
        'input_attrs' => array('min' => 3, 'max' => 15),
    ));

    $wp_customize->add_setting('atk_ved_live_notifications_max', array(
        'default'           => 5,
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control('atk_ved_live_notifications_max', array(
        'label'       => __('Макс. показов за сессию', 'atk-ved'),
        'section'     => 'atk_ved_live_notifications',
        'type'        => 'number',
        'input_attrs' => array('min' => 1, 'max' => 20),
    ));
}
add_action('customize_register', 'atk_ved_live_notifications_customizer');
