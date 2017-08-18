<?php
$inline = intval($inline);
if( $inline >=12 ){
    $inline = 0;
}
$onupdate = strlen($taskUpdate) > 0 ? 'onchange="javascript: submitbutton(\''.$taskUpdate.'\');"' : null;

?>

<div class="form-group <?php echo $inline > 0 ? "row" : NULL; ?>">
    <label for="date-from" class="col-form-label <?php echo $inline > 0 ? "col-$inline" : NULL; ?>"><?php echo $label; ?></label>
    <div class="input-group  <?php echo $inline > 0 ? "col-".(12-$inline) : NULL; ?>" >
        <input  <?php echo $onupdate; ?> class="form-control form_date" type="text" data-link-field="dtp_input<?php echo $name; ?>" value="<?php echo $value ?>"  data-date-format="dd/mm/yyyy" data-link-format="yyyy-mm-dd" >
        <div class="input-group-addon ">
            <span class="fa fa-calendar"></span>
        </div>

    </div>
    <input type="hidden" id="dtp_input<?php echo $name; ?>" value=""  name="<?php echo $name; ?>"  />
</div>