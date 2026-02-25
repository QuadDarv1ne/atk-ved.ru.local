<?php
/**
 * Callback Request Widget
 * Виджет запроса обратного звонка
 * 
 * @package ATK_VED
 * @since 2.5.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Регистрация виджета обратного звонка
 */
function atk_ved_register_callback_widget(): void {
    register_widget('ATK_VED_Callback_Widget');
}
add_action('widgets_init', 'atk_ved_register_callback_widget');

/**
 * Класс виджета обратного звонка
 */
class ATK_VED_Callback_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'atk_ved_callback_widget',
            __('АТК ВЭД: Обратный звонок', 'atk-ved'),
            array(
                'description' => __('Виджет формы обратного звонка', 'atk-ved'),
                'classname' => 'atk-ved-callback-widget',
            )
        );
    }
    
    public function widget($args, $instance): void {
        $title = !empty($instance['title']) ? apply_filters('widget_title', $instance['title']) : '';
        $button_text = !empty($instance['button_text']) ? $instance['button_text'] : __('Заказать звонок', 'atk-ved');
        $show_phone = !empty($instance['show_phone']) ? true : false;
        $phone = !empty($instance['phone']) ? $instance['phone'] : '';
        
        echo $args['before_widget'];
        
        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        ?>
        
        <div class="callback-widget-content">
            <?php if ($show_phone && $phone): ?>
            <div class="callback-widget-phone">
                <a href="tel:<?php echo esc_attr($phone); ?>">
                    <?php echo esc_html($phone); ?>
                </a>
            </div>
            <?php endif; ?>
            
            <button type="button" class="callback-widget-button" data-modal-open="callback-modal">
                <?php echo esc_html($button_text); ?>
            </button>
        </div>
        
        <?php
        echo $args['after_widget'];
    }
    
    public function form($instance): void {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $button_text = !empty($instance['button_text']) ? $instance['button_text'] : __('Заказать звонок', 'atk-ved');
        $show_phone = !empty($instance['show_phone']) ? true : false;
        $phone = !empty($instance['phone']) ? $instance['phone'] : '';
        ?>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php _e('Заголовок:', 'atk-ved'); ?>
            </label>
            <input class="widefat" 
                   id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                   type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('button_text')); ?>">
                <?php _e('Текст кнопки:', 'atk-ved'); ?>
            </label>
            <input class="widefat" 
                   id="<?php echo esc_attr($this->get_field_id('button_text')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('button_text')); ?>" 
                   type="text" 
                   value="<?php echo esc_attr($button_text); ?>">
        </p>
        
        <p>
            <input id="<?php echo esc_attr($this->get_field_id('show_phone')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('show_phone')); ?>" 
                   type="checkbox" 
                   value="1" 
                   <?php checked($show_phone); ?>>
            <label for="<?php echo esc_attr($this->get_field_id('show_phone')); ?>">
                <?php _e('Показывать телефон', 'atk-ved'); ?>
            </label>
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('phone')); ?>">
                <?php _e('Телефон:', 'atk-ved'); ?>
            </label>
            <input class="widefat" 
                   id="<?php echo esc_attr($this->get_field_id('phone')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('phone')); ?>" 
                   type="text" 
                   value="<?php echo esc_attr($phone); ?>">
        </p>
        
        <?php
    }
    
    public function update($new_instance, $old_instance): array {
        $instance = array();
        $instance['title'] = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        $instance['button_text'] = !empty($new_instance['button_text']) ? sanitize_text_field($new_instance['button_text']) : __('Заказать звонок', 'atk-ved');
        $instance['show_phone'] = !empty($new_instance['show_phone']) ? true : false;
        $instance['phone'] = !empty($new_instance['phone']) ? sanitize_text_field($new_instance['phone']) : '';
        
        return $instance;
    }
}

/**
 * Шорткод для виджета обратного звонка
 */
