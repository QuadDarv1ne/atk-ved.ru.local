<?php
/**
 * AmoCRM Integration
 * Автоматическое создание сделок из форм
 * 
 * @package ATK_VED
 * @since 2.6.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Класс для работы с AmoCRM
 */
class ATK_VED_AmoCRM {
    
    private string $api_url;
    private string $api_key;
    
    public function __construct() {
        $this->api_url = get_option('atk_ved_amocrm_domain', '');
        $this->api_key = get_option('atk_ved_amocrm_api_key', '');
    }
    
    /**
     * Создание лида в AmoCRM
     */
    public function create_lead(array $data): array {
        if (empty($this->api_url) || empty($this->api_key)) {
            return array('success' => false, 'message' => 'AmoCRM не настроена');
        }
        
        $lead_data = array(
            'name' => $data['name'] ?? 'Новый лид',
            'price' => $data['price'] ?? 0,
            'pipeline_id' => intval(get_option('atk_ved_amocrm_pipeline_id', 1)),
            'status_id' => intval(get_option('atk_ved_amocrm_status_id', 1)),
            'custom_fields_values' => array(),
            '_embedded' => array(
                'contacts' => array(),
            ),
        );
        
        // Добавляем кастомные поля
        if (!empty($data['phone'])) {
            $lead_data['custom_fields_values'][] = array(
                'field_id' => intval(get_option('atk_ved_amocrm_phone_field_id', 1)),
                'values' => array(array('value' => $data['phone'])),
            );
        }
        
        if (!empty($data['email'])) {
            $lead_data['custom_fields_values'][] = array(
                'field_id' => intval(get_option('atk_ved_amocrm_email_field_id', 2)),
                'values' => array(array('value' => $data['email'])),
            );
        }
        
        if (!empty($data['message'])) {
            $lead_data['custom_fields_values'][] = array(
                'field_id' => intval(get_option('atk_ved_amocrm_message_field_id', 3)),
                'values' => array(array('value' => $data['message'])),
            );
        }
        
        // Создаём контакт
        if (!empty($data['name']) || !empty($data['phone']) || !empty($data['email'])) {
            $contact = array(
                'first_name' => $data['name'] ?? '',
                'custom_fields_values' => array(),
            );
            
            if (!empty($data['phone'])) {
                $contact['custom_fields_values'][] = array(
                    'field_id' => intval(get_option('atk_ved_amocrm_contact_phone_field_id', 1)),
                    'values' => array(array('value' => $data['phone'], 'enum_id' => 703181)), // Мобильный
                );
            }
            
            if (!empty($data['email'])) {
                $contact['custom_fields_values'][] = array(
                    'field_id' => intval(get_option('atk_ved_amocrm_contact_email_field_id', 2)),
                    'values' => array(array('value' => $data['email'], 'enum_id' => 703183)), // Email
                );
            }
            
            $lead_data['_embedded']['contacts'][] = $contact;
        }
        
        // Отправка запроса
        $response = wp_remote_post($this->api_url . '/api/v4/leads', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type' => 'application/json',
            ),
            'body' => wp_json_encode($lead_data),
            'timeout' => 15,
        ));
        
        if (is_wp_error($response)) {
            return array('success' => false, 'message' => $response->get_error_message());
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (wp_remote_retrieve_response_code($response) === 200) {
            // Логирование успешного создания
            if (function_exists('atk_ved_log_user_action')) {
                atk_ved_log_user_action('AmoCRM Lead Created', array(
                    'lead_id' => $data['id'] ?? null,
                    'name' => $data['name'] ?? null,
                ));
            }
            
            return array('success' => true, 'lead_id' => $data['id'] ?? null);
        }
        
        return array('success' => false, 'message' => $data['title'] ?? 'Ошибка создания лида');
    }
    
    /**
     * Создание контакта
     */
    public function create_contact(array $data): array {
        if (empty($this->api_url) || empty($this->api_key)) {
            return array('success' => false, 'message' => 'AmoCRM не настроена');
        }
        
        $contact_data = array(
            'first_name' => $data['first_name'] ?? '',
            'last_name' => $data['last_name'] ?? '',
            'custom_fields_values' => array(),
        );
        
        if (!empty($data['phone'])) {
            $contact_data['custom_fields_values'][] = array(
                'field_id' => intval(get_option('atk_ved_amocrm_contact_phone_field_id', 1)),
                'values' => array(array('value' => $data['phone'], 'enum_id' => 703181)),
            );
        }
        
        if (!empty($data['email'])) {
            $contact_data['custom_fields_values'][] = array(
                'field_id' => intval(get_option('atk_ved_amocrm_contact_email_field_id', 2)),
                'values' => array(array('value' => $data['email'], 'enum_id' => 703183)),
            );
        }
        
        $response = wp_remote_post($this->api_url . '/api/v4/contacts', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type' => 'application/json',
            ),
            'body' => wp_json_encode($contact_data),
            'timeout' => 15,
        ));
        
        if (is_wp_error($response)) {
            return array('success' => false, 'message' => $response->get_error_message());
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (wp_remote_retrieve_response_code($response) === 200) {
            return array('success' => true, 'contact_id' => $data['id'] ?? null);
        }
        
        return array('success' => false, 'message' => $data['title'] ?? 'Ошибка создания контакта');
    }
    
    /**
     * Добавление заметки к сделке
     */
    public function add_note(int $entity_id, string $text, string $entity_type = 'leads'): array {
        if (empty($this->api_url) || empty($this->api_key)) {
            return array('success' => false, 'message' => 'AmoCRM не настроена');
        }
        
        $note_data = array(
            array(
                'entity_id' => $entity_id,
                'entity_type' => $entity_type,
                'note_type' => 'common',
                'params' => array(
                    'text' => $text,
                ),
            ),
        );
        
        $response = wp_remote_post($this->api_url . '/api/v4/notes', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type' => 'application/json',
            ),
            'body' => wp_json_encode($note_data),
            'timeout' => 15,
        ));
        
        if (is_wp_error($response)) {
            return array('success' => false, 'message' => $response->get_error_message());
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (wp_remote_retrieve_response_code($response) === 200) {
            return array('success' => true, 'note_id' => $data[0]['id'] ?? null);
        }
        
        return array('success' => false, 'message' => $data['title'] ?? 'Ошибка добавления заметки');
    }
}

