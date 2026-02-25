# Расширенные возможности темы АТК ВЭД v1.2

## Новые функции

### 1. Кастомные типы записей

#### Услуги (Services)
Управление услугами через админ-панель WordPress.

**Как использовать:**
1. Перейдите в админке: **Услуги → Добавить услугу**
2. Заполните название и описание
3. Добавьте изображение (опционально)
4. Опубликуйте

**Вывод на сайте:**
```php
<?php
$services = atk_ved_get_services(6);
if ($services->have_posts()) {
    while ($services->have_posts()) {
        $services->the_post();
        // Ваш код
    }
}
wp_reset_postdata();
?>
```

#### Отзывы (Reviews)
Управление отзывами клиентов с рейтингом.

**Поля:**
- Имя автора
- Должность/Компания
- Рейтинг (1-5 звезд)
- Текст отзыва
- Фото автора (опционально)

**Как использовать:**
1. **Отзывы → Добавить отзыв**
2. Заполните все поля
3. Опубликуйте

**Вывод на сайте:**
```php
<?php
$reviews = atk_ved_get_reviews(4);
if ($reviews->have_posts()) {
    while ($reviews->have_posts()) {
        $reviews->the_post();
        $author = get_post_meta(get_the_ID(), '_review_author_name', true);
        $rating = get_post_meta(get_the_ID(), '_review_rating', true);
        // Ваш код
    }
}
wp_reset_postdata();
?>
```

#### FAQ
Управление часто задаваемыми вопросами.

**Как использовать:**
1. **FAQ → Добавить вопрос**
2. Заголовок = Вопрос
3. Содержимое = Ответ
4. Порядок = Сортировка (в атрибутах страницы)
5. Опубликуйте

### 2. Вспомогательные функции

#### Получение инициалов
```php
$initials = atk_ved_get_initials('Иван Петров'); // ИП
```

#### Вывод звезд рейтинга
```php
echo atk_ved_get_rating_stars(5); // ★★★★★
```

#### Обрезка текста
```php
$short_text = atk_ved_trim_text($long_text, 100, '...');
```

#### Форматирование телефона
```php
$formatted = atk_ved_format_phone('79991234567'); // +7 (999) 123-45-67
$link = atk_ved_get_phone_link('+7 (999) 123-45-67'); // tel:+79991234567
```

#### Проверка социальных сетей
```php
if (atk_ved_has_social_links()) {
    // Вывод социальных сетей
}
```

#### Хлебные крошки
```php
echo atk_ved_breadcrumbs();
```

#### SVG иконки
```php
echo atk_ved_get_svg_icon('phone'); // Иконка телефона
echo atk_ved_get_svg_icon('email'); // Иконка email
echo atk_ved_get_svg_icon('location'); // Иконка местоположения
echo atk_ved_get_svg_icon('check'); // Иконка галочки
```

### 3. AJAX функции

#### Отправка формы обратной связи
```javascript
jQuery.ajax({
    url: atkVedData.ajaxUrl,
    type: 'POST',
    data: {
        action: 'atk_ved_contact_form',
        nonce: atkVedData.nonce,
        name: 'Иван',
        email: 'ivan@example.com',
        phone: '+7 999 123-45-67',
        message: 'Текст сообщения'
    },
    success: function(response) {
        if (response.success) {
            alert(response.data.message);
        }
    }
});
```

#### Загрузка дополнительных отзывов
```javascript
jQuery.ajax({
    url: atkVedData.ajaxUrl,
    type: 'POST',
    data: {
        action: 'atk_ved_load_more_reviews',
        nonce: atkVedData.nonce,
        page: 2
    },
    success: function(response) {
        if (response.success) {
            jQuery('.reviews-grid').append(response.data.html);
        }
    }
});
```

#### AJAX поиск
```javascript
jQuery.ajax({
    url: atkVedData.ajaxUrl,
    type: 'POST',
    data: {
        action: 'atk_ved_search',
        nonce: atkVedData.nonce,
        query: 'поисковый запрос'
    },
    success: function(response) {
        if (response.success) {
            // response.data.results - массив результатов
        }
    }
});
```

### 4. Шаблоны страниц

#### 404 Error Page
Красивая страница ошибки 404 с:
- Большим номером ошибки
- Описанием проблемы
- Кнопками навигации
- Формой поиска
- Полезными ссылками

**Файл:** `404.php`

#### Search Results
Страница результатов поиска с:
- Хлебными крошками
- Счетчиком результатов
- Карточками результатов с превью
- Пагинацией
- Сообщением "Ничего не найдено"

**Файл:** `search.php`

#### Custom Search Form
Кастомная форма поиска с иконкой и стилями.

