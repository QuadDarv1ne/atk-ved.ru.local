# Настройка мониторинга и отслеживания ошибок

## 📊 Sentry - Отслеживание ошибок

### Установка

Sentry SDK уже установлен через Composer:

```bash
composer install
```

### Настройка

1. **Регистрация в Sentry**
   - Перейдите на https://sentry.io
   - Создайте аккаунт (бесплатный план доступен)
   - Создайте новый проект, выберите платформу "PHP"

2. **Получение DSN**
   - После создания проекта скопируйте DSN
   - DSN выглядит так: `https://ключ@sentry.io/номер-проекта`

3. **Добавление DSN в .env**
   
   Откройте файл `.env` и раскомментируйте строку:
   
   ```env
   SENTRY_DSN=https://your-key@sentry.io/your-project-id
   ```
   
   Замените значение на ваш реальный DSN.

### Тестирование

Запустите тестовый скрипт:

```bash
php test-sentry.php
```

Скрипт отправит тестовое сообщение и ошибку в Sentry. Проверьте их в панели управления Sentry.

### Интеграция с WordPress

Добавьте в `wp-config.php` (после загрузки .env):

```php
// Инициализация Sentry
if (defined('SENTRY_DSN') && !empty(SENTRY_DSN)) {
    \Sentry\init([
        'dsn' => SENTRY_DSN,
        'environment' => WP_ENV,
        'traces_sample_rate' => WP_ENV === 'production' ? 0.2 : 1.0,
    ]);
    
    // Отлов фатальных ошибок
    register_shutdown_function(function() {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            \Sentry\captureException(new ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            ));
        }
    });
}
```

### Использование в коде

```php
// Отправка сообщения
\Sentry\captureMessage('Что-то произошло', \Sentry\Severity::warning());

// Отправка исключения
try {
    // ваш код
} catch (Exception $e) {
    \Sentry\captureException($e);
}

// Добавление контекста
\Sentry\configureScope(function (\Sentry\State\Scope $scope): void {
    $scope->setUser([
        'id' => get_current_user_id(),
        'username' => wp_get_current_user()->user_login,
    ]);
    $scope->setTag('page', $_SERVER['REQUEST_URI'] ?? 'unknown');
});
```

---

## 📈 Web Vitals - Метрики производительности

### Установка

Web Vitals уже установлен через npm:

```bash
npm install
```

### Интеграция

Добавьте в ваш основной JavaScript файл (например, `wp-content/themes/your-theme/js/main.js`):

```javascript
import { onCLS, onFID, onLCP, onFCP, onTTFB } from 'web-vitals';

function sendToAnalytics(metric) {
    // Отправка в Google Analytics
    if (window.gtag) {
        gtag('event', metric.name, {
            event_category: 'Web Vitals',
            value: Math.round(metric.name === 'CLS' ? metric.value * 1000 : metric.value),
            event_label: metric.id,
            non_interaction: true,
        });
    }
    
    // Или отправка в собственную систему аналитики
    fetch('/api/analytics', {
        method: 'POST',
        body: JSON.stringify(metric),
        headers: { 'Content-Type': 'application/json' }
    });
    
    // Логирование в консоль (только для разработки)
    if (process.env.NODE_ENV === 'development') {
        console.log(metric);
    }
}

// Отслеживание метрик
onCLS(sendToAnalytics);  // Cumulative Layout Shift
onFID(sendToAnalytics);  // First Input Delay
onLCP(sendToAnalytics);  // Largest Contentful Paint
onFCP(sendToAnalytics);  // First Contentful Paint
onTTFB(sendToAnalytics); // Time to First Byte
```

### Что отслеживается

- **LCP (Largest Contentful Paint)** - время загрузки основного контента
  - Хорошо: < 2.5s
  - Требует улучшения: 2.5s - 4s
  - Плохо: > 4s

- **FID (First Input Delay)** - время до первого взаимодействия
  - Хорошо: < 100ms
  - Требует улучшения: 100ms - 300ms
  - Плохо: > 300ms

- **CLS (Cumulative Layout Shift)** - стабильность визуального контента
  - Хорошо: < 0.1
  - Требует улучшения: 0.1 - 0.25
  - Плохо: > 0.25

### Альтернатива: Использование через CDN

Если не используете сборщик модулей:

```html
<script type="module">
import {onCLS, onFID, onLCP} from 'https://unpkg.com/web-vitals@5?module';

onCLS(console.log);
onFID(console.log);
onLCP(console.log);
</script>
```

---

## 🎯 Рекомендации

### Для разработки
- Включите `WP_DEBUG=true` и `WP_DEBUG_LOG=true`
- Используйте Sentry для отслеживания всех ошибок
- Мониторьте Web Vitals в консоли браузера

### Для продакшена
- Установите `WP_DEBUG=false` и `WP_DEBUG_DISPLAY=false`
- Настройте Sentry с `traces_sample_rate` около 0.1-0.2
- Интегрируйте Web Vitals с Google Analytics или собственной системой
- Регулярно проверяйте отчеты в Sentry

### Безопасность
- Никогда не коммитьте `.env` с реальным `SENTRY_DSN`
- Используйте разные проекты Sentry для dev/staging/production
- Фильтруйте чувствительные данные перед отправкой в Sentry

---

## 📚 Дополнительные ресурсы

- [Документация Sentry PHP](https://docs.sentry.io/platforms/php/)
- [Документация Web Vitals](https://web.dev/vitals/)
- [Google Analytics + Web Vitals](https://github.com/GoogleChrome/web-vitals#send-the-results-to-google-analytics)
