# Улучшения дизайна АТК ВЭД v3.0.0

## 📋 Обзор

Версия 3.0.0 фокусируется на современном дизайне с продвинутыми анимациями, улучшенными UI компонентами и тёмной темой.

---

## 🎨 Новые CSS файлы

| Файл | Назначение | Размер |
|------|-----------|--------|
| `css/advanced-animations.css` | 50+ анимаций | ~600 строк |
| `css/modern-ui-components.css` | UI компоненты | ~500 строк |

---

## ✨ Анимации

### Категории анимаций

#### 1. Fade (Появление/Исчезновение)
```css
.animate-fade-in
.animate-fade-out
.animate-fade-in-up
.animate-fade-in-down
.animate-fade-in-left
.animate-fade-in-right
```

#### 2. Scale (Масштабирование)
```css
.animate-scale-in
.animate-scale-out
.animate-pulse
.animate-pulse-strong
```

#### 3. Slide (Скольжение)
```css
.animate-slide-in-up
.animate-slide-in-down
.animate-slide-in-left
.animate-slide-in-right
```

#### 4. Rotate (Вращение)
```css
.animate-spin
.animate-spin-reverse
.animate-wobble
.animate-swing
```

#### 5. Bounce (Прыжки)
```css
.animate-bounce
.animate-bounce-in
.animate-bounce-in-up
.animate-bounce-in-down
```

#### 6. Flip (Переворот)
```css
.animate-flip-in-x
.animate-flip-in-y
```

#### 7. Attention (Привлечение внимания)
```css
.animate-shake
.animate-head-shake
.animate-heartbeat
.animate-flash
.animate-glow
```

#### 8. Special (Специальные)
```css
.skeleton          — Загрузчик скелетон
.progress-indeterminate — Неопределённый прогресс
.ripple            — Эффект волны
.shimmer           — Мерцание
.typewriter        — Печатная машинка
.animate-float     — Парение
.gradient-animated — Анимированный градиент
.animate-glitch    — Помехи
```

### Переменные анимаций

```css
:root {
    /* Easing функции */
    --ease-smooth: cubic-bezier(0.4, 0, 0.2, 1);
    --ease-bounce: cubic-bezier(0.34, 1.56, 0.64, 1);
    --ease-elastic: cubic-bezier(0.68, -0.55, 0.265, 1.55);
    
    /* Длительности */
    --duration-fast: 200ms;
    --duration-normal: 300ms;
    --duration-slow: 500ms;
}
```

### Задержки анимаций

```html
<div class="animate-fade-in delay-100">...</div>
<div class="animate-fade-in delay-200">...</div>
<div class="animate-fade-in delay-300">...</div>
```

### Scroll-Triggered анимации

```html
<!-- Элемент появится при скролле -->
<div class="reveal">Контент</div>
<div class="reveal-left">Слева</div>
<div class="reveal-right">Справа</div>
<div class="reveal-scale">Масштаб</div>

<!-- С задержкой для списков -->
<div class="reveal stagger-1">Элемент 1</div>
<div class="reveal stagger-2">Элемент 2</div>
<div class="reveal stagger-3">Элемент 3</div>
```

---

## 🎯 UI Компоненты

### Кнопки

#### Modern Button
```html
<button class="btn-modern btn-primary-modern">
    Кнопка
</button>
```

#### Типы кнопок
```html
<!-- Основная -->
<button class="btn-modern btn-primary-modern">Primary</button>

<!-- Градиентная -->
<button class="btn-modern btn-gradient-modern">Gradient</button>

<!-- Стеклянная -->
<button class="btn-modern btn-glass-modern">Glass</button>

<!-- С иконкой -->
<button class="btn-modern btn-icon-modern">
    Текст
    <span class="btn-icon">→</span>
</button>

<!-- Загрузка -->
<button class="btn-modern btn-loading-modern">
    <span class="btn-text">Отправка...</span>
    <span class="btn-loader">
        <span></span><span></span><span></span>
    </span>
</button>
```

#### Размеры
```html
<button class="btn-modern btn-modern-sm">Маленькая</button>
<button class="btn-modern">Обычная</button>
<button class="btn-modern btn-modern-lg">Большая</button>
```

### Карточки

