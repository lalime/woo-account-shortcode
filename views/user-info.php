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

$api_key = 'AIzaSyBIwzALxUPNbatRBj3Xi1Uhp0fFzwWNBkE';
?>

<form class="secudeal-EditAccountForm edit-account" action="" method="post" >

	<p class="secudeal-form-row secudeal-form-row--first">
		<label for="account_first_name"><?php esc_html_e('Prénom', 'woo-shortcodes'); ?>&nbsp;<span class="required">*</span></label> 
		<input type="text" class="secudeal-Input secudeal-Input--text input-text" name="billing[billing_first_name]" id="account_first_name" autocomplete="given-name" placeholder="Prénom" value="<?php echo esc_attr($_billing_first_name); ?>" />
	</p>
	<p class="secudeal-form-row secudeal-form-row--last ">
		<label for="account_last_name"><?php esc_html_e('Nom', 'woo-shortcodes'); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="secudeal-Input secudeal-Input--text input-text" name="billing[billing_last_name]" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr($_billing_last_name); ?>" />
	</p>
	<p class="secudeal-form-row secudeal-form-row--last ">
		<label for="billing_address_1"><?php esc_html_e('Votre addresse', 'woo-shortcodes'); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="secudeal-Input secudeal-Input--text input-text" name="billing[billing_address_1]" id="billing_address_1" autocomplete="off" placeholder="Votre addresse" value="<?php echo esc_attr($_billing_address_1); ?>" />
		<input type="hidden" id="billing_latitude" name="billing[billing_latitude]" value="<?php echo esc_attr($_billing_latitude); ?>" />
		<input type="hidden" id="billing_longitude" name="billing[billing_longitude]" value="<?php echo esc_attr($_billing_longitude); ?>" />
		<input type="hidden" id="billing_country" name="billing[billing_country]" value="<?php echo esc_attr($_billing_country); ?>" />

		<input type="hidden" id="billing_city" name="billing[billing_city]" value="<?php echo esc_attr($_billing_city); ?>" />
		<input type="hidden" id="sd_vendor_country" name="billing[sd_vendor_country]" value="<?php echo esc_attr($_billing_country); ?>" />
		<input type="hidden" id="sd_vendor_city" name="billing[sd_vendor_city]" value="<?php echo esc_attr($_billing_city); ?>" />
		<input type="hidden" id="sd_vendor_address_data" name="billing[sd_vendor_address_data]" value="<?php echo esc_attr($_billing_address_1); ?>" />
	</p>
	<div class="clear"></div>

	<div class="clear"></div>

	<p class="secudeal-form-row secudeal-form-row--wide">
		<label for="account_billing_email"><?php esc_html_e('Email', 'woo-shortcodes'); ?>&nbsp;<span class="required">*</span></label>
		<input type="email" class="secudeal-Input secudeal-Input--email input-text" name="billing[billing_email]" id="account_billing_email" placeholder="Email" autocomplete="email" value="<?php echo esc_attr($_billing_email); ?>" />
	</p>

	<p class="secudeal-form-row secudeal-form-row--wide">
		<label for="account_billing_phone"><?php esc_html_e('Téléphone', 'woo-shortcodes'); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="secudeal-Input secudeal-Input--text input-text" name="billing[billing_phone]" id="account_billing_phone" value="<?php echo esc_attr($_billing_phone); ?>" placeholder="Téléphone" autocomplete="tel"/>
	</p>

	<div class="clear"></div>

	<p>
		<?php wp_nonce_field('save_account_details', 'save-account-details-nonce'); ?>
        <input type="hidden" name="woas_action" value="update-billing-info"/>
        <input type="hidden" name="woas_redirect" value="<?php echo $redirect; ?>"/>
        <input type="hidden" name="woas_info_nonce" value="<?php echo wp_create_nonce('update-info-nonce'); ?>"/>
		<button type="submit" class="secudeal-Button button" name="save_account_details" value="<?php esc_attr_e('Save changes', 'woo-shortcodes'); ?>"><?php esc_html_e('Save changes', 'woo-shortcodes'); ?></button>
		<input type="hidden" name="action" value="save_account_details" />
	</p>

</form>

<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $api_key ?>&sessiontoken=####&libraries=places&callback=initAutocomplete" async defer></script>
<script >
	let placeSearch;
	let autocomplete;

	function initAutocomplete() {
		
		const options = {
			fields: ["address_components", "geometry", "icon", "name"],
			strictBounds: false,
			types: ["establishment"],
		};

		autocomplete = new google.maps.places.Autocomplete(
			document.getElementById("billing_address_1"), options
		);
		// Avoid paying for data that you don't need by restricting the set of
		// place fields that are returned to just the address components.
		// autocomplete.setFields(["address_component"]);
		// When the user selects an address from the drop-down, populate the
		// address fields in the form.
		autocomplete.addListener("place_changed", fillInAddress);
	}

	function fillInAddress() {
		// Get the place details from the autocomplete object.
		const place = autocomplete.getPlace();

		console.log('>>>>>>>>>>>> ', place)
  
		for(var i = 0; i < place.address_components.length; i += 1) {
			var addressObj = place.address_components[i];
			for(var j = 0; j < addressObj.types.length; j += 1) {
				if (addressObj.types[j] === 'country') {
					console.log(addressObj.types[j]); // confirm that this is 'country'
					console.log(addressObj.long_name);
					console.log('<<<<<<<<<<<<<<<<<<<< country ', addressObj.long_name)

					document.getElementById("billing_city").value = addressObj.long_name;
					document.getElementById("sd_vendor_country").value = addressObj.long_name;
					// confirm that this is the country name
				}
				if (addressObj.types[j] === "locality") {
					console.log(addressObj.types[j]); // confirm that this is 'country'
					console.log(addressObj.long_name);
					console.log('<<<<<<<<<<<<<<<<<<<< city ', addressObj.long_name)

					document.getElementById("billing_city").value = addressObj.long_name;
					document.getElementById("sd_vendor_city").value = addressObj.long_name;
					// confirm that this is the country name
				}
			}
		}

		// Get each component of the address from the place details,
		// and then fill-in the corresponding field on the form
		document.getElementById('billing_latitude').value = place.geometry['location'].lat();
		document.getElementById("billing_longitude").value = place.geometry['location'].lng();
		document.getElementById('billing_address_1').value = place.formatted_address;
		document.getElementById("sd_vendor_address_data").value = place.formatted_address;
	}

	// Bias the autocomplete object to the user's geographical location,
	// as supplied by the browser's 'navigator.geolocation' object.
	function geolocate() {
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition((position) => {
				const geolocation = {
					lat: position.coords.latitude,
					lng: position.coords.longitude,
				};
				const circle = new google.maps.Circle({
					center: geolocation,
					radius: position.coords.accuracy,
				});
				autocomplete.setBounds(circle.getBounds());
			});
		}
	}

</script>
