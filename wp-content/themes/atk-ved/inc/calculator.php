<?php
/**
 * Калькулятор доставки
 * 
 * @package ATK_VED
 * @since 2.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Регистрация AJAX обработчиков
 */
function atk_ved_calculator_ajax_handlers(): void {
    // Для авторизованных и неавторизованных
    add_action('wp_ajax_atk_ved_calculate_delivery', 'atk_ved_handle_calculate_delivery');
    add_action('wp_ajax_nopriv_atk_ved_calculate_delivery', 'atk_ved_handle_calculate_delivery');
}
add_action('wp_ajax_atk_ved_calculate_delivery', 'atk_ved_calculator_ajax_handlers');
add_action('wp_ajax_nopriv_atk_ved_calculate_delivery', 'atk_ved_calculator_ajax_handlers');

/**
 * Обработка запроса расчёта доставки
 */
function atk_ved_handle_calculate_delivery(): void {
    // Проверка nonce
    $nonce = $_POST['nonce'] ?? '';
    if (!wp_verify_nonce($nonce, 'atk_ved_calculator_nonce')) {
        wp_send_json_error(['message' => 'Ошибка безопасности']);
    }

    // Получение данных
    $weight = floatval($_POST['weight'] ?? 0);
    $volume = floatval($_POST['volume'] ?? 0);
    $from_city = sanitize_text_field($_POST['from_city'] ?? 'Пекин');
    $to_city = sanitize_text_field($_POST['to_city'] ?? 'Москва');
    $method = sanitize_text_field($_POST['method'] ?? 'all');
    $insurance = isset($_POST['insurance']) && $_POST['insurance'] === '1';
    $customs = isset($_POST['customs']) && $_POST['customs'] === '1';

    // Валидация
    if ($weight <= 0) {
        wp_send_json_error(['message' => 'Укажите вес груза']);
    }

    // Расчёт
    $result = atk_ved_calculate_delivery_cost([
        'weight' => $weight,
        'volume' => $volume,
        'from_city' => $from_city,
        'to_city' => $to_city,
        'method' => $method,
        'insurance' => $insurance,
        'customs' => $customs
    ]);

    wp_send_json_success($result);
}

/**
 * Расчёт стоимости доставки
 * 
 * @param array $data Данные для расчёта
 * @return array Результат расчёта
 */
function atk_ved_calculate_delivery_cost(array $data): array {
    $weight = $data['weight'];
    $volume = $data['volume'];
    $method = $data['method'];
    $insurance = $data['insurance'];
    $customs = $data['customs'];

    // Объёмный вес (для авиа и авто)
    $volumetric_weight = $volume * 167; // 1 м³ = 167 кг для авиа

    // Базовые тарифы ($/кг)
    $rates = [
        'air' => [
            'base' => 4.5,
            'min' => 100,
            'days_min' => 5,
            'days_max' => 10
        ],
        'sea' => [
            'base' => 1.2,
            'min' => 500,
            'days_min' => 35,
            'days_max' => 45
        ],
        'rail' => [
            'base' => 2.5,
            'min' => 300,
            'days_min' => 20,
            'days_max' => 30
        ],
        'auto' => [
            'base' => 3.5,
            'min' => 200,
            'days_min' => 15,
            'days_max' => 25
        ]
    ];

    $results = [];

    foreach ($rates as $key => $rate) {
        if ($method !== 'all' && $method !== $key) {
            continue;
        }

        // Расчёт веса для оплаты (больший из фактического и объёмного)
        $chargeable_weight = max($weight, $volumetric_weight);

        // Базовая стоимость
        $cost = $chargeable_weight * $rate['base'];

        // Минимальная стоимость
        $cost = max($cost, $rate['min']);

        // Страховка (3% от объявленной ценности)
        $insurance_cost = 0;
        if ($insurance) {
            $insurance_cost = $cost * 0.03;
            $cost += $insurance_cost;
        }

        // Таможенное оформление
        $customs_cost = 0;
        if ($customs) {
            $customs_cost = 150 + ($weight * 0.5);
            $cost += $customs_cost;
        }

        $results[$key] = [
            'method' => $key,
            'method_name' => atk_ved_get_delivery_method_name($key),
            'cost' => round($cost, 2),
            'cost_usd' => round($cost, 2),
            'cost_rub' => round($cost * 90, 2), // Примерный курс
            'days_min' => $rate['days_min'],
            'days_max' => $rate['days_max'],
            'chargeable_weight' => round($chargeable_weight, 2),
            'insurance_cost' => round($insurance_cost, 2),
            'customs_cost' => round($customs_cost, 2),
            'breakdown' => [
                'base_cost' => round($chargeable_weight * $rate['base'], 2),
                'insurance' => round($insurance_cost, 2),
                'customs' => round($customs_cost, 2)
            ]
        ];
    }

    // Рекомендация
    $recommended = 'rail';
    if ($weight < 100) {
        $recommended = 'air';
    } elseif ($weight > 1000) {
        $recommended = 'sea';
    }

    return [
        'calculations' => $results,
        'recommended' => $recommended,
        'weight' => $weight,
        'volume' => $volume,
        'volumetric_weight' => round($volumetric_weight, 2),
        'currency' => 'USD',
        'exchange_rate' => 90
    ];
}

