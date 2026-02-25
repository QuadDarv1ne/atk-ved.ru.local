<?php
/**
 * Расширенный калькулятор доставки с PDF экспортом
 * 
 * @package ATK_VED
 * @since 2.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Категории товаров с таможенными ставками
 */
function atk_ved_get_product_categories(): array {
    return [
        'electronics' => [
            'name' => 'Электроника',
            'duty_rate' => 0.15, // 15%
            'vat_rate' => 0.20,  // 20%
            'examples' => 'Смартфоны, планшеты, ноутбуки'
        ],
        'clothing' => [
            'name' => 'Одежда и обувь',
            'duty_rate' => 0.10,
            'vat_rate' => 0.20,
            'examples' => 'Футболки, джинсы, кроссовки'
        ],
        'toys' => [
            'name' => 'Игрушки',
            'duty_rate' => 0.12,
            'vat_rate' => 0.20,
            'examples' => 'Детские игрушки, конструкторы'
        ],
        'household' => [
            'name' => 'Товары для дома',
            'duty_rate' => 0.08,
            'vat_rate' => 0.20,
            'examples' => 'Посуда, текстиль, декор'
        ],
        'cosmetics' => [
            'name' => 'Косметика',
            'duty_rate' => 0.06,
            'vat_rate' => 0.20,
            'examples' => 'Уход за кожей, макияж'
        ],
        'auto_parts' => [
            'name' => 'Автозапчасти',
            'duty_rate' => 0.18,
            'vat_rate' => 0.20,
            'examples' => 'Фильтры, тормозные колодки'
        ],
        'sports' => [
            'name' => 'Спорттовары',
            'duty_rate' => 0.10,
            'vat_rate' => 0.20,
            'examples' => 'Спортивная одежда, инвентарь'
        ],
        'other' => [
            'name' => 'Другое',
            'duty_rate' => 0.10,
            'vat_rate' => 0.20,
            'examples' => 'Прочие товары'
        ]
    ];
}

/**
 * Маршруты доставки с детальной информацией
 */
function atk_ved_get_delivery_routes(): array {
    return [
        'air' => [
            'name' => 'Авиадоставка',
            'icon' => '✈️',
            'routes' => [
                'beijing_moscow' => [
                    'from' => 'Пекин',
                    'to' => 'Москва',
                    'days_min' => 5,
                    'days_max' => 8,
                    'rate_per_kg' => 5.5
                ],
                'shanghai_moscow' => [
                    'from' => 'Шанхай',
                    'to' => 'Москва',
                    'days_min' => 6,
                    'days_max' => 10,
                    'rate_per_kg' => 5.0
                ],
                'guangzhou_moscow' => [
                    'from' => 'Гуанчжоу',
                    'to' => 'Москва',
                    'days_min' => 5,
                    'days_max' => 9,
                    'rate_per_kg' => 5.2
                ]
            ]
        ],
        'sea' => [
            'name' => 'Морская доставка',
            'icon' => '🚢',
            'routes' => [
                'shanghai_vladivostok' => [
                    'from' => 'Шанхай',
                    'to' => 'Владивосток',
                    'days_min' => 10,
                    'days_max' => 15,
                    'rate_per_kg' => 0.8
                ],
                'ningbo_spb' => [
                    'from' => 'Нинбо',
                    'to' => 'Санкт-Петербург',
                    'days_min' => 35,
                    'days_max' => 45,
                    'rate_per_kg' => 1.2
                ]
            ]
        ],
        'rail' => [
            'name' => 'Ж/Д доставка',
            'icon' => '🚂',
            'routes' => [
                'yiwu_moscow' => [
                    'from' => 'Иу',
                    'to' => 'Москва',
                    'days_min' => 18,
                    'days_max' => 25,
                    'rate_per_kg' => 2.8
                ],
                'chengdu_moscow' => [
                    'from' => 'Чэнду',
                    'to' => 'Москва',
                    'days_min' => 20,
                    'days_max' => 28,
                    'rate_per_kg' => 3.0
                ]
            ]
        ],
        'auto' => [
            'name' => 'Автодоставка',
            'icon' => '🚛',
            'routes' => [
                'urumqi_moscow' => [
                    'from' => 'Урумчи',
                    'to' => 'Москва',
                    'days_min' => 12,
                    'days_max' => 18,
                    'rate_per_kg' => 3.8
                ]
            ]
        ]
    ];
}

