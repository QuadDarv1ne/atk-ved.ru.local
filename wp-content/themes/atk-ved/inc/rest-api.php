<?php
/**
 * REST API Endpoints for ATK VED Theme
 * 
 * @package ATK_VED
 * @since 2.5.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Регистрация REST API роутов
 */
function atk_ved_register_rest_routes(): void {
    
    /* ==========================================================================
       CALCULATOR ENDPOINT
       ========================================================================== */
    
    register_rest_route('atk-ved/v1', '/calculator/delivery', array(
        'methods' => 'POST',
        'callback' => 'atk_ved_rest_calculate_delivery',
        'permission_callback' => '__return_true',
        'args' => array(
            'weight' => array(
                'required' => true,
                'type' => 'number',
                'minimum' => 0,
            ),
            'volume' => array(
                'required' => false,
                'type' => 'number',
                'minimum' => 0,
            ),
            'from_city' => array(
                'required' => false,
                'type' => 'string',
            ),
            'to_city' => array(
                'required' => false,
                'type' => 'string',
            ),
            'method' => array(
                'required' => false,
                'type' => 'string',
                'enum' => array('all', 'air', 'sea', 'rail', 'auto'),
            ),
            'insurance' => array(
                'required' => false,
                'type' => 'boolean',
            ),
            'customs' => array(
                'required' => false,
                'type' => 'boolean',
            ),
        ),
    ));
    
    /* ==========================================================================
       TRACKING ENDPOINT
       ========================================================================== */
    
    register_rest_route('atk-ved/v1', '/tracking/(?P<tracking_number>[a-zA-Z0-9]+)', array(
        'methods' => 'GET',
        'callback' => 'atk_ved_rest_get_tracking',
        'permission_callback' => '__return_true',
        'args' => array(
            'tracking_number' => array(
                'required' => true,
                'type' => 'string',
                'pattern' => '[a-zA-Z0-9]+',
            ),
        ),
    ));
    
    /* ==========================================================================
       CONTACT FORM ENDPOINT
       ========================================================================== */
    
    register_rest_route('atk-ved/v1', '/contact', array(
        'methods' => 'POST',
        'callback' => 'atk_ved_rest_contact_form',
        'permission_callback' => '__return_true',
        'args' => array(
            'name' => array(
                'required' => true,
                'type' => 'string',
                'minLength' => 2,
            ),
            'phone' => array(
                'required' => true,
                'type' => 'string',
            ),
            'email' => array(
                'required' => false,
                'type' => 'string',
                'format' => 'email',
            ),
            'message' => array(
                'required' => false,
                'type' => 'string',
            ),
            'recaptcha' => array(
                'required' => false,
                'type' => 'string',
            ),
        ),
    ));
    
    /* ==========================================================================
       CALLBACK REQUEST ENDPOINT
       ========================================================================== */
    
    register_rest_route('atk-ved/v1', '/callback', array(
        'methods' => 'POST',
        'callback' => 'atk_ved_rest_callback_request',
        'permission_callback' => '__return_true',
        'args' => array(
            'name' => array(
                'required' => true,
                'type' => 'string',
            ),
            'phone' => array(
                'required' => true,
                'type' => 'string',
            ),
            'preferred_time' => array(
                'required' => false,
                'type' => 'string',
            ),
        ),
    ));
    
    /* ==========================================================================
       SERVICES ENDPOINT
       ========================================================================== */
    
    register_rest_route('atk-ved/v1', '/services', array(
        'methods' => 'GET',
        'callback' => 'atk_ved_rest_get_services',
        'permission_callback' => '__return_true',
    ));
    
    /* ==========================================================================
       FAQ ENDPOINT
       ========================================================================== */
    
    register_rest_route('atk-ved/v1', '/faq', array(
        'methods' => 'GET',
        'callback' => 'atk_ved_rest_get_faq',
        'permission_callback' => '__return_true',
        'args' => array(
            'category' => array(
                'required' => false,
                'type' => 'string',
            ),
        ),
    ));
    
    /* ==========================================================================
       TEAM ENDPOINT
       ========================================================================== */
    
    register_rest_route('atk-ved/v1', '/team', array(
        'methods' => 'GET',
        'callback' => 'atk_ved_rest_get_team',
        'permission_callback' => '__return_true',
    ));
    
    /* ==========================================================================
       PARTNERS ENDPOINT
       ========================================================================== */
    
    register_rest_route('atk-ved/v1', '/partners', array(
        'methods' => 'GET',
        'callback' => 'atk_ved_rest_get_partners',
        'permission_callback' => '__return_true',
        'args' => array(
            'featured' => array(
                'required' => false,
                'type' => 'boolean',
            ),
        ),
    ));
    
    /* ==========================================================================
       ANALYTICS ENDPOINT (Admin only)
       ========================================================================== */
    
    register_rest_route('atk-ved/v1', '/analytics', array(
        'methods' => 'GET',
        'callback' => 'atk_ved_rest_get_analytics',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        },
    ));
}

