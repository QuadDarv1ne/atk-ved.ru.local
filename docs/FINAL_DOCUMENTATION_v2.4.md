# Финальная документация проекта АТК ВЭД v2.4

## 📋 Обзор

Полная документация по всем компонентам и функциям темы АТК ВЭД.

---

## 🎯 Быстрый старт

### Требования

- **WordPress** 5.0+
- **PHP** 7.4+ (рекомендуется 8.1+)
- **MySQL** 5.7+ или MariaDB 10.3+
- **ACF** 5.8+ (Pro рекомендуется)

### Установка

1. Скопируйте тему в `wp-content/themes/atk-ved/`
2. Активируйте в админке WordPress
3. Установите рекомендуемые плагины (ACF, Contact Form 7)
4. Настройте через **Внешний вид → Настроить**

---

## 📁 Структура проекта

```
atk-ved/
├── inc/                        # PHP файлы темы
│   ├── acf-*.php              # ACF интеграция
│   ├── analytics.php           # Аналитика
│   ├── breadcrumbs.php         # Навигация
│   ├── calculator.php          # Калькулятор
│   ├── cookie-banner.php       # Cookie banner
│   ├── logger.php              # Логирование
│   ├── pwa.php                 # PWA
│   ├── recaptcha.php           # reCAPTCHA
│   ├── security.php            # Безопасность
│   ├── seo.php                 # SEO
│   ├── shipment-tracking.php   # Отслеживание
│   ├── sitemap.php             # Sitemap
│   ├── translations.php        # Переводы
│   └── ui-components.php       # UI компоненты
│
├── css/                        # Стили
│   ├── modern-design.css       # Дизайн-система
│   ├── animations-enhanced.css # Анимации
│   ├── ui-components.css       # UI компоненты
│   ├── additional-components.css # Доп. компоненты
│   └── blocks/                 # Стили блоков
│
├── js/                         # Скрипты
│   ├── main.js                 # Основные функции
│   ├── ui-components.js        # UI компоненты
│   ├── additional-components.js # Доп. компоненты
│   └── blocks/                 # Скрипты блоков
│
├── blocks/                     # Gutenberg блоки
│   └── index.js                # Регистрация блоков
│
├── template-parts/blocks/      # Шаблоны блоков
│   ├── hero/
│   ├── services/
│   ├── stats/
│   └── ...
│
├── tests/                      # Тесты
│   ├── php/                    # PHPUnit тесты
│   └── js/                     # QUnit тесты
│
├── docs/                       # Документация
├── docker/                     # Docker окружение
├── .github/workflows/          # CI/CD
└── wp-config.php               # Конфигурация
```

---

## 🎨 Компоненты

### UI Компоненты

| Компонент | Файл | Документация |
|-----------|------|--------------|
| Modal | `ui-components.php` | `docs/UI_COMPONENTS.md` |
| Tabs | `ui-components.php` | `docs/UI_COMPONENTS.md` |
| Accordion | `ui-components.php` | `docs/UI_COMPONENTS.md` |
| Alert | `additional-components.css` | `docs/ADDITIONAL_COMPONENTS.md` |
| Toast | `additional-components.js` | `docs/ADDITIONAL_COMPONENTS.md` |
| Progress | `additional-components.css` | `docs/ADDITIONAL_COMPONENTS.md` |
| Skeleton | `additional-components.css` | `docs/ADDITIONAL_COMPONENTS.md` |
| Badge | `additional-components.css` | `docs/ADDITIONAL_COMPONENTS.md` |

### Функциональные компоненты

| Компонент | Файл | Описание |
|-----------|------|----------|
| Калькулятор | `calculator.php` | Расчёт доставки |
| Отслеживание | `shipment-tracking.php` | Трекинг грузов |
| Breadcrumbs | `breadcrumbs.php` | Навигация |
| Sitemap | `sitemap.php` | XML карта сайта |
| Cookie Banner | `cookie-banner.php` | 152-ФЗ |
| PWA | `pwa.php` | Progressive Web App |

### Безопасность

| Компонент | Файл | Описание |
|-----------|------|----------|
| Honeypot | `security.php` | Защита от спама |
| reCAPTCHA | `recaptcha.php` | Google reCAPTCHA v3 |
| Security Headers | `.htaccess` | HTTP заголовки |
| Logger | `logger.php` | Логирование событий |

---

## 🔧 ACF Интеграция

### Настройки темы

**Админка → Настройки темы → Настройки темы АТК ВЭД**

| Вкладка | Поля |
|---------|------|
| Главный экран | hero_title, hero_subtitle, hero_features, hero_image, hero_stats |
| Контакты | header_phone, header_email, header_working_hours |
| Соцсети | social_networks (repeater) |
| SEO | seo_title, seo_description, seo_keywords |

### Custom Post Types

| CPT | Поля | Описание |
|-----|------|----------|
| Service | icon, number, short_desc, features | Услуги |
| FAQ | icon, category, is_active | Вопросы |
| Team | position, photo, social | Команда |
| Partner | logo, url, is_featured | Партнёры |

### Gutenberg блоки

| Блок | Шаблон | Описание |
|------|--------|----------|
| Hero | `template-parts/blocks/hero/` | Главный экран |
| Services | `template-parts/blocks/services/` | Услуги |
| Stats | `template-parts/blocks/stats/` | Статистика |
| Testimonials | `template-parts/blocks/testimonials/` | Отзывы |
| CTA | `template-parts/blocks/cta/` | Призыв |
| Team | `template-parts/blocks/team/` | Команда |
| Partners | `template-parts/blocks/partners/` | Партнёры |
| FAQ | `template-parts/blocks/faq/` | Вопросы |

