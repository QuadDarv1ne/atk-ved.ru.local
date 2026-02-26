<?php
/**
 * Theme Version Configuration
 *
 * Single source of truth for ATK VED theme version
 *
 * @package ATK_VED
 * @since 3.3.0
 */

declare(strict_types=1);

// =============================================================================
// THEME VERSION - SINGLE SOURCE OF TRUTH
// =============================================================================

/**
 * Current theme version
 *
 * Format: MAJOR.MINOR.PATCH
 * - MAJOR: Breaking changes
 * - MINOR: New features (backward compatible)
 * - PATCH: Bug fixes (backward compatible)
 */
if ( ! defined( 'ATK_VED_VERSION' ) ) {
	define( 'ATK_VED_VERSION', '3.3.0' );
}

/**
 * Version history
 */
if ( ! defined( 'ATK_VED_VERSION_HISTORY' ) ) {
	define( 'ATK_VED_VERSION_HISTORY', [
		'3.3.0' => '2026-02-26 — Полное улучшение проекта (тесты, Docker, CI/CD)',
		'3.2.0' => '2026-02-26 — UI/UX Улучшения',
		'3.1.0' => '2026-02-25 — Оптимизация производительности',
		'3.0.0' => '2026-02-20 — Мажорное обновление',
	] );
}

/**
 * Minimum requirements
 */
if ( ! defined( 'ATK_VED_MIN_PHP_VERSION' ) ) {
	define( 'ATK_VED_MIN_PHP_VERSION', '8.1' );
}
if ( ! defined( 'ATK_VED_MIN_WP_VERSION' ) ) {
	define( 'ATK_VED_MIN_WP_VERSION', '6.0' );
}
if ( ! defined( 'ATK_VED_MIN_MYSQL_VERSION' ) ) {
	define( 'ATK_VED_MIN_MYSQL_VERSION', '5.7' );
}

/**
 * Theme directories and URLs
 */
if (!defined('ATK_VED_DIR')) {
    define('ATK_VED_DIR', function_exists('get_template_directory') ? get_template_directory() : __DIR__);
}
if (!defined('ATK_VED_URI')) {
    define('ATK_VED_URI', function_exists('get_template_directory_uri') ? get_template_directory_uri() : '');
}

/**
 * Check if version matches
 *
 * @param string $version Version to check
 * @return bool True if version matches current version
 */
if (!function_exists('atk_ved_is_version')):
function atk_ved_is_version(string $version): bool
{
    return ATK_VED_VERSION === $version;
}
endif;

/**
 * Compare versions
 *
 * @param string $version Version to compare
 * @param string $operator Comparison operator (>, <, >=, <=, =, !=)
 * @return bool Result of comparison
 */
if (!function_exists('atk_ved_version_compare')):
function atk_ved_version_compare(string $version, string $operator = '>='): bool
{
    return version_compare(ATK_VED_VERSION, $version, $operator);
}
endif;

/**
 * Get version information
 *
 * @return array Version information
 */
if (!function_exists('atk_ved_get_version_info')):
function atk_ved_get_version_info(): array
{
    return [
        'version' => ATK_VED_VERSION,
        'min_php' => ATK_VED_MIN_PHP_VERSION,
        'min_wp' => ATK_VED_MIN_WP_VERSION,
        'history' => ATK_VED_VERSION_HISTORY,
    ];
}
endif;
