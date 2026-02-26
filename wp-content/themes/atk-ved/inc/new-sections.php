<?php
/**
 * Новые секции для лендинга
 *
 * @package ATK_VED
 * @since 3.5.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Шорткод: Калькулятор с лид-магнитом
 * Показывает результат только после ввода контактных данных
 */
function atk_ved_lead_calculator_shortcode(array $atts): string {
    $atts = shortcode_atts([
        'title' => __('Рассчитайте стоимость доставки', 'atk-ved'),
        'subtitle' => __('Получите расчёт и скидку 10% на первую доставку', 'atk-ved'),
        'cta_text' => __('Получить расчёт', 'atk-ved'),
    ], $atts);

    ob_start();
    ?>
    <section class="lead-calculator-section" id="leadCalculator">
        <div class="container">
            <div class="lead-calculator-content">
                <div class="lead-calculator-text">
                    <h2 class="section-title"><?php echo esc_html($atts['title']); ?></h2>
                    <p class="section-subtitle"><?php echo esc_html($atts['subtitle']); ?></p>
                    
                    <div class="lead-magnet-benefits">
                        <div class="benefit-item">
                            <span class="benefit-icon">📊</span>
                            <span><?php _e('Точный расчёт стоимости', 'atk-ved'); ?></span>
                        </div>
                        <div class="benefit-item">
                            <span class="benefit-icon">⏱️</span>
                            <span><?php _e('Сроки доставки по дням', 'atk-ved'); ?></span>
                        </div>
                        <div class="benefit-item">
                            <span class="benefit-icon">💰</span>
                            <span><?php _e('Скидка 10% на первую доставку', 'atk-ved'); ?></span>
                        </div>
                        <div class="benefit-item">
                            <span class="benefit-icon">📦</span>
                            <span><?php _e('Бесплатная консультация логиста', 'atk-ved'); ?></span>
                        </div>
                    </div>
                </div>

                <div class="lead-calculator-form-wrapper">
                    <!-- Шаг 1: Параметры груза -->
                    <form class="lead-calc-step step-1 active" id="leadCalcStep1">
                        <h3><?php _e('Параметры груза', 'atk-ved'); ?></h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="lead_weight"><?php _e('Вес (кг)*', 'atk-ved'); ?></label>
                                <input type="number" id="lead_weight" name="weight" min="1" required placeholder="500">
                            </div>
                            
                            <div class="form-group">
                                <label for="lead_volume"><?php _e('Объём (м³)*', 'atk-ved'); ?></label>
                                <input type="number" id="lead_volume" name="volume" min="0.1" step="0.1" required placeholder="2.5">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="lead_from"><?php _e('Откуда', 'atk-ved'); ?></label>
                                <select id="lead_from" name="from_city">
                                    <option value="Пекин"><?php _e('Пекин', 'atk-ved'); ?></option>
                                    <option value="Шанхай"><?php _e('Шанхай', 'atk-ved'); ?></option>
                                    <option value="Гуанчжоу"><?php _e('Гуанчжоу', 'atk-ved'); ?></option>
                                    <option value="Шэньчжэнь"><?php _e('Шэньчжэнь', 'atk-ved'); ?></option>
                                    <option value="Иу"><?php _e('Иу', 'atk-ved'); ?></option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="lead_to"><?php _e('Куда', 'atk-ved'); ?></label>
                                <select id="lead_to" name="to_city">
                                    <option value="Москва"><?php _e('Москва', 'atk-ved'); ?></option>
                                    <option value="Санкт-Петербург"><?php _e('Санкт-Петербург', 'atk-ved'); ?></option>
                                    <option value="Екатеринбург"><?php _e('Екатеринбург', 'atk-ved'); ?></option>
                                    <option value="Новосибирск"><?php _e('Новосибирск', 'atk-ved'); ?></option>
                                    <option value="Владивосток"><?php _e('Владивосток', 'atk-ved'); ?></option>
                                </select>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-primary btn-full">
                            <?php _e('Далее', 'atk-ved'); ?>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </form>

                    <!-- Шаг 2: Контактные данные -->
                    <form class="lead-calc-step step-2" id="leadCalcStep2">
                        <h3><?php _e('Куда отправить расчёт?', 'atk-ved'); ?></h3>
                        
                        <div class="form-group">
                            <label for="lead_name"><?php _e('Ваше имя*', 'atk-ved'); ?></label>
                            <input type="text" id="lead_name" name="name" required placeholder="Иван">
                        </div>
                        
                        <div class="form-group">
                            <label for="lead_phone"><?php _e('Телефон*', 'atk-ved'); ?></label>
                            <input type="tel" id="lead_phone" name="phone" required placeholder="+7 (___) ___-__-__">
                        </div>
                        
                        <div class="form-group">
                            <label for="lead_email"><?php _e('Email*', 'atk-ved'); ?></label>
                            <input type="email" id="lead_email" name="email" required placeholder="example@mail.ru">
                        </div>
                        
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="consent" required checked>
                                <span><?php _e('Согласен на обработку персональных данных', 'atk-ved'); ?></span>
                            </label>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn-secondary" onclick="leadCalcGoToStep(1)">
                                <?php _e('Назад', 'atk-ved'); ?>
                            </button>
                            <button type="submit" class="btn-primary">
                                <?php echo esc_html($atts['cta_text']); ?>
                            </button>
                        </div>
                        
                        <p class="form-note">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            <?php _e('Ваши данные под защитой', 'atk-ved'); ?>
                        </p>
                    </form>

                    <!-- Шаг 3: Результат -->
                    <div class="lead-calc-step step-3" id="leadCalcStep3">
                        <div class="calc-result">
                            <div class="result-icon">✓</div>
                            <h3><?php _e('Расчёт готов!', 'atk-ved'); ?></h3>
                            <p><?php _e('Менеджер свяжется с вами в течение 15 минут', 'atk-ved'); ?></p>
                            
                            <div class="result-details" id="leadResultDetails">
                                <!-- Детали заполняются через JS -->
                            </div>
                            
                            <div class="result-actions">
                                <a href="https://wa.me/79990000000" class="btn-whatsapp">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                    </svg>
                                    <?php _e('Написать в WhatsApp', 'atk-ved'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
    .lead-calculator-section {
        padding: 80px 0;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    }

    .lead-calculator-content {
        display: grid;
        grid-template-columns: 1fr 500px;
        gap: 60px;
        align-items: start;
    }

    .section-title {
        font-size: 42px;
        font-weight: 800;
        color: #2c2c2c;
        margin-bottom: 20px;
        line-height: 1.2;
    }

    .section-subtitle {
        font-size: 18px;
        color: #666;
        margin-bottom: 40px;
        line-height: 1.6;
    }

    .lead-magnet-benefits {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .benefit-item {
        display: flex;
        align-items: center;
        gap: 15px;
        font-size: 16px;
        color: #2c2c2c;
    }

    .benefit-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #e31e24, #ff6b6b);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .lead-calculator-form-wrapper {
        background: #fff;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    }

    .lead-calc-step {
        display: none;
    }

    .lead-calc-step.active {
        display: block;
        animation: fadeIn 0.4s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .lead-calc-step h3 {
        font-size: 22px;
        font-weight: 700;
        color: #2c2c2c;
        margin-bottom: 25px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #2c2c2c;
        margin-bottom: 8px;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 14px 16px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-size: 15px;
        transition: all 0.3s ease;
    }

    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: #e31e24;
        box-shadow: 0 0 0 3px rgba(227, 30, 36, 0.1);
    }

    .checkbox-label {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 13px;
        color: #666;
        cursor: pointer;
    }

    .checkbox-label input[type="checkbox"] {
        margin-top: 2px;
        flex-shrink: 0;
    }

    .btn-primary,
    .btn-secondary {
        padding: 16px 32px;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        justify-content: center;
    }

    .btn-primary {
        background: linear-gradient(135deg, #e31e24, #ff6b6b);
        color: #fff;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(227, 30, 36, 0.3);
    }

    .btn-secondary {
        background: #f0f0f0;
        color: #2c2c2c;
    }

    .btn-secondary:hover {
        background: #e0e0e0;
    }

    .btn-full {
        width: 100%;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        margin-top: 25px;
    }

    .form-actions .btn-secondary {
        flex: 1;
    }

    .form-actions .btn-primary {
        flex: 2;
    }

    .form-note {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: #999;
        margin-top: 15px;
        justify-content: center;
    }

    .form-note svg {
        color: #4caf50;
    }

    /* Результат */
    .calc-result {
        text-align: center;
        padding: 20px;
    }

    .result-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #4caf50, #45a049);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        color: #fff;
        margin: 0 auto 25px;
        animation: scaleIn 0.5s ease;
    }

    @keyframes scaleIn {
        from { transform: scale(0); }
        to { transform: scale(1); }
    }

    .result-details {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin: 25px 0;
        text-align: left;
    }

    .result-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #e0e0e0;
    }

    .result-row:last-child {
        border-bottom: none;
        font-weight: 700;
        font-size: 18px;
        color: #e31e24;
    }

    .btn-whatsapp {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 16px 32px;
        background: #25D366;
        color: #fff;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-whatsapp:hover {
        background: #128C7E;
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(37, 211, 102, 0.3);
    }

    @media (max-width: 1024px) {
        .lead-calculator-content {
            grid-template-columns: 1fr;
        }

        .lead-calculator-text {
            text-align: center;
        }

        .lead-magnet-benefits {
            max-width: 500px;
            margin: 0 auto;
        }
    }

    @media (max-width: 768px) {
        .lead-calculator-section {
            padding: 40px 0;
        }

        .section-title {
            font-size: 28px;
        }

        .section-subtitle {
            font-size: 16px;
        }

        .lead-calculator-form-wrapper {
            padding: 25px;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }

        .form-actions .btn-secondary,
        .form-actions .btn-primary {
            flex: 1;
            width: 100%;
        }
    }
    </style>

    <script>
    (function($) {
        'use strict';

        // Переход к шагу
        window.leadCalcGoToStep = function(step) {
            $('.lead-calc-step').removeClass('active');
            $('.lead-calc-step.step-' + step).addClass('active');
        };

        // Шаг 1: Параметры груза
        $('#leadCalcStep1').on('submit', function(e) {
            e.preventDefault();
            
            const weight = $('#lead_weight').val();
            const volume = $('#lead_volume').val();
            const from = $('#lead_from').val();
            const to = $('#lead_to').val();

            // Сохраняем данные
            window.leadCalcData = { weight, volume, from, to };

            // Переходим к шагу 2
            leadCalcGoToStep(2);
        });

        // Шаг 2: Контактные данные
        $('#leadCalcStep2').on('submit', function(e) {
            e.preventDefault();
            
            const name = $('#lead_name').val();
            const phone = $('#lead_phone').val();
            const email = $('#lead_email').val();

            // Сохраняем данные
            window.leadCalcData = { ...window.leadCalcData, name, phone, email };

            // Отправляем данные
            $.ajax({
                url: atkVedData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'atk_ved_lead_calculator',
                    nonce: atkVedData.nonce,
                    ...window.leadCalcData
                },
                success: function(response) {
                    if (response.success) {
                        // Показываем результат
                        showResult();
                    } else {
                        alert(response.data?.message || 'Ошибка отправки');
                    }
                },
                error: function() {
                    alert('Ошибка соединения');
                }
            });
        });

        function showResult() {
            const data = window.leadCalcData;
            
            // Рассчитываем примерную стоимость
            const baseRate = 2.5; // Ж/Д тариф
            const estimatedCost = Math.round(data.weight * baseRate);
            const days = '20-30';

            $('#leadResultDetails').html(`
                <div class="result-row">
                    <span>Маршрут:</span>
                    <strong>${data.from} → ${data.to}</strong>
                </div>
                <div class="result-row">
                    <span>Вес:</span>
                    <strong>${data.weight} кг</strong>
                </div>
                <div class="result-row">
                    <span>Объём:</span>
                    <strong>${data.volume} м³</strong>
                </div>
                <div class="result-row">
                    <span>Срок доставки:</span>
                    <strong>${days} дней</strong>
                </div>
                <div class="result-row">
                    <span>Примерная стоимость:</span>
                    <strong>от $${estimatedCost}</strong>
                </div>
            `);

            leadCalcGoToStep(3);
        }
    })(jQuery);
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('lead_calculator', 'atk_ved_lead_calculator_shortcode');

/**
 * Обработчик AJAX для лид-калькулятора
 */
add_action('wp_ajax_atk_ved_lead_calculator', 'atk_ved_handle_lead_calculator');
add_action('wp_ajax_nopriv_atk_ved_lead_calculator', 'atk_ved_handle_lead_calculator');

function atk_ved_handle_lead_calculator(): void {
    // Проверка nonce
    $nonce = $_POST['nonce'] ?? '';
    if (!wp_verify_nonce($nonce, 'atk_ved_calculator_nonce')) {
        wp_send_json_error(['message' => 'Ошибка безопасности']);
    }

    // Сбор данных
    $data = [
        'weight' => sanitize_text_field($_POST['weight'] ?? ''),
        'volume' => sanitize_text_field($_POST['volume'] ?? ''),
        'from_city' => sanitize_text_field($_POST['from_city'] ?? ''),
        'to_city' => sanitize_text_field($_POST['to_city'] ?? ''),
        'name' => sanitize_text_field($_POST['name'] ?? ''),
        'phone' => sanitize_text_field($_POST['phone'] ?? ''),
        'email' => sanitize_text_field($_POST['email'] ?? ''),
    ];

    // Валидация
    if (empty($data['name']) || empty($data['phone']) || empty($data['email'])) {
        wp_send_json_error(['message' => 'Заполните все поля']);
    }

    // Сохранение лида
    $post_id = wp_insert_post([
        'post_type' => 'atk_lead',
        'post_title' => 'Лид: ' . $data['name'] . ' - ' . $data['phone'],
        'post_status' => 'publish',
    ]);

    if ($post_id) {
        // Сохраняем мета-данные
        foreach ($data as $key => $value) {
            update_post_meta($post_id, '_' . $key, $value);
        }
        update_post_meta($post_id, '_source', 'lead_calculator');
        update_post_meta($post_id, '_date', current_time('mysql'));

        // Отправка уведомления (опционально)
        // atk_ved_send_lead_notification($data);

        wp_send_json_success(['message' => 'Лид сохранён']);
    }

    wp_send_json_error(['message' => 'Ошибка сохранения']);
}

/**
 * Регистрация типа записи для лидов
 */
function atk_ved_register_lead_post_type(): void {
    register_post_type('atk_lead', [
        'labels' => [
            'name' => __('Лиды', 'atk-ved'),
            'singular_name' => __('Лид', 'atk-ved'),
            'add_new' => __('Добавить', 'atk-ved'),
            'add_new_item' => __('Добавить лид', 'atk-ved'),
            'edit_item' => __('Редактировать лид', 'atk-ved'),
            'all_items' => __('Все лиды', 'atk-ved'),
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-leads',
        'supports' => ['title'],
        'capability_type' => 'post',
    ]);
}
add_action('init', 'atk_ved_register_lead_post_type');

/**
 * Колонки в админке для лидов
 */
add_filter('manage_atk_lead_posts_columns', function($cols) {
    return [
        'cb' => $cols['cb'],
        'title' => __('Имя', 'atk-ved'),
        'phone' => __('Телефон', 'atk-ved'),
        'email' => __('Email', 'atk-ved'),
        'source' => __('Источник', 'atk-ved'),
        'date' => __('Дата', 'atk-ved'),
    ];
});

add_action('manage_atk_lead_posts_custom_column', function($col, $id) {
    switch ($col) {
        case 'phone':
            echo esc_html(get_post_meta($id, '_phone', true));
            break;
        case 'email':
            echo esc_html(get_post_meta($id, '_email', true));
            break;
        case 'source':
            echo esc_html(get_post_meta($id, '_source', true));
            break;
        case 'date':
            echo esc_html(get_post_meta($id, '_date', true));
            break;
    }
}, 10, 2);

/**
 * Шорткод: Видео-презентация
 */
function atk_ved_video_presentation_shortcode(array $atts): string {
    $atts = shortcode_atts([
        'video_id' => '',
        'title' => __('Видео о компании', 'atk-ved'),
        'subtitle' => __('Узнайте больше о нашей работе', 'atk-ved'),
        'thumbnail' => '',
    ], $atts);

    $video_url = '';
    if ($atts['video_id']) {
        $video_url = 'https://www.youtube.com/embed/' . esc_attr($atts['video_id']);
    }

    ob_start();
    ?>
    <section class="video-presentation-section">
        <div class="container">
            <div class="video-section-header">
                <h2 class="section-title"><?php echo esc_html($atts['title']); ?></h2>
                <p class="section-subtitle"><?php echo esc_html($atts['subtitle']); ?></p>
            </div>

            <div class="video-wrapper" data-video="<?php echo esc_url($video_url); ?>">
                <?php if ($atts['thumbnail']): ?>
                <div class="video-thumbnail" style="background-image: url('<?php echo esc_url($atts['thumbnail']); ?>')">
                    <button class="video-play-btn" type="button" aria-label="<?php esc_attr_e('Воспроизвести видео', 'atk-ved'); ?>">
                        <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="40" cy="40" r="40" fill="#e31e24" fill-opacity="0.9"/>
                            <path d="M32 28L60 40L32 52V28Z" fill="white"/>
                        </svg>
                    </button>
                </div>
                <?php endif; ?>
                <iframe 
                    src="<?php echo esc_url($video_url); ?>?enablejsapi=1" 
                    title="<?php esc_attr_e('Видео презентация', 'atk-ved'); ?>"
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen
                    loading="lazy"
                ></iframe>
            </div>
        </div>
    </section>

    <style>
    .video-presentation-section {
        padding: 80px 0;
        background: linear-gradient(135deg, #1a1a1a 0%, #2c2c2c 100%);
    }

    .video-section-header {
        text-align: center;
        margin-bottom: 50px;
    }

    .video-section-header .section-title {
        color: #fff;
        font-size: 42px;
        margin-bottom: 15px;
    }

    .video-section-header .section-subtitle {
        color: rgba(255, 255, 255, 0.7);
        font-size: 18px;
    }

    .video-wrapper {
        position: relative;
        padding-bottom: 56.25%;
        height: 0;
        overflow: hidden;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .video-wrapper iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .video-thumbnail {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .video-thumbnail::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.3);
        transition: background 0.3s ease;
    }

    .video-thumbnail:hover::before {
        background: rgba(0, 0, 0, 0.5);
    }

    .video-play-btn {
        position: relative;
        z-index: 1;
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 0;
        transition: transform 0.3s ease;
    }

    .video-play-btn:hover {
        transform: scale(1.1);
    }

    .video-wrapper iframe + .video-thumbnail {
        display: none;
    }

    .video-wrapper.loaded .video-thumbnail {
        display: none;
    }

    @media (max-width: 768px) {
        .video-presentation-section {
            padding: 40px 0;
        }

        .video-section-header .section-title {
            font-size: 28px;
        }

        .video-section-header .section-subtitle {
            font-size: 16px;
        }
    }
    </style>

    <script>
    (function($) {
        'use strict';

        $('.video-play-btn').on('click', function() {
            const $wrapper = $(this).closest('.video-wrapper');
            const videoUrl = $wrapper.data('video');
            
            if (videoUrl) {
                $wrapper.find('iframe').attr('src', videoUrl + '&autoplay=1');
                $wrapper.addClass('loaded');
            }
        });
    })(jQuery);
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('video_presentation', 'atk_ved_video_presentation_shortcode');

/**
 * Шорткод: Партнёры и сертификаты
 */
function atk_ved_partners_certificates_shortcode(array $atts): string {
    $atts = shortcode_atts([
        'title' => __('Наши партнёры и сертификаты', 'atk-ved'),
        'subtitle' => __('Доверие клиентов и официальная сертификация', 'atk-ved'),
        'limit' => '10',
    ], $atts);

    // Получаем логотипы партнёров
    $partners = [];
    $partners_query = new WP_Query([
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'post_status' => 'inherit',
        'posts_per_page' => (int)$atts['limit'],
        'meta_key' => '_partner_type',
        'meta_value' => 'logo',
    ]);

    if ($partners_query->have_posts()) {
        while ($partners_query->have_posts()) {
            $partners_query->the_post();
            $partners[] = [
                'id' => get_the_ID(),
                'url' => wp_get_attachment_url(get_the_ID()),
                'title' => get_the_title(),
                'link' => get_post_meta(get_the_ID(), '_partner_link', true),
            ];
        }
        wp_reset_postdata();
    }

    ob_start();
    ?>
    <section class="partners-section">
        <div class="container">
            <div class="partners-header">
                <h2 class="section-title"><?php echo esc_html($atts['title']); ?></h2>
                <p class="section-subtitle"><?php echo esc_html($atts['subtitle']); ?></p>
            </div>

            <?php if (!empty($partners)): ?>
            <div class="partners-grid">
                <?php foreach ($partners as $partner): ?>
                <div class="partner-item">
                    <?php if ($partner['link']): ?>
                    <a href="<?php echo esc_url($partner['link']); ?>" target="_blank" rel="noopener">
                    <?php endif; ?>
                    <img src="<?php echo esc_url($partner['url']); ?>" alt="<?php echo esc_attr($partner['title']); ?>" loading="lazy">
                    <?php if ($partner['link']): ?>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="certificates-section">
                <h3 class="certificates-title"><?php _e('Сертификаты', 'atk-ved'); ?></h3>
                
                <div class="certificates-grid">
                    <div class="certificate-item">
                        <div class="certificate-thumb">
                            <span class="cert-icon">📜</span>
                        </div>
                        <p><?php _e('Лицензия на перевозку', 'atk-ved'); ?></p>
                    </div>
                    <div class="certificate-item">
                        <div class="certificate-thumb">
                            <span class="cert-icon">✓</span>
                        </div>
                        <p><?php _e('Сертификат ISO 9001', 'atk-ved'); ?></p>
                    </div>
                    <div class="certificate-item">
                        <div class="certificate-thumb">
                            <span class="cert-icon">🛡️</span>
                        </div>
                        <p><?php _e('Страхование грузов', 'atk-ved'); ?></p>
                    </div>
                    <div class="certificate-item">
                        <div class="certificate-thumb">
                            <span class="cert-icon">🏆</span>
                        </div>
                        <p><?php _e('Лучший логист 2024', 'atk-ved'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
    .partners-section {
        padding: 80px 0;
        background: #fff;
    }

    .partners-header {
        text-align: center;
        margin-bottom: 50px;
    }

    .partners-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 30px;
        align-items: center;
        margin-bottom: 60px;
    }

    .partner-item {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        height: 100px;
    }

    .partner-item:hover {
        background: #f0f0f0;
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }

    .partner-item img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        filter: grayscale(100%);
        opacity: 0.7;
        transition: all 0.3s ease;
    }

    .partner-item:hover img {
        filter: grayscale(0%);
        opacity: 1;
    }

    .certificates-section {
        text-align: center;
    }

    .certificates-title {
        font-size: 28px;
        font-weight: 700;
        color: #2c2c2c;
        margin-bottom: 40px;
    }

    .certificates-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 30px;
    }

    .certificate-item {
        text-align: center;
        padding: 30px 20px;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 16px;
        border: 1px solid #e0e0e0;
        transition: all 0.3s ease;
    }

    .certificate-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        border-color: #e31e24;
    }

    .certificate-thumb {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        background: linear-gradient(135deg, #e31e24, #ff6b6b);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
    }

    .certificate-item p {
        font-size: 14px;
        color: #666;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .partners-section {
            padding: 40px 0;
        }

        .partners-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .partner-item {
            height: 80px;
            padding: 15px;
        }

        .certificates-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode('partners_certificates', 'atk_ved_partners_certificates_shortcode');

/**
 * Шорткод: Команда / О компании
 */
function atk_ved_team_section_shortcode(array $atts): string {
    $atts = shortcode_atts([
        'title' => __('Наша команда', 'atk-ved'),
        'subtitle' => __('Профессионалы с многолетним опытом', 'atk-ved'),
        'limit' => '4',
    ], $atts);

    ob_start();
    ?>
    <section class="team-section">
        <div class="container">
            <div class="team-header">
                <h2 class="section-title"><?php echo esc_html($atts['title']); ?></h2>
                <p class="section-subtitle"><?php echo esc_html($atts['subtitle']); ?></p>
            </div>

            <div class="team-grid">
                <div class="team-member">
                    <div class="member-photo">
                        <div class="photo-placeholder">👨‍💼</div>
                    </div>
                    <div class="member-info">
                        <h4>Александр Петров</h4>
                        <p class="member-position">Генеральный директор</p>
                        <p class="member-desc">15 лет опыта в логистике</p>
                        <div class="member-social">
                            <a href="#" class="social-link">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                </svg>
                            </a>
                            <a href="mailto:alexander@atk-ved.ru" class="social-link">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="team-member">
                    <div class="member-photo">
                        <div class="photo-placeholder">👩‍💼</div>
                    </div>
                    <div class="member-info">
                        <h4>Елена Смирнова</h4>
                        <p class="member-position">Коммерческий директор</p>
                        <p class="member-desc">12 лет в ВЭД</p>
                        <div class="member-social">
                            <a href="#" class="social-link">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                </svg>
                            </a>
                            <a href="mailto:elena@atk-ved.ru" class="social-link">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="team-member">
                    <div class="member-photo">
                        <div class="photo-placeholder">👨‍💻</div>
                    </div>
                    <div class="member-info">
                        <h4>Дмитрий Волков</h4>
                        <p class="member-position">Руководитель отдела логистики</p>
                        <p class="member-desc">10 лет опыта</p>
                        <div class="member-social">
                            <a href="#" class="social-link">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                </svg>
                            </a>
                            <a href="mailto:dmitry@atk-ved.ru" class="social-link">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="team-member">
                    <div class="member-photo">
                        <div class="photo-placeholder">👩‍🔧</div>
                    </div>
                    <div class="member-info">
                        <h4>Анна Козлова</h4>
                        <p class="member-position">Менеджер по работе с клиентами</p>
                        <p class="member-desc">8 лет в сфере услуг</p>
                        <div class="member-social">
                            <a href="#" class="social-link">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                </svg>
                            </a>
                            <a href="mailto:anna@atk-ved.ru" class="social-link">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
    .team-section {
        padding: 80px 0;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    }

    .team-header {
        text-align: center;
        margin-bottom: 50px;
    }

    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
    }

    .team-member {
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .team-member:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .member-photo {
        height: 280px;
        background: linear-gradient(135deg, #e31e24, #ff6b6b);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .photo-placeholder {
        font-size: 100px;
        opacity: 0.8;
    }

    .member-info {
        padding: 25px;
        text-align: center;
    }

    .member-info h4 {
        font-size: 20px;
        font-weight: 700;
        color: #2c2c2c;
        margin-bottom: 8px;
    }

    .member-position {
        font-size: 14px;
        color: #e31e24;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .member-desc {
        font-size: 13px;
        color: #666;
        margin-bottom: 15px;
    }

    .member-social {
        display: flex;
        justify-content: center;
        gap: 15px;
    }

    .social-link {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f0f0f0;
        border-radius: 50%;
        color: #666;
        transition: all 0.3s ease;
    }

    .social-link:hover {
        background: #e31e24;
        color: #fff;
        transform: translateY(-3px);
    }

    @media (max-width: 768px) {
        .team-section {
            padding: 40px 0;
        }

        .team-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .member-photo {
            height: 200px;
        }

        .photo-placeholder {
            font-size: 60px;
        }

        .member-info h4 {
            font-size: 16px;
        }
    }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode('team_section', 'atk_ved_team_section_shortcode');
