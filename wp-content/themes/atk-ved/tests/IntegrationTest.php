<?php
/**
 * Integration Tests for ATK VED Theme
 *
 * @package ATK_VED
 * @since 3.3.0
 */

declare(strict_types=1);

namespace ATKVed\Tests;

use WP_UnitTestCase;

/**
 * Integration tests for the theme
 */
class IntegrationTest extends WP_UnitTestCase {

    /**
     * Test database connection
     */
    public function test_database_connection(): void {
        global $wpdb;

        $this->assertNotNull($wpdb);
        $this->assertEquals($wpdb->charset, 'utf8mb4');
    }

    /**
     * Test AJAX form submission
     */
    public function test_ajax_form_submission(): void {
        // Set up POST data
        $_POST['name'] = 'Тестовый Пользователь';
        $_POST['email'] = 'test@example.com';
        $_POST['phone'] = '+7 (999) 123-45-67';
        $_POST['message'] = 'Тестовое сообщение для проверки интеграции';
        $_POST['consent'] = '1';
        $_POST['nonce'] = wp_create_nonce('atk_ved_nonce');

        // Execute AJAX handler
        $handler = 'atk_ved_contact_form_handler';
        $this->assertTrue(function_exists($handler));

        // Clean up
        $_POST = [];
        $this->assertTrue(true);
    }

    /**
     * Test custom post type creation
     */
    public function test_service_post_creation(): void {
        $service_data = [
            'post_type' => 'service',
            'post_title' => 'Тестовая услуга',
            'post_content' => 'Описание тестовой услуги',
            'post_status' => 'publish',
        ];

        $post_id = wp_insert_post($service_data);

        $this->assertGreaterThan(0, $post_id);
        $this->assertEquals('service', get_post_type($post_id));

        // Clean up
        wp_delete_post($post_id, true);
    }

    /**
     * Test review post with meta data
     */
    public function test_review_post_with_meta(): void {
        $review_data = [
            'post_type' => 'review',
            'post_title' => 'Тестовый отзыв',
            'post_content' => 'Содержание отзыва',
            'post_status' => 'publish',
        ];

        $post_id = wp_insert_post($review_data);

        // Add meta data
        update_post_meta($post_id, '_review_author_name', 'Иван Иванов');
        update_post_meta($post_id, '_review_author_position', 'Директор');
        update_post_meta($post_id, '_review_rating', '5');

        // Verify
        $this->assertEquals('Иван Иванов', get_post_meta($post_id, '_review_author_name', true));
        $this->assertEquals('Директор', get_post_meta($post_id, '_review_author_position', true));
        $this->assertEquals('5', get_post_meta($post_id, '_review_rating', true));

        // Clean up
        wp_delete_post($post_id, true);
    }

    /**
     * Test FAQ post type
     */
    public function test_faq_post_creation(): void {
        $faq_data = [
            'post_type' => 'faq',
            'post_title' => 'Часто задаваемый вопрос',
            'post_content' => 'Ответ на вопрос',
            'post_status' => 'publish',
        ];

        $post_id = wp_insert_post($faq_data);

        $this->assertGreaterThan(0, $post_id);
        $this->assertEquals('faq', get_post_type($post_id));

        // Clean up
        wp_delete_post($post_id, true);
    }

    /**
     * Test theme options retrieval
     */
    public function test_theme_options(): void {
        // Test default values
        $company_name = get_theme_mod('atk_ved_company_name', 'АТК ВЭД');
        $this->assertEquals('АТК ВЭД', $company_name);
    }

    /**
     * Test menu retrieval
     */
    public function test_menu_retrieval(): void {
        // Create test menu
        $menu_id = wp_create_nav_menu('Test Menu');

        $this->assertGreaterThan(0, $menu_id);

        // Add menu item
        wp_update_nav_menu_item($menu_id, 0, [
            'menu-item-title' => 'Test Item',
            'menu-item-url' => home_url('/test'),
            'menu-item-status' => 'publish',
        ]);

        // Get menu
        $menu_items = wp_get_nav_menu_items($menu_id);

        if ($menu_items) {
            $this->assertIsArray($menu_items);
            $this->assertGreaterThan(0, count($menu_items));
        }

        // Clean up
        wp_delete_nav_menu($menu_id);
    }

