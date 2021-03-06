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
	// update_option('plugin_permalinks_flushed', 0);
 
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


add_shortcode( 'woo_custom_account', 'woo_custom_account_callback' );
function woo_custom_account_callback( $atts ) {
    $atts = shortcode_atts( array(
        'tab' => '',
        'key' => 'no foo',
    ), $atts, 'woo_custom_account' );

	global $wp;
	// $key =  $atts['tab']; !empty($atts['tab'])?$atts['tab']: 
	$key = (!empty($wp->query_vars['tab'])?$wp->query_vars['tab']:'');
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

add_shortcode( 'secudeal_form', 'render_secudeal_form' );

function render_secudeal_form( $atts ) {
   
    $field_group_key = 'group_6041f8829e1e1';
    $user_id = get_current_user_id();
		
    // enqueue
    acf_enqueue_scripts();

	ob_start();

    echo '<form class="woocommerce-secudealForm edit-account" action="" method="post">';
    // Allow $_POST data to persist across form submission attempts.
    if( isset($_POST['acf']) ) {
        add_filter('acf/pre_load_value', 'woo_as_filter_pre_load_value', 10, 3);
    }
    
    // defaults
    $args = array(
        'user_id'	=> $user_id,
        'view'		=> 'edit',
        'el'		=> 'div',
    );
    
    // vars
    $post_id = 'user_' . $args['user_id'];
    
    // get field groups
    $field_groups = acf_get_field_groups(array(
        'user_id'	=> $args['user_id'] ? $args['user_id'] : 'new',
        'user_form'	=> $args['view']
    ));
    
    // bail early if no field groups
    if( empty($field_groups) ) {
        return;
    }
    
    // form data
    acf_form_data(array(
        'screen'		=> 'user',
        'post_id'		=> $post_id,
        'validation'	=> ($args['view'] == 'register') ? 0 : 1
    ));
		
    // elements
    $before = '<div class="acf-user-' . $args['view'] . '-fields acf-fields -clear">';
    $after = '</div>';
    $fields = acf_get_fields($field_group_key);

    // loop
    foreach( $field_groups as $field_group ) {
        
        // vars
        $fields = acf_get_fields( $field_group );
        
        // title
        if( $field_group['style'] === 'default' ) {
            echo '<h2>' . $field_group['title'] . '</h2>';
        }
        
        // render
        echo $before;
        acf_render_fields( $fields, $post_id, $args['el'], $field_group['instruction_placement'] );
        echo $after;
    }
    ?>
	<p>
		<?php // wp_nonce_field( 'save_secudeal_details', 'save-secudeal-details-nonce' ); ?>
		<button type="submit" class="secudeal-Button button" name="save_account_details" value="<?php esc_attr_e( 'Enregistrer', 'secudeal' ); ?>"><?php esc_html_e( 'Enregistrer', 'secudeal' ); ?></button>
		<input type="hidden" name="action" value="save_secudeal_details" />
	</p>
    <?php
    echo '</form>';
    $out = ob_get_clean();
            
    // actions
    add_action('acf/input/admin_footer', 'woo_as_admin_footer', 10, 1);

	return $out;
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
            var view = '<?php echo $this->view; ?>';

            // add missing spinners
            var $submit = $('input.button-primary');
            if( !$submit.next('.spinner').length ) {
                $submit.after('<span class="spinner"></span>');
            }

        })(jQuery);	
    </script>
    <?php
    
}

function save_user( $user_id ) {
		
    // verify nonce
    if( !acf_verify_nonce('user') ) {
        return $user_id;
    }
    
    // save
    if( acf_validate_save_post(true) ) {
        acf_save_post( "user_$user_id" );
    }
}


add_shortcode( 'dispute_form', 'render_dispute_form' );

function render_dispute_form() {
    $dispute_description = '';
    $customer_orders = wc_get_orders(
        apply_filters(
            'woocommerce_my_account_my_orders_query',
            array(
                'customer' => get_current_user_id(),
                'status'      => 'complete',
                'posts_per_page'     => -1
            )
        )
    );
    
	ob_start();
    include(dirname(__FILE__) .'/views/dispute.php');
	$out = ob_get_clean();

	return $out;
}

add_shortcode( 'password_update_form', 'render_password_update_form' );

function render_password_update_form() {
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
	$redirect = $current_url;

	ob_start();

    // show any error messages after form submission
    include(dirname(__FILE__) .'/views/notif/errors.php');
    
    // if (isset($_GET['password-reset']) && $_GET['password-reset'] == 'true') {
        include(dirname(__FILE__) .'/views/notif/success.php');
    // }

    if (is_user_logged_in()) {
        include(dirname(__FILE__) .'/views/edit-password.php');
    }
	$out = ob_get_clean();

	return $out;
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