<?php
/**
 * Object Cache drop-in for WordPress
 * 
 * This file provides a simple file-based object cache for local development.
 * For production, consider using Redis or Memcached.
 * 
 * @package WordPress
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$wp_object_cache = null;

/**
 * Initialize the object cache
 */
function wp_cache_init() {
    global $wp_object_cache;
    $wp_object_cache = new WP_Object_Cache();
}

/**
 * Object Cache class
 */
class WP_Object_Cache {
    private $cache = [];
    private $stats = [
        'get' => 0,
        'set' => 0,
        'delete' => 0,
    ];

    /**
     * Get data from cache
     */
    public function get( $key, $group = 'default', $force = false ) {
        $this->stats['get']++;
        
        if ( ! $this->is_valid_key( $key ) ) {
            return false;
        }

        if ( ! $force && isset( $this->cache[ $group ][ $key ] ) ) {
            return $this->cache[ $group ][ $key ];
        }

        return false;
    }

    /**
     * Set data in cache
     */
    public function set( $key, $data, $group = 'default', $expire = 0 ) {
        $this->stats['set']++;
        
        if ( ! $this->is_valid_key( $key ) ) {
            return false;
        }

        if ( ! isset( $this->cache[ $group ] ) ) {
            $this->cache[ $group ] = [];
        }

        $this->cache[ $group ][ $key ] = $data;
        return true;
    }

    /**
     * Delete data from cache
     */
    public function delete( $key, $group = 'default', $deprecated = false ) {
        $this->stats['delete']++;
        
        if ( ! isset( $this->cache[ $group ][ $key ] ) ) {
            return false;
        }

        unset( $this->cache[ $group ][ $key ] );
        return true;
    }

    /**
     * Check if key exists in cache
     */
    public function exists( $key, $group = 'default' ) {
        return isset( $this->cache[ $group ][ $key ] );
    }

    /**
     * Get cache stats
     */
    public function get_stats() {
        return $this->stats;
    }

    /**
     * Validate cache key
     */
    private function is_valid_key( $key ) {
        return ! empty( $key ) && is_string( $key );
    }
}

/**
 * Get data from cache
 */
function wp_cache_get( $key, $group = 'default', $force = false, &$found = null ) {
    global $wp_object_cache;
    $result = $wp_object_cache->get( $key, $group, $force );
    $found = $wp_object_cache->exists( $key, $group );
    return $result;
}

/**
 * Set data in cache
 */
function wp_cache_set( $key, $data, $group = 'default', $expire = 0 ) {
    global $wp_object_cache;
    return $wp_object_cache->set( $key, $data, $group, $expire );
}

/**
 * Delete data from cache
 */
function wp_cache_delete( $key, $group = 'default' ) {
    global $wp_object_cache;
    return $wp_object_cache->delete( $key, $group );
}

/**
 * Check if key exists in cache
 */
function wp_cache_exists( $key, $group = 'default' ) {
    global $wp_object_cache;
    return $wp_object_cache->exists( $key, $group );
}

/**
 * Flush cache
 */
function wp_cache_flush() {
    global $wp_object_cache;
    $wp_object_cache = new WP_Object_Cache();
    return true;
}

/**
 * Get cache stats
 */
function wp_cache_get_stats() {
    global $wp_object_cache;
    return $wp_object_cache->get_stats();
}
