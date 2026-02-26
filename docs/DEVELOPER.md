# ATK VED Theme — Руководство разработчика

## Требования

- PHP 8.1+
- Composer
- Node.js 18+ (для сборки фронтенда)

## Установка

```bash
# Установка PHP-зависимостей
composer install

# Установка Node-зависимостей
npm install
```

## Команды

### PHP-инструменты

```bash
# Статический анализ (PHPStan)
composer phpstan

# Проверка кода на соответствие стандартам (WordPress)
composer phpcs

# Автоматическое исправление стиля кода
composer fix

# Полная проверка (phpcs + phpstan)
composer lint
```

### Фронтенд

```bash
# Сборка для разработки (с sourcemaps)
npm run dev

# Сборка для продакшна (минификация)
npm run build

# Слежение за изменениями
npm run watch

# Очистка кэша сборки
npm run clean
```

## Структура проекта

```
atk-ved/
├── src/                    # PSR-4 классы (namespace ATKVed)
│   ├── loader.php          # Загрузчик
│   ├── base.php            # Базовый класс
│   ├── theme.php           # Главный класс
│   ├── setup.php           # Настройки темы
│   ├── enqueue.php         # CSS/JS
│   ├── ajax.php            # AJAX-обработчики
│   ├── shortcodes.php      # Шорткоды
│   └── customizer.php      # Настройки Customizer
├── inc/                    # Устаревшие файлы (постепенно мигрируются в src/)
├── admin/                  # Админские стили и скрипты
├── css/                    # Стили темы
├── js/                     # Скрипты темы
├── functions.php           # Точка входа
├── composer.json           # PHP-зависимости
├── package.json            # Node-зависимости
├── vite.config.js          # Конфиг Vite
├── phpstan.neon            # Конфиг PHPStan
└── phpunit.xml             # Конфиг PHPUnit (для тестов)
```

## Разработка

### Добавление нового класса в src/

1. Создайте файл в `src/`, например `src/my-class.php`
2. Используйте namespace `ATKVed`:

```php
<?php
declare(strict_types=1);

namespace ATKVed;

class MyClass {
    public function init(): void {
        add_action('init', [$this, 'handle']);
    }

    public function handle(): void {
        // ваш код
    }
}
```

3. Класс автоматически загрузится через Composer autoloader

### Добавление функции в functions.php

Для обратной совместимости функции можно добавлять в `functions.php`:

```php
// Функции-обёртки для доступа из шаблонов
function atk_ved_get_company_info(): array {
    return \ATKVed\Theme::get_company_info();
}
```

## Тестирование

```bash
# Запуск PHPUnit (если настроен)
./vendor/bin/phpunit

# Статический анализ с повышенным уровнем
vendor/bin/phpstan analyse src/ --level=5
```

## Code Style

Проект следует стандартам:
- **PHP**: PSR-12 + WordPress Coding Standards
- **JS/CSS**: Vite + стандартные плагины

## Деплой

1. Запустите `npm run build` для сборки фронтенда
2. Проверьте код: `composer lint`
3. Убедитесь, что нет синтаксических ошибок: `php -l functions.php`
