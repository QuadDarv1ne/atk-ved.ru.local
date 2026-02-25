<?php
/**
 * Система отслеживания грузов
 *
 * @package ATK_VED
 * @since 2.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Регистрация AJAX обработчиков для отслеживания
 */
add_action('wp_ajax_atk_ved_track_shipment', 'atk_ved_handle_track_shipment');
add_action('wp_ajax_nopriv_atk_ved_track_shipment', 'atk_ved_handle_track_shipment');

/**
 * Обработка запроса отслеживания
 */
function atk_ved_handle_track_shipment(): void {
    $nonce = $_POST['nonce'] ?? '';
    if (!wp_verify_nonce($nonce, 'atk_ved_tracking_nonce')) {
        wp_send_json_error(['message' => 'Ошибка безопасности']);
    }

    $tracking_number = sanitize_text_field($_POST['tracking_number'] ?? '');
    
    if (empty($tracking_number)) {
        wp_send_json_error(['message' => 'Введите номер отслеживания']);
    }

    $result = atk_ved_get_tracking_info($tracking_number);
    
    if ($result) {
        wp_send_json_success($result);
    } else {
        wp_send_json_error(['message' => 'Груз с таким номером не найден']);
    }
}

/**
 * Получение информации об отслеживании
 * 
 * @param string $tracking_number Номер отслеживания
 * @return array|null
 */
function atk_ved_get_tracking_info(string $tracking_number): ?array {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'atk_ved_shipments';
    
    // Проверка существования таблицы
    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") !== $table_name) {
        // Таблица не существует - возвращаем демо данные
        return atk_ved_get_demo_tracking($tracking_number);
    }
    
    $shipment = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$table_name} WHERE tracking_number = %s",
        $tracking_number
    ), ARRAY_A);
    
    if (!$shipment) {
        return null;
    }
    
    // Получаем историю перемещений
    $history_table = $wpdb->prefix . 'atk_ved_shipment_history';
    $history = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$history_table} WHERE shipment_id = %d ORDER BY created_at DESC",
        $shipment['id']
    ), ARRAY_A);
    
    return [
        'tracking_number' => $shipment['tracking_number'],
        'status' => $shipment['status'],
        'status_name' => atk_ved_get_status_name($shipment['status']),
        'status_color' => atk_ved_get_status_color($shipment['status']),
        'origin' => $shipment['origin_city'],
        'destination' => $shipment['destination_city'],
        'weight' => $shipment['weight'],
        'estimated_delivery' => $shipment['estimated_delivery'],
        'history' => $history,
        'progress' => atk_ved_calculate_progress($shipment['status'])
    ];
}

/**
 * Демо данные для отслеживания (если нет БД)
 */
function atk_ved_get_demo_tracking(string $tracking_number): array {
    $statuses = [
        'created' => [
            'name' => 'Заявка создана',
            'color' => '#999',
            'progress' => 10
        ],
        'pickup' => [
            'name' => 'Забран со склада',
            'color' => '#2196F3',
            'progress' => 30
        ],
        'in_transit' => [
            'name' => 'В пути',
            'color' => '#FF9800',
            'progress' => 60
        ],
        'customs' => [
            'name' => 'Таможенное оформление',
            'color' => '#9C27B0',
            'progress' => 80
        ],
        'delivered' => [
            'name' => 'Доставлен',
            'color' => '#4CAF50',
            'progress' => 100
        ]
    ];
    
    // Генерируем демо историю
    $demo_statuses = ['created', 'pickup', 'in_transit', 'customs'];
    $random_status = $demo_statuses[array_rand($demo_statuses)];
    $current_status = $statuses[$random_status];
    
    $history = [];
    $cities = ['Пекин', 'Шанхай', 'Гуанчжоу', 'Москва', 'Екатеринбург'];
    
    foreach ($demo_statuses as $status) {
        $history[] = [
            'status' => $status,
            'status_name' => $statuses[$status]['name'],
            'location' => $cities[array_rand($cities)],
            'description' => atk_ved_get_status_description($status),
            'created_at' => date('Y-m-d H:i:s', strtotime('-' . (array_search($status, $demo_statuses) * 5) . ' days'))
        ];
        
        if ($status === $random_status) {
            break;
        }
    }
    
    return [
        'tracking_number' => $tracking_number,
        'status' => $random_status,
        'status_name' => $current_status['name'],
        'status_color' => $current_status['color'],
        'origin' => 'Пекин, Китай',
        'destination' => 'Москва, Россия',
        'weight' => rand(100, 1000),
        'estimated_delivery' => date('Y-m-d', strtotime('+10 days')),
        'history' => array_reverse($history),
        'progress' => $current_status['progress']
    ];
}

/**
 * Название статуса
 */