/**
 * Интеграция с формами
 */

// Contact Form 7
add_action('wpcf7_before_send_mail', function($contact_form) {
    $submission = WPCF7_Submission::get_instance();
    $data = $submission->get_posted_data();
    
    $amo_data = array(
        'name' => $data['your-name'][0] ?? $data['name'][0] ?? '',
        'phone' => $data['your-phone'][0] ?? $data['phone'][0] ?? '',
        'email' => $data['your-email'][0] ?? $data['email'][0] ?? '',
        'message' => $data['your-message'][0] ?? $data['message'][0] ?? '',
    );
    
    $amo = new ATK_VED_AmoCRM();
    $amo->create_lead($amo_data);
});

// Contact Form 7 ID 1 (основная форма)
add_action('wpcf7_mail_sent', function($contact_form) {
    if ($contact_form->id() == 1) {
        $submission = WPCF7_Submission::get_instance();
        $data = $submission->get_posted_data();
        
        // Отправка события в аналитику
        ?>
        <script>
        if (typeof ym !== 'undefined') {
            ym(<?php echo esc_js(get_theme_mod('atk_ved_metrika_id', 0)); ?>, 'reachGoal', 'amocrm_lead_created');
        }
        </script>
        <?php
    }
});

// AJAX формы темы
add_action('wp_ajax_atk_ved_contact_form', 'atk_ved_amocrm_contact_form', 5);
add_action('wp_ajax_nopriv_atk_ved_contact_form', 'atk_ved_amocrm_contact_form', 5);

function atk_ved_amocrm_contact_form(): void {
    // Продолжаем основную обработку формы
    // AmoCRM создаётся в основном обработчике
}

// Callback форма
add_action('wp_ajax_atk_ved_callback', 'atk_ved_amocrm_callback', 5);
add_action('wp_ajax_nopriv_atk_ved_callback', 'atk_ved_amocrm_callback', 5);

