<?php 

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' ); ?>

<form class="secudeal-EditPasswordForm edit-password" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?> >

	<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

		<p class="secudeal-form-row secudeal-form-row--wide">
			<label for="password_current"><?php esc_html_e( 'Current password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
			<input type="password" class="secudeal-Input secudeal-Input--password input-text" name="password_current" id="password_current" autocomplete="off" />
		</p>
		<p class="secudeal-form-row secudeal-form-row--wide">
			<label for="password_1"><?php esc_html_e( 'New password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
			<input type="password" class="secudeal-Input secudeal-Input--password input-text" name="password_1" id="password_1" autocomplete="off" />
		</p>
		<p class="secudeal-form-row secudeal-form-row--wide">
			<label for="password_2"><?php esc_html_e( 'Confirm new password', 'woocommerce' ); ?></label>
			<input type="password" class="secudeal-Input secudeal-Input--password input-text" name="password_2" id="password_2" autocomplete="off" />
		</p>
		
	<div class="clear"></div>

	<?php do_action( 'woocommerce_edit_account_form' ); ?>

	<p>
		<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
        <input type="hidden" name="woas_action" value="reset-password"/>
        <input type="hidden" name="woas_redirect" value="<?php echo $redirect; ?>"/>
        <input type="hidden" name="woas_password_nonce" value="<?php echo wp_create_nonce('rcp-password-nonce'); ?>"/>
		<button type="submit" class="secudeal-Button button" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>"><?php esc_html_e( 'Save changes', 'woocommerce' ); ?></button>
	</p>

	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
</form>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>
