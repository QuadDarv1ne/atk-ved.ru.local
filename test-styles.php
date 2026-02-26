<?php
/**
 * Тест подключения стилей
 */

// Минимальная эмуляция WordPress
define('ABSPATH', true);

// Проверяем загрузку классов
require __DIR__ . '/wp-content/themes/atk-ved/vendor/autoload.php';

echo "=== Проверка классов ===" . PHP_EOL;
echo "Base: " . (class_exists('ATKVed\Base') ? 'OK' : 'NOT FOUND') . PHP_EOL;
echo "Enqueue: " . (class_exists('ATKVed\Enqueue') ? 'OK' : 'NOT FOUND') . PHP_EOL;
echo "Loader: " . (class_exists('ATKVed\Loader') ? 'OK' : 'NOT FOUND') . PHP_EOL;
echo "Theme: " . (class_exists('ATKVed\Theme') ? 'OK' : 'NOT FOUND') . PHP_EOL;

// Проверяем константы
echo PHP_EOL . "=== Проверка констант ===" . PHP_EOL;
echo "ATK_VED_VERSION: " . (defined('ATK_VED_VERSION') ? ATK_VED_VERSION : 'NOT DEFINED') . PHP_EOL;

// Проверяем файлы CSS
echo PHP_EOL . "=== Проверка CSS файлов ===" . PHP_EOL;
$css_files = [
    '/wp-content/themes/atk-ved/css/design-tokens.css',
    '/wp-content/themes/atk-ved/css/modern-design.css',
    '/wp-content/themes/atk-ved/style.css',
    '/wp-content/themes/atk-ved/css/ui-components.css',
];

foreach ($css_files as $file) {
    $path = __DIR__ . $file;
    echo basename($file) . ": " . (file_exists($path) ? 'EXISTS' : 'NOT FOUND') . PHP_EOL;
}

echo PHP_EOL . "=== Готово ===" . PHP_EOL;
