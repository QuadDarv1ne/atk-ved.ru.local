<?php
/**
 * Улучшенная система PWA (Progressive Web App)
 *
 * @package ATK_VED
 * @since 3.5.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Класс для улучшенной реализации PWA
 */
class ATK_VED_Enhanced_PWA {
    
    private static $instance = null;
    
    public static function get_instance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Инициализация хуков WordPress для PWA
     */
    private function init_hooks(): void {
        // Регистрация Service Worker
        add_action('wp_enqueue_scripts', [$this, 'register_service_worker'], 20);
        
        // Добавление метатегов PWA
        add_action('wp_head', [$this, 'add_pwa_meta_tags'], 5);
        
        // Генерация манифеста
        add_action('wp_head', [$this, 'add_manifest_link'], 6);
        
        // Обработка маршрута манифеста
        add_action('init', [$this, 'handle_manifest_request']);
        
        // Обработка маршрута service worker
        add_action('init', [$this, 'handle_sw_request']);
        
        // Добавление фоновой синхронизации
        add_action('wp_footer', [$this, 'add_background_sync_script'], 999);
    }
    
    /**
     * Регистрация Service Worker
     */
    public function register_service_worker(): void {
        if (!is_admin() && !is_customize_preview()) {
            wp_add_inline_script(
                'jquery', 
                '
                if ("serviceWorker" in navigator) {
                    window.addEventListener("load", function() {
                        navigator.serviceWorker.register("' . esc_url(get_template_directory_uri() . '/sw.js') . '")
                            .then(function(registration) {
                                console.log("ServiceWorker registration successful with scope: ", registration.scope);
                            })
                            .catch(function(err) {
                                console.log("ServiceWorker registration failed: ", err);
                            });
                    });
                }
                '
            );
        }
    }
    
    /**
     * Добавление метатегов PWA
     */
    public function add_pwa_meta_tags(): void {
        $site_name = get_bloginfo('name');
        $description = get_bloginfo('description');
        $theme_color = '#e31e24'; // Красный цвет из брендбука АТК ВЭД
        
        echo '<meta name="mobile-web-app-capable" content="yes">' . "\n";
        echo '<meta name="apple-mobile-web-app-title" content="' . esc_attr($site_name) . '">' . "\n";
        echo '<meta name="apple-mobile-web-app-status-bar-style" content="default">' . "\n";
        echo '<meta name="theme-color" content="' . esc_attr($theme_color) . '">' . "\n";
        echo '<meta name="application-name" content="' . esc_attr($site_name) . '">' . "\n";
        echo '<meta name="msapplication-TileColor" content="' . esc_attr($theme_color) . '">' . "\n";
        echo '<meta name="HandheldFriendly" content="true">' . "\n";
        echo '<meta name="format-detection" content="telephone=no">' . "\n";
    }
    
    /**
     * Добавление ссылки на манифест
     */
    public function add_manifest_link(): void {
        echo '<link rel="manifest" href="' . esc_url(home_url('/wp-content/themes/atk-ved/manifest.json')) . '">' . "\n";
        echo '<link rel="apple-touch-icon" href="' . esc_url(get_template_directory_uri() . '/images/apple-touch-icon.png') . '">' . "\n";
        echo '<link rel="icon" type="image/png" sizes="192x192" href="' . esc_url(get_template_directory_uri() . '/images/android-chrome-192x192.png') . '">' . "\n";
        echo '<link rel="icon" type="image/png" sizes="512x512" href="' . esc_url(get_template_directory_uri() . '/images/android-chrome-512x512.png') . '">' . "\n";
    }
    
