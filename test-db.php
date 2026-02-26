<?php
/**
 * Database Connection Test Script
 * Проверка подключения к базе данных
 */

echo "╔═══════════════════════════════════════════════════════════╗\n";
echo "║         ATK VED - Проверка подключения к БД               ║\n";
echo "╚═══════════════════════════════════════════════════════════╝\n\n";

// Load .env file
$env_file = __DIR__ . '/.env';
$env = [];

if (file_exists($env_file)) {
    echo "✓ .env файл найден\n\n";
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $env[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
        }
    }
} else {
    echo "✗ .env файл не найден\n";
    exit(1);
}

// Database settings
$db_host = $env['WP_DB_HOST'] ?? 'localhost';
$db_name = $env['WP_DB_NAME'] ?? 'atk_ved';
$db_user = $env['WP_DB_USER'] ?? 'root';
$db_pass = $env['WP_DB_PASSWORD'] ?? '';

echo "Параметры подключения:\n";
echo "  Хост:     $db_host\n";
echo "  База:     $db_name\n";
echo "  Пользователь: $db_user\n";
echo "  Пароль:   " . (empty($db_pass) ? '(пустой)' : '***') . "\n\n";

// Try to connect
echo "Проверка подключения...\n";

// Parse host and port
$host = $db_host;
$port = 3306;
if (strpos($db_host, ':') !== false) {
    list($host, $port) = explode(':', $db_host);
}

echo "  Хост: $host\n";
echo "  Порт: $port\n\n";

// Test connection
try {
    $mysqli = new mysqli($host, $db_user, $db_pass, null, (int)$port);
    
    if ($mysqli->connect_error) {
        echo "✗ Ошибка подключения: " . $mysqli->connect_error . "\n";
        echo "\nВозможные решения:\n";
        echo "  1. Запустите MySQL в OpenServer (треугольник → Запустить)\n";
        echo "  2. Проверьте порт MySQL (по умолчанию 3306)\n";
        echo "  3. Создайте базу данных: CREATE DATABASE $db_name;\n";
        exit(1);
    }
    
    echo "✓ Подключение к MySQL успешно!\n\n";
    
    // Check if database exists
    $result = $mysqli->query("SHOW DATABASES LIKE '$db_name'");
    if ($result && $result->num_rows > 0) {
        echo "✓ База данных '$db_name' существует\n\n";
        
        // Try to select database
        $mysqli->select_db($db_name);
        echo "✓ Выбор базы данных успешен\n\n";
        
        // Check tables
        $result = $mysqli->query("SHOW TABLES");
        if ($result && $result->num_rows > 0) {
            echo "✓ В базе данных таблиц: " . $result->num_rows . "\n\n";
        } else {
            echo "⚠ База данных пуста (требуется установка WordPress)\n\n";
        }
    } else {
        echo "⚠ База данных '$db_name' не существует\n\n";
        echo "Создайте базу данных:\n";
        echo "  1. Откройте phpMyAdmin\n";
        echo "  2. Нажмите 'Базы данных'\n";
        echo "  3. Введите имя: $db_name\n";
        echo "  4. Кодировка: utf8mb4_unicode_ci\n";
        echo "  5. Нажмите 'Создать'\n\n";
    }
    
    $mysqli->close();
    
} catch (mysqli_sql_exception $e) {
    echo "✗ Ошибка: " . $e->getMessage() . "\n\n";
    echo "Возможные причины:\n";
    echo "  1. MySQL не запущен\n";
    echo "  2. Неверный порт (по умолчанию 3306)\n";
    echo "  3. Неверный пользователь или пароль\n\n";
    echo "Решение:\n";
    echo "  1. Откройте OpenServer (флажок в трее)\n";
    echo "  2. Нажмите 'Запустить' или 'Перезапустить'\n";
    echo "  3. Дождитесь зелёного флажка\n";
    echo "  4. Обновите страницу сайта\n";
    exit(1);
}

echo "═══════════════════════════════════════════════════════════\n";
echo "✓ Все проверки пройдены успешно!\n";
echo "═══════════════════════════════════════════════════════════\n";
