<?php
/**
 * Улучшенные UI компоненты
 * 
 * @package ATK_VED
 * @since 2.2.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Шорткод для улучшенной формы обратной связи
 * 
 * Usage: [enhanced_contact_form title="Остались вопросы?" description="Оставьте свои контактные данные и наш специалист свяжется с вами"]
 */
function atk_ved_enhanced_contact_form_shortcode(array $atts): string {
    $atts = shortcode_atts([
        'title' => 'Остались вопросы?',
        'description' => 'Оставьте свои контактные данные и наш специалист свяжется с вами',
        'button_text' => 'Отправить заявку',
        'show_fields' => 'name,email,phone,message', // CSV: name, email, phone, message, company
        'required_fields' => 'name,phone', // CSV: обязательные поля
        'consent_text' => 'Даю согласие на обработку персональных данных',
        'privacy_text' => 'Согласен с политикой конфиденциальности',
    ], $atts);

    $fields = explode(',', $atts['show_fields']);
    $required = explode(',', $atts['required_fields']);

    ob_start();
    ?>
    <div class="enhanced-contact-form">
        <div class="form-header">
            <h3><?php echo esc_html($atts['title']); ?></h3>
            <p><?php echo esc_html($atts['description']); ?></p>
        </div>
        
        <form class="contact-form-enhanced" id="contactFormEnhanced" method="post">
            <?php wp_nonce_field('atk_ved_enhanced_form', 'enhanced_form_nonce'); ?>
            
            <?php if (in_array('name', $fields)): ?>
            <div class="form-group">
                <label for="enhanced_name" class="form-label">
                    Имя <?php if (in_array('name', $required)): ?><span class="required">*</span><?php endif; ?>
                </label>
                <input type="text" 
                       id="enhanced_name" 
                       name="name" 
                       class="form-control" 
                       <?php if (in_array('name', $required)): ?>required<?php endif; ?>
                       placeholder="Ваше имя">
            </div>
            <?php endif; ?>
            
            <?php if (in_array('email', $fields)): ?>
            <div class="form-group">
                <label for="enhanced_email" class="form-label">
                    Email <?php if (in_array('email', $required)): ?><span class="required">*</span><?php endif; ?>
                </label>
                <input type="email" 
                       id="enhanced_email" 
                       name="email" 
                       class="form-control" 
                       <?php if (in_array('email', $required)): ?>required<?php endif; ?>
                       placeholder="your@email.com">
            </div>
            <?php endif; ?>
            
            <?php if (in_array('phone', $fields)): ?>
            <div class="form-group">
                <label for="enhanced_phone" class="form-label">
                    Телефон <?php if (in_array('phone', $required)): ?><span class="required">*</span><?php endif; ?>
                </label>
                <input type="tel" 
                       id="enhanced_phone" 
                       name="phone" 
                       class="form-control phone-input" 
                       <?php if (in_array('phone', $required)): ?>required<?php endif; ?>
                       placeholder="+7 (___) ___-__-__">
            </div>
            <?php endif; ?>
            
            <?php if (in_array('company', $fields)): ?>
            <div class="form-group">
                <label for="enhanced_company" class="form-label">Компания</label>
                <input type="text" 
                       id="enhanced_company" 
                       name="company" 
                       class="form-control" 
                       placeholder="Название компании">
            </div>
            <?php endif; ?>
            
            <?php if (in_array('message', $fields)): ?>
            <div class="form-group">
                <label for="enhanced_message" class="form-label">
                    Сообщение <?php if (in_array('message', $required)): ?><span class="required">*</span><?php endif; ?>
                </label>
                <textarea id="enhanced_message" 
                          name="message" 
                          class="form-control" 
                          rows="4" 
                          <?php if (in_array('message', $required)): ?>required<?php endif; ?>
                          placeholder="Ваше сообщение..."></textarea>
            </div>
            <?php endif; ?>
            
            <div class="form-group form-consent-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="consent" value="1" required>
                    <span class="checkmark"></span>
                    <?php echo esc_html($atts['consent_text']); ?> <span class="required">*</span>
                </label>
                
                <label class="checkbox-label">
                    <input type="checkbox" name="privacy" value="1" required>
                    <span class="checkmark"></span>
                    <?php echo esc_html($atts['privacy_text']); ?> <span class="required">*</span>
                </label>
            </div>
            
            <!-- Honeypot -->
            <div style="position: absolute; left: -9999px;" aria-hidden="true">
                <label for="hp_website">Website</label>
                <input type="text" name="hp_website" id="hp_website" value="">
            </div>
            
            <input type="hidden" name="hp_timestamp" value="<?php echo time(); ?>">
            <input type="hidden" name="action" value="atk_ved_contact_form">
            
            <button type="submit" class="cta-button cta-button-large">
                <?php echo esc_html($atts['button_text']); ?>
            </button>
        </form>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('enhanced_contact_form', 'atk_ved_enhanced_contact_form_shortcode');

/**
 * Шорткод для улучшенной карты
 * 
 * Usage: [enhanced_map address="Москва, ул. Примерная, 1" zoom="15"]
 */
function atk_ved_enhanced_map_shortcode(array $atts): string {
    $atts = shortcode_atts([
        'address' => 'Москва, Россия',
        'zoom' => '13',
        'height' => '400px',
        'show_marker' => '1',
        'marker_title' => 'Наш офис',
    ], $atts);

    $encoded_address = urlencode($atts['address']);
    $map_url = "https://www.google.com/maps/embed/v1/place?key=AIzaSyBFw0QZECsql_TFxx22y8oG0N5tr5W7uj4&q={$encoded_address}&zoom={$atts['zoom']}";

    ob_start();
    ?>
    <div class="enhanced-map" style="height: <?php echo esc_attr($atts['height']); ?>; position: relative; overflow: hidden; border-radius: 8px;">
        <iframe 
            width="100%" 
            height="100%" 
            frameborder="0" 
            style="border:0" 
            src="<?php echo esc_url($map_url); ?>" 
            allowfullscreen
            aria-label="Карта с местоположением">
        </iframe>
        <?php if ($atts['show_marker'] === '1'): ?>
        <div class="map-marker" aria-label="<?php echo esc_attr($atts['marker_title']); ?>">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="red" stroke="white" stroke-width="2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
            </svg>
        </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('enhanced_map', 'atk_ved_enhanced_map_shortcode');

/**
 * Шорткод для улучшенного таймера обратного отсчета
 * 
 * Usage: [countdown_timer date="2023-12-31 23:59:59" title="До конца акции осталось"]
 */
function atk_ved_countdown_timer_shortcode(array $atts): string {
    $atts = shortcode_atts([
        'date' => '',
        'title' => 'До окончания осталось',
        'show_days' => '1',
        'show_hours' => '1',
        'show_minutes' => '1',
        'show_seconds' => '1',
    ], $atts);

    if (empty($atts['date'])) {
        $atts['date'] = date('Y-m-d H:i:s', strtotime('+7 days'));
    }

    $date = date('c', strtotime($atts['date'])); // ISO 8601 format

    ob_start();
    ?>
    <div class="countdown-timer" data-end-date="<?php echo esc_attr($date); ?>">
        <h4 class="timer-title"><?php echo esc_html($atts['title']); ?></h4>
        <div class="timer-display">
            <?php if ($atts['show_days'] === '1'): ?>
            <div class="timer-unit">
                <span class="timer-value" data-unit="days">00</span>
                <span class="timer-label">Дней</span>
            </div>
            <?php endif; ?>
            
            <?php if ($atts['show_hours'] === '1'): ?>
            <div class="timer-unit">
                <span class="timer-value" data-unit="hours">00</span>
                <span class="timer-label">Часов</span>
            </div>
            <?php endif; ?>
            
            <?php if ($atts['show_minutes'] === '1'): ?>
            <div class="timer-unit">
                <span class="timer-value" data-unit="minutes">00</span>
                <span class="timer-label">Минут</span>
            </div>
            <?php endif; ?>
            
            <?php if ($atts['show_seconds'] === '1'): ?>
            <div class="timer-unit">
                <span class="timer-value" data-unit="seconds">00</span>
                <span class="timer-label">Секунд</span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <style>
        .countdown-timer {
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
            border-radius: 12px;
            margin: 20px 0;
        }
        
        .timer-title {
            font-size: 18px;
            margin-bottom: 15px;
            color: #2c2c2c;
        }
        
        .timer-display {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .timer-unit {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 60px;
        }
        
        .timer-value {
            background: #e31e24;
            color: white;
            font-size: 24px;
            font-weight: bold;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            margin-bottom: 5px;
        }
        
        .timer-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function initializeCountdown() {
                const timerElements = document.querySelectorAll('.countdown-timer');
                
                timerElements.forEach(function(timerEl) {
                    const endDate = new Date(timerEl.dataset.endDate).getTime();
                    
                    function updateTimer() {
                        const now = new Date().getTime();
                        const distance = endDate - now;
                        
                        if (distance <= 0) {
                            clearInterval(timerInterval);
                            timerEl.innerHTML = '<div class="timer-expired">Время истекло!</div>';
                            return;
                        }
                        
                        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                        
                        const dayEl = timerEl.querySelector('[data-unit="days"]');
                        const hourEl = timerEl.querySelector('[data-unit="hours"]');
                        const minuteEl = timerEl.querySelector('[data-unit="minutes"]');
                        const secondEl = timerEl.querySelector('[data-unit="seconds"]');
                        
                        if (dayEl) dayEl.textContent = days.toString().padStart(2, '0');
                        if (hourEl) hourEl.textContent = hours.toString().padStart(2, '0');
                        if (minuteEl) minuteEl.textContent = minutes.toString().padStart(2, '0');
                        if (secondEl) secondEl.textContent = seconds.toString().padStart(2, '0');
                    }
                    
                    updateTimer(); // Initial call
                    const timerInterval = setInterval(updateTimer, 1000);
                });
            }
            
            // Initialize when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initializeCountdown);
            } else {
                initializeCountdown();
            }
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('countdown_timer', 'atk_ved_countdown_timer_shortcode');