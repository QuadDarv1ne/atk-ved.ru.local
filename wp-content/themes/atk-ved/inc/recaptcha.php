<?php
/**
 * reCAPTCHA v3 защита форм
 * 
 * @package ATK_VED
 * @since 1.9.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Добавление reCAPTCHA v3 в head
 */
function atk_ved_recaptcha_v3_head(): void {
    $site_key = get_theme_mod('atk_ved_recaptcha_site_key', '');
    
    if (empty($site_key)) {
        return;
    }
    ?>
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo esc_attr($site_key); ?>"></script>
    <?php
}
add_action('wp_head', 'atk_ved_recaptcha_v3_head');

/**
 * Добавление скрытого поля reCAPTCHA в формы
 */
function atk_ved_recaptcha_v3_field(): void {
    $site_key = get_theme_mod('atk_ved_recaptcha_site_key', '');
    
    if (empty($site_key)) {
        return;
    }
    ?>
    <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
    <script>
    (function() {
        var forms = document.querySelectorAll('form');
        forms.forEach(function(form) {
            // Пропускаем формы поиска и админки
            if (form.classList.contains('search-form') || form.closest('#wpadminbar')) {
                return;
            }
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                var siteKey = '<?php echo esc_js($site_key); ?>';
                
                grecaptcha.ready(function() {
                    grecaptcha.execute(siteKey, {action: 'submit'}).then(function(token) {
                        document.getElementById('recaptchaResponse').value = token;
                        form.submit();
                    });
                });
            });
        });
    })();
    </script>
    <?php
}
add_action('wp_footer', 'atk_ved_recaptcha_v3_field');

/**
 * Проверка reCAPTCHA ответа
 * 
 * @param string $response Токен от reCAPTCHA
 * @return array Результат проверки ['valid' => bool, 'score' => float]
 */
function atk_ved_verify_recaptcha(string $response): array {
    $secret_key = get_theme_mod('atk_ved_recaptcha_secret_key', '');
    
    if (empty($secret_key) || empty($response)) {
        return ['valid' => false, 'score' => 0];
    }
    
    $verify_response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
        'body' => [
            'secret' => $secret_key,
            'response' => $response
        ]
    ]);
    
    if (is_wp_error($verify_response)) {
        return ['valid' => false, 'score' => 0];
    }
    
    $body = json_decode(wp_remote_retrieve_body($verify_response), true);
    
    if (!isset($body['success']) || !$body['success']) {
        return ['valid' => false, 'score' => $body['score'] ?? 0];
    }
    
    // Минимальный порог scores - 0.5 (настраивается)
    $threshold = floatval(get_theme_mod('atk_ved_recaptcha_threshold', 0.5));
    $score = $body['score'] ?? 0;
    
    return [
        'valid' => $score >= $threshold,
        'score' => $score
    ];
}

/**
 * Проверка reCAPTCHA перед отправкой AJAX форм
 */
function atk_ved_check_recaptcha_ajax(): void {
    $response = $_POST['recaptcha_response'] ?? '';
    $result = atk_ved_verify_recaptcha($response);
    
    if (!$result['valid']) {
        wp_send_json_error([
            'message' => 'Проверка на бота не пройдена. Попробуйте снова.',
            'score' => $result['score']
        ]);
    }
}
add_action('wp_ajax_nopriv_atk_ved_contact_form', 'atk_ved_check_recaptcha_ajax', 1);
add_action('wp_ajax_atk_ved_contact_form', 'atk_ved_check_recaptcha_ajax', 1);
add_action('wp_ajax_nopriv_atk_ved_quick_search', 'atk_ved_check_recaptcha_ajax', 1);
add_action('wp_ajax_atk_ved_quick_search', 'atk_ved_check_recaptcha_ajax', 1);

/**
 * Логирование подозрительных активностей reCAPTCHA
 */
function atk_ved_log_recaptcha_failures(): void {
    if (isset($_POST['recaptcha_response'])) {
        $result = atk_ved_verify_recaptcha($_POST['recaptcha_response']);
        
        if (!$result['valid']) {
            atk_ved_log_security('reCAPTCHA Failure', [
                'score' => $result['score'],
                'action' => $_POST['action'] ?? 'unknown',
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
        }
    }
}
add_action('init', 'atk_ved_log_recaptcha_failures');

/**
 * Настройки reCAPTCHA в Customizer
 */
function atk_ved_recaptcha_customizer($wp_customize): void {
    $wp_customize->add_section('atk_ved_recaptcha', array(
        'title'    => __('reCAPTCHA v3', 'atk-ved'),
        'priority' => 38,
        'description' => __('Получите ключи на <a href="https://www.google.com/recaptcha/admin" target="_blank">Google reCAPTCHA</a>', 'atk-ved')
    ));

    // Site Key
    $wp_customize->add_setting('atk_ved_recaptcha_site_key', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('atk_ved_recaptcha_site_key', array(
        'label'       => __('Site Key', 'atk-ved'),
        'section'     => 'atk_ved_recaptcha',
        'type'        => 'text',
        'description' => __('Ключ сайта из Google reCAPTCHA', 'atk-ved')
    ));

    // Secret Key
    $wp_customize->add_setting('atk_ved_recaptcha_secret_key', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('atk_ved_recaptcha_secret_key', array(
        'label'       => __('Secret Key', 'atk-ved'),
        'section'     => 'atk_ved_recaptcha',
        'type'        => 'password',
        'description' => __('Секретный ключ из Google reCAPTCHA', 'atk-ved')
    ));

    // Threshold
    $wp_customize->add_setting('atk_ved_recaptcha_threshold', array(
        'default'           => 0.5,
        'sanitize_callback' => 'floatval',
    ));

    $wp_customize->add_control('atk_ved_recaptcha_threshold', array(
        'label'       => __('Порог scores (0.1-1.0)', 'atk-ved'),
        'section'     => 'atk_ved_recaptcha',
        'type'        => 'number',
        'input_attrs' => [
            'min' => 0.1,
            'max' => 1.0,
            'step' => 0.1
        ],
        'description' => __('Рекомендуемое значение: 0.5. Меньше = строже', 'atk-ved')
    ));

    // Enable/Disable
    $wp_customize->add_setting('atk_ved_recaptcha_enabled', array(
        'default'           => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));

    $wp_customize->add_control('atk_ved_recaptcha_enabled', array(
        'label'   => __('Включить reCAPTCHA v3', 'atk-ved'),
        'section' => 'atk_ved_recaptcha',
        'type'    => 'checkbox',
    ));
}
add_action('customize_register', 'atk_ved_recaptcha_customizer');

/**
 * Шорткод для reCAPTCHA badge
 */
function atk_ved_recaptcha_badge_shortcode(): string {
    if (!get_theme_mod('atk_ved_recaptcha_enabled', false)) {
        return '';
    }
    
    $site_key = get_theme_mod('atk_ved_recaptcha_site_key', '');
    
    if (empty($site_key)) {
        return '';
    }
    
    ob_start();
    ?>
    <div class="recaptcha-badge" style="position: fixed; bottom: 10px; right: 10px; z-index: 9998;">
        <img src="https://www.gstatic.com/recaptcha/api2/logo_48.png" alt="reCAPTCHA" style="width: 48px; height: 48px; opacity: 0.5;">
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('recaptcha_badge', 'atk_ved_recaptcha_badge_shortcode');
