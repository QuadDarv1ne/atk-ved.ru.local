#!/usr/bin/env php
<?php
/**
 * CSS Minification Script
 * 
 * Минифицирует все CSS файлы в папке css/
 * Создаёт .min.css версии для продакшена
 * 
 * Использование: php scripts/minify-css.php
 * 
 * @package ATK_VED
 */

// Путь к папке с CSS
$css_dir = dirname(__DIR__) . '/css';
$output_dir = $css_dir . '/min';

// Создаём папку для минифицированных файлов
if (!is_dir($output_dir)) {
    mkdir($output_dir, 0755, true);
}

/**
 * Простая минификация CSS
 */
function minify_css($css) {
    // Удаляем комментарии
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    
    // Удаляем пробелы
    $css = str_replace(["\r\n", "\r", "\n", "\t"], '', $css);
    $css = preg_replace('/\s+/', ' ', $css);
    
    // Удаляем пробелы вокруг специальных символов
    $css = str_replace([' {', '{ ', ' }', '} ', ' :', ': ', ' ;', '; ', ' ,', ', '], 
                       ['{', '{', '}', '}', ':', ':', ';', ';', ',', ','], $css);
    
    // Удаляем последнюю точку с запятой в блоке
    $css = preg_replace('/;}/','}',$css);
    
    return trim($css);
}

/**
 * Рекурсивный поиск CSS файлов
 */
function find_css_files($dir) {
    $files = [];
    $items = scandir($dir);
    
    foreach ($items as $item) {
        if ($item === '.' || $item === '..' || $item === 'min') continue;
        
        $path = $dir . '/' . $item;
        
        if (is_dir($path)) {
            $files = array_merge($files, find_css_files($path));
        } elseif (pathinfo($path, PATHINFO_EXTENSION) === 'css' && 
                  strpos($item, '.min.css') === false) {
            $files[] = $path;
        }
    }
    
    return $files;
}

// Находим все CSS файлы
$css_files = find_css_files($css_dir);

echo "Найдено CSS файлов: " . count($css_files) . "\n\n";

$total_original = 0;
$total_minified = 0;

foreach ($css_files as $file) {
    $content = file_get_contents($file);
    $original_size = strlen($content);
    
    $minified = minify_css($content);
    $minified_size = strlen($minified);
    
    // Создаём относительный путь
    $relative_path = str_replace($css_dir . '/', '', $file);
    $output_file = $output_dir . '/' . str_replace('.css', '.min.css', basename($file));
    
    // Сохраняем минифицированный файл
    file_put_contents($output_file, $minified);
    
    $saved = $original_size - $minified_size;
    $percent = round(($saved / $original_size) * 100, 1);
    
    echo "✓ {$relative_path}\n";
    echo "  Оригинал: " . number_format($original_size) . " байт\n";
    echo "  Минифицирован: " . number_format($minified_size) . " байт\n";
    echo "  Сэкономлено: " . number_format($saved) . " байт ({$percent}%)\n\n";
    
    $total_original += $original_size;
    $total_minified += $minified_size;
}

$total_saved = $total_original - $total_minified;
$total_percent = round(($total_saved / $total_original) * 100, 1);

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "ИТОГО:\n";
echo "Оригинал: " . number_format($total_original) . " байт (" . round($total_original/1024, 1) . " KB)\n";
echo "Минифицировано: " . number_format($total_minified) . " байт (" . round($total_minified/1024, 1) . " KB)\n";
echo "Сэкономлено: " . number_format($total_saved) . " байт ({$total_percent}%)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

echo "\nМинифицированные файлы сохранены в: {$output_dir}\n";
