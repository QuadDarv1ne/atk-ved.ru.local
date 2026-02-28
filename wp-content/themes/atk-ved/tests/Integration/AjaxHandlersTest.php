<?php
/**
 * Integration Tests for AJAX Handlers
 *
 * @package ATK_VED
 * @since 3.6.0
 */

declare(strict_types=1);

namespace ATKVed\Tests\Integration;

use PHPUnit\Framework\TestCase;

class AjaxHandlersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Load WordPress test environment
        require_once dirname(__DIR__) . '/bootstrap.php';
        require_once get_template_directory() . '/inc/contact-form.php';
    }
    
    public function testContactFormWithValidData(): void
    {
        $_POST['contact_nonce'] = wp_create_nonce('atk_contact_form');
        $_POST['name'] = 'Test User';
        $_POST['phone'] = '+7 (999) 123-45-67';
        $_POST['email'] = 'test@example.com';
        $_POST['message'] = 'Test message';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        
        // Mock wp_mail
        add_filter('pre_wp_mail', function() {
            return true;
        });
        
        ob_start();
        try {
            atk_ved_handle_contact_form();
        } catch (\Exception $e) {
            // wp_send_json_* functions call exit, catch it
        }
        $output = ob_get_clean();
        
        $this->assertStringContainsString('success', $output);
    }
    
    public function testContactFormWithInvalidNonce(): void
    {
        $_POST['contact_nonce'] = 'invalid_nonce';
        $_POST['name'] = 'Test User';
        $_POST['phone'] = '+7 (999) 123-45-67';
        
        ob_start();
        try {
            atk_ved_handle_contact_form();
        } catch (\Exception $e) {
            // Expected
        }
        $output = ob_get_clean();
        
        $this->assertStringContainsString('error', $output);
    }
    
    public function testContactFormWithMissingFields(): void
    {
        $_POST['contact_nonce'] = wp_create_nonce('atk_contact_form');
        $_POST['name'] = '';
        $_POST['phone'] = '';
        
        ob_start();
        try {
            atk_ved_handle_contact_form();
        } catch (\Exception $e) {
            // Expected
        }
        $output = ob_get_clean();
        
        $this->assertStringContainsString('error', $output);
    }
    
    public function testNewsletterSubscriptionWithValidEmail(): void
    {
        $_POST['newsletter_nonce'] = wp_create_nonce('atk_newsletter_form');
        $_POST['newsletter_email'] = 'test@example.com';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        
        ob_start();
        try {
            atk_ved_handle_newsletter_form();
        } catch (\Exception $e) {
            // wp_send_json_* functions call exit
        }
        $output = ob_get_clean();
        
        $this->assertStringContainsString('success', $output);
    }
    
    public function testNewsletterSubscriptionWithInvalidEmail(): void
    {
        $_POST['newsletter_nonce'] = wp_create_nonce('atk_newsletter_form');
        $_POST['newsletter_email'] = 'invalid-email';
        
        ob_start();
        try {
            atk_ved_handle_newsletter_form();
        } catch (\Exception $e) {
            // Expected
        }
        $output = ob_get_clean();
        
        $this->assertStringContainsString('error', $output);
    }
    
    public function testRateLimiting(): void
    {
        $_POST['contact_nonce'] = wp_create_nonce('atk_contact_form');
        $_POST['name'] = 'Test User';
        $_POST['phone'] = '+7 (999) 123-45-67';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        
        // Simulate multiple requests
        for ($i = 0; $i < 5; $i++) {
            ob_start();
            try {
                atk_ved_handle_contact_form();
            } catch (\Exception $e) {
                // Expected
            }
            $output = ob_get_clean();
        }
        
        // Last request should be rate limited
        $this->assertStringContainsString('Слишком много попыток', $output);
    }
    
    public function testHoneypotDetection(): void
    {
        $_POST['contact_nonce'] = wp_create_nonce('atk_contact_form');
        $_POST['name'] = 'Test User';
        $_POST['phone'] = '+7 (999) 123-45-67';
        $_POST['website'] = 'http://spam.com'; // Honeypot field
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        
        ob_start();
        try {
            atk_ved_handle_contact_form();
        } catch (\Exception $e) {
            // Expected
        }
        $output = ob_get_clean();
        
        $this->assertStringContainsString('подозрительная активность', $output);
    }
}
