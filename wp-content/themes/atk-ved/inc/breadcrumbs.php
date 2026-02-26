<?php
/**
 * Breadcrumbs навигация
 * 
 * @package ATK_VED
 * @since 1.9.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Генерация breadcrumbs
 * 
 * @param array $args Аргументы
 * @return void
 */
function atk_ved_breadcrumbs(array $args = []): void {
    $defaults = [
        'container'       => 'nav',
        'container_class' => 'breadcrumbs',
        'container_id'    => '',
        'class'           => 'breadcrumbs-list',
        'home_text'       => __('Главная', 'atk-ved'),
        'show_home'       => true,
        'show_current'    => true,
        'separator'       => '/',
        'before'          => '<span class="breadcrumbs-before">',
        'after'           => '</span>',
    ];
    
    $args = wp_parse_args($args, $defaults);
    
    $breadcrumbs = atk_ved_get_breadcrumbs_array($args);
    
    if (empty($breadcrumbs)) {
        return;
    }
    
    echo '<' . esc_html($args['container']);
    echo ' class="' . esc_attr($args['container_class']) . '"';
    echo ' aria-label="' . esc_attr__('Хлебные крошки', 'atk-ved') . '"';
    echo ' role="navigation"';
    if (!empty($args['container_id'])) {
        echo ' id="' . esc_attr($args['container_id']) . '"';
    }
    echo '>' . "\n";
    
    echo '<ol class="' . esc_attr($args['class']) . '" itemscope itemtype="https://schema.org/BreadcrumbList" role="list">' . "\n";
    
    foreach ($breadcrumbs as $key => $crumb) {
        $is_last = ($key === count($breadcrumbs) - 1);
        $position = $key + 1;
        
        echo '<li class="' . esc_attr($is_last ? 'breadcrumbs-item--current' : 'breadcrumbs-item') . '"' . 
             ' itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
        
        if (!$is_last && !empty($crumb['url'])) {
            echo '<a href="' . esc_url($crumb['url']) . '" itemprop="item">' . "\n";
            echo '<span itemprop="name">' . esc_html($crumb['text']) . '</span>' . "\n";
            echo '</a>' . "\n";
        } else {
            echo '<span class="breadcrumbs-current" aria-current="page">' . "\n";
            echo '<span itemprop="name">' . esc_html($crumb['text']) . '</span>' . "\n";
            echo '</span>' . "\n";
        }
        
        echo '<meta itemprop="position" content="' . esc_attr($position) . '" />' . "\n";
        
        if (!$is_last) {
            echo '<span class="breadcrumbs-separator" aria-hidden="true">' . esc_html($args['separator']) . '</span>' . "\n";
        }
        
        echo '</li>' . "\n";
    }
    
    echo '</ol>' . "\n";
    echo '</' . esc_html($args['container']) . '>' . "\n";
}

/**
 * Получение массива breadcrumbs
 * 
 * @param array $args Аргументы
 * @return array
 */
function atk_ved_get_breadcrumbs_array(array $args = []): array {
    $breadcrumbs = [];
    
    // Не показываем на главной
    if (is_front_page()) {
        return $breadcrumbs;
    }
    
    // Добавляем главную
    if ($args['show_home']) {
        $breadcrumbs[] = [
            'text' => $args['home_text'],
            'url'  => home_url('/')
        ];
    }
    
    // Страницы
    if (is_page()) {
        global $post;
        
        // Получаем родителей
        if ($post->post_parent) {
            $parent_ids = get_post_ancestors($post->ID);
            $parent_ids = array_reverse($parent_ids);
            
            foreach ($parent_ids as $parent_id) {
                $parent = get_post($parent_id);
                if ($parent) {
                    $breadcrumbs[] = [
                        'text' => get_the_title($parent),
                        'url'  => get_permalink($parent)
                    ];
                }
            }
        }
        
        // Текущая страница
        if ($args['show_current']) {
            $breadcrumbs[] = [
                'text' => get_the_title(),
                'url'  => ''
            ];
        }
    }
    
    // Записи блога
    if (is_single() && get_post_type() === 'post') {
        // Страница блога
        $blog_page_id = get_option('page_for_posts');
        if ($blog_page_id) {
            $breadcrumbs[] = [
                'text' => get_the_title($blog_page_id),
                'url'  => get_permalink($blog_page_id)
            ];
        }
        
        // Категория
        $categories = get_the_category();
        if (!empty($categories)) {
            $category = $categories[0];
            $breadcrumbs[] = [
                'text' => $category->name,
                'url'  => get_category_link($category->term_id)
            ];
        }
        
        // Текущая запись
        if ($args['show_current']) {
            $breadcrumbs[] = [
                'text' => get_the_title(),
                'url'  => ''
            ];
        }
    }
    
    // Категории
    if (is_category()) {
        $category = get_queried_object();
        $breadcrumbs[] = [
            'text' => single_cat_title('', false),
            'url'  => ''
        ];
    }
    
    // Метки
    if (is_tag()) {
        $breadcrumbs[] = [
            'text' => single_tag_title('', false),
            'url'  => ''
        ];
    }
    
    // Архив автора
    if (is_author()) {
        $breadcrumbs[] = [
            'text' => get_the_author_meta('display_name', get_query_var('author')),
            'url'  => ''
        ];
    }
    
    // Архив по дате
    if (is_year()) {
        $breadcrumbs[] = [
            'text' => get_the_time('Y'),
            'url'  => ''
        ];
    } elseif (is_month()) {
        $breadcrumbs[] = [
            'text' => get_the_time('F Y'),
            'url'  => ''
        ];
    } elseif (is_day()) {
        $breadcrumbs[] = [
            'text' => get_the_time('j F, Y'),
            'url'  => ''
        ];
    }
    
    // Поиск
    if (is_search()) {
        $breadcrumbs[] = [
            'text' => sprintf(__('Поиск: %s', 'atk-ved'), get_search_query()),
            'url'  => ''
        ];
    }
    
    // 404
    if (is_404()) {
        $breadcrumbs[] = [
            'text' => __('Страница не найдена', 'atk-ved'),
            'url'  => ''
        ];
    }
    
    // Custom post types
    if (is_singular() && !is_page() && get_post_type() !== 'post') {
        $post_type = get_post_type_object(get_post_type());
        
        // Архив CPT
        if ($post_type->has_archive) {
            $breadcrumbs[] = [
                'text' => $post_type->labels->name,
                'url'  => get_post_type_archive_link($post_type->name)
            ];
        }
        
        // Текущая запись
        if ($args['show_current']) {
            $breadcrumbs[] = [
                'text' => get_the_title(),
                'url'  => ''
            ];
        }
    }
    
    return apply_filters('atk_ved_breadcrumbs_array', $breadcrumbs, $args);
}

