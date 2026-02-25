# Дизайн-система АТК ВЭД v2.0

## 📋 Обзор

Современная дизайн-система с CSS переменными, анимациями и готовыми компонентами.

---

## 🎨 Цветовая палитра

### Основные цвета

```css
--color-primary: #e31e24;          /* Красный - основной */
--color-primary-dark: #c01a1f;     /* Тёмный красный */
--color-primary-light: #ff4d4f;    /* Светлый красный */
```

### Нейтральные цвета

```css
--color-gray-50: #fafafa;    /* Очень светлый */
--color-gray-100: #f5f5f5;   /* Светлый фон */
--color-gray-200: #eeeeee;   /* Границы */
--color-gray-300: #e0e0e0;   /* Разделители */
--color-gray-400: #bdbdbd;   /* Неактивный текст */
--color-gray-500: #9e9e9e;   /* Вторичный текст */
--color-gray-600: #757575;   /* Основной текст */
--color-gray-700: #616161;   /* Заголовки */
--color-gray-800: #424242;   /* Тёмный текст */
--color-gray-900: #2c2c2c;   /* Почти чёрный */
```

### Семантические цвета

```css
--color-success: #4caf50;  /* Успех */
--color-warning: #ff9800;  /* Предупреждение */
--color-error: #f44336;    /* Ошибка */
--color-info: #2196f3;     /* Информация */
```

---

## 📏 Типографика

### Размеры шрифта

| Класс | Размер | REM |
|-------|--------|-----|
| `--font-size-xs` | 12px | 0.75rem |
| `--font-size-sm` | 14px | 0.875rem |
| `--font-size-base` | 16px | 1rem |
| `--font-size-lg` | 18px | 1.125rem |
| `--font-size-xl` | 20px | 1.25rem |
| `--font-size-2xl` | 24px | 1.5rem |
| `--font-size-3xl` | 30px | 1.875rem |
| `--font-size-4xl` | 36px | 2.25rem |
| `--font-size-5xl` | 48px | 3rem |
| `--font-size-6xl` | 60px | 3.75rem |

### Начертание

```css
--font-weight-light: 300;
--font-weight-normal: 400;
--font-weight-medium: 500;
--font-weight-semibold: 600;
--font-weight-bold: 700;
```

---

## 📐 Отступы (Spacing)

| Класс | Размер | REM |
|-------|--------|-----|
| `--spacing-1` | 4px | 0.25rem |
| `--spacing-2` | 8px | 0.5rem |
| `--spacing-3` | 12px | 0.75rem |
| `--spacing-4` | 16px | 1rem |
| `--spacing-5` | 20px | 1.25rem |
| `--spacing-6` | 24px | 1.5rem |
| `--spacing-8` | 32px | 2rem |
| `--spacing-10` | 40px | 2.5rem |
| `--spacing-12` | 48px | 3rem |
| `--spacing-16` | 64px | 4rem |
| `--spacing-20` | 80px | 5rem |
| `--spacing-24` | 96px | 6rem |

---

## 🔲 Border Radius

```css
--radius-sm: 4px;      /* 0.25rem */
--radius-md: 8px;      /* 0.5rem */
--radius-lg: 12px;     /* 0.75rem */
--radius-xl: 16px;     /* 1rem */
--radius-2xl: 24px;    /* 1.5rem */
--radius-full: 9999px; /* Круг */
```

---

## 🌈 Тени

```css
--shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
--shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
--shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
--shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
--shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
--shadow-colored: 0 10px 40px rgba(227, 30, 36, 0.3);
```

---

## ⚡ Анимации

### Базовые анимации

```css
/* Появление */
.animate-fade-in
.animate-fade-in-up
.animate-fade-in-down
.animate-scale-in
.animate-slide-in-right

/* Эффекты */
.animate-pulse
.animate-bounce
.animate-spin
```

### Задержки

```css
.delay-100  /* 100ms */
.delay-200  /* 200ms */
.delay-300  /* 300ms */
.delay-400  /* 400ms */
.delay-500  /* 500ms */
```

### Scroll Reveal

```html
<!-- Элемент появится при прокрутке -->
<div class="reveal">Контент</div>
<div class="reveal-left">Слева</div>
<div class="reveal-right">Справа</div>
<div class="reveal-scale">Масштаб</div>
```

---

## 🎯 Компоненты

### Кнопки

```html
<!-- Основная -->
<button class="btn btn-primary">Кнопка</button>
<a href="#" class="cta-button">CTA</a>

<!-- Вторичная -->
<button class="btn btn-secondary">Кнопка</button>

<!-- Ghost -->
<button class="btn btn-ghost">Кнопка</button>

<!-- Размеры -->
<button class="btn btn-sm">Маленькая</button>
<button class="btn btn-lg">Большая</button>
```

### Карточки

```html
<div class="card hover-lift">
    <div class="card-header">Заголовок</div>
    <div class="card-body">
        <p>Содержимое карточки</p>
    </div>
    <div class="card-footer">Подвал</div>
</div>
```

### Бейджи

```html
<span class="badge badge-primary">Новый</span>
<span class="badge badge-success">Успех</span>
<span class="badge badge-warning">Внимание</span>
<span class="badge badge-info">Инфо</span>
```

### Формы

```html
<label for="input">Название поля</label>
<input type="text" id="input" placeholder="Подсказка">

<textarea placeholder="Сообщение"></textarea>

<select>
    <option>Опция 1</option>
    <option>Опция 2</option>
</select>
```

---