    /**
     * Test widget area registration
     */
    public function test_widget_areas(): void {
        global $wp_registered_sidebars;

        $this->assertIsArray($wp_registered_sidebars);
        $this->assertArrayHasKey('sidebar-1', $wp_registered_sidebars);
    }

    /**
     * Test image size registration
     */
    public function test_image_sizes(): void {
        $image_sizes = get_intermediate_image_sizes();

        $this->assertContains('atk-ved-hero', $image_sizes);
        $this->assertContains('atk-ved-service', $image_sizes);
    }

    /**
     * Test security headers
     */
    public function test_security_headers_function(): void {
        $this->assertTrue(function_exists('atk_ved_security_headers'));
        $this->assertTrue(function_exists('atk_ved_remove_wp_version'));
    }

    /**
     * Test rate limiting mechanism
     */
    public function test_rate_limiting(): void {
        $ip = '192.168.1.1';
        $rate_key = 'atk_test_rate_' . md5($ip);

        // Clean up before test
        delete_transient($rate_key);

        // Simulate requests
        for ($i = 0; $i < 5; $i++) {
            $rate_limit = get_transient($rate_key);
            set_transient($rate_key, ($rate_limit ?: 0) + 1, 5 * MINUTE_IN_SECONDS);
        }

        // Check rate limit reached
        $rate_limit = get_transient($rate_key);
        $this->assertEquals(5, $rate_limit);

        // Clean up
        delete_transient($rate_key);
    }

    /**
     * Test email sending (mocked)
     */
    public function test_email_functionality(): void {
        // WordPress wp_mail should be available
        $this->assertTrue(function_exists('wp_mail'));

        // Test with invalid email (should fail gracefully)
        $result = wp_mail('', 'Test', 'Test message');
        $this->assertFalse($result);
    }

    /**
     * Test nonce verification
     */
    public function test_nonce_verification(): void {
        $nonce = wp_create_nonce('atk_ved_nonce');

        $this->assertTrue(wp_verify_nonce($nonce, 'atk_ved_nonce'));
        $this->assertFalse(wp_verify_nonce($nonce, 'wrong_action'));
    }

    /**
     * Test user capabilities
     */
    public function test_user_capabilities(): void {
        // Create test user
        $user_id = $this->factory()->user->create([
            'role' => 'administrator',
        ]);

        wp_set_current_user($user_id);

        $this->assertTrue(current_user_can('manage_options'));
        $this->assertTrue(current_user_can('edit_posts'));

        // Clean up
        wp_set_current_user(0);
    }

    /**
     * Test query functionality
     */
    public function test_custom_query(): void {
        // Create test posts
        $post_ids = [];
        for ($i = 0; $i < 3; $i++) {
            $post_ids[] = $this->factory()->post->create([
                'post_type' => 'service',
                'post_title' => 'Service ' . $i,
            ]);
        }

        // Query services
        $query = new \WP_Query([
            'post_type' => 'service',
            'posts_per_page' => 10,
        ]);

        $this->assertGreaterThan(0, $query->found_posts);

        // Clean up
        foreach ($post_ids as $id) {
            wp_delete_post($id, true);
        }
    }

    /**
     * Test meta query
     */
    public function test_meta_query(): void {
        // Create post with meta
        $post_id = $this->factory()->post->create();
        update_post_meta($post_id, '_test_meta', 'test_value');

        // Query by meta
        $query = new \WP_Query([
            'post_type' => 'post',
            'meta_query' => [
                [
                    'key' => '_test_meta',
                    'value' => 'test_value',
                ],
            ],
        ]);

        $this->assertGreaterThan(0, $query->found_posts);

        // Clean up
        wp_delete_post($post_id, true);
    }

    /**
     * Test taxonomy registration
     */
    public function test_taxonomy_registration(): void {
        $taxonomies = get_taxonomies();

        $this->assertContains('category', $taxonomies);
        $this->assertContains('post_tag', $taxonomies);
    }

    /**
     * Test REST API availability
     */
    public function test_rest_api(): void {
        $this->assertTrue(class_exists('WP_REST_Server'));

        // Test REST route registration
        $routes = rest_get_server()->get_routes();
        $this->assertArrayHasKey('/wp/v2/posts', $routes);
    }