/**
 * Расширенный расчет стоимости
 */
function atk_ved_calculate_advanced(array $data): array {
    $weight = floatval($data['weight'] ?? 0);
    $volume = floatval($data['volume'] ?? 0);
    $product_value = floatval($data['product_value'] ?? 0);
    $category = sanitize_text_field($data['category'] ?? 'other');
    $from_city = sanitize_text_field($data['from_city'] ?? 'Пекин');
    $to_city = sanitize_text_field($data['to_city'] ?? 'Москва');
    $method = sanitize_text_field($data['method'] ?? 'all');
    $insurance = (bool)($data['insurance'] ?? false);
    
    $categories = atk_ved_get_product_categories();
    $routes = atk_ved_get_delivery_routes();
    $category_data = $categories[$category] ?? $categories['other'];
    
    // Объемный вес
    $volumetric_weight = $volume * 167;
    $chargeable_weight = max($weight, $volumetric_weight);
    
    $results = [];
    $exchange_rate = 90; // USD to RUB
    
    foreach ($routes as $method_key => $method_data) {
        if ($method !== 'all' && $method !== $method_key) {
            continue;
        }
        
        foreach ($method_data['routes'] as $route_key => $route) {
            $delivery_cost_usd = $chargeable_weight * $route['rate_per_kg'];
            $delivery_cost_rub = $delivery_cost_usd * $exchange_rate;
            
            // Таможенные платежи
            $customs_duty = $product_value * $category_data['duty_rate'];
            $vat_base = $product_value + $customs_duty + $delivery_cost_rub;
            $vat = $vat_base * $category_data['vat_rate'];
            
            // Страхование
            $insurance_cost = $insurance ? ($product_value * 0.03) : 0;
            
            // Услуги компании
            $service_fee = 5000; // фиксированная комиссия
            
            $total_rub = $delivery_cost_rub + $customs_duty + $vat + $insurance_cost + $service_fee;
            
            $results[] = [
                'method' => $method_key,
                'method_name' => $method_data['name'],
                'icon' => $method_data['icon'],
                'route_key' => $route_key,
                'from' => $route['from'],
                'to' => $route['to'],
                'days_min' => $route['days_min'],
                'days_max' => $route['days_max'],
                'delivery_cost_usd' => round($delivery_cost_usd, 2),
                'delivery_cost_rub' => round($delivery_cost_rub, 2),
                'customs_duty' => round($customs_duty, 2),
                'vat' => round($vat, 2),
                'insurance_cost' => round($insurance_cost, 2),
                'service_fee' => $service_fee,
                'total_rub' => round($total_rub, 2),
                'total_usd' => round($total_rub / $exchange_rate, 2),
                'chargeable_weight' => round($chargeable_weight, 2),
                'rate_per_kg' => $route['rate_per_kg']
            ];
        }
    }
    
    // Сортировка по стоимости
    usort($results, function($a, $b) {
        return $a['total_rub'] <=> $b['total_rub'];
    });
    
    return [
        'calculations' => $results,
        'input_data' => [
            'weight' => $weight,
            'volume' => $volume,
            'product_value' => $product_value,
            'category' => $category_data['name'],
            'volumetric_weight' => round($volumetric_weight, 2),
            'chargeable_weight' => round($chargeable_weight, 2)
        ],
        'exchange_rate' => $exchange_rate,
        'timestamp' => current_time('mysql')
    ];
}

/**
 * AJAX обработчик расширенного калькулятора
 */
