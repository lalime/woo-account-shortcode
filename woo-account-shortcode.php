<?php
/**
* Plugin Name: Woocommerce account custom shortcodes
* Plugin URI: https://www.wordpress.com/
* Description: This is the very first plugin I ever created.
* Version: 1.0
* Author: Anonymous
* Author URI: http://wordpress.com/
**/

if ( ! function_exists( 'is_plugin_active' ) ){
    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
}

if ( !defined( 'DS' ) ) {
    define( 'DS', DIRECTORY_SEPARATOR );
}
if ( !defined( 'WAS_DIR' ) ) {
    define( 'WAS_DIR', plugin_dir_path( __FILE__ ) );
}
if ( !defined( 'WAS_URL' ) ) {
    define( 'WAS_URL', plugin_dir_url( __FILE__ ) );
}
if ( !defined( 'WAS_IMG_URL' ) ) {
    define( 'WAS_IMG_URL', WAS_URL.DS.'images' );
}
if ( !defined( 'WAS_CSS_URL' ) ) {
    define( 'WAS_CSS_URL', WAS_URL.DS.'css' );
}

if ( !defined( 'WAS_VIEWS_DIR' ) ) {
    define( 'WAS_VIEWS_DIR', WAS_DIR.DS.'views' );
}

if ( !defined( 'WOA_ENDPOINT_URL' ) ) {
    define( 'WOA_ENDPOINT_URL', 10 );
}

register_activation_hook( __FILE__, 'is_woocommerce_enabled' );

add_action( 'plugins_loaded', 'woo_acs_init' );

function woo_needed_admin_notice() {
     echo '<div class="notice notice-warning is-dismissible">
        <p><b>Woocommerce account custom shortcodes</b> needs woocommerce to be installed and activated to work properly.</p>
     </div>';
}

/** 
 * Add rules for plugin
 * */
add_action( 'init', 'custom_query_vars' );

add_action('init', 'save_user');
// add_action('init', 'load_woocommerce_scripts');

add_action('init', 'woo_as_reset_password');
add_action('wp_head', 'woa_theme_styles');
add_filter('woocommerce_available_payment_gateways', 'woa_available_pg');
add_filter('woocommerce_get_view_order_url', 'woa_get_view_order_url', 10, 2);

add_action( 'woocommerce_save_account_details', 'woa_override_redirect' );
add_action( 'woocommerce_customer_save_address', 'woa_override_redirect' );

include(dirname(__FILE__).'/functions.php');
include(dirname(__FILE__).'/shortcodes.php');