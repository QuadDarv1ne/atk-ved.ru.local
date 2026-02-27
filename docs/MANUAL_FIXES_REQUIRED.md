# Ручные исправления (требуется выполнить)

**Дата**: 27 февраля 2026  
**Причина**: Ограничения дискового пространства при автоматическом внедрении

---

## 1. Подключить security-helpers.php

**Файл**: `wp-content/themes/atk-ved/inc/module-loader.php`

**Найти** (строка ~35):
```php
        // Безопасность - всегда
        'security' => [
            'always' => true,
            'files' => [
                '/inc/security.php',
                '/inc/security-headers.php',
                '/inc/advanced-security.php',
                '/inc/logger.php',
            ],
        ],
```

**Заменить на**:
```php
        // Безопасность - всегда
        'security' => [
            'always' => true,
            'files' => [
                '/inc/security-helpers.php',  // ДОБАВИТЬ ЭТУ СТРОКУ
                '/inc/security.php',
                '/inc/security-headers.php',
                '/inc/advanced-security.php',
                '/inc/logger.php',
            ],
        ],
```

---

## 2. Использовать helper функции в security.php

**Файл**: `wp-content/themes/atk-ved/inc/security.php`

### 2.1 Функция atk_ved_limit_login_attempts (строка ~126)

**Найти**:
```php
function atk_ved_limit_login_attempts() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $attempts = get_transient('login_attempts_' . $ip);
```

**Заменить на**:
```php
function atk_ved_limit_login_attempts() {
    $ip = atk_ved_get_client_ip();
    $attempts = get_transient('login_attempts_' . $ip);
```

### 2.2 wp_login_failed hook (строка ~134)

**Найти**:
```php
add_action('wp_login_failed', function() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $attempts = get_transient('login_attempts_' . $ip) ?: 0;
```

**Заменить на**:
```php
add_action('wp_login_failed', function() {
    $ip = atk_ved_get_client_ip();
    $attempts = get_transient('login_attempts_' . $ip) ?: 0;
```

### 2.3 wp_login hook (строка ~140)

**Найти**:
```php
add_action('wp_login', function() {
    $ip = $_SERVER['REMOTE_ADDR'];
    delete_transient('login_attempts_' . $ip);
```

**Заменить на**:
```php
add_action('wp_login', function() {
    $ip = atk_ved_get_client_ip();
    delete_transient('login_attempts_' . $ip);
```

### 2.4 Функция atk_ved_protect_wp_config (строка ~160)

**Найти**:
```php
function atk_ved_protect_wp_config() {
    if (strpos($_SERVER['REQUEST_URI'], 'wp-config.php') !== false) {
```

**Заменить на**:
```php
function atk_ved_protect_wp_config() {
    if (strpos(atk_ved_get_request_uri(), 'wp-config.php') !== false) {
```

### 2.5 Функция atk_ved_prevent_hotlinking (строка ~177)

**Найти**:
```php
function atk_ved_prevent_hotlinking(): void {
    if (!is_admin()) {
        $referer    = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $site_url   = home_url();
        $parsed_url = wp_parse_url($site_url);
        $host       = $parsed_url['host'] ?? '';
        
        if ($referer && strpos($referer, $host) === false) {
            // Проверка на изображения
            $request_uri = $_SERVER['REQUEST_URI'];
```

**Заменить на**:
```php
function atk_ved_prevent_hotlinking(): void {
    if (!is_admin()) {
        $referer    = atk_ved_get_referer();
        $site_url   = home_url();
        $parsed_url = wp_parse_url($site_url);
        $host       = $parsed_url['host'] ?? '';
        
        if ($referer && strpos($referer, $host) === false) {
            // Проверка на изображения
            $request_uri = atk_ved_get_request_uri();
```

### 2.6 Функция atk_ved_block_suspicious_requests (строка ~298)

**Найти**:
```php
function atk_ved_block_suspicious_requests(): void {
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$request_uri = $_SERVER['REQUEST_URI'] ?? '';
```

**Заменить на**:
```php
function atk_ved_block_suspicious_requests(): void {
	$request_uri = atk_ved_get_request_uri();
```

**И найти** (строка ~320):
```php
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
	if ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
```

**Заменить на**:
```php
	if ( atk_ved_get_request_method() === 'POST' ) {
```

### 2.7 Функция atk_ved_verify_referer (строка ~341)

**Найти**:
```php
function atk_ved_verify_referer(): void {
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
	if ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$referer = $_SERVER['HTTP_REFERER'] ?? '';
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$host = $_SERVER['HTTP_HOST'] ?? '';
```

