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
    woo_as_process_quote();
    woo_as_process_vendor_data();
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

                // $to = $transaction->get_billing_email();
                $current_user = wp_get_current_user();
                $subject = 'Nouveau devis';
                $content = 'Ci-joint le devis';
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

/** 
 * Process user info update
 * 
 * */
function woo_as_process_quote() {
    // reset a users password
    if (isset($_POST['quote_action']) && $_POST['quote_action'] == 'send-quote-details') {
        global $user_ID;
 
        if (!is_user_logged_in()) {
            return;
        }
 
        if (wp_verify_nonce($_POST['send_quote_nonce'], 'quote-send-nonce')) {
            // wp_die(var_dump($user_ID, $_POST));
            $current_user = wp_get_current_user();
            $quote_first_name = $_POST['quote_first_name'];
            $quote_last_name = $_POST['quote_last_name'];
            $quote_email = $_POST['quote_email'];
            $quote_price = (float) $_POST['quote_price'];
            $quote_description = $_POST['quote_description'];

            $image_src = wp_get_attachment_image_src(get_theme_mod( 'custom_logo' ), 'full');
            $path = str_replace(get_home_url()."/wp-content", WP_CONTENT_DIR, $image_src[0]);
            $logo_path = get_base_64($path);
            $currency_symbol = get_woocommerce_currency_symbol(get_option('woocommerce_currency'));

            ob_start();

            include(dirname(__FILE__) .'/views/quote-tpl.php');

            $content = ob_get_clean();
            $file = woa_generate_file($content);
            
            // retrieve all error messages, if any
            $errors = woo_errors()->get_error_messages();

            if (!$file) {
                woo_errors()->add('pdf_empty', __('Erreur generation du PDF', 'pippin'));
            }
 
            if (empty($errors)) {

                $subject = 'Nouveau devis';
                $attachments = array( $file );
                $headers = 'From: '. $current_user->user_firstname .' '. $current_user->user_lastname .' <'. $current_user->user_email .'>' . "\r\n";
                
                add_filter( 'wp_mail_content_type', 'set_html_content_type' );

                $status = wp_mail($to, $subject, $content, $headers, $attachments);

                remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
                
                // send password change email here (if WP doesn't)
                wp_redirect(add_query_arg('qs', 1, $_POST['woas_redirect']));
                exit;
            }
        }
    }
}

function woa_generate_file($html) {
    // include autoloader
    require_once 'dompdf/autoload.inc.php';

    // instantiate and use the dompdf class
    $dompdf = new Dompdf\Dompdf();

    $dompdf->loadHtml($html);
    
    $dompdf->set_option('isRemoteEnabled', TRUE);
    
    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper('A4');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser
    $output = $dompdf->output();

    $filename = get_random_string(10);
    $structure = WP_CONTENT_DIR . '/uploads/was-pdf/';

    if (!file_exists($structure) && !mkdir($structure, 0777, true)) {
        die('Failed to create folders...');
    }

    if (file_put_contents( $structure.$filename .'.pdf', $output));
        return  $structure.$filename .'.pdf';
    
    return false;
}

function get_base_64($path)
{
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    return 'data:image/' . $type . ';base64,' . base64_encode($data);
}

function get_random_string($n) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
  
    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }
  
    return $randomString;
}

function woa_text_render_field_settings( $field ) {
	
	acf_render_field_setting( $field, array(
		'label'			=> __('Exclude words'),
		'instructions'	=> __('Enter words separated by a comma'),
		'name'			=> 'display_as',
		'type'			=> 'select',
        'choices' 		=> array(
            'normal'			=> __("Normal text input",'acf'),
            'gallery' 				=> __("Multiple images",'acf'),
        ),
        'default_value'	=> 'normal'
	));
	
}

function woo_as_process_vendor_data( $field ) {
    my_acf_save_post( $post_id );
}

function woa_acf_render_field( $field ) {
    if ( empty($field['display_as']) || $field['display_as'] != 'gallery') 
        return ;
    var_dump($field['value']);
    include dirname(__FILE__). '/views/gallery.php';
}