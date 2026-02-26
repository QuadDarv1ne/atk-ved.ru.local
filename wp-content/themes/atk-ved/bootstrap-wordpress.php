<?php
/**
 * Bootstrap для phpstan - имитирует WordPress константы и функции
 */

// Константы WordPress
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__DIR__, 4) . '/');
}

if (!defined('WP_CONTENT_DIR')) {
    define('WP_CONTENT_DIR', dirname(__DIR__, 3) . '/wp-content');
}

if (!defined('DAY_IN_SECONDS')) {
    define('DAY_IN_SECONDS', 86400);
}

if (!defined('HOUR_IN_SECONDS')) {
    define('HOUR_IN_SECONDS', 3600);
}

if (!defined('MINUTE_IN_SECONDS')) {
    define('MINUTE_IN_SECONDS', 60);
}

// Stub функции WordPress
if (!function_exists('esc_html')) {
    function esc_html($text) { return $text; }
}
if (!function_exists('esc_attr')) {
    function esc_attr($text) { return $text; }
}
if (!function_exists('esc_url')) {
    function esc_url($url) { return $url; }
}
if (!function_exists('esc_html__')) {
    function esc_html__($text, $domain) { return $text; }
}
if (!function_exists('esc_attr__')) {
    function esc_attr__($text, $domain) { return $text; }
}
if (!function_exists('__')) {
    function __($text, $domain) { return $text; }
}
if (!function_exists('_e')) {
    function _e($text, $domain) { echo $text; }
}
if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($text) { return $text; }
}
if (!function_exists('sanitize_email')) {
    function sanitize_email($email) { return $email; }
}
if (!function_exists('sanitize_key')) {
    function sanitize_key($key) { return $key; }
}
if (!function_exists('wp_enqueue_style')) {}
if (!function_exists('wp_enqueue_script')) {}
if (!function_exists('wp_localize_script')) {}
if (!function_exists('add_theme_support')) {}
if (!function_exists('register_nav_menus')) {}
if (!function_exists('register_sidebar')) {}
if (!function_exists('add_action')) {}
if (!function_exists('add_filter')) {}
if (!function_exists('add_shortcode')) {}
if (!function_exists('get_template_directory')) {
    function get_template_directory() { return dirname(__DIR__); }
}
if (!function_exists('get_template_directory_uri')) {
    function get_template_directory_uri() { return ''; }
}
if (!function_exists('get_stylesheet_uri')) {
    function get_stylesheet_uri() { return ''; }
}
if (!function_exists('get_post')) {
    function get_post($id = null) { return null; }
}
if (!function_exists('get_theme_mod')) {
    function get_theme_mod($name, $default = false) { return $default; }
}
if (!function_exists('admin_url')) {
    function admin_url($path = '', $scheme = 'admin') { return ''; }
}
if (!function_exists('home_url')) {
    function home_url($path = '', $scheme = 'https') { return ''; }
}
if (!function_exists('wp_create_nonce')) {
    function wp_create_nonce($action = -1) { return ''; }
}
if (!function_exists('check_ajax_referer')) {
    function check_ajax_referer($action = -1, $query_arg = false, $die = true) { return true; }
}
if (!function_exists('wp_send_json_success')) {
    function wp_send_json_success($data = null, $status_code = 200) {}
}
if (!function_exists('wp_send_json_error')) {
    function wp_send_json_error($data = null, $status_code = 400) {}
}
if (!function_exists('get_transient')) {
    function get_transient($transient) { return false; }
}
if (!function_exists('set_transient')) {
    function set_transient($transient, $value, $expiration = 0) { return true; }
}
if (!function_exists('get_option')) {
    function get_option($option, $default = false) { return $default; }
}
if (!function_exists('update_option')) {
    function update_option($option, $value, $autoload = null) { return true; }
}
if (!function_exists('is_front_page')) {
    function is_front_page() { return false; }
}
if (!function_exists('is_page')) {
    function is_page($page = '') { return false; }
}
if (!function_exists('is_404')) {
    function is_404() { return false; }
}
if (!function_exists('is_admin')) {
    function is_admin() { return false; }
}
if (!function_exists('has_shortcode')) {
    function has_shortcode($content, $tag) { return false; }
}
if (!function_exists('do_action')) {}
if (!function_exists('apply_filters')) {}

// Stub классы
class WP_Customize_Manager {
    public function add_setting() {}
    public function add_control() {}
    public function add_section() {}
}
class WP_Error {}
class WP_Post {}
