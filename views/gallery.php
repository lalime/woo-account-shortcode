<?php

// if (!empty($images) && is_array($images) ): ?>

    <!-- <div class="woa-thumbnails">
        <?php 
        //  foreach ($images as $id) : 
        //     $url = wp_get_attachment_url( (int) $id);
            ?>
            <img name="img" src="<?php echo $url; ?>" alt="" />
        <?php // endforeach; ?>
    </div> -->

<?php // endif; ?>

<script>
    var images_ids = "<?php echo !empty($field['value'])?$field['value']:""; ?>";
    var gallery_id = "<?php echo !empty($field['id'])?$field['id']:""; ?>";
</script>
<style>
    #<?php echo !empty($field['id'])?$field['id']:""; ?> {
        display: none;
    }
</style>
<div id="woa-secudeal-wrapper">
    <div id="woa-secudeal-uploder" class="dropzone"></div>
    <?php echo wp_nonce_field('woa_secudeal_ajax_nonce', 'woa-secudeal-nonce', true, false);  ?>
</div>