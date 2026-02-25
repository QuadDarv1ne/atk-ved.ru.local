<?php
/**
 * Система мультиязычности
 * 
 * @package ATK_VED
 * @since 2.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Поддерживаемые языки
 */
function atk_ved_get_languages(): array {
    return [
        'ru' => [
            'name' => 'Русский',
            'native_name' => 'Русский',
            'flag' => '🇷🇺',
            'locale' => 'ru_RU',
            'direction' => 'ltr',
            'enabled' => true
        ],
        'en' => [
            'name' => 'English',
            'native_name' => 'English',
            'flag' => '🇬🇧',
            'locale' => 'en_US',
            'direction' => 'ltr',
            'enabled' => true
        ],
        'zh' => [
            'name' => 'Chinese',
            'native_name' => '中文',
            'flag' => '🇨🇳',
            'locale' => 'zh_CN',
            'direction' => 'ltr',
            'enabled' => true
        ]
    ];
}

/**
 * Получить текущий язык
 */
function atk_ved_get_current_language(): string {
    // Проверка WPML
    if (defined('ICL_LANGUAGE_CODE')) {
        return ICL_LANGUAGE_CODE;
    }
    
    // Проверка Polylang
    if (function_exists('pll_current_language')) {
        return pll_current_language();
    }
    
    // Проверка cookie
    if (isset($_COOKIE['atk_ved_lang'])) {
        $lang = sanitize_text_field($_COOKIE['atk_ved_lang']);
        $languages = atk_ved_get_languages();
        if (isset($languages[$lang])) {
            return $lang;
        }
    }
    
    // Проверка сессии
    if (isset($_SESSION['atk_ved_lang'])) {
        return sanitize_text_field($_SESSION['atk_ved_lang']);
    }
    
    // Определение по браузеру
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $browser_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        $languages = atk_ved_get_languages();
        if (isset($languages[$browser_lang])) {
            return $browser_lang;
        }
    }
    
    return 'ru'; // По умолчанию
}

/**
 * Установить текущий язык
 */
function atk_ved_set_language(string $lang): void {
    $languages = atk_ved_get_languages();
    
    if (!isset($languages[$lang])) {
        return;
    }
    
    // Сохранить в cookie на 1 год
    setcookie('atk_ved_lang', $lang, time() + YEAR_IN_SECONDS, '/');
    
    // Сохранить в сессию
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['atk_ved_lang'] = $lang;
    
    // Установить locale WordPress
    switch_to_locale($languages[$lang]['locale']);
}

/**
 * AJAX переключение языка
 */
function atk_ved_ajax_switch_language(): void {
    check_ajax_referer('atk_ved_nonce', 'nonce');
    
    $lang = sanitize_text_field($_POST['lang'] ?? '');
    $languages = atk_ved_get_languages();
    
    if (!isset($languages[$lang])) {
        wp_send_json_error(['message' => 'Invalid language']);
    }
    
    atk_ved_set_language($lang);
    
    wp_send_json_success([
        'message' => 'Language switched',
        'lang' => $lang,
        'redirect_url' => home_url('/')
    ]);
}
add_action('wp_ajax_atk_ved_switch_language', 'atk_ved_ajax_switch_language');
add_action('wp_ajax_nopriv_atk_ved_switch_language', 'atk_ved_ajax_switch_language');

/**
 * Переводы для всех языков
 */
