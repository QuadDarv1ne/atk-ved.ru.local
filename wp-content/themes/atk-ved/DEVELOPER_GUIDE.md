# 🧑‍💻 Руководство разработчика

## Быстрый старт

### 1. Установка

```bash
# Клонировать репозиторий
git clone <repo-url>
cd atk-ved.ru.local

# Установить PHP зависимости
composer install

# Установить JS зависимости
cd wp-content/themes/atk-ved
npm install

# Запустить Docker (опционально)
docker-compose up -d
```

### 2. Запуск разработки

```bash
# Режим разработки с hot-reload
npm run dev

# Сборка в продакшен
npm run build

# Watch режим
npm run watch
```

---

## 📁 Структура проекта

```
atk-ved.ru.local/
├── .github/workflows/       # CI/CD конфигурации
├── docker/                  # Docker файлы
├── wp-content/themes/atk-ved/
│   ├── admin/              # Админ панель
│   ├── blocks/             # Gutenberg блоки
│   ├── css/                # Стили
│   │   ├── accessibility.css
│   │   ├── ui-enhancements.css
│   │   └── ...
│   ├── inc/                # PHP инклюды
│   │   ├── accessibility.php
│   │   ├── cache-manager.php
│   │   ├── seo-advanced.php
│   │   └── ...
│   ├── js/                 # JavaScript
│   │   ├── ui-components-enhanced.js
│   │   └── ...
│   ├── scripts/            # NPM скрипты
│   ├── src/                # PSR-4 классы
│   ├── tests/              # PHPUnit тесты
│   └── ...
└── ...
```

---

## 🛠️ Инструменты разработки

### PHP

```bash
# Статический анализ
composer phpstan

# Проверка стиля кода
composer phpcs

# Авто-исправление стиля
composer phpcbf

# Запуск тестов
composer phpunit

# Все проверки
composer test
```

### JavaScript

```bash
# ESLint
npm run lint:js

# Stylelint
npm run lint:css

# Форматирование
npm run format

# Сборка
npm run build
```

---

## 🧪 Тестирование

### PHPUnit

```bash
# Запустить все тесты
composer phpunit

# Конкретный тест
vendor/bin/phpunit --filter test_pluralize

# С покрытием
vendor/bin/phpunit --coverage-html ./coverage
```

### JavaScript тесты

```bash
# Запустить тесты
npm test

# Watch режим
npm run test:watch
```

### E2E тесты (Playwright)

```bash
# Установить браузеры
npx playwright install

# Запустить тесты
npx playwright test

# UI режим
npx playwright test --ui
```

---

## 📝 Convention Commits

Используем [Conventional Commits](https://www.conventionalcommits.org/):

```
<type>(<scope>): <description>

[optional body]

[optional footer(s)]
```

### Типы

- `feat` — новая функция
- `fix` — исправление ошибки
- `docs` — документация
- `style` — стиль кода
- `refactor` — рефакторинг
- `test` — тесты
- `chore` — обслуживание

### Примеры

```bash
git commit -m "feat(ui): добавить toast уведомления"
git commit -m "fix(seo): исправить дублирование meta tags"
git commit -m "docs: обновить README"
git commit -m "refactor(cache): оптимизировать кэширование"
```

---

## 🎨 Code Style

### PHP

Следуем [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/):

```php
<?php
/**
 * Пример функции
 *
 * @since 3.3.0
 * @param string $param Параметр
 * @return mixed Результат
 */
function atk_ved_example( string $param ) {
    if ( empty( $param ) ) {
        return new WP_Error( 'empty', 'Пусто' );
    }

    /**
     * Фильтр результата
     *
     * @since 3.3.0
     * @param mixed  $result Результат
     * @param string $param  Параметр
     */
    return apply_filters( 'atk_ved_example', $result, $param );
}
```

### JavaScript

```javascript
/**
 * Пример функции
 *
 * @since 3.3.0
 * @param {Object} options Настройки
 * @return {void}
 */
(function($) {
    'use strict';

    function atkVedExample(options) {
        var settings = $.extend({
            debug: false
        }, options);

        if (settings.debug) {
            console.log('Debug');
        }
    }

    window.atkVedExample = atkVedExample;

})(jQuery);
```

### CSS

```css
/**
 * Компонент
 *
 * @since 3.3.0
 */

.component {
    /* Переменные */
    --color: var(--color-primary);

    /* Свойства */
    display: flex;
    gap: var(--spacing-4);

    /* Состояния */
    &:hover {
        --color: var(--color-primary-dark);
    }
}
```

---

## 🚀 Деплой

### Через GitHub Actions

```bash
# Создать тег
git tag -a v3.3.0 -m "Release v3.3.0"
git push origin v3.3.0

# Автоматически создастся релиз
```

### Вручную

```bash
# Собрать тему
npm run build

# Создать zip
cd wp-content/themes/atk-ved
zip -r ../../../atk-ved-theme.zip . -x "node_modules/*" -x "tests/*"

# Загрузить на сервер
scp ../../../atk-ved-theme.zip user@server:/tmp/

# Распаковать на сервере
ssh user@server
cd /path/to/wp-content/themes
unzip -o /tmp/atk-ved-theme.zip
```

---

## 🔧 Отладка

### WordPress Debug

```php
// wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', true);
define('SAVEQUERIES', true);
```

### Логирование

```php
// В файл debug.log
error_log('Message');

// С данными
error_log(print_r($data, true));

// В консоли браузера
console.log('Message');
```

### Query Monitor

```bash
# Установить плагин
wp plugin install query-monitor --activate

# Откроет панель отладки в админке
```

---

## 📊 Производительность

### Проверка

```bash
# Lighthouse
npm run lighthouse

# PageSpeed Insights
open https://pagespeed.web.dev/

# WebPageTest
open https://www.webpagetest.org/
```

### Оптимизация

```bash
# Оптимизировать изображения
npm run optimize:images

# Сгенерировать WebP
npm run generate:webp

# Критический CSS
npm run critical
```

---

## 🐛 Troubleshooting

### Ошибки сборки

```bash
# Очистить кэш
composer clear-cache
npm cache clean --force

# Переустановить
rm -rf vendor node_modules
composer install
npm install
```

### Проблемы с Docker

```bash
# Пересоздать контейнеры
docker-compose down -v
docker-compose up -d

# Очистить volumes
docker volume prune
```

### Тесты не работают

```bash
# Проверить версию PHP
php -v

# Проверить зависимости
composer install
npm install

# Запустить локально
composer test
```

---

## 📚 Ресурсы

### Документация

- [WordPress Developer Handbook](https://developer.wordpress.org/)
- [Gutenberg Handbook](https://developer.wordpress.org/block-editor/)
- [ACF Documentation](https://www.advancedcustomfields.com/resources/)

### Инструменты

- [Query Monitor](https://querymonitor.com/)
- [Debug Bar](https://wordpress.org/plugins/debug-bar/)
- [Log Deprecated Notices](https://wordpress.org/plugins/log-deprecated-notices/)

### Стандарты

- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- [PSR Standards](https://www.php-fig.org/psr/)
- [Conventional Commits](https://www.conventionalcommits.org/)

---

**Версия:** 3.3.0  
**Последнее обновление:** Февраль 2026
