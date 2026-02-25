<?php
/**
 * Translation Manager Admin Panel
 * Админ-панель для управления переводами
 * 
 * @package ATK_VED
 * @since 3.5.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Админ-панель переводов
 */
function atk_ved_translation_manager_menu(): void {
    add_submenu_page(
        'themes.php',
        __('Управление переводами', 'atk-ved'),
        __('Переводы', 'atk-ved'),
        'manage_options',
        'atk-ved-translations',
        'atk_ved_translation_manager_page'
    );
}
add_action('admin_menu', 'atk_ved_translation_manager_menu');

/**
 * Страница управления переводами
 */
function atk_ved_translation_manager_page(): void {
    $languages = ATK_VED_Multilingual::get_languages();
    $current_lang = atk_ved_get_current_language();
    
    // Обработка сохранения
    if (isset($_POST['save_translation']) && wp_verify_nonce($_POST['translation_nonce'], 'save_translation')) {
        $lang = sanitize_text_field($_POST['language']);
        $translations = array();
        
        foreach ($_POST['translations'] as $original => $translated) {
            if (!empty($translated)) {
                $translations[$original] = sanitize_text_field($translated);
            }
        }
        
        // Сохранение в файл
        $file_path = get_template_directory() . '/languages/atk-ved-' . $lang . '.php';
        $content = "<?php\n/**\n * Translations for {$lang}\n */\n\nreturn " . var_export($translations, true) . ";\n";
        
        if (file_put_contents($file_path, $content)) {
            echo '<div class="notice notice-success"><p>' . __('Переводы сохранены', 'atk-ved') . '</p></div>';
            
            // Очистка кэша
            wp_cache_delete('atk_ved_translations_' . $lang, 'translations');
        } else {
            echo '<div class="notice notice-error"><p>' . __('Ошибка сохранения', 'atk-ved') . '</p></div>';
        }
    }
    
    // Загрузка текущих переводов
    $selected_lang = $_GET['lang'] ?? 'en';
    $translation_file = get_template_directory() . '/languages/atk-ved-' . $selected_lang . '.php';
    $current_translations = file_exists($translation_file) ? include $translation_file : array();
    
    // Загрузка русских оригиналов
    $ru_file = get_template_directory() . '/languages/atk-ved-ru.php';
    $originals = file_exists($ru_file) ? include $ru_file : array();
    
    // Если нет русского файла, используем заглушки
    if (empty($originals)) {
        $originals = array_keys($current_translations);
    }
    ?>
    <div class="wrap atk-translation-manager">
        <h1><?php _e('Управление переводами', 'atk-ved'); ?></h1>
        
        <div class="translation-header">
            <div class="translation-stats">
                <div class="stat-card">
                    <div class="stat-value"><?php echo count($languages); ?></div>
                    <div class="stat-label"><?php _e('Языков', 'atk-ved'); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo count($originals); ?></div>
                    <div class="stat-label"><?php _e('Строк', 'atk-ved'); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo count($current_translations); ?></div>
                    <div class="stat-label"><?php _e('Переведено', 'atk-ved'); ?></div>
                </div>
            </div>
        </div>
        
        <div class="translation-controls">
            <form method="get" class="lang-selector">
                <input type="hidden" name="page" value="atk-ved-translations">
                <label><?php _e('Выберите язык:', 'atk-ved'); ?></label>
                <select name="lang" onchange="this.form.submit()">
                    <?php foreach ($languages as $lang): ?>
                    <option value="<?php echo esc_attr($lang['code']); ?>" <?php selected($selected_lang, $lang['code']); ?>>
                        <?php echo esc_html($lang['flag'] . ' ' . $lang['name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </form>
            
            <div class="translation-actions">
                <button type="button" class="button" onclick="exportTranslations()">
                    <?php _e('Экспорт', 'atk-ved'); ?>
                </button>
                <button type="button" class="button" onclick="importTranslations()">
                    <?php _e('Импорт', 'atk-ved'); ?>
                </button>
            </div>
        </div>
        
        <form method="post" class="translation-form">
            <?php wp_nonce_field('save_translation', 'translation_nonce'); ?>
            <input type="hidden" name="language" value="<?php echo esc_attr($selected_lang); ?>">
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width: 50%;"><?php _e('Оригинал (Русский)', 'atk-ved'); ?></th>
                        <th style="width: 50%;"><?php printf(__('Перевод (%s)', 'atk-ved'), $languages[$selected_lang]['name']); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($originals as $key => $original): ?>
                    <tr>
                        <td>
                            <code><?php echo esc_html($original); ?></code>
                        </td>
                        <td>
                            <input type="text" 
                                   name="translations[<?php echo esc_attr($original); ?>]" 
                                   value="<?php echo esc_attr($current_translations[$original] ?? ''); ?>"
                                   style="width: 100%;"
                                   placeholder="<?php esc_attr_e('Введите перевод', 'atk-ved'); ?>">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <p class="submit">
                <input type="submit" name="save_translation" class="button button-primary" value="<?php _e('Сохранить переводы', 'atk-ved'); ?>">
            </p>
        </form>
        
        <div id="translationModal" style="display: none;">
            <!-- Modal content -->
        </div>
    </div>
    
    <style>
    .atk-translation-manager { max-width: 1400px; }
    .translation-header { margin: 20px 0; }
    .translation-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    .stat-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center; }
    .stat-value { font-size: 32px; font-weight: 700; color: #e31e24; }
    .stat-label { color: #666; margin-top: 5px; }
    .translation-controls { display: flex; justify-content: space-between; align-items: center; margin: 20px 0; background: #fff; padding: 20px; border-radius: 8px; }
    .lang-selector select { min-width: 200px; padding: 8px; border-radius: 4px; }
    .translation-form { background: #fff; padding: 20px; border-radius: 8px; }
    .translation-form table { margin-top: 20px; }
    .translation-form input[type="text"] { padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
    .translation-form input[type="text"]:focus { border-color: #e31e24; }
    </style>
    
    <script>
    function exportTranslations() {
        const lang = document.querySelector('select[name="lang"]').value;
        window.location.href = '<?php echo admin_url('admin-ajax.php'); ?>?action=export_translations&lang=' + lang;
    }
    
    function importTranslations() {
        const lang = document.querySelector('select[name="lang"]').value;
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = '.php,.json';
        fileInput.onchange = function(e) {
            const formData = new FormData();
            formData.append('action', 'import_translations');
            formData.append('lang', lang);
            formData.append('file', e.target.files[0]);
            // Implementation needed
        };
        fileInput.click();
    }
    </script>
    <?php
}

/**
 * AJAX: Экспорт переводов
 */
function atk_ved_ajax_export_translations(): void {
    $lang = sanitize_text_field($_GET['lang'] ?? 'en');
    $file_path = get_template_directory() . '/languages/atk-ved-' . $lang . '.php';
    
    if (file_exists($file_path)) {
        header('Content-Type: text/php');
        header('Content-Disposition: attachment; filename="atk-ved-' . $lang . '.php"');
        readfile($file_path);
        exit;
    }
    
    wp_send_json_error(array('message' => __('Файл не найден', 'atk-ved')));
}
add_action('wp_ajax_export_translations', 'atk_ved_ajax_export_translations');

/**
 * Настройки в Customizer
 */
function atk_ved_multilingual_customizer($wp_customize): void {
    $wp_customize->add_section('atk_ved_multilingual', array(
        'title'    => __('Мультиязычность', 'atk-ved'),
        'priority' => 47,
    ));
    
    // Стиль переключателя
    $wp_customize->add_setting('atk_ved_lang_switcher_style', array(
        'default'           => 'dropdown',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('atk_ved_lang_switcher_style', array(
        'label'   => __('Стиль переключателя', 'atk-ved'),
        'section' => 'atk_ved_multilingual',
        'type'    => 'select',
        'choices' => array(
            'dropdown' => __('Выпадающий список', 'atk-ved'),
            'list'     => __('Список', 'atk-ved'),
            'flags'    => __('Флаги', 'atk-ved'),
        ),
    ));
    
    // Показывать флаги
    $wp_customize->add_setting('atk_ved_lang_switcher_flag', array(
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));
    
    $wp_customize->add_control('atk_ved_lang_switcher_flag', array(
        'label'   => __('Показывать флаги', 'atk-ved'),
        'section' => 'atk_ved_multilingual',
        'type'    => 'checkbox',
    ));
    
    // Показывать названия
    $wp_customize->add_setting('atk_ved_lang_switcher_name', array(
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));
    
    $wp_customize->add_control('atk_ved_lang_switcher_name', array(
        'label'   => __('Показывать названия', 'atk-ved'),
        'section' => 'atk_ved_multilingual',
        'type'    => 'checkbox',
    ));
    
    // Включённые языки
    $wp_customize->add_setting('atk_ved_enabled_languages', array(
        'default'           => array('ru', 'en'),
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('atk_ved_enabled_languages', array(
        'label'   => __('Включённые языки', 'atk-ved'),
        'section' => 'atk_ved_multilingual',
        'type'    => 'select',
        'multiple' => true,
        'choices' => array(
            'ru' => 'Русский',
            'en' => 'English',
            'zh' => '中文',
            'kk' => 'Қазақша',
        ),
    ));
}
add_action('customize_register', 'atk_ved_multilingual_customizer');