add_action('rest_api_init', 'atk_ved_register_rest_routes');

/**
 * REST API: Расчёт доставки
 */
function atk_ved_rest_calculate_delivery(WP_REST_Request $request): WP_REST_Response {
    $data = array(
        'weight' => $request->get_param('weight'),
        'volume' => $request->get_param('volume') ?? 0,
        'from_city' => $request->get_param('from_city') ?? 'Пекин',
        'to_city' => $request->get_param('to_city') ?? 'Москва',
        'method' => $request->get_param('method') ?? 'all',
        'insurance' => $request->get_param('insurance') ?? false,
        'customs' => $request->get_param('customs') ?? false,
    );
    
    if (function_exists('atk_ved_calculate_delivery_cost')) {
        $result = atk_ved_calculate_delivery_cost($data);
        return rest_ensure_response(array(
            'success' => true,
            'data' => $result,
        ));
    }
    
    return rest_ensure_response(array(
        'success' => false,
        'message' => 'Calculator function not available',
    ));
}

/**
 * REST API: Получение информации об отслеживании
 */
function atk_ved_rest_get_tracking(WP_REST_Request $request): WP_REST_Response {
    $tracking_number = $request->get_param('tracking_number');
    
    if (function_exists('atk_ved_get_tracking_info')) {
        $result = atk_ved_get_tracking_info($tracking_number);
        
        if ($result) {
            return rest_ensure_response(array(
                'success' => true,
                'data' => $result,
            ));
        }
    }
    
    return rest_ensure_response(array(
        'success' => false,
        'message' => 'Tracking number not found',
    ));
}

/**
 * REST API: Контактная форма
 */
function atk_ved_rest_contact_form(WP_REST_Request $request): WP_REST_Response {
    $name = sanitize_text_field($request->get_param('name'));
    $phone = sanitize_text_field($request->get_param('phone'));
    $email = sanitize_email($request->get_param('email'));
    $message = sanitize_textarea_field($request->get_param('message'));
    
    // Валидация
    if (empty($name) || empty($phone)) {
        return rest_ensure_response(array(
            'success' => false,
            'message' => 'Заполните обязательные поля',
        ));
    }
    
    // Отправка email
    $to = get_option('admin_email');
    $subject = sprintf(__('Новая заявка с сайта от %s', 'atk-ved'), $name);
    $body = sprintf(
        "Имя: %s\nТелефон: %s\nEmail: %s\n\nСообщение:\n%s",
        $name,
        $phone,
        $email,
        $message
    );
    
    $headers = array('Content-Type: text/html; charset=UTF-8');
    
    if (wp_mail($to, $subject, nl2br($body), $headers)) {
        // Логирование
        if (function_exists('atk_ved_log_user_action')) {
            atk_ved_log_user_action('Contact Form Submission', array(
                'name' => $name,
                'phone' => $phone,
            ));
        }
        
        return rest_ensure_response(array(
            'success' => true,
            'message' => 'Сообщение отправлено',
        ));
    }
    
    return rest_ensure_response(array(
        'success' => false,
        'message' => 'Ошибка отправки',
    ));
}

/**
 * REST API: Запрос обратного звонка
 */
function atk_ved_rest_callback_request(WP_REST_Request $request): WP_REST_Response {
    $name = sanitize_text_field($request->get_param('name'));
    $phone = sanitize_text_field($request->get_param('phone'));
    $preferred_time = sanitize_text_field($request->get_param('preferred_time'));
    
    // Валидация
    if (empty($name) || empty($phone)) {
        return rest_ensure_response(array(
            'success' => false,
            'message' => 'Заполните обязательные поля',
        ));
    }
    
    // Отправка email
    $to = get_option('admin_email');
    $subject = sprintf(__('Запрос обратного звонка от %s', 'atk-ved'), $name);
    $body = sprintf(
        "Имя: %s\nТелефон: %s\nУдобное время: %s",
        $name,
        $phone,
        $preferred_time
    );
    
    if (wp_mail($to, $subject, nl2br($body))) {
        return rest_ensure_response(array(
            'success' => true,
            'message' => 'Запрос отправлен',
        ));
    }
    
    return rest_ensure_response(array(
        'success' => false,
        'message' => 'Ошибка отправки',
    ));
}