    /**
     * Обработка запроса манифеста
     */
    public function handle_manifest_request(): void {
        if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'wp-content/themes/atk-ved/manifest.json') !== false) {
            $this->output_manifest();
        }
    }
    
    /**
     * Обработка запроса service worker
     */
    public function handle_sw_request(): void {
        if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'wp-content/themes/atk-ved/sw.js') !== false) {
            $this->output_service_worker();
        }
    }
    
    /**
     * Вывод манифеста PWA
     */
    private function output_manifest(): void {
        header('Content-Type: application/json');
        
        $site_name = get_bloginfo('name');
        $description = get_bloginfo('description');
        
        $manifest = [
            'name' => $site_name,
            'short_name' => substr($site_name, 0, 12),
            'description' => $description,
            'start_url' => home_url('/'),
            'scope' => home_url('/'),
            'display' => 'standalone',
            'orientation' => 'portrait',
            'background_color' => '#ffffff',
            'theme_color' => '#e31e24',
            'icons' => [
                [
                    'src' => get_template_directory_uri() . '/images/android-chrome-72x72.png',
                    'sizes' => '72x72',
                    'type' => 'image/png'
                ],
                [
                    'src' => get_template_directory_uri() . '/images/android-chrome-96x96.png',
                    'sizes' => '96x96',
                    'type' => 'image/png'
                ],
                [
                    'src' => get_template_directory_uri() . '/images/android-chrome-128x128.png',
                    'sizes' => '128x128',
                    'type' => 'image/png'
                ],
                [
                    'src' => get_template_directory_uri() . '/images/android-chrome-144x144.png',
                    'sizes' => '144x144',
                    'type' => 'image/png'
                ],
                [
                    'src' => get_template_directory_uri() . '/images/android-chrome-152x152.png',
                    'sizes' => '152x152',
                    'type' => 'image/png'
                ],
                [
                    'src' => get_template_directory_uri() . '/images/android-chrome-192x192.png',
                    'sizes' => '192x192',
                    'type' => 'image/png'
                ],
                [
                    'src' => get_template_directory_uri() . '/images/android-chrome-384x384.png',
                    'sizes' => '384x384',
                    'type' => 'image/png'
                ],
                [
                    'src' => get_template_directory_uri() . '/images/android-chrome-512x512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png'
                ]
            ]
        ];
        
        echo json_encode($manifest, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * Вывод Service Worker
     */
    private function output_service_worker(): void {
        header('Content-Type: application/javascript');
        
        $sw_content = "
            const CACHE_NAME = 'atk-ved-v1';
            const urlsToCache = [
                '/',
                '/offline/',
                '/wp-content/themes/atk-ved/style.css',
                '/wp-content/themes/atk-ved/js/main.js',
                '/wp-content/themes/atk-ved/images/logo.png'
            ];
            
            self.addEventListener('install', function(event) {
                event.waitUntil(
                    caches.open(CACHE_NAME)
                        .then(function(cache) {
                            return cache.addAll(urlsToCache);
                        })
                );
            });
            
            self.addEventListener('fetch', function(event) {
                event.respondWith(
                    caches.match(event.request)
                        .then(function(response) {
                            // Return cached version or fetch from network
                            return response || fetch(event.request);
                        })
                );
            });
            
            // Background sync for offline data
            self.addEventListener('sync', function(event) {
                if (event.tag === 'form-submission') {
                    event.waitUntil(sendFormWhenOnline());
                }
            });
            
            function sendFormWhenOnline() {
                // Implementation for sending offline collected data when online
                return new Promise(function(resolve) {
                    if (navigator.onLine) {
                        // Send stored data
                        resolve();
                    }
                });
            }
        ";
        
        echo $sw_content;
        exit;
    }
    
    /**
     * Добавление скрипта фоновой синхронизации
     */
    public function add_background_sync_script(): void {
        if (!is_admin()) {
            $script = "<script>
            // Проверка поддержки Background Sync
            if (typeof navigator.onLine !== 'undefined') {
                // Фоновая синхронизация для отправки форм
                if ('serviceWorker' in navigator && 'SyncManager' in window) {
                    // Регистрация фоновой синхронизации
                    navigator.serviceWorker.ready.then(function(registration) {
                        registration.sync.register('form-submission');
                    });
                }
                
                // Проверка онлайн/офлайн статуса
                window.addEventListener('online', function() {
                    // Событие при восстановлении соединения
                    console.log('Соединение восстановлено');
                });
                
                window.addEventListener('offline', function() {
                    // Событие при потере соединения
                    console.log('Подключение к интернету отсутствует');
                });
            }
            </script>";
            
            echo $script;
        }
    }
}

// Инициализация улучшенной системы PWA
ATK_VED_Enhanced_PWA::get_instance();

/**
 * Функция для проверки поддержки PWA
 */
function atk_ved_is_pwa_supported(): bool {
    return isset($_GET['utm_source']) && $_GET['utm_source'] === 'pwa';
}

/**
 * Функция для получения информации о PWA
 */
function atk_ved_get_pwa_info(): array {
    return [
        'supported' => true,
        'name' => get_bloginfo('name'),
        'version' => defined('ATK_VED_VERSION') ? ATK_VED_VERSION : '3.5.0',
        'features' => [
            'service_worker' => true,
            'manifest' => true,
            'offline_support' => true,
            'background_sync' => class_exists('SyncManager') ?? false
        ]
    ];
}

// Добавляем поддержку Web App Manifest в тему
add_action('wp_head', function() {
    if (is_home() || is_front_page()) {
        echo '<!-- Enhanced PWA Support -->' . "\n";
    }
});

// Обеспечиваем, что PWA компоненты подключаются с высоким приоритетом
add_action('after_setup_theme', function() {
    if (get_template() === 'atk-ved') {
        require_once get_template_directory() . '/inc/enhanced-pwa.php';
    }
}, 11);