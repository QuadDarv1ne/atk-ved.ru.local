# Дополнительные UI компоненты v2.2

## 📋 Обзор

Готовые компоненты для уведомлений, прогресса и загрузки с JavaScript API.

---

## 🔔 Уведомления (Toast)

### JavaScript API

```javascript
// Показать уведомление
atkShowToast({
    type: 'success',      // info, success, warning, error
    title: 'Успешно!',
    message: 'Данные сохранены',
    duration: 5000,       // мс, 0 = не закрывать
    position: 'top-right', // top-right, top-left, top-center, bottom-right, bottom-left, bottom-center
    closable: true,
    onClose: function() { console.log('Закрыто'); }
});

// Закрыть конкретное уведомление
atkCloseToast($toastElement);

// Закрыть все уведомления
atkCloseAllToasts();
atkCloseAllToasts('top-right'); // Только в позиции
```

### Примеры

```javascript
// Успех
atkShowToast({
    type: 'success',
    title: 'Готово',
    message: 'Форма успешно отправлена'
});

// Ошибка
atkShowToast({
    type: 'error',
    title: 'Ошибка',
    message: 'Не удалось сохранить данные'
});

// Предупреждение
atkShowToast({
    type: 'warning',
    title: 'Внимание',
    message: 'Форма содержит ошибки'
});

// Информация
atkShowToast({
    type: 'info',
    title: 'Инфо',
    message: 'Обновление доступно'
});
```

---

## 📢 Alert (Блоки уведомлений)

### HTML

```html
<div class="alert alert-success" role="alert">
    <div class="alert-icon">✅</div>
    <div class="alert-content">
        <strong class="alert-title">Успешно!</strong>
        <p class="alert-message">Данные сохранены</p>
    </div>
    <button class="alert-close" aria-label="Закрыть">×</button>
</div>
```

### JavaScript API

```javascript
// Показать alert
atkShowAlert({
    type: 'success',      // info, success, warning, error
    title: 'Заголовок',
    message: 'Текст сообщения',
    dismissible: true,    // Можно закрыть
    duration: 5000,       // Автозакрытие (мс)
    container: 'body'     // Куда вставить
});

// Закрыть alert
atkCloseAlert($alertElement);
```

### Типы alert

```html
<div class="alert alert-info">Информация</div>
<div class="alert alert-success">Успех</div>
<div class="alert alert-warning">Предупреждение</div>
<div class="alert alert-error">Ошибка</div>
```

---

## 📊 Progress Bar

### HTML

```html
<div class="progress-bar">
    <div class="progress-label">
        <span>Загрузка</span>
        <span>75%</span>
    </div>
    <div class="progress-track">
        <div class="progress-fill progress-primary" 
             style="width: 75%"
             role="progressbar"
             aria-valuenow="75"
             aria-valuemin="0"
             aria-valuemax="100">
        </div>
    </div>
</div>
```

### JavaScript API

```javascript
// Установить значение
atkSetProgress('#my-progress', 75, 100);

// Увеличить на шаг
atkIncrementProgress('#my-progress', 10);

// Сбросить
atkResetProgress('#my-progress');
```

### Варианты

```html
<!-- Цвета -->
<div class="progress-bar progress-primary">...</div>
<div class="progress-bar progress-success">...</div>
<div class="progress-bar progress-warning">...</div>
<div class="progress-bar progress-info">...</div>
<div class="progress-bar progress-error">...</div>

<!-- Размеры -->
<div class="progress-bar progress-sm">...</div>
<div class="progress-bar progress-lg">...</div>
<div class="progress-bar progress-xl">...</div>

<!-- Полосатый -->
<div class="progress-bar">
    <div class="progress-fill progress-striped">...</div>
</div>

<!-- Анимированный -->
<div class="progress-bar">
    <div class="progress-fill progress-striped progress-animated">...</div>
</div>
```

### Circular Progress (Круговой)

```html
<div class="progress-circular">
    <svg viewBox="0 0 120 120">
        <circle class="progress-track" cx="60" cy="60" r="45"/>
        <circle class="progress-fill" cx="60" cy="60" r="45" 
                style="stroke-dashoffset: 71"/>
    </svg>
    <div class="progress-label">75%</div>
</div>
```

---

## 💀 Skeleton Loader

### HTML

```html
<!-- Текстовый скелетон -->
<div class="skeleton skeleton-text"></div>
<div class="skeleton skeleton-text"></div>
<div class="skeleton skeleton-text" style="width: 60%"></div>

<!-- Карточка -->
<div class="skeleton-card">
    <div class="skeleton-title"></div>
    <div class="skeleton-text"></div>
    <div class="skeleton-text"></div>
    <div class="skeleton-text"></div>
</div>

<!-- С аватаром -->
<div class="skeleton-card">
    <div class="skeleton-avatar"></div>
    <div class="skeleton-title"></div>
    <div class="skeleton-text"></div>
</div>

<!-- С изображением -->
<div class="skeleton-card">
    <div class="skeleton-image"></div>
    <div class="skeleton-title"></div>
    <div class="skeleton-text"></div>
    <div class="skeleton-button"></div>
</div>
```

