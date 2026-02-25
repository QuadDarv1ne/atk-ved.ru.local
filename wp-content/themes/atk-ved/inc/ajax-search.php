<?php
/**
 * AJAX Search with Autocomplete
 * Умный поиск с автодополнением и подсказками
 * 
 * @package ATK_VED
 * @since 3.2.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Регистрация AJAX endpoints для поиска
 */
function atk_ved_register_ajax_search(): void {
    add_action('wp_ajax_atk_ved_search', 'atk_ved_ajax_search');
    add_action('wp_ajax_nopriv_atk_ved_search', 'atk_ved_ajax_search');
    
    add_action('wp_ajax_atk_ved_search_suggestions', 'atk_ved_ajax_search_suggestions');
    add_action('wp_ajax_nopriv_atk_ved_search_suggestions', 'atk_ved_ajax_search_suggestions');
}
add_action('wp_ajax_init', 'atk_ved_register_ajax_search');

/**
 * AJAX поиск
 */
function atk_ved_ajax_search(): void {
    check_ajax_referer('atk_ved_search_nonce', 'nonce');
    
    $query = sanitize_text_field($_POST['query'] ?? '');
    $post_type = sanitize_text_field($_POST['post_type'] ?? 'any');
    $category = sanitize_text_field($_POST['category'] ?? '');
    $min_price = floatval($_POST['min_price'] ?? 0);
    $max_price = floatval($_POST['max_price'] ?? 999999);
    $page = intval($_POST['page'] ?? 1);
    $per_page = intval($_POST['per_page'] ?? 10);
    
    if (strlen($query) < 2) {
        wp_send_json_error(array('message' => __('Введите минимум 2 символа', 'atk-ved')));
    }
    
    $args = array(
        's' => $query,
        'post_type' => $post_type === 'any' ? array('post', 'product') : $post_type,
        'post_status' => 'publish',
        'posts_per_page' => $per_page,
        'paged' => $page,
        'orderby' => 'relevance',
        'order' => 'DESC',
    );
    
    // Фильтр по категории
    if ($category) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $category,
            ),
        );
    }
    
    // Фильтр по цене (для WooCommerce)
    if (class_exists('WooCommerce') && $post_type !== 'post') {
        $args['meta_query'] = array(
            array(
                'key' => '_price',
                'value' => array($min_price, $max_price),
                'type' => 'numeric',
                'compare' => 'BETWEEN',
            ),
        );
    }
    
    $search = new WP_Query($args);
    
    $results = array();
    $total_results = $search->found_posts;
    
    if ($search->have_posts()) {
        while ($search->have_posts()) {
            $search->the_post();
            
            $result = array(
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'permalink' => get_permalink(),
                'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
                'excerpt' => wp_trim_words(get_the_excerpt(), 20),
                'post_type' => get_post_type(),
            );
            
            // Дополнительные данные для товаров
            if (get_post_type() === 'product' && class_exists('WooCommerce')) {
                $product = wc_get_product(get_the_ID());
                
                if ($product) {
                    $result['price'] = $product->get_price_html();
                    $result['price_raw'] = $product->get_price();
                    $result['in_stock'] = $product->is_in_stock();
                    $result['rating'] = $product->get_average_rating();
                    $result['review_count'] = $product->get_review_count();
                }
            }
            
            $results[] = $result;
        }
        wp_reset_postdata();
    }
    
    wp_send_json_success(array(
        'results' => $results,
        'total' => $total_results,
        'pages' => ceil($total_results / $per_page),
        'current_page' => $page,
        'query' => $query,
    ));
}

/**
 * AJAX подсказки для поиска
 */
