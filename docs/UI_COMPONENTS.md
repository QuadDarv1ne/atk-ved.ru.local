# UI Компоненты АТК ВЭД v2.1

## 📋 Обзор

Готовые UI компоненты с анимациями, доступностью и шорткодами для WordPress.

---

## 🪟 Модальные окна (Modal)

### HTML

```html
<!-- Кнопка открытия -->
<button data-modal-open="my-modal">Открыть модальное окно</button>

<!-- Модальное окно -->
<div id="my-modal" class="modal modal-center">
    <div class="modal-backdrop"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Заголовок</h3>
            <button class="modal-close" data-modal-close="my-modal">×</button>
        </div>
        <div class="modal-body">
            <p>Содержимое модального окна</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" data-modal-close="my-modal">Отмена</button>
            <button class="btn btn-primary">OK</button>
        </div>
    </div>
</div>
```

### Шорткод WordPress

```
[modal id="my-modal" 
         trigger="Открыть окно" 
         title="Заголовок" 
         size="md" 
         position="center"]
    Содержимое модального окна
[/modal]
```

### Параметры

| Параметр | Значения | Описание |
|----------|----------|----------|
| `id` | string | Уникальный ID модального окна |
| `trigger` | string | Текст кнопки открытия (необязательно) |
| `trigger_class` | string | Классы кнопки |
| `title` | string | Заголовок модального окна |
| `size` | sm, md, lg, xl, full | Размер |
| `position` | center, top, bottom, left, right | Позиция |
| `show_close` | 0, 1 | Показывать кнопку закрытия |
| `close_on_backdrop` | 0, 1 | Закрывать по клику на фон |
| `footer` | string | Содержимое подвала |

### JavaScript API

```javascript
// Открыть
atkOpenModal('my-modal');

// Закрыть
atkCloseModal('my-modal');
```

### Размеры

- **sm** - 400px (компактные уведомления)
- **md** - 500px (по умолчанию)
- **lg** - 800px (формы)
- **xl** - 1000px (галереи)
- **full** - 95% экрана (презентации)

---

## 📑 Табы (Tabs)

### HTML

```html
<div class="tabs" id="my-tabs">
    <div class="tabs-header">
        <button class="tab-button is-active" data-tab="tab-1">Таб 1</button>
        <button class="tab-button" data-tab="tab-2">Таб 2</button>
        <button class="tab-button" data-tab="tab-3">Таб 3</button>
    </div>
    <div class="tabs-content">
        <div class="tab-panel is-active" id="tab-1">
            Содержимое таба 1
        </div>
        <div class="tab-panel" id="tab-2">
            Содержимое таба 2
        </div>
        <div class="tab-panel" id="tab-3">
            Содержимое таба 3
        </div>
    </div>
</div>
```

### Шорткод WordPress

```
[tabs id="my-tabs" style="default" active="0"]
    [tab title="Таб 1" icon="📄"]Содержимое 1[/tab]
    [tab title="Таб 2" icon="📊"]Содержимое 2[/tab]
    [tab title="Таб 3" icon="⚙️"]Содержимое 3[/tab]
[/tabs]
```

### Параметры

| Параметр | Значения | Описание |
|----------|----------|----------|
| `id` | string | Уникальный ID табов |
| `style` | default, pill | Стиль оформления |
| `vertical` | 0, 1 | Вертикальное расположение |
| `active` | number | Индекс активного таба (0-based) |

### JavaScript API

```javascript
// Активировать по индексу
atkActivateTab('#my-tabs', 1);

// Активировать по ID
atkActivateTab('#my-tabs', '#tab-2');
```

### События

```javascript
jQuery('#my-tabs').on('tabChange', function(e, data) {
    console.log('Индекс:', data.index);
    console.log('ID панели:', data.panelId);
});
```

---

## 📎 Аккордеон (Accordion)

### HTML

