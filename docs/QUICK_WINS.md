# Быстрые победы (Quick Wins)

**Цель**: Улучшения, которые можно внедрить за 1-2 дня с максимальным эффектом

---

## 1. 🔒 БЕЗОПАСНОСТЬ (2-3 часа)

### 1.1 Явное экранирование

**Файлы для исправления**:
- `inc/woocommerce.php`
- `inc/ui-components.php`
- `inc/enhanced-pwa.php`

**Что делать**:
```php
// Найти все echo без esc_*
// Заменить на:
echo esc_html($variable);      // для текста
echo esc_url($url);            // для URL
echo esc_attr($attribute);     // для атрибутов
echo wp_kses_post($html);      // для HTML контента
```

### 1.2 Валидация $_SERVER

**Файл**: `inc/security.php`

```php
// Было
$ip = $_SERVER['REMOTE_ADDR'];

// Стало
$ip = filter_var(
    $_SERVER['REMOTE_ADDR'] ?? '',
    FILTER_VALIDATE_IP,
    FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
) ?: '0.0.0.0';
```

### 1.3 HSTS заголовок

**Файл**: `.htaccess`

```apache
# Добавить после других заголовков
<IfModule mod_headers.c>
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
</IfModule>
```

---

## 2. ⚡ ПРОИЗВОДИТЕЛЬНОСТЬ (3-4 часа)

### 2.1 Preconnect для внешних ресурсов

**Файл**: `header.php`

```html
<head>
    <!-- Preconnect для Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Preconnect для Google Maps -->
    <link rel="preconnect" href="https://maps.googleapis.com">
    
    <!-- DNS-prefetch для аналитики -->
    <link rel="dns-prefetch" href="https://www.google-analytics.com">
    <link rel="dns-prefetch" href="https://mc.yandex.ru">
</head>
```

### 2.2 Defer для некритичных скриптов

**Файл**: `functions.php` или `src/Enqueue.php`

```php
function atk_ved_defer_scripts($tag, $handle, $src) {
    $defer_scripts = [
        'atk-ved-statistics',
        'atk-ved-tracking',
        'atk-ved-gallery',
        'google-analytics',
        'yandex-metrika'
    ];
    
    if (in_array($handle, $defer_scripts, true)) {
        return str_replace(' src', ' defer src', $tag);
    }
    
    return $tag;
}
add_filter('script_loader_tag', 'atk_ved_defer_scripts', 10, 3);
```

### 2.3 Оптимизация шрифтов

**Файл**: `header.php`

```html
<!-- Вместо обычной загрузки -->
<link rel="preload" href="/path/to/font.woff2" as="font" type="font/woff2" crossorigin>

<!-- И добавить font-display -->
<style>
@font-face {
    font-family: 'YourFont';
    src: url('/path/to/font.woff2') format('woff2');
    font-display: swap; /* Показывать системный шрифт пока загружается */
}
</style>
```

---

## 3. 🔄 CI/CD (1 час)

### 3.1 Убрать continue-on-error

**Файл**: `.github/workflows/ci.yml`

```yaml
# Было
- name: Run PHPCS
  run: composer phpcs
  continue-on-error: true  # УДАЛИТЬ ЭТУ СТРОКУ

# Стало
- name: Run PHPCS
  run: composer phpcs
  # Теперь CI упадет при ошибках
```

### 3.2 Добавить cache для зависимостей

```yaml
- name: Cache Composer dependencies
  uses: actions/cache@v3
  with:
    path: vendor
    key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}

- name: Cache npm dependencies
  uses: actions/cache@v3
  with:
    path: node_modules
    key: ${{ runner.os }}-npm-${{ hashFiles('**/package-lock.json') }}
```

---

## 4. 📊 МОНИТОРИНГ (2 часа)

### 4.1 Sentry для отслеживания ошибок

**Установка**:
```bash
composer require sentry/sdk
```

**Файл**: `functions.php`

```php
if (defined('SENTRY_DSN') && SENTRY_DSN) {
    \Sentry\init([
        'dsn' => SENTRY_DSN,
        'environment' => WP_ENV ?? 'production',
        'traces_sample_rate' => 0.2,
    ]);
}
```

**Файл**: `.env`

```env
SENTRY_DSN=https://your-sentry-dsn@sentry.io/project-id
```

### 4.2 Web Vitals tracking

**Файл**: `js/performance-metrics.js`

```javascript
import {getCLS, getFID, getFCP, getLCP, getTTFB} from 'web-vitals';

function sendToAnalytics({name, value, id}) {
    // Google Analytics
    if (typeof gtag !== 'undefined') {
        gtag('event', name, {
            event_category: 'Web Vitals',
            value: Math.round(name === 'CLS' ? value * 1000 : value),
            event_label: id,
            non_interaction: true,
        });
    }
    
    // Или отправить на свой сервер
    navigator.sendBeacon('/api/metrics', JSON.stringify({name, value, id}));
}

getCLS(sendToAnalytics);
getFID(sendToAnalytics);
getFCP(sendToAnalytics);
getLCP(sendToAnalytics);
getTTFB(sendToAnalytics);
```

---

## 5. 📝 ДОКУМЕНТАЦИЯ (1 час)

### 5.1 README badges

**Файл**: `README.md`

```markdown
[![CI Status](https://github.com/QuadDarv1ne/atk-ved.ru.local/workflows/CI/badge.svg)](https://github.com/QuadDarv1ne/atk-ved.ru.local/actions)
[![Code Coverage](https://codecov.io/gh/QuadDarv1ne/atk-ved.ru.local/branch/main/graph/badge.svg)](https://codecov.io/gh/QuadDarv1ne/atk-ved.ru.local)
[![Security Score](https://img.shields.io/badge/security-9.5%2F10-green)](docs/SECURITY_AUDIT.md)
[![PageSpeed](https://img.shields.io/badge/PageSpeed-90%2B-brightgreen)](https://pagespeed.web.dev/)
```

