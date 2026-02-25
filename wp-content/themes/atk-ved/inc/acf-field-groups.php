<?php
/**
 * ACF Field Groups Configuration
 * Программная регистрация групп полей ACF
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
    add_action('admin_notices', function() {
        echo '<div class="notice notice-warning"><p>';
        echo __('Для работы расширенных настроек требуется плагин <strong>Advanced Custom Fields</strong>.', 'atk-ved');
        echo '</p></div>';
    });
    return;
}

/**
 * Регистрация групп полей
 */
function atk_ved_acf_add_field_groups(): void {
    
    /* ==========================================================================
       THEME SETTINGS - Общие настройки темы
       ========================================================================== */
    
    acf_add_local_field_group(array(
        'key' => 'group_atk_ved_theme_settings',
        'title' => __('Настройки темы АТК ВЭД', 'atk-ved'),
        'fields' => array(
            // Вкладка: Главная
            array(
                'key' => 'field_atk_ved_hero_section',
                'label' => __('Главный экран (Hero)', 'atk-ved'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_atk_ved_hero_title',
                'label' => __('Заголовок H1', 'atk-ved'),
                'name' => 'hero_title',
                'type' => 'text',
                'default_value' => __('ТОВАРЫ ДЛЯ МАРКЕТПЛЕЙСОВ ИЗ КИТАЯ ОПТОМ', 'atk-ved'),
                'placeholder' => __('Введите заголовок', 'atk-ved'),
                'maxlength' => 100,
            ),
            array(
                'key' => 'field_atk_ved_hero_subtitle',
                'label' => __('Подзаголовок', 'atk-ved'),
                'name' => 'hero_subtitle',
                'type' => 'textarea',
                'default_value' => __('Полный цикл поставок от поиска до доставки', 'atk-ved'),
                'placeholder' => __('Введите подзаголовок', 'atk-ved'),
                'rows' => 2,
            ),
            array(
                'key' => 'field_atk_ved_hero_features',
                'label' => __('Преимущества (список)', 'atk-ved'),
                'name' => 'hero_features',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => __('Добавить преимущество', 'atk-ved'),
                'sub_fields' => array(
                    array(
                        'key' => 'field_atk_ved_hero_feature_text',
                        'label' => __('Текст', 'atk-ved'),
                        'name' => 'text',
                        'type' => 'text',
                        'width' => '80',
                    ),
                    array(
                        'key' => 'field_atk_ved_hero_feature_icon',
                        'label' => __('Иконка', 'atk-ved'),
                        'name' => 'icon',
                        'type' => 'select',
                        'width' => '20',
                        'choices' => array(
                            '✓' => 'Галочка',
                            '★' => 'Звезда',
                            '→' => 'Стрелка',
                            '●' => 'Точка',
                        ),
                        'default_value' => '✓',
                    ),
                ),
                'min' => 1,
                'max' => 6,
            ),
            array(
                'key' => 'field_atk_ved_hero_image',
                'label' => __('Изображение', 'atk-ved'),
                'name' => 'hero_image',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
                'instructions' => __('Рекомендуемый размер: 800x600px', 'atk-ved'),
            ),
            
            // Вкладка: Статистика
            array(
                'key' => 'field_atk_ved_stats_section',
                'label' => __('Статистика', 'atk-ved'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_atk_ved_stats',
                'label' => __('Показатели статистики', 'atk-ved'),
                'name' => 'hero_stats',
                'type' => 'repeater',
                'layout' => 'block',
                'button_label' => __('Добавить показатель', 'atk-ved'),
                'sub_fields' => array(
                    array(
                        'key' => 'field_atk_ved_stat_number',
                        'label' => __('Число', 'atk-ved'),
                        'name' => 'number',
                        'type' => 'text',
                        'placeholder' => '500+',
                    ),
                    array(
                        'key' => 'field_atk_ved_stat_label',
                        'label' => __('Подпись', 'atk-ved'),
                        'name' => 'label',
                        'type' => 'text',
                        'placeholder' => 'КЛИЕНТОВ',
                    ),
                ),
                'min' => 1,
                'max' => 4,
                'default_value' => array(
                    array('number' => '500+', 'label' => 'КЛИЕНТОВ'),
                    array('number' => '10+', 'label' => 'ЛЕТ ОПЫТА'),
                    array('number' => '98%', 'label' => 'УСПЕХА'),
                ),
            ),
            
            // Вкладка: Контакты в хедере
            array(
                'key' => 'field_atk_ved_header_contacts',
                'label' => __('Контакты в шапке', 'atk-ved'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_atk_ved_header_phone',
                'label' => __('Телефон', 'atk-ved'),
                'name' => 'header_phone',
                'type' => 'text',
                'placeholder' => '+7 (XXX) XXX-XX-XX',
            ),
            array(
                'key' => 'field_atk_ved_header_email',
                'label' => __('Email', 'atk-ved'),
                'name' => 'header_email',
                'type' => 'email',
                'placeholder' => 'info@atk-ved.ru',
            ),
            array(
                'key' => 'field_atk_ved_header_working_hours',
                'label' => __('Режим работы', 'atk-ved'),
                'name' => 'header_working_hours',
                'type' => 'text',
                'placeholder' => 'Пн-Пт 9:00-18:00',
            ),
            
            // Вкладка: Социальные сети
            array(
                'key' => 'field_atk_ved_social_section',
                'label' => __('Социальные сети', 'atk-ved'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_atk_ved_social_networks',
                'label' => __('Соцсети', 'atk-ved'),
                'name' => 'social_networks',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => __('Добавить соцсеть', 'atk-ved'),
                'sub_fields' => array(
                    array(
                        'key' => 'field_atk_ved_social_name',
                        'label' => __('Название', 'atk-ved'),
                        'name' => 'name',
                        'type' => 'text',
                        'width' => '30',
                    ),
                    array(
                        'key' => 'field_atk_ved_social_icon',
                        'label' => __('Иконка', 'atk-ved'),
                        'name' => 'icon',
                        'type' => 'select',
                        'width' => '20',
                        'choices' => array(
                            'telegram' => 'Telegram',
                            'whatsapp' => 'WhatsApp',
                            'vk' => 'VK',
                            'youtube' => 'YouTube',
                        ),
                    ),
                    array(
                        'key' => 'field_atk_ved_social_url',
                        'label' => __('Ссылка', 'atk-ved'),
                        'name' => 'url',
                        'type' => 'url',
                        'width' => '50',
                    ),
                ),
            ),
            
            // Вкладка: SEO
            array(
                'key' => 'field_atk_ved_seo_section',
                'label' => __('SEO настройки', 'atk-ved'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_atk_ved_seo_title',
                'label' => __('SEO Title', 'atk-ved'),
                'name' => 'seo_title',
                'type' => 'text',
                'placeholder' => __('Заголовок для поисковиков', 'atk-ved'),
                'maxlength' => 60,
            ),
            array(
                'key' => 'field_atk_ved_seo_description',
                'label' => __('SEO Description', 'atk-ved'),
                'name' => 'seo_description',
                'type' => 'textarea',
                'placeholder' => __('Описание для поисковиков', 'atk-ved'),
                'rows' => 3,
                'maxlength' => 160,
            ),
            array(
                'key' => 'field_atk_ved_seo_keywords',
                'label' => __('SEO Keywords', 'atk-ved'),
                'name' => 'seo_keywords',
                'type' => 'text',
                'placeholder' => __('Ключевые слова через запятую', 'atk-ved'),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'atk-ved-theme-settings',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
    ));
    
    /* ==========================================================================
       SERVICES - Услуги
       ========================================================================== */
    
    acf_add_local_field_group(array(
        'key' => 'group_atk_ved_services',
        'title' => __('Услуги', 'atk-ved'),
        'fields' => array(
            array(
                'key' => 'field_atk_ved_service_icon',
                'label' => __('Иконка', 'atk-ved'),
                'name' => 'service_icon',
                'type' => 'text',
                'placeholder' => '📦',
                'instructions' => __('Emoji или символ', 'atk-ved'),
            ),
            array(
                'key' => 'field_atk_ved_service_number',
                'label' => __('Номер', 'atk-ved'),
                'name' => 'service_number',
                'type' => 'text',
                'placeholder' => '01',
                'maxlength' => 2,
            ),
            array(
                'key' => 'field_atk_ved_service_short_desc',
                'label' => __('Краткое описание', 'atk-ved'),
                'name' => 'service_short_desc',
                'type' => 'textarea',
                'rows' => 3,
                'maxlength' => 200,
            ),
            array(
                'key' => 'field_atk_ved_service_features',
                'label' => __('Особенности', 'atk-ved'),
                'name' => 'service_features',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => __('Добавить особенность', 'atk-ved'),
                'sub_fields' => array(
                    array(
                        'key' => 'field_atk_ved_service_feature_text',
                        'label' => __('Текст', 'atk-ved'),
                        'name' => 'text',
                        'type' => 'text',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'service',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
    ));
    
    /* ==========================================================================
       FAQ - Часто задаваемые вопросы
       ========================================================================== */
    
    acf_add_local_field_group(array(
        'key' => 'group_atk_ved_faq',
        'title' => __('FAQ - Часто задаваемые вопросы', 'atk-ved'),
        'fields' => array(
            array(
                'key' => 'field_atk_ved_faq_icon',
                'label' => __('Иконка', 'atk-ved'),
                'name' => 'faq_icon',
                'type' => 'text',
                'default_value' => '❓',
            ),
            array(
                'key' => 'field_atk_ved_faq_category',
                'label' => __('Категория', 'atk-ved'),
                'name' => 'faq_category',
                'type' => 'select',
                'choices' => array(
                    'general' => __('Общие', 'atk-ved'),
                    'delivery' => __('Доставка', 'atk-ved'),
                    'payment' => __('Оплата', 'atk-ved'),
                    'customs' => __('Таможня', 'atk-ved'),
                ),
                'default_value' => 'general',
            ),
            array(
                'key' => 'field_atk_ved_faq_is_active',
                'label' => __('Показывать на сайте', 'atk-ved'),
                'name' => 'faq_is_active',
                'type' => 'true_false',
                'default_value' => 1,
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'faq',
                ),
            ),
        ),
    ));
    
    /* ==========================================================================
       TEAM - Команда
       ========================================================================== */
    
    acf_add_local_field_group(array(
        'key' => 'group_atk_ved_team',
        'title' => __('Члены команды', 'atk-ved'),
        'fields' => array(
            array(
                'key' => 'field_atk_ved_team_position',
                'label' => __('Должность', 'atk-ved'),
                'name' => 'team_position',
                'type' => 'text',
            ),
            array(
                'key' => 'field_atk_ved_team_photo',
                'label' => __('Фото', 'atk-ved'),
                'name' => 'team_photo',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'thumbnail',
            ),
            array(
                'key' => 'field_atk_ved_team_social',
                'label' => __('Соцсети', 'atk-ved'),
                'name' => 'team_social',
                'type' => 'repeater',
                'layout' => 'table',
                'sub_fields' => array(
                    array(
                        'key' => 'field_atk_ved_team_social_network',
                        'label' => __('Сеть', 'atk-ved'),
                        'name' => 'network',
                        'type' => 'select',
                        'choices' => array(
                            'telegram' => 'Telegram',
                            'whatsapp' => 'WhatsApp',
                            'vk' => 'VK',
                        ),
                    ),
                    array(
                        'key' => 'field_atk_ved_team_social_url',
                        'label' => __('Ссылка', 'atk-ved'),
                        'name' => 'url',
                        'type' => 'url',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'team',
                ),
            ),
        ),
    ));
    
    /* ==========================================================================
       PARTNERS - Партнёры
       ========================================================================== */
    
    acf_add_local_field_group(array(
        'key' => 'group_atk_ved_partners',
        'title' => __('Партнёры', 'atk-ved'),
        'fields' => array(
            array(
                'key' => 'field_atk_ved_partner_logo',
                'label' => __('Логотип', 'atk-ved'),
                'name' => 'partner_logo',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_atk_ved_partner_url',
                'label' => __('Ссылка на сайт', 'atk-ved'),
                'name' => 'partner_url',
                'type' => 'url',
            ),
            array(
                'key' => 'field_atk_ved_partner_is_featured',
                'label' => __('VIP партнёр', 'atk-ved'),
                'name' => 'partner_is_featured',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'partner',
                ),
            ),
        ),
    ));
}

// Проверка функции перед вызовом
if (function_exists('acf_add_local_field_group')) {
    add_action('init', 'atk_ved_acf_add_field_groups', 20);
}