function atk_ved_ajax_calculate_advanced(): void {
    check_ajax_referer('atk_ved_calculator_nonce', 'nonce');
    
    $data = [
        'weight' => floatval($_POST['weight'] ?? 0),
        'volume' => floatval($_POST['volume'] ?? 0),
        'product_value' => floatval($_POST['product_value'] ?? 0),
        'category' => sanitize_text_field($_POST['category'] ?? 'other'),
        'from_city' => sanitize_text_field($_POST['from_city'] ?? 'Пекин'),
        'to_city' => sanitize_text_field($_POST['to_city'] ?? 'Москва'),
        'method' => sanitize_text_field($_POST['method'] ?? 'all'),
        'insurance' => isset($_POST['insurance'])
    ];
    
    if ($data['weight'] <= 0 || $data['product_value'] <= 0) {
        wp_send_json_error(['message' => 'Заполните все обязательные поля']);
    }
    
    $result = atk_ved_calculate_advanced($data);
    wp_send_json_success($result);
}
add_action('wp_ajax_atk_ved_calculate_advanced', 'atk_ved_ajax_calculate_advanced');
add_action('wp_ajax_nopriv_atk_ved_calculate_advanced', 'atk_ved_ajax_calculate_advanced');

/**
 * Генерация PDF с расчетом
 */
function atk_ved_generate_pdf_calculation(): void {
    check_ajax_referer('atk_ved_calculator_nonce', 'nonce');
    
    $calculation_data = json_decode(stripslashes($_POST['calculation_data'] ?? '{}'), true);
    
    if (empty($calculation_data)) {
        wp_send_json_error(['message' => 'Нет данных для экспорта']);
    }
    
    // Генерация HTML для PDF
    $html = atk_ved_generate_pdf_html($calculation_data);
    
    // Сохранение во временный файл
    $upload_dir = wp_upload_dir();
    $pdf_dir = $upload_dir['basedir'] . '/calculations';
    
    if (!file_exists($pdf_dir)) {
        wp_mkdir_p($pdf_dir);
    }
    
    $filename = 'calculation_' . time() . '.html';
    $filepath = $pdf_dir . '/' . $filename;
    
    file_put_contents($filepath, $html);
    
    $pdf_url = $upload_dir['baseurl'] . '/calculations/' . $filename;
    
    wp_send_json_success([
        'pdf_url' => $pdf_url,
        'filename' => $filename,
        'message' => 'PDF готов к скачиванию'
    ]);
}
add_action('wp_ajax_atk_ved_generate_pdf', 'atk_ved_generate_pdf_calculation');
add_action('wp_ajax_nopriv_atk_ved_generate_pdf', 'atk_ved_generate_pdf_calculation');

/**
 * Генерация HTML для PDF
 */
