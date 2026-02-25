<?php
/**
 * Интеграция онлайн-чатов
 * 
 * @package ATK_VED
 * @since 2.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Jivosite
 */
function atk_ved_add_jivosite(): void {
    $jivosite_id = get_theme_mod('atk_ved_jivosite_id', '');
    
    if (empty($jivosite_id)) {
        return;
    }
    ?>
    <!-- Jivosite Code -->
    <script>
    (function(){ 
        var widget_id = '<?php echo esc_js($jivosite_id); ?>';
        var d=document;
        var w=window;
        function l(){
            var s = document.createElement('script'); 
            s.type = 'text/javascript'; 
            s.async = true;
            s.src = '//code.jivosite.com/script/widget/'+widget_id;
            var ss = document.getElementsByTagName('script')[0]; 
            ss.parentNode.insertBefore(s, ss);
        }
        if(d.readyState=='complete'){l();}else{
            if(w.attachEvent){w.attachEvent('onload',l);}
            else{w.addEventListener('load',l,false);}
        }
    })();
    </script>
    <!-- /Jivosite Code -->
    <?php
}
add_action('wp_footer', 'atk_ved_add_jivosite', 100);

/**
 * Carrot quest
 */
function atk_ved_add_carrotquest(): void {
    $carrot_id = get_theme_mod('atk_ved_carrotquest_id', '');
    
    if (empty($carrot_id)) {
        return;
    }
    ?>
    <!-- Carrot quest Code -->
    <script>
    (function(){
        function Build(name, args){return function(){window.carrotquestasync.push(name, arguments);}}
        if (typeof carrotquest === 'undefined') {
            var s = document.createElement('script'); 
            s.type = 'text/javascript'; 
            s.async = true;
            s.src = '//cdn.carrotquest.io/api.min.js';
            var x = document.getElementsByTagName('head')[0]; 
            x.appendChild(s);
            window.carrotquest = {};
            window.carrotquestasync = [];
            carrotquest.settings = {};
            var m = ['connect','track','identify','auth','oth','onReady','addCallback','removeCallback','trackMessageInteraction'];
            for (var i = 0; i < m.length; i++) carrotquest[m[i]] = Build(m[i]);
        }
        carrotquest.connect('<?php echo esc_js($carrot_id); ?>');
    })();
    </script>
    <!-- /Carrot quest Code -->
    <?php
}
add_action('wp_footer', 'atk_ved_add_carrotquest', 100);

/**
 * Telegram Widget
 */
function atk_ved_add_telegram_widget(): void {
    $telegram_username = get_theme_mod('atk_ved_telegram_username', '');
    
    if (empty($telegram_username)) {
        return;
    }
    ?>
    <!-- Telegram Widget -->
    <div class="telegram-widget">
        <a href="https://t.me/<?php echo esc_attr($telegram_username); ?>" 
           target="_blank" 
           rel="noopener noreferrer"
           class="telegram-widget-btn"
           aria-label="Написать в Telegram">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z" fill="currentColor"/>
            </svg>
            <span>Написать в Telegram</span>
        </a>
    </div>
    
    <style>
    .telegram-widget {
        position: fixed;
        bottom: 170px;
        right: 30px;
        z-index: 997;
    }
    
    .telegram-widget-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 20px;
        background: linear-gradient(135deg, #0088cc 0%, #00a0e9 100%);
        color: #fff;
        border-radius: 50px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 4px 20px rgba(0, 136, 204, 0.4);
        transition: all 0.3s ease;
        animation: pulse-telegram 2s infinite;
    }
    
    .telegram-widget-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 30px rgba(0, 136, 204, 0.6);
        animation: none;
    }
    
    .telegram-widget-btn svg {
        flex-shrink: 0;
    }
    
    @keyframes pulse-telegram {
        0%, 100% {
            box-shadow: 0 4px 20px rgba(0, 136, 204, 0.4), 0 0 0 0 rgba(0, 136, 204, 0.7);
        }
        50% {
            box-shadow: 0 4px 20px rgba(0, 136, 204, 0.4), 0 0 0 15px rgba(0, 136, 204, 0);
        }
    }
    
    @media (max-width: 768px) {
        .telegram-widget {
            bottom: 140px;
            right: 20px;
        }
        
        .telegram-widget-btn span {
            display: none;
        }
        
        .telegram-widget-btn {
            width: 56px;
            height: 56px;
            padding: 0;
            justify-content: center;
            border-radius: 50%;
        }
    }
    </style>
    <!-- /Telegram Widget -->
    <?php
}
add_action('wp_footer', 'atk_ved_add_telegram_widget', 99);