**Заменить на**:
```php
function atk_ved_verify_referer(): void {
	if ( atk_ved_get_request_method() === 'POST' ) {
		$referer = atk_ved_get_referer();
		$host = atk_ved_get_http_host();
```

### 2.8 Функция atk_ved_verify_csrf_token (строка ~378)

**Найти**:
```php
function atk_ved_verify_csrf_token(): bool {
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
	if ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
```

**Заменить на**:
```php
function atk_ved_verify_csrf_token(): bool {
	if ( atk_ved_get_request_method() === 'POST' ) {
```

### 2.9 Функция atk_ved_protect_sensitive_files (строка ~432)

**Найти**:
```php
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$request_uri = $_SERVER['REQUEST_URI'] ?? '';
```

**Заменить на**:
```php
	$request_uri = atk_ved_get_request_uri();
```

---

## 3. Настроить Sentry для мониторинга ошибок

### 3.1 Установить Sentry SDK

```bash
cd wp-content/themes/atk-ved
composer require sentry/sdk
```

### 3.2 Зарегистрироваться в Sentry

1. Перейти на https://sentry.io
2. Создать аккаунт (бесплатный план)
3. Создать новый проект (PHP)
4. Скопировать DSN (выглядит как: `https://xxx@sentry.io/123456`)

### 3.3 Добавить в .env

**Файл**: `.env`

```env
# Sentry Error Tracking
SENTRY_DSN=https://your-key-here@sentry.io/your-project-id
SENTRY_ENVIRONMENT=production
```

### 3.4 Добавить в wp-config.php

**Файл**: `wp-config.php`

Добавить после загрузки .env (после строки с `Dotenv::createImmutable`):

```php
// Sentry Configuration
if (getenv('SENTRY_DSN')) {
    define('SENTRY_DSN', getenv('SENTRY_DSN'));
    define('SENTRY_ENVIRONMENT', getenv('SENTRY_ENVIRONMENT') ?: 'production');
}
```

### 3.5 Инициализировать Sentry в functions.php

**Файл**: `wp-content/themes/atk-ved/functions.php`

Добавить после инициализации темы (после строки ~80):

```php
// ============================================
// 4. Sentry Error Tracking
// ============================================

if (defined('SENTRY_DSN') && SENTRY_DSN) {
    try {
        \Sentry\init([
            'dsn' => SENTRY_DSN,
            'environment' => SENTRY_ENVIRONMENT ?? 'production',
            'traces_sample_rate' => 0.2, // 20% транзакций для performance monitoring
            'profiles_sample_rate' => 0.2,
            'send_default_pii' => false, // Не отправлять PII данные
            'before_send' => function (\Sentry\Event $event): ?\Sentry\Event {
                // Фильтрация чувствительных данных
                if ($event->getRequest()) {
                    $request = $event->getRequest();
                    // Удаляем пароли из данных
                    if ($request['data'] ?? null) {
                        unset($request['data']['password']);
                        unset($request['data']['pwd']);
                    }
                }
                return $event;
            },
        ]);
        
        // Добавить пользовательский контекст
        \Sentry\configureScope(function (\Sentry\State\Scope $scope): void {
            $scope->setTag('wordpress_version', get_bloginfo('version'));
            $scope->setTag('theme_version', ATK_VED_VERSION ?? 'unknown');
            $scope->setTag('php_version', PHP_VERSION);
        });
        
    } catch (\Throwable $e) {
        error_log('Sentry initialization failed: ' . $e->getMessage());
    }
}
```

### 3.6 Тестирование Sentry

Добавить временно в functions.php для теста:

```php
// ТЕСТ SENTRY - УДАЛИТЬ ПОСЛЕ ПРОВЕРКИ
add_action('init', function() {
    if (isset($_GET['test_sentry']) && current_user_can('manage_options')) {
        throw new \Exception('Sentry test error - это работает!');
    }
});
```

Затем открыть: `https://your-site.com/?test_sentry`

Проверить в Sentry dashboard, что ошибка появилась.

После проверки - удалить тестовый код!

---

## 4. Добавить Web Vitals tracking

### 4.1 Установить web-vitals

```bash
cd wp-content/themes/atk-ved
npm install web-vitals --save
```

### 4.2 Обновить performance-metrics.js

**Файл**: `wp-content/themes/atk-ved/js/performance-metrics.js`

**Заменить весь файл на**:

