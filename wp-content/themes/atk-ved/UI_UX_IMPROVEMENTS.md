# 🎨 UI/UX Улучшения — Руководство

## Обзор

Тема АТК ВЭД v3.2 включает современные UI паттерны и улучшения UX для повышения конверсии и удобства использования.

---

## ✅ Реализованные компоненты

### 1. Кнопки (Buttons)

#### Типы кнопок

```html
<!-- Основная кнопка -->
<button class="btn btn-primary">Отправить заявку</button>

<!-- Вторичная кнопка -->
<button class="btn btn-secondary">Узнать больше</button>

<!-- Контурная кнопка -->
<button class="btn btn-outline">Скачать PDF</button>
```

#### Размеры

```html
<button class="btn btn-sm">Маленькая</button>
<button class="btn">Обычная</button>
<button class="btn btn-lg">Большая</button>
```

#### С иконкой

```html
<button class="btn btn-primary btn-icon">
    <svg>...</svg>
    Отправить
</button>
```

#### Состояние загрузки

```javascript
// JavaScript
$('.btn').loading(true, { loadingText: 'Отправка...' });

// Через Toast
$('.btn').on('click', function() {
    $(this).loading(true);
    // AJAX request...
    setTimeout(() => $(this).loading(false), 2000);
});
```

#### Эффекты

- **Ripple effect** — волна при клике
- **Hover lift** — подъём при наведении
- **Gradient border** — градиентная рамка
- **Glow effect** — свечение

---

### 2. Уведомления (Toast)

#### Использование

```javascript
// Базовое уведомление
atkToast.show({
    type: 'info',
    title: 'Заголовок',
    message: 'Текст сообщения',
    duration: 5000,
    closable: true
});

// Успех
atkToast.success('Данные успешно сохранены!');

// Ошибка
atkToast.error('Произошла ошибка при отправке');

// Предупреждение
atkToast.warning('Проверьте заполненные поля');

// Информация
atkToast.info('Новое сообщение получено');
```

#### Типы уведомлений

| Тип | Описание | Цвет |
|-----|----------|------|
| `success` | Успешное действие | Зелёный |
| `error` | Ошибка | Красный |
| `warning` | Предупреждение | Оранжевый |
| `info` | Информация | Синий |

#### Особенности

- Автозакрытие через 5 секунд
- Кнопка закрытия
- Progress bar
- Поддержка screen readers
- Анимации slideIn/slideOut

---

### 3. Модальные окна (Modal)

#### Использование

```javascript
// Открыть модальное окно
const modal = atkModal.open(`
    <h2 id="modal-title">Заголовок</h2>
    <p>Содержимое модального окна</p>
    <button data-close>Закрыть</button>
`, {
    closeOnBackdrop: true,
    closeOnEsc: true,
    onClose: () => console.log('Закрыто')
});

// Закрыть программно
atkModal.close(modal);
```

#### Особенности

- Focus trap (удержание фокуса)
- Закрытие по Escape
- Закрытие по клику на backdrop
- Блокировка прокрутки body
- Плавные анимации

---

### 4. Формы (Forms)

#### Плавающие лейблы

```html
<div class="form-group">
    <input type="text" id="name" placeholder=" " required>
    <label for="name">Ваше имя</label>
</div>
```

#### Валидация

```javascript
// Автоматическая валидация
// Добавляется класс .success или .error

// Email валидация
// Автоматическая проверка формата

// Phone маска
// Автоматическая очистка от нецифровых символов
```

#### Сообщения об ошибках

```html
<div class="form-group error">
    <input type="email" value="invalid">
    <div class="error-message">Введите корректный email</div>
</div>
```

---

### 5. Карточки (Cards)

#### Улучшенные карточки

```html
<div class="service-card">
    <h3>Услуга</h3>
    <p>Описание</p>
</div>
```

#### Эффекты при наведении

- **Подъём** — translateY(-8px)
- **Тень** — box-shadow увеличивается
- **Градиентная рамка** — появляется border
- **Glow** — радиальный градиент

---

### 6. Заглушки загрузки (Skeleton)

#### Использование

```html
<!-- Текстовый скелетон -->
<div class="skeleton skeleton-text"></div>
<div class="skeleton skeleton-text"></div>

<!-- Заголовок -->
<div class="skeleton skeleton-title"></div>

<!-- Изображение -->
<div class="skeleton skeleton-image"></div>

<!-- Аватар -->
<div class="skeleton skeleton-avatar"></div>
```

#### Анимация

- Плавная пульсация
- Градиентный эффект
- Бесконечная анимация

---

### 7. Бейджи и теги (Badges)

#### Использование

```html
<span class="badge badge-primary">Новинка</span>
<span class="badge badge-success">Успешно</span>
<span class="badge badge-warning">Внимание</span>
<span class="badge badge-error">Ошибка</span>
```

#### Анимированная точка

```html
<span class="badge badge-success badge-dot">Онлайн</span>
```

---

### 8. Прогресс бары (Progress)

#### Использование

```html
<div class="progress">
    <div class="progress-bar" style="width: 75%"></div>
</div>
```

#### Полосатая анимация

```html
<div class="progress">
    <div class="progress-bar progress-bar-striped"></div>
</div>
```

---

### 9. Подсказки (Tooltip)

