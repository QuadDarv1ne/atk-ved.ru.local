<?php
/**
 * ACF Options Page & Helper Functions
 * 
 * @package ATK_VED
 * @since 2.3.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

// Проверка наличия ACF
if (!class_exists('ACF')) {
    return;
}

/**
 * Регистрация страницы опций темы
 */
function atk_ved_acf_add_options_page(): void {
    if (!function_exists('acf_add_options_page')) {
        return;
    }
    
    // Главная страница настроек
    acf_add_options_page(array(
        'page_title' => __('Настройки темы АТК ВЭД', 'atk-ved'),
        'menu_title' => __('Настройки темы', 'atk-ved'),
        'menu_slug' => 'atk-ved-theme-settings',
        'capability' => 'edit_theme_options',
        'redirect' => false,
        'position' => 60,
        'icon_url' => 'dashicons-admin-multisite',
    ));
    
    // Подстраница: Контакты
    acf_add_options_sub_page(array(
        'page_title' => __('Контакты', 'atk-ved'),
        'menu_title' => __('Контакты', 'atk-ved'),
        'parent_slug' => 'atk-ved-theme-settings',
        'menu_slug' => 'atk-ved-contacts',
    ));
    
    // Подстраница: Соцсети
    acf_add_options_sub_page(array(
        'page_title' => __('Социальные сети', 'atk-ved'),
        'menu_title' => __('Соцсети', 'atk-ved'),
        'parent_slug' => 'atk-ved-theme-settings',
        'menu_slug' => 'atk-ved-social',
    ));
    
    // Подстраница: SEO
    acf_add_options_sub_page(array(
        'page_title' => __('SEO настройки', 'atk-ved'),
        'menu_title' => __('SEO', 'atk-ved'),
        'parent_slug' => 'atk-ved-theme-settings',
        'menu_slug' => 'atk-ved-seo',
    ));
    
    // Подстраница: Аналитика
    acf_add_options_sub_page(array(
        'page_title' => __('Аналитика и счётчики', 'atk-ved'),
        'menu_title' => __('Аналитика', 'atk-ved'),
        'parent_slug' => 'atk-ved-theme-settings',
        'menu_slug' => 'atk-ved-analytics',
    ));
}

add_action('acf/init', 'atk_ved_acf_add_options_page');

/**
 * Helper функции для работы с ACF
 */

/**
 * Получить значение поля с fallback
 * 
 * @param string $field_name Имя поля
 * @param mixed $default Значение по умолчанию
 * @param int|null $post_id ID поста (null для текущей записи)
 * @return mixed
 */
function atk_ved_get_field(string $field_name, $default = '', ?int $post_id = null) {
    if (!function_exists('get_field')) {
        return $default;
    }
    
    $value = get_field($field_name, $post_id);
    
    if ($value === null || $value === '') {
        return $default;
    }
    
    return $value;
}

/**
 * Получить значение из опций темы
 * 
 * @param string $field_name Имя поля
 * @param mixed $default Значение по умолчанию
 * @return mixed
 */
function atk_ved_get_option(string $field_name, $default = '') {
    return atk_ved_get_field($field_name, $default, 'option');
}

/**
 * Получить подполе из repeater
 * 
 * @param string $field_name Имя поля
 * @param string $sub_field Имя подполя
 * @param int $index Индекс в repeater
 * @param mixed $default Значение по умолчанию
 * @return mixed
 */
function atk_ved_get_sub_field(string $field_name, string $sub_field, int $index = 0, $default = '') {
    if (!function_exists('get_field')) {
        return $default;
    }
    
    $repeater = get_field($field_name);
    
    if (!is_array($repeater) || !isset($repeater[$index])) {
        return $default;
    }
    
    $value = $repeater[$index][$sub_field] ?? null;
    
    if ($value === null || $value === '') {
        return $default;
    }
    
    return $value;
}

/**
 * Вывести значение поля с экранированием
 * 
 * @param string $field_name Имя поля
 * @param mixed $default Значение по умолчанию
 * @param int|null $post_id ID поста
 */
function atk_ved_the_field(string $field_name, $default = '', ?int $post_id = null): void {
    $value = atk_ved_get_field($field_name, $default, $post_id);
    echo esc_html($value);
}

/**
 * Вывести HTML значение поля
 * 
 * @param string $field_name Имя поля
 * @param mixed $default Значение по умолчанию
 * @param int|null $post_id ID поста
 */
function atk_ved_the_field_html(string $field_name, $default = '', ?int $post_id = null): void {
    $value = atk_ved_get_field($field_name, $default, $post_id);
    echo wp_kses_post($value);
}

/**
 * Проверить наличие значения поля
 * 
 * @param string $field_name Имя поля
 * @param int|null $post_id ID поста
 * @return bool
 */
function atk_ved_have_field(string $field_name, ?int $post_id = null): bool {
    $value = atk_ved_get_field($field_name, '', $post_id);
    return !empty($value);
}

/**
 * Получить изображение
 * 
 * @param string $field_name Имя поля
 * @param string $size Размер изображения
 * @param array $default Изображение по умолчанию
 * @param int|null $post_id ID поста
 * @return array|null
 */