function atk_ved_ajax_search_suggestions(): void {
    check_ajax_referer('atk_ved_search_nonce', 'nonce');
    
    $query = sanitize_text_field($_POST['query'] ?? '');
    
    if (strlen($query) < 2) {
        wp_send_json_success(array('suggestions' => array()));
    }
    
    $suggestions = array();
    
    // Популярные запросы
    $popular_searches = get_option('atk_ved_popular_searches', array());
    foreach ($popular_searches as $search) {
        if (stripos($search, $query) !== false) {
            $suggestions[] = array(
                'type' => 'popular',
                'text' => $search,
                'icon' => '🔥',
            );
        }
    }
    
    // Категории
    $categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'search' => $query,
        'number' => 5,
    ));
    
    if (!is_wp_error($categories) && !empty($categories)) {
        foreach ($categories as $category) {
            $suggestions[] = array(
                'type' => 'category',
                'text' => $category->name,
                'url' => get_term_link($category),
                'icon' => '📁',
                'count' => $category->count,
            );
        }
    }
    
    // Товары
    if (class_exists('WooCommerce')) {
        $products = wc_get_products(array(
            'search' => $query,
            'limit' => 5,
            'status' => 'publish',
        ));
        
        foreach ($products as $product) {
            $suggestions[] = array(
                'type' => 'product',
                'text' => $product->get_name(),
                'url' => $product->get_permalink(),
                'icon' => '🛍️',
                'price' => $product->get_price_html(),
                'thumbnail' => $product->get_image('thumbnail'),
            );
        }
    }
    
    // Страницы и записи
    $pages = get_posts(array(
        's' => $query,
        'post_type' => array('page', 'post'),
        'posts_per_page' => 3,
        'post_status' => 'publish',
    ));
    
    foreach ($pages as $page) {
        $suggestions[] = array(
            'type' => 'page',
            'text' => $page->post_title,
            'url' => get_permalink($page->ID),
            'icon' => '📄',
        );
    }
    
    // Ограничиваем количество подсказок
    $suggestions = array_slice($suggestions, 0, 10);
    
    wp_send_json_success(array('suggestions' => $suggestions));
}

/**
 * Логирование популярных запросов
 */
function atk_ved_log_search_query(string $query): void {
    if (strlen($query) < 2) {
        return;
    }
    
    $popular_searches = get_option('atk_ved_popular_searches', array());
    
    // Нормализация запроса
    $query = strtolower(trim($query));
    
    // Добавляем или увеличиваем счётчик
    if (in_array($query, $popular_searches)) {
        // Уже есть в списке
    } else {
        array_unshift($popular_searches, $query);
        $popular_searches = array_slice($popular_searches, 0, 20); // Максимум 20 запросов
    }
    
    update_option('atk_ved_popular_searches', $popular_searches);
}

/**
 * Шорткод для поиска
 */
