<?php
/**
 * Multilingual System
 * Система мультиязычности с поддержкой WPML/Polylang
 * 
 * @package ATK_VED
 * @since 3.5.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Класс для управления мультиязычностью
 */
class ATK_VED_Multilingual {
    
    /**
     * Доступные языки
     */
    private static array $languages = array(
        'ru' => array(
            'code' => 'ru',
            'name' => 'Русский',
            'native_name' => 'Русский',
            'flag' => '🇷🇺',
            'locale' => 'ru_RU',
            'direction' => 'ltr',
        ),
        'en' => array(
            'code' => 'en',
            'name' => 'English',
            'native_name' => 'English',
            'flag' => '🇬🇧',
            'locale' => 'en_US',
            'direction' => 'ltr',
        ),
        'zh' => array(
            'code' => 'zh',
            'name' => 'Chinese',
            'native_name' => '中文',
            'flag' => '🇨🇳',
            'locale' => 'zh_CN',
            'direction' => 'ltr',
        ),
        'kk' => array(
            'code' => 'kk',
            'name' => 'Kazakh',
            'native_name' => 'Қазақша',
            'flag' => '🇰🇿',
            'locale' => 'kk_KZ',
            'direction' => 'ltr',
        ),
    );
    
    /**
     * Текущий язык
     */
    private static ?string $current_language = null;
    
    /**
     * Инициализация
     */
    public static function init(): void {
        add_action('init', array(__CLASS__, 'register_languages'));
        add_action('wp_footer', array(__CLASS__, 'language_switcher'), 100);
        add_filter('body_class', array(__CLASS__, 'language_body_class'));
        add_action('wp_head', array(__CLASS__, 'language_hreflang'));
        add_filter('locale', array(__CLASS__, 'set_locale'));
        
        // WPML совместимость
        add_action('wpml_registered', array(__CLASS__, 'wpml_compatibility'));
        
        // Кэширование переводов
        add_action('init', array(__CLASS__, 'cache_translations'), 1);
    }
    
    /**
     * Регистрация языков
     */
    public static function register_languages(): void {
        // Сохраняем текущий язык
        self::$current_language = self::get_current_language();
    }
    
    /**
     * Получение текущего языка
     */
    public static function get_current_language(): string {
        if (self::$current_language !== null) {
            return self::$current_language;
        }
        
        // Проверяем WPML
        if (defined('ICL_LANGUAGE_CODE')) {
            self::$current_language = ICL_LANGUAGE_CODE;
            return self::$current_language;
        }
        
        // Проверяем Polylang
        if (function_exists('pll_current_language')) {
            self::$current_language = pll_current_language();
            return self::$current_language;
        }
        
        // Проверяем URL
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        foreach (array_keys(self::$languages) as $lang) {
            if (strpos($uri, '/' . $lang . '/') === 0 || strpos($uri, '?' . $lang . '=') !== false) {
                self::$current_language = $lang;
                return self::$current_language;
            }
        }
        
        // Проверяем cookie
        if (isset($_COOKIE['atk_ved_language'])) {
            $lang = sanitize_text_field($_COOKIE['atk_ved_language']);
            if (isset(self::$languages[$lang])) {
                self::$current_language = $lang;
                return self::$current_language;
            }
        }
        
        // Язык по умолчанию
        self::$current_language = 'ru';
        return self::$current_language;
    }
    
    /**
     * Получение всех языков
     */
    public static function get_languages(): array {
        return self::$languages;
    }
    
    /**
     * Получение информации о языке
     */
    public static function get_language_info(string $code): ?array {
        return self::$languages[$code] ?? null;
    }
    
    /**
     * Установка локали
     */
    public static function set_locale(string $locale): string {
        $lang = self::get_current_language();
        
        if (isset(self::$languages[$lang])) {
            return self::$languages[$lang]['locale'];
        }
        
        return $locale;
    }
    
    /**
     * Переключение языка
     */
    public static function switch_language(string $lang_code): bool {
        if (!isset(self::$languages[$lang_code])) {
            return false;
        }
        
        self::$current_language = $lang_code;
        
        // Сохраняем в cookie на 1 год
        setcookie('atk_ved_language', $lang_code, time() + YEAR_IN_SECONDS, '/');
        
        // Перенаправление
        $url = self::get_language_url($lang_code);
        wp_safe_redirect($url);
        exit;
    }
    
