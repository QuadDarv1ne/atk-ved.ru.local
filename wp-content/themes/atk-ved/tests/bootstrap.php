<?php
/**
 * PHPUnit Bootstrap Configuration
 *
 * @package ATKVed
 */

declare( strict_types=1 );

// Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// WordPress test environment
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', dirname( __DIR__, 3 ) . '/' );
}

// Theme constants
define( 'ATK_VED_VERSION', '3.3.0' );
define( 'ATK_VED_DIR', __DIR__ . '/..' );
define( 'ATK_VED_URI', 'http://example.org/wp-content/themes/atk-ved' );

// Load theme functions
require_once ATK_VED_DIR . '/functions.php';
