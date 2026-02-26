<?php
/**
 * Безопасность
 *
 * @package ATK_VED
 * @since 1.0.0
 */

declare(strict_types=1);

/**
 * Скрытие версии WordPress
 *
 * @return string Пустая строка
 */
function atk_ved_remove_wp_version(): string {
    return '';
}
add_filter('the_generator', 'atk_ved_remove_wp_version');

/**
 * Дополнительное скрытие версии WordPress
 *
 * @return void
 */
function atk_ved_remove_version_from_scripts_and_styles(): void {
    if (!is_admin()) {
        // Удаляем версию из RSS-лент
        add_filter('the_generator', '__return_empty_string', 999);

        // Удаляем версию из RSS заголовков
        remove_action('wp_head', 'feed_links_extra', 3);
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'index_rel_link');
        remove_action('wp_head', 'parent_post_rel_link', 10, 0);
        remove_action('wp_head', 'start_post_rel_link', 10, 0);
        remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);
        remove_action('wp_head', 'wp_generator');

        // Удаляем версию из script и style тегов
        add_filter('style_loader_src', 'atk_ved_remove_version_from_url', 9999);
        add_filter('script_loader_src', 'atk_ved_remove_version_from_url', 9999);
    }
}
add_action('init', 'atk_ved_remove_version_from_scripts_and_styles');

/**
 * Функция для удаления версии из URL
 *
 * @param string $src Исходный URL
 * @return string URL без версии
 */
function atk_ved_remove_version_from_url(string $src): string {
    if (strpos($src, 'ver=') !== false) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}

// Отключение редактирования файлов из админки
if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}

// Honeypot для защиты от спама в формах
function atk_ved_add_honeypot_field(): void {
    echo '<div class="hp-field" style="position:absolute;left:-9999px;" aria-hidden="true">';
    echo '<label for="hp_website">Website</label>';
    echo '<input type="text" name="hp_website" id="hp_website" value="" autocomplete="off" tabindex="-1">';
    echo '</div>';
    
    // Добавляем timestamp для проверки времени заполнения
    $timestamp = time();
    $token = wp_hash($timestamp . NONCE_KEY);
    echo '<input type="hidden" name="hp_timestamp" value="' . esc_attr($timestamp) . '">';
    echo '<input type="hidden" name="hp_token" value="' . esc_attr($token) . '">';
}
add_action('wp_footer', 'atk_ved_add_honeypot_field');

// Проверка honeypot
function atk_ved_verify_honeypot(): bool {
    // Если поле заполнено - это бот
    if (!empty($_POST['hp_website'])) {
        atk_ved_log_security_event('Honeypot сработал: заполнено скрытое поле');
        return false;
    }
    
    // Проверка timestamp (форма не должна быть отправлена быстрее чем за 3 секунды)
    if (!empty($_POST['hp_timestamp'])) {
        $time_diff = time() - (int) $_POST['hp_timestamp'];
        if ($time_diff < 3) {
            atk_ved_log_security_event('Honeypot сработал: слишком быстрая отправка формы');
            return false;
        }
    }
    
    // Проверка токена
    if (!empty($_POST['hp_token']) && !empty($_POST['hp_timestamp'])) {
        $expected_token = wp_hash($_POST['hp_timestamp'] . NONCE_KEY);
        if (!hash_equals($expected_token, $_POST['hp_token'])) {
            atk_ved_log_security_event('Honeypot сработал: неверный токен');
            return false;
        }
    }
    
    return true;
}
add_action('wp_ajax_nopriv_atk_ved_contact_form', 'atk_ved_check_honeypot_before_ajax', 1);
add_action('wp_ajax_atk_ved_contact_form', 'atk_ved_check_honeypot_before_ajax', 1);

function atk_ved_check_honeypot_before_ajax(): void {
    if (!atk_ved_verify_honeypot()) {
        wp_send_json_error(array('message' => 'Подозрительная активность'));
    }
}

// Защита от SQL инъекций
function atk_ved_sanitize_sql($input): string {
    global $wpdb;
    return $wpdb->prepare('%s', $input);
}

// Ограничение попыток входа
function atk_ved_limit_login_attempts() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $attempts = get_transient('login_attempts_' . $ip);
    
    if ($attempts && $attempts >= 5) {
        wp_die('Слишком много попыток входа. Попробуйте через 15 минут.');
    }
}
add_action('wp_login_failed', function() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $attempts = get_transient('login_attempts_' . $ip) ?: 0;
    set_transient('login_attempts_' . $ip, $attempts + 1, 15 * MINUTE_IN_SECONDS);
});

add_action('wp_login', function() {
    $ip = $_SERVER['REMOTE_ADDR'];
    delete_transient('login_attempts_' . $ip);
});

// Защита от XSS
function atk_ved_sanitize_output($content) {
    return htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
}

// Отключение отображения ошибок входа
function atk_ved_login_errors() {
    return 'Неверные учетные данные';
}
add_filter('login_errors', 'atk_ved_login_errors');

