<?php
$colLeft = 2;
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
        <div class="col-md-3">
            <div class="form-group row">
                <label class="col-7 col-form-label"><?php echo JText::_( 'Nhóm công việc' ); ?></label>
                <div class="col-5">
                    <p class="form-control-static"><?php echo $row->group_name ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group row">
                <label class="col-<?php echo $colLeft*2 ?> col-form-label"><?php echo JText::_( 'Cấp độ' ); ?></label>
                <div class="col-<?php echo 12-$colLeft*2 ?>">
                    <p class="form-control-static"><?php echo JHTMLJobMg::level($row) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group row">
                <label class="col-6 col-form-label"><?php echo JText::_( 'Ngày Bắt Đầu' ); ?></label>
                <div class="col-6">
                    <p class="form-control-static"><?php echo JHTML::_('jobMg.DateFormat',  $row->date_start); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group row">
                <label class="col-6 col-form-label"><?php echo JText::_( 'Ngày Kết Thúc' ); ?></label>
                <div class="col-6">
                    <p class="form-control-static"><?php echo JHTML::_('jobMg.DateFormat',  $row->date_end); ?></p>
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
        <label class="col-2 col-form-label"><?php echo JText::_( 'Người giao' ); ?></label>
        <div class="col-3"><p class="form-control-static"><?php echo JHTMLJobMg::GetUserDetail($row->creator) ?></p></div>
        <label class="col-2 col-form-label"><?php echo JText::_( 'Người tham gia' ); ?></label>
        <div class="col-4">
            <p class="form-control-static userfront">
                <?php
                $uids = JHTMLJobMg::UidMapUids("job",$row->id);
                if( isset($uids) && !empty($uids) ):  foreach ($uids AS $uid):?>
                    <span class="fa fa-user" title="<?php echo JHTMLJobMg::GetUserDetail($uid) ?>"
                     

                    ></span>
                    <?php endforeach;?>
                <?php else :?>
                    Không có người nào
                <?php endif;?>
            </p>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-2 col-form-label"><?php echo JText::_( 'File đính kèm' ); ?></label>
        <div class="col-10">
            <p class="form-control-static userfront">
                <?php
                $files = explode(",",$row->files);
                $uploadPath = 'upload'.DS.'job_management'.DS;
                if( isset($files) && !empty($files) ):  foreach ($files AS $f):
                    if( strlen($f) < 3 ) continue;
                    ?>
                    <a href="/<?=$uploadPath.$f;?>" target="_blank"><i class="fa fa-file-text-o" ></i></a>
                <?php endforeach;?>
                <?php else :?>
                    Không có file nào
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