<?php 
?>
<div class="secudeal-vendor-fields">
    <form action="">
        <div class="acf-user-vendor-fields acf-fields -clear">

                <?php acf_render_fields( $fields, $post_id, $args['el'], $field_group['instruction_placement'] ); ?>
                
        </div>
        <p>
            <input type="hidden" name="save_secudeal" value="<?php echo wp_create_nonce('save-sd-nonce'); ?>"/>
            <input type="hidden" name="woas_redirect" value="<?php echo $redirect; ?>"/>
            <button type="submit" class="secudeal-Button button" name="save_account_details" value="<?php esc_attr_e( 'Enregistrer', 'secudeal' ); ?>"><?php esc_html_e( 'Enregistrer', 'secudeal' ); ?></button>
            <input type="hidden" name="sd_action" value="save_secudeal_details" />
        </p>
    </form>
</div>