**Файл:** `searchform.php`

### 5. Шорткоды

#### Кнопка
```
[button url="#contact" style="primary" target="_self"]Текст кнопки[/button]
```

**Параметры:**
- `url` - ссылка (по умолчанию: #)
- `style` - стиль (primary, secondary)
- `target` - цель (_self, _blank)

#### Иконка
```
[icon name="check" size="24"]
```

**Параметры:**
- `name` - название иконки (check, phone, email, location)
- `size` - размер в пикселях (по умолчанию: 24)

### 6. Настройки темы (Customizer)

#### Контакты
- Телефон
- Email
- Адрес

#### Социальные сети
- WhatsApp (ссылка)
- Telegram (ссылка)
- VK (ссылка)

#### Главный экран
- Заголовок
- Статистика 1 (число и подпись)
- Статистика 2 (число и подпись)
- Статистика 3 (число и подпись)
- Изображение Hero (опционально)

### 7. Обращения с сайта

Все сообщения с формы обратной связи сохраняются в админке:
**Обращения** - список всех обращений с сайта

**Информация:**
- Имя отправителя
- Email
- Телефон
- Текст сообщения
- Дата отправки

## Использование в коде

### Проверка типа страницы
```php
if (atk_ved_is_landing_page()) {
    // Код для лендинга
}
```

### Получение времени чтения
```php
$reading_time = atk_ved_get_reading_time(get_the_content());
echo $reading_time . ' мин. чтения';
```

### Цвет аватара по имени
```php
$color = atk_ved_get_avatar_color('Иван Петров');
echo '<div style="background: ' . $color . '">ИП</div>';
```

### Безопасный вывод HTML
```php
echo atk_ved_kses_post($content);
```

## Структура файлов

```
wp-content/themes/atk-ved/
├── inc/
│   ├── custom-post-types.php  # Кастомные типы записей
│   ├── helpers.php             # Вспомогательные функции
│   └── ajax-handlers.php       # AJAX обработчики
├── template-parts/
│   └── hero-section.php        # Шаблон Hero секции
├── 404.php                     # Страница ошибки 404
├── search.php                  # Страница поиска
├── searchform.php              # Форма поиска
└── FEATURES.md                 # Эта документация
```

## Примеры использования

### Динамический вывод услуг
```php
<div class="services-grid">
    <?php
    $services = atk_ved_get_services(6);
    if ($services->have_posts()) {
        while ($services->have_posts()) {
            $services->the_post();
            ?>
            <div class="service-card">
                <?php if (has_post_thumbnail()) : ?>
                    <?php the_post_thumbnail('medium'); ?>
                <?php endif; ?>
                <h3><?php the_title(); ?></h3>
                <p><?php the_excerpt(); ?></p>
            </div>
            <?php
        }
    }
    wp_reset_postdata();
    ?>
</div>
```

### Динамический вывод отзывов
```php
<div class="reviews-grid">
    <?php
    $reviews = atk_ved_get_reviews(4);
    if ($reviews->have_posts()) {
        while ($reviews->have_posts()) {
            $reviews->the_post();
            $author = get_post_meta(get_the_ID(), '_review_author_name', true);
            $rating = get_post_meta(get_the_ID(), '_review_rating', true);
            $initials = atk_ved_get_initials($author);
            ?>
            <div class="review-card">
                <div class="review-avatar"><?php echo esc_html($initials); ?></div>
                <h4><?php echo esc_html($author); ?></h4>
                <?php echo atk_ved_get_rating_stars($rating); ?>
                <p><?php the_content(); ?></p>
            </div>
            <?php
        }
    }
    wp_reset_postdata();
    ?>
</div>
```

### Динамический вывод FAQ
```php
<div class="faq-list">
    <?php
    $faq = atk_ved_get_faq();
    if ($faq->have_posts()) {
        while ($faq->have_posts()) {
            $faq->the_post();
            ?>
            <div class="faq-item">
                <div class="faq-question"><?php the_title(); ?></div>
                <div class="faq-answer"><?php the_content(); ?></div>
            </div>
            <?php
        }
    }
    wp_reset_postdata();
    ?>
</div>
```

## Советы по использованию

1. **Используйте кастомные типы записей** для управления контентом через админку
2. **Используйте вспомогательные функции** для единообразного форматирования
3. **Используйте AJAX** для динамической загрузки контента
4. **Используйте шорткоды** в редакторе WordPress
5. **Настраивайте тему** через Customizer для быстрых изменений

## Обновление с версии 1.1

Все новые функции обратно совместимы. Просто обновите файлы темы.

## Поддержка

Для вопросов и предложений обращайтесь к разработчику темы.