### JavaScript API

```javascript
// Показать скелетон
atkShowSkeleton('#container', {
    lines: 3,
    showAvatar: false,
    showImage: false,
    showButton: false
});

// Скрыть скелетон
atkHideSkeleton('#container');
```

### Пример использования

```javascript
// Загрузка данных
function loadData() {
    // Показываем скелетон
    atkShowSkeleton('#content', {
        lines: 5,
        showImage: true
    });
    
    // Загружаем данные
    fetch('/api/data')
        .then(response => response.json())
        .then(data => {
            // Скрываем скелетон, показываем контент
            atkHideSkeleton('#content');
            $('#content').html(renderData(data));
        });
}
```

---

## 🏷️ Badge (Бейджи)

### HTML

```html
<!-- Обычные -->
<span class="badge badge-primary">Новый</span>
<span class="badge badge-success">Успех</span>
<span class="badge badge-warning">Внимание</span>
<span class="badge badge-info">Инфо</span>
<span class="badge badge-error">Ошибка</span>
<span class="badge badge-secondary">Вторичный</span>

<!-- Счётчик -->
<span class="badge badge-primary badge-count" data-count="5">
    Сообщения
</span>
```

---

## 📱 Адаптивность

### Toast на мобильных

```css
/* На мобильных toast на всю ширину */
@media (max-width: 768px) {
    .toast-container {
        left: 10px;
        right: 10px;
        max-width: none;
    }
}
```

---

## 🌙 Тёмная тема

Все компоненты поддерживают тёмную тему:

```css
.dark-mode .alert {
    background: rgba(255, 255, 255, 0.05);
}

.dark-mode .toast {
    background: var(--color-gray-800);
}

.dark-mode .progress-track {
    background: var(--color-gray-700);
}
```

---

## 🎯 Примеры использования

### Форма с уведомлениями

```javascript
$('#myForm').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: atkVedData.ajaxUrl,
        type: 'POST',
        data: $(this).serialize(),
        beforeSend: function() {
            // Показываем прогресс
            atkSetProgress('#form-progress', 0);
            atkIncrementProgress('#form-progress', 30);
        },
        success: function(response) {
            atkIncrementProgress('#form-progress', 70);
            atkShowToast({
                type: 'success',
                title: 'Успешно',
                message: 'Форма отправлена'
            });
        },
        error: function() {
            atkShowToast({
                type: 'error',
                title: 'Ошибка',
                message: 'Не удалось отправить форму'
            });
        }
    });
});
```

### Загрузка с скелетоном

```javascript
function loadContent() {
    atkShowSkeleton('#content', {
        lines: 5,
        showImage: true,
        showButton: true
    });
    
    setTimeout(function() {
        atkHideSkeleton('#content');
        $('#content').html('<p>Контент загружен</p>');
    }, 2000);
}
```

### Прогресс многошаговой формы

```javascript
const steps = 4;
let currentStep = 1;

function nextStep() {
    currentStep++;
    const progress = (currentStep / steps) * 100;
    atkSetProgress('#wizard-progress', progress);
    
    if (currentStep === steps) {
        atkShowToast({
            type: 'success',
            message: 'Все шаги завершены'
        });
    }
}
```

---

## 📊 Сравнение компонентов

| Компонент | Для чего | Автозакрытие | Позиции |
|-----------|----------|--------------|---------|
| **Toast** | Краткие уведомления | ✅ | 6 |
| **Alert** | Важные сообщения | ✅ | В потоке |
| **Progress** | Индикатор прогресса | ❌ | В потоке |
| **Skeleton** | Загрузка контента | ❌ | В потоке |

---

## 🔧 Настройка

### Глобальные настройки Toast

```javascript
// Изменить настройки по умолчанию
$.extend(atkToastDefaults, {
    duration: 10000,
    position: 'bottom-right',
    closable: false
});
```

### Кастомные стили

```css
/* Кастомный цвет toast */
.toast-custom {
    border-left-color: #purple;
    background: rgba(128, 0, 128, 0.1);
}
```

---

## ♿ Доступность

- ✅ ARIA роли (`role="alert"`)
- ✅ ARIA атрибуты для progress
- ✅ Keyboard поддержка (закрытие по Escape)
- ✅ Focus management
- ✅ Screen reader поддержка

---

**Версия:** 2.2.0  
**Обновлено:** Февраль 2026
