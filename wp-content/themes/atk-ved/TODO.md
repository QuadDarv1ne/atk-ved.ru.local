# TODO - Дальнейшие улучшения

## Высокий приоритет

### JavaScript
- [ ] Конвертировать calculator.js и calculator-frontend.js в vanilla JS (убрать jQuery)
- [ ] Конвертировать shipment-tracking.js в vanilla JS (убрать jQuery)
- [ ] Добавить code splitting для больших модулей
- [ ] Реализовать dynamic imports для редко используемых функций

### CSS
- [ ] Создать critical CSS для каждого типа страницы (home, page, single)
- [ ] Добавить CSS containment для изоляции стилей
- [ ] Использовать CSS custom properties для темизации
- [ ] Оптимизировать анимации (will-change, transform)

### Изображения
- [ ] Автоматическая генерация responsive images (srcset)
- [ ] Добавить blur placeholder для всех изображений
- [ ] Конвертация всех PNG в WebP/AVIF
- [ ] Lazy loading для background images

### Производительность
- [ ] Реализовать HTTP/2 Server Push для критических ресурсов
- [ ] Добавить Resource Hints (dns-prefetch, preconnect) для всех внешних доменов
- [ ] Оптимизировать database queries (добавить индексы)
- [ ] Реализовать fragment caching для тяжёлых секций

## Средний приоритет

### PWA
- [ ] Добавить push notifications
- [ ] Реализовать background sync для всех форм
- [ ] Добавить offline страницы для всех типов контента
- [ ] Создать app shell architecture

### SEO
- [ ] Добавить structured data (JSON-LD) для всех типов контента
- [ ] Реализовать автоматическую генерацию Open Graph изображений
- [ ] Добавить breadcrumbs на все страницы
- [ ] Оптимизировать meta descriptions

### Безопасность
- [ ] Добавить rate limiting для форм
- [ ] Реализовать honeypot для защиты от спама
- [ ] Добавить CAPTCHA для критических форм
- [ ] Настроить Content Security Policy (строгий режим)

### Доступность
- [ ] Добавить skip links для всех секций
- [ ] Реализовать keyboard navigation для всех интерактивных элементов
- [ ] Добавить ARIA labels для всех форм
- [ ] Протестировать с screen readers

## Низкий приоритет

### UX
- [ ] Добавить skeleton screens для загрузки контента
- [ ] Реализовать infinite scroll для блога
- [ ] Добавить фильтры и сортировку для каталога
- [ ] Создать интерактивный tour для новых пользователей

### Аналитика
- [ ] Настроить Google Analytics 4
- [ ] Добавить event tracking для всех кнопок
- [ ] Реализовать heatmaps (Hotjar/Yandex Metrika)
- [ ] Настроить A/B тестирование

### Интеграции
- [ ] Интеграция с CRM (AmoCRM, Bitrix24)
- [ ] Интеграция с мессенджерами (WhatsApp Business API, Telegram Bot)
- [ ] Интеграция с платёжными системами
- [ ] Интеграция с системами доставки

### Документация
- [ ] Создать style guide для компонентов
- [ ] Написать документацию для разработчиков
- [ ] Создать видео-туториалы для администраторов
- [ ] Добавить inline комментарии для сложных функций

## Технический долг

### Рефакторинг
- [ ] Разделить большие PHP файлы на меньшие модули
- [ ] Унифицировать naming conventions
- [ ] Добавить type hints для всех функций
- [ ] Реализовать dependency injection

### Тестирование
- [ ] Написать unit tests для критических функций
- [ ] Добавить integration tests для форм
- [ ] Настроить E2E тестирование (Playwright/Cypress)
- [ ] Добавить visual regression tests

### CI/CD
- [ ] Настроить автоматический деплой
- [ ] Добавить pre-commit hooks (linting, formatting)
- [ ] Настроить автоматическое тестирование
- [ ] Реализовать staging environment

## Мониторинг

### Метрики для отслеживания
- [ ] Core Web Vitals (LCP, FID, CLS)
- [ ] Time to First Byte (TTFB)
- [ ] First Contentful Paint (FCP)
- [ ] Time to Interactive (TTI)
- [ ] Total Blocking Time (TBT)
- [ ] Cumulative Layout Shift (CLS)

### Инструменты
- [ ] Google PageSpeed Insights
- [ ] Lighthouse CI
- [ ] WebPageTest
- [ ] GTmetrix
- [ ] Chrome User Experience Report

## Заметки

### Оптимизации, которые НЕ нужно делать
- ❌ Удаление всех комментариев из кода (ухудшает читаемость)
- ❌ Inline всех стилей (ухудшает кэширование)
- ❌ Объединение всех JS в один файл (ухудшает code splitting)
- ❌ Удаление всех пробелов из HTML (минимальная выгода)

### Приоритеты
1. Производительность (Core Web Vitals)
2. Безопасность (Security Headers, HTTPS)
3. Доступность (WCAG 2.1 AA)
4. SEO (Structured Data, Meta Tags)
5. UX (Интерактивность, Анимации)

### Целевые метрики
- LCP: < 2.5s
- FID: < 100ms
- CLS: < 0.1
- TTFB: < 600ms
- Lighthouse Score: > 90
