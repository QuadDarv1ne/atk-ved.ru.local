<?php
/**
 * Welcome Page & Onboarding Wizard
 * Страница приветствия и мастер настройки темы
 * 
 * @package ATK_VED
 * @since 2.9.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Добавление страницы Welcome
 */
function atk_ved_add_welcome_page(): void {
    add_theme_page(
        __('О теме', 'atk-ved'),
        __('О теме', 'atk-ved'),
        'manage_options',
        'atk-ved-welcome',
        'atk_ved_welcome_page'
    );
}
add_action('admin_menu', 'atk_ved_add_welcome_page');

/**
 * Страница Welcome
 */
function atk_ved_welcome_page(): void {
    $theme = wp_get_theme();
    $is_imported = get_option('atk_ved_demo_imported', false);
    ?>
    <div class="wrap atk-welcome-page">
        <div class="atk-welcome-header">
            <h1><?php echo esc_html($theme->get('Name')); ?></h1>
            <p class="version"><?php printf(__('Версия %s', 'atk-ved'), esc_html($theme->get('Version'))); ?></p>
        </div>
        
        <?php if (!$is_imported): ?>
        <div class="atk-onboarding-wizard">
            <h2>🎉 <?php _e('Добро пожаловать в АТК ВЭД!', 'atk-ved'); ?></h2>
            <p><?php _e('Давайте настроим ваш сайт за 5 простых шагов:', 'atk-ved'); ?></p>
            
            <div class="onboarding-steps">
                <div class="step" data-step="1">
                    <span class="step-number">1</span>
                    <h3>📦 <?php _e('Импорт демо контента', 'atk-ved'); ?></h3>
                    <p><?php _e('Быстрая настройка страниц, меню и виджетов', 'atk-ved'); ?></p>
                    <button class="button button-primary" onclick="location.href='<?php echo admin_url('themes.php?page=atk-ved-demo-import'); ?>'">
                        <?php _e('Импортировать', 'atk-ved'); ?>
                    </button>
                </div>
                
                <div class="step" data-step="2">
                    <span class="step-number">2</span>
                    <h3>🎨 <?php _e('Настройка дизайна', 'atk-ved'); ?></h3>
                    <p><?php _e('Настройте цвета, шрифты и макет', 'atk-ved'); ?></p>
                    <a href="<?php echo admin_url('customize.php'); ?>" class="button button-primary">
                        <?php _e('Открыть Customizer', 'atk-ved'); ?>
                    </a>
                </div>
                
                <div class="step" data-step="3">
                    <span class="step-number">3</span>
                    <h3>📊 <?php _e('Подключение аналитики', 'atk-ved'); ?></h3>
                    <p><?php _e('Яндекс.Метрика и Google Analytics', 'atk-ved'); ?></p>
                    <a href="<?php echo admin_url('customize.php?panel=atk_ved_analytics'); ?>" class="button button-primary">
                        <?php _e('Настроить', 'atk-ved'); ?>
                    </a>
                </div>
                
                <div class="step" data-step="4">
                    <span class="step-number">4</span>
                    <h3>🔗 <?php _e('Настройка контактов', 'atk-ved'); ?></h3>
                    <p><?php _e('Телефон, email, социальные сети', 'atk-ved'); ?></p>
                    <a href="<?php echo admin_url('customize.php?section=atk_ved_contacts'); ?>" class="button button-primary">
                        <?php _e('Настроить', 'atk-ved'); ?>
                    </a>
                </div>
                
                <div class="step" data-step="5">
                    <span class="step-number">5</span>
                    <h3>🚀 <?php _e('Запуск сайта', 'atk-ved'); ?></h3>
                    <p><?php _e('Проверьте все настройки и запускайте!', 'atk-ved'); ?></p>
                    <a href="<?php echo home_url(); ?>" target="_blank" class="button button-primary">
                        <?php _e('Посетить сайт', 'atk-ved'); ?>
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="atk-features-grid">
            <h2><?php _e('Возможности темы', 'atk-ved'); ?></h2>
            
            <div class="feature-card">
                <div class="feature-icon">🎨</div>
                <h3><?php _e('Современный дизайн', 'atk-ved'); ?></h3>
                <p><?php _e('Адаптивный дизайн с плавными анимациями', 'atk-ved'); ?></p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3><?php _e('Высокая скорость', 'atk-ved'); ?></h3>
                <p><?php _e('Оптимизация и кэширование для быстрой загрузки', 'atk-ved'); ?></p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🛍️</div>
                <h3><?php _e('WooCommerce', 'atk-ved'); ?></h3>
                <p><?php _e('Полная поддержка интернет-магазина', 'atk-ved'); ?></p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3><?php _e('Аналитика', 'atk-ved'); ?></h3>
                <p><?php _e('Интеграция с Яндекс.Метрикой и Google Analytics', 'atk-ved'); ?></p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🔐</div>
                <h3><?php _e('Безопасность', 'atk-ved'); ?></h3>
                <p><?php _e('2FA, Audit Log, reCAPTCHA защита', 'atk-ved'); ?></p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">📱</div>
                <h3><?php _e('PWA', 'atk-ved'); ?></h3>
                <p><?php _e('Progressive Web App для мобильных', 'atk-ved'); ?></p>
            </div>
        </div>
        
        <div class="atk-quick-links">
            <h2><?php _e('Быстрые ссылки', 'atk-ved'); ?></h2>
            
            <div class="quick-links-grid">
                <a href="<?php echo admin_url('customize.php'); ?>" class="quick-link">
                    <span class="dashicons dashicons-admin-customizer"></span>
                    <?php _e('Настроить тему', 'atk-ved'); ?>
                </a>
                
                <a href="<?php echo admin_url('widgets.php'); ?>" class="quick-link">
                    <span class="dashicons dashicons-widgets"></span>
                    <?php _e('Виджеты', 'atk-ved'); ?>
                </a>
                
                <a href="<?php echo admin_url('nav-menus.php'); ?>" class="quick-link">
                    <span class="dashicons dashicons-menu"></span>
                    <?php _e('Меню', 'atk-ved'); ?>
                </a>
                
                <a href="<?php echo admin_url('themes.php?page=atk-ved-demo-import'); ?>" class="quick-link">
                    <span class="dashicons dashicons-download"></span>
                    <?php _e('Импорт демо', 'atk-ved'); ?>
                </a>
                
                <a href="<?php echo admin_url('admin.php?page=atk-ved-dashboard'); ?>" class="quick-link">
                    <span class="dashicons dashicons-dashboard"></span>
                    <?php _e('Dashboard', 'atk-ved'); ?>
                </a>
                
                <a href="<?php echo home_url(); ?>" target="_blank" class="quick-link">
                    <span class="dashicons dashicons-external"></span>
                    <?php _e('Посетить сайт', 'atk-ved'); ?>
                </a>
            </div>
        </div>
        
        <div class="atk-changelog-preview">
            <h2><?php _e('Последние изменения', 'atk-ved'); ?></h2>
            <div class="changelog-content">
                <h3>Версия 2.9.0</h3>
                <ul>
                    <li>✅ Demo Content Importer</li>
                    <li>✅ Welcome Page & Onboarding</li>
                    <li>✅ Health Check мониторинг</li>
                    <li>✅ REST API кэширование</li>
                    <li>✅ Оптимизация производительности</li>
                </ul>
                <a href="<?php echo get_template_directory_uri(); ?>/docs/OPTIMIZATIONS_V2.8.md" target="_blank" class="button">
                    <?php _e('Полный changelog', 'atk-ved'); ?>
                </a>
            </div>
        </div>
        
        <style>
            .atk-welcome-page { max-width: 1200px; }
            .atk-welcome-header {
                background: linear-gradient(135deg, #e31e24, #c01a1f);
                color: #fff;
                padding: 40px;
                border-radius: 12px;
                margin-bottom: 30px;
                text-align: center;
            }
            .atk-welcome-header h1 { margin: 0 0 10px; font-size: 48px; }
            .version { font-size: 18px; opacity: 0.9; }
            
            .atk-onboarding-wizard {
                background: #fff;
                padding: 30px;
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                margin-bottom: 30px;
                text-align: center;
            }
            
            .onboarding-steps {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                margin-top: 30px;
            }
            
            .step {
                background: #f8f9fa;
                padding: 25px;
                border-radius: 8px;
                transition: transform 0.3s;
            }
            
            .step:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            }
            
            .step-number {
                display: inline-block;
                width: 50px;
                height: 50px;
                background: #e31e24;
                color: #fff;
                border-radius: 50%;
                font-size: 24px;
                font-weight: bold;
                line-height: 50px;
                margin-bottom: 15px;
            }
            
            .step h3 { margin: 10px 0; font-size: 18px; }
            .step p { color: #666; margin-bottom: 15px; }
            
            .atk-features-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 20px;
                margin-bottom: 30px;
            }
            
            .feature-card {
                background: #fff;
                padding: 25px;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                text-align: center;
            }
            
            .feature-icon { font-size: 48px; margin-bottom: 15px; }
            .feature-card h3 { margin: 10px 0; }
            .feature-card p { color: #666; }
            
            .quick-links-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 15px;
                margin-top: 20px;
            }
            
            .quick-link {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 15px 20px;
                background: #fff;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                text-decoration: none;
                color: #333;
                transition: all 0.3s;
            }
            
            .quick-link:hover {
                background: #e31e24;
                color: #fff;
                border-color: #e31e24;
            }
            
            .atk-changelog-preview {
                background: #fff;
                padding: 30px;
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            }
            
            .changelog-content ul {
                list-style: none;
                padding-left: 0;
            }
            
            .changelog-content li {
                padding: 8px 0;
                border-bottom: 1px solid #f0f0f0;
            }
        </style>
    </div>
    <?php
}

