<?php
/**
 * Preservation Property Tests - Footer Menu Behavior
 * 
 * **Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.5**
 * 
 * IMPORTANT: These tests run on UNFIXED code to establish baseline behavior.
 * They MUST PASS on unfixed code to confirm what behavior needs to be preserved.
 * 
 * Property 2: Preservation - Non-Primary Menu Behavior
 * For any menu render request where the theme location is NOT 'primary',
 * the fixed code SHALL produce exactly the same rendering behavior as the original code.
 * 
 * Test Strategy:
 * 1. Observe footer-services menu rendering on unfixed code
 * 2. Observe footer-company menu rendering on unfixed code
 * 3. Observe unassigned menu location fallback behavior
 * 4. Observe menu caching behavior for footer menus
 * 5. Document baseline behavior patterns
 * 
 * @package ATK_VED
 */

declare(strict_types=1);

// Load WordPress
$wp_load_path = dirname(__DIR__, 4) . '/wp-load.php';
if (!file_exists($wp_load_path)) {
    die("Error: Cannot find wp-load.php at: {$wp_load_path}\n");
}
require_once $wp_load_path;

// Test configuration
const TEST_TIMEOUT = 3; // seconds
const CACHE_GROUP = 'atk_ved_menus';

/**
 * Test Result Tracker
 */
class TestResults {
    private array $tests = [];
    private int $passed = 0;
    private int $failed = 0;
    
    public function add(string $name, bool $passed, string $message = ''): void {
        $this->tests[] = [
            'name' => $name,
            'passed' => $passed,
            'message' => $message
        ];
        
        if ($passed) {
            $this->passed++;
        } else {
            $this->failed++;
        }
    }
    
    public function summary(): void {
        echo "\n=== Test Summary ===\n";
        echo "Total: " . count($this->tests) . "\n";
        echo "Passed: {$this->passed}\n";
        echo "Failed: {$this->failed}\n\n";
        
        foreach ($this->tests as $test) {
            $status = $test['passed'] ? '✓ PASS' : '✗ FAIL';
            echo "{$status}: {$test['name']}\n";
            if (!empty($test['message'])) {
                echo "  → {$test['message']}\n";
            }
        }
    }
    
    public function allPassed(): bool {
        return $this->failed === 0;
    }
}

$results = new TestResults();

/**
 * Helper: Render menu with timeout protection
 */
function render_menu_with_timeout(array $args, int $timeout = TEST_TIMEOUT): array {
    $start_time = microtime(true);
    
    ob_start();
    $menu_output = wp_nav_menu(array_merge($args, ['echo' => false]));
    ob_end_clean();
    
    $elapsed = microtime(true) - $start_time;
    
    return [
        'output' => $menu_output,
        'elapsed' => $elapsed,
        'timed_out' => $elapsed >= $timeout,
        'is_valid_html' => !empty($menu_output) && (strpos($menu_output, '<') !== false)
    ];
}

/**
 * Helper: Setup test menus
 */
function setup_test_menus(): array {
    $menu_ids = [];
    
    // Delete any existing test menus first
    $existing_menus = wp_get_nav_menus();
    foreach ($existing_menus as $menu) {
        if (strpos($menu->name, 'Test Footer') !== false) {
            wp_delete_nav_menu($menu->term_id);
            echo "Deleted existing test menu: {$menu->name} (ID: {$menu->term_id})\n";
        }
    }
    
    // Create footer-services menu with unique name
    $services_menu_name = 'Test Footer Services ' . uniqid();
    $services_menu_id = wp_create_nav_menu($services_menu_name);
    if (!is_wp_error($services_menu_id)) {
        $item1_id = wp_update_nav_menu_item($services_menu_id, 0, [
            'menu-item-title' => 'Service 1',
            'menu-item-url' => home_url('/service-1/'),
            'menu-item-status' => 'publish',
            'menu-item-type' => 'custom'
        ]);
        $item2_id = wp_update_nav_menu_item($services_menu_id, 0, [
            'menu-item-title' => 'Service 2',
            'menu-item-url' => home_url('/service-2/'),
            'menu-item-status' => 'publish',
            'menu-item-type' => 'custom'
        ]);
        
        echo "Created footer-services menu (ID: {$services_menu_id}) with items: {$item1_id}, {$item2_id}\n";
        
        $locations = get_theme_mod('nav_menu_locations', []);
        $locations['footer-services'] = $services_menu_id;
        set_theme_mod('nav_menu_locations', $locations);
        
        $menu_ids['footer-services'] = $services_menu_id;
    } else {
        echo "Error creating footer-services menu: " . $services_menu_id->get_error_message() . "\n";
    }
    
    // Create footer-company menu with unique name
    $company_menu_name = 'Test Footer Company ' . uniqid();
    $company_menu_id = wp_create_nav_menu($company_menu_name);
    if (!is_wp_error($company_menu_id)) {
        $item1_id = wp_update_nav_menu_item($company_menu_id, 0, [
            'menu-item-title' => 'About Us',
            'menu-item-url' => home_url('/about/'),
            'menu-item-status' => 'publish',
            'menu-item-type' => 'custom'
        ]);
        $item2_id = wp_update_nav_menu_item($company_menu_id, 0, [
            'menu-item-title' => 'Contact',
            'menu-item-url' => home_url('/contact/'),
            'menu-item-status' => 'publish',
            'menu-item-type' => 'custom'
        ]);
        
        echo "Created footer-company menu (ID: {$company_menu_id}) with items: {$item1_id}, {$item2_id}\n";
        
        $locations = get_theme_mod('nav_menu_locations', []);
        $locations['footer-company'] = $company_menu_id;
        set_theme_mod('nav_menu_locations', $locations);
        
        $menu_ids['footer-company'] = $company_menu_id;
    } else {
        echo "Error creating footer-company menu: " . $company_menu_id->get_error_message() . "\n";
    }
    
    return $menu_ids;
}