    /**
     * Получение URL для языка
     */
    public static function get_language_url(string $lang_code): string {
        $current_url = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        
        // WPML
        if (function_exists('wpml_url')) {
            return wpml_url($lang_code);
        }
        
        // Polylang
        if (function_exists('pll_home_url')) {
            return pll_home_url($lang_code);
        }
        
        // Fallback: добавляем префикс языка
        $url = preg_replace('#^https?://[^/]+#', '', $current_url);
        
        // Удаляем существующий префикс языка
        foreach (array_keys(self::$languages) as $code) {
            $url = preg_replace('#^/' . $code . '/#', '/', $url);
        }
        
        // Добавляем новый префикс
        if ($lang_code !== 'ru') {
            $url = '/' . $lang_code . $url;
        }
        
        return (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $url;
    }
    
    /**
     * Переключатель языков (HTML)
     */
    public static function language_switcher(): void {
        $style = get_theme_mod('atk_ved_lang_switcher_style', 'dropdown');
        $show_flag = get_theme_mod('atk_ved_lang_switcher_flag', true);
        $show_name = get_theme_mod('atk_ved_lang_switcher_name', true);
        
        $current_lang = self::get_current_language();
        $languages = self::get_languages();
        
        if (count($languages) <= 1) {
            return;
        }
        ?>
        <div class="atk-language-switcher atk-lang-<?php echo esc_attr($style); ?>">
            <?php if ($style === 'dropdown'): ?>
            <button type="button" class="lang-current" aria-label="<?php esc_attr_e('Выбрать язык', 'atk-ved'); ?>">
                <?php if ($show_flag): ?>
                <span class="lang-flag"><?php echo esc_html($languages[$current_lang]['flag']); ?></span>
                <?php endif; ?>
                <?php if ($show_name): ?>
                <span class="lang-name"><?php echo esc_html($languages[$current_lang]['native_name']); ?></span>
                <?php endif; ?>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
            <div class="lang-dropdown">
                <?php foreach ($languages as $lang): ?>
                <?php if ($lang['code'] !== $current_lang): ?>
                <a href="<?php echo esc_url(self::get_language_url($lang['code'])); ?>" 
                   class="lang-option"
                   lang="<?php echo esc_attr($lang['code']); ?>">
                    <?php if ($show_flag): ?>
                    <span class="lang-flag"><?php echo esc_html($lang['flag']); ?></span>
                    <?php endif; ?>
                    <?php if ($show_name): ?>
                    <span class="lang-name"><?php echo esc_html($lang['native_name']); ?></span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
            
            <?php elseif ($style === 'list'): ?>
            <ul class="lang-list">
                <?php foreach ($languages as $lang): ?>
                <li class="lang-item <?php echo $lang['code'] === $current_lang ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url(self::get_language_url($lang['code'])); ?>" 
                       lang="<?php echo esc_attr($lang['code']); ?>">
                        <?php if ($show_flag): ?>
                        <span class="lang-flag"><?php echo esc_html($lang['flag']); ?></span>
                        <?php endif; ?>
                        <?php if ($show_name): ?>
                        <span class="lang-name"><?php echo esc_html($lang['native_name']); ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
            
            <?php elseif ($style === 'flags'): ?>
            <div class="lang-flags">
                <?php foreach ($languages as $lang): ?>
                <a href="<?php echo esc_url(self::get_language_url($lang['code'])); ?>" 
                   class="lang-flag-item <?php echo $lang['code'] === $current_lang ? 'active' : ''; ?>"
                   lang="<?php echo esc_attr($lang['code']); ?>"
                   title="<?php echo esc_attr($lang['name']); ?>">
                    <?php echo esc_html($lang['flag']); ?>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <style>
        .atk-language-switcher { position: relative; display: inline-block; }
        .atk-lang-dropdown .lang-current,
        .atk-lang-list .lang-item a,
        .atk-lang-flags .lang-flag-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: transparent;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
        }
        .atk-lang-dropdown .lang-current:hover,
        .atk-lang-list .lang-item a:hover,
        .atk-lang-flags .lang-flag-item:hover {
            border-color: #e31e24;
            background: rgba(227, 30, 36, 0.05);
        }
        .atk-lang-dropdown .lang-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 8px;
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            min-width: 180px;
            z-index: 1000;
            display: none;
        }
        .atk-lang-dropdown:hover .lang-dropdown { display: block; }
        .atk-lang-list { list-style: none; padding: 0; margin: 0; display: flex; gap: 10px; }
        .atk-lang-flags { display: flex; gap: 8px; }
        .atk-lang-flags .lang-flag-item { font-size: 24px; padding: 4px; border: none; }
        .lang-flag { font-size: 18px; }
        .lang-name { font-size: 14px; font-weight: 500; }
        @media (max-width: 768px) {
            .atk-lang-list { flex-wrap: wrap; }
        }
        </style>
        <?php
    }
    
    /**
     * Языковой класс для body
     */
    public static function language_body_class(array $classes): array {
        $lang = self::get_current_language();
        $classes[] = 'lang-' . $lang;
        
        $lang_info = self::get_language_info($lang);
        if ($lang_info && isset($lang_info['direction'])) {
            $classes[] = 'dir-' . $lang_info['direction'];
        }
        
        return $classes;
    }
    
    /**
     * Hreflang теги для SEO
     */
    public static function language_hreflang(): void {
        $languages = self::get_languages();
        $current_url = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        
        foreach ($languages as $lang) {
            $url = self::get_language_url($lang['code']);
            echo '<link rel="alternate" hreflang="' . esc_attr($lang['code']) . '" href="' . esc_url($url) . '" />' . "\n";
        }
        
        echo '<link rel="alternate" hreflang="x-default" href="' . esc_url(self::get_language_url('ru')) . '" />' . "\n";
    }
    
    /**
     * Кэширование переводов
     */
    public static function cache_translations(): void {
        // Загрузка переводов из кэша
        $lang = self::get_current_language();
        $cache_key = 'atk_ved_translations_' . $lang;
        
        $translations = wp_cache_get($cache_key);
        
        if ($translations === false) {
            $translations = self::load_translations($lang);
            wp_cache_set($cache_key, $translations, 'translations', DAY_IN_SECONDS);
        }
        
        // Глобальная переменная для доступа
        $GLOBALS['atk_ved_translations'] = $translations;
    }
    
    /**
     * Загрузка переводов
     */
    public static function load_translations(string $lang): array {
        $translations = array();
        
        // Путь к файлу переводов
        $lang_file = get_template_directory() . '/languages/atk-ved-' . $lang . '.php';
        
        if (file_exists($lang_file)) {
            $translations = include $lang_file;
        }
        
        return $translations;
    }
    
    /**
     * Перевод строки
     */
    public static function translate(string $text, string $domain = 'atk-ved'): string {
        $lang = self::get_current_language();
        
        if ($lang === 'ru') {
            return $text;
        }
        
        $translations = $GLOBALS['atk_ved_translations'] ?? array();
        
        return $translations[$text] ?? $text;
    }
    
    /**
     * WPML совместимость
     */
    public static function wpml_compatibility(): void {
        // Регистрация строк для перевода
        do_action('wpml_register_single_string', 'ATK VED', 'Site Title', get_bloginfo('name'));
        do_action('wpml_register_single_string', 'ATK VED', 'Site Description', get_bloginfo('description'));
        
        // Регистрация типов постов
        add_filter('wpml_translatable_post_types', function($post_types) {
            $post_types[] = 'product';
            $post_types[] = 'service';
            return $post_types;
        });
        
        // Регистрация таксономий
        add_filter('wpml_translatable_taxonomies', function($taxonomies) {
            $taxonomies[] = 'product_cat';
            $taxonomies[] = 'product_tag';
            return $taxonomies;
        });
    }
}

