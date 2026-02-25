# ACF Интеграция АТК ВЭД v2.3

## 📋 Обзор

Интеграция с Advanced Custom Fields для гибкого управления контентом темы.

---

## 🚀 Требования

- **WordPress** 5.0+
- **ACF** 5.8+ (Pro или Free)
- **PHP** 7.4+

---

## 📁 Структура файлов

```
inc/
├── acf-field-groups.php    # Регистрация групп полей
├── acf-options.php         # Options page и helper функции
└── acf-blocks.php          # Gutenberg блоки ACF

template-parts/blocks/
├── hero/
├── services/
├── stats/
├── testimonials/
├── cta/
├── team/
├── partners/
└── faq/

css/blocks/                 # Стили для блоков
js/blocks/                  # Скрипты для блоков
```

---

## ⚙️ Настройки темы

### Доступ к настройкам

**Админка → Настройки темы → Настройки темы АТК ВЭД**

### Вкладки настроек

#### 1. Главный экран (Hero)

| Поле | Тип | Описание |
|------|-----|----------|
| `hero_title` | Text | Заголовок H1 |
| `hero_subtitle` | Textarea | Подзаголовок |
| `hero_features` | Repeater | Список преимуществ |
| `hero_image` | Image | Изображение |
| `hero_stats` | Repeater | Показатели статистики |

#### 2. Контакты в хедере

| Поле | Тип | Описание |
|------|-----|----------|
| `header_phone` | Text | Телефон |
| `header_email` | Email | Email |
| `header_working_hours` | Text | Режим работы |

#### 3. Социальные сети

| Поле | Тип | Описание |
|------|-----|----------|
| `social_networks` | Repeater | Список соцсетей |

#### 4. SEO настройки

| Поле | Тип | Описание |
|------|-----|----------|
| `seo_title` | Text | SEO Title (до 60 символов) |
| `seo_description` | Textarea | SEO Description (до 160 символов) |
| `seo_keywords` | Text | Ключевые слова |

---

## 🔧 Helper функции

### Получение значений

```php
// Получить поле
$value = atk_ved_get_field('field_name');

// Получить поле из опций
$value = atk_ved_get_option('field_name');

// Получить поле с fallback
$value = atk_ved_get_field('field_name', 'default value');

// Вывести значение
atk_ved_the_field('field_name');

// Вывести HTML
atk_ved_the_field_html('field_name');
```

### Работа с изображениями

```php
// Получить изображение
$image = atk_ved_get_image('hero_image', 'full');

// Вывести изображение
atk_ved_the_image('hero_image', 'medium', array(
    'class' => 'custom-class',
    'alt' => 'Описание'
));
```

### Работа с repeater

```php
// Получить repeater
$items = atk_ved_get_repeater('hero_features');

// Проверить наличие
if (atk_ved_have_rows('hero_features')) {
    while (atk_ved_have_rows('hero_features')) {
        atk_ved_the_row();
        $text = get_sub_field('text');
        $icon = get_sub_field('icon');
        // Вывод...
    }
}

// Получить подполе
$value = atk_ved_get_sub_field('repeater_name', 'sub_field', 0);
```

### Готовые функции

```php
// Получить настройки темы
$settings = atk_ved_get_theme_settings();
echo $settings['hero_title'];

// Получить контакты
$contacts = atk_ved_get_contacts();
echo $contacts['phone'];
echo $contacts['email'];

// Получить соцсети
$social = atk_ved_get_social();
echo atk_ved_get_social_url('telegram');

// Получить SEO
$seo = atk_ved_get_seo();
echo $seo['title'];
```

---

## 🧩 ACF Gutenberg блоки

### Доступные блоки

| Блок | Описание | Шорткод |
|------|----------|---------|
| **Hero** | Главный экран | - |
| **Services** | Сетка услуг | - |
| **Stats** | Счётчики статистики | - |
| **Testimonials** | Отзывы клиентов | - |
| **CTA** | Призыв к действию | - |
| **Team** | Команда | - |
| **Partners** | Партнёры | - |
| **FAQ** | Вопросы и ответы | `[faq]` |

### Использование в редакторе

1. Откройте страницу/запись
2. Нажмите **+** (добавить блок)
3. Найдите категорию **АТК ВЭД**
4. Выберите нужный блок
5. Заполните поля в сайдбаре

---

## 📊 Custom Post Types

### Services (Услуги)

**Поля:**
- `service_icon` - Иконка
- `service_number` - Номер
- `service_short_desc` - Краткое описание
- `service_features` - Особенности (repeater)

**Пример вывода:**
```php
$services = get_posts(array(
    'post_type' => 'service',
    'posts_per_page' => -1,
));

foreach ($services as $service) {
    $icon = get_field('service_icon', $service->ID);
    $number = get_field('service_number', $service->ID);
    $desc = get_field('service_short_desc', $service->ID);
}
```

### FAQ (Вопросы и ответы)

**Поля:**
- `faq_icon` - Иконка
- `faq_category` - Категория
- `faq_is_active` - Показывать на сайте

