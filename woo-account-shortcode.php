<?php
/**
* Plugin Name: Woocommerce account custom shortcodes
* Plugin URI: https://www.wordpress.com/
* Description: This is the very first plugin I ever created.
* Version: 1.0
* Author: Anonymous
* Author URI: http://wordpress.com/
* Text Domain:       woo-shortcodes
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

// add_action('init', 'save_user');

add_action('init', 'do_init');
add_action( 'admin_init', 'woa_register_settings' );

add_action('wp_head', 'woa_theme_styles');
add_action('wp_enqueue_scripts', 'woa_enqueue_scripts');
add_filter('woocommerce_available_payment_gateways', 'woa_available_pg');

// add_action( 'woocommerce_save_account_details', 'woa_override_redirect' );
// add_action( 'woocommerce_customer_save_address', 'woa_override_redirect' );
add_action( 'wc_stripe_add_payment_method_success', 'woa_override_redirect' );

/**
 * ACF Overrides
 */
add_action('acf/render_field_settings/type=text', 'woa_text_render_field_settings');
// add_filter('acf/prepare_field', 'my_admin_only_prepare_field');
add_action('acf/render_field/type=text', 'woa_acf_render_field');

/**
 * AJAX Actions
 */
add_action('wp_ajax_woa_secudeal_upload_handler', 'handle_form_data'); 
add_action('wp_ajax_woa_secudeal_fetch_file', 'fetch_file');
add_action('wp_ajax_woa_secudeal_delete_file', 'delete_file');

// require_once 'dompdf/autoload.inc.php';

include(dirname(__FILE__).'/ajax-process.php');
include(dirname(__FILE__).'/functions.php');
include(dirname(__FILE__).'/shortcodes.php');