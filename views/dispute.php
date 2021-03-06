<?php
    ?>
    <form class="woocommerce-EditdisputeForm edit-dispute" action="" method="post" <?php do_action( 'woocommerce_edit_dispute_form_tag' ); ?> >
    
        <?php do_action( 'woocommerce_edit_dispute_form_start' ); ?>
    
        <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
            <label for="dispute_trans"><?php esc_html_e( 'Transaction', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
            <div class="select-custom">

					<select id="dispute_trans" class="select2 custom-select" name="dispute_transaction">
                        <option value=""> Choisir la transaction</option>
						<?php
						$options = array(
							''         => __( '— No change —', 'woocommerce' ),
							'taxable'  => __( 'Taxable', 'woocommerce' ),
							'shipping' => __( 'Shipping only', 'woocommerce' ),
							'none'     => _x( 'None', 'Tax status', 'woocommerce' ),
						);
						foreach ( $customer_orders as $key => $order ) {
							echo '<option value="' . esc_attr( $order->id ) . '">' . esc_html( '#'. $order->get_id() .' '. $order->get_order_key() ) . '</option>';
						}
						?>
					</select>
            </div>
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
            <label for="dispute_last_name"><?php esc_html_e( 'Description', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
            <textarea type="text" class="woocommerce-Input woocommerce-Input--text input-text" rows="5" name="dispute_description" id="dispute_desc"><?php echo esc_attr( $dispute_description ); ?></textarea>
        </p>
        <div class="clear"></div>
    
        <p>
            <?php wp_nonce_field( 'save_dispute_details', 'save-dispute-details-nonce' ); ?>
            <button type="submit" class="woocommerce-Button button" name="save_dispute_details" value="<?php esc_attr_e( 'Envoyer', 'woocommerce' ); ?>"><?php esc_html_e( 'Envoyer', 'woocommerce' ); ?></button>
            <input type="hidden" name="action" value="save_dispute_details" />
        </p>
        
    </form>