### 5.2 CONTRIBUTING.md

**Файл**: `CONTRIBUTING.md`

```markdown
# Как внести вклад

## Процесс разработки

1. Fork репозитория
2. Создайте ветку: `git checkout -b feature/amazing-feature`
3. Commit изменения: `git commit -m 'Add amazing feature'`
4. Push в ветку: `git push origin feature/amazing-feature`
5. Откройте Pull Request

## Стандарты кода

- PHP: PSR-12
- JavaScript: ESLint
- CSS: Stylelint

## Перед коммитом

```bash
composer phpcs
composer phpstan
npm run lint
npm test
```
```

---

## 6. 🎨 UI/UX (2-3 часа)

### 6.1 Skeleton screens для загрузки

**Файл**: `css/utilities.css`

```css
.skeleton {
    background: linear-gradient(
        90deg,
        #f0f0f0 25%,
        #e0e0e0 50%,
        #f0f0f0 75%
    );
    background-size: 200% 100%;
    animation: skeleton-loading 1.5s infinite;
}

@keyframes skeleton-loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

.skeleton-text {
    height: 1em;
    margin-bottom: 0.5em;
    border-radius: 4px;
}

.skeleton-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
}
```

### 6.2 Улучшенные состояния загрузки

**Файл**: `js/forms.js`

```javascript
function showLoadingState(button) {
    const originalText = button.textContent;
    button.disabled = true;
    button.innerHTML = `
        <span class="spinner"></span>
        <span>Отправка...</span>
    `;
    
    return () => {
        button.disabled = false;
        button.textContent = originalText;
    };
}

// Использование
const resetLoading = showLoadingState(submitButton);
// ... отправка формы
resetLoading();
```

---

## 7. ♿ ДОСТУПНОСТЬ (2 часа)

### 7.1 Skip link

**Файл**: `header.php`

```html
<body>
    <a href="#main-content" class="skip-link">
        Перейти к основному содержимому
    </a>
    <!-- ... -->
    <main id="main-content">
```

**Файл**: `style.css`

```css
.skip-link {
    position: absolute;
    top: -40px;
    left: 0;
    background: #000;
    color: #fff;
    padding: 8px;
    text-decoration: none;
    z-index: 100;
}

.skip-link:focus {
    top: 0;
}
```

### 7.2 Focus visible стили

**Файл**: `css/a11y.css`

```css
/* Убрать outline для мыши, оставить для клавиатуры */
:focus:not(:focus-visible) {
    outline: none;
}

:focus-visible {
    outline: 2px solid #e31e24;
    outline-offset: 2px;
}

/* Улучшенный focus для кнопок */
button:focus-visible,
a:focus-visible {
    outline: 2px solid #e31e24;
    outline-offset: 2px;
    box-shadow: 0 0 0 4px rgba(227, 30, 36, 0.1);
}
```

---

## 8. 📱 МОБИЛЬНАЯ ОПТИМИЗАЦИЯ (1-2 часа)

### 8.1 Viewport height fix для iOS

**Файл**: `js/core.js`

```javascript
// Фикс для 100vh на мобильных
function setVH() {
    const vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', `${vh}px`);
}

setVH();
window.addEventListener('resize', setVH);
```

**Файл**: `style.css`

```css
/* Вместо height: 100vh */
.full-height {
    height: 100vh;
    height: calc(var(--vh, 1vh) * 100);
}
```

### 8.2 Touch-friendly кнопки

**Файл**: `style.css`

```css
/* Минимальный размер для touch */
button,
a.button,
input[type="submit"] {
    min-height: 44px;
    min-width: 44px;
    padding: 12px 24px;
}

/* Увеличить область клика */
.icon-button {
    position: relative;
}

.icon-button::after {
    content: '';
    position: absolute;
    top: -10px;
    right: -10px;
    bottom: -10px;
    left: -10px;
}
```

---

## ✅ ЧЕКЛИСТ ВНЕДРЕНИЯ

### День 1 (4-5 часов)
- [ ] Явное экранирование во всех файлах
- [ ] Валидация $_SERVER
- [ ] HSTS заголовок
- [ ] Preconnect для внешних ресурсов
- [ ] Defer для некритичных скриптов

### День 2 (3-4 часа)
- [ ] Убрать continue-on-error из CI
- [ ] Добавить cache в CI
- [ ] Настроить Sentry
- [ ] Web Vitals tracking
- [ ] Skip link и focus styles

### Бонус (если есть время)
- [ ] Skeleton screens
- [ ] Viewport height fix
- [ ] Touch-friendly кнопки
- [ ] README badges
- [ ] CONTRIBUTING.md

---

## 📊 ОЖИДАЕМЫЙ ЭФФЕКТ

| Улучшение | Эффект |
|-----------|--------|
| Явное экранирование | Безопасность +0.5 |
| HSTS | Безопасность +0.5 |
| Preconnect | LCP -200ms |
| Defer scripts | TTI -300ms |
| Sentry | Быстрое обнаружение ошибок |
| Web Vitals | Мониторинг производительности |
| Skip link | A11y +5 баллов |
| Touch-friendly | Мобильный UX +20% |

**Общее время**: 7-9 часов  
**Общий эффект**: Значительное улучшение безопасности, производительности и доступности

---

**Следующий шаг**: После внедрения Quick Wins переходите к [COMPREHENSIVE_IMPROVEMENTS_PLAN.md](COMPREHENSIVE_IMPROVEMENTS_PLAN.md)