// Инициализация
ATK_VED_Multilingual::init();

/**
 * Helper функции
 */

/**
 * Получить текущий язык
 */
function atk_ved_get_current_language(): string {
    return ATK_VED_Multilingual::get_current_language();
}

/**
 * Получить все языки
 */
function atk_ved_get_languages(): array {
    return ATK_VED_Multilingual::get_languages();
}

/**
 * Переключить язык
 */
function atk_ved_switch_language(string $lang_code): bool {
    return ATK_VED_Multilingual::switch_language($lang_code);
}

/**
 * Получить URL для языка
 */
function atk_ved_get_language_url(string $lang_code): string {
    return ATK_VED_Multilingual::get_language_url($lang_code);
}

/**
 * Перевести строку
 */
function atk_ved_translate(string $text, string $domain = 'atk-ved'): string {
    return ATK_VED_Multilingual::translate($text, $domain);
}

/**
 * Шорткод переключателя языков
 */
function atk_ved_language_switcher_shortcode(array $atts): string {
    $atts = shortcode_atts(array(
        'style' => get_theme_mod('atk_ved_lang_switcher_style', 'dropdown'),
        'show_flag' => get_theme_mod('atk_ved_lang_switcher_flag', '1'),
        'show_name' => get_theme_mod('atk_ved_lang_switcher_name', '1'),
    ), $atts);
    
    ob_start();
    ATK_VED_Multilingual::language_switcher();
    return ob_get_clean();
}
add_shortcode('language_switcher', 'atk_ved_language_switcher_shortcode');