#### Modern Card
```html
<div class="card-modern">
    <div class="card-content-modern">
        <h3 class="card-title-modern">Заголовок</h3>
        <p class="card-description-modern">Описание</p>
    </div>
</div>
```

#### Карточка с изображением
```html
<div class="card-modern card-image-modern">
    <span class="card-badge-modern">New</span>
    <img src="image.jpg" alt="">
    <div class="card-content-modern">
        <h3 class="card-title-modern">Заголовок</h3>
        <p class="card-description-modern">Описание</p>
    </div>
</div>
```

#### Стеклянная карточка
```html
<div class="card-modern card-glass-modern">
    <div class="card-content-modern">
        Контент
    </div>
</div>
```

### Формы

#### Input с плавающим лейблом
```html
<div class="form-group-modern input-floating-modern">
    <input type="text" class="input-modern" placeholder=" " required>
    <label>Ваше имя</label>
</div>
```

#### Input с иконкой
```html
<div class="form-group-modern input-icon-modern">
    <input type="email" class="input-modern" placeholder="Email">
    <span class="input-icon">
        <svg>...</svg>
    </span>
</div>
```

#### Checkbox & Radio
```html
<label class="checkbox-modern">
    <input type="checkbox" name="agree">
    <span>Согласен с условиями</span>
</label>

<label class="radio-modern">
    <input type="radio" name="option" value="1">
    <span>Опция 1</span>
</label>
```

### Бейджи

```html
<span class="badge-modern badge-modern-primary">Primary</span>
<span class="badge-modern badge-modern-success">Success</span>
<span class="badge-modern badge-modern-warning">Warning</span>
<span class="badge-modern badge-modern-info">Info</span>
```

### Аватары

```html
<div class="avatar-modern avatar-modern-lg">
    <img src="avatar.jpg" alt="User">
    <span class="avatar-status online"></span>
</div>
```

**Статусы:**
- `.online` — зелёный
- `.offline` — серый
- `.busy` — красный

**Размеры:**
- `.avatar-modern-sm` — 35px
- `.avatar-modern` — 50px
- `.avatar-modern-lg` — 80px
- `.avatar-modern-xl` — 120px

### Alerts (Уведомления)

```html
<div class="alert-modern alert-modern-success">
    <span class="alert-icon-modern">✓</span>
    <div class="alert-content-modern">
        <div class="alert-title-modern">Успешно!</div>
        <div class="alert-message-modern">Данные сохранены</div>
    </div>
    <button class="alert-close-modern">×</button>
</div>
```

**Типы:**
- `.alert-modern-success` — зелёный
- `.alert-modern-warning` — оранжевый
- `.alert-modern-error` — красный
- `.alert-modern-info` — синий

### Progress Bar

```html
<div class="progress-modern">
    <div class="progress-fill-modern" style="width: 75%"></div>
</div>

<!-- С полосками -->
<div class="progress-modern progress-modern-striped">
    <div class="progress-fill-modern" style="width: 75%"></div>
</div>

<!-- Неопределённый -->
<div class="progress-modern progress-indeterminate">
    <div class="progress-fill-modern"></div>
</div>
```

### Tooltip

```html
<span class="tooltip-modern" data-tooltip="Подсказка">
    Наведите на меня
</span>
```

---

## 🌙 Тёмная тема

### Автоматическое переключение

```css
@media (prefers-color-scheme: dark) {
    :root {
        --color-white: #1a1a1a;
        --color-gray-50: #242424;
        /* ... другие цвета */
    }
}
```

### Ручное переключение

```html
<body class="dark-mode">
    <!-- Тёмная тема активна -->
</body>
```

```javascript
// Переключатель
document.body.classList.toggle('dark-mode');
```

### Поддержка компонентов

Все компоненты поддерживают тёмную тему:
- ✅ Карточки
- ✅ Формы
- ✅ Кнопки
- ✅ Alerts

---

## 📊 Easing Functions

### Названия и кривые

```css
/* Плавные */
--ease-smooth: cubic-bezier(0.4, 0, 0.2, 1);

/* Эффектные */
--ease-bounce: cubic-bezier(0.34, 1.56, 0.64, 1);
--ease-elastic: cubic-bezier(0.68, -0.55, 0.265, 1.55);
--ease-back: cubic-bezier(0.68, -0.6, 0.32, 1.6);
```

