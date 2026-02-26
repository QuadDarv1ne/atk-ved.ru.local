<?php
/**
 * Example PHPUnit Test
 *
 * @package ATKVed
 */

declare( strict_types=1 );

namespace ATKVed\Tests;

use WP_UnitTestCase;

/**
 * Sample test cases
 */
class SampleTest extends WP_UnitTestCase {

    /**
     * Test that true is true
     */
    public function test_true_is_true(): void {
        $this->assertTrue( true );
    }

    /**
     * Test theme version constant
     */
    public function test_theme_version(): void {
        $this->assertEquals( '3.3.0', ATK_VED_VERSION );
    }

    /**
     * Test helper function exists
     */
    public function test_helper_function_exists(): void {
        $this->assertTrue( function_exists( 'atk_ved_pluralize' ) );
    }

    /**
     * Test pluralize function
     */
    public function test_pluralize(): void {
        $this->assertEquals( 'год', atk_ved_pluralize( 1, 'год', 'года', 'лет' ) );
        $this->assertEquals( 'года', atk_ved_pluralize( 2, 'год', 'года', 'лет' ) );
        $this->assertEquals( 'лет', atk_ved_pluralize( 5, 'год', 'года', 'лет' ) );
        $this->assertEquals( 'лет', atk_ved_pluralize( 11, 'год', 'года', 'лет' ) );
    }
}
