<?php
global $mainframe;

if( !isset($page) ){
    $page = $pagination;
}
// Initialize variables
$db		=& JFactory::getDBO();

$config	=& JFactory::getConfig();
$now	=& JFactory::getDate();

$ordering = ($lists['order'] == 'section_name' || $lists['order'] == 'cc.title' || $lists['order'] == 'c.ordering');

$close_allow = JHTML::_('jobMg.isClose');
$add_allow = JHTML::_('jobMg.isAdd');
$add_allow = true;
?>
<div class="clearfix"><div class="row">
        <?php if ( $add_allow ): ?>
            <div class="col-4">
                <a class="btn btn-success text-white" href="<?php echo JRoute::_( 'index.php?option=com_job_management&task=add' ); ?>" >Thêm Công Việc</a>

                <a class="btn btn-success text-white" href="<?php echo JRoute::_( 'index.php?option=com_job_management&view=jobs&status=-1' ); ?>" >Đã xóa</a>
            </div>
        <?php endif; ?>
        <div class="col-4">
            <div class="form-group row">
                <label for="date-from" class="col-4 col-form-label">Từ ngày</label>
                <div class="col-8 input-group">
                    <input class="form-control date" type="text" value="" id="date-from" name="date_from">
                    <div class="input-group-addon fa fa-calendar"></div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="form-group row">
                <label for="date-to" class="col-4 col-form-label">Đến ngày</label>
                <div class="col-8 input-group">
                    <input class="form-control date" type="text" value="" id="date-to" name="date_to">
                    <div class="input-group-addon fa fa-calendar"></div>
                </div>
            </div>
        </div>

<form action="" method="post" name="adminForm" class="col-12">

    <table class="table table-hover table-responsive" >
        <thead>
        <tr>
            <td style="display: none">
                <?php echo JText::_( 'Num' ); ?>
            </td>
            <th class="text-left">
                <?php echo JHTML::_('grid.sort',   'Tên Công Việc', 'g.title', @$lists['order_Dir'], @$lists['order'] ); ?>
            </th>
            <th style="width: 5%;" class="text-center">
                <?php echo JText::_( 'Reply' ); ?>
            </th>
            <th style="width: 5%;" class="text-center">
                <?php echo JText::_( 'User' ); ?>
            </th>

            <th width="2%" nowrap="nowrap">
                <?php echo JHTML::_('grid.sort',   'Ngày Bắt đầu', 'g.date_start', @$lists['order_Dir'], @$lists['order'] ); ?>
            </th>
            <th width="2%" nowrap="nowrap">
                <?php echo JHTML::_('grid.sort',   'Ngày Kết Thúc', 'g.date_end', @$lists['order_Dir'], @$lists['order'] ); ?>
            </th>
            <th  nowrap="nowrap" style="width: 8%;">
                <?php echo JHTML::_('grid.sort',   'Cấp Độ', 'g.access', @$lists['order_Dir'], @$lists['order'] ); ?>
            </th>
            <th class="text-left" nowrap="nowrap" >
                <?php echo JHTML::_('grid.sort',   'Nhóm Việc', 'section_name', @$lists['order_Dir'], @$lists['order'] ); ?>
            </th>

            <th width="1%" class="title">Quá hạn</th>
            <th width="1%" class="title">Đã xem</th>
            <?php if ( $close_allow ): ?>
            <th>Đóng</th>
            <?php endif; ?>
        </tr>
        </thead>

        <tbody>
        <?php
        $k = 0;
        $nullDate = $db->getNullDate();
        if( count( $rows ) > 0 ):

        for ($i=0, $n=count( $rows ); $i < $n; $i++)
        {
            $row = &$rows[$i];
            $row->published = $row
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td style="display: none"><?php echo JHTML::_('grid.checkedout',   $row, $i );?></td>
                <td>
                    <a href="<?php echo JRoute::_( 'index.php?option=com_job_management&task=view&jid='. $row->id ); ?>">
                        <?php echo htmlspecialchars($row->title, ENT_QUOTES); ?></a>
                </td>
                <td align="center" >
                    <a href="<?php echo JRoute::_( 'index.php?option=com_job_management&c=reply&jid='. $row->id ); ?>" class="count_reply" >
                        <span class="fa fa-comments-o font-size-23" ></span>
                        <i><?php echo JHTML::_('jobMg.ReplyCount',   $row)?></i>
                    </a>
                </td>
                <td class="text-center" >
                    <span class="count_uid ">
                        <span class="fa fa-user font-size-23"></span>
                        <i><?php echo JHTML::_('jobMg.UidCount',   $row)?></i>
                    </span>
                </td>
                <td align="center" nowrap="nowrap">
                    <?php
                    echo JHTML::_('jobMg.DateFormat',   $row->date_start);
                    ?>
                </td>
                <td align="center" nowrap="nowrap" >
                    <?php
                    $date_end= JHTML::_('jobMg.DateFormat',  $row->date_end);
                    if( strtotime($row->date_end) < time()  ){
                        echo '<span class="txt-danger">'.$date_end.'</span>';
                    } else {
                        echo $date_end;
                    }
                    ?>
                </td>
                <td align="center"><?php echo JHTML::_('jobMg.level',   $row, $i)?></td>

                <td><?php echo $row->section_name; ?></td>

                <td class="userfront">
                    <?php if( strtotime($row->date_end) < time() && $row->status !=-1 ):?>
                        <i class="fa fa-ban red "></i>
                    <?php else : ?>
                        <i class="fa fa-check"></i>
                    <?php endif; ?>
                </td>
                <td class="userfront">
                    <?php if( $row->viewed ==1 ):?>
                        <i class="fa fa-eye"></i>
                    <?php else : ?>
                        <i class="fa fa-eye-slash red"></i>
                    <?php endif; ?>
                </td>
                <?php if ( $close_allow ): ?>
                <td align="center" class="userfront" ><?php echo JHTMLJobMg::JobClose($row,$i) ?></td>
                <?php endif; ?>
            </tr>
            <?php
            $k = 1 - $k;
        }
        else : ?>
            <tr><td class="text-center" colspan="10">Không có công việc nào</td></tr>
        <?php endif; ?>
        </tbody>
    </table>


    <input type="hidden" name="option" value="com_job_management" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="redirect" value="<?php echo $redirect;?>" />
    <input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>
</div></div>