### Использование

```css
.element {
    transition: all 0.3s var(--ease-bounce);
}

.element:hover {
    transform: translateY(-5px);
}
```

---

## 🎯 Примеры использования

### Hero секция с анимациями

```html
<section class="hero-section">
    <div class="container">
        <h1 class="animate-fade-in-down">
            Заголовок
        </h1>
        <p class="animate-fade-in-up delay-200">
            Описание
        </p>
        <button class="btn-modern btn-primary-modern animate-bounce-in delay-400">
            Действие
        </button>
    </div>
</section>
```

### Карточки товаров

```html
<div class="products-grid">
    <div class="card-modern card-image-modern reveal stagger-1">
        <span class="card-badge-modern">New</span>
        <img src="product.jpg" alt="">
        <div class="card-content-modern">
            <h3 class="card-title-modern">Товар 1</h3>
            <p class="card-description-modern">Описание</p>
            <button class="btn-modern btn-primary-modern">
                В корзину
            </button>
        </div>
    </div>
    
    <div class="card-modern card-image-modern reveal stagger-2">
        <!-- Товар 2 -->
    </div>
    
    <div class="card-modern card-image-modern reveal stagger-3">
        <!-- Товар 3 -->
    </div>
</div>
```

### Форма обратной связи

```html
<form class="contact-form-modern">
    <div class="form-group-modern input-floating-modern">
        <input type="text" class="input-modern" placeholder=" " required>
        <label>Ваше имя</label>
    </div>
    
    <div class="form-group-modern input-icon-modern">
        <input type="email" class="input-modern" placeholder=" " required>
        <span class="input-icon">✉️</span>
        <label>Email</label>
    </div>
    
    <div class="form-group-modern">
        <textarea class="textarea-modern input-modern" placeholder="Сообщение"></textarea>
    </div>
    
    <label class="checkbox-modern">
        <input type="checkbox" required>
        <span>Согласен с политикой конфиденциальности</span>
    </label>
    
    <button type="submit" class="btn-modern btn-primary-modern btn-modern-lg">
        Отправить
    </button>
</form>
```

---

## 🔧 Кастомизация

### Изменение цветов

```css
:root {
    /* Основной цвет */
    --color-primary: #your-color;
    --color-primary-rgb: r, g, b;
    
    /* Градиенты */
    --gradient-primary: linear-gradient(135deg, #color1, #color2);
}
```

### Изменение анимаций

```css
:root {
    /* Длительности */
    --duration-fast: 150ms;
    --duration-normal: 250ms;
    --duration-slow: 400ms;
}
```

### Добавление своих анимаций

```css
@keyframes myAnimation {
    0% { /* start */ }
    100% { /* end */ }
}

.animate-my-animation {
    animation: myAnimation 1s var(--ease-smooth);
}
```

---

## 📈 Производительность

### Оптимизации

- ✅ CSS переменные для быстрого переключения тем
- ✅ transform и opacity для GPU ускорения
- ✅ will-change для часто анимируемых элементов
- ✅ Reduced motion для доступности

### Best Practices

```css
/* ✅ Хорошо */
.element {
    transform: translateY(0);
    opacity: 1;
    transition: transform 0.3s, opacity 0.3s;
}

/* ❌ Плохо */
.element {
    top: 0;
    transition: top 0.3s; /* Вызывает reflow */
}
```

---

## ♿ Доступность

### Reduced Motion

```css
@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
```

### Focus States

```css
.btn-modern:focus-visible {
    outline: 2px solid var(--color-primary);
    outline-offset: 2px;
}
```

---

## 🎉 Итоги v3.0.0

### Добавлено
- ✅ 50+ CSS анимаций
- ✅ 15+ UI компонентов
- ✅ Тёмная тема
- ✅ Easing функции
- ✅ Scroll-Triggered анимации

### Улучшено
- ✅ Современный дизайн
- ✅ Плавные переходы
- ✅ Микро-взаимодействия
- ✅ Доступность

---

**Версия:** 3.0.0  
**Дата:** Февраль 2026  
**Статус:** ✅ Готово к продакшену
