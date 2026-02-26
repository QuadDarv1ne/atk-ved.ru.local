# Миграция на v3.5.0

## Обзор изменений

Версия 3.5.0 включает значительный рефакторинг архитектуры с переходом на современные стандарты PHP.

## Основные изменения

### 1. PSR-12 Coding Standard

Весь код переписан в соответствии со стандартом PSR-12:
- Строгая типизация
- Type hints для всех методов
- camelCase для методов (было snake_case)

### 2. Dependency Injection Container

Добавлен DI контейнер для управления зависимостями.

**Было:**
```php
$company_info = Theme::get_company_info();
```

**Стало:**
```php
$companyService = Theme::getInstance()->get('company');
$companyInfo = $companyService->getInfo();
```

### 3. Разделение логики и представления

Логика вынесена в сервисы, представления - в отдельные файлы.

**Было:**
```php
// front-page.php
<?php
$name = 'АТК ВЭД';
?>
<h1><?php echo $name; ?></h1>
```

**Стало:**
```php
// Controller
$data = $companyService->getInfo();

// View
$view = new View(Base::viewsDir() . '/partials/hero');
$view->display($data);
```

## Обратная совместимость

### Сохранены статические методы

Для обратной совместимости сохранены статические методы:

```php
// Работает как раньше
Theme::getCompanyInfo();
Theme::getSocialLinks();
Theme::getTrustBadges();
```

### Изменения в именовании

| Старое название | Новое название |
|----------------|----------------|
| `get_company_info()` | `getCompanyInfo()` |
| `get_social_links()` | `getSocialLinks()` |
| `get_trust_badges()` | `getTrustBadges()` |
| `is_safe_url()` | `isSafeUrl()` |

## Шаги миграции

### 1. Обновление Composer

```bash
cd wp-content/themes/atk-ved
composer install --no-dev --optimize-autoloader
```

### 2. Проверка кода

```bash
# Проверка стиля кода
composer phpcs

# Статический анализ
composer phpstan
```

### 3. Обновление пользовательского кода

Если вы использовали функции темы в дочерней теме или плагинах:

**Старый код:**
```php
$info = Theme::get_company_info();
```

**Новый код (рекомендуется):**
```php
$companyService = Theme::getInstance()->get('company');
$info = $companyService->getInfo();
```

**Или (обратная совместимость):**
```php
$info = Theme::getCompanyInfo();
```

### 4. Обновление шаблонов

Если вы переопределяли шаблоны в дочерней теме:

**Старый шаблон:**
```php
<?php
$company = Theme::get_company_info();
?>
<h1><?php echo $company['name']; ?></h1>
```

**Новый шаблон:**
```php
<?php
use ATKVed\Core\View;

$company = Theme::getCompanyInfo();
?>
<h1><?php echo View::escape($company['name']); ?></h1>
```

## Новые возможности

### 1. DI Container

Регистрация собственных сервисов:

```php
add_action('after_setup_theme', function() {
    $theme = Theme::getInstance();
    $theme->get('container')->register('myService', function() {
        return new MyService();
    });
});
```

### 2. View System

Использование системы представлений:

```php
use ATKVed\Core\View;

$view = new View(Base::viewsDir() . '/partials/my-component');
echo $view->render([
    'title' => 'Заголовок',
    'description' => 'Описание',
]);
```

### 3. Type Safety

Все методы теперь с type hints:

```php
public function getName(): string
{
    return $this->name;
}

public function setName(string $name): void
{
    $this->name = $name;
}
```

## Устранение проблем

### Ошибка: Class not found

**Проблема:** `Class 'ATKVed\Theme' not found`

**Решение:**
```bash
composer dump-autoload --optimize
```

### Ошибка: Call to undefined method

**Проблема:** `Call to undefined method Theme::get_company_info()`

**Решение:** Используйте новое имя метода:
```php
Theme::getCompanyInfo()
```

### Ошибка: Type error

**Проблема:** `TypeError: Return value must be of type string`

**Решение:** Убедитесь, что возвращаете правильный тип данных.

## Тестирование

### 1. Проверка функциональности

- [ ] Главная страница загружается
- [ ] Меню работает
- [ ] Формы отправляются
- [ ] Калькулятор работает
- [ ] Отслеживание грузов работает

### 2. Проверка производительности

```bash
# Lighthouse
npm run lighthouse

# PageSpeed Insights
# https://pagespeed.web.dev/
```

### 3. Проверка безопасности

```bash
# WPScan
wpscan --url https://your-site.com
```

## Откат на предыдущую версию

Если возникли проблемы:

```bash
cd wp-content/themes/atk-ved
git checkout v3.4.0
composer install --no-dev --optimize-autoloader
```

## Поддержка

При возникновении проблем:

1. Проверьте [ARCHITECTURE.md](ARCHITECTURE.md)
2. Проверьте [CHANGELOG.md](CHANGELOG.md)
3. Создайте issue на GitHub
4. Напишите на support@atk-ved.ru

## Дальнейшие планы

- v3.6.0: Repository pattern для работы с БД
- v3.7.0: Event Dispatcher
- v3.8.0: Middleware system
- v4.0.0: Полный переход на современную архитектуру