function atk_ved_get_status_name(string $status): string {
    $names = [
        'created' => __('Заявка создана', 'atk-ved'),
        'pickup' => __('Забран со склада', 'atk-ved'),
        'in_transit' => __('В пути', 'atk-ved'),
        'customs' => __('Таможенное оформление', 'atk-ved'),
        'delivered' => __('Доставлен', 'atk-ved'),
        'cancelled' => __('Отменён', 'atk-ved')
    ];
    return $names[$status] ?? $status;
}

/**
 * Цвет статуса
 */
function atk_ved_get_status_color(string $status): string {
    $colors = [
        'created' => '#999',
        'pickup' => '#2196F3',
        'in_transit' => '#FF9800',
        'customs' => '#9C27B0',
        'delivered' => '#4CAF50',
        'cancelled' => '#F44336'
    ];
    return $colors[$status] ?? '#999';
}

/**
 * Описание статуса
 */
function atk_ved_get_status_description(string $status): string {
    $descriptions = [
        'created' => 'Заявка зарегистрирована в системе',
        'pickup' => 'Груз получен на складе отправителя',
        'in_transit' => 'Груз следует к месту назначения',
        'customs' => 'Проходит таможенную очистку',
        'delivered' => 'Груз доставлен и получен',
        'cancelled' => 'Отслеживание отменено'
    ];
    return $descriptions[$status] ?? '';
}

/**
 * Расчёт прогресса в процентах
 */
function atk_ved_calculate_progress(string $status): int {
    $progress = [
        'created' => 10,
        'pickup' => 30,
        'in_transit' => 60,
        'customs' => 80,
        'delivered' => 100,
        'cancelled' => 0
    ];
    return $progress[$status] ?? 0;
}

/**
 * Создание таблицы отслеживания
 */
function atk_ved_create_shipments_table(): void {
    global $wpdb;
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
    $shipments_table = $wpdb->prefix . 'atk_ved_shipments';
    $history_table = $wpdb->prefix . 'atk_ved_shipment_history';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql_shipments = "CREATE TABLE IF NOT EXISTS {$shipments_table} (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        tracking_number varchar(50) NOT NULL UNIQUE,
        status varchar(20) NOT NULL DEFAULT 'created',
        origin_city varchar(100) DEFAULT '',
        destination_city varchar(100) DEFAULT '',
        weight decimal(10,2) DEFAULT 0,
        estimated_delivery date DEFAULT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY tracking_number (tracking_number),
        KEY status (status)
    ) {$charset_collate};";
    
    $sql_history = "CREATE TABLE IF NOT EXISTS {$history_table} (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        shipment_id bigint(20) NOT NULL,
        status varchar(20) NOT NULL,
        location varchar(200) DEFAULT '',
        description text,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY shipment_id (shipment_id),
        KEY created_at (created_at)
    ) {$charset_collate};";
    
    dbDelta($sql_shipments);
    dbDelta($sql_history);
}
register_activation_hook(__FILE__, 'atk_ved_create_shipments_table');

/**
 * Шорткод отслеживания
 */
function atk_ved_tracking_shortcode(array $atts): string {
    $atts = shortcode_atts([
        'title' => __('Отслеживание груза', 'atk-ved'),
    ], $atts);

    ob_start();
    ?>
    <div class="shipment-tracking" data-nonce="<?php echo esc_attr(wp_create_nonce('atk_ved_tracking_nonce')); ?>">
        <h3 class="tracking-title"><?php echo esc_html($atts['title']); ?></h3>
        
        <form id="trackingForm" class="tracking-form">
            <div class="tracking-input-group">
                <input type="text" 
                       id="trackingNumber" 
                       name="tracking_number" 
                       placeholder="<?php echo esc_attr__('Введите номер отслеживания', 'atk-ved'); ?>"
                       required
                       autocomplete="off">
                <button type="submit" class="tracking-submit">
                    <?php echo esc_html__('Найти', 'atk-ved'); ?>
                </button>
            </div>
        </form>
        
        <div class="tracking-result" id="trackingResult" style="display: none;">
            <div class="tracking-loader">
                <div class="spinner"></div>
            </div>
            
            <div class="tracking-result-content"></div>
        </div>
        
        <div class="tracking-error" id="trackingError" style="display: none;"></div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('shipment_tracking', 'atk_ved_tracking_shortcode');

/**
 * Настройки отслеживания в Customizer
 */
function atk_ved_tracking_customizer($wp_customize): void {
    $wp_customize->add_section('atk_ved_tracking', array(
        'title'    => __('Отслеживание грузов', 'atk-ved'),
        'priority' => 42,
    ));

    $wp_customize->add_setting('atk_ved_tracking_info', array(
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('atk_ved_tracking_info', array(
        'section'     => 'atk_ved_tracking',
        'type'        => 'info',
        'description' => __('Используйте шорткод [shipment_tracking] для добавления формы отслеживания на страницу.', 'atk-ved') . 
                        "\n" . __('Для тестирования введите любой номер (например: ATK123456).', 'atk-ved')
    ));
}
add_action('customize_register', 'atk_ved_tracking_customizer');