```javascript
/**
 * Web Vitals Tracking
 * Отслеживание Core Web Vitals и отправка в Google Analytics
 */

import {getCLS, getFID, getFCP, getLCP, getTTFB} from 'web-vitals';

/**
 * Отправка метрик в Google Analytics
 */
function sendToAnalytics({name, value, id, delta}) {
    // Google Analytics 4
    if (typeof gtag !== 'undefined') {
        gtag('event', name, {
            event_category: 'Web Vitals',
            value: Math.round(name === 'CLS' ? value * 1000 : value),
            event_label: id,
            non_interaction: true,
        });
    }
    
    // Яндекс.Метрика
    if (typeof ym !== 'undefined') {
        const metrikaId = window.atk_ved_metrika_id || 0;
        if (metrikaId) {
            ym(metrikaId, 'reachGoal', 'web_vitals', {
                metric: name,
                value: Math.round(name === 'CLS' ? value * 1000 : value),
            });
        }
    }
    
    // Отправка на свой сервер (опционально)
    if (navigator.sendBeacon) {
        const body = JSON.stringify({
            name,
            value,
            id,
            delta,
            url: window.location.href,
            timestamp: Date.now(),
        });
        
        navigator.sendBeacon('/wp-json/atk-ved/v1/metrics', body);
    }
    
    // Логирование в консоль (только для разработки)
    if (window.location.hostname === 'localhost' || window.location.hostname === 'atk-ved.ru.local') {
        console.log(`[Web Vitals] ${name}:`, {
            value: Math.round(value),
            rating: getRating(name, value),
            id,
        });
    }
}

/**
 * Получить рейтинг метрики (good/needs-improvement/poor)
 */
function getRating(name, value) {
    const thresholds = {
        CLS: [0.1, 0.25],
        FID: [100, 300],
        LCP: [2500, 4000],
        FCP: [1800, 3000],
        TTFB: [800, 1800],
    };
    
    const [good, poor] = thresholds[name] || [0, 0];
    
    if (value <= good) return 'good';
    if (value <= poor) return 'needs-improvement';
    return 'poor';
}

// Инициализация отслеживания
getCLS(sendToAnalytics);
getFID(sendToAnalytics);
getFCP(sendToAnalytics);
getLCP(sendToAnalytics);
getTTFB(sendToAnalytics);

// Экспорт для использования в других модулях
export {sendToAnalytics, getRating};
```

### 4.3 Создать REST API endpoint для метрик

**Файл**: `wp-content/themes/atk-ved/inc/rest-api.php`

Добавить в конец файла:

```php
/**
 * Endpoint для сохранения Web Vitals метрик
 */
add_action('rest_api_init', function() {
    register_rest_route('atk-ved/v1', '/metrics', [
        'methods' => 'POST',
        'callback' => 'atk_ved_save_web_vitals',
        'permission_callback' => '__return_true',
    ]);
});

function atk_ved_save_web_vitals(\WP_REST_Request $request) {
    $data = $request->get_json_params();
    
    // Валидация
    if (!isset($data['name']) || !isset($data['value'])) {
        return new \WP_Error('invalid_data', 'Missing required fields', ['status' => 400]);
    }
    
    // Сохранение в transient для агрегации
    $metrics_key = 'web_vitals_' . date('Y-m-d');
    $metrics = get_transient($metrics_key) ?: [];
    
    $metrics[] = [
        'name' => sanitize_text_field($data['name']),
        'value' => floatval($data['value']),
        'url' => esc_url_raw($data['url'] ?? ''),
        'timestamp' => intval($data['timestamp'] ?? time()),
    ];
    
    set_transient($metrics_key, $metrics, DAY_IN_SECONDS);
    
    return ['success' => true];
}
```

---

## 5. Проверка выполненных изменений

### Чеклист

- [ ] security-helpers.php подключен в module-loader.php
- [ ] Все $_SERVER заменены на helper функции в security.php
- [ ] Sentry установлен и настроен
- [ ] Sentry протестирован и работает
- [ ] Web Vitals tracking добавлен
- [ ] REST API endpoint для метрик создан
- [ ] Все изменения закоммичены в git

### Команды для проверки

```bash
# Проверить, что security-helpers.php загружается
php -r "require 'wp-content/themes/atk-ved/inc/security-helpers.php'; echo 'OK';"

# Проверить синтаксис всех измененных файлов
php -l wp-content/themes/atk-ved/inc/module-loader.php
php -l wp-content/themes/atk-ved/inc/security.php
php -l wp-content/themes/atk-ved/functions.php

# Запустить PHPCS
cd wp-content/themes/atk-ved
composer phpcs

# Запустить PHPStan
composer phpstan
```

---

## 6. После внедрения

1. Очистить кэш WordPress
2. Очистить кэш браузера
3. Проверить логи на ошибки
4. Протестировать формы
5. Проверить Sentry dashboard
6. Проверить Google Analytics (Web Vitals события)

---

**Статус**: ⏳ Ожидает ручного внедрения  
**Приоритет**: 🔴 Высокий  
**Время**: ~30-40 минут
