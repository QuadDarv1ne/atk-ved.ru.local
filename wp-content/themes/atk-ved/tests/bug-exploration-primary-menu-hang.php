<?php
/**
 * Bug Condition Exploration Test - Primary Menu Hang
 * 
 * **Validates: Requirements 2.1, 2.2, 2.3, 2.4**
 * 
 * This test demonstrates the hang condition when rendering the primary navigation menu.
 * 
 * CRITICAL: This test is EXPECTED TO FAIL on unfixed code - failure confirms the bug exists.
 * 
 * The test calls wp_nav_menu(['theme_location' => 'primary']) with a 3-second timeout
 * and verifies that:
 * 1. Menu rendering completes within 3 seconds (does not hang)
 * 2. The result is valid HTML (not empty or error)
 * 
 * EXPECTED OUTCOME ON UNFIXED CODE: Test FAILS with timeout or hang
 * This proves the bug exists and helps identify the root cause.
 * 
 * @package ATKVed
 */

declare(strict_types=1);

// Load WordPress
define('WP_USE_THEMES', false);
require_once __DIR__ . '/../../../../wp-load.php';

// Test configuration
const TIMEOUT_SECONDS = 3;
const TEST_THEME_LOCATION = 'primary';

/**
 * Setup a test menu for the primary location
 */
function setup_test_menu(): ?int {
    // Check if primary menu already exists
    $locations = get_nav_menu_locations();
    if (isset($locations[TEST_THEME_LOCATION]) && $locations[TEST_THEME_LOCATION] > 0) {
        $menu_id = $locations[TEST_THEME_LOCATION];
        echo "Using existing menu ID: {$menu_id}\n";
        return $menu_id;
    }
    
    // Create a test menu
    $menu_name = 'Test Primary Menu';
    $menu_id = wp_create_nav_menu($menu_name);
    
    if (is_wp_error($menu_id)) {
        echo "Failed to create test menu: " . $menu_id->get_error_message() . "\n";
        return null;
    }
    
    echo "Created test menu ID: {$menu_id}\n";
    
    // Add some menu items
    $home_id = wp_update_nav_menu_item($menu_id, 0, [
        'menu-item-title' => 'Home',
        'menu-item-url' => home_url('/'),
        'menu-item-status' => 'publish',
        'menu-item-type' => 'custom',
    ]);
    
    $about_id = wp_update_nav_menu_item($menu_id, 0, [
        'menu-item-title' => 'About',
        'menu-item-url' => home_url('/about'),
        'menu-item-status' => 'publish',
        'menu-item-type' => 'custom',
    ]);
    
    $services_id = wp_update_nav_menu_item($menu_id, 0, [
        'menu-item-title' => 'Services',
        'menu-item-url' => home_url('/services'),
        'menu-item-status' => 'publish',
        'menu-item-type' => 'custom',
    ]);
    
    // Add a child item to test hierarchy
    $service1_id = wp_update_nav_menu_item($menu_id, 0, [
        'menu-item-title' => 'Service 1',
        'menu-item-url' => home_url('/services/service-1'),
        'menu-item-status' => 'publish',
        'menu-item-type' => 'custom',
        'menu-item-parent-id' => $services_id,
    ]);
    
    echo "Added menu items: Home, About, Services (with child)\n";
    
    // Assign menu to primary location
    $locations = get_nav_menu_locations();
    $locations[TEST_THEME_LOCATION] = $menu_id;
    set_theme_mod('nav_menu_locations', $locations);
    
    echo "Assigned menu to 'primary' location\n";
    
    return $menu_id;
}

/**
 * Run the bug exploration test
 */
