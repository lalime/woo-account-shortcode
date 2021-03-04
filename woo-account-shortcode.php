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

/** 
 * Check for activated Woocommerce plugin
 * 
 * */
function is_woocommerce_enabled() {
	if ( !is_plugin_active('woocommerce/woocommerce.php') ) {
	   //  wp_die('Install and activate the Woocommerce plugin.');
	    woo_needed_admin_notice();
	    exit;
	}
}
register_activation_hook( __FILE__, 'is_woocommerce_enabled' );

/** 
 * plugins_loaded Process activation hooks
 * 
 * */
function woo_acs_init() {
	if( is_plugin_active('woocommerce/woocommerce.php') ) {
		// do my plugin stuff here
    	update_option('plugin_permalinks_flushed', 0);

	} else {
            add_action('admin_notices', 'woo_needed_admin_notice');
	}
}
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
function custom_query_vars() {
    global $wp;
 
    $page_id = 10; 
	$page_data = get_post( $page_id );
	// update_option('plugin_permalinks_flushed', 0);
 
    if( ! is_object($page_data) ) { // post not there
        return;
    }

    $wp->add_query_var( 'tab' );
    $wp->add_query_var( 'value' );
    add_rewrite_rule( '^custom-account/([^/]*)/?([^/]*)/?$', 'index.php?page_id='.$page_id.'&tab=$matches[1]&value=$matches[2]', 'top' );
 
    if( !get_option('plugin_permalinks_flushed') ) {
 
        flush_rewrite_rules(false);
        update_option('plugin_permalinks_flushed', 1);
 
    }
}

add_shortcode( 'woo_custom_account', 'woo_custom_account_callback' );
function woo_custom_account_callback( $atts ) {
    $atts = shortcode_atts( array(
        'endpoint' => '',
        'key' => 'no foo',
    ), $atts, 'woo_custom_account' );

	global $wp;
	$key = 'edit-address';// $atts['endpoint'];
	$key = !empty($atts['tab'])?$atts['tab']: (!empty($wp->query_vars['tab'])?$wp->query_vars['tab']:'');
	$value = !empty($wp->query_vars['value'])?$wp->query_vars['value']:'';

	ob_start();
	// var_dump($atts, $wp->query_vars);

	if ( has_action( 'woocommerce_account_' . $key . '_endpoint' ) ) {
		do_action( 'woocommerce_account_' . $key . '_endpoint', $value );
		// return;
	} else {

    	// No endpoint found? Default to dashboard.
        wc_get_template(
            'myaccount/dashboard.php',
            array(
            'current_user' => get_user_by('id', get_current_user_id()),
        )
        );
    }
	$out = ob_get_clean();

	return $out;
}
flush_rewrite_rules(false);