<style>
img {
    float: left;
}
 
#display-success {
    width: 100%;
    border: 1px solid #D8D8D8;
    padding: 10px;
    border-radius: 5px;
    font-family: Arial;
    font-size: 11px;
    text-transform: uppercase;
    background-color: rgb(236, 255, 216);
    color: green;
    text-align: left;
    margin-bottom: 30px;
}
 
#display-success img
{
    position: relative;
    bottom: 5px;
}
</style>
<div class="woas_message success" id="display-success"> 
    <img src="<?php echo WAS_IMG_URL; ?>/icon-ok.png" alt="Success" width="25" />
    <span><?php echo $message; ?></span>
</div>