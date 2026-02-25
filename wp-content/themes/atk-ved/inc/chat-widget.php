<?php
/**
 * Online Chat Widget
 * Виджет онлайн-чата
 * 
 * @package ATK_VED
 * @since 2.5.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Настройки чата в Customizer
 */
function atk_ved_chat_customizer($wp_customize): void {
    $wp_customize->add_section('atk_ved_chat', array(
        'title'    => __('Онлайн чат', 'atk-ved'),
        'priority' => 43,
    ));

    // Включение чата
    $wp_customize->add_setting('atk_ved_chat_enabled', array(
        'default'           => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));

    $wp_customize->add_control('atk_ved_chat_enabled', array(
        'label'   => __('Включить онлайн чат', 'atk-ved'),
        'section' => 'atk_ved_chat',
        'type'    => 'checkbox',
    ));

    // Заголовок чата
    $wp_customize->add_setting('atk_ved_chat_title', array(
        'default'           => __('Онлайн консультант', 'atk-ved'),
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('atk_ved_chat_title', array(
        'label'   => __('Заголовок чата', 'atk-ved'),
        'section' => 'atk_ved_chat',
        'type'    => 'text',
    ));

    // Приветственное сообщение
    $wp_customize->add_setting('atk_ved_chat_greeting', array(
        'default'           => __('Здравствуйте! Чем можем помочь?', 'atk-ved'),
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('atk_ved_chat_greeting', array(
        'label'   => __('Приветствие', 'atk-ved'),
        'section' => 'atk_ved_chat',
        'type'    => 'text',
    ));

    // Телефон
    $wp_customize->add_setting('atk_ved_chat_phone', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('atk_ved_chat_phone', array(
        'label'   => __('Телефон', 'atk-ved'),
        'section' => 'atk_ved_chat',
        'type'    => 'text',
    ));

    // Email
    $wp_customize->add_setting('atk_ved_chat_email', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_email',
    ));

    $wp_customize->add_control('atk_ved_chat_email', array(
        'label'   => __('Email', 'atk-ved'),
        'section' => 'atk_ved_chat',
        'type'    => 'email',
    ));

    // Telegram
    $wp_customize->add_setting('atk_ved_chat_telegram', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control('atk_ved_chat_telegram', array(
        'label'   => __('Telegram', 'atk-ved'),
        'section' => 'atk_ved_chat',
        'type'    => 'url',
    ));

    // WhatsApp
    $wp_customize->add_setting('atk_ved_chat_whatsapp', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control('atk_ved_chat_whatsapp', array(
        'label'   => __('WhatsApp', 'atk-ved'),
        'section' => 'atk_ved_chat',
        'type'    => 'url',
    ));

    // Время работы
    $wp_customize->add_setting('atk_ved_chat_working_hours', array(
        'default'           => __('Пн-Пт 9:00-18:00', 'atk-ved'),
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('atk_ved_chat_working_hours', array(
        'label'   => __('Режим работы', 'atk-ved'),
        'section' => 'atk_ved_chat',
        'type'    => 'text',
    ));

    // Offline сообщение
    $wp_customize->add_setting('atk_ved_chat_offline_message', array(
        'default'           => __('Мы сейчас offline. Оставьте сообщение и мы перезвоним.', 'atk-ved'),
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('atk_ved_chat_offline_message', array(
        'label'   => __('Сообщение offline', 'atk-ved'),
        'section' => 'atk_ved_chat',
        'type'    => 'text',
    ));
}
add_action('customize_register', 'atk_ved_chat_customizer');

/**
 * Вывод виджета чата
 */
function atk_ved_chat_widget(): void {
    $enabled = get_theme_mod('atk_ved_chat_enabled', false);
    
    if (!$enabled) {
        return;
    }
    
    $title = get_theme_mod('atk_ved_chat_title', __('Онлайн консультант', 'atk-ved'));
    $greeting = get_theme_mod('atk_ved_chat_greeting', __('Здравствуйте! Чем можем помочь?', 'atk-ved'));
    $phone = get_theme_mod('atk_ved_chat_phone', '');
    $email = get_theme_mod('atk_ved_chat_email', '');
    $telegram = get_theme_mod('atk_ved_chat_telegram', '');
    $whatsapp = get_theme_mod('atk_ved_chat_whatsapp', '');
    $working_hours = get_theme_mod('atk_ved_chat_working_hours', __('Пн-Пт 9:00-18:00', 'atk-ved'));
    $offline_message = get_theme_mod('atk_ved_chat_offline_message', __('Мы сейчас offline.', 'atk-ved'));
    ?>
    
    <!-- Chat Widget -->
    <div class="chat-widget" id="chatWidget">
        <!-- Chat Button -->
        <button type="button" class="chat-widget-button" id="chatButton" aria-label="<?php esc_attr_e('Открыть чат', 'atk-ved'); ?>">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            </svg>
            <span class="chat-badge" id="chatBadge" style="display: none;">0</span>
        </button>
        
        <!-- Chat Window -->
        <div class="chat-window" id="chatWindow">
            <div class="chat-header">
                <div class="chat-header-info">
                    <h4 class="chat-title"><?php echo esc_html($title); ?></h4>
                    <span class="chat-status" id="chatStatus">
                        <span class="status-dot"></span>
                        <span class="status-text"><?php echo esc_html($working_hours); ?></span>
                    </span>
                </div>
                <button type="button" class="chat-close" id="chatClose" aria-label="<?php esc_attr_e('Закрыть чат', 'atk-ved'); ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            
            <div class="chat-body">
                <div class="chat-messages" id="chatMessages">
                    <div class="message bot">
                        <div class="message-content"><?php echo esc_html($greeting); ?></div>
                        <div class="message-time"><?php echo current_time('H:i'); ?></div>
                    </div>
                </div>
                
                <div class="chat-quick-actions" id="chatQuickActions">
                    <button type="button" class="quick-action" data-action="callback"><?php _e('Заказать звонок', 'atk-ved'); ?></button>
                    <?php if ($telegram): ?>
                    <a href="<?php echo esc_url($telegram); ?>" target="_blank" class="quick-action">Telegram</a>
                    <?php endif; ?>
                    <?php if ($whatsapp): ?>
                    <a href="<?php echo esc_url($whatsapp); ?>" target="_blank" class="quick-action">WhatsApp</a>
                    <?php endif; ?>
                </div>
                
                <form class="chat-input-form" id="chatInputForm">
                    <input type="text" id="chatInput" name="message" placeholder="<?php esc_attr_e('Введите сообщение...', 'atk-ved'); ?>" autocomplete="off">
                    <button type="submit" class="chat-submit">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                    </button>
                </form>
            </div>
            
            <div class="chat-footer">
                <?php if ($phone): ?>
                <a href="tel:<?php echo esc_attr($phone); ?>" class="chat-contact">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                    <?php echo esc_html($phone); ?>
                </a>
                <?php endif; ?>
                
                <?php if ($email): ?>
                <a href="mailto:<?php echo esc_attr($email); ?>" class="chat-contact">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    <?php echo esc_html($email); ?>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <style>
        .chat-widget {
            position: fixed;
            bottom: 100px;
            right: 30px;
            z-index: 9999;
        }
        
        .chat-widget-button {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e31e24, #c01a1f);
            color: #fff;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(227, 30, 36, 0.4);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .chat-widget-button:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 30px rgba(227, 30, 36, 0.5);
        }
        
        .chat-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #f44336;
            color: #fff;
            font-size: 12px;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 10px;
            min-width: 20px;
            text-align: center;
        }
        
        .chat-window {
            position: absolute;
            bottom: 80px;
            right: 0;
            width: 350px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            display: none;
        }
        
        .chat-window.active {
            display: block;
            animation: slideInRight 0.3s ease;
        }
        
        .chat-header {
            background: linear-gradient(135deg, #e31e24, #c01a1f);
            color: #fff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .chat-title {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }
        
        .chat-status {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            opacity: 0.9;
        }
        
        .status-dot {
            width: 8px;
            height: 8px;
            background: #4CAF50;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        .chat-close {
            background: transparent;
            border: none;
            color: #fff;
            cursor: pointer;
            padding: 5px;
        }
        
        .chat-body {
            height: 400px;
            display: flex;
            flex-direction: column;
        }
        
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
            background: #f5f5f5;
        }
        
        .message {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }
        
        .message.bot {
            align-items: flex-start;
        }
        
        .message.user {
            align-items: flex-end;
        }
        
        .message-content {
            background: #fff;
            padding: 10px 15px;
            border-radius: 12px;
            max-width: 80%;
            font-size: 14px;
        }
        
        .message.user .message-content {
            background: #e31e24;
            color: #fff;
        }
        
        .message-time {
            font-size: 11px;
            color: #999;
            margin-top: 4px;
        }
        
        .chat-quick-actions {
            display: flex;
            gap: 10px;
            padding: 10px 15px;
            border-top: 1px solid #e0e0e0;
        }
        
        .quick-action {
            padding: 8px 16px;
            background: #f0f0f0;
            border: none;
            border-radius: 20px;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            color: #333;
            transition: all 0.2s;
        }
        
        .quick-action:hover {
            background: #e0e0e0;
        }
        
        .chat-input-form {
            display: flex;
            padding: 10px 15px;
            border-top: 1px solid #e0e0e0;
            gap: 10px;
        }
        
        .chat-input-form input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 20px;
            font-size: 14px;
        }
        
        .chat-submit {
            background: #e31e24;
            color: #fff;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .chat-footer {
            padding: 10px 15px;
            border-top: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .chat-contact {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #666;
            text-decoration: none;
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @media (max-width: 768px) {
            .chat-widget {
                bottom: 80px;
                right: 15px;
            }
            
            .chat-window {
                width: calc(100vw - 30px);
                bottom: 80px;
            }
        }
    </style>
    
    <script>
    (function($) {
        'use strict';
        
        $(document).ready(function() {
            var $chatWidget = $('#chatWidget');
            var $chatWindow = $('#chatWindow');
            var $chatButton = $('#chatButton');
            var $chatClose = $('#chatClose');
            var $chatInput = $('#chatInput');
            var $chatInputForm = $('#chatInputForm');
            var $chatMessages = $('#chatMessages');
            
            // Toggle chat
            $chatButton.on('click', function() {
                $chatWindow.toggleClass('active');
            });
            
            $chatClose.on('click', function() {
                $chatWindow.removeClass('active');
            });
            
            // Send message
            $chatInputForm.on('submit', function(e) {
                e.preventDefault();
                
                var message = $chatInput.val().trim();
                if (!message) return;
                
                // Add user message
                addMessage(message, 'user');
                $chatInput.val('');
                
                // Auto-reply
                setTimeout(function() {
                    addMessage('<?php echo esc_js($offline_message); ?>', 'bot');
                }, 1000);
            });
            
            // Quick actions
            $('.quick-action[data-action="callback"]').on('click', function() {
                if (typeof atkOpenModal === 'function') {
                    atkOpenModal('callback-modal');
                }
            });
            
            function addMessage(text, type) {
                var time = new Date().toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
                var html = '<div class="message ' + type + '">' +
                    '<div class="message-content">' + text + '</div>' +
                    '<div class="message-time">' + time + '</div>' +
                    '</div>';
                $chatMessages.append(html);
                $chatMessages.scrollTop($chatMessages[0].scrollHeight);
            }
        });
    })(jQuery);
    </script>
    <?php
}
add_action('wp_footer', 'atk_ved_chat_widget');
