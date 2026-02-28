<?php
/**
 * Unit Tests for Helper Functions
 *
 * @package ATK_VED
 * @since 3.6.0
 */

declare(strict_types=1);

namespace ATKVed\Tests\Unit;

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        require_once dirname(__DIR__) . '/bootstrap.php';
        require_once get_template_directory() . '/inc/helpers.php';
    }
    
    public function testSanitizePhone(): void
    {
        $phone = '+7 (999) 123-45-67';
        $sanitized = atk_ved_sanitize_phone($phone);
        
        $this->assertEquals('+79991234567', $sanitized);
    }
    
    public function testValidateEmail(): void
    {
        $this->assertTrue(atk_ved_validate_email('test@example.com'));
        $this->assertFalse(atk_ved_validate_email('invalid-email'));
        $this->assertFalse(atk_ved_validate_email(''));
    }
    
    public function testGetInitials(): void
    {
        $this->assertEquals('ИИ', atk_ved_get_initials('Иван Иванов'));
        $this->assertEquals('ИИП', atk_ved_get_initials('Иван Иванович Петров'));
        $this->assertEquals('А', atk_ved_get_initials(''));
    }
    
    public function testFormatPrice(): void
    {
        $this->assertEquals('1 000 ₽', atk_ved_format_price(1000));
        $this->assertEquals('1 234 567 ₽', atk_ved_format_price(1234567));
        $this->assertEquals('100 $', atk_ved_format_price(100, '$'));
    }
    
    public function testIsSafeUrl(): void
    {
        $this->assertTrue(atk_ved_is_safe_url('https://example.com'));
        $this->assertTrue(atk_ved_is_safe_url('http://example.com'));
        $this->assertFalse(atk_ved_is_safe_url('javascript:alert(1)'));
        $this->assertFalse(atk_ved_is_safe_url(''));
        $this->assertFalse(atk_ved_is_safe_url('ftp://example.com'));
    }
    
    public function testTrimString(): void
    {
        $string = 'This is a long string that needs to be trimmed';
        $trimmed = atk_ved_trim_string($string, 20);
        
        $this->assertEquals('This is a long st...', $trimmed);
        $this->assertEquals(20, mb_strlen($trimmed));
    }
    
    public function testHumanFileSize(): void
    {
        $this->assertEquals('1 KB', atk_ved_human_file_size(1024));
        $this->assertEquals('1 MB', atk_ved_human_file_size(1048576));
        $this->assertEquals('1.5 MB', atk_ved_human_file_size(1572864));
    }
    
    public function testIsJson(): void
    {
        $this->assertTrue(atk_ved_is_json('{"key":"value"}'));
        $this->assertTrue(atk_ved_is_json('[]'));
        $this->assertFalse(atk_ved_is_json('not json'));
        $this->assertFalse(atk_ved_is_json(''));
    }
    
    public function testTransliterate(): void
    {
        $this->assertEquals('Privet mir', atk_ved_transliterate('Привет мир'));
        $this->assertEquals('Moskva', atk_ved_transliterate('Москва'));
    }
    
    public function testCreateSlug(): void
    {
        $this->assertEquals('privet-mir', atk_ved_create_slug('Привет мир'));
        $this->assertEquals('test-slug-123', atk_ved_create_slug('Test Slug 123'));
        $this->assertEquals('test', atk_ved_create_slug('Test!!!'));
    }
    
    public function testCheckRateLimit(): void
    {
        // First 3 attempts should pass
        $this->assertTrue(atk_ved_check_rate_limit('test_form', 3, 60));
        $this->assertTrue(atk_ved_check_rate_limit('test_form', 3, 60));
        $this->assertTrue(atk_ved_check_rate_limit('test_form', 3, 60));
        
        // 4th attempt should fail
        $this->assertFalse(atk_ved_check_rate_limit('test_form', 3, 60));
    }
    
    public function testCheckHoneypot(): void
    {
        $_POST['website'] = '';
        $this->assertTrue(atk_ved_check_honeypot('website'));
        
        $_POST['website'] = 'spam';
        $this->assertFalse(atk_ved_check_honeypot('website'));
    }
}
