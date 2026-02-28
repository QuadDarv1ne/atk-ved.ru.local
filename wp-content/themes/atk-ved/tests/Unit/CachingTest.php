<?php
/**
 * Unit Tests for Caching Functions
 *
 * @package ATK_VED
 * @since 3.6.0
 */

declare(strict_types=1);

namespace ATKVed\Tests\Unit;

use PHPUnit\Framework\TestCase;

class CachingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Load WordPress test environment
        require_once dirname(__DIR__) . '/bootstrap.php';
        require_once get_template_directory() . '/inc/caching.php';
    }
    
    public function testGetCachedPosts(): void
    {
        $args = [
            'post_type' => 'post',
            'posts_per_page' => 5,
        ];
        
        $posts = atk_ved_get_cached_posts($args);
        
        $this->assertIsArray($posts);
    }
    
    public function testGetCachedPostMeta(): void
    {
        $post_id = 1;
        $meta_key = '_test_meta';
        
        // Set test meta
        update_post_meta($post_id, $meta_key, 'test_value');
        
        $value = atk_ved_get_cached_post_meta($post_id, $meta_key);
        
        $this->assertEquals('test_value', $value);
        
        // Clean up
        delete_post_meta($post_id, $meta_key);
    }
    
    public function testClearPostCache(): void
    {
        $post_id = 1;
        
        // This should not throw any errors
        atk_ved_clear_post_cache($post_id);
        
        $this->assertTrue(true);
    }
    
    public function testGetCachedThemeMod(): void
    {
        $option = 'test_option';
        $default = 'default_value';
        
        set_theme_mod($option, 'test_value');
        
        $value = atk_ved_get_cached_theme_mod($option, $default);
        
        $this->assertEquals('test_value', $value);
        
        // Clean up
        remove_theme_mod($option);
    }
}
