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
if ( !defined( 'WAS_VIEWS_DIR' ) ) {
    define( 'WAS_VIEWS_DIR', WAS_DIR.DS.'views' );
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
	update_option('plugin_permalinks_flushed', 0);
 
    if( ! is_object($page_data) ) { // post not there
        return;
    }
    
    $wp->add_query_var( 'tab' );
    $wp->add_query_var( 'value' );
    add_rewrite_rule( '^custom-account/([^/]*)/?([^/]*)/?$', 'index.php?page_id='. $page_id.'&tab=$matches[1]&value=$matches[2]', 'top' );
 
    if( !get_option('plugin_permalinks_flushed') ) {
 
        flush_rewrite_rules(false);
        update_option('plugin_permalinks_flushed', 1);
 
    }
}

function woo_as_filter_pre_load_value( $null, $post_id, $field ) {
    $field_key = $field['key'];
    if( isset( $_POST['acf'][ $field_key ] )) {
        return $_POST['acf'][ $field_key ];
    }
    return $null;
}

function woo_as_admin_footer() {
	
    // script
    ?>
    <script type="text/javascript">
        (function($) {

            // vars
            var view = 'edit';

            // add missing spinners
            var $submit = $('input.button-primary');
            if( !$submit.next('.spinner').length ) {
                $submit.after('<span class="spinner"></span>');
            }

        })(jQuery);	
    </script>
    <?php
    
}

add_action('init', 'save_user');
function save_user( $user_id ) {
    $user_id=$user_id?$user_id:get_current_user_id();

    // verify nonce
    if( !acf_verify_nonce('user') ) {
        return $user_id;
    }
    
    // save
    if( acf_validate_save_post(true) ) {
        acf_save_post( "user_$user_id" );
    }
}

if(!function_exists('woo_errors')) { 
	// used for tracking error messages
	function woo_errors(){
	    static $wp_error; // Will hold global variable safely
	    return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
	}
}

add_action('init', 'woo_as_reset_password');
function woo_as_reset_password() {
    // reset a users password
    if (isset($_POST['woas_action']) && $_POST['woas_action'] == 'reset-password') {
        global $user_ID;
 
        if (!is_user_logged_in()) {
            return;
        }
 
        if (wp_verify_nonce($_POST['woas_password_nonce'], 'rcp-password-nonce')) {
            if ($_POST['password_1'] == '' || $_POST['password_2'] == '') {
                // password(s) field empty
                woo_errors()->add('password_empty', __('Please enter a password, and confirm it', 'pippin'));
            }
            if ($_POST['password_1'] != $_POST['password_2']) {
                // passwords do not match
                woo_errors()->add('password_mismatch', __('Passwords do not match', 'pippin'));
            }
 
            // retrieve all error messages, if any
            $errors = woo_errors()->get_error_messages();
 
            if (empty($errors)) {
                // change the password here
                $user_data = array(
                    'ID' => $user_ID,
                    'user_pass' => $_POST['password_1']
                );
                wp_update_user($user_data);
                // send password change email here (if WP doesn't)
                wp_redirect(add_query_arg('password-reset', 'true', $_POST['woas_redirect']));
                exit;
            }
        }
    }
}

include(dirname(__FILE__).'/shortcodes.php');