/**
 * Helper: Cleanup test menus
 */
function cleanup_test_menus(array $menu_ids): void {
    foreach ($menu_ids as $location => $menu_id) {
        wp_delete_nav_menu($menu_id);
    }
    
    // Clear menu locations
    $locations = get_theme_mod('nav_menu_locations', []);
    unset($locations['footer-services'], $locations['footer-company']);
    set_theme_mod('nav_menu_locations', $locations);
    
    // Clear cache
    wp_cache_flush_group(CACHE_GROUP);
}

echo "=== Preservation Property Tests - Footer Menu Behavior ===\n";
echo "Property 2: Non-Primary Menu Behavior\n";
echo "Running on: UNFIXED code\n";
echo "Expected: All tests PASS (confirms baseline behavior to preserve)\n\n";

// Setup test menus
echo "Setting up test menus...\n";
$menu_ids = setup_test_menus();
echo "Created menus: " . implode(', ', array_keys($menu_ids)) . "\n\n";

/**
 * Test 1: Footer Services Menu Renders Correctly
 * Requirement 3.1: footer-services menu SHALL CONTINUE TO render correctly
 */
echo "Test 1: Footer Services Menu Rendering\n";
echo "----------------------------------------\n";

// Debug: Check what menu is assigned
$locations = get_nav_menu_locations();
echo "Assigned menu ID: " . ($locations['footer-services'] ?? 'NONE') . "\n";
if (isset($locations['footer-services'])) {
    $menu_items = wp_get_nav_menu_items($locations['footer-services']);
    echo "Menu items count: " . (is_array($menu_items) ? count($menu_items) : 0) . "\n";
}

$result = render_menu_with_timeout([
    'theme_location' => 'footer-services',
    'container' => 'nav',
    'menu_class' => 'footer-services-menu'
]);

echo "Elapsed time: " . number_format($result['elapsed'], 4) . " seconds\n";
echo "Output size: " . strlen($result['output']) . " bytes\n";
echo "Valid HTML: " . ($result['is_valid_html'] ? 'YES' : 'NO') . "\n";
echo "Output preview: " . substr($result['output'], 0, 200) . "...\n";
echo "Contains 'Service 1': " . (strpos($result['output'], 'Service 1') !== false ? 'YES' : 'NO') . "\n";
echo "Contains 'Service 2': " . (strpos($result['output'], 'Service 2') !== false ? 'YES' : 'NO') . "\n";

$test1_passed = !$result['timed_out'] 
    && $result['is_valid_html'] 
    && strpos($result['output'], 'Service 1') !== false
    && strpos($result['output'], 'Service 2') !== false
    && $result['elapsed'] < 1.0; // Should be fast

$results->add(
    'Footer Services Menu Renders',
    $test1_passed,
    $test1_passed ? 'Menu rendered successfully in ' . number_format($result['elapsed'], 4) . 's' : 'Menu rendering failed or timed out'
);

echo "Status: " . ($test1_passed ? '✓ PASS' : '✗ FAIL') . "\n\n";

