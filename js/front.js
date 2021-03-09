(function( $ ) {
	'use strict';

    // Disable auto discover to prevent Dropzone being attached twice
    Dropzone.autoDiscover = false; 

	$( document ).ready(function() {
		console.log(woa_front_cntrl);
		// init DropzoneJS
		var myDropzone = new Dropzone("div#woa-secudeal-uploder", {
			
			url: woa_front_cntrl.upload_file,
			params: {
				'woa-secudeal-nonce': $('#woa-secudeal-nonce').val()
			},
			paramName: "woa-secudeal-file", // name of file field
			acceptedFiles: 'image/*', // accepted file types
			maxFilesize: 2, // MB
			addRemoveLinks: true,
            init: function() {

                if (images_ids.trim()) {
					$.ajax({
						url: woa_front_cntrl.fetch_file,
						type: 'post',
						data: {images_ids: images_ids, 'woa-secudeal-nonce': $('#woa-secudeal-nonce').val()},
						dataType: 'json',
				
						success: function(response){
							$.each(response, function(key,value) {
								var mockFile = { name: value.name, size: value.size};
								myDropzone.emit("addedfile", mockFile);
								myDropzone.emit("thumbnail", mockFile, value.path);
								myDropzone.emit("complete", mockFile);
							});
						}
					});
				}
            },
			//success file upload handling
			success: function (file, response) {
				// handle your response object
				console.log(response.status);
				
				if (response.status == 'ok') {
				
					file.previewElement.classList.add("dz-success");

					var currentValue = getInputValue();
					var newValue =currentValue.concat(','+ response.attachment_id);
					jQuery('#'+ gallery_id).val(newValue);
				} else {
					file.previewElement.classList.add("dz-error");
				}
			},

			//error while handling file upload
			error: function (file,response) {
				file.previewElement.classList.add("dz-error");
			},

			// removing uploaded images
			removedfile: function(file) {
				var _ref;  
				console.log('>>>>>>>>>>>> file ', file)
				// AJAX request for attachment removing
				$.ajax({
					type: 'POST',
					url: woa_front_cntrl.delete_file,
					data: {
						'attachment_id': file.attachment_id,
						'woa-secudeal-nonce': $('#woa-secudeal-nonce').val()
					},
					// handle response from server
					success: function (response) {
						// handle your response object
						console.log(response.status);
						if (response.status == 'ok') {
							file.attachment_id
							var currentValue = getInputValue();
							var newValue = deleteValue(currentValue, ','+ gallery_id);
							jQuery('#'+ gallery_id).val(newValue);
						}
					},
				});
				
				return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;        
			}
		});
		
	});

	function getInputValue() {
		return jQuery('#'+ gallery_id).val();
	}
	function deleteValue(str, substr) {
		const result = str.replace(substr, '');
		const final = result.trim().split(' ').filter(a => a.trim() != "");
		return final;
	}
})( jQuery );