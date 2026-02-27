/**
 * Web Vitals Integration Example
 * 
 * Этот файл показывает, как интегрировать web-vitals в WordPress тему
 * 
 * Использование:
 * 1. Скопируйте этот код в ваш основной JS файл темы
 * 2. Убедитесь, что web-vitals установлен: npm install web-vitals
 * 3. Настройте сборщик (webpack/vite) для поддержки ES modules
 */

import { onCLS, onFID, onLCP, onFCP, onTTFB, onINP } from 'web-vitals';

/**
 * Отправка метрик в систему аналитики
 */
function sendToAnalytics(metric) {
    const body = JSON.stringify({
        name: metric.name,
        value: metric.value,
        rating: metric.rating,
        delta: metric.delta,
        id: metric.id,
        navigationType: metric.navigationType,
        url: window.location.href,
        userAgent: navigator.userAgent,
    });

    // Метод 1: Google Analytics 4
    if (window.gtag) {
        gtag('event', metric.name, {
            event_category: 'Web Vitals',
            value: Math.round(metric.name === 'CLS' ? metric.value * 1000 : metric.value),
            event_label: metric.id,
            non_interaction: true,
        });
    }

    // Метод 2: Отправка на собственный endpoint
    if (navigator.sendBeacon) {
        // Используем sendBeacon для надежной отправки
        navigator.sendBeacon('/wp-json/atk-ved/v1/analytics', body);
    } else {
        // Fallback для старых браузеров
        fetch('/wp-json/atk-ved/v1/analytics', {
            method: 'POST',
            body: body,
            headers: { 'Content-Type': 'application/json' },
            keepalive: true,
        }).catch(console.error);
    }

    // Метод 3: Логирование в консоль (только для разработки)
    if (window.location.hostname === 'localhost' || window.location.hostname.includes('.local')) {
        console.log('📊 Web Vital:', metric.name, {
            value: metric.value,
            rating: metric.rating,
            delta: metric.delta,
        });
    }
}

/**
 * Визуальная индикация метрик (для разработки)
 */
function showMetricIndicator(metric) {
    // Только для локальной разработки
    if (!window.location.hostname.includes('.local')) return;

    const indicator = document.createElement('div');
    indicator.style.cssText = `
        position: fixed;
        bottom: 10px;
        right: 10px;
        background: ${metric.rating === 'good' ? '#0cce6b' : metric.rating === 'needs-improvement' ? '#ffa400' : '#ff4e42'};
        color: white;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-family: monospace;
        z-index: 10000;
        animation: slideIn 0.3s ease-out;
    `;
    indicator.textContent = `${metric.name}: ${Math.round(metric.value)}${metric.name === 'CLS' ? '' : 'ms'} (${metric.rating})`;
    
    document.body.appendChild(indicator);
    
    setTimeout(() => {
        indicator.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => indicator.remove(), 300);
    }, 3000);
}

// Добавляем CSS анимации
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);

/**
 * Обработчик метрик
 */
function handleMetric(metric) {
    sendToAnalytics(metric);
    showMetricIndicator(metric);
}

// Инициализация отслеживания всех метрик
onCLS(handleMetric);   // Cumulative Layout Shift
onFID(handleMetric);   // First Input Delay (deprecated, но все еще полезен)
onINP(handleMetric);   // Interaction to Next Paint (замена FID)
onLCP(handleMetric);   // Largest Contentful Paint
onFCP(handleMetric);   // First Contentful Paint
onTTFB(handleMetric);  // Time to First Byte

console.log('✅ Web Vitals tracking initialized');

/**
 * Пример WordPress REST API endpoint для приема метрик
 * Добавьте в functions.php вашей темы:
 * 
 * add_action('rest_api_init', function () {
 *     register_rest_route('atk-ved/v1', '/analytics', [
 *         'methods' => 'POST',
 *         'callback' => 'atk_ved_save_web_vitals',
 *         'permission_callback' => '__return_true',
 *     ]);
 * });
 * 
 * function atk_ved_save_web_vitals($request) {
 *     $data = $request->get_json_params();
 *     
 *     // Валидация
 *     if (empty($data['name']) || empty($data['value'])) {
 *         return new WP_Error('invalid_data', 'Invalid metric data', ['status' => 400]);
 *     }
 *     
 *     // Сохранение в базу данных или отправка в внешний сервис
 *     // Например, можно использовать custom post type или таблицу
 *     
 *     // Пример: логирование в файл
 *     if (WP_DEBUG_LOG) {
 *         error_log(sprintf(
 *             'Web Vital: %s = %s (%s) - %s',
 *             $data['name'],
 *             $data['value'],
 *             $data['rating'],
 *             $data['url']
 *         ));
 *     }
 *     
 *     return ['success' => true];
 * }
 */