/**
 * Test 2: Footer Company Menu Renders Correctly
 * Requirement 3.2: footer-company menu SHALL CONTINUE TO render correctly
 */
echo "Test 2: Footer Company Menu Rendering\n";
echo "----------------------------------------\n";

$result = render_menu_with_timeout([
    'theme_location' => 'footer-company',
    'container' => 'nav',
    'menu_class' => 'footer-company-menu'
]);

echo "Elapsed time: " . number_format($result['elapsed'], 4) . " seconds\n";
echo "Output size: " . strlen($result['output']) . " bytes\n";
echo "Valid HTML: " . ($result['is_valid_html'] ? 'YES' : 'NO') . "\n";
echo "Contains 'About Us': " . (strpos($result['output'], 'About Us') !== false ? 'YES' : 'NO') . "\n";
echo "Contains 'Contact': " . (strpos($result['output'], 'Contact') !== false ? 'YES' : 'NO') . "\n";

$test2_passed = !$result['timed_out'] 
    && $result['is_valid_html'] 
    && strpos($result['output'], 'About Us') !== false
    && strpos($result['output'], 'Contact') !== false
    && $result['elapsed'] < 1.0;

$results->add(
    'Footer Company Menu Renders',
    $test2_passed,
    $test2_passed ? 'Menu rendered successfully in ' . number_format($result['elapsed'], 4) . 's' : 'Menu rendering failed or timed out'
);

echo "Status: " . ($test2_passed ? '✓ PASS' : '✗ FAIL') . "\n\n";

/**
 * Test 3: Unassigned Menu Location Calls Fallback
 * Requirement 3.5: Unassigned menu location SHALL CONTINUE TO call fallback callback
 */
echo "Test 3: Unassigned Menu Location Fallback\n";
echo "----------------------------------------\n";

// Use a named function instead of closure to avoid serialization issues
function test_fallback_callback($args) {
    global $test_fallback_called, $test_fallback_args;
    $test_fallback_called = true;
    $test_fallback_args = $args;
    return '<ul><li>Fallback Menu</li></ul>';
}

$GLOBALS['test_fallback_called'] = false;
$GLOBALS['test_fallback_args'] = null;

$result = render_menu_with_timeout([
    'theme_location' => 'nonexistent-location',
    'container' => 'nav',
    'fallback_cb' => 'test_fallback_callback'
]);

echo "Elapsed time: " . number_format($result['elapsed'], 4) . " seconds\n";
echo "Fallback called: " . ($GLOBALS['test_fallback_called'] ? 'YES' : 'NO') . "\n";
echo "Output contains 'Fallback Menu': " . (strpos($result['output'], 'Fallback Menu') !== false ? 'YES' : 'NO') . "\n";

$test3_passed = !$result['timed_out'] 
    && $GLOBALS['test_fallback_called'] 
    && strpos($result['output'], 'Fallback Menu') !== false
    && $result['elapsed'] < 1.0;

$results->add(
    'Unassigned Location Fallback',
    $test3_passed,
    $test3_passed ? 'Fallback executed successfully' : 'Fallback not called or timed out'
);

echo "Status: " . ($test3_passed ? '✓ PASS' : '✗ FAIL') . "\n\n";

/**
 * Test 4: Menu Caching Works for Footer Menus
 * Requirement 3.4: Menu caching SHALL CONTINUE TO work correctly for footer menus
 */
echo "Test 4: Footer Menu Caching Behavior\n";
echo "----------------------------------------\n";

// Clear cache first
wp_cache_flush_group(CACHE_GROUP);
echo "Cache cleared\n";

// First render (cache miss)
$start1 = microtime(true);
$output1 = wp_nav_menu([
    'theme_location' => 'footer-services',
    'container' => 'nav',
    'echo' => false
]);
$time1 = microtime(true) - $start1;

echo "First render (cache miss): " . number_format($time1, 4) . " seconds\n";
echo "Output size: " . strlen($output1) . " bytes\n";
echo "Output is valid: " . (!empty($output1) ? 'YES' : 'NO') . "\n";

// Second render (should attempt cache hit)
$start2 = microtime(true);
$output2 = wp_nav_menu([
    'theme_location' => 'footer-services',
    'container' => 'nav',
    'echo' => false
]);
$time2 = microtime(true) - $start2;

