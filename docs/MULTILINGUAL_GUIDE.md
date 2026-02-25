# Руководство по мультиязычности АТК ВЭД

## 📋 Обзор

Система мультиязычности поддерживает 3 языка:
- 🇷🇺 **Русский** (по умолчанию)
- 🇬🇧 **Английский** (для международных клиентов)
- 🇨🇳 **Китайский** (для работы с поставщиками)

## 🚀 Быстрый старт

### 1. Активация

Мультиязычность активируется автоматически при подключении файла:

```php
require_once get_template_directory() . '/inc/multilingual.php';
```

### 2. Настройка через Customizer

1. Перейдите в **Внешний вид → Настроить → Мультиязычность**
2. Включите нужные языки
3. Выберите язык по умолчанию
4. Настройте стиль переключателя

### 3. Добавление переключателя

#### В шаблон:
```php
<?php echo atk_ved_language_switcher(); ?>
```

#### С параметрами:
```php
<?php 
echo atk_ved_language_switcher([
    'show_flags' => true,
    'show_names' => true,
    'style' => 'dropdown', // dropdown, list, flags
    'class' => 'my-custom-class'
]); 
?>
```

#### Шорткод:
```
[language_switcher style="dropdown" show_flags="yes" show_names="yes"]
```

## 📝 Использование переводов

### Базовое использование

```php
// Получить перевод
$text = atk_ved_translate('home');

// Короткая функция
$text = __t('services');

// Вывод с экранированием
_et('contact_us');
```

### В шаблонах

```php
<h1><?php _et('hero_title'); ?></h1>
<p><?php echo esc_html(__t('hero_subtitle')); ?></p>
<button><?php _et('get_consultation'); ?></button>
```

### Получить текущий язык

```php
$current_lang = atk_ved_get_current_language(); // 'ru', 'en', 'zh'
```

### Установить язык программно

```php
atk_ved_set_language('en');
```

## 🎨 Стили переключателя

### Dropdown (выпадающий список)

```php
echo atk_ved_language_switcher(['style' => 'dropdown']);
```

**Вид:** Текущий язык с стрелкой вниз, при клике открывается список

### List (горизонтальный список)

```php
echo atk_ved_language_switcher(['style' => 'list']);
```

**Вид:** Все языки в ряд, активный выделен

### Flags (только флаги)

```php
echo atk_ved_language_switcher(['style' => 'flags']);
```

**Вид:** Круглые кнопки с флагами

## 🔧 Добавление новых переводов

### 1. Добавить в массив переводов

Откройте `inc/multilingual.php` и добавьте в функцию `atk_ved_get_translations()`:

```php
'my_new_key' => [
    'ru' => 'Мой текст',
    'en' => 'My text',
    'zh' => '我的文字'
],
```

### 2. Использовать в шаблоне

```php
<?php _et('my_new_key'); ?>
```

## 🌐 Интеграция с WPML/Polylang

Система автоматически определяет наличие WPML или Polylang:

```php
// Проверка WPML
if (defined('ICL_LANGUAGE_CODE')) {
    $lang = ICL_LANGUAGE_CODE;
}

// Проверка Polylang
if (function_exists('pll_current_language')) {
    $lang = pll_current_language();
}
```

### Использование с WPML

1. Установите WPML
2. Настройте языки в WPML
3. Система автоматически будет использовать WPML

### Использование с Polylang

1. Установите Polylang
2. Настройте языки в Polylang
3. Система автоматически будет использовать Polylang

## 📍 Размещение переключателя

### В header.php

```php
<header class="site-header">
    <div class="header-container">
        <div class="logo">...</div>
        <nav class="main-nav">...</nav>
        <?php echo atk_ved_language_switcher(['style' => 'flags']); ?>
    </div>
</header>
```

### В меню WordPress

Переключатель автоматически добавляется в primary меню.

Для отключения:
```php
remove_filter('wp_nav_menu_items', 'atk_ved_add_language_switcher_to_menu', 10);
```

### В footer.php

```php
<footer class="site-footer">
    <div class="footer-top">
        <?php echo atk_ved_language_switcher(['style' => 'list']); ?>
    </div>
</footer>
```

### В виджете

```php
// В functions.php
function my_language_widget() {
    echo atk_ved_language_switcher();
}
add_action('widgets_init', function() {
    register_sidebar([
        'name' => 'Language Switcher',
        'id' => 'language-switcher',
        'before_widget' => '<div class="widget">',
        'after_widget' => '</div>',
    ]);
});
```

## 🎯 Примеры использования

### Пример 1: Многоязычная форма

```php
<form id="contactForm">
    <input type="text" 
           name="name" 
           placeholder="<?php echo esc_attr(__t('your_name')); ?>" 
           required>
    
    <input type="email" 
           name="email" 
           placeholder="<?php echo esc_attr(__t('your_email')); ?>" 
           required>
    
    <textarea name="message" 
              placeholder="<?php echo esc_attr(__t('your_message')); ?>" 
              required></textarea>
    
    <button type="submit">
        <?php _et('send'); ?>
    </button>
</form>
```

### Пример 2: Многоязычное меню

