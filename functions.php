<?php

/** 
 * Initialisation functions
 * 
 * */
function do_init() {
    custom_query_vars();
    wpdocs_load_textdomain();
    woo_as_reset_password();
    woo_as_process_dispute();
    woo_as_process_user_infos();
}
  
/**
 * Load plugin textdomain.
 */
function wpdocs_load_textdomain() {
  load_plugin_textdomain( 'woo-shortcodes', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
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


/** 
 * Add rules for plugin
 * 
 * */
function custom_query_vars() {
    global $wp;
 
    $page_id = WOA_ENDPOINT_URL; 
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


/** 
 * Password reset process
 * 
 * */
function woo_as_reset_password() {
    // reset a users password
    if (isset($_POST['woas_action']) && $_POST['woas_action'] == 'reset-password') {
        global $user_ID;
 
        if (!is_user_logged_in()) {
            return;
        }
 
        if (wp_verify_nonce($_POST['woas_password_nonce'], 'update-password-nonce')) {
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


function woo_as_filter_pre_load_value( $null, $post_id, $field ) {
    $field_key = $field['key'];
    if( isset( $_POST['acf'][ $field_key ] )) {
        return $_POST['acf'][ $field_key ];
    }
    return $null;
}

function woo_as_admin_footer() {
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

if(!function_exists('woo_errors')) { 
	// used for tracking error messages
	function woo_errors(){
	    static $wp_error; // Will hold global variable safely
	    return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
	}
}


/** 
 * Custom style for plugin views
 * 
 * */
function woa_theme_styles(){
    wp_enqueue_style('woa-front', WAS_CSS_URL .'/front.css', array(), time());
}

function woa_available_pg($available_gateways){
    $_available_gateways = [];
    
    foreach ( $available_gateways as $gateway ) {
        if ( $gateway->supports( 'add_payment_method' ) || $gateway->supports( 'tokenization' ) ) {
            $_available_gateways[ $gateway->id ] = $gateway;
        }
    }
    
    return $_available_gateways;
}

/** 
 * Redirect overrides after forms process
 * 
 * */
function woa_override_redirect($user_id) {
    // die(var_dump($_POST));

	ob_start();
    if ( isset($_POST['_wp_http_referer']) && get_the_Id() == WOA_ENDPOINT_URL):
        $redirect_url = $_POST['_wp_http_referer'];
    else :
        return $user_id;
    endif;
	$out = ob_get_clean();

    wp_safe_redirect( home_url($redirect_url) );
    exit;
}

function load_woocommerce_scripts() {

    if ( get_the_Id() == WOA_ENDPOINT_URL):
        include_once WC_ABSPATH . 'includes/class-wc-frontend-scripts.php';
    endif;
}

/** 
 * Enable HTML in mail
 * 
 * */
function set_html_content_type() {
	return 'text/html';
}

/** 
 * Dispute process
 * 
 * */
function woo_as_process_dispute() {
    // reset a users password
    if (isset($_POST['dispute_action']) && $_POST['dispute_action'] == 'send-dispute-mail') {
        global $user_ID;
 
        if (!is_user_logged_in()) {
            return;
        }
 
        if (isset($_POST['send_dispute_nonce']) && wp_verify_nonce($_POST['send_dispute_nonce'], 'dispute-send-nonce')) {
            // wp_die(var_dump($_POST));
            $order_id = (int) $_POST['dispute_transaction'];
            $transaction = wc_get_order($order_id);
            // retrieve all error messages, if any
            $errors = woo_errors()->get_error_messages();
 
            if (empty($errors)) {
                // change the password here
                
                $to = $transaction->get_billing_email();
                $current_user = wp_get_current_user();
                $subject = "Litige de transaction No #". $transaction->get_id();
                $description = sanitize_textarea_field($_POST['dispute_description']);


                add_filter( 'wp_mail_content_type', 'set_html_content_type' );

                /**
                 * Load mail content
                 */
                ob_start();

                include(dirname(__FILE__) .'/views/dispute.php');

                $content = ob_get_clean();
                /**
                 * End mail content
                 */

                $status = wp_mail($to, $subject, $content);

                remove_filter( 'wp_mail_content_type', 'set_html_content_type' );

                // send password change email here (if WP doesn't)
                wp_redirect(add_query_arg('ds', 1, $_POST['woas_redirect']));
                exit;
            }
        }
    }
}

function current_page_url() {
	global $post;

    if (is_singular()) :
        $current_url = get_permalink($post->ID);
    else :
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") $pageURL .= "s";
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        else $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        $current_url = $pageURL;
    endif;

    return $current_url;
}


/** 
 * Process user info update
 * 
 * */
function woo_as_process_user_infos() {
    // reset a users password
    if (isset($_POST['woas_action']) && $_POST['woas_action'] == 'update-billing-info') {
        global $user_ID;
 
        if (!is_user_logged_in()) {
            return;
        }
 
        if (wp_verify_nonce($_POST['woas_info_nonce'], 'update-info-nonce')) {
            // wp_die(var_dump($user_ID, $_POST));
            // if ($_POST['password_1'] == '' || $_POST['password_2'] == '') {
            //     // password(s) field empty
            //     woo_errors()->add('password_empty', __('Please enter a password, and confirm it', 'pippin'));
            // }
            // if ($_POST['password_1'] != $_POST['password_2']) {
            //     // passwords do not match
            //     woo_errors()->add('password_mismatch', __('Passwords do not match', 'pippin'));
            // }
 
            // retrieve all error messages, if any
            $errors = woo_errors()->get_error_messages();
 
            if (empty($errors)) {
                
                // change the password here
                $billing = $_POST['billing'];
                foreach ($billing as $key => $value) {
                    update_user_meta($user_ID, $key, $value);
                }

                // send password change email here (if WP doesn't)
                wp_redirect(add_query_arg('iup', 1, $_POST['woas_redirect']));
                exit;
            }
        }
    }
}