function atk_ved_ajax_search_shortcode(array $atts): string {
    $atts = shortcode_atts(array(
        'placeholder' => __('Поиск...', 'atk-ved'),
        'post_type' => 'any',
        'show_categories' => '1',
        'show_price_filter' => '1',
        'results_per_page' => '10',
    ), $atts);
    
    ob_start();
    ?>
    <div class="atk-ajax-search" data-post-type="<?php echo esc_attr($atts['post_type']); ?>">
        <div class="atk-search-input-wrapper">
            <input 
                type="text" 
                class="atk-search-input" 
                placeholder="<?php echo esc_attr($atts['placeholder']); ?>"
                autocomplete="off"
                aria-label="<?php esc_attr_e('Поиск', 'atk-ved'); ?>"
            >
            <span class="atk-search-icon">🔍</span>
            <button type="button" class="atk-search-clear" aria-label="<?php esc_attr_e('Очистить', 'atk-ved'); ?>">×</button>
        </div>
        
        <div class="atk-search-suggestions" style="display: none;"></div>
        
        <div class="atk-search-results" style="display: none;">
            <div class="atk-search-results-header">
                <span class="atk-search-count"></span>
                <div class="atk-search-filters">
                    <?php if ($atts['show_categories'] === '1' && class_exists('WooCommerce')): ?>
                    <select class="atk-search-category">
                        <option value=""><?php _e('Все категории', 'atk-ved'); ?></option>
                        <?php
                        $categories = get_terms(array(
                            'taxonomy' => 'product_cat',
                            'hide_empty' => true,
                        ));
                        if (!is_wp_error($categories)) {
                            foreach ($categories as $category) {
                                echo '<option value="' . esc_attr($category->slug) . '">' . esc_html($category->name) . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <?php endif; ?>
                    
                    <?php if ($atts['show_price_filter'] === '1' && class_exists('WooCommerce')): ?>
                    <div class="atk-search-price">
                        <input type="number" class="atk-search-price-min" placeholder="От" min="0">
                        <span>—</span>
                        <input type="number" class="atk-search-price-max" placeholder="До" min="0">
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="atk-search-results-grid"></div>
            
            <div class="atk-search-pagination"></div>
        </div>
        
        <div class="atk-search-no-results" style="display: none;">
            <p><?php _e('Ничего не найдено. Попробуйте другой запрос.', 'atk-ved'); ?></p>
        </div>
        
        <div class="atk-search-loading" style="display: none;">
            <div class="atk-search-spinner"></div>
            <p><?php _e('Поиск...', 'atk-ved'); ?></p>
        </div>
    </div>
    
    <style>
    .atk-ajax-search {
        position: relative;
        max-width: 800px;
        margin: 0 auto;
    }
    
    .atk-search-input-wrapper {
        position: relative;
    }
    
    .atk-search-input {
        width: 100%;
        padding: 16px 50px 16px 20px;
        font-size: 16px;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        transition: all 0.3s;
    }
    
    .atk-search-input:focus {
        outline: none;
        border-color: #e31e24;
        box-shadow: 0 0 0 4px rgba(227, 30, 36, 0.1);
    }
    
    .atk-search-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 20px;
        color: #999;
    }
    
    .atk-search-clear {
        position: absolute;
        right: 45px;
        top: 50%;
        transform: translateY(-50%);
        background: #f0f0f0;
        border: none;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 16px;
        color: #666;
        display: none;
    }
    
    .atk-search-clear:hover {
        background: #e0e0e0;
    }
    
    .atk-search-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        z-index: 1000;
        max-height: 400px;
        overflow-y: auto;
        margin-top: 8px;
    }
    
    .atk-suggestion-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: background 0.2s;
        text-decoration: none;
        color: inherit;
    }
    
    .atk-suggestion-item:hover {
        background: #f8f9fa;
    }
    
    .atk-suggestion-item:last-child {
        border-bottom: none;
    }
    
    .atk-suggestion-icon {
        font-size: 20px;
    }
    
    .atk-suggestion-text {
        flex: 1;
        font-size: 15px;
    }
    
    .atk-suggestion-meta {
        font-size: 13px;
        color: #999;
    }
    
    .atk-search-results {
        margin-top: 30px;
    }
    
    .atk-search-results-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e0e0e0;
    }
    
    .atk-search-count {
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }
    
    .atk-search-filters {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }
    
    .atk-search-category,
    .atk-search-price input {
        padding: 10px 15px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
    }
    
    .atk-search-results-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
    }
    
    .atk-search-result-item {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: all 0.3s;
        text-decoration: none;
        color: inherit;
        display: block;
    }
    
    .atk-search-result-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    
    .atk-search-result-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    
    .atk-search-result-content {
        padding: 15px;
    }
    
    .atk-search-result-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
    }
    
    .atk-search-result-price {
        font-size: 18px;
        font-weight: 700;
        color: #e31e24;
        margin-bottom: 8px;
    }
    
    .atk-search-result-excerpt {
        font-size: 14px;
        color: #666;
        line-height: 1.6;
    }
    
    .atk-search-pagination {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 30px;
    }
    
    .atk-pagination-btn {
        padding: 10px 16px;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .atk-pagination-btn:hover,
    .atk-pagination-btn.active {
        background: #e31e24;
        color: #fff;
        border-color: #e31e24;
    }
    
    .atk-pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .atk-search-loading,
    .atk-search-no-results {
        text-align: center;
        padding: 40px;
    }
    
    .atk-search-spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f0f0f0;
        border-top-color: #e31e24;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 15px;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    </style>
    
    <script>
    (function($) {
        'use strict';
        
        const searchNonce = '<?php echo wp_create_nonce('atk_ved_search_nonce'); ?>';
        const ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';
        
        $('.atk-ajax-search').each(function() {
            const $container = $(this);
            const $input = $container.find('.atk-search-input');
            const $clearBtn = $container.find('.atk-search-clear');
            const $suggestions = $container.find('.atk-search-suggestions');
            const $results = $container.find('.atk-search-results');
            const $resultsGrid = $container.find('.atk-search-results-grid');
            const $noResults = $container.find('.atk-search-no-results');
            const $loading = $container.find('.atk-search-loading');
            const $count = $container.find('.atk-search-count');
            const $pagination = $container.find('.atk-search-pagination');
            const $categoryFilter = $container.find('.atk-search-category');
            const $priceMin = $container.find('.atk-search-price-min');
            const $priceMax = $container.find('.atk-search-price-max');
            
            let searchTimeout;
            let currentPage = 1;
            let totalPages = 1;
            
            // Поиск при вводе
            $input.on('input', function() {
                const query = $(this).val().trim();
                
                clearTimeout(searchTimeout);
                
                if (query.length < 2) {
                    $suggestions.hide();
                    $results.hide();
                    $clearBtn.hide();
                    return;
                }
                
                $clearBtn.show();
                
                // Подсказки
                searchTimeout = setTimeout(function() {
                    getSuggestions(query);
                }, 300);
            });
            
            // Поиск по Enter
            $input.on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    performSearch();
                    $suggestions.hide();
                }
            });
            
            // Очистка
            $clearBtn.on('click', function() {
                $input.val('').trigger('input');
                $results.hide();
                $suggestions.hide();
            });
            
            // Клик вне поиска
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.atk-ajax-search').length) {
                    $suggestions.hide();
                }
            });
            
            // Фильтры
            $categoryFilter.on('change', performSearch);
            $priceMin.add($priceMax).on('change', performSearch);
            
            function getSuggestions(query) {
                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'atk_ved_search_suggestions',
                        nonce: searchNonce,
                        query: query
                    },
                    success: function(response) {
                        if (response.success && response.data.suggestions.length > 0) {
                            renderSuggestions(response.data.suggestions);
                            $suggestions.show();
                        } else {
                            $suggestions.hide();
                        }
                    }
                });
            }
            
            function renderSuggestions(suggestions) {
                let html = '';
                
                suggestions.forEach(function(item) {
                    html += '<a href="' + (item.url || '#') + '" class="atk-suggestion-item" data-type="' + item.type + '">';
                    html += '<span class="atk-suggestion-icon">' + item.icon + '</span>';
                    html += '<span class="atk-suggestion-text">' + item.text + '</span>';
                    
                    if (item.price) {
                        html += '<span class="atk-suggestion-meta">' + item.price + '</span>';
                    } else if (item.count) {
                        html += '<span class="atk-suggestion-meta">' + item.count + ' товаров</span>';
                    }
                    
                    html += '</a>';
                });
                
                $suggestions.html(html);
            }
            
            function performSearch() {
                const query = $input.val().trim();
                
                if (query.length < 2) return;
                
                $loading.show();
                $results.hide();
                $noResults.hide();
                
                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'atk_ved_search',
                        nonce: searchNonce,
                        query: query,
                        post_type: $container.data('post-type'),
                        category: $categoryFilter.val(),
                        min_price: $priceMin.val() || 0,
                        max_price: $priceMax.val() || 999999,
                        page: currentPage,
                        per_page: <?php echo intval($atts['results_per_page']); ?>
                    },
                    success: function(response) {
                        $loading.hide();
                        
                        if (response.success && response.data.results.length > 0) {
                            renderResults(response.data);
                            $results.show();
                        } else {
                            $noResults.show();
                        }
                        
                        // Логирование запроса
                        $.ajax({
                            url: ajaxUrl,
                            type: 'POST',
                            data: {
                                action: 'atk_ved_log_search',
                                query: query
                            }
                        });
                    },
                    error: function() {
                        $loading.hide();
                        $noResults.show();
                    }
                });
            }
            
            function renderResults(data) {
                let html = '';
                
                data.results.forEach(function(item) {
                    html += '<a href="' + item.permalink + '" class="atk-search-result-item">';
                    
                    if (item.thumbnail) {
                        html += '<img src="' + item.thumbnail + '" alt="' + item.title + '" class="atk-search-result-image">';
                    }
                    
                    html += '<div class="atk-search-result-content">';
                    html += '<div class="atk-search-result-title">' + item.title + '</div>';
                    
                    if (item.price) {
                        html += '<div class="atk-search-result-price">' + item.price + '</div>';
                    }
                    
                    if (item.excerpt) {
                        html += '<div class="atk-search-result-excerpt">' + item.excerpt + '</div>';
                    }
                    
                    html += '</div></a>';
                });
                
                $resultsGrid.html(html);
                $count.text('Найдено: ' + data.total);
                
                renderPagination(data.current_page, data.pages);
            }
            
            function renderPagination(current, total) {
                if (total <= 1) {
                    $pagination.html('');
                    return;
                }
                
                let html = '';
                
                html += '<button class="atk-pagination-btn" ' + (current === 1 ? 'disabled' : '') + ' data-page="' + (current - 1) + '">←</button>';
                
                for (let i = 1; i <= total; i++) {
                    html += '<button class="atk-pagination-btn ' + (i === current ? 'active' : '') + '" data-page="' + i + '">' + i + '</button>';
                }
                
                html += '<button class="atk-pagination-btn" ' + (current === total ? 'disabled' : '') + ' data-page="' + (current + 1) + '">→</button>';
                
                $pagination.html(html);
                
                currentPage = current;
                totalPages = total;
            }
            
            $pagination.on('click', '.atk-pagination-btn', function() {
                const page = $(this).data('page');
                if (page && page !== currentPage) {
                    currentPage = page;
                    performSearch();
                }
            });
        });
    })(jQuery);
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('atk_ajax_search', 'atk_ved_ajax_search_shortcode');

/**
 * Логирование поиска (AJAX handler)
 */
function atk_ved_ajax_log_search(): void {
    $query = sanitize_text_field($_POST['query'] ?? '');
    atk_ved_log_search_query($query);
    wp_send_json_success();
}
add_action('wp_ajax_atk_ved_log_search', 'atk_ved_ajax_log_search');
add_action('wp_ajax_nopriv_atk_ved_log_search', 'atk_ved_ajax_log_search');
