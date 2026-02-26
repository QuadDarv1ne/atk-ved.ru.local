<?php
/**
 * Интерактивная карта доставки
 * Показывает маршруты из Китая в города России
 *
 * @package ATK_VED
 * @since 3.4.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Шорткод интерактивной карты доставки
 */
function atk_ved_delivery_map_shortcode(array $atts): string {
    $atts = shortcode_atts([
        'height' => '500',
        'zoom' => '4',
        'show_routes' => '1',
        'show_cities' => '1',
    ], $atts);

    // Города отправления (Китай)
    $china_cities = [
        'beijing' => ['name' => 'Пекин', 'lat' => 39.9042, 'lng' => 116.4074],
        'shanghai' => ['name' => 'Шанхай', 'lat' => 31.2304, 'lng' => 121.4737],
        'guangzhou' => ['name' => 'Гуанчжоу', 'lat' => 23.1291, 'lng' => 113.2644],
        'shenzhen' => ['name' => 'Шэньчжэнь', 'lat' => 22.5431, 'lng' => 114.0579],
        'yiwu' => ['name' => 'Иу', 'lat' => 29.3069, 'lng' => 120.0769],
        'dongguan' => ['name' => 'Дунгуань', 'lat' => 23.0205, 'lng' => 113.7518],
    ];

    // Города назначения (Россия)
    $russia_cities = [
        'moscow' => ['name' => 'Москва', 'lat' => 55.7558, 'lng' => 37.6173],
        'spb' => ['name' => 'Санкт-Петербург', 'lat' => 59.9343, 'lng' => 30.3351],
        'ekb' => ['name' => 'Екатеринбург', 'lat' => 56.8389, 'lng' => 60.6057],
        'kazan' => ['name' => 'Казань', 'lat' => 55.7961, 'lng' => 49.1064],
        'novosibirsk' => ['name' => 'Новосибирск', 'lat' => 55.0084, 'lng' => 82.9357],
        'vladivostok' => ['name' => 'Владивосток', 'lat' => 43.1198, 'lng' => 131.8869],
    ];

    ob_start();
    ?>
    <div class="delivery-map-container" style="height: <?php echo esc_attr($atts['height']); ?>px;">
        <div id="deliveryMap" class="delivery-map" style="height: 100%; width: 100%;"></div>
        
        <div class="delivery-map-legend">
            <h4><?php _e('Маршруты доставки', 'atk-ved'); ?></h4>
            <div class="legend-item">
                <span class="legend-color" style="background: #e31e24;"></span>
                <span><?php _e('Авиа', 'atk-ved'); ?></span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background: #2196f3;"></span>
                <span><?php _e('Ж/Д', 'atk-ved'); ?></span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background: #4caf50;"></span>
                <span><?php _e('Авто', 'atk-ved'); ?></span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background: #ff9800;"></span>
                <span><?php _e('Море', 'atk-ved'); ?></span>
            </div>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // Инициализация карты Leaflet
        function initDeliveryMap() {
            if (typeof L === 'undefined') {
                // Загружаем Leaflet динамически
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                document.head.appendChild(link);

                const script = document.createElement('script');
                script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                script.onload = createMap;
                document.body.appendChild(script);
            } else {
                createMap();
            }

            function createMap() {
                // Создаем карту
                const map = L.map('deliveryMap').setView([45, 80], <?php echo esc_js($atts['zoom']); ?>);

                // Добавляем слой OpenStreetMap
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 18
                }).addTo(map);

                // Города Китая
                const chinaCities = <?php echo json_encode($china_cities); ?>;
                const russiaCities = <?php echo json_encode($russia_cities); ?>;

                // Добавляем маркеры городов Китая
                Object.values(chinaCities).forEach(city => {
                    L.marker([city.lat, city.lng])
                        .addTo(map)
                        .bindPopup(`<b>${city.name}</b><br>Город отправления`);
                });

                // Добавляем маркеры городов России
                Object.values(russiaCities).forEach(city => {
                    L.marker([city.lat, city.lng])
                        .addTo(map)
                        .bindPopup(`<b>${city.name}</b><br>Город назначения`);
                });

                // Рисуем маршруты
                <?php if ($atts['show_routes'] === '1'): ?>
                // Ж/Д маршрут (Пекин - Москва)
                const railRoute = L.polyline([
                    [39.9042, 116.4074], // Пекин
                    [55.7558, 37.6173]   // Москва
                ], {
                    color: '#2196f3',
                    weight: 3,
                    opacity: 0.7,
                    dashArray: '10, 10'
                }).addTo(map);

                railRoute.bindPopup('Ж/Д маршрут: Пекин → Москва<br>20-30 дней');

                // Авиа маршрут (Шанхай - Москва)
                const airRoute = L.polyline([
                    [31.2304, 121.4737], // Шанхай
                    [55.7558, 37.6173]   // Москва
                ], {
                    color: '#e31e24',
                    weight: 2,
                    opacity: 0.8
                }).addTo(map);

                airRoute.bindPopup('Авиа маршрут: Шанхай → Москва<br>5-10 дней');

                // Авто маршрут (Гуанчжоу - Екатеринбург)
                const autoRoute = L.polyline([
                    [23.1291, 113.2644], // Гуанчжоу
                    [56.8389, 60.6057]   // Екатеринбург
                ], {
                    color: '#4caf50',
                    weight: 3,
                    opacity: 0.7
                }).addTo(map);

                autoRoute.bindPopup('Авто маршрут: Гуанчжоу → Екатеринбург<br>15-25 дней');

                // Морской маршрут (Шанхай - Владивосток)
                const seaRoute = L.polyline([
                    [31.2304, 121.4737], // Шанхай
                    [35.0, 130.0],       // Точка в море
                    [43.1198, 131.8869]  // Владивосток
                ], {
                    color: '#ff9800',
                    weight: 2,
                    opacity: 0.7,
                    dashArray: '5, 5'
                }).addTo(map);

                seaRoute.bindPopup('Морской маршрут: Шанхай → Владивосток<br>35-45 дней');
                <?php endif; ?>

                // Автоматически подгоняем зум для показа всех маршрутов
                const group = new L.featureGroup([
                    ...Object.values(chinaCities).map(c => L.marker([c.lat, c.lng])),
                    ...Object.values(russiaCities).map(c => L.marker([c.lat, c.lng]))
                ]);
                
                if (group.getLayers().length > 0) {
                    map.fitBounds(group.getBounds(), { padding: [50, 50] });
                }
            }
        }

        initDeliveryMap();
    });
    </script>

    <style>
    .delivery-map-container {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .delivery-map {
        z-index: 1;
    }

    .delivery-map-legend {
        position: absolute;
        bottom: 20px;
        right: 20px;
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        min-width: 180px;
    }

    .delivery-map-legend h4 {
        margin: 0 0 15px;
        font-size: 14px;
        font-weight: 700;
        color: #2c2c2c;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        font-size: 13px;
        color: #666;
    }

    .legend-item:last-child {
        margin-bottom: 0;
    }

    .legend-color {
        width: 20px;
        height: 3px;
        border-radius: 2px;
        flex-shrink: 0;
    }

    @media (max-width: 768px) {
        .delivery-map-legend {
            bottom: 10px;
            right: 10px;
            left: 10px;
            padding: 15px;
            min-width: auto;
        }

        .delivery-map-legend h4 {
            font-size: 12px;
        }

        .legend-item {
            font-size: 12px;
        }

        .legend-color {
            width: 16px;
        }
    }

    /* Тёмная тема */
    body.dark-mode .delivery-map-legend,
    body.auto-dark .delivery-map-legend {
        background: #2d2d2d;
    }

    body.dark-mode .delivery-map-legend h4,
    body.auto-dark .delivery-map-legend h4 {
        color: #fff;
    }

    body.dark-mode .delivery-map-legend .legend-item,
    body.auto-dark .delivery-map-legend .legend-item {
        color: #e0e0e0;
    }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode('delivery_map', 'atk_ved_delivery_map_shortcode');

/**
 * Регистрация виджета карты
 */
function atk_ved_delivery_map_widget_init(): void {
    register_widget('ATK_VED_Delivery_Map_Widget');
}
add_action('widgets_init', 'atk_ved_delivery_map_widget_init');

/**
 * Класс виджета карты доставки
 */
class ATK_VED_Delivery_Map_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'atk_ved_delivery_map',
            __('Карта доставки', 'atk-ved'),
            [
                'description' => __('Интерактивная карта маршрутов доставки', 'atk-ved'),
            ]
        );
    }

    public function widget($args, $instance): void {
        $title = !empty($instance['title']) ? $instance['title'] : __('Карта доставки', 'atk-ved');
        $height = !empty($instance['height']) ? $instance['height'] : '400';

        echo $args['before_widget'];

        if (!empty($title)) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }

        echo do_shortcode('[delivery_map height="' . esc_attr($height) . '"]');

        echo $args['after_widget'];
    }

    public function form($instance): void {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $height = !empty($instance['height']) ? $instance['height'] : '400';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php _e('Заголовок:', 'atk-ved'); ?>
            </label>
            <input class="widefat"
                   id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>"
                   type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('height')); ?>">
                <?php _e('Высота (px):', 'atk-ved'); ?>
            </label>
            <input class="tiny-text"
                   id="<?php echo esc_attr($this->get_field_id('height')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('height')); ?>"
                   type="number"
                   value="<?php echo esc_attr($height); ?>"
                   min="200"
                   max="800">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance): array {
        return [
            'title' => sanitize_text_field($new_instance['title']),
            'height' => absint($new_instance['height']),
        ];
    }
}
