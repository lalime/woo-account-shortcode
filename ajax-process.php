<?php
/**
 * Handle form data received from frontend via AJAX
 *
 * @return void
 */
function handle_form_data()
{
    if (isset($_POST['woa-secudeal-nonce'])
        && wp_verify_nonce($_POST['woa-secudeal-nonce'], 'woa_secudeal_ajax_nonce')
    ) {
        if (!empty($_FILES)) {

            // These files need to be included as dependencies when on the front end.
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            require_once( ABSPATH . 'wp-admin/includes/media.php' );

            foreach ($_FILES as $file => $array) {
                if ($_FILES[$file]['error'] !== UPLOAD_ERR_OK) { // If there is some errors, during file upload
                    wp_send_json(array('status' => 'error', 'message' => __('Error: ', 'woo-shortcodes') . $_FILES[$file]['error']));
                }

                // HANDLE RECEIVED FILE

                $post_id = 0; // Set post ID to attach uploaded image to specific post

                $attachment_id = media_handle_upload($file, $post_id);

                if (is_wp_error($attachment_id)) { // Check for errors during attachment creation
                    wp_send_json(array(
                        'status' => 'error',
                        'message' => __('Error while processing file', 'woo-shortcodes'),
                    ));
                } else {
                    wp_send_json(array(
                        'status' => 'ok',
                        'attachment_id' => $attachment_id,
                        'message' => __('File uploaded', 'woo-shortcodes'),
                    ));
                }
            }
        }
        wp_send_json(array('status' => 'error', 'message' => __('There is nothing to upload!', 'woo-shortcodes')));
    }
    wp_send_json(array('status' => 'error', 'message' => __('Security check failed!', 'woo-shortcodes')));
}

/**
 * Fetch attachments by id via AJAX
 *
 * @return void
 */
function fetch_file()
{
    
    if (isset($_POST['images_ids']) 
        && isset($_POST['woa-secudeal-nonce'])
        && wp_verify_nonce($_POST['woa-secudeal-nonce'], 'woa_secudeal_ajax_nonce')
    ) {
        $images = [];
        $fileList = [];
        $attachment_ids = $_POST['images_ids'];

        $images = explode(',', $attachment_ids);

        foreach ($images as $id) {
            # code...
            $file_url = wp_get_attachment_image_src($id);
            $file_path = str_replace(get_home_url()."/wp-content", WP_CONTENT_DIR, $file_url[0]);
            $size = filesize($file_path);
            $ext = pathinfo($file_path, PATHINFO_EXTENSION);
            $filename = basename($file_path, ".".$ext);

            $fileList[] = [
                'attachment_id'=> $id,
                'name'=> $filename,
                'size'=>$size,
                'path'=>$file_url[0]
            ];
        }
        
        wp_send_json($fileList);
    }
    wp_send_json(array('status' => 'error'));
}

/**
 * Delete attachment by id via AJAX
 *
 * @return void
 */
function delete_file()
{

    if (isset($_POST['attachment_id']) 
        && isset($_POST['woa-secudeal-nonce'])
        && wp_verify_nonce($_POST['woa-secudeal-nonce'], 'woa_secudeal_ajax_nonce')
    ) {
        $attachment_id = absint($_POST['attachment_id']);

        $result = wp_delete_attachment($attachment_id, true); // permanently delete attachment

        if ($result) {
            wp_send_json(array('status' => 'ok'));
        }
    }
    wp_send_json(array('status' => 'error'));
}