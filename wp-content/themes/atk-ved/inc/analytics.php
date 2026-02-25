<?php
/**
 * Аналитика и отслеживание
 * 
 * @package ATK_VED
 * @since 1.8.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Подключение Яндекс.Метрики
 * ID счётчика настраивается через Customizer
 */
function atk_ved_add_yandex_metrika(): void {
    $metrika_id = get_theme_mod('atk_ved_metrika_id', '');
    
    if (empty($metrika_id)) {
        return;
    }
    ?>
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function(m,e,t,r,i,k,a){
            m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();
            for(var j=0;j<document.scripts.length;j++){
                if(document.scripts[j].src===r){return;}
            }
            k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)
        })(window,document,'script','https://mc.yandex.ru/metrika/tag.js','ym');

        ym(<?php echo esc_js($metrika_id); ?>, 'init', {
            webvisor: true,
            track_links: true,
            track_hash: true,
            clickmap: true,
            accurate_track_bounce: true,
            ecommerce: 'dataLayer'
        });
    </script>
    <noscript>
        <div>
            <img src="https://mc.yandex.ru/watch/<?php echo esc_attr($metrika_id); ?>" 
                 style="position:absolute; left:-9999px;" alt="Yandex.Metrika" />
        </div>
    </noscript>
    <!-- /Yandex.Metrika counter -->
    <?php
}
add_action('wp_head', 'atk_ved_add_yandex_metrika', 1);

/**
 * Подключение Google Analytics 4
 * ID измерения настраивается через Customizer
 */
function atk_ved_add_google_analytics(): void {
    $ga_id = get_theme_mod('atk_ved_ga_id', '');
    
    if (empty($ga_id)) {
        return;
    }
    ?>
    <!-- Google Analytics 4 -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($ga_id); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo esc_js($ga_id); ?>', {
            'send_page_view': true,
            'anonymize_ip': true
        });
    </script>
    <!-- /Google Analytics 4 -->
    <?php
}
add_action('wp_head', 'atk_ved_add_google_analytics', 1);

/**
 * Facebook Pixel
 */
function atk_ved_add_facebook_pixel(): void {
    $pixel_id = get_theme_mod('atk_ved_facebook_pixel', '');
    
    if (empty($pixel_id)) {
        return;
    }
    ?>
    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s){
            if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '<?php echo esc_js($pixel_id); ?>');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none"
             src="https://www.facebook.com/tr?id=<?php echo esc_attr($pixel_id); ?>&ev=PageView&noscript=1"
             alt="Facebook Pixel" />
    </noscript>
    <!-- End Facebook Pixel Code -->
    <?php
}
add_action('wp_head', 'atk_ved_add_facebook_pixel', 1);

/**
 * VK Retargeting Pixel
 */
function atk_ved_add_vk_pixel(): void {
    $vk_pixel = get_theme_mod('atk_ved_vk_pixel', '');
    
    if (empty($vk_pixel)) {
        return;
    }
    ?>
    <!-- VK Retargeting Pixel -->
    <script type="text/javascript">
        (function() {
            var retargeting = document.createElement('script');
            retargeting.type = 'text/javascript';
            retargeting.async = true;
            retargeting.src = 'https://vk.com/js/api/retargeting.js';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(retargeting, s);
        })();
        
        window.addEventListener('load', function() {
            VK.Retargeting.init('<?php echo esc_js($vk_pixel); ?>');
            VK.Retargeting.track('PageView');
        });
    </script>
    <!-- /VK Retargeting Pixel -->
    <?php
}
add_action('wp_head', 'atk_ved_add_vk_pixel', 1);

/**
 * Отправка событий в аналитику при отправке форм
 */