```php
<nav class="main-nav">
    <ul>
        <li><a href="#home"><?php _et('home'); ?></a></li>
        <li><a href="#services"><?php _et('services'); ?></a></li>
        <li><a href="#delivery"><?php _et('delivery'); ?></a></li>
        <li><a href="#contacts"><?php _et('contacts'); ?></a></li>
    </ul>
</nav>
```

### Пример 3: Условный контент по языку

```php
<?php
$current_lang = atk_ved_get_current_language();

if ($current_lang === 'zh') {
    // Специальный контент для китайских пользователей
    echo '<div class="china-special">特别优惠</div>';
} elseif ($current_lang === 'en') {
    // Контент для англоязычных
    echo '<div class="promo">Special Offer</div>';
} else {
    // Русский контент
    echo '<div class="promo">Специальное предложение</div>';
}
?>
```

## 🔄 AJAX переключение языка

JavaScript автоматически обрабатывает переключение:

```javascript
// Переключение происходит автоматически при клике
// Показывается загрузчик
// Отправляется AJAX запрос
// Страница перезагружается с новым языком
```

### Кастомное переключение

```javascript
jQuery(document).ready(function($) {
    $('.my-lang-button').on('click', function() {
        const lang = $(this).data('lang');
        
        $.ajax({
            url: atkVedData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'atk_ved_switch_language',
                nonce: atkVedData.nonce,
                lang: lang
            },
            success: function(response) {
                if (response.success) {
                    window.location.reload();
                }
            }
        });
    });
});
```

## 🎨 Кастомизация стилей

### Изменение цветов

```css
/* В вашем custom.css */
.lang-current {
    background: #your-color;
    border-color: #your-border;
}

.lang-current:hover {
    background: #your-hover-color;
}

.language-switcher-list li.active .lang-option {
    background: #your-active-color;
}
```

### Изменение размеров

```css
.lang-flag {
    font-size: 24px; /* Увеличить флаги */
}

.lang-flag-btn {
    width: 50px;  /* Увеличить кнопки */
    height: 50px;
}
```

## 📱 Мобильная адаптация

Стили автоматически адаптируются:

- **Desktop:** Полный переключатель с названиями
- **Tablet:** Компактный вид
- **Mobile:** Только флаги или иконки

### Кастомная адаптация

```css
@media (max-width: 768px) {
    .lang-name {
        display: none; /* Скрыть названия на мобильных */
    }
}
```

## 🔍 SEO оптимизация

### Hreflang теги

Добавьте в `header.php`:

```php
<?php
$languages = atk_ved_get_languages();
$current_url = home_url($_SERVER['REQUEST_URI']);

foreach ($languages as $code => $lang) {
    if ($lang['enabled']) {
        echo '<link rel="alternate" hreflang="' . esc_attr($code) . '" href="' . esc_url($current_url) . '" />';
    }
}
?>
```

### Языковые версии страниц

```php
// Для каждой страницы создайте версии:
// /about/ (русский)
// /en/about/ (английский)
// /zh/about/ (китайский)
```

## 🐛 Отладка

### Проверка текущего языка

```php
// Добавьте в шаблон для отладки
echo 'Current language: ' . atk_ved_get_current_language();
```

### Проверка cookie

```php
// В браузере откройте консоль
console.log(document.cookie);
// Должно быть: atk_ved_lang=ru (или en, zh)
```

### Логирование

```php
// Добавьте в inc/multilingual.php
error_log('Language switched to: ' . $lang);
```

## ⚙️ Настройки

### Отключить автоопределение языка

```php
// В functions.php
remove_action('init', 'atk_ved_init_language');
```

### Изменить язык по умолчанию

```php
// В inc/multilingual.php измените:
return 'en'; // вместо 'ru'
```

### Добавить новый язык

1. Добавьте в `atk_ved_get_languages()`:

```php
'de' => [
    'name' => 'German',
    'native_name' => 'Deutsch',
    'flag' => '🇩🇪',
    'locale' => 'de_DE',
    'direction' => 'ltr',
    'enabled' => true
]
```

2. Добавьте переводы в `atk_ved_get_translations()`:

```php
'home' => [
    'ru' => 'Главная',
    'en' => 'Home',
    'zh' => '首页',
    'de' => 'Startseite' // Новый перевод
],
```

## 📊 Производительность

- Cookie хранится 1 год
- Нет дополнительных запросов к БД
- Минимальный JavaScript (< 5KB)
- CSS загружается только при наличии переключателя

## ✅ Чек-лист внедрения

```
□ Подключить inc/multilingual.php в functions.php
□ Подключить CSS и JS файлы
□ Добавить переключатель в header
□ Перевести все тексты в шаблонах
□ Настроить языки в Customizer
□ Протестировать переключение
□ Проверить на мобильных устройствах
□ Добавить hreflang теги
□ Настроить SEO для каждого языка
□ Протестировать формы на всех языках
```

## 🆘 Поддержка

При возникновении проблем:

1. Проверьте консоль браузера (F12)
2. Проверьте логи WordPress
3. Убедитесь что cookie включены
4. Очистите кэш браузера и WordPress

---

**Версия:** 2.0.0  
**Дата:** Февраль 2026  
**Статус:** ✅ Готово к использованию
