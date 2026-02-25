<?php
/**
 * Cookie Banner для соответствия 152-ФЗ
 * 
 * @package ATK_VED
 * @since 1.8.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Добавление cookie banner в footer
 */
function atk_ved_cookie_banner(): void {
    // Не показывать если пользователь уже принял
    if (!empty($_COOKIE['atk_ved_cookies_accepted'])) {
        return;
    }
    ?>
    <div class="cookie-banner" id="cookieBanner" role="alert" aria-live="polite">
        <div class="cookie-banner-content">
            <div class="cookie-banner-text">
                <p>
                    <strong><?php echo esc_html__('Мы используем cookie', 'atk-ved'); ?></strong>
                </p>
                <p>
                    <?php echo esc_html__('Этот сайт использует файлы cookie для улучшения пользовательского опыта, анализа трафика и персонализации контента. Продолжая использовать сайт, вы соглашаетесь с нашей', 'atk-ved'); ?>
                    <a href="<?php echo esc_url(get_privacy_policy_url()); ?>" target="_blank" rel="noopener">
                        <?php echo esc_html__('политикой конфиденциальности', 'atk-ved'); ?>
                    </a>.
                </p>
                <p class="cookie-banner-152fz">
                    <small>
                        <?php echo esc_html__('В соответствии с Федеральным законом № 152-ФЗ «О персональных данных»', 'atk-ved'); ?>
                    </small>
                </p>
            </div>
            <div class="cookie-banner-actions">
                <button type="button" class="cookie-accept" id="cookieAccept" aria-label="<?php echo esc_attr__('Принять и закрыть', 'atk-ved'); ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <?php echo esc_html__('Принять', 'atk-ved'); ?>
                </button>
            </div>
        </div>
        <button type="button" class="cookie-banner-close" id="cookieBannerClose" aria-label="<?php echo esc_attr__('Закрыть уведомление', 'atk-ved'); ?>">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    <style>
        .cookie-banner {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            padding: 20px;
            transform: translateY(100%);
            transition: transform 0.3s ease;
            border-top: 3px solid #e31e24;
        }

        .cookie-banner.visible {
            transform: translateY(0);
        }

        .cookie-banner-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 20px;
            align-items: center;
            padding-right: 40px;
        }

        .cookie-banner-text p {
            margin: 0 0 10px;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
        }

        .cookie-banner-text p:last-child {
            margin-bottom: 0;
        }

        .cookie-banner-text a {
            color: #e31e24;
            text-decoration: underline;
        }

        .cookie-banner-text a:hover {
            text-decoration: none;
        }

        .cookie-banner-152fz {
            margin-top: 10px !important;
        }

        .cookie-banner-152fz small {
            color: #888;
            font-size: 12px;
        }

        .cookie-banner-actions {
            display: flex;
            gap: 10px;
        }

        .cookie-accept {
            background: #e31e24;
            color: #fff;
            border: none;
            padding: 12px 30px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .cookie-accept:hover {
            background: #c01a1f;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(227, 30, 36, 0.3);
        }

        .cookie-accept svg {
            width: 20px;
            height: 20px;
        }

        .cookie-banner-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 5px;
            color: #888;
            transition: color 0.3s ease;
        }

        .cookie-banner-close:hover {
            color: #333;
        }

        @media (max-width: 768px) {
            .cookie-banner-content {
                grid-template-columns: 1fr;
                gap: 15px;
                padding-right: 30px;
            }

            .cookie-banner-actions {
                justify-content: center;
            }

            .cookie-accept {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <script>
    (function() {
        var banner = document.getElementById('cookieBanner');
        var acceptBtn = document.getElementById('cookieAccept');
        var closeBtn = document.getElementById('cookieBannerClose');

        // Показываем banner с задержкой
        setTimeout(function() {
            banner.classList.add('visible');
        }, 2000);

        // Функция принятия cookies
        function acceptCookies() {
            // Устанавливаем cookie на 1 год
            var expiryDate = new Date();
            expiryDate.setFullYear(expiryDate.getFullYear() + 1);
            document.cookie = 'atk_ved_cookies_accepted=1;expires=' + expiryDate.toUTCString() + ';path=/;SameSite=Lax';

            // Скрываем banner
            banner.classList.remove('visible');

            // Отправляем событие в аналитику
            if (typeof ym !== 'undefined') {
                ym(<?php echo esc_js(get_theme_mod('atk_ved_metrika_id', 0)); ?>, 'reachGoal', 'cookie_accept');
            }
            if (typeof gtag !== 'undefined') {
                gtag('event', 'cookie_accept', {
                    'event_category': 'Cookies',
                    'event_label': '152-FZ Compliance'
                });
            }
        }

        // Обработчики событий
        if (acceptBtn) {
            acceptBtn.addEventListener('click', acceptCookies);
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                banner.classList.remove('visible');
            });
        }

        // Закрытие по Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && banner.classList.contains('visible')) {
                banner.classList.remove('visible');
            }
        });
    })();
    </script>
    <?php
}
add_action('wp_footer', 'atk_ved_cookie_banner', 100);

/**
 * Настройки cookie banner в Customizer
 */
function atk_ved_cookie_customizer($wp_customize): void {
    $wp_customize->add_section('atk_ved_cookie', array(
        'title'    => __('Cookie Banner', 'atk-ved'),
        'priority' => 36,
    ));

    // Включение/выключение banner
    $wp_customize->add_setting('atk_ved_cookie_enabled', array(
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));

    $wp_customize->add_control('atk_ved_cookie_enabled', array(
        'label'   => __('Показывать cookie banner', 'atk-ved'),
        'section' => 'atk_ved_cookie',
        'type'    => 'checkbox',
    ));
}
add_action('customize_register', 'atk_ved_cookie_customizer');