/**
 * WhatsApp Widget
 */
function atk_ved_add_whatsapp_widget(): void {
    $whatsapp_number = get_theme_mod('atk_ved_whatsapp_number', '');
    
    if (empty($whatsapp_number)) {
        return;
    }
    
    // Очищаем номер от лишних символов
    $clean_number = preg_replace('/[^0-9]/', '', $whatsapp_number);
    ?>
    <!-- WhatsApp Widget -->
    <div class="whatsapp-widget">
        <a href="https://wa.me/<?php echo esc_attr($clean_number); ?>?text=Здравствуйте!%20Хочу%20узнать%20о%20доставке%20из%20Китая" 
           target="_blank" 
           rel="noopener noreferrer"
           class="whatsapp-widget-btn"
           aria-label="Написать в WhatsApp">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" fill="currentColor"/>
            </svg>
            <span>Написать в WhatsApp</span>
        </a>
    </div>
    
    <style>
    .whatsapp-widget {
        position: fixed;
        bottom: 240px;
        right: 30px;
        z-index: 997;
    }
    
    .whatsapp-widget-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 20px;
        background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
        color: #fff;
        border-radius: 50px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);
        transition: all 0.3s ease;
        animation: pulse-whatsapp 2s infinite;
    }
    
    .whatsapp-widget-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 30px rgba(37, 211, 102, 0.6);
        animation: none;
    }
    
    .whatsapp-widget-btn svg {
        flex-shrink: 0;
    }
    
    @keyframes pulse-whatsapp {
        0%, 100% {
            box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4), 0 0 0 0 rgba(37, 211, 102, 0.7);
        }
        50% {
            box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4), 0 0 0 15px rgba(37, 211, 102, 0);
        }
    }
    
    @media (max-width: 768px) {
        .whatsapp-widget {
            bottom: 210px;
            right: 20px;
        }
        
        .whatsapp-widget-btn span {
            display: none;
        }
        
        .whatsapp-widget-btn {
            width: 56px;
            height: 56px;
            padding: 0;
            justify-content: center;
            border-radius: 50%;
        }
    }
    </style>
    <!-- /WhatsApp Widget -->
    <?php
}
add_action('wp_footer', 'atk_ved_add_whatsapp_widget', 99);

/**
 * Настройки чатов в Customizer
 */
function atk_ved_chat_customizer($wp_customize): void {
    // Секция онлайн-чатов
    $wp_customize->add_section('atk_ved_chat', array(
        'title'    => __('Онлайн-чаты', 'atk-ved'),
        'priority' => 36,
        'description' => __('Настройка виджетов онлайн-чатов и мессенджеров', 'atk-ved')
    ));

    // Jivosite
    $wp_customize->add_setting('atk_ved_jivosite_id', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('atk_ved_jivosite_id', array(
        'label'       => __('Jivosite Widget ID', 'atk-ved'),
        'section'     => 'atk_ved_chat',
        'type'        => 'text',
        'description' => __('Например: AbCdEfGh12', 'atk-ved')
    ));

    // Carrot quest
    $wp_customize->add_setting('atk_ved_carrotquest_id', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('atk_ved_carrotquest_id', array(
        'label'       => __('Carrot quest ID', 'atk-ved'),
        'section'     => 'atk_ved_chat',
        'type'        => 'text',
        'description' => __('Например: 12345-67890abcdef', 'atk-ved')
    ));

    // Telegram
    $wp_customize->add_setting('atk_ved_telegram_username', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('atk_ved_telegram_username', array(
        'label'       => __('Telegram Username', 'atk-ved'),
        'section'     => 'atk_ved_chat',
        'type'        => 'text',
        'description' => __('Без @, например: your_username', 'atk-ved')
    ));

    // WhatsApp
    $wp_customize->add_setting('atk_ved_whatsapp_number', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('atk_ved_whatsapp_number', array(
        'label'       => __('WhatsApp номер', 'atk-ved'),
        'section'     => 'atk_ved_chat',
        'type'        => 'text',
        'description' => __('С кодом страны, например: 79991234567', 'atk-ved')
    ));
}
add_action('customize_register', 'atk_ved_chat_customizer');
