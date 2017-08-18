<?php
$inline = intval($inline);
if( $inline >=12 ){
    $inline = 0;
}
?>

<div class="form-group <?php echo $inline > 0 ? "row" : NULL; ?>">
    <label for="date-from" class="col-form-label <?php echo $inline > 0 ? "col-$inline" : NULL; ?>"><?php echo $label; ?></label>
    <div class="input-group  <?php echo $inline > 0 ? "col-".(12-$inline) : NULL; ?>"  data-date=""  data-link-field="dtp_input1" >
        <?php echo $input_html ?>
    </div>
</div>