```html
<div class="accordion" id="my-accordion">
    <div class="accordion-item">
        <button class="accordion-header">
            <span class="accordion-title">Вопрос 1</span>
            <span class="accordion-icon">
                <svg>...</svg>
            </span>
        </button>
        <div class="accordion-body">
            <div class="accordion-content">
                <p>Ответ 1</p>
            </div>
        </div>
    </div>
    
    <div class="accordion-item">
        <button class="accordion-header">
            <span class="accordion-title">Вопрос 2</span>
            <span class="accordion-icon">
                <svg>...</svg>
            </span>
        </button>
        <div class="accordion-body">
            <div class="accordion-content">
                <p>Ответ 2</p>
            </div>
        </div>
    </div>
</div>
```

### Шорткод WordPress

```
[accordion id="my-accordion" exclusive="0"]
    [accordion-item title="Вопрос 1" icon="1"]Ответ 1[/accordion-item]
    [accordion-item title="Вопрос 2" icon="1"]Ответ 2[/accordion-item]
    [accordion-item title="Вопрос 3" icon="1" active="1"]Ответ 3[/accordion-item]
[/accordion]
```

### Параметры

| Параметр | Значения | Описание |
|----------|----------|----------|
| `id` | string | Уникальный ID аккордеона |
| `exclusive` | 0, 1 | Только один открытый элемент |
| `class` | string | Дополнительные классы |
| `seamless` | 0, 1 | Бесшовный стиль (без границ) |

### JavaScript API

```javascript
// Раскрыть элемент
atkToggleAccordion('#my-accordion', 0, true);

// Свернуть элемент
atkToggleAccordion('#my-accordion', 0, false);

// Переключить
atkToggleAccordion('#my-accordion', 0);

// Раскрыть все
atkExpandAllAccordions('#my-accordion');

// Свернуть все
atkCollapseAllAccordions('#my-accordion');
```

### События

```javascript
jQuery('#my-accordion').on('accordionChange', function(e, data) {
    console.log('Индекс:', data.index);
    console.log('Состояние:', data.isActive);
});
```

---

## ❓ FAQ (Часто задаваемые вопросы)

### Шорткод WordPress

```
[faq id="faq-1"]
    [faq-item q="Какой минимальный объем заказа?" 
              a="Мы работаем с заказами от 100 кг."]
    [faq-item q="Сколько времени занимает доставка?" 
              a="Сроки зависят от способа: авиа 5-10 дней, море 35-45 дней."]
    [faq-item q="Как происходит оплата?" 
              a="70% предоплата, 30% после проверки товара."]
[/faq]
```

### Schema.org разметка

Автоматическая микроразметка `FAQPage` для SEO.

---

## 🎨 Стили

### Модальные окна

```css
/* Позиции */
.modal-center    /* По центру (по умолчанию) */
.modal-top       /* Сверху */
.modal-bottom    /* Снизу */
.modal-left      /* Слева (выезжает) */
.modal-right     /* Справа (выезжает) */

/* Размеры */
.modal-sm        /* 400px */
.modal-md        /* 500px */
.modal-lg        /* 800px */
.modal-xl        /* 1000px */
.modal-full      /* 95% экрана */
```

### Табы

```css
/* Стили */
.tabs            /* По умолчанию */
.tabs-pill       /* Закруглённые табы */
.tabs-vertical   /* Вертикальные */
```

### Аккордеоны

```css
/* Стили */
.accordion                /* По умолчанию */
.accordion-exclusive      /* Только один открытый */
.accordion-seamless       /* Без границ */
```

---

## ♿ Доступность

### ARIA атрибуты

```html
<!-- Модальное окно -->
<div role="dialog" aria-modal="true" aria-labelledby="modal-title">

<!-- Табы -->
<button role="tab" aria-controls="panel-1" aria-selected="true">
<div role="tabpanel" aria-labelledby="tab-1">

<!-- Аккордеон -->
<button aria-expanded="true" aria-controls="body-1">
<div aria-labelledby="header-1">
```

