<?php
/**
 * Advanced Security - Расширенные меры безопасности
 *
 * @package ATK_VED
 * @since 3.2.0
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Класс для расширенной безопасности
 */
class ATK_VED_Advanced_Security {

    private static ?self $instance = null;

    public static function get_instance(): self {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Установка заголовков безопасности
        add_action( 'wp_headers', [ $this, 'set_security_headers' ] );
        add_action( 'send_headers', [ $this, 'send_security_headers' ] );
        
        // Дополнительная защита от XSS
        add_filter( 'wp_kses_allowed_html', [ $this, 'enhance_kses_config' ], 10, 2 );
        
        // Защита от CSRF
        add_action( 'wp_ajax_nopriv_atk_verify_nonce', [ $this, 'verify_nonce_callback' ] );
        add_action( 'wp_ajax_atk_verify_nonce', [ $this, 'verify_nonce_callback' ] );
        
        // Защита от brute force атак
        add_action( 'wp_login_failed', [ $this, 'log_failed_login_attempts' ] );
        add_filter( 'authenticate', [ $this, 'check_brute_force_attack' ], 99 );
        
        // Безопасность файлов
        add_filter( 'wp_handle_upload_prefilter', [ $this, 'secure_file_upload' ] );
        
        // Защита от clickjacking
        add_action( 'wp_headers', [ $this, 'add_frame_options_header' ] );
    }

    /**
     * Установка заголовков безопасности
     */
    public function set_security_headers( array $headers ): array {
        // Content Security Policy
        $csp_directives = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.google-analytics.com https://www.googletagmanager.com https://connect.facebook.net",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com",
            "img-src 'self' data: https: blob:",
            "connect-src 'self' https://www.google-analytics.com https://stats.g.doubleclick.net",
            "frame-src 'self' https://www.youtube.com https://player.vimeo.com",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
        ];

        $headers['Content-Security-Policy'] = implode( '; ', $csp_directives );
        
        // Strict Transport Security
        $headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains; preload';
        
        // X-Content-Type-Options
        $headers['X-Content-Type-Options'] = 'nosniff';
        
        // X-Frame-Options
        $headers['X-Frame-Options'] = 'SAMEORIGIN';
        
        // Referrer Policy
        $headers['Referrer-Policy'] = 'strict-origin-when-cross-origin';
        
        // Permissions Policy
        $headers['Permissions-Policy'] = 'geolocation=(), microphone=(), camera=()';
        