function atk_ved_get_translations(): array {
    return [
        // Навигация
        'home' => [
            'ru' => 'Главная',
            'en' => 'Home',
            'zh' => '首页'
        ],
        'services' => [
            'ru' => 'Услуги',
            'en' => 'Services',
            'zh' => '服务'
        ],
        'delivery' => [
            'ru' => 'Доставка',
            'en' => 'Delivery',
            'zh' => '运输'
        ],
        'about' => [
            'ru' => 'О нас',
            'en' => 'About',
            'zh' => '关于我们'
        ],
        'contacts' => [
            'ru' => 'Контакты',
            'en' => 'Contacts',
            'zh' => '联系方式'
        ],
        'faq' => [
            'ru' => 'Вопросы',
            'en' => 'FAQ',
            'zh' => '常见问题'
        ],
        'reviews' => [
            'ru' => 'Отзывы',
            'en' => 'Reviews',
            'zh' => '评论'
        ],
        
        // Hero секция
        'hero_title' => [
            'ru' => 'Товары для маркетплейсов из Китая оптом',
            'en' => 'Wholesale Products from China for Marketplaces',
            'zh' => '来自中国的批发商品用于市场'
        ],
        'hero_subtitle' => [
            'ru' => 'Полный цикл поставок: от поиска товара до доставки на ваш склад',
            'en' => 'Full supply cycle: from product search to delivery to your warehouse',
            'zh' => '完整的供应周期：从产品搜索到交付到您的仓库'
        ],
        'get_consultation' => [
            'ru' => 'Получить консультацию',
            'en' => 'Get Consultation',
            'zh' => '获取咨询'
        ],
        'calculate_delivery' => [
            'ru' => 'Рассчитать доставку',
            'en' => 'Calculate Delivery',
            'zh' => '计算运费'
        ],
        
        // Статистика
        'happy_clients' => [
            'ru' => 'Довольных клиентов',
            'en' => 'Happy Clients',
            'zh' => '满意的客户'
        ],
        'years_on_market' => [
            'ru' => 'Лет на рынке',
            'en' => 'Years on Market',
            'zh' => '市场年限'
        ],
        'containers_delivered' => [
            'ru' => 'Контейнеров доставлено',
            'en' => 'Containers Delivered',
            'zh' => '已交付集装箱'
        ],
        'delivery_cities' => [
            'ru' => 'Городов доставки',
            'en' => 'Delivery Cities',
            'zh' => '配送城市'
        ],
        
        // Услуги
        'our_services' => [
            'ru' => 'Наши услуги',
            'en' => 'Our Services',
            'zh' => '我们的服务'
        ],
        'product_search' => [
            'ru' => 'Поиск товаров',
            'en' => 'Product Search',
            'zh' => '产品搜索'
        ],
        'product_search_desc' => [
            'ru' => 'Найдем нужные товары на китайских площадках по вашим требованиям',
            'en' => 'We will find the right products on Chinese platforms according to your requirements',
            'zh' => '我们将根据您的要求在中国平台上找到合适的产品'
        ],
        'quality_control' => [
            'ru' => 'Контроль качества',
            'en' => 'Quality Control',
            'zh' => '质量控制'
        ],
        'quality_control_desc' => [
            'ru' => 'Проверка товара перед отправкой на соответствие стандартам',
            'en' => 'Product inspection before shipment for compliance with standards',
            'zh' => '发货前检查产品是否符合标准'
        ],
        'customs_clearance' => [
            'ru' => 'Таможенное оформление',
            'en' => 'Customs Clearance',
            'zh' => '清关'
        ],
        'customs_clearance_desc' => [
            'ru' => 'Полное сопровождение груза через таможню',
            'en' => 'Full cargo support through customs',
            'zh' => '通过海关的全程货物支持'
        ],
        'warehousing' => [
            'ru' => 'Складское хранение',
            'en' => 'Warehousing',
            'zh' => '仓储'
        ],
        'warehousing_desc' => [
            'ru' => 'Хранение товара на наших складах в Китае и России',
            'en' => 'Storage of goods in our warehouses in China and Russia',
            'zh' => '在我们位于中国和俄罗斯的仓库中存储货物'
        ],
        'packaging' => [
            'ru' => 'Упаковка и маркировка',
            'en' => 'Packaging & Labeling',
            'zh' => '包装和标签'
        ],
        'packaging_desc' => [
            'ru' => 'Подготовка товара для маркетплейсов по требованиям',
            'en' => 'Product preparation for marketplaces according to requirements',
            'zh' => '根据要求为市场准备产品'
        ],
        'insurance' => [
            'ru' => 'Страхование грузов',
            'en' => 'Cargo Insurance',
            'zh' => '货物保险'
        ],
        'insurance_desc' => [
            'ru' => 'Защита вашего груза от рисков при транспортировке',
            'en' => 'Protection of your cargo from risks during transportation',
            'zh' => '在运输过程中保护您的货物免受风险'
        ],
        
        // Способы доставки
        'delivery_methods' => [
            'ru' => 'Способы доставки',
            'en' => 'Delivery Methods',
            'zh' => '配送方式'
        ],
        'air_delivery' => [
            'ru' => 'Авиадоставка',
            'en' => 'Air Delivery',
            'zh' => '空运'
        ],
        'air_delivery_desc' => [
            'ru' => 'Самый быстрый способ. Доставка за 5-10 дней.',
            'en' => 'The fastest way. Delivery in 5-10 days.',
            'zh' => '最快的方式。5-10天内交付。'
        ],
        'sea_delivery' => [
            'ru' => 'Морская доставка',
            'en' => 'Sea Delivery',
            'zh' => '海运'
        ],
        'sea_delivery_desc' => [
            'ru' => 'Экономичный вариант для больших объемов. 35-45 дней.',
            'en' => 'Economical option for large volumes. 35-45 days.',
            'zh' => '大批量的经济选择。35-45天。'
        ],
        'rail_delivery' => [
            'ru' => 'Ж/Д доставка',
            'en' => 'Rail Delivery',
            'zh' => '铁路运输'
        ],
        'rail_delivery_desc' => [
            'ru' => 'Оптимальное соотношение цены и скорости. 18-25 дней.',
            'en' => 'Optimal price-speed ratio. 18-25 days.',
            'zh' => '最佳性价比。18-25天。'
        ],
        'auto_delivery' => [
            'ru' => 'Автодоставка',
            'en' => 'Auto Delivery',
            'zh' => '汽车运输'
        ],
        'auto_delivery_desc' => [
            'ru' => 'Гибкий маршрут и сроки. 12-18 дней.',
            'en' => 'Flexible route and timing. 12-18 days.',
            'zh' => '灵活的路线和时间。12-18天。'
        ],
        
        // Калькулятор
        'delivery_calculator' => [
            'ru' => 'Калькулятор доставки',
            'en' => 'Delivery Calculator',
            'zh' => '运费计算器'
        ],
        'weight' => [
            'ru' => 'Вес (кг)',
            'en' => 'Weight (kg)',
            'zh' => '重量（公斤）'
        ],
        'volume' => [
            'ru' => 'Объем (м³)',
            'en' => 'Volume (m³)',
            'zh' => '体积（立方米）'
        ],
        'product_value' => [
            'ru' => 'Стоимость товара',
            'en' => 'Product Value',
            'zh' => '产品价值'
        ],
        'category' => [
            'ru' => 'Категория',
            'en' => 'Category',
            'zh' => '类别'
        ],
        'from' => [
            'ru' => 'Откуда',
            'en' => 'From',
            'zh' => '从'
        ],
        'to' => [
            'ru' => 'Куда',
            'en' => 'To',
            'zh' => '到'
        ],
        'calculate' => [
            'ru' => 'Рассчитать',
            'en' => 'Calculate',
            'zh' => '计算'
        ],
        'download_pdf' => [
            'ru' => 'Скачать PDF',
            'en' => 'Download PDF',
            'zh' => '下载PDF'
        ],
        
        // Категории товаров
        'electronics' => [
            'ru' => 'Электроника',
            'en' => 'Electronics',
            'zh' => '电子产品'
        ],
        'clothing' => [
            'ru' => 'Одежда и обувь',
            'en' => 'Clothing & Footwear',
            'zh' => '服装和鞋类'
        ],
        'toys' => [
            'ru' => 'Игрушки',
            'en' => 'Toys',
            'zh' => '玩具'
        ],
        'household' => [
            'ru' => 'Товары для дома',
            'en' => 'Household Goods',
            'zh' => '家居用品'
        ],
        'cosmetics' => [
            'ru' => 'Косметика',
            'en' => 'Cosmetics',
            'zh' => '化妆品'
        ],
        'auto_parts' => [
            'ru' => 'Автозапчасти',
            'en' => 'Auto Parts',
            'zh' => '汽车零件'
        ],
        'sports' => [
            'ru' => 'Спорттовары',
            'en' => 'Sports Goods',
            'zh' => '体育用品'
        ],
        
        // Контакты
        'contact_us' => [
            'ru' => 'Свяжитесь с нами',
            'en' => 'Contact Us',
            'zh' => '联系我们'
        ],
        'your_name' => [
            'ru' => 'Ваше имя',
            'en' => 'Your Name',
            'zh' => '您的姓名'
        ],
        'your_phone' => [
            'ru' => 'Ваш телефон',
            'en' => 'Your Phone',
            'zh' => '您的电话'
        ],
        'your_email' => [
            'ru' => 'Ваш email',
            'en' => 'Your Email',
            'zh' => '您的电子邮件'
        ],
        'your_message' => [
            'ru' => 'Ваше сообщение',
            'en' => 'Your Message',
            'zh' => '您的留言'
        ],
        'send' => [
            'ru' => 'Отправить',
            'en' => 'Send',
            'zh' => '发送'
        ],
        'sending' => [
            'ru' => 'Отправка...',
            'en' => 'Sending...',
            'zh' => '发送中...'
        ],
        'success_message' => [
            'ru' => 'Спасибо! Мы свяжемся с вами в ближайшее время.',
            'en' => 'Thank you! We will contact you soon.',
            'zh' => '谢谢！我们会尽快与您联系。'
        ],
        'error_message' => [
            'ru' => 'Ошибка отправки. Попробуйте позже.',
            'en' => 'Sending error. Please try again later.',
            'zh' => '发送错误。请稍后再试。'
        ],
        
        // Футер
        'all_rights_reserved' => [
            'ru' => 'Все права защищены',
            'en' => 'All rights reserved',
            'zh' => '版权所有'
        ],
        'privacy_policy' => [
            'ru' => 'Политика конфиденциальности',
            'en' => 'Privacy Policy',
            'zh' => '隐私政策'
        ],
        'terms_of_service' => [
            'ru' => 'Условия использования',
            'en' => 'Terms of Service',
            'zh' => '服务条款'
        ],
        
        // Кнопки
        'learn_more' => [
            'ru' => 'Узнать больше',
            'en' => 'Learn More',
            'zh' => '了解更多'
        ],
        'order_now' => [
            'ru' => 'Заказать сейчас',
            'en' => 'Order Now',
            'zh' => '立即订购'
        ],
        'back_to_top' => [
            'ru' => 'Наверх',
            'en' => 'Back to Top',
            'zh' => '返回顶部'
        ],
        'close' => [
            'ru' => 'Закрыть',
            'en' => 'Close',
            'zh' => '关闭'
        ],
        'reset' => [
            'ru' => 'Сбросить',
            'en' => 'Reset',
            'zh' => '重置'
        ],
        
        // Время
        'days' => [
            'ru' => 'дней',
            'en' => 'days',
            'zh' => '天'
        ],
        'hours' => [
            'ru' => 'часов',
            'en' => 'hours',
            'zh' => '小时'
        ],
        'minutes' => [
            'ru' => 'минут',
            'en' => 'minutes',
            'zh' => '分钟'
        ]
    ];
}

