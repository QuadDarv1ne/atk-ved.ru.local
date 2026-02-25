<?php
/**
 * Admin Dashboard for ATK VED Theme
 * Статистика, аналитика и управление темой
 * 
 * @package ATK_VED
 * @since 2.7.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Добавление меню Dashboard
 */
function atk_ved_admin_dashboard_menu(): void {
    add_menu_page(
        __('АТК ВЭД Dashboard', 'atk-ved'),
        __('АТК ВЭД', 'atk-ved'),
        'manage_options',
        'atk-ved-dashboard',
        'atk_ved_admin_dashboard_page',
        'dashicons-dashboard',
        3
    );
    
    add_submenu_page(
        'atk-ved-dashboard',
        __('Обзор', 'atk-ved'),
        __('Обзор', 'atk-ved'),
        'manage_options',
        'atk-ved-dashboard',
        'atk_ved_admin_dashboard_page'
    );
    
    add_submenu_page(
        'atk-ved-dashboard',
        __('Статистика', 'atk-ved'),
        __('Статистика', 'atk-ved'),
        'manage_options',
        'atk-ved-statistics',
        'atk_ved_admin_statistics_page'
    );
    
    add_submenu_page(
        'atk-ved-dashboard',
        __('Настройки темы', 'atk-ved'),
        __('Настройки', 'atk-ved'),
        'manage_options',
        'atk-ved-settings',
        'atk_ved_admin_settings_page'
    );
    
    add_submenu_page(
        'atk-ved-dashboard',
        __('Системная информация', 'atk-ved'),
        __('Система', 'atk-ved'),
        'manage_options',
        'atk-ved-system',
        'atk_ved_admin_system_page'
    );
}
add_action('admin_menu', 'atk_ved_admin_dashboard_menu');

/**
 * Главная страница Dashboard
 */
