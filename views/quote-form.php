<?php
    ?>
<form class="secudeal-EditdisputeForm edit-dispute" action="" method="post" <?php do_action( 'woocommerce_edit_dispute_form_tag' ); ?> >

    <?php do_action( 'woocommerce_edit_dispute_form_start' ); ?>

    
    <p class="secudeal-form-row secudeal-form-row--wide">
        <label for="quote_first_name"><?php esc_html_e( 'Prénom ', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
        <input type="text" class="secudeal-Input secudeal-Input--email input-text" required name="quote_first_name" id="quote_name" autocomplete="given-name" />
    </p>

    <p class="secudeal-form-row secudeal-form-row--wide">
        <label for="quote_last_name"><?php esc_html_e( 'Nom', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
        <input type="text" class="secudeal-Input secudeal-Input--text input-text" required name="quote_last_name" id="quote_last_name" autocomplete="family-name" />
    </p>
    
    <p class="secudeal-form-row secudeal-form-row--wide">
        <label for="quote_email"><?php esc_html_e( 'Email', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
        <input type="email" class="secudeal-Input secudeal-Input--email input-text" required name="quote_email" id="quote_email" autocomplete="off" />
    </p>
    
    <p class="secudeal-form-row secudeal-form-row--wide">
        <label for="quote_price"><?php esc_html_e( 'Prix ', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
        <input type="text" class="secudeal-Input secudeal-Input--text input-text" required name="quote_price" id="quote_price" autocomplete="off" />
    </p>

    <p class="secudeal-form-row secudeal-form-row--last">
        <label for="quote_description"><?php esc_html_e( 'Description du service', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
        <textarea type="text" class="secudeal-Input secudeal-Input--text input-text" rows="5" required name="quote_description" id="quote_description"><?php echo esc_attr( $quote_description ); ?></textarea>
    </p>
    <div class="clear"></div>

    <p>
        <?php wp_nonce_field( 'save_quote_details', 'save-quote-details-nonce' ); ?>
        <input type="hidden" name="quote_action" value="send-quote-details"/>
        <input type="hidden" name="woas_redirect" value="<?php echo $redirect; ?>"/>
        <input type="hidden" name="send_quote_nonce" value="<?php echo wp_create_nonce('quote-send-nonce'); ?>"/>
        <button type="submit" class="secudeal-Button button" name="save_quote_details" value="<?php esc_attr_e( 'Envoyer', 'woocommerce' ); ?>"><?php esc_html_e( 'Envoyer', 'woocommerce' ); ?></button>
    </p>
    
</form>