// Изменение URL входа (опционально)
// Требует настройки .htaccess

// Защита wp-config.php
function atk_ved_protect_wp_config() {
    if (strpos($_SERVER['REQUEST_URI'], 'wp-config.php') !== false) {
        header('HTTP/1.0 403 Forbidden');
        exit;
    }
}
add_action('init', 'atk_ved_protect_wp_config');

// Отключение просмотра директорий
// Добавить в .htaccess: Options -Indexes

/**
 * Защита от hotlinking изображений
 * Примечание: Функция отключена, так как может блокировать легитимные запросы
 * Для включения раскомментируйте add_action в конце файла
 */
function atk_ved_prevent_hotlinking(): void {
    if (!is_admin()) {
        $referer    = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $site_url   = home_url();
        $parsed_url = wp_parse_url($site_url);
        $host       = $parsed_url['host'] ?? '';

        if ($referer && strpos($referer, $host) === false) {
            // Проверка на изображения
            $request_uri = $_SERVER['REQUEST_URI'];
            if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $request_uri)) {
                header('HTTP/1.0 403 Forbidden');
                atk_ved_log_security_event('Hotlinking blocked', $referer);
                exit;
            }
        }
    }
}
// add_action('init', 'atk_ved_prevent_hotlinking'); // Отключено по умолчанию

// Добавление заголовков безопасности
function atk_ved_security_headers() {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\' https://www.google-analytics.com https://www.googletagmanager.com; style-src \'self\' \'unsafe-inline\' https://fonts.googleapis.com; font-src \'self\' https://fonts.gstatic.com; img-src \'self\' data: https:; frame-src https:');
}
add_action('send_headers', 'atk_ved_security_headers');

// Отключение XML-RPC для предотвращения DDoS
add_filter('xmlrpc_enabled', '__return_false');

/**
 * Удаление информации о пользователях из REST API
 *
 * @param WP_Error|mixed $result Результат проверки прав
 * @return WP_Error|mixed
 */
function atk_ved_remove_user_info_rest( $result ) {
	if ( ! is_user_logged_in() ) {
		return new \WP_Error(
			'rest_user_cannot_view',
			'Доступ запрещен',
			[ 'status' => 403 ]
		);
	}
	return $result;
}
add_filter( 'rest_authentication_errors', 'atk_ved_remove_user_info_rest' );

/**
 * Защита от перебора паролей (задержка)
 *
 * @return void
 */
function atk_ved_add_login_delay(): void {
	sleep( 2 );
}
add_action( 'wp_login_failed', 'atk_ved_add_login_delay' );

/**
 * Скрытие логина администратора
 *
 * @param string $login Имя пользователя
 * @return string
 */
function atk_ved_hide_admin_login( string $login ): string {
	if ( $login === 'admin' ) {
		return 'Пользователь';
	}
	return $login;
}
add_filter( 'the_author', 'atk_ved_hide_admin_login' );

/**
 * Отключение отображения версий плагинов
 *
 * @return void
 */
function atk_ved_remove_plugin_versions(): void {
	global $wp_scripts, $wp_styles;

	if ( ! is_admin() ) {
		foreach ( $wp_scripts->registered as $handle => $script ) {
			$wp_scripts->registered[ $handle ]->ver = null;
		}

		foreach ( $wp_styles->registered as $handle => $style ) {
			$wp_styles->registered[ $handle ]->ver = null;
		}
	}
}
add_action( 'wp_enqueue_scripts', 'atk_ved_remove_plugin_versions', 999 );

/**
 * Защита от SQL инъекций в URL
 *
 * @return void
 */
function atk_ved_sanitize_url_params(): void {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		foreach ( $_GET as $key => $value ) {
			if ( is_string( $value ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$_GET[ $key ] = sanitize_text_field( $value );
			}
		}
	}
}
add_action( 'init', 'atk_ved_sanitize_url_params' );

/**
 * Блокировка подозрительных запросов
 *
 * @return void
 */
function atk_ved_block_suspicious_requests(): void {
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$request_uri = $_SERVER['REQUEST_URI'] ?? '';

	$suspicious_patterns = [
		'/eval\(/i',
		'/base64_decode/i',
		'/gzinflate/i',
		'/\.\.\//i',
		'/<script/i',
		'/union.*select/i',
		'/concat.*\(/i',
	];

	foreach ( $suspicious_patterns as $pattern ) {
		if ( preg_match( $pattern, $request_uri ) ) {
			header( 'HTTP/1.0 403 Forbidden' );
			atk_ved_log_security_event( 'Заблокирован подозрительный запрос', $request_uri );
			exit( 'Подозрительный запрос заблокирован' );
		}
	}

	// Дополнительная проверка POST данных
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
	if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_php_file_get_contents
		$post_data = file_get_contents( 'php://input' );
		foreach ( $suspicious_patterns as $pattern ) {
			if ( preg_match( $pattern, $post_data ) ) {
				header( 'HTTP/1.0 403 Forbidden' );
				atk_ved_log_security_event( 'Заблокирован подозрительный POST запрос', $post_data );
				exit( 'Подозрительный запрос заблокирован' );
			}
		}
	}
}
add_action( 'init', 'atk_ved_block_suspicious_requests' );

