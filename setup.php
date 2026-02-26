#!/usr/bin/env php
<?php
/**
 * ATK VED Theme Setup Script
 *
 * Автоматическая настройка проекта после установки
 *
 * @package ATK_VED
 * @since 3.3.0
 */

declare(strict_types=1);

// =============================================================================
// CONFIGURATION
// =============================================================================

$root_dir = dirname(__DIR__);
$theme_dir = $root_dir . '/wp-content/themes/atk-ved';
$env_file = $root_dir . '/.env';
$env_example = $root_dir . '/.env.example';
$wp_config = $root_dir . '/wp-config.php';

// Colors for output
$COLORS = [
    'reset'  => "\033[0m",
    'red'    => "\033[31m",
    'green'  => "\033[32m",
    'yellow' => "\033[33m",
    'blue'   => "\033[34m",
    'purple' => "\033[35m",
    'cyan'   => "\033[36m",
    'white'  => "\033[37m",
    'bold'   => "\033[1m",
];

// =============================================================================
// HELPER FUNCTIONS
// =============================================================================

function out(string $message, string $color = 'white', bool $newline = true): void
{
    global $COLORS;
    $color_code = $COLORS[$color] ?? $COLORS['white'];
    echo $color_code . $message . $COLORS['reset'];
    if ($newline) {
        echo PHP_EOL;
    }
}

function success(string $message): void
{
    out("✓ $message", 'green');
}

function error(string $message): void
{
    out("✗ $message", 'red');
}

function warning(string $message): void
{
    out("⚠ $message", 'yellow');
}

function info(string $message): void
{
    out("ℹ $message", 'cyan');
}

function header_text(string $message): void
{
    echo PHP_EOL;
    out(str_repeat('=', 60), 'cyan');
    out($message, 'bold', true);
    out(str_repeat('=', 60), 'cyan');
    echo PHP_EOL;
}

function run_command(string $command): bool
{
    out("Executing: $command", 'blue', false);
    echo ' ... ';
    
    $output = [];
    $return_var = 0;
    exec($command, $output, $return_var);
    
    if ($return_var === 0) {
        out('Done', 'green');
        return true;
    }
    
    out('Failed', 'red');
    foreach ($output as $line) {
        out("  $line", 'yellow');
    }
    
    return false;
}

function file_exists_and_writable(string $path): bool
{
    return file_exists($path) && is_writable(dirname($path));
}

function copy_file(string $source, string $dest): bool
{
    if (!file_exists($source)) {
        return false;
    }
    
    return @copy($source, $dest);
}

function is_windows(): bool
{
    return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
}

// =============================================================================
// MAIN SETUP
// =============================================================================

out('', 'reset');
out('╔═══════════════════════════════════════════════════════════╗', 'cyan');
out('║         ATK VED Theme - Setup Wizard                      ║', 'cyan');
out('║         Версия: ' . str_pad(ATK_VED_VERSION, 34) . ' ║', 'cyan');
out('╚═══════════════════════════════════════════════════════════╝', 'cyan');
out('', 'reset');

// Step 1: Check PHP version
header_text('ШАГ 1: Проверка версии PHP');

if (version_compare(PHP_VERSION, ATK_VED_MIN_PHP_VERSION, '>=')) {
    success("PHP версия: " . PHP_VERSION . ' (требуется ' . ATK_VED_MIN_PHP_VERSION . '+)');
} else {
    error("PHP версия: " . PHP_VERSION . ' (требуется ' . ATK_VED_MIN_PHP_VERSION . '+)');
    exit(1);
}

// Step 2: Check .env file
header_text('ШАГ 2: Настройка .env файла');

if (file_exists($env_file)) {
    warning('.env файл уже существует');
    out('  Путь: ' . $env_file, 'white');
} else {
    if (copy_file($env_example, $env_file)) {
        success('.env файл создан из .env.example');
        out('  Путь: ' . $env_file, 'white');
        warning('Не забудьте настроить .env файл под ваше окружение!');
    } else {
        error('Не удалось создать .env файл');
        info('Скопируйте .env.example вручную:');
        
        if (is_windows()) {
            out('  copy .env.example .env', 'yellow');
        } else {
            out('  cp .env.example .env', 'yellow');
        }
    }
}