function atk_ved_callback_widget_shortcode(array $atts): string {
    $atts = shortcode_atts(array(
        'title' => '',
        'button_text' => __('Заказать звонок', 'atk-ved'),
        'show_phone' => '0',
        'phone' => '',
        'class' => '',
    ), $atts);
    
    ob_start();
    ?>
    <div class="callback-widget <?php echo esc_attr($atts['class']); ?>">
        <?php if ($atts['title']): ?>
        <h4 class="callback-widget-title"><?php echo esc_html($atts['title']); ?></h4>
        <?php endif; ?>
        
        <?php if ($atts['show_phone'] === '1' && $atts['phone']): ?>
        <div class="callback-widget-phone">
            <a href="tel:<?php echo esc_attr($atts['phone']); ?>">
                <?php echo esc_html($atts['phone']); ?>
            </a>
        </div>
        <?php endif; ?>
        
        <button type="button" class="callback-widget-button" data-modal-open="callback-modal">
            <?php echo esc_html($atts['button_text']); ?>
        </button>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('callback_widget', 'atk_ved_callback_widget_shortcode');

/**
 * Модальное окно обратного звонка (footer)
 */
function atk_ved_callback_modal(): void {
    ?>
    <div id="callback-modal" class="modal modal-center modal-sm" data-static-backdrop="false">
        <div class="modal-backdrop"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><?php _e('Заказать звонок', 'atk-ved'); ?></h3>
                <button type="button" class="modal-close" data-modal-close="callback-modal" aria-label="<?php _e('Закрыть', 'atk-ved'); ?>">×</button>
            </div>
            <div class="modal-body">
                <form id="callbackForm" class="callback-form">
                    <div class="form-group">
                        <label for="callback-name"><?php _e('Ваше имя', 'atk-ved'); ?> *</label>
                        <input type="text" id="callback-name" name="name" required placeholder="<?php _e('Иван', 'atk-ved'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="callback-phone"><?php _e('Телефон', 'atk-ved'); ?> *</label>
                        <input type="tel" id="callback-phone" name="phone" required placeholder="+7 (___) ___-__-__">
                    </div>
                    <div class="form-group">
                        <label for="callback-time"><?php _e('Удобное время', 'atk-ved'); ?></label>
                        <input type="text" id="callback-time" name="preferred_time" placeholder="<?php _e('Например: с 10 до 12', 'atk-ved'); ?>">
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="privacy" required checked>
                            <span><?php _e('Согласен с политикой конфиденциальности', 'atk-ved'); ?></span>
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <?php _e('Отправить заявку', 'atk-ved'); ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'atk_ved_callback_modal');

/**
 * Обработка формы обратного звонка (AJAX)
 */
function atk_ved_handle_callback_form(): void {
    check_ajax_referer('atk_ved_nonce', 'nonce');
    
    $name = sanitize_text_field($_POST['name'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $preferred_time = sanitize_text_field($_POST['preferred_time'] ?? '');
    
    if (empty($name) || empty($phone)) {
        wp_send_json_error(array('message' => __('Заполните обязательные поля', 'atk-ved')));
    }
    
    // Отправка email
    $to = get_option('admin_email');
    $subject = sprintf(__('Запрос обратного звонка от %s', 'atk-ved'), $name);
    $body = sprintf(
        "Имя: %s\nТелефон: %s\nУдобное время: %s\n\nIP: %s",
        $name,
        $phone,
        $preferred_time,
        $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
    );
    
    if (wp_mail($to, $subject, nl2br($body))) {
        // Логирование
        if (function_exists('atk_ved_log_user_action')) {
            atk_ved_log_user_action('Callback Request', array(
                'name' => $name,
                'phone' => $phone,
            ));
        }
        
        wp_send_json_success(array('message' => __('Заявка отправлена! Мы перезвоним вам в ближайшее время.', 'atk-ved')));
    } else {
        wp_send_json_error(array('message' => __('Ошибка отправки', 'atk-ved')));
    }
}
add_action('wp_ajax_atk_ved_callback', 'atk_ved_handle_callback_form');
add_action('wp_ajax_nopriv_atk_ved_callback', 'atk_ved_handle_callback_form');
