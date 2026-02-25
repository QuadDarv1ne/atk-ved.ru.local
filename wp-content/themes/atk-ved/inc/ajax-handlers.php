<?php
/**
 * AJAX обработчики
 */

// Обработчик отправки формы обратной связи
function atk_ved_contact_form_handler() {
    check_ajax_referer('atk_ved_nonce', 'nonce');
    
    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $phone = sanitize_text_field($_POST['phone']);
    $message = sanitize_textarea_field($_POST['message']);
    
    // Валидация
    if (empty($name) || empty($email) || empty($message)) {
        wp_send_json_error(array('message' => 'Пожалуйста, заполните все обязательные поля'));
    }
    
    if (!is_email($email)) {
        wp_send_json_error(array('message' => 'Некорректный email адрес'));
    }
    
    // Отправка email
    $to = get_option('admin_email');
    $subject = 'Новое сообщение с сайта ' . get_bloginfo('name');
    $body = "Имя: $name\n";
    $body .= "Email: $email\n";
    $body .= "Телефон: $phone\n\n";
    $body .= "Сообщение:\n$message";
    
    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        'Reply-To: ' . $name . ' <' . $email . '>',
    );
    
    $sent = wp_mail($to, $subject, $body, $headers);
    
    if ($sent) {
        // Сохранение в базу данных (опционально)
        $post_id = wp_insert_post(array(
            'post_type' => 'contact_form',
            'post_title' => $name . ' - ' . date('d.m.Y H:i'),
            'post_content' => $message,
            'post_status' => 'private',
            'meta_input' => array(
                '_contact_email' => $email,
                '_contact_phone' => $phone,
            ),
        ));
        
        wp_send_json_success(array('message' => 'Спасибо! Ваше сообщение отправлено.'));
    } else {
        wp_send_json_error(array('message' => 'Ошибка отправки. Попробуйте позже.'));
    }
}
add_action('wp_ajax_atk_ved_contact_form', 'atk_ved_contact_form_handler');
add_action('wp_ajax_nopriv_atk_ved_contact_form', 'atk_ved_contact_form_handler');

// Обработчик загрузки дополнительных отзывов
function atk_ved_load_more_reviews() {
    check_ajax_referer('atk_ved_nonce', 'nonce');
    
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $per_page = 4;
    
    $args = array(
        'post_type' => 'review',
        'posts_per_page' => $per_page,
        'paged' => $page,
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        ob_start();
        
        while ($query->have_posts()) {
            $query->the_post();
            $author_name = get_post_meta(get_the_ID(), '_review_author_name', true);
            $rating = get_post_meta(get_the_ID(), '_review_rating', true);
            $initials = atk_ved_get_initials($author_name);
            ?>
            <div class="review-card">
                <div class="review-avatar"><?php echo esc_html($initials); ?></div>
                <h4><?php echo esc_html($author_name); ?></h4>
                <?php if ($rating) : ?>
                    <?php echo atk_ved_get_rating_stars($rating); ?>
                <?php endif; ?>
                <p><?php the_content(); ?></p>
            </div>
            <?php
        }
        
        $html = ob_get_clean();
        
        wp_send_json_success(array(
            'html' => $html,
            'has_more' => $query->max_num_pages > $page,
        ));
    } else {
        wp_send_json_error(array('message' => 'Больше отзывов нет'));
    }
    
    wp_reset_postdata();
}
add_action('wp_ajax_atk_ved_load_more_reviews', 'atk_ved_load_more_reviews');
add_action('wp_ajax_nopriv_atk_ved_load_more_reviews', 'atk_ved_load_more_reviews');

// Обработчик поиска
function atk_ved_ajax_search() {
    check_ajax_referer('atk_ved_nonce', 'nonce');
    
    $search_query = sanitize_text_field($_POST['query']);
    
    if (strlen($search_query) < 3) {
        wp_send_json_error(array('message' => 'Введите минимум 3 символа'));
    }
    
    $args = array(
        's' => $search_query,
        'post_type' => array('post', 'page', 'service', 'faq'),
        'posts_per_page' => 5,
    );
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        $results = array();
        
        while ($query->have_posts()) {
            $query->the_post();
            $results[] = array(
                'title' => get_the_title(),
                'url' => get_permalink(),
                'excerpt' => wp_trim_words(get_the_excerpt(), 15),
                'type' => get_post_type(),
            );
        }
        
        wp_send_json_success(array('results' => $results));
    } else {
        wp_send_json_error(array('message' => 'Ничего не найдено'));
    }
    
    wp_reset_postdata();
}
add_action('wp_ajax_atk_ved_search', 'atk_ved_ajax_search');
add_action('wp_ajax_nopriv_atk_ved_search', 'atk_ved_ajax_search');

// Регистрация кастомного типа записи для форм обратной связи
function atk_ved_register_contact_form_post_type() {
    register_post_type('contact_form', array(
        'labels' => array(
            'name' => 'Обращения',
            'singular_name' => 'Обращение',
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'capability_type' => 'post',
        'capabilities' => array(
            'create_posts' => false,
        ),
        'map_meta_cap' => true,
        'supports' => array('title', 'editor'),
        'menu_icon' => 'dashicons-email',
    ));
}
add_action('init', 'atk_ved_register_contact_form_post_type');
