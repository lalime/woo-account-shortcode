<?php
if ($codes = woo_errors()->get_error_codes()) {
    ?>
    <style>
#display-error {
    width: 400px;
    border: 1px solid #D8D8D8;
    padding: 5px;
    border-radius: 5px;
    font-family: Arial;
    font-size: 11px;
    text-transform: uppercase;
    background-color: rgb(255, 249, 242);
    color: rgb(211, 0, 0);
    text-align: center;
}
 
img {
    float: left;
}
</style>
    <div class="woas_message error" id="display-error">
        <img src="<?php echo WAS_IMG_URL; ?>/icon-nok.png" alt="error" width="25" /> 
        <!-- // Loop error codes and display errors -->
      <?php foreach($codes as $code) {
            $message = woo_errors()->get_error_message($code);
            ?>
        <span class="woo_errors"> <strong><?php echo __('Error', 'rcp')  ?></strong>: <?php echo $message  ?></span><br/>
<?php  } ?>
    </div>
<?php  
}	?>