// Step 3: Check wp-config.php
header_text('ШАГ 3: Проверка wp-config.php');

if (file_exists($wp_config)) {
    success('wp-config.php существует');
    out('  Путь: ' . $wp_config, 'white');
} else {
    error('wp-config.php не найден');
    info('Создайте wp-config.php из wp-config-sample.php');
}

// Step 4: Install Composer dependencies
header_text('ШАГ 4: Установка PHP зависимостей (Composer)');

if (file_exists($root_dir . '/composer.json')) {
    out('Установка зависимостей в корне проекта...', 'blue');
    run_command('composer install --no-interaction --prefer-dist');
    
    out('Установка зависимостей в теме...', 'blue');
    run_command('composer install --no-interaction --prefer-dist --working-dir=' . escapeshellarg($theme_dir));
} else {
    warning('composer.json не найден в корне проекта');
}

// Step 5: Install Node.js dependencies
header_text('ШАГ 5: Установка JavaScript зависимостей (npm)');

if (file_exists($theme_dir . '/package.json')) {
    run_command('npm install --prefix ' . escapeshellarg($theme_dir));
} else {
    warning('package.json не найден');
}

// Step 6: Build assets
header_text('ШАГ 6: Сборка ассетов');

out('Сборка для разработки (dev mode)...', 'blue');
run_command('npm run dev --prefix ' . escapeshellarg($theme_dir));

// Step 7: Check directory permissions
header_text('ШАГ 7: Проверка прав доступа');

$dirs_to_check = [
    $root_dir . '/wp-content/uploads',
    $root_dir . '/wp-content/cache',
    $theme_dir . '/dist',
];

foreach ($dirs_to_check as $dir) {
    if (!file_exists($dir)) {
        @mkdir($dir, 0755, true);
        if (file_exists($dir)) {
            success("Директория создана: $dir");
        } else {
            warning("Не удалось создать директорию: $dir");
        }
    } else {
        success("Директория существует: $dir");
    }
}

// Step 8: Create .gitignore if needed
header_text('ШАГ 8: Проверка .gitignore');

$gitignore_file = $root_dir . '/.gitignore';
if (file_exists($gitignore_file)) {
    success('.gitignore существует');
} else {
    warning('.gitignore не найден');
}

// Final summary
header_text('НАСТРОЙКА ЗАВЕРШЕНА');

out('Следующие шаги:', 'bold', true);
out('1. Настройте .env файл (база данных, URL сайта)', 'white');
out('2. Создайте базу данных для WordPress', 'white');
out('3. Запустите установку WordPress через браузер', 'white');
out('4. Активируйте тему "АТК ВЭД" в админке', 'white');
out('5. Установите необходимые плагины', 'white');
out('', 'reset');

// Quick start commands
out('Полезные команды:', 'bold', true);

if (is_windows()) {
    out('  composer install              # Установить PHP зависимости', 'cyan');
    out('  npm install                   # Установить npm зависимости', 'cyan');
    out('  npm run dev                   # Сборка для разработки', 'cyan');
    out('  npm run build                 # Сборка для продакшена', 'cyan');
    out('  docker-compose up -d          # Запуск Docker контейнеров', 'cyan');
} else {
    out('  composer install              # Установить PHP зависимости', 'cyan');
    out('  npm install                   # Установить npm зависимости', 'cyan');
    out('  npm run dev                   # Сборка для разработки', 'cyan');
    out('  npm run build                 # Сборка для продакшена', 'cyan');
    out('  docker-compose up -d          # Запуск Docker контейнеров', 'cyan');
}

out('', 'reset');
success('Установка завершена успешно!');
out('', 'reset');
