<?php
/**
 * AJAX обработчики
 *
 * @package ATK_VED
 * @since 3.3.0
 */

declare(strict_types=1);

/**
 * Обработчик отправки формы обратной связи
 */
function atk_ved_contact_form_handler(): void {
    // Проверка nonce
    if (!check_ajax_referer('atk_ved_nonce', 'nonce', false)) {
        wp_send_json_error([
            'message' => 'Ошибка безопасности. Обновите страницу и попробуйте снова.',
            'code' => 'invalid_nonce'
        ], 403);
        return;
    }

    // Проверка метода запроса
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        wp_send_json_error([
            'message' => 'Некорректный метод запроса.',
            'code' => 'invalid_method'
        ], 405);
        return;
    }

    // Получение и санитизация данных
    $name    = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    $email   = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $phone   = isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';
    $consent = isset($_POST['consent']) ? sanitize_text_field(wp_unslash($_POST['consent'])) : '';

    // Валидация обязательных полей
    $errors = [];

    if (empty($name)) {
        $errors['name'] = 'Имя обязательно';
    } elseif (strlen($name) < 2) {
        $errors['name'] = 'Имя должно быть не менее 2 символов';
    } elseif (strlen($name) > 100) {
        $errors['name'] = 'Имя не должно превышать 100 символов';
    }

    if (empty($email)) {
        $errors['email'] = 'Email обязателен';
    } elseif (!is_email($email)) {
        $errors['email'] = 'Некорректный email адрес';
    } elseif (strlen($email) > 255) {
        $errors['email'] = 'Email слишком длинный';
    }

    if (empty($message)) {
        $errors['message'] = 'Сообщение обязательно';
    } elseif (strlen($message) < 10) {
        $errors['message'] = 'Сообщение должно быть не менее 10 символов';
    } elseif (strlen($message) > 2000) {
        $errors['message'] = 'Сообщение не должно превышать 2000 символов';
    }

    if (!empty($phone)) {
        // Очистка телефона от лишних символов
        $clean_phone = preg_replace('/[^\d+]/', '', $phone);
        if (strlen($clean_phone) < 10) {
            $errors['phone'] = 'Некорректный номер телефона';
        }
    }

    if (empty($consent)) {
        $errors['consent'] = 'Необходимо согласие на обработку персональных данных';
    }

    // Проверка honeypot
    if (!empty($_POST['hp_website'])) {
        atk_ved_log_security_event('Honeypot: форма обратной связи', $_SERVER['REMOTE_ADDR']);
        // Возвращаем успех для бота (тихая блокировка)
        wp_send_json_success(['message' => 'Спасибо! Ваше сообщение отправлено.']);
        return;
    }

    // Проверка времени отправки формы (слишком быстро)
    if (!empty($_POST['hp_timestamp'])) {
        $time_diff = time() - intval($_POST['hp_timestamp']);
        if ($time_diff < 3) {
            atk_ved_log_security_event('Honeypot: быстрая отправка формы', $_SERVER['REMOTE_ADDR']);
            wp_send_json_error(['message' => 'Форма отправлена слишком быстро']);
            return;
        }
    }

    // Rate limiting - проверка количества запросов с одного IP
    $ip = $_SERVER['REMOTE_ADDR'];
    $rate_key = 'atk_contact_rate_' . md5($ip);
    $rate_limit = get_transient($rate_key);

    if ($rate_limit && $rate_limit >= 5) {
        wp_send_json_error([
            'message' => 'Слишком много запросов. Попробуйте через 5 минут.',
            'code' => 'rate_limit_exceeded'
        ], 429);
        return;
    }

    // Увеличиваем счётчик
    set_transient($rate_key, ($rate_limit ?: 0) + 1, 5 * MINUTE_IN_SECONDS);

    // Возвращаем ошибки валидации
    if (!empty($errors)) {
        wp_send_json_error([
            'message' => 'Пожалуйста, исправьте ошибки в форме.',
            'errors' => $errors
        ], 400);
        return;
    }

    // Отправка email
    $to = get_option('admin_email');
    $subject = sprintf(
        'Новое сообщение с сайта %s (от: %s)',
        get_bloginfo('name'),
        $name
    );

    $body = sprintf(
        "Новое сообщение с сайта %s\n\n",
        get_bloginfo('name')
    );
    $body .= sprintf("Имя: %s\n", $name);
    $body .= sprintf("Email: %s\n", $email);
    $body .= sprintf("Телефон: %s\n", $phone ?: 'Не указан');
    $body .= sprintf("\nСообщение:\n%s\n", $message);
    $body .= sprintf("\n---\nIP: %s\n", $_SERVER['REMOTE_ADDR']);
    $body .= sprintf("Дата: %s\n", date('d.m.Y H:i:s'));
    $body .= sprintf("User-Agent: %s\n", $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown');

    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        'Reply-To: ' . $name . ' <' . $email . '>',
    ];

    $sent = wp_mail($to, $subject, $body, $headers);

    if ($sent) {
        // Сохранение в базу данных
        $post_id = wp_insert_post([
            'post_type'   => 'contact_form',
            'post_title'  => $name . ' - ' . date('d.m.Y H:i'),
            'post_content' => $message,
            'post_status' => 'private',
            'meta_input'  => [
                '_contact_email'   => $email,
                '_contact_phone'   => $phone,
                '_contact_ip'      => $_SERVER['REMOTE_ADDR'],
                '_contact_consent' => $consent,
                '_contact_user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            ],
        ]);

        // Логирование успешной отправки
        error_log(sprintf(
            '[ATK VED] Contact form submitted successfully. ID: %d, Email: %s, IP: %s',
            $post_id,
            $email,
            $_SERVER['REMOTE_ADDR']
        ));

        wp_send_json_success(['message' => 'Спасибо! Ваше сообщение отправлено. Мы свяжемся с вами в течение 15 минут.']);
    } else {
        error_log('[ATK VED] Contact form submission failed. Email: ' . $email);
        wp_send_json_error(['message' => 'Ошибка отправки. Попробуйте позже или свяжитесь с нами по телефону.']);
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

/**
 * Обработчик формы быстрого поиска товаров
 */
function atk_ved_quick_search_handler(): void {
    // Проверка nonce
    if (!check_ajax_referer('atk_ved_nonce', 'nonce', false)) {
        wp_send_json_error([
            'message' => 'Ошибка безопасности. Обновите страницу и попробуйте снова.',
            'code' => 'invalid_nonce'
        ], 403);
        return;
    }

    // Получение и санитизация данных
    $name  = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    $phone = isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '';

    // Валидация
    $errors = [];

    if (empty($name)) {
        $errors['name'] = 'Имя обязательно';
    } elseif (strlen($name) < 2) {
        $errors['name'] = 'Имя должно быть не менее 2 символов';
    } elseif (strlen($name) > 100) {
        $errors['name'] = 'Имя не должно превышать 100 символов';
    }

    if (empty($phone)) {
        $errors['phone'] = 'Телефон обязателен';
    } else {
        $clean_phone = preg_replace('/[^\d+]/', '', $phone);
        if (strlen($clean_phone) < 10) {
            $errors['phone'] = 'Некорректный номер телефона';
        }
    }

    // Проверка honeypot
    if (!empty($_POST['hp_website'])) {
        atk_ved_log_security_event('Honeypot: быстрый поиск', $_SERVER['REMOTE_ADDR']);
        wp_send_json_success(['message' => 'Спасибо! Ваша заявка принята.']);
        return;
    }

    // Rate limiting
    $ip = $_SERVER['REMOTE_ADDR'];
    $rate_key = 'atk_search_rate_' . md5($ip);
    $rate_limit = get_transient($rate_key);

    if ($rate_limit && $rate_limit >= 10) {
        wp_send_json_error([
            'message' => 'Слишком много запросов. Попробуйте через 5 минут.',
            'code' => 'rate_limit_exceeded'
        ], 429);
        return;
    }

    set_transient($rate_key, ($rate_limit ?: 0) + 1, 5 * MINUTE_IN_SECONDS);

    if (!empty($errors)) {
        wp_send_json_error([
            'message' => 'Пожалуйста, заполните все поля.',
            'errors' => $errors
        ], 400);
        return;
    }

    // Отправка email администратору
    $to = get_option('admin_email');
    $subject = sprintf(
        'Новая заявка на поиск товара - %s',
        get_bloginfo('name')
    );

    $body = "Новая заявка на поиск товара в Китае\n\n";
    $body .= sprintf("Имя: %s\n", $name);
    $body .= sprintf("Телефон: %s\n", $phone);
    $body .= sprintf("Дата: %s\n", date('d.m.Y H:i'));
    $body .= sprintf("IP: %s\n", $_SERVER['REMOTE_ADDR']);

    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
    ];

    $sent = wp_mail($to, $subject, $body, $headers);

    if ($sent) {
        // Сохранение заявки в базу данных
        $post_id = wp_insert_post([
            'post_type'    => 'contact_form',
            'post_title'   => 'Заявка на поиск товара - ' . $name . ' - ' . date('d.m.Y H:i'),
            'post_content' => sprintf("Имя: %s\nТелефон: %s", $name, $phone),
            'post_status'  => 'private',
            'meta_input'   => [
                '_contact_name' => $name,
                '_contact_phone' => $phone,
                '_form_type'     => 'quick_search',
                '_contact_ip'    => $_SERVER['REMOTE_ADDR'],
            ],
        ]);

        error_log(sprintf(
            '[ATK VED] Quick search submitted. ID: %d, Name: %s, Phone: %s',
            $post_id,
            $name,
            $phone
        ));

        wp_send_json_success(['message' => 'Спасибо! Ваша заявка принята. Мы свяжемся с вами в ближайшее время.']);
    } else {
        error_log('[ATK VED] Quick search submission failed. Name: ' . $name);
        wp_send_json_error(['message' => 'Ошибка отправки. Попробуйте позже.']);
    }
}
add_action('wp_ajax_atk_ved_quick_search', 'atk_ved_quick_search_handler');
add_action('wp_ajax_nopriv_atk_ved_quick_search', 'atk_ved_quick_search_handler');