/**
 * Проверка referer для форм
 *
 * @return void
 */
function atk_ved_verify_referer(): void {
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
	if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$referer = $_SERVER['HTTP_REFERER'] ?? '';
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$host = $_SERVER['HTTP_HOST'] ?? '';

		if ( $referer && ! strpos( $referer, $host ) ) {
			atk_ved_log_security_event( 'Потенциально небезопасный referer', $referer );
		}
	}
}
add_action( 'init', 'atk_ved_verify_referer' );

/**
 * Добавление CSRF токена
 *
 * @return void
 */
function atk_ved_add_csrf_token(): void {
	if ( ! session_id() ) {
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		@session_start();
	}

	if ( ! isset( $_SESSION['csrf_token'] ) ) {
		$_SESSION['csrf_token'] = bin2hex( random_bytes( 32 ) );
	}
}
add_action( 'init', 'atk_ved_add_csrf_token' );

/**
 * Проверка CSRF токена
 *
 * @return bool
 */
function atk_ved_verify_csrf_token(): bool {
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
	if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
		if ( ! isset( $_POST['csrf_token'] ) || ! isset( $_SESSION['csrf_token'] ) ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		return hash_equals( $_SESSION['csrf_token'], $_POST['csrf_token'] );
	}

	return true;
}

/**
 * Генерация CSRF токена для форм
 *
 * @return string
 */
function atk_ved_get_csrf_token(): string {
	if ( ! session_id() ) {
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		@session_start();
	}

	if ( ! isset( $_SESSION['csrf_token'] ) ) {
		$_SESSION['csrf_token'] = bin2hex( random_bytes( 32 ) );
	}

	return $_SESSION['csrf_token'];
}

/**
 * Шорткод для вставки CSRF токена в формы
 *
 * @return string
 */
function atk_ved_csrf_token_shortcode(): string {
	return '<input type="hidden" name="csrf_token" value="' . esc_attr( atk_ved_get_csrf_token() ) . '">';
}
add_shortcode( 'csrf_token', 'atk_ved_csrf_token_shortcode' );

/**
 * Блокировка доступа к важным файлам
 *
 * @return void
 */
function atk_ved_protect_important_files(): void {
	$protected_files = [
		'wp-config.php',
		'.htaccess',
		'error_log',
		'debug.log',
	];

	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$request_uri = $_SERVER['REQUEST_URI'] ?? '';

	foreach ( $protected_files as $file ) {
		if ( strpos( $request_uri, $file ) !== false ) {
			header( 'HTTP/1.0 403 Forbidden' );
			atk_ved_log_security_event( 'Попытка доступа к защищенному файлу', $file );
			exit;
		}
	}
}
add_action( 'init', 'atk_ved_protect_important_files' );

/**
 * Отключение отображения ошибок PHP на продакшене
 *
 * @return void
 */
function atk_ved_disable_php_errors(): void {
	if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
		// phpcs:ignore WordPress.PHP.IniSet.Risky
		ini_set( 'display_errors', 0 );
		// phpcs:ignore WordPress.PHP.IniSet.Risky
		ini_set( 'log_errors', 1 );
		// phpcs:ignore WordPress.PHP.IniSet.Risky
		ini_set( 'error_log', WP_CONTENT_DIR . '/php-errors.log' );
	}
}
add_action( 'init', 'atk_ved_disable_php_errors' );

/**
 * Автоматический выход при неактивности
 *
 * @return void
 */
function atk_ved_auto_logout(): void {
	$timeout = 30 * MINUTE_IN_SECONDS;

	if ( is_user_logged_in() ) {
		$user_id = get_current_user_id();
		$last_activity = get_user_meta( $user_id, 'last_activity', true );

		if ( $last_activity && ( time() - $last_activity ) > $timeout ) {
			wp_logout();
			wp_redirect( home_url() );
			exit;
		}

		update_user_meta( $user_id, 'last_activity', time() );
	}
}
add_action( 'init', 'atk_ved_auto_logout' );

/**
 * WordPress Hardening - дополнительные меры безопасности
 *
 * @return void
 */
function atk_ved_wordpress_hardening(): void {
	// Отключение возможности установки плагинов/тем из админки
	if ( ! defined( 'DISALLOW_FILE_MODS' ) ) {
		define( 'DISALLOW_FILE_MODS', true );
	}

	// Ограничение количества ревизий
	if ( ! defined( 'WP_POST_REVISIONS' ) ) {
		define( 'WP_POST_REVISIONS', 3 );
	}
}
add_action( 'init', 'atk_ved_wordpress_hardening' );
