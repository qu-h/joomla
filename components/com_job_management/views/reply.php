<?php

JHTML::stylesheet("css/bootstrap.min.css","components/com_job_management/assets/");
$option	= JRequest::getCmd( 'option' );
$editor = &JFactory::getEditor();
?>
<h4>Thảo luận công việc</h4>
<form action="index.php" method="post" name="adminForm" enctype="multipart/form-data">
    <div class="row p-3">
        <div class="col-md-12">
            <div class="form-group">
                <label><?php echo JText::_( 'Tiêu đề' ); ?></label>
                <input class="form-control" type="text" name="title" id="title"  value="" placeholder="Tiêu đề" />
            </div>
        </div>


        <div class="col-md-12">
            <div class="form-group">
                <label><?php echo JText::_( 'File đính kèm (nếu có)' ); ?></label>
                <input class="form-control" type="file" name="file" />
            </div>

        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label><?php echo JText::_( 'Nội dung' ); ?></label>
                <textarea name="content" class="form-control" ></textarea>
            </div>
        </div>

    </div>
    <input type="hidden" name="option" value="<?php echo $option;?>" />
    <input type="hidden" name="task" value="save" />
    <input type="hidden" name="jid" value="<?php echo $id?>" />
    <input type="hidden" name="c" value="reply" />
    <?php echo JHTML::_( 'form.token' ); ?>

    <button type="submit" class="btn btn-primary">Gửi thảo luận</button>
</form>


