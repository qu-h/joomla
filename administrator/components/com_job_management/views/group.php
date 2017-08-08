<?php
JHTML::stylesheet("css/bootstrap.min.css","components/com_job_management/assets/");
?>
<form action="index.php" method="post" name="adminForm">
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label for="title"><?php echo JText::_( 'Tên Nhóm công việc' ); ?></label>
                <input class="form-control" type="text" name="title" id="title"maxlength="255" value="<?php echo $row->title; ?>" />
            </div>
            <div class="form-group">
                <label><?php echo JText::_( 'Published' ); ?></label>
                <?php echo JHTMLJobMg::PublishSelelect($row->status,"status"); ?>
            </div>
            <div class="form-group">
                <label><?php echo JText::_( 'Select User' ); ?></label>
                <?php echo JHTMLJobMg::SelectMultiUsers(NULL,"users",$row->id,'group'); ?>
            </div>
        </div>
        <div class="col-md-4 pt-3" style="border: 1px dashed silver;">

            <?php if ( $row->id ) :?>
            <div class="form-group row">
                <label class="col-3"><?php echo JText::_( 'Group ID' ); ?></label>
                <p class="col-9 form-control-static">
                <?php echo $row->id; ?>
                </p>
            </div>
            <?php endif;?>
            <div class="form-group row">
                <label class="col-3"><?php echo JText::_( 'Status' ); ?></label>
                <p class="form-control-static col-9">
                <?php echo $row->status > 0 ? JText::_( 'Published' ) : ($row->status < 0 ? JText::_( 'Archived' ) : JText::_( 'Draft Unpublished' ) );?>
                </p>
            </div>
            <div class="form-group row">
                <label class="col-3"><?php echo JText::_( 'Created' ); ?></label>
                <p class="form-control-static col-9">
                <?php
                if ( $row->created == $nullDate ) {
                    echo JText::_( 'New document' );
                } else {
                    echo JHTML::_('date',  $row->created,  JText::_('DATE_FORMAT_LC2') );
                }
                ?>
                </p>
            </div>

            <div class="form-group row">
                <label class="col-3"><?php echo JText::_( 'Modified' ); ?></label>
                <p class="form-control-static col-9">
                <?php
                if ( $row->modified == $nullDate ) {
                    echo JText::_( 'Not modified' );
                } else {
                    echo JHTML::_('date',  $row->modified, JText::_('DATE_FORMAT_LC2'));
                }
                ?>
                </p>
        </div>
        </div>
    </div>

    <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
    <input type="hidden" name="cid[]" value="<?php echo $row->id; ?>" />
    <input type="hidden" name="version" value="<?php echo $row->version; ?>" />
    <input type="hidden" name="mask" value="0" />
    <input type="hidden" name="option" value="<?php echo $option;?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="c" value="group" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>
<?=JHTML::_('behavior.keepalive');?>