echo "Second render (cache attempt): " . number_format($time2, 4) . " seconds\n";
echo "Output size: " . strlen($output2) . " bytes\n";
echo "Output is valid: " . (!empty($output2) ? 'YES' : 'NO') . "\n";

// Check if caching filter is active (baseline behavior)
$has_cache_filter = has_filter('pre_wp_nav_menu');
echo "Cache filter active: " . ($has_cache_filter ? 'YES' : 'NO') . "\n";

// Test passes if both renders produce valid output and caching mechanism exists
$test4_passed = !empty($output1) 
    && !empty($output2) 
    && $time1 < 1.0
    && $time2 < 1.0
    && $has_cache_filter !== false;

$results->add(
    'Footer Menu Caching',
    $test4_passed,
    $test4_passed ? 'Caching mechanism active and menus render' : 'Caching failed or menus invalid'
);

echo "Status: " . ($test4_passed ? '✓ PASS' : '✗ FAIL') . "\n\n";

/**
 * Test 5: Property-Based Test - Multiple Footer Menu Variations
 * Tests that various footer menu configurations all work correctly
 */
echo "Test 5: Property-Based Test - Footer Menu Variations\n";
echo "----------------------------------------\n";

$variations = [
    ['location' => 'footer-services', 'container' => 'nav'],
    ['location' => 'footer-services', 'container' => 'div'],
    ['location' => 'footer-services', 'container' => false],
    ['location' => 'footer-company', 'container' => 'nav'],
    ['location' => 'footer-company', 'container' => 'div'],
    ['location' => 'footer-company', 'container' => false],
];

$all_variations_passed = true;
$variation_count = 0;

foreach ($variations as $i => $variation) {
    $result = render_menu_with_timeout([
        'theme_location' => $variation['location'],
        'container' => $variation['container'],
        'menu_class' => 'test-menu-' . $i
    ]);
    
    $passed = !$result['timed_out'] && $result['is_valid_html'] && $result['elapsed'] < 1.0;
    
    if (!$passed) {
        $all_variations_passed = false;
        echo "  ✗ Variation " . ($i + 1) . " failed: {$variation['location']} with container=" . var_export($variation['container'], true) . "\n";
    } else {
        $variation_count++;
    }
}

echo "Tested variations: " . count($variations) . "\n";
echo "Passed: {$variation_count}\n";

$results->add(
    'Footer Menu Variations',
    $all_variations_passed,
    $all_variations_passed ? "All {$variation_count} variations passed" : "Some variations failed"
);

echo "Status: " . ($all_variations_passed ? '✓ PASS' : '✗ FAIL') . "\n\n";

/**
 * Test 6: Property-Based Test - Non-Primary Locations Never Hang
 * Property: For ANY non-primary location, rendering completes quickly
 */
echo "Test 6: Property-Based Test - Non-Primary Locations Performance\n";
echo "----------------------------------------\n";

// Use a named function instead of closure to avoid serialization issues
function test_performance_fallback() {
    return '<ul><li>Fallback</li></ul>';
}

$non_primary_locations = [
    'footer-services',
    'footer-company',
    'nonexistent-location-1',
    'nonexistent-location-2',
    'random-location-' . uniqid(),
];

$all_fast = true;
$max_time = 0;

foreach ($non_primary_locations as $location) {
    $result = render_menu_with_timeout([
        'theme_location' => $location,
        'container' => 'nav',
        'fallback_cb' => 'test_performance_fallback'
    ]);
    
    $max_time = max($max_time, $result['elapsed']);
    
    if ($result['timed_out'] || $result['elapsed'] >= 1.0) {
        $all_fast = false;
        echo "  ✗ Location '{$location}' was slow: " . number_format($result['elapsed'], 4) . "s\n";
    }
}

echo "Tested locations: " . count($non_primary_locations) . "\n";
echo "Max render time: " . number_format($max_time, 4) . " seconds\n";
echo "All under 1 second: " . ($all_fast ? 'YES' : 'NO') . "\n";

$results->add(
    'Non-Primary Locations Performance',
    $all_fast,
    $all_fast ? "All locations rendered in < 1s (max: " . number_format($max_time, 4) . "s)" : "Some locations were slow"
);

echo "Status: " . ($all_fast ? '✓ PASS' : '✗ FAIL') . "\n\n";

// Cleanup
echo "Cleaning up test menus...\n";
cleanup_test_menus($menu_ids);
echo "Cleanup complete\n\n";

// Summary
$results->summary();

// Exit with appropriate code
exit($results->allPassed() ? 0 : 1);
