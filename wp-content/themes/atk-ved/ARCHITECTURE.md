# Архитектура темы ATK VED v3.5.0

## Обзор

Тема построена на современной архитектуре с разделением логики и представления, следуя принципам SOLID и стандарту PSR-12.

## Структура директорий

```
wp-content/themes/atk-ved/
├── src/                        # Исходный код (PHP классы)
│   ├── Contracts/             # Интерфейсы
│   │   ├── ModuleInterface.php
│   │   └── ViewInterface.php
│   ├── Core/                  # Ядро системы
│   │   ├── Container.php      # DI контейнер
│   │   └── View.php           # Система представлений
│   ├── Services/              # Бизнес-логика
│   │   └── CompanyService.php
│   ├── Base.php               # Базовый класс
│   ├── Theme.php              # Главный класс темы
│   ├── Setup.php              # Настройка темы
│   ├── Enqueue.php            # Подключение ресурсов
│   ├── Ajax.php               # AJAX обработчики
│   ├── Shortcodes.php         # Шорткоды
│   └── Customizer.php         # Настройки темы
├── views/                      # Представления (шаблоны)
│   └── partials/              # Частичные представления
│       ├── hero.php
│       ├── service-card.php
│       └── advantage-card.php
├── inc/                        # Дополнительные модули
├── css/                        # Стили
├── js/                         # Скрипты
├── assets/                     # Статические ресурсы
└── tests/                      # Тесты

```

## Принципы архитектуры

### 1. Разделение ответственности (SRP)

Каждый класс отвечает за одну задачу:
- `Theme` - инициализация и координация
- `CompanyService` - работа с данными компании
- `View` - рендеринг представлений
- `Container` - управление зависимостями

### 2. Dependency Injection

Используется DI контейнер для управления зависимостями:

```php
// Регистрация сервиса
$container->register('company', function() {
    return new CompanyService();
});

// Получение сервиса
$company = $container->get('company');
```

### 3. Разделение логики и представления

**Логика (Controller):**
```php
// src/Services/CompanyService.php
public function getInfo(): array
{
    return [
        'name' => 'АТК ВЭД',
        'years' => 5,
        // ...
    ];
}
```

**Представление (View):**
```php
// views/partials/hero.php
<h1><?php echo View::escape($data['title']); ?></h1>
```

### 4. PSR-12 Coding Standard

Весь код следует стандарту PSR-12:
- Строгая типизация (`declare(strict_types=1)`)
- Type hints для всех параметров и возвращаемых значений
- camelCase для методов
- PascalCase для классов
- Документация PHPDoc

## Использование

### Создание нового сервиса

```php
namespace ATKVed\Services;

final class MyService
{
    public function doSomething(): string
    {
        return 'result';
    }
}
```

### Регистрация в контейнере

```php
// src/Theme.php
$this->container->register('myService', function() {
    return new MyService();
});
```

### Использование в коде

```php
$service = Theme::getInstance()->get('myService');
$result = $service->doSomething();
```

### Создание представления

```php
// views/partials/my-component.php
<?php
declare(strict_types=1);

use ATKVed\Core\View;

?>

<div class="my-component">
    <h2><?php echo View::escape($data['title']); ?></h2>
    <p><?php echo View::escape($data['description']); ?></p>
</div>
```

### Рендеринг представления

```php
use ATKVed\Core\View;

$view = new View(Base::viewsDir() . '/partials/my-component');
echo $view->render([
    'title' => 'Заголовок',
    'description' => 'Описание',
]);
```

## Стандарты кодирования

### PHP

- **Стандарт:** PSR-12
- **Проверка:** `composer phpcs`
- **Исправление:** `composer phpcbf`
- **Анализ:** `composer phpstan`

### JavaScript

- **Стандарт:** ES6+
- **Формат:** Prettier
- **Линтер:** ESLint

### CSS

- **Методология:** BEM
- **Препроцессор:** Нативный CSS с переменными
- **Линтер:** Stylelint

## Тестирование

### Unit тесты

```bash
composer test
```

### Статический анализ

```bash
composer phpstan
```

### Code Style

```bash
composer phpcs
```

## Миграция со старой архитектуры

### Было (спагетти-код):

```php
<?php
// front-page.php
$company_name = 'АТК ВЭД';
$years = 5;
?>
<h1><?php echo $company_name; ?></h1>
<p>Работаем <?php echo $years; ?> лет</p>
```

### Стало (разделение логики):

```php
<?php
// Логика (Service)
$companyService = Theme::getInstance()->get('company');
$data = $companyService->getInfo();

// Представление (View)
$view = new View(Base::viewsDir() . '/partials/hero');
$view->display($data);
```

## Преимущества новой архитектуры

1. **Читаемость** - код легче понять и поддерживать
2. **Тестируемость** - легко писать unit тесты
3. **Переиспользование** - компоненты можно использовать повторно
4. **Масштабируемость** - легко добавлять новые функции
5. **Безопасность** - централизованное экранирование данных
6. **Производительность** - ленивая загрузка и кэширование

## Best Practices

### 1. Всегда используйте Type Hints

```php
// ✅ Хорошо
public function getName(): string
{
    return $this->name;
}

// ❌ Плохо
public function getName()
{
    return $this->name;
}
```

### 2. Экранируйте вывод

```php
// ✅ Хорошо
echo View::escape($data['title']);

// ❌ Плохо
echo $data['title'];
```

### 3. Используйте DI контейнер

```php
// ✅ Хорошо
$service = $container->get('company');

// ❌ Плохо
$service = new CompanyService();
```

### 4. Разделяйте логику и представление

```php
// ✅ Хорошо
// Controller
$data = $service->getData();
// View
$view->display($data);

// ❌ Плохо
// Всё в одном файле
```

## Дальнейшее развитие

- [ ] Добавить Repository pattern для работы с БД
- [ ] Реализовать Event Dispatcher
- [ ] Добавить Middleware для обработки запросов
- [ ] Создать Command Bus для бизнес-логики
- [ ] Добавить кэширование на уровне сервисов