        return $headers;
    }

    /**
     * Отправка заголовков безопасности
     */
    public function send_security_headers(): void {
        if ( ! headers_sent() ) {
            // Content Security Policy
            $csp_directives = [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.google-analytics.com https://www.googletagmanager.com https://connect.facebook.net",
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
                "font-src 'self' https://fonts.gstatic.com",
                "img-src 'self' data: https: blob:",
                "connect-src 'self' https://www.google-analytics.com https://stats.g.doubleclick.net",
                "frame-src 'self' https://www.youtube.com https://player.vimeo.com",
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self'",
                "frame-ancestors 'self'",
            ];

            header( 'Content-Security-Policy: ' . implode( '; ', $csp_directives ), false );
            
            // Strict Transport Security
            header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains; preload', false );
            
            // X-Content-Type-Options
            header( 'X-Content-Type-Options: nosniff', false );
            
            // X-Frame-Options
            header( 'X-Frame-Options: SAMEORIGIN', false );
            
            // Referrer Policy
            header( 'Referrer-Policy: strict-origin-when-cross-origin', false );
            
            // Permissions Policy
            header( 'Permissions-Policy: geolocation=(), microphone=(), camera=()', false );
        }
    }

    /**
     * Усиление конфигурации KSES
     */
    public function enhance_kses_config( array $allowed, string $context ): array {
        if ( 'post' === $context ) {
            // Добавляем безопасные атрибуты для SVG
            $allowed['svg'] = [
                'class' => true,
                'id' => true,
                'style' => true,
                'viewBox' => true,
                'xmlns' => true,
                'width' => true,
                'height' => true,
                'fill' => true,
                'stroke' => true,
                'stroke-width' => true,
                'stroke-linecap' => true,
                'stroke-linejoin' => true,
            ];
            
            $allowed['path'] = [
                'd' => true,
                'fill' => true,
                'stroke' => true,
                'stroke-width' => true,
            ];
            
            $allowed['circle'] = [
                'cx' => true,
                'cy' => true,
                'r' => true,
                'fill' => true,
                'stroke' => true,
                'stroke-width' => true,
            ];
            
            $allowed['rect'] = [
                'x' => true,
                'y' => true,
                'width' => true,
                'height' => true,
                'rx' => true,
                'ry' => true,
                'fill' => true,
                'stroke' => true,
                'stroke-width' => true,
            ];
        }
        
        return $allowed;
    }

    /**
     * Проверка CSRF nonce
     */
    public function verify_nonce_callback(): void {
        $nonce = sanitize_text_field( $_POST['nonce'] ?? '' );
        $action = sanitize_text_field( $_POST['action_name'] ?? '' );
        
        if ( ! wp_verify_nonce( $nonce, $action ) ) {
            wp_send_json_error( 'Invalid nonce' );
        }
        
        wp_send_json_success();
    }

    /**
     * Логирование неудачных попыток входа
     */
    public function log_failed_login_attempts( string $username ): void {
        $ip = $this->get_client_ip();
        $timestamp = time();
        
        // Сохраняем попытку в transient
        $attempts = get_transient( "failed_login_attempts_$ip" ) ?: [];
        $attempts[] = [
            'timestamp' => $timestamp,
            'username' => $username
        ];
        
        // Удаляем старые попытки (старше 15 минут)
        $attempts = array_filter( $attempts, function( $attempt ) {
            return ( time() - $attempt['timestamp'] ) < 900; // 15 минут
        } );
        
        set_transient( "failed_login_attempts_$ip", $attempts, 900 );
    }

    /**
     * Проверка на brute force атаку
     */
    public function check_brute_force_attack( $user ): ?WP_Error {
        if ( is_wp_error( $user ) ) {
            return $user;
        }
        
        $ip = $this->get_client_ip();
        $attempts = get_transient( "failed_login_attempts_$ip" ) ?: [];
        
        // Если больше 5 неудачных попыток за 15 минут - блокируем
        if ( count( $attempts ) >= 5 ) {
            return new WP_Error( 
                'too_many_attempts', 
                'Слишком много попыток входа. Пожалуйста, повторите попытку позже.' 
            );
        }
        
        return $user;
    }

    /**
     * Безопасная загрузка файлов
     */
    public function secure_file_upload( array $file ): array {
        $allowed_types = [
            'jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx'
        ];
        
        $file_ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
        
        if ( ! in_array( $file_ext, $allowed_types ) ) {
            $file['error'] = 'Недопустимый тип файла. Разрешены только: ' . implode( ', ', $allowed_types );
        }
        
        // Проверяем размер файла (максимум 5MB)
        if ( $file['size'] > 5 * 1024 * 1024 ) {
            $file['error'] = 'Файл слишком большой. Максимальный размер 5MB.';
        }
        
        return $file;
    }

    /**
     * Добавление заголовка Frame Options
     */
    public function add_frame_options_header( array $headers ): array {
        $headers['X-Frame-Options'] = 'SAMEORIGIN';
        return $headers;
    }

    /**
     * Получение IP-адреса клиента
     */
    private function get_client_ip(): string {
        $ip_keys = [
            'HTTP_CF_CONNECTING_IP',    // Cloudflare
            'HTTP_X_FORWARDED_FOR',     // Load balancer
            'HTTP_X_REAL_IP',           // Reverse proxy
            'HTTP_CLIENT_IP',           // Proxy
            'REMOTE_ADDR',              // Standard
        ];
        
        foreach ( $ip_keys as $key ) {
            if ( ! empty( $_SERVER[ $key ] ) ) {
                $ips = explode( ',', $_SERVER[ $key ] );
                return trim( $ips[0] );
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}

// Инициализация
function atk_ved_init_advanced_security(): void {
    ATK_VED_Advanced_Security::get_instance();
}
add_action( 'after_setup_theme', 'atk_ved_init_advanced_security' );