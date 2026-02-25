<?php
/**
 * Вспомогательные функции темы
 */

// Получение услуг
function atk_ved_get_services($limit = -1) {
    $args = array(
        'post_type'      => 'service',
        'posts_per_page' => $limit,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    );
    
    return new WP_Query($args);
}

// Получение отзывов
function atk_ved_get_reviews($limit = 4) {
    $args = array(
        'post_type'      => 'review',
        'posts_per_page' => $limit,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );
    
    return new WP_Query($args);
}

// Получение FAQ
function atk_ved_get_faq($limit = -1) {
    $args = array(
        'post_type'      => 'faq',
        'posts_per_page' => $limit,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    );
    
    return new WP_Query($args);
}

// Получение инициалов из имени
function atk_ved_get_initials($name) {
    $words = explode(' ', $name);
    $initials = '';
    
    foreach ($words as $word) {
        if (!empty($word)) {
            $initials .= mb_substr($word, 0, 1);
        }
    }
    
    return mb_strtoupper($initials);
}

// Вывод звезд рейтинга
function atk_ved_get_rating_stars($rating) {
    $output = '<div class="rating-stars">';
    
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $output .= '<span class="star filled">★</span>';
        } else {
            $output .= '<span class="star">☆</span>';
        }
    }
    
    $output .= '</div>';
    
    return $output;
}

// Обрезка текста
function atk_ved_trim_text($text, $length = 100, $more = '...') {
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    
    $text = mb_substr($text, 0, $length);
    $text = mb_substr($text, 0, mb_strrpos($text, ' '));
    
    return $text . $more;
}

// Получение времени чтения
function atk_ved_get_reading_time($content) {
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // 200 слов в минуту
    
    return $reading_time;
}

// Проверка, является ли страница лендингом
function atk_ved_is_landing_page() {
    return is_front_page() || is_page_template('template-landing.php');
}

// Получение цвета для аватара
function atk_ved_get_avatar_color($name) {
    $colors = array(
        '#e31e24', // красный
        '#2196F3', // синий
        '#4CAF50', // зеленый
        '#FF9800', // оранжевый
        '#9C27B0', // фиолетовый
        '#00BCD4', // голубой
    );
    
    $index = ord(mb_substr($name, 0, 1)) % count($colors);
    
    return $colors[$index];
}

// Форматирование номера телефона
function atk_ved_format_phone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    if (strlen($phone) == 11 && $phone[0] == '7') {
        return '+7 (' . substr($phone, 1, 3) . ') ' . substr($phone, 4, 3) . '-' . substr($phone, 7, 2) . '-' . substr($phone, 9, 2);
    }
    
    return $phone;
}

// Получение ссылки для телефона
function atk_ved_get_phone_link($phone) {
    return 'tel:' . preg_replace('/[^0-9+]/', '', $phone);
}

// Проверка, есть ли социальные сети
function atk_ved_has_social_links() {
    return get_theme_mod('atk_ved_whatsapp') || 
           get_theme_mod('atk_ved_telegram') || 
           get_theme_mod('atk_ved_vk');
}

// Получение SVG иконки
function atk_ved_get_svg_icon($name) {
    $icons = array(
        'phone' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>',
        'email' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>',
        'location' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>',
        'check' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>',
    );
    
    return isset($icons[$name]) ? $icons[$name] : '';
}

// Безопасный вывод HTML
function atk_ved_kses_post($content) {
    $allowed_tags = wp_kses_allowed_html('post');
    $allowed_tags['svg'] = array(
        'xmlns' => true,
        'width' => true,
        'height' => true,
        'viewbox' => true,
        'fill' => true,
        'stroke' => true,
        'stroke-width' => true,
        'stroke-linecap' => true,
        'stroke-linejoin' => true,
    );
    $allowed_tags['path'] = array(
        'd' => true,
        'fill' => true,
        'stroke' => true,
    );
    $allowed_tags['circle'] = array(
        'cx' => true,
        'cy' => true,
        'r' => true,
    );
    $allowed_tags['polyline'] = array(
        'points' => true,
    );
    
    return wp_kses($content, $allowed_tags);
}