function atk_ved_generate_pdf_html(array $data): string {
    $input = $data['input_data'] ?? [];
    $calculations = $data['calculations'] ?? [];
    $timestamp = $data['timestamp'] ?? current_time('mysql');
    
    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <title>Расчет стоимости доставки - АТК ВЭД</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: Arial, sans-serif; padding: 40px; color: #333; }
            .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #e31e24; padding-bottom: 20px; }
            .logo { font-size: 28px; font-weight: bold; color: #e31e24; }
            .subtitle { color: #666; margin-top: 5px; }
            .info-block { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
            .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e0e0e0; }
            .info-label { font-weight: 600; }
            .calculation-card { border: 2px solid #e0e0e0; border-radius: 8px; padding: 20px; margin-bottom: 20px; page-break-inside: avoid; }
            .calculation-header { background: #e31e24; color: white; padding: 15px; margin: -20px -20px 15px; border-radius: 6px 6px 0 0; }
            .method-name { font-size: 20px; font-weight: bold; }
            .route-info { font-size: 14px; margin-top: 5px; }
            .breakdown-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
            .breakdown-table td { padding: 10px; border-bottom: 1px solid #e0e0e0; }
            .breakdown-table .label { font-weight: 500; }
            .breakdown-table .value { text-align: right; font-weight: 600; }
            .total-row { background: #f8f9fa; font-size: 18px; }
            .total-row td { border-top: 2px solid #e31e24; padding-top: 15px; }
            .footer { margin-top: 40px; text-align: center; color: #999; font-size: 12px; border-top: 1px solid #e0e0e0; padding-top: 20px; }
            .note { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin-top: 20px; font-size: 14px; }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="logo">АТК ВЭД</div>
            <div class="subtitle">Расчет стоимости доставки из Китая</div>
            <div style="margin-top: 10px; font-size: 12px; color: #999;">
                Дата расчета: <?php echo esc_html(date('d.m.Y H:i', strtotime($timestamp))); ?>
            </div>
        </div>
        
        <div class="info-block">
            <h3 style="margin-bottom: 15px;">Исходные данные</h3>
            <div class="info-row">
                <span class="info-label">Вес груза:</span>
                <span><?php echo esc_html($input['weight']); ?> кг</span>
            </div>
            <div class="info-row">
                <span class="info-label">Объем:</span>
                <span><?php echo esc_html($input['volume']); ?> м³</span>
            </div>
            <div class="info-row">
                <span class="info-label">Объемный вес:</span>
                <span><?php echo esc_html($input['volumetric_weight']); ?> кг</span>
            </div>
            <div class="info-row">
                <span class="info-label">Расчетный вес:</span>
                <span><?php echo esc_html($input['chargeable_weight']); ?> кг</span>
            </div>
            <div class="info-row">
                <span class="info-label">Стоимость товара:</span>
                <span><?php echo number_format($input['product_value'], 0, ',', ' '); ?> ₽</span>
            </div>
            <div class="info-row">
                <span class="info-label">Категория товара:</span>
                <span><?php echo esc_html($input['category']); ?></span>
            </div>
        </div>
        
        <?php foreach ($calculations as $index => $calc): ?>
        <div class="calculation-card">
            <div class="calculation-header">
                <div class="method-name">
                    <?php echo esc_html($calc['icon'] . ' ' . $calc['method_name']); ?>
                </div>
                <div class="route-info">
                    <?php echo esc_html($calc['from'] . ' → ' . $calc['to']); ?> 
                    (<?php echo esc_html($calc['days_min'] . '-' . $calc['days_max']); ?> дней)
                </div>
            </div>
            
            <table class="breakdown-table">
                <tr>
                    <td class="label">Доставка (<?php echo esc_html($calc['chargeable_weight']); ?> кг × $<?php echo esc_html($calc['rate_per_kg']); ?>)</td>
                    <td class="value"><?php echo number_format($calc['delivery_cost_rub'], 0, ',', ' '); ?> ₽</td>
                </tr>
                <tr>
                    <td class="label">Таможенная пошлина</td>
                    <td class="value"><?php echo number_format($calc['customs_duty'], 0, ',', ' '); ?> ₽</td>
                </tr>
                <tr>
                    <td class="label">НДС (20%)</td>
                    <td class="value"><?php echo number_format($calc['vat'], 0, ',', ' '); ?> ₽</td>
                </tr>
                <?php if ($calc['insurance_cost'] > 0): ?>
                <tr>
                    <td class="label">Страхование (3%)</td>
                    <td class="value"><?php echo number_format($calc['insurance_cost'], 0, ',', ' '); ?> ₽</td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td class="label">Услуги компании</td>
                    <td class="value"><?php echo number_format($calc['service_fee'], 0, ',', ' '); ?> ₽</td>
                </tr>
                <tr class="total-row">
                    <td class="label">ИТОГО:</td>
                    <td class="value" style="color: #e31e24; font-size: 20px;">
                        <?php echo number_format($calc['total_rub'], 0, ',', ' '); ?> ₽
                    </td>
                </tr>
            </table>
        </div>
        <?php endforeach; ?>
        
        <div class="note">
            <strong>Важно:</strong> Данный расчет является предварительным и может отличаться от финальной стоимости. 
            Точную стоимость уточняйте у менеджера. Курс валют и тарифы могут меняться.
        </div>
        
        <div class="footer">
            <p><strong>АТК ВЭД</strong> - Товары для маркетплейсов из Китая оптом</p>
            <p>Телефон: +7 (XXX) XXX-XX-XX | Email: info@atk-ved.ru</p>
            <p>www.atk-ved.ru</p>
        </div>
    </body>
    </html>
    <?php
    return ob_get_clean();
}

/**
 * Шорткод расширенного калькулятора
 */
function atk_ved_advanced_calculator_shortcode(array $atts): string {
    $atts = shortcode_atts([
        'title' => 'Расчет стоимости доставки',
    ], $atts);
    
    $categories = atk_ved_get_product_categories();
    $nonce = wp_create_nonce('atk_ved_calculator_nonce');
    
    ob_start();
    include get_template_directory() . '/template-parts/calculator-advanced.php';
    return ob_get_clean();
}
add_shortcode('advanced_calculator', 'atk_ved_advanced_calculator_shortcode');
