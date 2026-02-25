<?php
/**
 * ACF Blocks for Gutenberg
 * Регистрация кастомных блоков ACF
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
 * Регистрация ACF блоков
 */
function atk_ved_register_acf_blocks(): void {
    
    /* ==========================================================================
       HERO BLOCK - Главный экран
       ========================================================================== */
    
    acf_register_block_type(array(
        'name' => 'hero',
        'title' => __('Hero (Главный экран)', 'atk-ved'),
        'description' => __('Главный экран лендинга с заголовком и статистикой', 'atk-ved'),
        'render_template' => 'template-parts/blocks/hero/hero.php',
        'category' => 'atk-ved',
        'icon' => 'cover-image',
        'keywords' => array('hero', 'главный', 'лендинг'),
        'supports' => array(
            'align' => array('full'),
            'anchor' => true,
            'mode' => 'preview',
        ),
        'enqueue_assets' => function() {
            wp_enqueue_style('atk-ved-hero-block', get_template_directory_uri() . '/css/blocks/hero.css', array(), '2.3');
        },
    ));
    
    /* ==========================================================================
       SERVICES BLOCK - Услуги
       ========================================================================== */
    
    acf_register_block_type(array(
        'name' => 'services',
        'title' => __('Services (Услуги)', 'atk-ved'),
        'description' => __('Сетка услуг с иконками', 'atk-ved'),
        'render_template' => 'template-parts/blocks/services/services.php',
        'category' => 'atk-ved',
        'icon' => 'grid-view',
        'keywords' => array('services', 'услуги', 'карточки'),
        'supports' => array(
            'align' => array('full'),
            'anchor' => true,
            'mode' => 'preview',
        ),
        'enqueue_assets' => function() {
            wp_enqueue_style('atk-ved-services-block', get_template_directory_uri() . '/css/blocks/services.css', array(), '2.3');
        },
    ));
    
    /* ==========================================================================
       STATS BLOCK - Статистика
       ========================================================================== */
    
    acf_register_block_type(array(
        'name' => 'stats',
        'title' => __('Stats (Статистика)', 'atk-ved'),
        'description' => __('Счётчики статистики с анимацией', 'atk-ved'),
        'render_template' => 'template-parts/blocks/stats/stats.php',
        'category' => 'atk-ved',
        'icon' => 'chart-bar',
        'keywords' => array('stats', 'статистика', 'счётчики'),
        'supports' => array(
            'align' => array('wide', 'full'),
            'anchor' => true,
            'mode' => 'preview',
        ),
        'enqueue_assets' => function() {
            wp_enqueue_style('atk-ved-stats-block', get_template_directory_uri() . '/css/blocks/stats.css', array(), '2.3');
            wp_enqueue_script('atk-ved-stats-block', get_template_directory_uri() . '/js/blocks/stats.js', array('jquery'), '2.3', true);
        },
    ));
    
    /* ==========================================================================
       TESTIMONIALS BLOCK - Отзывы
       ========================================================================== */
    
    acf_register_block_type(array(
        'name' => 'testimonials',
        'title' => __('Testimonials (Отзывы)', 'atk-ved'),
        'description' => __('Слайдер с отзывами клиентов', 'atk-ved'),
        'render_template' => 'template-parts/blocks/testimonials/testimonials.php',
        'category' => 'atk-ved',
        'icon' => 'format-quote',
        'keywords' => array('testimonials', 'отзывы', 'клиенты'),
        'supports' => array(
            'align' => array('wide', 'full'),
            'anchor' => true,
            'mode' => 'preview',
        ),
        'enqueue_assets' => function() {
            wp_enqueue_style('atk-ved-testimonials-block', get_template_directory_uri() . '/css/blocks/testimonials.css', array(), '2.3');
            wp_enqueue_script('atk-ved-testimonials-block', get_template_directory_uri() . '/js/blocks/testimonials.js', array('jquery'), '2.3', true);
        },
    ));
    
    /* ==========================================================================
       CTA BLOCK - Призыв к действию
       ========================================================================== */
    
    acf_register_block_type(array(
        'name' => 'cta',
        'title' => __('CTA (Призыв к действию)', 'atk-ved'),
        'description' => __('Блок с призывом и кнопкой', 'atk-ved'),
        'render_template' => 'template-parts/blocks/cta/cta.php',
        'category' => 'atk-ved',
        'icon' => 'button',
        'keywords' => array('cta', 'кнопка', 'призыв'),
        'supports' => array(
            'align' => array('wide', 'full'),
            'anchor' => true,
            'mode' => 'preview',
        ),
        'enqueue_assets' => function() {
            wp_enqueue_style('atk-ved-cta-block', get_template_directory_uri() . '/css/blocks/cta.css', array(), '2.3');
        },
    ));
    
    /* ==========================================================================
       TEAM BLOCK - Команда
       ========================================================================== */
    
    acf_register_block_type(array(
        'name' => 'team',
        'title' => __('Team (Команда)', 'atk-ved'),
        'description' => __('Карточки членов команды', 'atk-ved'),
        'render_template' => 'template-parts/blocks/team/team.php',
        'category' => 'atk-ved',
        'icon' => 'groups',
        'keywords' => array('team', 'команда', 'сотрудники'),
        'supports' => array(
            'align' => array('wide', 'full'),
            'anchor' => true,
            'mode' => 'preview',
        ),
        'enqueue_assets' => function() {
            wp_enqueue_style('atk-ved-team-block', get_template_directory_uri() . '/css/blocks/team.css', array(), '2.3');
        },
    ));
    
    /* ==========================================================================
       PARTNERS BLOCK - Партнёры
       ========================================================================== */
    
    acf_register_block_type(array(
        'name' => 'partners',
        'title' => __('Partners (Партнёры)', 'atk-ved'),
        'description' => __('Логотипы партнёров', 'atk-ved'),
        'render_template' => 'template-parts/blocks/partners/partners.php',
        'category' => 'atk-ved',
        'icon' => 'handshake',
        'keywords' => array('partners', 'партнёры', 'логотипы'),
        'supports' => array(
            'align' => array('wide', 'full'),
            'anchor' => true,
            'mode' => 'preview',
        ),
        'enqueue_assets' => function() {
            wp_enqueue_style('atk-ved-partners-block', get_template_directory_uri() . '/css/blocks/partners.css', array(), '2.3');
        },
    ));
    
    /* ==========================================================================
       FAQ BLOCK - Вопросы и ответы
       ========================================================================== */
    
    acf_register_block_type(array(
        'name' => 'faq',
        'title' => __('FAQ (Вопросы и ответы)', 'atk-ved'),
        'description' => __('Аккордеон с часто задаваемыми вопросами', 'atk-ved'),
        'render_template' => 'template-parts/blocks/faq/faq.php',
        'category' => 'atk-ved',
        'icon' => 'editor-help',
        'keywords' => array('faq', 'вопросы', 'ответы'),
        'supports' => array(
            'align' => array('wide', 'full'),
            'anchor' => true,
            'mode' => 'preview',
        ),
        'enqueue_assets' => function() {
            wp_enqueue_style('atk-ved-faq-block', get_template_directory_uri() . '/css/blocks/faq.css', array(), '2.3');
        },
    ));
}

// Хук для регистрации блоков
add_action('acf/init', 'atk_ved_register_acf_blocks');

/**
 * Регистрация категории блоков
 */
function atk_ved_block_category(array $categories): array {
    return array_merge(
        array(
            array(
                'slug' => 'atk-ved',
                'title' => __('АТК ВЭД', 'atk-ved'),
                'icon' => null,
            ),
        ),
        $categories
    );
}
add_filter('block_categories_all', 'atk_ved_block_category');

/**
 * Добавление стилей редактора для блоков
 */
function atk_ved_block_editor_styles(): void {
    wp_enqueue_style(
        'atk-ved-block-editor',
        get_template_directory_uri() . '/css/blocks/editor.css',
        array(),
        '2.3'
    );
}
add_action('enqueue_block_editor_assets', 'atk_ved_block_editor_styles');
