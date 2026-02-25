<?php
/**
 * Plugin Name: Project Core
 * Description: Core functionality for the project
 * Version: 1.0.0
 * Author: Dupley Maxim Igorevich
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Disable WordPress emoji
 */
function project_core_disable_emojis() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'project_core_disable_emojis' );

/**
 * Remove WordPress version from head
 */
function project_core_remove_version() {
    return '';
}
add_filter( 'the_generator', 'project_core_remove_version' );

/**
 * Remove wlwmanifest link
 */
function project_core_remove_wlwmanifest() {
    remove_action( 'wp_head', 'wlwmanifest_link' );
}
add_action( 'init', 'project_core_remove_wlwmanifest' );

/**
 * Remove REST API links from head
 */
function project_core_remove_rest_links() {
    remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
}
add_action( 'init', 'project_core_remove_rest_links' );

/**
 * Disable XML-RPC
 */
function project_core_disable_xmlrpc( $methods ) {
    return [];
}
add_filter( 'xmlrpc_methods', 'project_core_disable_xmlrpc' );

/**
 * Add body classes
 */
function project_core_body_classes( $classes ) {
    $classes[] = 'project-custom';
    return $classes;
}
add_filter( 'body_class', 'project_core_body_classes' );

/**
 * Change login logo URL
 */
function project_core_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'project_core_login_logo_url' );

/**
 * Change login logo title
 */
function project_core_login_logo_url_title() {
    return get_bloginfo( 'name' );
}
add_filter( 'login_headertitle', 'project_core_login_logo_url_title' );

/**
 * Custom login logo styles
 */
function project_core_login_logo() {
    ?>
    <style>
        .login h1 a {
            background-image: none;
            width: auto;
            padding-bottom: 0;
        }
    </style>
    <?php
}
add_action( 'login_enqueue_scripts', 'project_core_login_logo' );