/**
 * REST API: Получить услуги
 */
function atk_ved_rest_get_services(): WP_REST_Response {
    $services = get_posts(array(
        'post_type' => 'service',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ));
    
    $result = array();
    foreach ($services as $service) {
        $result[] = array(
            'id' => $service->ID,
            'title' => get_the_title($service),
            'excerpt' => get_the_excerpt($service),
            'content' => get_the_content(null, false, $service),
            'thumbnail' => get_the_post_thumbnail_url($service, 'medium'),
            'meta' => array(
                'icon' => get_field('service_icon', $service->ID),
                'number' => get_field('service_number', $service->ID),
                'short_desc' => get_field('service_short_desc', $service->ID),
            ),
        );
    }
    
    return rest_ensure_response(array(
        'success' => true,
        'data' => $result,
    ));
}

/**
 * REST API: Получить FAQ
 */
function atk_ved_rest_get_faq(WP_REST_Request $request): WP_REST_Response {
    $category = $request->get_param('category');
    
    $args = array(
        'post_type' => 'faq',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'faq_is_active',
                'value' => '1',
            ),
        ),
    );
    
    if ($category) {
        $args['meta_query'][] = array(
            'key' => 'faq_category',
            'value' => $category,
        );
    }
    
    $faqs = get_posts($args);
    
    $result = array();
    foreach ($faqs as $faq) {
        $result[] = array(
            'id' => $faq->ID,
            'question' => get_the_title($faq),
            'answer' => get_the_content(null, false, $faq),
            'category' => get_field('faq_category', $faq->ID),
            'icon' => get_field('faq_icon', $faq->ID),
        );
    }
    
    return rest_ensure_response(array(
        'success' => true,
        'data' => $result,
    ));
}

/**
 * REST API: Получить команду
 */
function atk_ved_rest_get_team(): WP_REST_Response {
    $team = get_posts(array(
        'post_type' => 'team',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ));
    
    $result = array();
    foreach ($team as $member) {
        $result[] = array(
            'id' => $member->ID,
            'name' => get_the_title($member),
            'position' => get_field('team_position', $member->ID),
            'photo' => get_field('team_photo', $member->ID),
            'social' => get_field('team_social', $member->ID),
        );
    }
    
    return rest_ensure_response(array(
        'success' => true,
        'data' => $result,
    ));
}

/**
 * REST API: Получить партнёров
 */
function atk_ved_rest_get_partners(WP_REST_Request $request): WP_REST_Response {
    $featured = $request->get_param('featured');
    
    $args = array(
        'post_type' => 'partner',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    );
    
    if ($featured) {
        $args['meta_query'] = array(
            array(
                'key' => 'partner_is_featured',
                'value' => '1',
            ),
        );
    }
    
    $partners = get_posts($args);
    
    $result = array();
    foreach ($partners as $partner) {
        $result[] = array(
            'id' => $partner->ID,
            'name' => get_the_title($partner),
            'logo' => get_field('partner_logo', $partner->ID),
            'url' => get_field('partner_url', $partner->ID),
            'featured' => get_field('partner_is_featured', $partner->ID),
        );
    }
    
    return rest_ensure_response(array(
        'success' => true,
        'data' => $result,
    ));
}

/**
 * REST API: Получить аналитику (Admin only)
 */
function atk_ved_rest_get_analytics(): WP_REST_Response {
    $data = array(
        'total_posts' => wp_count_posts('post')->publish,
        'total_pages' => wp_count_posts('page')->publish,
        'total_services' => wp_count_posts('service')->publish,
        'total_faqs' => wp_count_posts('faq')->publish,
        'total_team' => wp_count_posts('team')->publish,
        'total_partners' => wp_count_posts('partner')->publish,
        'site_url' => home_url(),
        'wp_version' => get_bloginfo('version'),
        'theme_version' => wp_get_theme()->get('Version'),
        'php_version' => phpversion(),
    );
    
    return rest_ensure_response(array(
        'success' => true,
        'data' => $data,
    ));
}