/**
 * Получить перевод строки
 */
function atk_ved_translate(string $key, ?string $lang = null): string {
    if ($lang === null) {
        $lang = atk_ved_get_current_language();
    }
    
    $translations = atk_ved_get_translations();
    
    if (isset($translations[$key][$lang])) {
        return $translations[$key][$lang];
    }
    
    // Fallback на русский
    if (isset($translations[$key]['ru'])) {
        return $translations[$key]['ru'];
    }
    
    return $key;
}

/**
 * Короткая функция для перевода
 */
function __t(string $key, ?string $lang = null): string {
    return atk_ved_translate($key, $lang);
}

/**
 * Вывод перевода с экранированием
 */
function _et(string $key, ?string $lang = null): void {
    echo esc_html(atk_ved_translate($key, $lang));
}

/**
 * Виджет переключателя языков
 */
function atk_ved_language_switcher(array $args = []): string {
    $args = wp_parse_args($args, [
        'show_flags' => true,
        'show_names' => true,
        'style' => 'dropdown', // dropdown, list, flags
        'class' => ''
    ]);
    
    $current_lang = atk_ved_get_current_language();
    $languages = atk_ved_get_languages();
    $enabled_languages = array_filter($languages, function($lang) {
        return $lang['enabled'];
    });
    
    if (count($enabled_languages) <= 1) {
        return '';
    }
    
    ob_start();
    
    if ($args['style'] === 'dropdown') {
        ?>
        <div class="language-switcher language-switcher-dropdown <?php echo esc_attr($args['class']); ?>">
            <button class="lang-current" aria-label="<?php _et('select_language'); ?>" aria-haspopup="true">
                <?php if ($args['show_flags']): ?>
                    <span class="lang-flag"><?php echo esc_html($languages[$current_lang]['flag']); ?></span>
                <?php endif; ?>
                <?php if ($args['show_names']): ?>
                    <span class="lang-name"><?php echo esc_html($languages[$current_lang]['native_name']); ?></span>
                <?php endif; ?>
                <svg class="lang-arrow" width="12" height="12" viewBox="0 0 12 12" fill="currentColor">
                    <path d="M6 9L1 4h10z"/>
                </svg>
            </button>
            <ul class="lang-dropdown" role="menu">
                <?php foreach ($enabled_languages as $code => $lang): ?>
                    <?php if ($code === $current_lang) continue; ?>
                    <li role="none">
                        <a href="#" 
                           class="lang-option" 
                           data-lang="<?php echo esc_attr($code); ?>"
                           role="menuitem">
                            <?php if ($args['show_flags']): ?>
                                <span class="lang-flag"><?php echo esc_html($lang['flag']); ?></span>
                            <?php endif; ?>
                            <?php if ($args['show_names']): ?>
                                <span class="lang-name"><?php echo esc_html($lang['native_name']); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
    } elseif ($args['style'] === 'list') {
        ?>
        <ul class="language-switcher language-switcher-list <?php echo esc_attr($args['class']); ?>">
            <?php foreach ($enabled_languages as $code => $lang): ?>
                <li class="<?php echo $code === $current_lang ? 'active' : ''; ?>">
                    <a href="#" 
                       class="lang-option" 
                       data-lang="<?php echo esc_attr($code); ?>"
                       <?php echo $code === $current_lang ? 'aria-current="true"' : ''; ?>>
                        <?php if ($args['show_flags']): ?>
                            <span class="lang-flag"><?php echo esc_html($lang['flag']); ?></span>
                        <?php endif; ?>
                        <?php if ($args['show_names']): ?>
                            <span class="lang-name"><?php echo esc_html($lang['native_name']); ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php
    } elseif ($args['style'] === 'flags') {
        ?>
        <div class="language-switcher language-switcher-flags <?php echo esc_attr($args['class']); ?>">
            <?php foreach ($enabled_languages as $code => $lang): ?>
                <a href="#" 
                   class="lang-flag-btn <?php echo $code === $current_lang ? 'active' : ''; ?>" 
                   data-lang="<?php echo esc_attr($code); ?>"
                   title="<?php echo esc_attr($lang['native_name']); ?>"
                   <?php echo $code === $current_lang ? 'aria-current="true"' : ''; ?>>
                    <span class="lang-flag"><?php echo esc_html($lang['flag']); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
        <?php
    }
    
    return ob_get_clean();
}