**Пример вывода:**
```php
$faqs = get_posts(array(
    'post_type' => 'faq',
    'meta_key' => 'faq_is_active',
    'meta_value' => '1',
    'posts_per_page' => -1,
));
```

### Team (Команда)

**Поля:**
- `team_position` - Должность
- `team_photo` - Фото
- `team_social` - Соцсети (repeater)

### Partners (Партнёры)

**Поля:**
- `partner_logo` - Логотип
- `partner_url` - Ссылка
- `partner_is_featured` - VIP партнёр

---

## 🎨 Примеры использования

### Hero секция с ACF

```php
<?php
$settings = atk_ved_get_theme_settings();
?>

<section class="hero-section">
    <div class="container">
        <h1><?php echo esc_html($settings['hero_title']); ?></h1>
        
        <?php if ($settings['hero_features']): ?>
        <ul class="hero-features">
            <?php foreach ($settings['hero_features'] as $feature): ?>
            <li><?php echo esc_html($feature['text']); ?></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        
        <?php if ($settings['hero_image']): ?>
        <div class="hero-image">
            <img src="<?php echo esc_url($settings['hero_image']['url']); ?>" 
                 alt="<?php echo esc_attr($settings['hero_image']['alt']); ?>">
        </div>
        <?php endif; ?>
    </div>
</section>
```

### Статистика с ACF

```php
<?php
$stats = atk_ved_get_repeater('hero_stats');
?>

<div class="hero-stats">
    <?php foreach ($stats as $stat): ?>
    <div class="stat-item">
        <span class="stat-number" data-target="<?php echo esc_attr($stat['number']); ?>">
            <?php echo esc_html($stat['number']); ?>
        </span>
        <span class="stat-label"><?php echo esc_html($stat['label']); ?></span>
    </div>
    <?php endforeach; ?>
</div>
```

### Социальные сети

```php
<?php
$social = atk_ved_get_social();
?>

<div class="social-links">
    <?php foreach ($social as $network => $data): ?>
    <a href="<?php echo esc_url($data['url']); ?>" 
       class="social-link social-<?php echo esc_attr($network); ?>"
       target="_blank"
       rel="noopener">
        <?php echo esc_html($data['name']); ?>
    </a>
    <?php endforeach; ?>
</div>
```

### Контакты в хедере

```php
<?php
$contacts = atk_ved_get_contacts();
?>

<div class="header-contacts">
    <a href="tel:<?php echo esc_attr($contacts['phone']); ?>">
        <?php echo esc_html($contacts['phone']); ?>
    </a>
    <a href="mailto:<?php echo esc_attr($contacts['email']); ?>">
        <?php echo esc_html($contacts['email']); ?>
    </a>
    <span class="working-hours"><?php echo esc_html($contacts['working_hours']); ?></span>
</div>
```

---

## 🔐 Безопасность

### Экранирование вывода

```php
// Текст
echo esc_html($value);

// HTML
echo wp_kses_post($value);

// URL
echo esc_url($value);

// Атрибуты
echo esc_attr($value);
```

### Проверка наличия ACF

```php
if (!class_exists('ACF')) {
    // ACF не установлен
    return;
}

if (function_exists('get_field')) {
    // ACF активен
    $value = get_field('field_name');
}
```

---

## 🐛 Решение проблем

### ACF не отображает поля

1. Проверьте что плагин ACF активирован
2. Проверьте права доступа (Capabilities)
3. Очистите кэш WordPress

### Поля не сохраняются

1. Проверьте права на запись в БД
2. Проверьте конфликты с другими плагинами
3. Включите WP_DEBUG для отладки

### Блоки не отображаются в Gutenberg

1. Проверьте что ACF Pro установлен (для блоков)
2. Очистите кэш браузера
3. Пересохраните страницу

---

## 📚 Дополнительные ресурсы

- [ACF Documentation](https://www.advancedcustomfields.com/resources/)
- [ACF Blocks](https://www.advancedcustomfields.com/resources/blocks/)
- [ACF Functions](https://www.advancedcustomfields.com/resources/category/functions/)

---

## 🎯 Best Practices

### 1. Используйте helper функции

```php
// ✅ Хорошо
$value = atk_ved_get_option('field_name');

// ❌ Плохо
$value = get_field('field_name', 'option');
```

### 2. Всегда проверяйте наличие данных

```php
// ✅ Хорошо
if (atk_ved_have_field('image')) {
    atk_ved_the_image('image');
}

// ❌ Плохо
atk_ved_the_image('image'); // Может вызвать ошибку
```

### 3. Используйте fallback значения

```php
// ✅ Хорошо
$title = atk_ved_get_field('title', 'Заголовок по умолчанию');

// ❌ Плохо
$title = atk_ved_get_field('title'); // Может быть пустым
```

### 4. Кэшируйте сложные запросы

```php
// ✅ Хорошо
$settings = wp_cache_get('theme_settings');
if (!$settings) {
    $settings = atk_ved_get_theme_settings();
    wp_cache_set('theme_settings', $settings);
}
```

---

**Версия:** 2.3.0  
**Обновлено:** Февраль 2026
