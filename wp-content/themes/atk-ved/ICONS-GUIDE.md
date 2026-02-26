# Руководство по иконкам

## Bootstrap Icons подключены

В `Enqueue.php` подключены Bootstrap Icons:
```php
wp_enqueue_style( 'bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css', [], '1.11.3' );
```

## Использование

### В HTML/PHP
```html
<i class="bi bi-trophy"></i> Текст
<i class="bi bi-box-seam"></i> Текст
<i class="bi bi-truck"></i> Текст
```

### Популярные иконки для проекта

**Логистика:**
- `bi-truck` - грузовик
- `bi-airplane` - самолёт
- `bi-ship` - корабль
- `bi-train-freight` - поезд
- `bi-box-seam` - коробка
- `bi-boxes` - коробки

**Бизнес:**
- `bi-trophy` - трофей/награда
- `bi-cash-coin` - деньги
- `bi-graph-up` - рост
- `bi-people` - люди
- `bi-building` - здание
- `bi-shop` - магазин

**Действия:**
- `bi-search` - поиск
- `bi-check-circle` - галочка
- `bi-x-circle` - крестик
- `bi-arrow-right` - стрелка вправо
- `bi-download` - скачать
- `bi-upload` - загрузить

**Контакты:**
- `bi-telephone` - телефон
- `bi-envelope` - email
- `bi-geo-alt` - местоположение
- `bi-whatsapp` - WhatsApp
- `bi-telegram` - Telegram

**Интерфейс:**
- `bi-list` - меню
- `bi-x` - закрыть
- `bi-chevron-down` - вниз
- `bi-chevron-up` - вверх
- `bi-chevron-right` - вправо
- `bi-chevron-left` - влево

## Замена emoji на иконки

### front-page.php

**Было:**
```php
<span class="badge">🏆 5+ лет на рынке</span>
<span class="badge">💰 Цены от производителя</span>
<span class="badge">📦 От 1 кг без минималки</span>
```

**Стало:**
```php
<span class="badge"><i class="bi bi-trophy"></i> 5+ лет на рынке</span>
<span class="badge"><i class="bi bi-cash-coin"></i> Цены от производителя</span>
<span class="badge"><i class="bi bi-box-seam"></i> От 1 кг без минималки</span>
```

**Услуги:**
```php
['icon' => '<i class="bi bi-search"></i>'], // Поиск поставщиков
['icon' => '<i class="bi bi-check-circle"></i>'], // Контроль качества
['icon' => '<i class="bi bi-ship"></i>'], // Доставка
['icon' => '<i class="bi bi-file-text"></i>'], // Таможня
['icon' => '<i class="bi bi-building"></i>'], // Склад
['icon' => '<i class="bi bi-chat-dots"></i>'], // Выкуп товаров
```

## Стилизация иконок

### CSS для иконок в badge
```css
.badge i {
    margin-right: 6px;
    font-size: 1.1em;
}
```

### CSS для иконок в карточках
```css
.service-icon i {
    font-size: 32px;
    color: #e31e24;
}
```

### CSS для иконок в кнопках
```css
.btn i {
    margin-right: 8px;
}
```

## Размеры иконок

```css
/* Маленькие */
.bi-sm {
    font-size: 0.875rem;
}

/* Средние (по умолчанию) */
.bi {
    font-size: 1rem;
}

/* Большие */
.bi-lg {
    font-size: 1.5rem;
}

/* Очень большие */
.bi-xl {
    font-size: 2rem;
}
```

## Анимация иконок

```css
/* Вращение */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.bi-spin {
    animation: spin 2s linear infinite;
}

/* Пульсация */
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.bi-pulse {
    animation: pulse 2s ease-in-out infinite;
}
```

## Полный список иконок

https://icons.getbootstrap.com/

## Альтернативы

Если Bootstrap Icons не подходят, можно использовать:

1. **Font Awesome** (больше иконок)
```html
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<i class="fas fa-truck"></i>
```

2. **Material Icons** (Google)
```html
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<span class="material-icons">local_shipping</span>
```

3. **Feather Icons** (минималистичные)
```html
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<i data-feather="truck"></i>
```

## Рекомендации

1. Используйте иконки последовательно по всему сайту
2. Не смешивайте разные библиотеки иконок
3. Добавляйте `aria-hidden="true"` для декоративных иконок
4. Используйте `aria-label` для интерактивных иконок
5. Оптимизируйте размер - загружайте только нужные иконки

## Пример использования

```html
<!-- Декоративная иконка -->
<i class="bi bi-truck" aria-hidden="true"></i> Доставка

<!-- Интерактивная иконка -->
<button aria-label="Закрыть">
    <i class="bi bi-x" aria-hidden="true"></i>
</button>

<!-- Иконка с текстом -->
<a href="#" class="btn">
    <i class="bi bi-download"></i>
    <span>Скачать прайс</span>
</a>
```