/**
 * Шорткод переключателя языков
 */
function atk_ved_language_switcher_shortcode(array $atts): string {
    $atts = shortcode_atts([
        'show_flags' => 'yes',
        'show_names' => 'yes',
        'style' => 'dropdown',
        'class' => ''
    ], $atts);
    
    return atk_ved_language_switcher([
        'show_flags' => $atts['show_flags'] === 'yes',
        'show_names' => $atts['show_names'] === 'yes',
        'style' => $atts['style'],
        'class' => $atts['class']
    ]);
}
add_shortcode('language_switcher', 'atk_ved_language_switcher_shortcode');

/**
 * Настройки мультиязычности в Customizer
 */
function atk_ved_multilingual_customizer($wp_customize): void {
    $wp_customize->add_section('atk_ved_multilingual', [
        'title' => __('Мультиязычность', 'atk-ved'),
        'priority' => 45,
    ]);
    
    // Включить мультиязычность
    $wp_customize->add_setting('atk_ved_multilingual_enabled', [
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ]);
    
    $wp_customize->add_control('atk_ved_multilingual_enabled', [
        'label' => __('Включить мультиязычность', 'atk-ved'),
        'section' => 'atk_ved_multilingual',
        'type' => 'checkbox',
    ]);
    
    // Язык по умолчанию
    $wp_customize->add_setting('atk_ved_default_language', [
        'default' => 'ru',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    
    $wp_customize->add_control('atk_ved_default_language', [
        'label' => __('Язык по умолчанию', 'atk-ved'),
        'section' => 'atk_ved_multilingual',
        'type' => 'select',
        'choices' => [
            'ru' => 'Русский',
            'en' => 'English',
            'zh' => '中文'
        ]
    ]);
    
    // Включить английский
    $wp_customize->add_setting('atk_ved_enable_english', [
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ]);
    
    $wp_customize->add_control('atk_ved_enable_english', [
        'label' => __('Включить английский', 'atk-ved'),
        'section' => 'atk_ved_multilingual',
        'type' => 'checkbox',
    ]);
    
    // Включить китайский
    $wp_customize->add_setting('atk_ved_enable_chinese', [
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ]);
    
    $wp_customize->add_control('atk_ved_enable_chinese', [
        'label' => __('Включить китайский', 'atk-ved'),
        'section' => 'atk_ved_multilingual',
        'type' => 'checkbox',
    ]);
    
    // Стиль переключателя
    $wp_customize->add_setting('atk_ved_lang_switcher_style', [
        'default' => 'dropdown',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    
    $wp_customize->add_control('atk_ved_lang_switcher_style', [
        'label' => __('Стиль переключателя', 'atk-ved'),
        'section' => 'atk_ved_multilingual',
        'type' => 'select',
        'choices' => [
            'dropdown' => 'Выпадающий список',
            'list' => 'Горизонтальный список',
            'flags' => 'Только флаги'
        ]
    ]);
}
add_action('customize_register', 'atk_ved_multilingual_customizer');

/**
 * Инициализация языка при загрузке
 */
function atk_ved_init_language(): void {
    $current_lang = atk_ved_get_current_language();
    atk_ved_set_language($current_lang);
}
add_action('init', 'atk_ved_init_language');

/**
 * Добавить переключатель языков в меню
 */
function atk_ved_add_language_switcher_to_menu($items, $args): string {
    if ($args->theme_location === 'primary') {
        $switcher = atk_ved_language_switcher([
            'show_flags' => true,
            'show_names' => false,
            'style' => 'flags',
            'class' => 'menu-lang-switcher'
        ]);
        $items .= '<li class="menu-item menu-item-language">' . $switcher . '</li>';
    }
    return $items;
}
add_filter('wp_nav_menu_items', 'atk_ved_add_language_switcher_to_menu', 10, 2);