/**
 * Название метода доставки
 */
function atk_ved_get_delivery_method_name(string $method): string {
    $names = [
        'air' => 'Авиа',
        'sea' => 'Море',
        'rail' => 'Ж/Д',
        'auto' => 'Авто'
    ];
    return $names[$method] ?? $method;
}

/**
 * Шорткод калькулятора
 */
function atk_ved_calculator_shortcode(array $atts): string {
    $atts = shortcode_atts([
        'title' => __('Калькулятор доставки', 'atk-ved'),
        'show_insurance' => '1',
        'show_customs' => '1',
    ], $atts);

    ob_start();
    ?>
    <div class="delivery-calculator" data-nonce="<?php echo esc_attr(wp_create_nonce('atk_ved_calculator_nonce')); ?>">
        <h3 class="calculator-title"><?php echo esc_html($atts['title']); ?></h3>
        
        <form id="deliveryCalculatorForm" class="calculator-form">
            <div class="calculator-row">
                <div class="calculator-field">
                    <label for="calc_weight"><?php echo esc_html__('Вес груза (кг)*', 'atk-ved'); ?></label>
                    <input type="number" id="calc_weight" name="weight" min="1" step="0.1" required 
                           placeholder="Например: 500">
                </div>
                
                <div class="calculator-field">
                    <label for="calc_volume"><?php echo esc_html__('Объём (м³)', 'atk-ved'); ?></label>
                    <input type="number" id="calc_volume" name="volume" min="0" step="0.01" 
                           placeholder="Например: 2.5">
                </div>
            </div>
            
            <div class="calculator-row">
                <div class="calculator-field">
                    <label for="calc_from"><?php echo esc_html__('Откуда', 'atk-ved'); ?></label>
                    <select id="calc_from" name="from_city">
                        <option value="Пекин">Пекин</option>
                        <option value="Шанхай">Шанхай</option>
                        <option value="Гуанчжоу">Гуанчжоу</option>
                        <option value="Шэньчжэнь">Шэньчжэнь</option>
                        <option value="Иу">Иу</option>
                        <option value="Дунгуань">Дунгуань</option>
                    </select>
                </div>
                
                <div class="calculator-field">
                    <label for="calc_to"><?php echo esc_html__('Куда', 'atk-ved'); ?></label>
                    <select id="calc_to" name="to_city">
                        <option value="Москва">Москва</option>
                        <option value="Санкт-Петербург">Санкт-Петербург</option>
                        <option value="Екатеринбург">Екатеринбург</option>
                        <option value="Казань">Казань</option>
                        <option value="Новосибирск">Новосибирск</option>
                        <option value="Владивосток">Владивосток</option>
                    </select>
                </div>
            </div>
            
            <div class="calculator-row">
                <div class="calculator-field calculator-field--full">
                    <label><?php echo esc_html__('Способ доставки', 'atk-ved'); ?></label>
                    <div class="calculator-radio-group">
                        <label class="calculator-radio">
                            <input type="radio" name="method" value="all" checked>
                            <span><?php echo esc_html__('Все', 'atk-ved'); ?></span>
                        </label>
                        <label class="calculator-radio">
                            <input type="radio" name="method" value="air">
                            <span><?php echo esc_html__('Авиа', 'atk-ved'); ?></span>
                        </label>
                        <label class="calculator-radio">
                            <input type="radio" name="method" value="sea">
                            <span><?php echo esc_html__('Море', 'atk-ved'); ?></span>
                        </label>
                        <label class="calculator-radio">
                            <input type="radio" name="method" value="rail">
                            <span><?php echo esc_html__('Ж/Д', 'atk-ved'); ?></span>
                        </label>
                        <label class="calculator-radio">
                            <input type="radio" name="method" value="auto">
                            <span><?php echo esc_html__('Авто', 'atk-ved'); ?></span>
                        </label>
                    </div>
                </div>
            </div>
            
            <?php if ($atts['show_insurance'] === '1'): ?>
            <div class="calculator-row">
                <label class="calculator-checkbox">
                    <input type="checkbox" name="insurance" value="1">
                    <span><?php echo esc_html__('Страхование груза (3%)', 'atk-ved'); ?></span>
                </label>
            </div>
            <?php endif; ?>
            
            <?php if ($atts['show_customs'] === '1'): ?>
            <div class="calculator-row">
                <label class="calculator-checkbox">
                    <input type="checkbox" name="customs" value="1" checked>
                    <span><?php echo esc_html__('Таможенное оформление', 'atk-ved'); ?></span>
                </label>
            </div>
            <?php endif; ?>
            
            <div class="calculator-row">
                <button type="submit" class="calculator-submit">
                    <span><?php echo esc_html__('Рассчитать стоимость', 'atk-ved'); ?></span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </form>
        
        <div class="calculator-results" id="calculatorResults" style="display: none;">
            <div class="calculator-loader">
                <div class="spinner"></div>
                <p><?php echo esc_html__('Рассчитываем...', 'atk-ved'); ?></p>
            </div>
            
            <div class="calculator-results-content"></div>
        </div>
        
        <div class="calculator-error" id="calculatorError" style="display: none;"></div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('delivery_calculator', 'atk_ved_calculator_shortcode');

/**
 * Настройки калькулятора в Customizer
 */
function atk_ved_calculator_customizer($wp_customize): void {
    $wp_customize->add_section('atk_ved_calculator', array(
        'title'    => __('Калькулятор доставки', 'atk-ved'),
        'priority' => 41,
    ));

    // Базовые тарифы
    $wp_customize->add_setting('atk_ved_calculator_air_rate', array(
        'default'           => 4.5,
        'sanitize_callback' => 'floatval',
    ));

    $wp_customize->add_control('atk_ved_calculator_air_rate', array(
        'label'   => __('Тариф Авиа ($/кг)', 'atk-ved'),
        'section' => 'atk_ved_calculator',
        'type'    => 'number',
    ));

    $wp_customize->add_setting('atk_ved_calculator_sea_rate', array(
        'default'           => 1.2,
        'sanitize_callback' => 'floatval',
    ));

    $wp_customize->add_control('atk_ved_calculator_sea_rate', array(
        'label'   => __('Тариф Море ($/кг)', 'atk-ved'),
        'section' => 'atk_ved_calculator',
        'type'    => 'number',
    ));

    $wp_customize->add_setting('atk_ved_calculator_rail_rate', array(
        'default'           => 2.5,
        'sanitize_callback' => 'floatval',
    ));

    $wp_customize->add_control('atk_ved_calculator_rail_rate', array(
        'label'   => __('Тариф Ж/Д ($/кг)', 'atk-ved'),
        'section' => 'atk_ved_calculator',
        'type'    => 'number',
    ));

    $wp_customize->add_setting('atk_ved_calculator_auto_rate', array(
        'default'           => 3.5,
        'sanitize_callback' => 'floatval',
    ));

    $wp_customize->add_control('atk_ved_calculator_auto_rate', array(
        'label'   => __('Тариф Авто ($/кг)', 'atk-ved'),
        'section' => 'atk_ved_calculator',
        'type'    => 'number',
    ));
}
add_action('customize_register', 'atk_ved_calculator_customizer');