/**
 * Перенаправление на Welcome page после активации темы
 */
function atk_ved_welcome_redirect(): void {
    if (get_option('atk_ved_welcome_redirected', false)) {
        return;
    }
    
    // Не редиректим при активации через API или CLI
    if (defined('WP_CLI') || defined('REST_REQUEST')) {
        return;
    }
    
    update_option('atk_ved_welcome_redirected', true);
    wp_safe_redirect(admin_url('themes.php?page=atk-ved-welcome'));
    exit;
}
add_action('after_switch_theme', 'atk_ved_welcome_redirect');

/**
 * Уведомление о новой версии
 */
function atk_ved_version_notice(): void {
    $current_version = wp_get_theme()->get('Version');
    $seen_version = get_option('atk_ved_seen_version', '0');
    
    if (version_compare($seen_version, $current_version, '<')) {
        ?>
        <div class="notice notice-info is-dismissible">
            <p>
                <strong><?php _e('АТК ВЭД обновлён!', 'atk-ved'); ?></strong><br>
                <?php printf(__('Версия %s — новые возможности и оптимизации', 'atk-ved'), $current_version); ?>
                <a href="<?php echo admin_url('themes.php?page=atk-ved-welcome'); ?>" class="button">
                    <?php _e('Что нового?', 'atk-ved'); ?>
                </a>
            </p>
        </div>
        <?php
        update_option('atk_ved_seen_version', $current_version);
    }
}
add_action('admin_notices', 'atk_ved_version_notice');
