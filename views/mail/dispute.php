<?php 
?>

<b>Order No  : </b> <?php echo $transaction->get_id(); ?> <br/>
<b>From  : </b>  <?php echo $current_user->user_firstname(); ?>  <?php echo $current_user->user_lastname(); ?> (<?php echo $current_user->user_email(); ?>) <br/>
<b>Description  : </b> <?php echo $description; ?> <br/>