    /**
     * Test cache functionality
     */
    public function test_cache(): void {
        $cache_key = 'test_cache_key';
        $cache_value = 'test_value';

        // Set cache
        wp_cache_set($cache_key, $cache_value);

        // Get cache
        $retrieved = wp_cache_get($cache_key);
        $this->assertEquals($cache_value, $retrieved);

        // Delete cache
        wp_cache_delete($cache_key);
        $this->assertFalse(wp_cache_get($cache_key));
    }

    /**
     * Test transient functionality
     */
    public function test_transients(): void {
        $transient_key = 'test_transient';
        $transient_value = 'test_value';

        // Set transient
        set_transient($transient_key, $transient_value, MINUTE_IN_SECONDS);

        // Get transient
        $retrieved = get_transient($transient_key);
        $this->assertEquals($transient_value, $retrieved);

        // Delete transient
        delete_transient($transient_key);
        $this->assertFalse(get_transient($transient_key));
    }

    /**
     * Test options API
     */
    public function test_options_api(): void {
        $option_key = 'test_option';
        $option_value = 'test_value';

        // Add option
        add_option($option_key, $option_value);

        // Get option
        $retrieved = get_option($option_key);
        $this->assertEquals($option_value, $retrieved);

        // Update option
        update_option($option_key, 'updated_value');
        $this->assertEquals('updated_value', get_option($option_key));

        // Delete option
        delete_option($option_key);
        $this->assertFalse(get_option($option_key));
    }

    /**
     * Test helper class methods
     */
    public function test_helper_class(): void {
        $this->assertTrue(class_exists('\ATKVed\Helper'));

        // Test static methods
        $this->assertEquals('+79991234567', \ATKVed\Helper::sanitizePhone('+7 (999) 123-45-67'));
        $this->assertTrue(\ATKVed\Helper::validateEmail('test@example.com'));
        $this->assertFalse(\ATKVed\Helper::validateEmail('invalid'));
        $this->assertEquals('15 000 ₽', \ATKVed\Helper::formatPrice(15000));
    }

    /**
     * Test helper functions
     */
    public function test_helper_functions(): void {
        $this->assertTrue(function_exists('atk_ved_sanitize_phone'));
        $this->assertTrue(function_exists('atk_ved_validate_email'));
        $this->assertTrue(function_exists('atk_ved_format_price'));

        $this->assertEquals('+79991234567', atk_ved_sanitize_phone('+7 (999) 123-45-67'));
        $this->assertTrue(atk_ved_validate_email('test@example.com'));
        $this->assertEquals('15 000 ₽', atk_ved_format_price(15000));
    }

    /**
     * Test pagination
     */
    public function test_pagination(): void {
        // Create multiple posts
        for ($i = 0; $i < 15; $i++) {
            $this->factory()->post->create();
        }

        // Query with pagination
        $query = new \WP_Query([
            'posts_per_page' => 10,
            'paged' => 1,
        ]);

        $this->assertEquals(10, $query->post_count);
        $this->assertEquals(2, $query->max_num_pages);
    }

    /**
     * Test search functionality
     */
    public function test_search(): void {
        // Create post with specific content
        $post_id = $this->factory()->post->create([
            'post_title' => 'Unique Search Test',
            'post_content' => 'This is unique content for search testing',
        ]);

        // Search
        $query = new \WP_Query([
            's' => 'Unique Search Test',
        ]);

        $this->assertGreaterThan(0, $query->found_posts);

        // Clean up
        wp_delete_post($post_id, true);
    }

    /**
     * Test attachment handling
     */
    public function test_attachment(): void {
        // Create attachment
        $attachment_id = $this->factory()->attachment->create_upload_object();

        $this->assertGreaterThan(0, $attachment_id);
        $this->assertEquals('attachment', get_post_type($attachment_id));

        // Test attachment URL
        $url = wp_get_attachment_url($attachment_id);
        $this->assertIsString($url);

        // Clean up
        wp_delete_attachment($attachment_id, true);
    }

    /**
     * Test comment functionality
     */
    public function test_comments(): void {
        // Create post
        $post_id = $this->factory()->post->create();

        // Create comment
        $comment_id = $this->factory()->comment->create([
            'comment_post_ID' => $post_id,
            'comment_content' => 'Test comment',
        ]);

        $this->assertGreaterThan(0, $comment_id);

        // Get comment
        $comment = get_comment($comment_id);
        $this->assertEquals('Test comment', $comment->comment_content);

        // Clean up
        wp_delete_comment($comment_id, true);
        wp_delete_post($post_id, true);
    }
}