function atk_ved_add_analytics_events(): void {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Отслеживание отправки форм
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                var formName = form.id || 'contact_form';
                
                // Yandex.Metrika
                if (typeof ym !== 'undefined') {
                    ym(<?php echo esc_js(get_theme_mod('atk_ved_metrika_id', 0)); ?>, 'reachGoal', 'form_submit', {
                        form: formName
                    });
                }
                
                // Google Analytics
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'form_submit', {
                        'event_category': 'Forms',
                        'event_label': formName
                    });
                }
                
                // Facebook Pixel
                if (typeof fbq !== 'undefined') {
                    fbq('track', 'Lead');
                }
            });
        });
        
        // Отслеживание кликов по телефону
        document.querySelectorAll('a[href^="tel:"]').forEach(function(link) {
            link.addEventListener('click', function() {
                if (typeof ym !== 'undefined') {
                    ym(<?php echo esc_js(get_theme_mod('atk_ved_metrika_id', 0)); ?>, 'reachGoal', 'phone_click');
                }
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'phone_click', {
                        'event_category': 'Contacts',
                        'event_label': this.href
                    });
                }
            });
        });
        
        // Отслеживание кликов по email
        document.querySelectorAll('a[href^="mailto:"]').forEach(function(link) {
            link.addEventListener('click', function() {
                if (typeof ym !== 'undefined') {
                    ym(<?php echo esc_js(get_theme_mod('atk_ved_metrika_id', 0)); ?>, 'reachGoal', 'email_click');
                }
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'email_click', {
                        'event_category': 'Contacts',
                        'event_label': this.href
                    });
                }
            });
        });
        
        // Отслеживание кликов по мессенджерам
        document.querySelectorAll('a[href*="wa.me"], a[href*="whatsapp.com"], a[href*="t.me"]').forEach(function(link) {
            link.addEventListener('click', function() {
                var messenger = 'messenger';
                if (this.href.indexOf('wa.me') !== -1 || this.href.indexOf('whatsapp.com') !== -1) {
                    messenger = 'whatsapp';
                } else if (this.href.indexOf('t.me') !== -1) {
                    messenger = 'telegram';
                }
                
                if (typeof ym !== 'undefined') {
                    ym(<?php echo esc_js(get_theme_mod('atk_ved_metrika_id', 0)); ?>, 'reachGoal', 'messenger_click', {
                        messenger: messenger
                    });
                }
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'messenger_click', {
                        'event_category': 'Contacts',
                        'event_label': messenger
                    });
                }
            });
        });
        
        // Отслеживание прокрутки до конца страницы
        var scrolledToEnd = false;
        window.addEventListener('scroll', function() {
            if (!scrolledToEnd && (window.innerHeight + window.scrollY) >= document.body.offsetHeight - 100) {
                scrolledToEnd = true;
                if (typeof ym !== 'undefined') {
                    ym(<?php echo esc_js(get_theme_mod('atk_ved_metrika_id', 0)); ?>, 'reachGoal', 'scroll_100');
                }
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'scroll_100', {
                        'event_category': 'Engagement'
                    });
                }
            }
        });
        
        // Отслеживание кликов по кнопке CTA
        document.querySelectorAll('.cta-button').forEach(function(button) {
            button.addEventListener('click', function() {
                if (typeof ym !== 'undefined') {
                    ym(<?php echo esc_js(get_theme_mod('atk_ved_metrika_id', 0)); ?>, 'reachGoal', 'cta_click');
                }
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'cta_click', {
                        'event_category': 'CTA'
                    });
                }
            });
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'atk_ved_add_analytics_events', 100);

/**
 * Настройки аналитики в Customizer
 */
function atk_ved_analytics_customizer($wp_customize): void {
    // Секция аналитики
    $wp_customize->add_section('atk_ved_analytics', array(
        'title'    => __('Аналитика', 'atk-ved'),
        'priority' => 35,
        'description' => __('ID счётчиков и пикселей для отслеживания', 'atk-ved')
    ));

    // Яндекс.Метрика
    $wp_customize->add_setting('atk_ved_metrika_id', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('atk_ved_metrika_id', array(
        'label'       => __('ID Яндекс.Метрики', 'atk-ved'),
        'section'     => 'atk_ved_analytics',
        'type'        => 'text',
        'description' => __('Например: 12345678', 'atk-ved')
    ));

    // Google Analytics 4
    $wp_customize->add_setting('atk_ved_ga_id', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('atk_ved_ga_id', array(
        'label'       => __('ID Google Analytics 4', 'atk-ved'),
        'section'     => 'atk_ved_analytics',
        'type'        => 'text',
        'description' => __('Например: G-XXXXXXXXXX', 'atk-ved')
    ));

    // Facebook Pixel
    $wp_customize->add_setting('atk_ved_facebook_pixel', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('atk_ved_facebook_pixel', array(
        'label'       => __('Facebook Pixel ID', 'atk-ved'),
        'section'     => 'atk_ved_analytics',
        'type'        => 'text'
    ));

    // VK Retargeting
    $wp_customize->add_setting('atk_ved_vk_pixel', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('atk_ved_vk_pixel', array(
        'label'       => __('VK Retargeting Pixel ID', 'atk-ved'),
        'section'     => 'atk_ved_analytics',
        'type'        => 'text'
    ));
}
add_action('customize_register', 'atk_ved_analytics_customizer');