function atk_ved_get_image(string $field_name, string $size = 'full', array $default = [], ?int $post_id = null): ?array {
    $image = atk_ved_get_field($field_name, null, $post_id);
    
    if (!$image) {
        return $default ?: null;
    }
    
    // Если уже массив с размерами
    if (isset($image['sizes'])) {
        if (isset($image['sizes'][$size])) {
            $image['url'] = $image['sizes'][$size];
        }
        return $image;
    }
    
    // Если URL
    return array(
        'url' => $image,
        'alt' => '',
        'width' => 0,
        'height' => 0,
    );
}

/**
 * Вывести изображение
 * 
 * @param string $field_name Имя поля
 * @param string $size Размер изображения
 * @param array $attr Атрибуты img
 * @param int|null $post_id ID поста
 */
function atk_ved_the_image(string $field_name, string $size = 'full', array $attr = [], ?int $post_id = null): void {
    $image = atk_ved_get_image($field_name, $size, array(), $post_id);
    
    if (!$image) {
        return;
    }
    
    $defaults = array(
        'src' => esc_url($image['url']),
        'alt' => esc_attr($image['alt'] ?? ''),
        'width' => esc_attr($image['width'] ?? 0),
        'height' => esc_attr($image['height'] ?? 0),
        'loading' => 'lazy',
    );
    
    $attr = wp_parse_args($attr, $defaults);
    
    echo '<img ' . _build_query($attr) . '>';
}

/**
 * Получить repeater
 * 
 * @param string $field_name Имя поля
 * @param int|null $post_id ID поста
 * @return array
 */
function atk_ved_get_repeater(string $field_name, ?int $post_id = null): array {
    $repeater = atk_ved_get_field($field_name, array(), $post_id);
    
    if (!is_array($repeater)) {
        return array();
    }
    
    return $repeater;
}

/**
 * Проверить repeater на наличие данных
 * 
 * @param string $field_name Имя поля
 * @param int|null $post_id ID поста
 * @return bool
 */
function atk_ved_have_rows(string $field_name, ?int $post_id = null): bool {
    if (!function_exists('have_rows')) {
        return !empty(atk_ved_get_repeater($field_name, $post_id));
    }
    
    return have_rows($field_name, $post_id);
}

/**
 * Получить настройки темы из опций
 * 
 * @return array
 */
function atk_ved_get_theme_settings(): array {
    return array(
        'hero_title' => atk_ved_get_option('hero_title', __('ТОВАРЫ ДЛЯ МАРКЕТПЛЕЙСОВ ИЗ КИТАЯ ОПТОМ', 'atk-ved')),
        'hero_subtitle' => atk_ved_get_option('hero_subtitle', ''),
        'hero_features' => atk_ved_get_repeater('hero_features'),
        'hero_image' => atk_ved_get_image('hero_image'),
        'hero_stats' => atk_ved_get_repeater('hero_stats'),
        'header_phone' => atk_ved_get_option('header_phone', ''),
        'header_email' => atk_ved_get_option('header_email', ''),
        'header_working_hours' => atk_ved_get_option('header_working_hours', ''),
        'social_networks' => atk_ved_get_repeater('social_networks'),
        'seo_title' => atk_ved_get_option('seo_title', ''),
        'seo_description' => atk_ved_get_option('seo_description', ''),
        'seo_keywords' => atk_ved_get_option('seo_keywords', ''),
    );
}

/**
 * Получить контакты
 * 
 * @return array
 */
function atk_ved_get_contacts(): array {
    return array(
        'phone' => atk_ved_get_option('header_phone', '+7 (XXX) XXX-XX-XX'),
        'email' => atk_ved_get_option('header_email', 'info@atk-ved.ru'),
        'working_hours' => atk_ved_get_option('header_working_hours', 'Пн-Пт 9:00-18:00'),
        'address' => atk_ved_get_option('address', 'Москва, Россия'),
    );
}

/**
 * Получить социальные сети
 * 
 * @return array
 */
function atk_ved_get_social(): array {
    $social = atk_ved_get_repeater('social_networks');
    
    $result = array();
    foreach ($social as $item) {
        if (!empty($item['url'])) {
            $result[$item['icon']] = array(
                'name' => $item['name'] ?? '',
                'url' => $item['url'],
                'icon' => $item['icon'] ?? '',
            );
        }
    }
    
    return $result;
}

/**
 * Получить URL соцсети
 * 
 * @param string $network Название сети
 * @param string $default Значение по умолчанию
 * @return string
 */
function atk_ved_get_social_url(string $network, string $default = ''): string {
    $social = atk_ved_get_social();
    return $social[$network]['url'] ?? $default;
}

/**
 * Получить SEO данные
 * 
 * @return array
 */
function atk_ved_get_seo(): array {
    return array(
        'title' => atk_ved_get_option('seo_title', ''),
        'description' => atk_ved_get_option('seo_description', ''),
        'keywords' => atk_ved_get_option('seo_keywords', ''),
    );
}

/**
 * Вспомогательная функция для построения query string
 */
function _build_query(array $attr): string {
    $parts = array();
    foreach ($attr as $key => $value) {
        $parts[] = esc_attr($key) . '="' . esc_attr($value) . '"';
    }
    return implode(' ', $parts);
}
