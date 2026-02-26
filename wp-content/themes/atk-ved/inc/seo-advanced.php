<?php
/**
 * Advanced SEO features
 * 
 * @package ATK_VED
 * @since 1.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add more specific Schema.org markup for products/services
 */
function atk_ved_add_product_schema(): void {
    if (is_front_page()) {
        $products = array(
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'itemListElement' => array(
                array(
                    '@type' => 'Product',
                    'name' => 'Поиск товаров из Китая',
                    'description' => 'Находим нужные товары на китайских площадках по вашим требованиям и бюджету',
                    'offers' => array(
                        '@type' => 'Offer',
                        'price' => 'Договорная',
                        'priceCurrency' => 'RUB',
                        'availability' => 'https://schema.org/InStock'
                    )
                ),
                array(
                    '@type' => 'Product',
                    'name' => 'Доставка грузов',
                    'description' => 'Организуем доставку авиа, морем, ЖД или авто — на выбор',
                    'offers' => array(
                        '@type' => 'Offer',
                        'price' => 'Договорная',
                        'priceCurrency' => 'RUB',
                        'availability' => 'https://schema.org/InStock'
                    )
                )
            )
        );
        
        echo '<script type="application/ld+json">' . json_encode($products, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
}
add_action('wp_head', 'atk_ved_add_product_schema');

/**
 * Add breadcrumbs as Schema markup
 */
function atk_ved_add_breadcrumb_schema(): void {
    if (is_single() || is_page() || is_category() || is_tag()) {
        $breadcrumbs = array();
        $position = 1;
        
        // Home
        $breadcrumbs[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => 'Главная',
            'item' => home_url('/')
        );
        
        // Page-specific breadcrumbs
        if (is_category()) {
            $category = get_queried_object();
            $breadcrumbs[] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $category->name,
                'item' => get_category_link($category->term_id)
            );
        } elseif (is_tag()) {
            $tag = get_queried_object();
            $breadcrumbs[] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $tag->name,
                'item' => get_tag_link($tag->term_id)
            );
        } elseif (is_single() || is_page()) {
            $post_type = get_post_type();
            if ($post_type !== 'post' && $post_type !== 'page') {
                $post_type_obj = get_post_type_object($post_type);
                if ($post_type_obj) {
                    $breadcrumbs[] = array(
                        '@type' => 'ListItem',
                        'position' => $position++,
                        'name' => $post_type_obj->labels->singular_name,
                        'item' => get_post_type_archive_link($post_type)
                    );
                }
            }
            
            $breadcrumbs[] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => get_the_title(),
                'item' => get_permalink()
            );
        }
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbs
        );
        
        echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
}
add_action('wp_head', 'atk_ved_add_breadcrumb_schema');