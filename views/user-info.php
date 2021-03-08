<?php
/**
 * Edit account form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined('ABSPATH') || exit;
?>

<form class="secudeal-EditAccountForm edit-account" action="" method="post" >

	<p class="secudeal-form-row secudeal-form-row--first">
		<label for="account_first_name"><?php esc_html_e('Prénom', 'woo-shortcodes'); ?>&nbsp;<span class="required">*</span></label> 
		<input type="text" class="secudeal-Input secudeal-Input--text input-text" name="account_first_name" id="account_first_name" autocomplete="given-name" placeholder="Prénom" value="<?php echo esc_attr($_billing_first_name); ?>" />
	</p>
	<p class="secudeal-form-row secudeal-form-row--last ">
		<label for="account_last_name"><?php esc_html_e('Nom', 'woo-shortcodes'); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="secudeal-Input secudeal-Input--text input-text" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr($_billing_last_name); ?>" />
	</p>
	<p class="secudeal-form-row secudeal-form-row--last ">
		<label for="account_billing_address_1"><?php esc_html_e('Votre addresse', 'woo-shortcodes'); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="secudeal-Input secudeal-Input--text input-text" name="billing_address_1" id="account_billing_address_1" autocomplete="off" placeholder="Votre addresse" value="<?php echo esc_attr($_billing_address_1); ?>" />
		<input type="hidden" name="billing_latitude" id="billing_latitude" value="<?php echo esc_attr($_billing_latitude); ?>" />
		<input type="hidden" name="billing_longitude" id="billing_longitude" value="<?php echo esc_attr($_billing_longitude); ?>" />
		<input type="hidden" name="billing_zipcode" id="billing_zipcode" value="<?php echo esc_attr($_billing_zipcode); ?>" />
		<input type="hidden" name="billing_city" id="billing_city" value="<?php echo esc_attr($_billing_city); ?>" />
	</p>
	<div class="clear"></div>

	<div class="clear"></div>

	<p class="secudeal-form-row secudeal-form-row--wide">
		<label for="account_billing_email"><?php esc_html_e('Email', 'woo-shortcodes'); ?>&nbsp;<span class="required">*</span></label>
		<input type="email" class="secudeal-Input secudeal-Input--email input-text" name="billing_email" id="account_billing_email" placeholder="Email" autocomplete="email" value="<?php echo esc_attr($_billing_email); ?>" />
	</p>

	<p class="secudeal-form-row secudeal-form-row--wide">
		<label for="account_billing_phone"><?php esc_html_e('Téléphone', 'woo-shortcodes'); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="secudeal-Input secudeal-Input--text input-text" name="billing_phone" id="account_billing_phone" value="<?php echo esc_attr($_billing_phone); ?>" placeholder="Téléphone" autocomplete="tel"/>
	</p>

	<div class="clear"></div>

	<p>
		<?php wp_nonce_field('save_account_details', 'save-account-details-nonce'); ?>
		<button type="submit" class="secudeal-Button button" name="save_account_details" value="<?php esc_attr_e('Save changes', 'woo-shortcodes'); ?>"><?php esc_html_e('Save changes', 'woo-shortcodes'); ?></button>
		<input type="hidden" name="action" value="save_account_details" />
	</p>

</form>
