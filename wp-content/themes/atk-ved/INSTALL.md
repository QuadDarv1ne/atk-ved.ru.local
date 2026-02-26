# Установка и настройка темы ATK VED v3.4.0

## Системные требования

### Минимальные
- PHP: 8.1+
- WordPress: 6.0+
- MySQL: 5.7+ или MariaDB 10.3+
- Apache/Nginx с mod_rewrite
- Память PHP: 256MB+

### Рекомендуемые
- PHP: 8.2+
- WordPress: 6.4+
- MySQL: 8.0+ или MariaDB 10.6+
- Nginx с HTTP/2
- Память PHP: 512MB+
- Redis/Memcached для object cache

## Быстрая установка

### 1. Загрузка темы
```bash
cd wp-content/themes/
git clone <repository-url> atk-ved
cd atk-ved
```

### 2. Установка зависимостей
```bash
# PHP зависимости (опционально)
composer install --no-dev --optimize-autoloader

# Node.js зависимости (для разработки)
npm install
```

### 3. Активация темы
1. Войдите в админ-панель WordPress
2. Перейдите в "Внешний вид" → "Темы"
3. Активируйте тему "АТК ВЭД"

### 4. Настройка .htaccess
Убедитесь, что в корне сайта есть файл .htaccess с правилами кэширования и безопасности.

### 5. Настройка темы
1. Перейдите в "Внешний вид" → "Настроить"
2. Заполните основные настройки:
   - Логотип
   - Контактные данные
   - Социальные сети
   - Цвета и шрифты

## Настройка для продакшена

### 1. Включить кэширование
```php
// wp-config.php
define('WP_CACHE', true);
define('WP_CACHE_KEY_SALT', 'atk-ved_');
```

### 2. Настроить Redis (опционально)
```php
// wp-config.php
define('WP_REDIS_HOST', '127.0.0.1');
define('WP_REDIS_PORT', 6379);
define('WP_REDIS_DATABASE', 0);
```

### 3. Включить HTTPS
```apache
# .htaccess - раскомментировать строку
Header set Strict-Transport-Security "max-age=31536000; includeSubDomains"
```

### 4. Настроить CDN (опционально)
```php
// wp-config.php
define('CDN_URL', 'https://cdn.atk-ved.ru');
```

### 5. Оптимизация базы данных
```sql
-- Добавить индексы для производительности
ALTER TABLE wp_posts ADD INDEX idx_post_type_status (post_type, post_status);
ALTER TABLE wp_postmeta ADD INDEX idx_meta_key_value (meta_key, meta_value(191));
```

## Настройка для разработки

### 1. Включить режим отладки
```php
// wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', true);
```

### 2. Установить dev зависимости
```bash
composer install
npm install
```

### 3. Запустить watchers
```bash
# Отслеживание изменений CSS
npm run watch:css

# Отслеживание изменений JS
npm run watch:js
```

## Настройка меню

### Создание меню
1. Перейдите в "Внешний вид" → "Меню"
2. Создайте следующие меню:
   - **Главное меню** (primary) - основная навигация
   - **Футер - Услуги** (footer-services) - услуги в футере
   - **Футер - Компания** (footer-company) - информация о компании

### Рекомендуемая структура главного меню
```
- Главная
- Услуги
  - Поиск товаров
  - Доставка
  - Таможня
- Калькулятор
- О компании
- Контакты
```

## Настройка виджетов

Тема не использует виджеты, все секции настраиваются через:
1. Customizer (Внешний вид → Настроить)
2. Шорткоды на страницах
3. ACF поля (если установлен плагин)

## Рекомендуемые плагины

### Обязательные
- **Contact Form 7** - формы обратной связи
- **Yoast SEO** или **Rank Math** - SEO оптимизация

### Рекомендуемые
- **WP Rocket** - кэширование и оптимизация
- **Smush** - оптимизация изображений
- **Wordfence** - безопасность
- **UpdraftPlus** - резервное копирование

### Опциональные
- **Advanced Custom Fields PRO** - дополнительные поля
- **WooCommerce** - интернет-магазин
- **WPML** - мультиязычность

## Настройка PWA

### 1. Проверить Service Worker
Откройте DevTools → Application → Service Workers
Должен быть зарегистрирован `/sw.js`

### 2. Проверить манифест
Откройте DevTools → Application → Manifest
Должен загружаться `/manifest.json`

### 3. Тестирование PWA
1. Откройте сайт в Chrome
2. Нажмите F12 → Lighthouse
3. Выберите "Progressive Web App"
4. Запустите аудит

## Настройка аналитики

### Google Analytics 4
```php
// Customizer → Интеграции → Google Analytics
// Введите Measurement ID (G-XXXXXXXXXX)
```

### Яндекс.Метрика
```php
// Customizer → Интеграции → Яндекс.Метрика
// Введите ID счётчика
```

## Проверка производительности

### 1. Google PageSpeed Insights
```
https://pagespeed.web.dev/
```

### 2. GTmetrix
```
https://gtmetrix.com/
```

### 3. WebPageTest
```
https://www.webpagetest.org/
```

### Целевые показатели
- PageSpeed Score: > 90
- LCP: < 2.5s
- FID: < 100ms
- CLS: < 0.1

## Резервное копирование

### Автоматическое (UpdraftPlus)
1. Установите плагин UpdraftPlus
2. Настройте расписание (ежедневно)
3. Подключите облачное хранилище (Google Drive, Dropbox)

### Ручное
```bash
# Бэкап файлов
tar -czf atk-ved-backup-$(date +%Y%m%d).tar.gz wp-content/themes/atk-ved

# Бэкап базы данных
mysqldump -u username -p database_name > backup-$(date +%Y%m%d).sql
```

## Обновление темы

### Через Git
```bash
cd wp-content/themes/atk-ved
git pull origin main
composer install --no-dev --optimize-autoloader
```

### Ручное
1. Скачайте новую версию
2. Сделайте резервную копию текущей темы
3. Замените файлы темы
4. Проверьте работоспособность

## Устранение неполадок

### Белый экран (WSOD)
1. Включите WP_DEBUG в wp-config.php
2. Проверьте логи ошибок (wp-content/debug.log)
3. Проверьте версию PHP (должна быть 8.1+)

### Медленная загрузка
1. Проверьте кэширование (.htaccess)
2. Оптимизируйте изображения
3. Включите CDN
4. Проверьте запросы к БД

### Проблемы с формами
1. Проверьте настройки SMTP
2. Проверьте логи ошибок
3. Отключите антиспам плагины временно

## Поддержка

### Документация
- [OPTIMIZATION.md](OPTIMIZATION.md) - оптимизация
- [TODO.md](TODO.md) - планы развития
- [CHANGELOG.md](CHANGELOG.md) - история изменений

### Контакты
- Email: support@atk-ved.ru
- Telegram: @atk_ved_support
- GitHub Issues: <repository-url>/issues

## Лицензия

GPL-2.0-or-later