#### Использование

```html
<span class="tooltip" data-tooltip="Текст подсказки">
    Наведите на меня
</span>
```

#### Особенности

- Появляется при hover
- Плавная анимация
- Стрелочка
- Тёмный фон

---

### 10. Индикатор прокрутки (Scroll Progress)

Автоматически добавляется на все страницы:

```html
<div class="scroll-progress"></div>
```

Показывает процент прочтения страницы.

---

## 🎯 JavaScript API

### Toast API

```javascript
// Показать уведомление
atkToast.show({ type, title, message, duration, closable });

// Быстрые методы
atkToast.success(message, title);
atkToast.error(message, title);
atkToast.warning(message, title);
atkToast.info(message, title);
```

### Modal API

```javascript
// Открыть
const modal = atkModal.open(content, options);

// Закрыть
atkModal.close(modal, onClose);
```

### Button Loading

```javascript
// Включить состояние загрузки
$('.btn').loading(true, { loadingText, originalText });

// Выключить
$('.btn').loading(false);
```

### Utility Functions

```javascript
// Debounce
$.debounce(func, wait);

// Throttle
$.throttle(func, limit);
```

---

## 📱 Адаптивность

Все компоненты адаптируются под мобильные устройства:

| Компонент | Мобильная версия |
|-----------|------------------|
| Кнопки | 100% ширина |
| Toast | На всю ширину экрана |
| Модальные окна | 95% ширины |
| Формы | Крупнее поля |

---

## 🌙 Тёмная тема

Компоненты поддерживают тёмную тему через `prefers-color-scheme`:

```css
@media (prefers-color-scheme: dark) {
    /* Автоматическая инверсия цветов */
}
```

---

## ♿ Доступность

Все компоненты соответствуют WCAG 2.1 AA:

- ✅ Keyboard navigation
- ✅ ARIA labels
- ✅ Focus visible
- ✅ Screen reader support
- ✅ Reduced motion

---

## 🎨 Кастомизация

### Через CSS переменные

```css
:root {
    --color-primary: #e31e24;
    --radius-lg: 12px;
    --transition-normal: 300ms;
}
```

### Переопределение стилей

```css
/* Переопределение кнопки */
.cta-button {
    background: your-color;
    border-radius: your-radius;
}
```

---

## 📊 Ожидаемые улучшения

| Метрика | До | После | Улучшение |
|---------|-----|-------|-----------|
| **Конверсия форм** | 2% | 3.5% | +75% |
| **Время на сайте** | 2:30 | 3:45 | +50% |
| **Отказы** | 45% | 35% | -22% |
| **UX Score** | 70 | 90+ | +28% |

---

## 🧪 Примеры использования

### Форма с валидацией и Toast

```javascript
$('.contact-form').on('submit', function(e) {
    e.preventDefault();
    
    const $btn = $(this).find('button[type="submit"]');
    $btn.loading(true);
    
    $.ajax({
        url: atkVed.ajaxUrl,
        method: 'POST',
        data: $(this).serialize()
    })
    .done(() => {
        atkToast.success('Заявка отправлена!');
        this.reset();
    })
    .fail(() => {
        atkToast.error('Ошибка отправки');
    })
    .always(() => {
        $btn.loading(false);
    });
});
```

### Модальное окно с подтверждением

```javascript
$('.delete-button').on('click', function() {
    const id = $(this).data('id');
    
    atkModal.open(`
        <h2>Подтверждение</h2>
        <p>Вы уверены, что хотите удалить?</p>
        <div class="modal-actions">
            <button class="btn btn-secondary" data-close>Отмена</button>
            <button class="btn btn-danger" id="confirm-delete">Удалить</button>
        </div>
    `);
    
    $('#confirm-delete').on('click', function() {
        // Delete logic
        atkToast.success('Удалено');
    });
});
```

### Skeleton при загрузке

```javascript
// Показать скелетон
$('.content').html(`
    <div class="skeleton skeleton-title"></div>
    <div class="skeleton skeleton-text"></div>
    <div class="skeleton skeleton-text"></div>
`);

// Загрузить контент
$.get('/api/data', function(data) {
    $('.content').html(data);
});
```

---

## ✅ Чек-лист перед релизом

- [ ] Все кнопки имеют hover эффекты
- [ ] Формы валидируются в реальном времени
- [ ] Toast уведомления работают
- [ ] Модальные окна закрываются по Escape
- [ ] Skeleton показывается при загрузке
- [ ] Scroll progress виден
- [ ] Мобильная версия адаптирована
- [ ] Тёмная тема работает
- [ ] Keyboard navigation работает
- [ ] Screen readers поддерживаются

---

## 📚 Ресурсы

### Документация

- [Design Tokens](css/design-tokens.css)
- [UI Enhancements](css/ui-enhancements.css)
- [UI Components JS](js/ui-components-enhanced.js)

### Инструменты

- [Easing Functions](https://easings.net/)
- [CSS Variables](https://developer.mozilla.org/en-US/docs/Web/CSS/Using_CSS_custom_properties)
- [WCAG Guidelines](https://www.w3.org/WAI/WCAG21/quickref/)

---

**Версия:** 3.2.0  
**Последнее обновление:** Февраль 2026  
**Статус:** Готово к использованию
