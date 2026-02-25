# АТК ВЭД - Лендинг для WordPress

Сайт компании по поставкам товаров из Китая для маркетплейсов.

## Быстрый запуск

1. Запустите OpenServer
2. Создайте БД `atk_ved` через phpMyAdmin: `open-phpmyadmin.bat`
3. Откройте сайт: `open-site.bat` или http://atk-ved.ru.local/
4. Установите WordPress (язык: Русский, название: АТК ВЭД)
5. Активируйте тему "АТК ВЭД" (Внешний вид → Темы)
6. Установите плагин Contact Form 7
7. Создайте меню с якорями: #services, #delivery, #steps, #faq, #reviews, #contact
8. Настройте контакты (Внешний вид → Настроить → Контакты)
9. Добавьте изображения в `wp-content/themes/atk-ved/images/`:
   - hero-containers.jpg
   - container.jpg

## Структура

```
wp-content/themes/atk-ved/
├── style.css       # Стили
├── functions.php   # Функции WP
├── index.php       # Лендинг
├── header.php      # Шапка
├── footer.php      # Подвал
└── js/main.js      # JavaScript
```

## Секции

Hero → Услуги → Доставка → Этапы → FAQ → Отзывы → Контакты → Карта

## Документация

См. папку `docs/`

## Адреса

- Сайт: http://atk-ved.ru.local/
- Админка: http://atk-ved.ru.local/wp-admin/