---

## 📊 Helper функции

### ACF функции

```php
// Получить значение
atk_ved_get_field('field_name', 'default');
atk_ved_get_option('field_name', 'default');

// Вывести значение
atk_ved_the_field('field_name');
atk_ved_the_field_html('field_name');

// Получить изображение
atk_ved_get_image('hero_image', 'full');
atk_ved_the_image('hero_image', 'medium');

// Получить repeater
atk_ved_get_repeater('hero_features');
atk_ved_have_rows('hero_features');

// Готовые функции
atk_ved_get_theme_settings();
atk_ved_get_contacts();
atk_ved_get_social();
atk_ved_get_seo();
```

### UI компоненты (JavaScript)

```javascript
// Modal
atkOpenModal('modal-id');
atkCloseModal('modal-id');

// Tabs
atkActivateTab('#tabs', 0);

// Accordion
atkToggleAccordion('#accordion', 0, true);
atkExpandAllAccordions('#accordion');
atkCollapseAllAccordions('#accordion');

// Toast
atkShowToast({ type: 'success', message: 'Готово!' });
atkCloseAllToasts();

// Alert
atkShowAlert({ type: 'error', message: 'Ошибка!' });

// Progress
atkSetProgress('#progress', 75, 100);
atkIncrementProgress('#progress', 10);
atkResetProgress('#progress');

// Skeleton
atkShowSkeleton('#container', { lines: 3 });
atkHideSkeleton('#container');
```

---

## 🧪 Тестирование

### PHPUnit

```bash
# Запуск тестов
vendor/bin/phpunit

# С coverage
vendor/bin/phpunit --coverage-html ./coverage
```

### QUnit

```bash
# Запуск тестов
npm test
```

### CI/CD

Автоматические тесты при push в GitHub:
- PHPUnit тесты (PHP 7.4-8.2)
- PHPCS проверка
- QUnit тесты
- Сборка темы

---

## 📚 Документация

| Файл | Описание |
|------|----------|
| `docs/DESIGN_SYSTEM.md` | Дизайн-система v2.0 |
| `docs/UI_COMPONENTS.md` | UI компоненты v2.1 |
| `docs/ADDITIONAL_COMPONENTS.md` | Доп. компоненты v2.2 |
| `docs/ACF_INTEGRATION.md` | ACF интеграция v2.3 |
| `docs/IMPROVEMENTS.md` | Улучшения v1.7 |
| `docs/IMPROVEMENTS_V1.8.md` | Улучшения v1.8 |
| `docs/IMPROVEMENTS_V1.9.md` | Улучшения v1.9 |
| `CHANGELOG.md` | История изменений |
| `CONTRIBUTING.md` | Для участников |

---

## 🚀 Развёртывание

### Локальная разработка (Docker)

```bash
cd docker
docker-compose up -d
```

Доступ:
- Сайт: http://localhost:8080
- phpMyAdmin: http://localhost:8081

### Продакшен

1. Настройте `wp-config.php`
2. Сгенерируйте новые соли
3. Включите HTTPS
4. Настройте кэширование
5. Отправьте sitemap в поисковики

---

## 🎯 Best Practices

### Безопасность

```php
// ✅ Всегда экранируйте вывод
echo esc_html($value);
echo esc_url($url);
echo wp_kses_post($html);

// ✅ Проверяйте nonce
if (!wp_verify_nonce($nonce, 'action_name')) {
    wp_die('Security check failed');
}

// ✅ Проверяйте capabilities
if (!current_user_can('edit_posts')) {
    wp_die('Access denied');
}
```

### Производительность

```php
// ✅ Используйте кэширование
$data = wp_cache_get('key');
if (!$data) {
    $data = expensive_operation();
    wp_cache_set('key', $data);
}

// ✅ Оптимизируйте запросы
$query = new WP_Query(array(
    'posts_per_page' => 10,
    'no_found_rows' => true,
    'update_post_meta_cache' => false,
));
```

### Доступность

```html
<!-- ✅ Используйте ARIA -->
<button aria-label="Закрыть">×</button>
<nav aria-label="Главное меню">
<div role="alert">Важное сообщение</div>

<!-- ✅ Keyboard navigation -->
<button tabindex="0">Focusable</button>
```

---

## 📊 Метрики проекта

| Метрика | Значение |
|---------|----------|
| **Файлов** | 40+ |
| **Строк кода** | 15000+ |
| **Функций PHP** | 80+ |
| **Функций JS** | 50+ |
| **Gutenberg блоков** | 15+ |
| **UI компонентов** | 25+ |
| **ACF полей** | 15+ |
| **Тестов** | 30+ |

---

## 🆘 Поддержка

### Логи

```bash
# PHP ошибки
wp-content/debug.log

# Логи темы
wp-content/logs/atk-ved-*.log

# Security логи
wp-content/security.log
```

### Отладка

```php
// В wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

---

## 📈 Roadmap

### v2.5.0 (Планируется)
- [ ] Мультиязычность (WPML)
- [ ] REST API endpoints
- [ ] Онлайн-чат виджет
- [ ] Интеграция с CRM

### v3.0.0 (Планируется)
- [ ] Полная поддержка Gutenberg
- [ ] Headless CMS режим
- [ ] GraphQL API
- [ ] Vue.js компоненты

---

**Версия:** 2.4.0  
**Последнее обновление:** 25 февраля 2026  
**Лицензия:** GPL v2+