function atk_ved_admin_dashboard_page(): void {
    ?>
    <div class="wrap atk-ved-dashboard">
        <h1><?php _e('АТК ВЭД Dashboard', 'atk-ved'); ?></h1>
        
        <div class="atk-dashboard-welcome">
            <div class="atk-welcome-card">
                <h2>👋 <?php _e('Добро пожаловать в АТК ВЭД!', 'atk-ved'); ?></h2>
                <p><?php _e('Панель управления темой для быстрого доступа ко всем функциям.', 'atk-ved'); ?></p>
            </div>
        </div>
        
        <div class="atk-stats-grid">
            <div class="atk-stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo wp_count_posts('post')->publish; ?></div>
                    <div class="stat-label"><?php _e('Записей', 'atk-ved'); ?></div>
                </div>
            </div>
            
            <div class="atk-stat-card">
                <div class="stat-icon">📄</div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo wp_count_posts('page')->publish; ?></div>
                    <div class="stat-label"><?php _e('Страниц', 'atk-ved'); ?></div>
                </div>
            </div>
            
            <?php if (class_exists('WooCommerce')): ?>
            <div class="atk-stat-card">
                <div class="stat-icon">🛍️</div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo wp_count_posts('product')->publish; ?></div>
                    <div class="stat-label"><?php _e('Товаров', 'atk-ved'); ?></div>
                </div>
            </div>
            
            <div class="atk-stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo wc_format_number(wc_get_order_count('completed')); ?></div>
                    <div class="stat-label"><?php _e('Заказов', 'atk-ved'); ?></div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="atk-stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo count_users()['total_users']; ?></div>
                    <div class="stat-label"><?php _e('Пользователей', 'atk-ved'); ?></div>
                </div>
            </div>
            
            <div class="atk-stat-card">
                <div class="stat-icon">💬</div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo wp_count_comments()->total_comments; ?></div>
                    <div class="stat-label"><?php _e('Комментариев', 'atk-ved'); ?></div>
                </div>
            </div>
        </div>
        
        <div class="atk-quick-actions">
            <h2><?php _e('Быстрые действия', 'atk-ved'); ?></h2>
            <div class="atk-actions-grid">
                <a href="<?php echo admin_url('post-new.php'); ?>" class="atk-action-card">
                    <span class="action-icon">📝</span>
                    <span><?php _e('Добавить запись', 'atk-ved'); ?></span>
                </a>
                <a href="<?php echo admin_url('post-new.php?post_type=page'); ?>" class="atk-action-card">
                    <span class="action-icon">📄</span>
                    <span><?php _e('Добавить страницу', 'atk-ved'); ?></span>
                </a>
                <?php if (class_exists('WooCommerce')): ?>
                <a href="<?php echo admin_url('post-new.php?post_type=product'); ?>" class="atk-action-card">
                    <span class="action-icon">🏷️</span>
                    <span><?php _e('Добавить товар', 'atk-ved'); ?></span>
                </a>
                <a href="<?php echo admin_url('edit.php?post_type=shop_order'); ?>" class="atk-action-card">
                    <span class="action-icon">📦</span>
                    <span><?php _e('Заказы', 'atk-ved'); ?></span>
                </a>
                <?php endif; ?>
                <a href="<?php echo admin_url('upload.php'); ?>" class="atk-action-card">
                    <span class="action-icon">📷</span>
                    <span><?php _e('Медиафайлы', 'atk-ved'); ?></span>
                </a>
                <a href="<?php echo home_url(); ?>" target="_blank" class="atk-action-card">
                    <span class="action-icon">🌐</span>
                    <span><?php _e('Посетить сайт', 'atk-ved'); ?></span>
                </a>
            </div>
        </div>
        
        <div class="atk-news-updates">
            <h2><?php _e('Новости и обновления', 'atk-ved'); ?></h2>
            <div class="atk-news-card">
                <div class="news-header">
                    <h3>🎉 АТК ВЭД v2.7.0</h3>
                    <span class="news-date"><?php echo date_i18n(get_option('date_format')); ?></span>
                </div>
                <div class="news-content">
                    <ul>
                        <li>✅ Email шаблоны для уведомлений</li>
                        <li>✅ Admin Dashboard со статистикой</li>
                        <li>✅ Система внутренних уведомлений</li>
                        <li>✅ Wishlist функционал</li>
                        <li>✅ Сравнение товаров</li>
                        <li>✅ Расширенный поиск с фильтрами</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <style>
            .atk-ved-dashboard { max-width: 1400px; }
            .atk-dashboard-welcome { margin-bottom: 30px; }
            .atk-welcome-card { background: linear-gradient(135deg, #e31e24, #c01a1f); color: #fff; padding: 30px; border-radius: 12px; }
            .atk-welcome-card h2 { margin: 0 0 10px; }
            .atk-welcome-card p { margin: 0; opacity: 0.9; }
            .atk-stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
            .atk-stat-card { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 15px; }
            .stat-icon { font-size: 40px; }
            .stat-content { flex: 1; }
            .stat-number { font-size: 28px; font-weight: 700; color: #2c2c2c; }
            .stat-label { font-size: 14px; color: #666; }
            .atk-quick-actions, .atk-news-updates { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px; }
            .atk-quick-actions h2, .atk-news-updates h2 { margin: 0 0 20px; font-size: 20px; }
            .atk-actions-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; }
            .atk-action-card { display: flex; flex-direction: column; align-items: center; padding: 20px; background: #f8f9fa; border-radius: 8px; text-decoration: none; color: #333; transition: all 0.3s; }
            .atk-action-card:hover { background: #e31e24; color: #fff; transform: translateY(-3px); }
            .action-icon { font-size: 32px; margin-bottom: 10px; }
            .atk-news-card { border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; }
            .news-header { background: #f8f9fa; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
            .news-header h3 { margin: 0; font-size: 16px; }
            .news-date { font-size: 13px; color: #999; }
            .news-content { padding: 20px; }
            .news-content ul { margin: 0; padding-left: 20px; }
            .news-content li { margin-bottom: 8px; }
        </style>
        <?php
    }

/**
 * Страница статистики
 */
function atk_ved_admin_statistics_page(): void {
    // Получение данных
    $post_count = wp_count_posts('post')->publish;
    $page_count = wp_count_posts('page')->publish;
    $user_count = count_users()['total_users'];
    $comment_count = wp_count_comments()->total_comments;
    
    // WooCommerce статистика
    $wc_stats = array();
    if (class_exists('WooCommerce')) {
        $wc_stats = array(
            'products' => wp_count_posts('product')->publish,
            'orders' => wc_get_order_count('completed'),
            'revenue' => wc_format_price(wc_get_order_total('completed')),
        );
    }
    
    // Посещаемость (если есть данные)
    $views = get_option('atk_ved_total_views', 0);
    ?>
    <div class="wrap">
        <h1><?php _e('Статистика сайта', 'atk-ved'); ?></h1>
        
        <div class="atk-stats-overview">
            <div class="stat-box">
                <h3><?php _e('Контент', 'atk-ved'); ?></h3>
                <ul>
                    <li><?php _e('Записи:', 'atk-ved'); ?> <strong><?php echo $post_count; ?></strong></li>
                    <li><?php _e('Страницы:', 'atk-ved'); ?> <strong><?php echo $page_count; ?></strong></li>
                    <li><?php _e('Комментарии:', 'atk-ved'); ?> <strong><?php echo $comment_count; ?></strong></li>
                </ul>
            </div>
            
            <div class="stat-box">
                <h3><?php _e('Пользователи', 'atk-ved'); ?></h3>
                <ul>
                    <li><?php _e('Всего:', 'atk-ved'); ?> <strong><?php echo $user_count; ?></strong></li>
                </ul>
            </div>
            
            <?php if ($wc_stats): ?>
            <div class="stat-box">
                <h3><?php _e('WooCommerce', 'atk-ved'); ?></h3>
                <ul>
                    <li><?php _e('Товары:', 'atk-ved'); ?> <strong><?php echo $wc_stats['products']; ?></strong></li>
                    <li><?php _e('Заказы:', 'atk-ved'); ?> <strong><?php echo $wc_stats['orders']; ?></strong></li>
                    <li><?php _e('Выручка:', 'atk-ved'); ?> <strong><?php echo $wc_stats['revenue']; ?></strong></li>
                </ul>
            </div>
            <?php endif; ?>
        </div>
        
        <style>
            .atk-stats-overview { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
            .stat-box { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
            .stat-box h3 { margin: 0 0 15px; font-size: 18px; color: #2c2c2c; }
            .stat-box ul { list-style: none; padding: 0; margin: 0; }
            .stat-box li { padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
            .stat-box li:last-child { border-bottom: none; }
        </style>
    </div>
    <?php
}

/**
 * Страница настроек темы
 */
function atk_ved_admin_settings_page(): void {
    ?>
    <div class="wrap">
        <h1><?php _e('Настройки темы', 'atk-ved'); ?></h1>
        <p><?php _e('Основные настройки доступны в разделе <strong>Внешний вид → Настроить</strong>', 'atk-ved'); ?></p>
        
        <div class="atk-settings-links">
            <a href="<?php echo admin_url('customize.php'); ?>" class="button button-primary button-hero">
                <?php _e('Открыть Customizer', 'atk-ved'); ?>
            </a>
        </div>
    </div>
    <?php
}

/**
 * Страница системной информации
 */
function atk_ved_admin_system_page(): void {
    global $wpdb;
    
    $server_info = array(
        'PHP Version' => phpversion(),
        'WordPress Version' => get_bloginfo('version'),
        'Theme Version' => wp_get_theme()->get('Version'),
        'MySQL Version' => $wpdb->db_version(),
        'Server Software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'Memory Limit' => WP_MEMORY_LIMIT,
        'Max Upload Size' => ini_get('upload_max_filesize'),
        'Post Max Size' => ini_get('post_max_size'),
    );
    
    $active_plugins = get_option('active_plugins', array());
    ?>
    <div class="wrap">
        <h1><?php _e('Системная информация', 'atk-ved'); ?></h1>
        
        <div class="atk-system-info">
            <h2><?php _e('Информация о сервере', 'atk-ved'); ?></h2>
            <table class="widefat">
                <tbody>
                    <?php foreach ($server_info as $key => $value): ?>
                    <tr>
                        <td><strong><?php echo esc_html($key); ?></strong></td>
                        <td><?php echo esc_html($value); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="atk-system-info">
            <h2><?php _e('Активные плагины', 'atk-ved'); ?></h2>
            <?php if ($active_plugins): ?>
            <ul>
                <?php foreach ($active_plugins as $plugin): ?>
                <li><?php echo esc_html($plugin); ?></li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
            <p><?php _e('Нет активных плагинов', 'atk-ved'); ?></p>
            <?php endif; ?>
        </div>
        
        <style>
            .atk-system-info { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
            .atk-system-info h2 { margin: 0 0 15px; font-size: 18px; }
            .atk-system-info table { width: 100%; }
            .atk-system-info td { padding: 10px; border-bottom: 1px solid #f0f0f0; }
            .atk-system-info ul { margin: 0; padding-left: 20px; }
            .atk-system-info li { margin-bottom: 5px; }
        </style>
    </div>
    <?php
}
