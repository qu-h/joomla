<h1>Thêm mới công việc</h1>
<div class="">
<form action="index.php" method="post" name="adminForm">

    <div class="row p-3">
        <div class="col-md-12">
            <div class="form-group">
                <label><?php echo JText::_( 'Tên công việc' ); ?></label>
                <input class="form-control" type="text" name="title" id="title"  value="<?php echo $this->job->title; ?>" placeholder="Tên công việc" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label><?php echo JText::_( 'Công Ty' ); ?></label>
                <?php

                echo JHTMLJobMg::CompanySelelect($this->job->companyid,"companyid",true);
                ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label><?php echo JText::_( 'Nhóm việc' ); ?></label>
                <?php echo JHTMLJobMg::GroupSelect($this->job,true,"groupid"); ?>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label><?php echo JText::_( 'Người tham gia' ); ?></label>
                <?php

                echo JHTMLJobMg::SelectMultiUsers(NULL,"users",0,'job');
                ?>
            </div>
        </div>

        <div class="col-md-6">
            <?php echo JHTML::_('JobForm.inputdate', "date_start",'Bắt Đầu',$this->job->date_start); ?>
        </div>
        <div class="col-md-6">
            <?php echo JHTML::_('JobForm.inputdate', "date_end",'Kết Thúc',$this->job->date_end); ?>
        </div>
        <div class="col-md-6">
            <?php echo JHTML::_('JobForm.level', "level",'Mức Độ',$this->job->level); ?>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label><?php echo JText::_( 'Mô tả' ); ?></label>
                <textarea name="content" class="form-control" ></textarea>
            </div>

        </div>
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary">Tạo Công Việc</button>
        </div>
    </div>



    <input type="hidden" name="status" value="1" />
    <input type="hidden" name="id" value="0" />
    <input type="hidden" name="cid[]" value="0" />
    <input type="hidden" name="version" value="0" />
    <input type="hidden" name="mask" value="0" />
    <input type="hidden" name="option" value="com_job_management" />
    <input type="hidden" name="task" value="save" />
    <input type="hidden" name="c" value="job" />
    <input type="hidden" name="view" value="job" />
    <input type="hidden" name="Itemid" value="<?php echo JRequest::getCmd( 'Itemid' ); ?>" />

    <?php echo JHTML::_( 'form.token' ); ?>

</form>
</div>