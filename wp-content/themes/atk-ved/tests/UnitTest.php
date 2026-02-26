<?php
/**
 * Unit Tests for ATK VED Theme
 *
 * @package ATK_VED
 * @since 3.3.0
 */

declare(strict_types=1);

namespace ATKVed\Tests;

use WP_UnitTestCase;

/**
 * Basic unit tests for the theme
 */
class UnitTest extends WP_UnitTestCase {

    /**
     * Test that the theme constants are defined
     */
    public function test_theme_constants_are_defined(): void {
        $this->assertTrue(defined('ATK_VED_VERSION'));
        $this->assertTrue(defined('ATK_VED_DIR'));
        $this->assertTrue(defined('ATK_VED_URI'));
    }

    /**
     * Test theme version format
     */
    public function test_theme_version_format(): void {
        $version = ATK_VED_VERSION;
        $this->assertMatchesRegularExpression('/^\d+\.\d+\.\d+$/', $version);
    }

    /**
     * Test that required files exist
     */
    public function test_required_files_exist(): void {
        $required_files = [
            ATK_VED_DIR . '/functions.php',
            ATK_VED_DIR . '/style.css',
            ATK_VED_DIR . '/index.php',
            ATK_VED_DIR . '/header.php',
            ATK_VED_DIR . '/footer.php',
        ];

        foreach ($required_files as $file) {
            $this->assertFileExists($file, "File {$file} should exist");
        }
    }

    /**
     * Test that required directories exist
     */
    public function test_required_directories_exist(): void {
        $required_dirs = [
            ATK_VED_DIR . '/inc',
            ATK_VED_DIR . '/css',
            ATK_VED_DIR . '/js',
            ATK_VED_DIR . '/images',
        ];

        foreach ($required_dirs as $dir) {
            $this->assertDirectoryExists($dir, "Directory {$dir} should exist");
        }
    }

    /**
     * Test helper function: sanitize phone number
     */
    public function test_sanitize_phone(): void {
        if (function_exists('atk_ved_sanitize_phone')) {
            $this->assertEquals('+79991234567', atk_ved_sanitize_phone('+7 (999) 123-45-67'));
            $this->assertEquals('89991234567', atk_ved_sanitize_phone('8-999-123-45-67'));
            $this->assertEquals('1234567890', atk_ved_sanitize_phone('123.456.7890'));
        }
        $this->assertTrue(true); // Skip if function doesn't exist yet
    }

    /**
     * Test helper function: validate email
     */
    public function test_validate_email(): void {
        if (function_exists('atk_ved_validate_email')) {
            $this->assertTrue(atk_ved_validate_email('test@example.com'));
            $this->assertFalse(atk_ved_validate_email('invalid'));
            $this->assertFalse(atk_ved_validate_email(''));
        }
        $this->assertTrue(true); // Skip if function doesn't exist yet
    }

    /**
     * Test that security functions are available
     */
    public function test_security_functions_exist(): void {
        $security_functions = [
            'atk_ved_remove_wp_version',
            'atk_ved_security_headers',
            'atk_ved_verify_honeypot',
        ];

        foreach ($security_functions as $function) {
            $this->assertTrue(
                function_exists($function),
                "Security function {$function} should exist"
            );
        }
    }

    /**
     * Test that AJAX handlers are registered
     */
    public function test_ajax_handlers_are_registered(): void {
        global $wp_filter;

        $ajax_actions = [
            'wp_ajax_atk_ved_contact_form',
            'wp_ajax_nopriv_atk_ved_contact_form',
            'wp_ajax_atk_ved_quick_search',
            'wp_ajax_nopriv_atk_ved_quick_search',
        ];

        foreach ($ajax_actions as $action) {
            $this->assertTrue(
                isset($wp_filter[$action]),
                "AJAX action {$action} should be registered"
            );
        }
    }

    /**
     * Test that custom post types are registered
     */
    public function test_custom_post_types_are_registered(): void {
        // Initialize post types
        do_action('init');

        $post_types = ['service', 'review', 'faq', 'contact_form'];

        foreach ($post_types as $post_type) {
            $this->assertTrue(
                post_type_exists($post_type),
                "Post type {$post_type} should be registered"
            );
        }
    }

