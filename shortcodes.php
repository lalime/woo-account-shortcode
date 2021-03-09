<?php

add_shortcode( 'woo_custom_account', 'woo_custom_account_callback' );
add_shortcode( 'woo_user_orders', 'render_user_orders' );
add_shortcode( 'woo_user_infos', 'render_user_profile_form' );
add_shortcode( 'woo_user_gateways', 'render_user_gateways' );
add_shortcode( 'secudeal_form', 'render_secudeal_form' );
add_shortcode( 'dispute_form', 'render_dispute_form' );
add_shortcode( 'password_update_form', 'render_password_update_form' );
add_shortcode( 'secudeal_quote_form', 'render_quote_form' );


/**
 * Shortcode : [woo_custom_account]
 */
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
    
	echo '<div class="woocommerce-MyAccount-content">';
	echo '<div class="woocommerce-notices-wrapper">';
	// wc_print_notices();
	echo '</div>';

	if ( has_action( 'woocommerce_account_' . $key . '_endpoint' ) ) {
		do_action( 'woocommerce_account_' . $key . '_endpoint', $value );
	} else {

    	// No endpoint found? Default to dashboard.
        wc_get_template(
            'myaccount/dashboard.php',
            array(
            'current_user' => get_user_by('id', get_current_user_id()),
        )
        );
    }
	echo '</div>';
	$out = ob_get_clean();

	return $out;
}
// flush_rewrite_rules(false);


/**
 * Shortcode : [secudeal_form]
 */
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


/**
 * Shortcode : [dispute_form]
 */
function render_dispute_form() {
    $dispute_description = '';
	$redirect = current_page_url();
    $before = '<div class="secudeal-password-update-fields">';
    $after = '</div>';

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

    echo $before;

    if(!is_user_logged_in()) {
        include(dirname(__FILE__) .'/views/notif/guest.php'); 
    } else {
    
        if (isset($_GET['ds']) && $_GET['ds'] == 1) {
            $message = __('Litige envoyé!', 'woo-shortcodes');

            include(dirname(__FILE__) .'/views/notif/success.php');
        }

        include(dirname(__FILE__) .'/views/dispute-form.php');
    }

    echo $after;

	$out = ob_get_clean();

	return $out;
}


/**
 * Shortcode : [woo_user_orders]
 */
function render_user_orders() {
    $current_page    = empty( $page ) ? 1 : absint( $page );
    $customer_orders = wc_get_orders(
        apply_filters(
            'woocommerce_my_account_my_orders_query',
            array(
                'customer' => get_current_user_id(),
                'page'     => $current_page,
                'posts_per_page' => 10,
                'paginate' => true,
            )
        )
    );
    
    $has_orders = 0 < $customer_orders->total;
    
    $before = '<div class="secudeal-transactions-list">';
    $after = '</div>';

	ob_start();

    echo $before;

    if(!is_user_logged_in()) {
        include(dirname(__FILE__) .'/views/notif/guest.php'); 
    } else {
        include(dirname(__FILE__) .'/views/transactions.php');
    }
    
    echo $after;

	$out = ob_get_clean();

	return $out;
}

/**
 * Shortcode : [woo_user_infos]
 */
function render_user_profile_form() {
    $user_id = get_current_user_id();
    $before = '<div class="secudeal-user_billing-fields">';
    $after = '</div>';
    $redirect = current_page_url();

    $_billing_first_name = get_user_meta( $user_id, 'billing_first_name', true );
    $_billing_last_name = get_user_meta( $user_id, 'billing_last_name', true );
    $_billing_address_1 = get_user_meta( $user_id, 'billing_address_1', true );
    $_billing_phone = get_user_meta( $user_id, 'billing_phone', true );
    $_billing_email = get_user_meta( $user_id, 'billing_email', true );
    $_billing_latitude = get_user_meta( $user_id, 'billing_latitude', true );
    $_billing_longitude = get_user_meta( $user_id, 'billing_longitude', true );
    $_billing_zipcode = get_user_meta( $user_id, 'billing_zipcode', true );
    $_billing_city = get_user_meta( $user_id, 'billing_city', true );
    $_billing_country = get_user_meta( $user_id, 'billing_country', true );
    
	ob_start();

    echo $before;

    if(!is_user_logged_in()) {
        include(dirname(__FILE__) .'/views/notif/guest.php'); 
    } else {
    
        if (isset($_GET['iup']) && $_GET['iup'] == 1) {
            $message = __('Profil mis à jour', 'woo-shortcodes');

            include(dirname(__FILE__) .'/views/notif/success.php');
        }

        include(dirname(__FILE__) .'/views/user-info.php');
    }

    echo $after;

	$out = ob_get_clean();

	return $out;
}

/**
 * Shortcode : [woo_user_gateways]
 */
function render_user_gateways() {
    
    $before = '<div class="secudeal-payment_methods-fields">';
    $after = '</div>';

	ob_start();

    echo $before;
    if(!is_user_logged_in()) {
        include(dirname(__FILE__) .'/views/notif/guest.php'); 
    } else {
        woocommerce_account_payment_methods();
    }

    echo $after;

	$out = ob_get_clean();

	return $out;
}

/**
 * Shortcode : [password_update_form]
 */
function render_password_update_form() {
   		
	$redirect = current_page_url();
    $before = '<div class="secudeal-password-update-fields">';
    $after = '</div>';

	ob_start();

    echo $before;

    if(!is_user_logged_in()) {
        include(dirname(__FILE__) .'/views/notif/guest.php'); 
    } else {
        // show any error messages after form submission
        include(dirname(__FILE__) .'/views/notif/errors.php');
    
        if (isset($_GET['password-reset']) && $_GET['password-reset'] == 'true') {
            $message = __('Mot de passe modifié', 'woo-shortcodes');

            include(dirname(__FILE__) .'/views/notif/success.php');
        }

        if (is_user_logged_in()) {
            include(dirname(__FILE__) .'/views/edit-password.php');
        }
    }
	echo $after;

	$out = ob_get_clean();

	return $out;
}

/**
 * Shortcode : [secudeal_quote_form]
 */
function render_quote_form() {
    
	$quote_description = '';
	$redirect = current_page_url();
    $before = '<div class="secudeal-quote-form-fields">';
    $after = '</div>';

	ob_start();

    echo $before;

    if(!is_user_logged_in()) {
        include(dirname(__FILE__) .'/views/notif/guest.php'); 
    } else {
        // show any error messages after form submission
        include(dirname(__FILE__) .'/views/notif/errors.php');
    
        if (isset($_GET['qs']) && $_GET['qs'] == 1) {
            $message = __('Devis envoyé!', 'woo-shortcodes');

            include(dirname(__FILE__) .'/views/notif/success.php');
        }

        if (is_user_logged_in()) {
            include(dirname(__FILE__) .'/views/quote-form.php');
        }
    }
	echo $after;

	$out = ob_get_clean();

	return $out;
}
 