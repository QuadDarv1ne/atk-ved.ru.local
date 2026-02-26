# 🔍 SEO Руководство

## Обзор

Тема АТК ВЭД включает полную SEO оптимизацию с поддержкой:

- **Schema.org** JSON-LD разметка
- **Open Graph** для социальных сетей
- **Twitter Cards**
- **Canonical URLs**
- **Meta tags**

---

## ✅ Реализованные функции

### 1. Schema.org Разметка

#### Organization Schema

Размечается на всех страницах:

```json
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "АТК ВЭД",
  "url": "https://atk-ved.ru",
  "logo": "...",
  "foundingDate": "2018",
  "description": "...",
  "sameAs": ["..."],
  "contactPoint": {...},
  "address": {...}
}
```

#### BreadcrumbList Schema

Автоматически генерируется для всех страниц кроме главной:

```json
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Главная",
      "item": "https://atk-ved.ru/"
    },
    ...
  ]
}
```

#### Service Schema

Для главной страницы:

```json
{
  "@context": "https://schema.org",
  "@type": "Service",
  "name": "Поставки товаров из Китая для маркетплейсов",
  "provider": {...},
  "areaServed": {"@type": "Country", "name": "Россия"},
  "serviceType": "Логистика и ВЭД"
}
```

#### FAQPage Schema

Генерируется из ACF полей:

```json
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "Вопрос?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Ответ"
      }
    }
  ]
}
```

#### AggregateRating Schema

Для отзывов:

```json
{
  "@context": "https://schema.org",
  "@type": "AggregateRating",
  "itemReviewed": {...},
  "ratingValue": "4.9",
  "reviewCount": "150"
}
```

---

### 2. Open Graph

Автоматически генерируемые теги:

```html
<meta property="og:locale" content="ru_RU">
<meta property="og:type" content="website">
<meta property="og:title" content="...">
<meta property="og:description" content="...">
<meta property="og:url" content="...">
<meta property="og:site_name" content="АТК ВЭД">
<meta property="og:image" content="...">
<meta property="og:image:alt" content="...">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
```

**Для статей:**
```html
<meta property="article:published_time" content="2026-02-26">
<meta property="article:modified_time" content="2026-02-26">
```

---

### 3. Twitter Cards

```html
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="...">
<meta name="twitter:description" content="...">
<meta name="twitter:image" content="...">
<meta name="twitter:image:alt" content="...">
<meta name="twitter:site" content="@username">
```

---

### 4. Meta Tags

```html
<meta name="robots" content="index, follow">
<meta name="description" content="...">
<meta name="author" content="...">
<meta name="theme-color" content="#e31e24">
<meta name="msapplication-TileColor" content="#e31e24">
```

---

### 5. Canonical URL

```html
<link rel="canonical" href="https://atk-ved.ru/current-page">
```

---

## 🎯 Интеграция с Yoast SEO

Если установлен Yoast SEO, тема автоматически использует его данные:

| Данные | Метод Yoast |
|--------|-------------|
| Title | `YoastSEO()->meta->for_current_page()->title` |
| Description | `YoastSEO()->meta->for_current_page()->description` |
| OG Title | `YoastSEO()->meta->for_current_page()->open_graph_title` |
| OG Image | `YoastSEO()->meta->for_current_page()->open_graph_image` |
| Canonical | `YoastSEO()->meta->for_current_page()->canonical` |

---

## 📊 Проверка разметки

### Google Rich Results Test

```
https://search.google.com/test/rich-results
```

Проверяет:
- Organization
- BreadcrumbList
- FAQPage
- AggregateRating
- Product

### Schema.org Validator

```
https://validator.schema.org/
```

Полная валидация JSON-LD разметки.

### Facebook Debugger

```
https://developers.facebook.com/tools/debug/
```

Проверка Open Graph разметки.

### Twitter Card Validator

```
https://cards-dev.twitter.com/validator
```

---

## 🔧 Настройка

### В Customizer

**Внешний вид → Настроить → SEO:**

| Опция | Описание |
|-------|----------|
| **Meta Description** | Описание сайта по умолчанию |
| **OG Image** | Изображение для соцсетей |
| **Twitter Username** | @username для Twitter Cards |

### Через ACF

**Настройки темы → FAQ:**

1. Добавьте вопросы и ответы
2. Автоматически появится FAQPage schema

**Настройки темы → Reviews:**

1. Добавьте отзывы с рейтингом
2. Автоматически появится AggregateRating schema

---

## 📈 Ожидаемые улучшения

| Метрика | До | После | Улучшение |
|---------|-----|-------|-----------|
| **Rich Results** | 0 | 5+ типов | +100% |
| **CTR из поиска** | 2% | 3.5% | +75% |
| **Social Shares** | Низкие | Высокие | +50% |
| **Видимость** | Обычная | Расширенная | +40% |

---

## 🎯 Rich Results в Google

### Поддерживаемые типы

| Тип | Статус | Где показывается |
|-----|--------|------------------|
| Organization | ✅ | Knowledge Panel |
| Breadcrumb | ✅ | Хлебные крошки в поиске |
| FAQ | ✅ | Развёрнутые ответы |
| AggregateRating | ✅ | Звёзды в поиске |
| Product | ✅ | Карточки товаров |
| Article | ✅ | Карточки статей |

---

## 🛠️ Troubleshooting

### Разметка не отображается

1. Проверьте кэш — очистите его
2. Проверьте валидатором
3. Убедитесь, что JSON-LD выводится в `<head>`

### Неправильные OG данные

1. Проверьте Yoast SEO настройки
2. Очистите кэш Facebook/Twitter
3. Используйте Facebook Debugger

### Отсутствуют FAQ в поиске

1. Проверьте ACF поля
2. Убедитесь, что FAQ на главной
3. Подождите переиндексации

---

## 📚 Ресурсы

### Документация

- [Schema.org](https://schema.org/docs/gs.html)
- [Google Structured Data](https://developers.google.com/search/docs/appearance/structured-data)
- [Open Graph Protocol](https://ogp.me/)
- [Twitter Cards](https://developer.twitter.com/en/docs/twitter-for-websites/cards/overview)

### Инструменты

- [Rich Results Test](https://search.google.com/test/rich-results)
- [Schema Validator](https://validator.schema.org/)
- [Facebook Debugger](https://developers.facebook.com/tools/debug/)
- [Twitter Card Validator](https://cards-dev.twitter.com/validator)
- [Lighthouse](https://developers.google.com/web/tools/lighthouse)

---

## ✅ Чек-лист перед релизом

- [ ] Organization schema отображается
- [ ] BreadcrumbList работает на всех страницах
- [ ] FAQPage валиден
- [ ] AggregateRating показывает рейтинг
- [ ] Open Graph корректен для всех страниц
- [ ] Twitter Cards валидны
- [ ] Canonical URL установлен
- [ ] Meta description уникален для каждой страницы
- [ ] Yoast SEO интегрирован (если используется)
- [ ] Rich Results Test проходит без ошибок

---

**Версия:** 3.2.0  
**Последнее обновление:** Февраль 2026  
**Соответствие:** Google Structured Data Guidelines