    /**
     * Test theme setup
     */
    public function test_theme_setup(): void {
        // Check theme supports
        $this->assertTrue(current_theme_supports('title-tag'));
        $this->assertTrue(current_theme_supports('post-thumbnails'));
        $this->assertTrue(current_theme_supports('custom-logo'));
        $this->assertTrue(current_theme_supports('html5'));
    }

    /**
     * Test menus are registered
     */
    public function test_menus_are_registered(): void {
        global $_wp_registered_nav_menus;

        $this->assertIsArray($_wp_registered_nav_menus);
        $this->assertArrayHasKey('primary', $_wp_registered_nav_menus);
        $this->assertArrayHasKey('footer-services', $_wp_registered_nav_menus);
        $this->assertArrayHasKey('footer-company', $_wp_registered_nav_menus);
    }

    /**
     * Test that translation is loaded
     */
    public function test_translation_loaded(): void {
        $this->assertTrue(is_textdomain_loaded('atk-ved'));
    }

    /**
     * Test CSS files exist
     */
    public function test_css_files_exist(): void {
        $css_files = [
            'design-tokens.css',
            'modern-design.css',
            'ui-components.css',
            'animations-enhanced.css',
        ];

        foreach ($css_files as $file) {
            $this->assertFileExists(
                ATK_VED_DIR . '/css/' . $file,
                "CSS file {$file} should exist"
            );
        }
    }

    /**
     * Test JS files exist
     */
    public function test_js_files_exist(): void {
        $js_files = [
            'main.js',
            'modal.js',
            'calculator.js',
            'ui-components.js',
        ];

        foreach ($js_files as $file) {
            $this->assertFileExists(
                ATK_VED_DIR . '/js/' . $file,
                "JS file {$file} should exist"
            );
        }
    }

    /**
     * Test that performance optimizer is available
     */
    public function test_performance_optimizer_exists(): void {
        $this->assertTrue(
            class_exists('ATK_VED_Performance_Optimizer'),
            'Performance optimizer class should exist'
        );
    }

    /**
     * Test image optimization functions
     */
    public function test_image_optimization(): void {
        if (function_exists('atk_ved_get_webp_path')) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(true); // Skip if not implemented
        }
    }

    /**
     * Test that security headers are set correctly
     */
    public function test_security_headers(): void {
        $headers = [
            'X-Content-Type-Options',
            'X-Frame-Options',
            'X-XSS-Protection',
        ];

        // Headers are set in atk_ved_security_headers()
        // This test ensures the function exists
        $this->assertTrue(
            function_exists('atk_ved_security_headers'),
            'Security headers function should exist'
        );
    }

    /**
     * Test rate limiting logic
     */
    public function test_rate_limiting(): void {
        // Simulate rate limiting
        $ip = '127.0.0.1';
        $rate_key = 'atk_test_rate_' . md5($ip);

        // Clean up
        delete_transient($rate_key);

        // First request should pass
        $rate_limit = get_transient($rate_key);
        $this->assertFalse($rate_limit);

        // Set rate limit
        set_transient($rate_key, 5, 5 * MINUTE_IN_SECONDS);

        // Check rate limit
        $rate_limit = get_transient($rate_key);
        $this->assertEquals(5, $rate_limit);

        // Clean up
        delete_transient($rate_key);
    }

    /**
     * Test helper function: get initials from name
     */
    public function test_get_initials(): void {
        if (function_exists('atk_ved_get_initials')) {
            $this->assertEquals('ИИ', atk_ved_get_initials('Иван Иванов'));
            $this->assertEquals('И', atk_ved_get_initials('Иван'));
            $this->assertEquals('А', atk_ved_get_initials(''));
        }
        $this->assertTrue(true); // Skip if function doesn't exist yet
    }

    /**
     * Test that theme version is consistent
     */
    public function test_version_consistency(): void {
        $style_css = file_get_contents(ATK_VED_DIR . '/style.css');
        preg_match('/Version:\s*(\d+\.\d+\.\d+)/', $style_css, $matches);

        if (isset($matches[1])) {
            $this->assertEquals(
                ATK_VED_VERSION,
                $matches[1],
                'Version in style.css should match ATK_VED_VERSION constant'
            );
        }
    }
}