### Keyboard навигация

| Компонент | Клавиши |
|-----------|---------|
| Modal | Escape (закрыть) |
| Tabs | ← → ↑ ↓ (навигация), Home/End |
| Accordion | ↑ ↓ (навигация), Enter/Space (открыть), Home/End |

### Focus management

- Фокус перемещается в модальное окно при открытии
- Фокус возвращается на кнопку после закрытия
- Видимые focus-стили для клавиатурной навигации

---

## 📱 Адаптивность

### Мобильные стили

```css
@media (max-width: 768px) {
    /* Модальные окна на весь экран */
    .modal { padding: 0; }
    .modal-content { max-height: 95vh; }
    
    /* Вертикальные табы становятся горизонтальными */
    .tabs-vertical { grid-template-columns: 1fr; }
    
    /* Уменьшенные отступы в аккордеоне */
    .accordion-header { padding: 12px 16px; }
}
```

---

## 🎭 Анимации

### Модальные окна

```css
/* Появление */
.modal-content {
    transform: translateY(-50px) scale(0.9);
    transition: opacity 0.3s, transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
```

### Табы

```css
/* Плавное переключение */
.tab-panel {
    animation: fadeIn 0.3s ease;
}
```

### Аккордеон

```css
/* Плавное раскрытие */
.accordion-body {
    max-height: 0;
    transition: max-height 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}
```

---

## 🔧 Примеры использования

### Модальное окно с формой

```
[modal id="contact-modal" 
         trigger="Связаться с нами" 
         title="Обратная связь" 
         size="md"]
    [contact-form-7 id="1"]
    
    [button text="Отправить" class="btn btn-primary"]
    [button text="Отмена" class="btn btn-secondary" data-modal-close="contact-modal"]
[/modal]
```

### Табы с услугами

```
[tabs style="pill" active="0"]
    [tab title="📦 Доставка" icon="📦"]
        Описание услуги доставки
    [/tab]
    [tab title="🔍 Поиск" icon="🔍"]
        Описание услуги поиска
    [/tab]
    [tab title="📋 Таможня" icon="📋"]
        Описание таможенного оформления
    [/tab]
[/tabs]
```

### FAQ для SEO

```
[faq]
    [faq-item q="Работаете ли вы с юр. лицами?" 
              a="Да, работаем с ИП и ООО."]
    [faq-item q="Предоставляете документы?" 
              a="Да, полный пакет для бухгалтерии."]
[/faq]
```

---

## 🎯 Лучшие практики

### 1. Уникальные ID

```html
<!-- ✅ Хорошо -->
<div id="modal-contact-1">
<div id="modal-contact-2">

<!-- ❌ Плохо -->
<div id="modal-1">
<div id="modal-1">
```

### 2. Осмысленные заголовки

```html
<!-- ✅ Хорошо -->
<h3 class="modal-title">Оформление заказа</h3>

<!-- ❌ Плохо -->
<h3 class="modal-title">Заголовок</h3>
```

### 3. Закрытие модальных окон

```javascript
// Всегда закрывайте модальное окно после действия
$('#submitOrder').on('click', function() {
    // Обработка заказа
    atkCloseModal('order-modal');
    atkOpenModal('success-modal');
});
```

### 4. Активный таб по URL

```javascript
// Сохранение таба в URL
const hash = window.location.hash;
if (hash) {
    atkActivateTab('#tabs', hash);
}
```

---

## 📊 Сравнение компонентов

| Компонент | Для чего | Анимация | Доступность |
|-----------|----------|----------|-------------|
| Modal | Диалоги, формы | ✅ | ✅ ARIA |
| Tabs | Переключение контента | ✅ | ✅ ARIA + Keyboard |
| Accordion | FAQ, списки | ✅ | ✅ ARIA + Keyboard |

---

**Версия:** 2.1.0  
**Обновлено:** Февраль 2026
