<?php
$query = "SELECT * FROM #__jobmanagement_reply WHERE job_id = $id";
$db	    = & JFactory::getDBO();
$db->setQuery($query);
$replys = $db->loadObjectList();

?>
<table class="table table-bordered">
    <thead>
    <tr>
        <th>#</th>
        <th class="text-center">Attach</th>
        <th class="text-center">Ngày cập nhật</th>
        <th>Người thực hiện</th>
        <th>Tiêu đề</th>
        <th>Chi tiết</th>
    </tr>
    </thead>
    <tbody>
    <?php if( isset($replys) ) foreach ($replys AS $k=>$r): ?>
        <tr>
            <td><?php echo $k+1; ?></td>
            <td class="text-center">
                <?php if( strlen($r->file) > 0 ) :?>
                    <a href="/upload/job_management/<?php echo $r->file; ?>" >
                        <img src="images/upload_f2.png">
                    </a>
                <?php endif;?>
            </td>
            <td class="text-center"><?php echo JHTML::_('date',  $r->created, JText::_('DATE_FORMAT_LC4') ); ?></td>
            <td><?php echo JHTMLJobMg::GetUserDetail($r->creator) ?></td>
            <td><?php echo $r->title ?></td>
            <td><?php echo $r->content ?></td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>