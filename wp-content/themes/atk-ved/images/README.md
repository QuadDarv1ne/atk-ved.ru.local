# Система изображений АТК ВЭД

## Структура папок

```
images/
├── hero/           # Изображения для главного экрана
├── sections/       # Изображения для секций
├── services/       # Изображения услуг
├── gallery/        # Галерея изображений
├── placeholders/   # Заглушки для отсутствующих изображений
├── logo/          # Логотипы
└── icons/         # Иконки
```

## Рекомендуемые изображения

### Hero секция
- `hero-main.jpg` (1920x1080) - главное изображение
- `hero-mobile.jpg` (768x1024) - для мобильных устройств

### Услуги
- `logistics-service.jpg` (800x600) - логистика
- `delivery-service.jpg` (800x600) - доставка
- `quality-service.jpg` (800x600) - контроль качества

### Секции
- `china-section.jpg` (1200x800) - Китай
- `logistics-section.jpg` (1200x800) - Логистика
- `delivery-section.jpg` (1200x800) - Доставка

### Галерея
- `china-factory-1.jpg` (1200x800) - Китайские фабрики
- `china-factory-2.jpg` (1200x800) - Производство
- `logistics-center-1.jpg` (1200x800) - Логистические центры
- `logistics-center-2.jpg` (1200x800) - Склады
- `delivery-truck-1.jpg` (1200x800) - Грузовые автомобили
- `delivery-truck-2.jpg` (1200x800) - Транспорт

## Требования к изображениям

- **Формат**: JPG/WebP
- **Качество**: 85-90%
- **Разрешение**: согласно спецификациям выше
- **Оптимизация**: сжатие без потерь качества

## Использование в коде

```php
// Получение изображения
echo atk_ved_get_image('hero', 'hero-main', array(
    'class' => 'hero-image',
    'alt' => 'Китайская логистика'
));

// Получение галереи
$gallery = atk_ved_get_gallery('china-logistics');
foreach ($gallery as $image) {
    echo '<img src="' . $image['url'] . '" alt="' . $image['alt'] . '">';
}
```

## Оптимизация

Система автоматически:
- Добавляет lazy loading
- Генерирует WebP версии
- Оптимизирует размеры
- Обеспечивает fallback для старых браузеров