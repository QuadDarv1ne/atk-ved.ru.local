<?php
/**
 * Plugin Management
 * 
 * @package ATK_VED
 * @since 3.0.0
 */

declare(strict_types=1);

// Защита от прямого доступа
defined('ABSPATH') || exit;

/**
 * Инициализация управления плагинами
 */
function atk_ved_init_plugin_management(): void {
    // Проверка и обновление плагинов при необходимости
    add_action('admin_init', 'atk_ved_check_plugins_status');
    
    // Уведомления об актуальных версиях плагинов
    add_action('admin_notices', 'atk_ved_plugin_updates_notice');
    
    // Управление неактивированными плагинами
    add_action('admin_notices', 'atk_ved_inactive_plugins_notice');
}
add_action('init', 'atk_ved_init_plugin_management');

/**
 * Проверка статуса установленных плагинов
 */
function atk_ved_check_plugins_status(): void {
    // Получаем список установленных плагинов
    if (!function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    
    $plugins = get_plugins();
    $active_plugins = get_option('active_plugins', array());
    
    // Проверяем, активированы ли важные плагины
    $akismet_active = false;
    $hello_dolly_active = false;
    
    foreach ($plugins as $plugin_path => $plugin_data) {
        if (strpos($plugin_path, 'akismet') !== false) {
            $akismet_active = in_array($plugin_path, $active_plugins, true);
        } elseif (strpos($plugin_path, 'hello.php') !== false) {
            $hello_dolly_active = in_array($plugin_path, $active_plugins, true);
        }
    }
    
    // Сохраняем информацию о статусе плагинов
    update_option('atk_ved_plugins_status', array(
        'akismet_active' => $akismet_active,
        'hello_dolly_active' => $hello_dolly_active,
        'last_check' => current_time('mysql')
    ));
}

/**
 * Показывает уведомление о необходимости обновления плагинов
 */
function atk_ved_plugin_updates_notice(): void {
    if (!current_user_can('update_plugins')) {
        return;
    }
    
    // Проверяем наличие обновлений для плагинов
    $updates = get_site_transient('update_plugins');
    
    if ($updates && !empty($updates->response)) {
        $count = count($updates->response);
        
        if ($count > 0) {
            echo '<div class="notice notice-warning is-dismissible">';
            echo '<p><strong>' . __('Обновления плагинов', 'atk-ved') . '</strong></p>';
            echo '<p>' . sprintf(
                __('Доступно %s обновлений для плагинов. Рекомендуется обновить их как можно скорее для обеспечения безопасности.', 'atk-ved'),
                number_format_i18n($count)
            ) . '</p>';
            echo '<p><a href="' . esc_url(admin_url('plugins.php')) . '" class="button button-primary">';
            echo __('Перейти к обновлениям', 'atk-ved') . '</a></p>';
            echo '</div>';
        }
    }
}

/**
 * Показывает уведомление о неактивированных плагинах
 */
function atk_ved_inactive_plugins_notice(): void {
    if (!current_user_can('activate_plugins')) {
        return;
    }
    
    // Проверяем статус важных плагинов
    $plugins_status = get_option('atk_ved_plugins_status', array());
    
    if (!$plugins_status) {
        return;
    }
    
    $show_notice = false;
    $inactive_plugins = array();
    
    if (isset($plugins_status['akismet_active']) && !$plugins_status['akismet_active']) {
        $inactive_plugins[] = 'Akismet';
        $show_notice = true;
    }
    
    if (isset($plugins_status['hello_dolly_active']) && !$plugins_status['hello_dolly_active']) {
        // Hello Dolly не является критичным, но уведомим об этом
        $show_notice = true;
    }
    
    if ($show_notice) {
        echo '<div class="notice notice-info is-dismissible">';
        echo '<p><strong>' . __('Плагины', 'atk-ved') . '</strong></p>';
        echo '<p>' . __('Некоторые плагины не активированы. Рекомендуется проверить их статус:', 'atk-ved') . '</p>';
        echo '<ul style="margin-left: 20px;">';
        
        foreach ($inactive_plugins as $plugin) {
            echo '<li>' . esc_html($plugin) . '</li>';
        }
        
        echo '</ul>';
        echo '<p><a href="' . esc_url(admin_url('plugins.php')) . '" class="button button-secondary">';
        echo __('Управление плагинами', 'atk-ved') . '</a></p>';
        echo '</div>';
    }
}

/**
 * Рекомендации по управлению плагинами
 */
function atk_ved_plugin_recommendations(): array {
    return array(
        'essential' => array(
            'akismet/akismet.php' => array(
                'name' => 'Akismet Anti-spam',
                'purpose' => __('Защита от спама в комментариях', 'atk-ved'),
                'recommended_action' => __('Рекомендуется активировать для защиты от спама', 'atk-ved')
            )
        ),
        'non_essential' => array(
            'hello.php' => array(
                'name' => 'Hello Dolly',
                'purpose' => __('Демонстрационный плагин WordPress', 'atk-ved'),
                'recommended_action' => __('Можно удалить, так как не несет функциональной нагрузки', 'atk-ved')
            )
        )
    );
}

/**
 * Проверка безопасности установленных плагинов
 */
function atk_ved_check_plugin_security(): array {
    $results = array(
        'secure' => array(),
        'warnings' => array(),
        'critical' => array()
    );
    
    if (!function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    
    $plugins = get_plugins();
    
    foreach ($plugins as $plugin_path => $plugin_data) {
        $plugin_name = $plugin_data['Name'];
        $plugin_version = $plugin_data['Version'];
        
        // Проверяем устаревшие плагины (не обновлялись более 1 года)
        $plugin_dir = WP_PLUGIN_DIR . '/' . dirname($plugin_path);
        if (is_dir($plugin_dir)) {
            $plugin_files = glob($plugin_dir . '/*.php');
        } else {
            $plugin_files = false;
        }
        
        if ($plugin_files) {
            $latest_modified = 0;
            foreach ($plugin_files as $file) {
                $modified = filemtime($file);
                if ($modified > $latest_modified) {
                    $latest_modified = $modified;
                }
            }
            
            $time_diff = time() - $latest_modified;
            $one_year = YEAR_IN_SECONDS;
            
            if ($time_diff > $one_year && !str_contains($plugin_path, 'akismet')) {
                $results['warnings'][] = array(
                    'plugin' => $plugin_name,
                    'message' => __('Плагин не обновлялся более года, что может представлять угрозу безопасности', 'atk-ved')
                );
            }
        }
    }
    
    return $results;
}