function run_bug_exploration_test(): void {
    echo "=== Bug Condition Exploration Test ===\n";
    echo "Testing: wp_nav_menu(['theme_location' => 'primary'])\n";
    echo "Timeout: " . TIMEOUT_SECONDS . " seconds\n";
    echo "Expected on UNFIXED code: FAIL (timeout or hang)\n";
    echo "Expected on FIXED code: PASS (renders within timeout)\n\n";
    
    // Setup test menu
    echo "Setup: Creating Test Menu\n";
    echo "--------------------------\n";
    $menu_id = setup_test_menu();
    if (!$menu_id) {
        echo "ERROR: Could not setup test menu\n";
        return;
    }
    echo "\n";
    
    // Test 1: Primary menu rendering with timeout
    echo "Test 1: Primary Menu Rendering\n";
    echo "-------------------------------\n";
    
    $start_time = microtime(true);
    $result = null;
    $timed_out = false;
    
    // Set a timeout using pcntl_alarm if available, otherwise use set_time_limit
    if (function_exists('pcntl_alarm') && function_exists('pcntl_signal')) {
        // Use PCNTL for more reliable timeout
        declare(ticks = 1);
        
        pcntl_signal(SIGALRM, function() use (&$timed_out) {
            $timed_out = true;
            throw new Exception("TIMEOUT: Menu rendering exceeded " . TIMEOUT_SECONDS . " seconds");
        });
        
        pcntl_alarm(TIMEOUT_SECONDS);
        
        try {
            ob_start();
            wp_nav_menu([
                'theme_location' => TEST_THEME_LOCATION,
                'container' => 'nav',
                'container_class' => 'primary-navigation',
                'menu_class' => 'nav-menu',
                'fallback_cb' => false,
                'echo' => true
            ]);
            $result = ob_get_clean();
            pcntl_alarm(0); // Cancel alarm
        } catch (Exception $e) {
            pcntl_alarm(0); // Cancel alarm
            $result = null;
            echo "CAUGHT EXCEPTION: " . $e->getMessage() . "\n";
        }
    } else {
        // Fallback: use set_time_limit (less reliable but works without pcntl)
        $old_time_limit = ini_get('max_execution_time');
        set_time_limit(TIMEOUT_SECONDS);
        
        try {
            ob_start();
            wp_nav_menu([
                'theme_location' => TEST_THEME_LOCATION,
                'container' => 'nav',
                'container_class' => 'primary-navigation',
                'menu_class' => 'nav-menu',
                'fallback_cb' => false,
                'echo' => true
            ]);
            $result = ob_get_clean();
        } catch (Exception $e) {
            $result = null;
            echo "CAUGHT EXCEPTION: " . $e->getMessage() . "\n";
        }
        
        set_time_limit((int)$old_time_limit);
    }
    
    $end_time = microtime(true);
    $elapsed = $end_time - $start_time;
    
    echo "Elapsed time: " . number_format($elapsed, 3) . " seconds\n";
    
    // Analyze results
    $test_passed = false;
    
    if ($timed_out || $elapsed >= TIMEOUT_SECONDS) {
        echo "RESULT: TIMEOUT - Menu rendering exceeded " . TIMEOUT_SECONDS . " seconds\n";
        echo "STATUS: TEST FAILED (as expected on unfixed code)\n";
        echo "COUNTEREXAMPLE: Primary menu rendering hangs/times out\n";
        echo "ROOT CAUSE HYPOTHESIS: Likely recursive serialization in cache filter\n";
    } elseif ($result === null || empty(trim($result))) {
        echo "RESULT: EMPTY - Menu rendering returned no output\n";
        echo "STATUS: TEST FAILED\n";
        echo "COUNTEREXAMPLE: Primary menu rendering returns empty result\n";
    } elseif (strpos($result, '<nav') !== false || strpos($result, '<ul') !== false) {
        echo "RESULT: SUCCESS - Menu rendered valid HTML\n";
        echo "Output length: " . strlen($result) . " bytes\n";
        echo "STATUS: TEST PASSED\n";
        echo "NOTE: If this passes on unfixed code, the bug may not be reproducible\n";
        $test_passed = true;
    } else {
        echo "RESULT: INVALID - Menu rendering returned unexpected output\n";
        echo "Output: " . substr($result, 0, 200) . "...\n";
        echo "STATUS: TEST FAILED\n";
    }
    
    echo "\n";
    
    // Test 2: Investigate cache filter behavior
    echo "Test 2: Cache Filter Investigation\n";
    echo "-----------------------------------\n";
    
    // Check if the problematic filter is active
    $has_pre_nav_menu_filter = has_filter('pre_wp_nav_menu');
    echo "pre_wp_nav_menu filter active: " . ($has_pre_nav_menu_filter ? 'YES' : 'NO') . "\n";
    
    if ($has_pre_nav_menu_filter) {
        echo "FINDING: The menu caching filter is active\n";
        echo "HYPOTHESIS: The filter may be causing recursive serialization\n";
        echo "LOCATION: wp-content/themes/atk-ved/inc/performance.php\n";
        echo "PROBLEMATIC CODE: md5(serialize(\$args)) where \$args is an object\n";
    }
    
    echo "\n";
    
    // Test 3: Check menu structure for circular references
    echo "Test 3: Menu Structure Analysis\n";
    echo "--------------------------------\n";
    
    $locations = get_nav_menu_locations();
    if (isset($locations[TEST_THEME_LOCATION])) {
        $menu_id = $locations[TEST_THEME_LOCATION];
        $menu_items = wp_get_nav_menu_items($menu_id);
        
        if ($menu_items) {
            echo "Primary menu ID: " . $menu_id . "\n";
            echo "Menu items count: " . count($menu_items) . "\n";
            
            // Check for circular references
            $parent_child_map = [];
            foreach ($menu_items as $item) {
                if ($item->menu_item_parent != 0) {
                    if (!isset($parent_child_map[$item->menu_item_parent])) {
                        $parent_child_map[$item->menu_item_parent] = [];
                    }
                    $parent_child_map[$item->menu_item_parent][] = $item->ID;
                }
            }
            
            echo "Parent-child relationships: " . count($parent_child_map) . "\n";
            
            // Simple circular reference check (not exhaustive)
            $has_circular = false;
            foreach ($menu_items as $item) {
                if ($item->menu_item_parent != 0) {
                    // Check if parent is also a child of this item
                    if (isset($parent_child_map[$item->ID]) && 
                        in_array($item->menu_item_parent, $parent_child_map[$item->ID])) {
                        $has_circular = true;
                        echo "CIRCULAR REFERENCE DETECTED: Item {$item->ID} <-> {$item->menu_item_parent}\n";
                    }
                }
            }
            
            if (!$has_circular) {
                echo "No obvious circular references detected\n";
            }
        } else {
            echo "No menu items found for primary location\n";
        }
    } else {
        echo "Primary menu location not assigned\n";
    }
    
    echo "\n";
    
    // Summary
    echo "=== Test Summary ===\n";
    if ($test_passed) {
        echo "UNEXPECTED: Test PASSED on what should be unfixed code\n";
        echo "This suggests either:\n";
        echo "1. The bug is not reproducible in this environment\n";
        echo "2. The code has already been fixed\n";
        echo "3. The root cause hypothesis is incorrect\n";
    } else {
        echo "EXPECTED: Test FAILED on unfixed code\n";
        echo "This confirms the bug exists and demonstrates the hang condition\n";
        echo "\nCounterexample documented:\n";
        echo "- Primary menu rendering times out or hangs\n";
        echo "- Elapsed time: " . number_format($elapsed, 3) . " seconds\n";
        echo "- Most likely cause: Recursive serialization in cache filter\n";
    }
    
    echo "\nNext steps:\n";
    echo "1. Implement fix for the identified root cause\n";
    echo "2. Re-run this test to verify fix works\n";
    echo "3. Ensure preservation tests pass (footer menus unaffected)\n";
}

// Run the test
try {
    run_bug_exploration_test();
} catch (Throwable $e) {
    echo "\nFATAL ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
