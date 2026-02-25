<?php
/**
 * Скрипт загрузки фотографий v3.1
 * 
 * @package ATK_VED
 * @subpackage Image_Downloader
 */

// Для использования в браузере
if (php_sapi_name() !== 'cli') {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Загрузка фотографий</title></head><body>';
    echo '<h1>Загрузка фотографий для АТК ВЭД</h1>';
}

// Конфигурация загрузки
$download_config = array(
    'china' => array(
        'urls' => array(
            'https://images.unsplash.com/photo-1544472843-723d73e5c7e3?w=1200&h=800&fit=crop', // Китайские фабрики
            'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=1200&h=800&fit=crop', // Производство
            'https://images.unsplash.com/photo-1581092921461-eab62e97a780?w=1200&h=800&fit=crop', // Шанхай
            'https://images.unsplash.com/photo-1566577739112-5180d4bf9390?w=1200&h=800&fit=crop', // Китайские товары
        ),
        'folder' => 'china',
        'prefix' => 'china-factory'
    ),
    'logistics' => array(
        'urls' => array(
            'https://images.unsplash.com/photo-1566577134770-3d85bb5a8b07?w=1200&h=800&fit=crop', // Логистический центр
            'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=1200&h=800&fit=crop', // Склад
            'https://images.unsplash.com/photo-1556623143-6f30a0a59c91?w=1200&h=800&fit=crop', // Логистика
            'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=1200&h=800&fit=crop', // Распределительный центр
        ),
        'folder' => 'logistics',
        'prefix' => 'logistics-center'
    ),
    'delivery' => array(
        'urls' => array(
            'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=1200&h=800&fit=crop', // Грузовики
            'https://images.unsplash.com/photo-1566577134770-3d85bb5a8b07?w=1200&h=800&fit=crop', // Доставка
            'https://images.unsplash.com/photo-1581092921461-eab62e97a780?w=1200&h=800&fit=crop', // Транспорт
            'https://images.unsplash.com/photo-1544472843-723d73e5c7e3?w=1200&h=800&fit=crop', // Посылки
        ),
        'folder' => 'delivery',
        'prefix' => 'delivery-truck'
    )
);

// Создание папок
foreach ($download_config as $category => $config) {
    $folder_path = __DIR__ . '/downloads/' . $config['folder'];
    if (!file_exists($folder_path)) {
        mkdir($folder_path, 0755, true);
        if (php_sapi_name() !== 'cli') {
            echo "<p>Создана папка: {$config['folder']}</p>";
        }
    }
}

// Функция загрузки изображения
function download_image($url, $filename) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'ATK-VED-Image-Downloader/3.1');
    
    $image_data = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code === 200 && $image_data) {
        file_put_contents($filename, $image_data);
        return true;
    }
    
    return false;
}

// Загрузка фотографий
foreach ($download_config as $category => $config) {
    if (php_sapi_name() !== 'cli') {
        echo "<h2>Категория: " . ucfirst($category) . "</h2>";
    }
    
    foreach ($config['urls'] as $index => $url) {
        $filename = __DIR__ . '/downloads/' . $config['folder'] . '/' . $config['prefix'] . '-' . ($index + 1) . '.jpg';
        
        if (php_sapi_name() !== 'cli') {
            echo "<p>Загрузка: " . basename($filename) . "... ";
        }
        
        if (download_image($url, $filename)) {
            if (php_sapi_name() !== 'cli') {
                echo "<span style='color: green;'>✓ Успешно</span></p>";
            }
        } else {
            if (php_sapi_name() !== 'cli') {
                echo "<span style='color: red;'>✗ Ошибка</span></p>";
            }
        }
    }
}

if (php_sapi_name() !== 'cli') {
    echo '<h2>Загрузка завершена!</h2>';
    echo '<p>Фотографии сохранены в папке: <code>wp-content/themes/atk-ved/images/downloads/</code></p>';
    echo '<p>Структура:</p>';
    echo '<ul>';
    echo '<li>downloads/china/ - фотографии Китая</li>';
    echo '<li>downloads/logistics/ - фотографии логистики</li>';
    echo '<li>downloads/delivery/ - фотографии доставки</li>';
    echo '</ul>';
    echo '</body></html>';
}
?>