<?php
/**
 * Регистрация кастомных типов записей
 */

// Регистрация типа записи "Услуги"
function atk_ved_register_services_post_type() {
    $labels = array(
        'name'               => 'Услуги',
        'singular_name'      => 'Услуга',
        'menu_name'          => 'Услуги',
        'add_new'            => 'Добавить услугу',
        'add_new_item'       => 'Добавить новую услугу',
        'edit_item'          => 'Редактировать услугу',
        'new_item'           => 'Новая услуга',
        'view_item'          => 'Посмотреть услугу',
        'search_items'       => 'Найти услугу',
        'not_found'          => 'Услуги не найдены',
        'not_found_in_trash' => 'В корзине услуг не найдено',
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => false,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'query_var'           => true,
        'rewrite'             => array('slug' => 'services'),
        'capability_type'     => 'post',
        'has_archive'         => false,
        'hierarchical'        => false,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-portfolio',
        'supports'            => array('title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest'        => true,
    );

    register_post_type('service', $args);
}
add_action('init', 'atk_ved_register_services_post_type');

// Регистрация типа записи "Отзывы"
function atk_ved_register_reviews_post_type() {
    $labels = array(
        'name'               => 'Отзывы',
        'singular_name'      => 'Отзыв',
        'menu_name'          => 'Отзывы',
        'add_new'            => 'Добавить отзыв',
        'add_new_item'       => 'Добавить новый отзыв',
        'edit_item'          => 'Редактировать отзыв',
        'new_item'           => 'Новый отзыв',
        'view_item'          => 'Посмотреть отзыв',
        'search_items'       => 'Найти отзыв',
        'not_found'          => 'Отзывы не найдены',
        'not_found_in_trash' => 'В корзине отзывов не найдено',
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => false,
        'publicly_queryable'  => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'query_var'           => true,
        'rewrite'             => array('slug' => 'reviews'),
        'capability_type'     => 'post',
        'has_archive'         => false,
        'hierarchical'        => false,
        'menu_position'       => 6,
        'menu_icon'           => 'dashicons-star-filled',
        'supports'            => array('title', 'editor', 'thumbnail'),
        'show_in_rest'        => true,
    );

    register_post_type('review', $args);
}
add_action('init', 'atk_ved_register_reviews_post_type');

// Регистрация типа записи "FAQ"
function atk_ved_register_faq_post_type() {
    $labels = array(
        'name'               => 'FAQ',
        'singular_name'      => 'Вопрос',
        'menu_name'          => 'FAQ',
        'add_new'            => 'Добавить вопрос',
        'add_new_item'       => 'Добавить новый вопрос',
        'edit_item'          => 'Редактировать вопрос',
        'new_item'           => 'Новый вопрос',
        'view_item'          => 'Посмотреть вопрос',
        'search_items'       => 'Найти вопрос',
        'not_found'          => 'Вопросы не найдены',
        'not_found_in_trash' => 'В корзине вопросов не найдено',
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => false,
        'publicly_queryable'  => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'query_var'           => true,
        'rewrite'             => array('slug' => 'faq'),
        'capability_type'     => 'post',
        'has_archive'         => false,
        'hierarchical'        => false,
        'menu_position'       => 7,
        'menu_icon'           => 'dashicons-editor-help',
        'supports'            => array('title', 'editor', 'page-attributes'),
        'show_in_rest'        => true,
    );

    register_post_type('faq', $args);
}
add_action('init', 'atk_ved_register_faq_post_type');

// Добавление мета-полей для отзывов
function atk_ved_add_review_meta_boxes() {
    add_meta_box(
        'review_details',
        'Детали отзыва',
        'atk_ved_review_meta_box_callback',
        'review',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'atk_ved_add_review_meta_boxes');

function atk_ved_review_meta_box_callback($post) {
    wp_nonce_field('atk_ved_save_review_meta', 'atk_ved_review_meta_nonce');
    
    $author_name = get_post_meta($post->ID, '_review_author_name', true);
    $author_position = get_post_meta($post->ID, '_review_author_position', true);
    $rating = get_post_meta($post->ID, '_review_rating', true);
    
    ?>
    <p>
        <label for="review_author_name">Имя автора:</label><br>
        <input type="text" id="review_author_name" name="review_author_name" value="<?php echo esc_attr($author_name); ?>" style="width: 100%;">
    </p>
    <p>
        <label for="review_author_position">Должность/Компания:</label><br>
        <input type="text" id="review_author_position" name="review_author_position" value="<?php echo esc_attr($author_position); ?>" style="width: 100%;">
    </p>
    <p>
        <label for="review_rating">Рейтинг (1-5):</label><br>
        <select id="review_rating" name="review_rating">
            <option value="5" <?php selected($rating, '5'); ?>>5 звезд</option>
            <option value="4" <?php selected($rating, '4'); ?>>4 звезды</option>
            <option value="3" <?php selected($rating, '3'); ?>>3 звезды</option>
            <option value="2" <?php selected($rating, '2'); ?>>2 звезды</option>
            <option value="1" <?php selected($rating, '1'); ?>>1 звезда</option>
        </select>
    </p>
    <?php
}

function atk_ved_save_review_meta($post_id) {
    if (!isset($_POST['atk_ved_review_meta_nonce']) || !wp_verify_nonce($_POST['atk_ved_review_meta_nonce'], 'atk_ved_save_review_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['review_author_name'])) {
        update_post_meta($post_id, '_review_author_name', sanitize_text_field($_POST['review_author_name']));
    }

    if (isset($_POST['review_author_position'])) {
        update_post_meta($post_id, '_review_author_position', sanitize_text_field($_POST['review_author_position']));
    }

    if (isset($_POST['review_rating'])) {
        update_post_meta($post_id, '_review_rating', sanitize_text_field($_POST['review_rating']));
    }
}
add_action('save_post', 'atk_ved_save_review_meta');
