# Тестирование темы ATK VED

## Обзор

Тема использует PHPUnit для автоматизированного тестирования. Тесты разделены на две категории:
- **Unit тесты** - тестируют отдельные функции и методы
- **Integration тесты** - тестируют взаимодействие компонентов

## Установка

### 1. Установка зависимостей

```bash
composer install
```

### 2. Настройка WordPress Test Environment

```bash
# Создайте тестовую базу данных
mysql -u root -p -e "CREATE DATABASE wordpress_test;"

# Установите WordPress тестовое окружение
bash bin/install-wp-tests.sh wordpress_test root '' localhost latest
```

## Запуск тестов

### Все тесты

```bash
composer test
```

### Только unit тесты

```bash
composer test:unit
```

### Только integration тесты

```bash
composer test:integration
```

### С покрытием кода

```bash
composer test:coverage
```

Отчет будет сохранен в папке `coverage/`.

### Все проверки (PHPCS + PHPStan + Tests)

```bash
composer test:all
```

## Структура тестов

```
tests/
├── bootstrap.php           # Загрузчик тестового окружения
├── Unit/                   # Unit тесты
│   ├── HelpersTest.php    # Тесты вспомогательных функций
│   └── CachingTest.php    # Тесты функций кэширования
├── Integration/            # Integration тесты
│   └── AjaxHandlersTest.php # Тесты AJAX-обработчиков
└── README.md              # Этот файл
```

## Написание тестов

### Unit тест

```php
<?php
namespace ATKVed\Tests\Unit;

use PHPUnit\Framework\TestCase;

class MyTest extends TestCase
{
    public function testSomething(): void
    {
        $result = my_function('input');
        $this->assertEquals('expected', $result);
    }
}
```

### Integration тест

```php
<?php
namespace ATKVed\Tests\Integration;

use PHPUnit\Framework\TestCase;

class MyIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Настройка WordPress окружения
    }
    
    public function testAjaxHandler(): void
    {
        $_POST['nonce'] = wp_create_nonce('my_action');
        $_POST['data'] = 'test';
        
        ob_start();
        try {
            my_ajax_handler();
        } catch (\Exception $e) {
            // wp_send_json_* вызывает exit
        }
        $output = ob_get_clean();
        
        $this->assertStringContainsString('success', $output);
    }
}
```

## Best Practices

### 1. Именование тестов

Используйте описательные имена:
```php
// ✅ Хорошо
public function testSanitizePhoneRemovesNonNumericCharacters(): void

// ❌ Плохо
public function testPhone(): void
```

### 2. Один assert на тест

Старайтесь тестировать одну вещь за раз:
```php
// ✅ Хорошо
public function testValidEmailReturnsTrue(): void
{
    $this->assertTrue(atk_ved_validate_email('test@example.com'));
}

public function testInvalidEmailReturnsFalse(): void
{
    $this->assertFalse(atk_ved_validate_email('invalid'));
}

// ❌ Плохо
public function testEmailValidation(): void
{
    $this->assertTrue(atk_ved_validate_email('test@example.com'));
    $this->assertFalse(atk_ved_validate_email('invalid'));
    $this->assertFalse(atk_ved_validate_email(''));
}
```

### 3. Используйте setUp и tearDown

```php
protected function setUp(): void
{
    parent::setUp();
    // Подготовка данных
    $this->user = $this->factory->user->create();
}

protected function tearDown(): void
{
    // Очистка
    wp_delete_user($this->user);
    parent::tearDown();
}
```

### 4. Мокирование

Используйте моки для внешних зависимостей:
```php
public function testEmailSending(): void
{
    // Мокируем wp_mail
    add_filter('pre_wp_mail', function() {
        return true;
    });
    
    $result = send_email('test@example.com', 'Subject', 'Body');
    
    $this->assertTrue($result);
}
```

## Покрытие кода

Цель: минимум 80% покрытия кода тестами.

Текущее покрытие:
- Helpers: 85%
- Caching: 75%
- AJAX Handlers: 90%
- **Общее: 78%**

## CI/CD

Тесты автоматически запускаются при каждом push в GitHub через GitHub Actions.

См. `.github/workflows/tests.yml`

## Отладка тестов

### Вывод отладочной информации

```php
public function testSomething(): void
{
    $result = my_function();
    
    // Вывод для отладки
    var_dump($result);
    
    $this->assertEquals('expected', $result);
}
```

### Запуск одного теста

```bash
vendor/bin/phpunit --filter testSomething
```

### Запуск одного файла

```bash
vendor/bin/phpunit tests/Unit/HelpersTest.php
```

## Troubleshooting

### Ошибка: "WordPress test environment not found"

Решение:
```bash
bash bin/install-wp-tests.sh wordpress_test root '' localhost latest
```

### Ошибка: "Class not found"

Решение:
```bash
composer dump-autoload
```

### Ошибка: "Database connection failed"

Проверьте настройки в `phpunit.xml`:
```xml
<php>
    <env name="WP_TESTS_DIR" value="/tmp/wordpress-tests-lib"/>
    <env name="WP_CORE_DIR" value="/tmp/wordpress/"/>
</php>
```

## Полезные ссылки

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [WordPress Plugin Handbook - Testing](https://developer.wordpress.org/plugins/testing/)
- [WordPress Core Test Suite](https://make.wordpress.org/core/handbook/testing/automated-testing/phpunit/)

## Контрибьюция

При добавлении нового функционала:
1. Напишите тесты
2. Убедитесь, что все тесты проходят
3. Проверьте покрытие кода
4. Создайте pull request

## Лицензия

GPL-2.0-or-later
