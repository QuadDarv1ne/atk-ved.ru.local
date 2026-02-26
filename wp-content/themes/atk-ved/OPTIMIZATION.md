# Руководство по оптимизации

## Быстрый старт

### 1. Минификация CSS

```bash
# Запустить скрипт минификации
php scripts/minify-css.php
```

Минифицированные файлы будут сохранены в `css/min/`

### 2. Оптимизация изображений

```bash
# Сделать скрипт исполняемым
chmod +x scripts/optimize-images.sh

# Запустить оптимизацию
./scripts/optimize-images.sh
```

Требуется установка:
- `imagemagick` - для конвертации в WebP
- `optipng` - для оптимизации PNG
- `jpegoptim` - для оптимизации JPG

**Ubuntu/Debian:**
```bash
sudo apt-get install imagemagick optipng jpegoptim
```

**macOS:**
```bash
brew install imagemagick optipng jpegoptim
```

### 3. Генерация иконок

См. `images/icons/README.md` для инструкций по созданию всех размеров favicon и app icons.

Рекомендуется использовать:
- https://realfavicongenerator.net/
- https://favicon.io/

### 4. Настройка .htaccess

```bash
# Скопировать пример в корень сайта
cp .htaccess.example ../../.htaccess

# Или добавить правила в существующий .htaccess
cat .htaccess.example >> ../../.htaccess
```

## Автоматические оптимизации

Следующие оптимизации включены автоматически через `inc/performance.php`:

✅ Отключены emoji скрипты
✅ Отключены embeds
✅ Удалены query strings из статических ресурсов
✅ Отключен XML-RPC
✅ Удалены лишние meta теги
✅ Оптимизирован Heartbeat API
✅ Добавлены preconnect для внешних ресурсов
✅ Defer для не критичных скриптов
✅ Кэширование меню
✅ Минификация HTML (опционально)

## Оптимизация изображений

Автоматические функции через `inc/image-optimization.php`:

✅ Генерация WebP при загрузке
✅ Автоматический srcset и sizes
✅ Lazy loading для всех изображений
✅ Decoding="async"
✅ Оптимизация SVG
✅ Поддержка AVIF (PHP 8.1+)

### Использование в шаблонах

```php
// Picture element с WebP fallback
echo atk_ved_picture_element($attachment_id, 'full', ['class' => 'hero-image']);

// Получить WebP URL
$webp_url = atk_ved_get_webp_image($attachment_id, 'large');
```

## Critical CSS

Critical CSS для главной страницы находится в `css/critical-inline.css`

Он автоматически инлайнится через `Enqueue.php` для быстрой отрисовки above-the-fold контента.

### Обновление Critical CSS

1. Откройте главную страницу в браузере
2. Используйте Chrome DevTools Coverage
3. Скопируйте используемые стили
4. Минифицируйте и обновите `critical-inline.css`

Или используйте онлайн-инструменты:
- https://www.sitelocity.com/critical-path-css-generator
- https://jonassebastianohlsson.com/criticalpathcssgenerator/

## Проверка производительности

### Lighthouse

```bash
# Chrome DevTools > Lighthouse
# Или через CLI
npm install -g lighthouse
lighthouse https://your-site.com --view
```

Целевые показатели:
- Performance: > 90
- Accessibility: > 90
- Best Practices: > 90
- SEO: > 90

### PageSpeed Insights

https://pagespeed.web.dev/

### GTmetrix

https://gtmetrix.com/

### WebPageTest

https://www.webpagetest.org/

## Core Web Vitals

Целевые показатели:

- **LCP** (Largest Contentful Paint): < 2.5s
- **FID** (First Input Delay): < 100ms
- **CLS** (Cumulative Layout Shift): < 0.1

### Улучшение LCP

- ✅ Critical CSS инлайн
- ✅ Preconnect для внешних ресурсов
- ✅ Оптимизация изображений
- ✅ Browser caching
- ⚠️ Используйте CDN для статики
- ⚠️ Оптимизируйте сервер (PHP 8+, OPcache)

### Улучшение FID

- ✅ Defer для не критичных скриптов
- ✅ Минимум JavaScript на главной
- ✅ Vanilla JS вместо jQuery
- ⚠️ Code splitting для больших скриптов

### Улучшение CLS

- ✅ Width/height для изображений
- ✅ Резервирование места для динамического контента
- ✅ Preload для критичных шрифтов
- ⚠️ Избегайте вставки контента над существующим

## Кэширование

### Browser Cache

Настроено через `.htaccess`:
- Изображения, шрифты: 1 год
- CSS, JS: 1 месяц
- HTML: без кэша

### Server Cache

Рекомендуется установить плагин кэширования:
- WP Rocket (платный, лучший)
- W3 Total Cache (бесплатный)
- WP Super Cache (бесплатный, простой)

### Object Cache

Для высоконагруженных сайтов:
- Redis
- Memcached

## CDN

Рекомендуемые CDN:
- Cloudflare (бесплатный тариф)
- BunnyCDN (дешёвый)
- KeyCDN
- Amazon CloudFront

## База данных

### Автоматическая очистка

Настроена через `inc/performance.php`:
- Удаление старых ревизий (> 30 дней)
- Удаление автосохранений (> 7 дней)
- Оптимизация таблиц

### Ручная оптимизация

```sql
-- Удалить все ревизии
DELETE FROM wp_posts WHERE post_type = 'revision';

-- Удалить спам комментарии
DELETE FROM wp_comments WHERE comment_approved = 'spam';

-- Оптимизировать таблицы
OPTIMIZE TABLE wp_posts, wp_postmeta, wp_comments, wp_options;
```

Или используйте плагин:
- WP-Optimize
- Advanced Database Cleaner

## Мониторинг

### Uptime мониторинг

- UptimeRobot (бесплатный)
- Pingdom
- StatusCake

### Performance мониторинг

- Google Search Console
- New Relic
- Datadog

### Error tracking

- Sentry
- Rollbar
- Bugsnag

## Чек-лист перед запуском

- [ ] Минифицированы CSS файлы
- [ ] Оптимизированы все изображения
- [ ] Созданы WebP версии
- [ ] Сгенерированы все размеры иконок
- [ ] Настроен .htaccess
- [ ] Установлен плагин кэширования
- [ ] Настроен CDN (опционально)
- [ ] Проверен Lighthouse (> 90)
- [ ] Проверен PageSpeed Insights
- [ ] Настроен мониторинг
- [ ] Включен SSL/HTTPS
- [ ] Настроены Security Headers

## Дополнительные ресурсы

- [Web.dev - Performance](https://web.dev/performance/)
- [MDN - Web Performance](https://developer.mozilla.org/en-US/docs/Web/Performance)
- [Google PageSpeed Insights](https://pagespeed.web.dev/)
- [WebPageTest Documentation](https://docs.webpagetest.org/)