/**
 * Шорткод для breadcrumbs
 */
function atk_ved_breadcrumbs_shortcode(array $atts): string {
    $atts = shortcode_atts([
        'home_text' => __('Главная', 'atk-ved'),
        'show_home' => '1',
        'separator' => '/',
    ], $atts);
    
    ob_start();
    atk_ved_breadcrumbs([
        'home_text' => $atts['home_text'],
        'show_home' => (bool) $atts['show_home'],
        'separator' => $atts['separator'],
    ]);
    return ob_get_clean();
}
add_shortcode('breadcrumbs', 'atk_ved_breadcrumbs_shortcode');

/**
 * Стили для breadcrumbs
 */
function atk_ved_breadcrumbs_styles(): void {
    ?>
    <style>
        .breadcrumbs {
            padding: 15px 0;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }
        
        .breadcrumbs-list {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            list-style: none;
            margin: 0;
            padding: 0;
            font-size: 14px;
        }
        
        .breadcrumbs-item,
        .breadcrumbs-current {
            display: inline-flex;
            align-items: center;
        }
        
        .breadcrumbs-item a {
            color: #666;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .breadcrumbs-item a:hover {
            color: #e31e24;
        }
        
        .breadcrumbs-current {
            color: #2c2c2c;
            font-weight: 500;
        }
        
        .breadcrumbs-separator {
            color: #999;
            margin: 0 5px;
        }
        
        @media (max-width: 768px) {
            .breadcrumbs {
                padding: 10px 0;
            }
            
            .breadcrumbs-list {
                font-size: 13px;
            }
        }
    </style>
    <?php
}
add_action('wp_head', 'atk_ved_breadcrumbs_styles');

/**
 * Настройки breadcrumbs в Customizer
 */
function atk_ved_breadcrumbs_customizer($wp_customize): void {
    $wp_customize->add_section('atk_ved_breadcrumbs', array(
        'title'    => __('Breadcrumbs', 'atk-ved'),
        'priority' => 40,
    ));

    // Показывать на главной
    $wp_customize->add_setting('atk_ved_breadcrumbs_show_home', array(
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));

    $wp_customize->add_control('atk_ved_breadcrumbs_show_home', array(
        'label'   => __('Показывать на всех страницах', 'atk-ved'),
        'section' => 'atk_ved_breadcrumbs',
        'type'    => 'checkbox',
    ));

    // Текст главной
    $wp_customize->add_setting('atk_ved_breadcrumbs_home_text', array(
        'default'           => __('Главная', 'atk-ved'),
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('atk_ved_breadcrumbs_home_text', array(
        'label'   => __('Текст главной', 'atk-ved'),
        'section' => 'atk_ved_breadcrumbs',
        'type'    => 'text',
    ));

    // Разделитель
    $wp_customize->add_setting('atk_ved_breadcrumbs_separator', array(
        'default'           => '/',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('atk_ved_breadcrumbs_separator', array(
        'label'   => __('Разделитель', 'atk-ved'),
        'section' => 'atk_ved_breadcrumbs',
        'type'    => 'text',
    ));
}
add_action('customize_register', 'atk_ved_breadcrumbs_customizer');
