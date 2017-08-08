<?php
$colLeft = 3;
?>
<div class="clearfix p-3">
    <div class="row">
        <h4>Chi Tiết Công Việc</h4>
    </div>
    <div class="form-group row">
        <label class="col-<?php echo $colLeft ?> col-form-label"><?php echo JText::_( 'Tên công việc' ); ?></label>
        <div class="col-<?php echo 12-$colLeft ?>">
            <p class="form-control-static"><?php echo $row->title ?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group row">
                <label class="col-<?php echo $colLeft*2 ?> col-form-label"><?php echo JText::_( 'Nhóm công việc' ); ?></label>
                <div class="col-<?php echo 12-$colLeft*2 ?>">
                    <p class="form-control-static"><?php echo $row->group_name ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group row">
                <label class="col-<?php echo $colLeft*2 ?> col-form-label"><?php echo JText::_( 'Cấp độ' ); ?></label>
                <div class="col-<?php echo 12-$colLeft*2 ?>">
                    <p class="form-control-static"><?php echo JHTMLJobMg::level($row) ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group row">
                <label class="col-<?php echo $colLeft*2 ?> col-form-label"><?php echo JText::_( 'Ngày Bắt Đầu' ); ?></label>
                <div class="col-<?php echo 12-$colLeft*2 ?>">
                    <p class="form-control-static"><?php echo JHTML::_('date',  $row->date_start, JText::_('DATE_FORMAT_LC4') ); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group row">
                <label class="col-<?php echo $colLeft*2 ?> col-form-label"><?php echo JText::_( 'Ngày Kết Thúc' ); ?></label>
                <div class="col-<?php echo 12-$colLeft*2 ?>">
                    <p class="form-control-static"><?php echo JHTML::_('date',  $row->date_end, JText::_('DATE_FORMAT_LC4') ); ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-<?php echo $colLeft ?> col-form-label"><?php echo JText::_( 'Nội Dung Công Việc' ); ?></label>
        <div class="col-<?php echo 12-$colLeft ?>">
            <p class="form-control-static"><?php echo $row->content; ?></p>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-<?php echo $colLeft ?> col-form-label"><?php echo JText::_( 'Người tham gia' ); ?></label>
        <div class="col-<?php echo 12-$colLeft ?>">
            <p class="form-control-static userfront">
                <?php if( isset($uids) && !empty($uids) ):  foreach ($uids AS $u):?>
                    <span class="fa fa-user" title="<?php echo JHTMLJobMg::GetUserDetail($u->uid) ?>"></span>
                    <?php endforeach;?>
                <?php else :?>
                    Không có người nào
                <?php endif;?>
            </p>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-12 col-form-label"><?php echo JText::_( 'Thảo luận' ); ?></label>
        <div class="col-12">
            <p class="form-control-static">
                <?php
                $id = $row->id;
                include_once JPATH_COMPONENT_ADMINISTRATOR.DS.'views/reply_items.php';
                ?>
            </p>
        </div>
    </div>

</div>