function atk_ved_amocrm_callback(): void {
    check_ajax_referer('atk_ved_nonce', 'nonce');
    
    $name = sanitize_text_field($_POST['name'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $preferred_time = sanitize_text_field($_POST['preferred_time'] ?? '');
    
    $amo = new ATK_VED_AmoCRM();
    
    $result = $amo->create_lead(array(
        'name' => $name,
        'phone' => $phone,
        'message' => 'Запрос обратного звонка. Удобное время: ' . $preferred_time,
        'source' => 'callback_widget',
    ));
    
    if ($result['success']) {
        // Добавляем заметку
        if (!empty($result['lead_id'])) {
            $amo->add_note($result['lead_id'], 'Клиент запросил обратный звонок. Время: ' . $preferred_time);
        }
    }
}

/**
 * Настройки AmoCRM в админке
 */
function atk_ved_amocrm_settings_page(): void {
    ?>
    <div class="wrap">
        <h1><?php _e('Настройки AmoCRM', 'atk-ved'); ?></h1>
        
        <form method="post" action="options.php">
            <?php settings_fields('atk_ved_amocrm_settings'); ?>
            <?php do_settings_sections('atk_ved_amocrm_settings'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Домен AmoCRM', 'atk-ved'); ?></th>
                    <td>
                        <input type="text" name="atk_ved_amocrm_domain" value="<?php echo esc_attr(get_option('atk_ved_amocrm_domain')); ?>" class="regular-text" placeholder="https://your-subdomain.amocrm.ru">
                        <p class="description"><?php _e('Ваш домен в AmoCRM', 'atk-ved'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('API Key', 'atk-ved'); ?></th>
                    <td>
                        <input type="password" name="atk_ved_amocrm_api_key" value="<?php echo esc_attr(get_option('atk_ved_amocrm_api_key')); ?>" class="regular-text">
                        <p class="description"><?php _e('API ключ из настроек AmoCRM', 'atk-ved'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Pipeline ID', 'atk-ved'); ?></th>
                    <td>
                        <input type="number" name="atk_ved_amocrm_pipeline_id" value="<?php echo esc_attr(get_option('atk_ved_amocrm_pipeline_id', 1)); ?>" class="small-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Status ID', 'atk-ved'); ?></th>
                    <td>
                        <input type="number" name="atk_ved_amocrm_status_id" value="<?php echo esc_attr(get_option('atk_ved_amocrm_status_id', 1)); ?>" class="small-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Phone Field ID', 'atk-ved'); ?></th>
                    <td>
                        <input type="number" name="atk_ved_amocrm_phone_field_id" value="<?php echo esc_attr(get_option('atk_ved_amocrm_phone_field_id', 1)); ?>" class="small-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Email Field ID', 'atk-ved'); ?></th>
                    <td>
                        <input type="number" name="atk_ved_amocrm_email_field_id" value="<?php echo esc_attr(get_option('atk_ved_amocrm_email_field_id', 2)); ?>" class="small-text">
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        
        <hr>
        
        <h2><?php _e('Тестирование', 'atk-ved'); ?></h2>
        <button type="button" class="button" id="testAmoCRM">
            <?php _e('Проверить соединение', 'atk-ved'); ?>
        </button>
        <span id="testAmoCRMResult"></span>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('#testAmoCRM').on('click', function() {
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'atk_ved_test_amocrm',
                    nonce: '<?php echo wp_create_nonce('atk_ved_nonce'); ?>'
                },
                success: function(response) {
                    $('#testAmoCRMResult').text(response.success ? '✓ ' + response.data.message : '✗ ' + response.data.message);
                }
            });
        });
    });
    </script>
    <?php
}

/**
 * Тест соединения с AmoCRM
 */
function atk_ved_test_amocrm(): void {
    check_ajax_referer('atk_ved_nonce', 'nonce');
    
    $amo = new ATK_VED_AmoCRM();
    
    if (empty($amo->api_url) || empty($amo->api_key)) {
        wp_send_json_error(array('message' => 'AmoCRM не настроена'));
    }
    
    $response = wp_remote_get($amo->api_url . '/api/v4/account', array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $amo->api_key,
        ),
    ));
    
    if (is_wp_error($response)) {
        wp_send_json_error(array('message' => $response->get_error_message()));
    }
    
    if (wp_remote_retrieve_response_code($response) === 200) {
        wp_send_json_success(array('message' => 'Соединение успешно'));
    }
    
    wp_send_json_error(array('message' => 'Ошибка соединения'));
}
add_action('wp_ajax_atk_ved_test_amocrm', 'atk_ved_test_amocrm');

/**
 * Регистрация страницы настроек
 */
function atk_ved_amocrm_admin_menu(): void {
    add_options_page(
        __('Настройки AmoCRM', 'atk-ved'),
        __('AmoCRM', 'atk-ved'),
        'manage_options',
        'atk-ved-amocrm',
        'atk_ved_amocrm_settings_page'
    );
}
add_action('admin_menu', 'atk_ved_amocrm_admin_menu');

/**
 * Регистрация настроек
 */
function atk_ved_amocrm_settings_init(): void {
    register_setting('atk_ved_amocrm_settings', 'atk_ved_amocrm_domain');
    register_setting('atk_ved_amocrm_settings', 'atk_ved_amocrm_api_key');
    register_setting('atk_ved_amocrm_settings', 'atk_ved_amocrm_pipeline_id');
    register_setting('atk_ved_amocrm_settings', 'atk_ved_amocrm_status_id');
    register_setting('atk_ved_amocrm_settings', 'atk_ved_amocrm_phone_field_id');
    register_setting('atk_ved_amocrm_settings', 'atk_ved_amocrm_email_field_id');
    register_setting('atk_ved_amocrm_settings', 'atk_ved_amocrm_message_field_id');
    register_setting('atk_ved_amocrm_settings', 'atk_ved_amocrm_contact_phone_field_id');
    register_setting('atk_ved_amocrm_settings', 'atk_ved_amocrm_contact_email_field_id');
}
add_action('admin_init', 'atk_ved_amocrm_settings_init');
