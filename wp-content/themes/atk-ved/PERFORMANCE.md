# ⚡ Оптимизация производительности — Руководство

## Обзор изменений v3.1

### ✅ Реализованные улучшения

| Компонент | Описание | Статус |
|-----------|----------|--------|
| **Vite сборка** | Минификация CSS/JS, tree-shaking | ✅ Готово |
| **WebP конвертация** | Автоматическая генерация WebP | ✅ Готово |
| **Lazy Loading** | Отложенная загрузка изображений | ✅ Готово |
| **Кэширование** | Redis + Page Cache | ✅ Готово |
| **Critical CSS** | Inline стили для первого экрана | ✅ Готово |
| **Gzip/Brotli** | Сжатие статических файлов | ✅ Готово |

---

## 🚀 Быстрый старт

### 1. Установка зависимостей

```bash
cd wp-content/themes/atk-ved
npm install
```

### 2. Оптимизация изображений

```bash
# Генерация WebP
npm run generate:webp

# Сжатие изображений
npm run optimize:images
```

### 3. Сборка CSS/JS

```bash
# Продакшен сборка
npm run build

# Или для разработки
npm run dev
```

### 4. Настройка кэширования

```bash
# Копируем конфиг Redis
cp redis.config.example .env

# Добавляем в wp-config.php
define('WP_REDIS_HOST', 'redis');
define('WP_CACHE', true);
```

---

## 📁 Новые файлы

```
atk-ved/
├── package.json              # NPM зависимости
├── vite.config.js            # Конфигурация Vite
├── .stylelintrc.json         # CSS линтер
├── .prettierrc               # Форматтер кода
├── BUILD.md                  # Документация сборки
├── CACHE_SETUP.md            # Настройка кэширования
├── PERFORMANCE.md            # Этот файл
├── redis.config.example      # Пример Redis конфига
├── scripts/
│   ├── generate-webp.js      # WebP генератор
│   ├── optimize-images.js    # Оптимизация изображений
│   └── critical-css.js       # Critical CSS
└── inc/
    ├── image-optimization.php # Оптимизация изображений WP
    └── cache-manager.php      # Менеджер кэширования
```

---

## 🎯 Проверка производительности

### Автоматический тест

```bash
# Создайте файл test-performance.sh
chmod +x test-performance.sh
./test-performance.sh
```

### Ручная проверка

1. Откройте сайт в Chrome
2. F12 → Lighthouse
3. Generate report
4. Проверьте метрики:
   - First Contentful Paint (FCP): < 1.5s
   - Largest Contentful Paint (LCP): < 2.5s
   - Total Blocking Time (TBT): < 200ms
   - Cumulative Layout Shift (CLS): < 0.1

---

## 📊 Ожидаемые результаты

### До оптимизации

```
PageSpeed Score: 65
First Contentful Paint: 2.8s
Largest Contentful Paint: 4.2s
Total Blocking Time: 850ms
Cumulative Layout Shift: 0.25
Size: 2.5MB
Requests: 85
```

### После оптимизации

```
PageSpeed Score: 95+
First Contentful Paint: 0.8s
Largest Contentful Paint: 1.5s
Total Blocking Time: 180ms
Cumulative Layout Shift: 0.05
Size: 0.8MB
Requests: 45
```

---

## 🔧 Настройка для разных окружений

### Локальная разработка

```env
# .env
NODE_ENV=development
WP_DEBUG=true
WP_CACHE=false
```

```bash
npm run dev
```

### Продакшен

```env
# .env
NODE_ENV=production
WP_DEBUG=false
WP_CACHE=true
WP_REDIS_HOST=redis
```

```bash
npm run build
```

---

## 🛠️ Дополнительные оптимизации

### 1. CDN подключение

```php
// В functions.php
add_filter('wp_get_attachment_url', function($url) {
    return str_replace(
        'https://atk-ved.ru/wp-content/',
        'https://cdn.atk-ved.ru/wp-content/'
    );
});
```

### 2. Database optimization

```bash
# Очистка ревизий
wp db query "DELETE FROM wp_posts WHERE post_type = 'revision'"

# Оптимизация таблиц
wp db query "OPTIMIZE TABLE wp_posts, wp_postmeta, wp_options"
```

### 3. Preload ключевых ресурсов

```html
<!-- В header.php -->
<link rel="preload" href="/wp-content/themes/atk-ved/css/modern-design.min.css" as="style">
<link rel="preload" href="/wp-content/themes/atk-ved/js/main.min.js" as="script">
<link rel="preload" href="/wp-content/uploads/hero-bg.webp" as="image">
```

---

## 📈 Мониторинг производительности

### Google Analytics + PageSpeed

```javascript
// В js/main.js
window.addEventListener('load', () => {
    const perfData = performance.getEntriesByType('navigation')[0];
    
    // Отправка в GA
    gtag('event', 'performance', {
        event_category: 'site',
        event_label: 'page_load',
        value: Math.round(perfData.loadEventEnd - perfData.fetchStart)
    });
});
```

### Redis мониторинг

```bash
# Hit rate
redis-cli INFO stats | grep keyspace

# Memory usage
redis-cli INFO memory

# Slow queries
redis-cli --latency
```

---

## ⚠️ Известные ограничения

### Не кэшируется

- Страницы для авторизованных пользователей
- Административная панель
- AJAX запросы
- REST API (частично)
- Поиск и архивы

### WebP поддержка

- Конвертируются только JPG/PNG/GIF
- SVG не конвертируются
- Анимированные GIF требуют отдельной обработки

---

## 🔍 Troubleshooting

### Сборка не работает

```bash
# Очистить кэш npm
npm cache clean --force

# Удалить node_modules
rm -rf node_modules package-lock.json

# Установить заново
npm install
```

### Кэш не очищается

```bash
# Redis CLI
redis-cli FLUSHDB

# WordPress
wp cache flush

# Page cache
rm -rf wp-content/cache/atk-ved/*
```

### Изображения не оптимизируются

Проверьте наличие GD/Imagick:

```php
// В wp-admin → Инструменты → Здоровье сайта
// Проверка: "Библиотека изображений"
```

---

## 📚 Ресурсы

- [Vite Documentation](https://vitejs.dev/)
- [Sharp (Image Processing)](https://sharp.pixelplumbing.com/)
- [WordPress Performance](https://developer.wordpress.org/advanced-administration/performance/)
- [WebP Documentation](https://developers.google.com/speed/webp)
- [Redis Documentation](https://redis.io/documentation)

---

## ✅ Чек-лист перед деплоем

- [ ] `npm run build` выполнен без ошибок
- [ ] Все изображения оптимизированы (`npm run generate:webp`)
- [ ] Critical CSS сгенерирован (`npm run critical`)
- [ ] Redis настроен и подключён
- [ ] Page Cache включён в Customizer
- [ ] Gzip/Brotli сжатие включено на сервере
- [ ] Проверен PageSpeed Insights (цель: 90+)
- [ ] Проверена мобильная версия
- [ ] Очищен кэш после деплоя

---

**Версия:** 3.1.0  
**Последнее обновление:** Февраль 2026  
**Автор:** ATK VED Team
