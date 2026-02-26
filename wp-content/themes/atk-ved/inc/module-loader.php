<?php
/**
 * Оптимизированный загрузчик модулей
 * Загружает только необходимые модули в зависимости от контекста
 *
 * @package ATK_VED
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Класс для оптимизированной загрузки модулей
 */
class ATK_VED_Module_Loader {
    
    /**
     * Карта модулей с условиями загрузки
     *
     * @var array
     */
    private static $modules = [
        // Всегда загружаются
        'core' => [
            'always' => true,
            'files' => [
                '/inc/helpers.php',
                '/inc/translations.php',
                '/inc/custom-post-types.php',
            ],
        ],
        
        // Безопасность - всегда
        'security' => [
            'always' => true,
            'files' => [
                '/inc/security.php',
                '/inc/advanced-security.php',
                '/inc/logger.php',
            ],
        ],
        
        // SEO - только фронтенд
        'seo' => [
            'condition' => '!is_admin',
            'files' => [
                '/inc/seo.php',
                '/inc/sitemap.php',
                '/inc/breadcrumbs.php',
            ],
        ],
        
        // Производительность - только фронтенд
        'performance' => [
            'condition' => '!is_admin',
            'files' => [
                '/inc/pwa.php',
                '/inc/enhanced-pwa.php',
                '/inc/enhanced-performance.php',
                '/inc/enhanced-image-optimization.php',
            ],
        ],
        
        // UI компоненты - только фронтенд
        'ui' => [
            'condition' => '!is_admin',
            'files' => [
                '/inc/ui-components.php',
                '/inc/enhanced-ui-components.php',
                '/inc/advanced-ui-components.php',
                '/inc/accessibility-enhancements.php',
            ],
        ],
        
        // Функционал - всегда
        'features' => [
            'always' => true,
            'files' => [
                '/inc/calculator.php',
                '/inc/shipment-tracking.php',
                '/inc/delivery-map.php',
                '/inc/ajax-handlers.php',
            ],
        ],
        
        // REST API - только если используется
        'api' => [
            'condition' => 'defined("REST_REQUEST") && REST_REQUEST',
            'files' => [
                '/inc/rest-api.php',
                '/inc/rest-cache.php',
            ],
        ],
        
        // Секции - только фронтенд
        'sections' => [
            'condition' => '!is_admin',
            'files' => [
                '/inc/new-sections.php',
                '/inc/cta-section.php',
                '/inc/faq-section.php',
                '/inc/reviews-section.php',
                '/inc/process-section.php',
            ],
        ],
        
        // Виджеты - только фронтенд
        'widgets' => [
            'condition' => '!is_admin',
            'files' => [
                '/inc/callback-widget.php',
                '/inc/chat-widget.php',
                '/inc/recaptcha.php',
                '/inc/cookie-banner.php',
            ],
        ],
        
        // Медиа - всегда
        'media' => [
            'always' => true,
            'files' => [
                '/inc/image-manager.php',
                '/inc/stock-photos.php',
                '/inc/stock-photos-integration.php',
                '/inc/webp-converter.php',
            ],
        ],
        
        // Интеграции - условно
        'integrations' => [
            'condition' => 'class_exists("WooCommerce") || class_exists("acf")',
            'files' => [
                '/inc/woocommerce.php',
                '/inc/amocrm.php',
                '/inc/acf-field-groups.php',
                '/inc/acf-options.php',
                '/inc/acf-blocks.php',
            ],
        ],
        
        // Админка - только админ панель
        'admin' => [
            'condition' => 'is_admin',
            'files' => [
                '/inc/admin-dashboard.php',
                '/inc/health-check.php',
                '/inc/demo-import.php',
            ],
        ],
        
        // Маркетинг - всегда
        'marketing' => [
            'always' => true,
            'files' => [
                '/inc/conversion.php',
                '/inc/email-templates.php',
                '/inc/notifications.php',
            ],
        ],
    ];
    
    /**
     * Загружает модули
     *
     * @return void
     */
    public static function load(): void {
        foreach ( self::$modules as $group => $config ) {
            if ( self::should_load( $config ) ) {
                self::load_files( $config['files'] );
            }
        }
    }
    
    /**
     * Проверяет, нужно ли загружать группу модулей
     *
     * @param array $config Конфигурация группы
     * @return bool
     */
    private static function should_load( array $config ): bool {
        // Всегда загружать
        if ( ! empty( $config['always'] ) ) {
            return true;
        }
        
        // Проверка условия
        if ( ! empty( $config['condition'] ) ) {
            $condition = $config['condition'];
            
            // Отрицание
            if ( strpos( $condition, '!' ) === 0 ) {
                $func = substr( $condition, 1 );
                return ! function_exists( $func ) || ! call_user_func( $func );
            }
            
            // Прямая проверка
            if ( function_exists( $condition ) ) {
                return call_user_func( $condition );
            }
            
            // eval для сложных условий (осторожно!)
            return (bool) eval( "return {$condition};" );
        }
        
        return false;
    }
    
    /**
     * Загружает файлы
     *
     * @param array $files Список файлов
     * @return void
     */
    private static function load_files( array $files ): void {
        foreach ( $files as $file ) {
            $path = ATK_VED_DIR . $file;
            if ( file_exists( $path ) ) {
                require_once $path;
            }
        }
    }
    
    /**
     * Получает статистику загруженных модулей
     *
     * @return array
     */
    public static function get_stats(): array {
        $stats = [
            'total_groups' => count( self::$modules ),
            'loaded_groups' => 0,
            'total_files' => 0,
            'loaded_files' => 0,
        ];
        
        foreach ( self::$modules as $group => $config ) {
            $stats['total_files'] += count( $config['files'] );
            
            if ( self::should_load( $config ) ) {
                $stats['loaded_groups']++;
                
                foreach ( $config['files'] as $file ) {
                    if ( file_exists( ATK_VED_DIR . $file ) ) {
                        $stats['loaded_files']++;
                    }
                }
            }
        }
        
        return $stats;
    }
}

// Загружаем модули
ATK_VED_Module_Loader::load();