## 🎭 Эффекты при наведении

```html
<!-- Подъём карточки -->
<div class="hover-lift">Контент</div>

<!-- Увеличение изображения -->
<div class="hover-zoom-img">
    <img src="image.jpg" alt="">
</div>

<!-- Свечение -->
<div class="hover-glow">Контент</div>

<!-- Блеск -->
<div class="hover-shine">Контент</div>
```

---

## 🎪 Загрузчики

### Skeleton Loader

```html
<div class="skeleton" style="width: 100%; height: 20px;"></div>
```

### Точки загрузки

```html
<div class="dots-loading">
    <span></span>
    <span></span>
    <span></span>
</div>
```

### Прогресс бар

```html
<div class="progress-loading"></div>
```

---

## 📱 Адаптивность

### Breakpoints

```css
/* Mobile first */
@media (max-width: 640px)  { /* SM */ }
@media (max-width: 768px)  { /* MD */ }
@media (max-width: 1024px) { /* LG */ }
```

### Утилиты

```html
<div class="sm:hidden md:block">Скрыто на мобильных</div>
<div class="md:hidden lg:block">Скрыто на планшетах</div>
```

---

## 🌙 Тёмная тема

### Автоматическая (preference)

```css
@media (prefers-color-scheme: dark) {
    /* Автоматически применяется тёмная тема */
}
```

### Ручное переключение

```html
<body class="dark-mode">
    <!-- Тёмная тема активна -->
</body>
```

```javascript
// Переключение
document.body.classList.toggle('dark-mode');
```

---

## ♿ Доступность

### Focus Visible

```css
:focus-visible {
    outline: 2px solid var(--color-primary);
    outline-offset: 2px;
}
```

### Skip Link

```html
<a href="#main" class="skip-link">Перейти к контенту</a>
```

### Reduced Motion

```css
@media (prefers-reduced-motion: reduce) {
    /* Анимации отключаются */
}
```

---

## 🔧 Утилитарные классы

### Текст

```html
<p class="text-center">По центру</p>
<p class="text-primary">Красный текст</p>
<p class="font-bold">Жирный текст</p>
```

### Фон

```html
<div class="bg-white">Белый фон</div>
<div class="bg-gray-50">Светлый фон</div>
<div class="bg-primary">Красный фон</div>
```

### Flexbox

```html
<div class="flex items-center justify-between gap-4">
    <div>Элемент 1</div>
    <div>Элемент 2</div>
</div>
```

### Отступы

```html
<div class="mt-4 mb-8">Отступы сверху/снизу</div>
```

---

## 📊 Z-Index Scale

```css
--z-dropdown: 100;
--z-sticky: 200;
--z-fixed: 300;
--z-modal-backdrop: 400;
--z-modal: 500;
--z-popover: 600;
--z-tooltip: 700;
--z-toast: 800;
```

---

## 🎨 Примеры использования

### Hero секция

```html
<section class="hero-section bg-white">
    <div class="container">
        <h1 class="reveal gradient-text">Заголовок</h1>
        <p class="reveal delay-200">Описание</p>
        <button class="btn btn-primary btn-pulse reveal delay-300">
            Действие
        </button>
    </div>
</section>
```

### Карточка услуги

```html
<div class="card hover-lift reveal">
    <div class="card-body">
        <div class="icon icon-bounce mb-4">🚀</div>
        <h3 class="text-xl font-bold mb-2">Название</h3>
        <p class="text-gray-600 mb-4">Описание услуги</p>
        <a href="#" class="btn btn-ghost btn-icon-slide">
            Подробнее
            <span class="icon">→</span>
        </a>
    </div>
</div>
```

### Форма обратной связи

```html
<form class="card">
    <div class="card-body">
        <div class="mb-4">
            <label for="name">Имя</label>
            <input type="text" id="name" placeholder="Ваше имя">
        </div>
        <div class="mb-4">
            <label for="email">Email</label>
            <input type="email" id="email" placeholder="email@example.com">
        </div>
        <div class="mb-4">
            <label for="message">Сообщение</label>
            <textarea id="message" placeholder="Текст сообщения"></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-fill">
            Отправить
        </button>
    </div>
</form>
```

---

## 📚 Лучшие практики

### 1. Используйте CSS переменные

```css
/* ✅ Хорошо */
color: var(--color-primary);

/* ❌ Плохо */
color: #e31e24;
```

### 2. Используйте утилитарные классы

```html
<!-- ✅ Хорошо -->
<div class="flex items-center gap-4 mb-8">

<!-- ❌ Плохо -->
<div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
```

### 3. Анимации с умеренностью

```html
<!-- ✅ Хорошо - одна анимация на элемент -->
<div class="reveal">

<!-- ❌ Плохо - слишком много анимаций -->
<div class="reveal animate-bounce animate-pulse hover-lift hover-glow">
```

### 4. Доступность

```html
<!-- ✅ Хорошо -->
<button class="btn btn-primary" aria-label="Отправить форму">

<!-- ❌ Плохо -->
<button class="btn btn-primary">→</button>
```

---

## 🔧 Кастомизация

### Изменение основного цвета

```css
:root {
    --color-primary: #your-color;
    --color-primary-rgb: r, g, b; /* Для прозрачности */
}
```

### Изменение шрифта

```css
:root {
    --font-family-base: 'Your Font', sans-serif;
}
```

### Изменение контейнера

```css
:root {
    --container-max: 1400px; /* Было 1200px */
}
```

---

**Версия:** 2.0.0  
**Обновлено:** Февраль 2026
