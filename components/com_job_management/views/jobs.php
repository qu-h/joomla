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


?>
<div class="clearfix"><div class="row">
<form action="" method="post" name="adminForm">

    <table class="table table-hover table-responsive" >
        <thead>
        <tr>

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
                <?php echo JHTML::_('grid.sort',   'Job Group', 'section_name', @$lists['order_Dir'], @$lists['order'] ); ?>
            </th>

            <th  class="title text-left" >
                <?php echo JHTML::_('grid.sort',   'Author', 'author', @$lists['order_Dir'], @$lists['order'] ); ?>
            </th>
            <th width="1%" class="title">Quá hạn</th>
            <th width="1%" class="title">Đã xem</th>

        </tr>
        </thead>

        <tbody>
        <?php
        $k = 0;
        $nullDate = $db->getNullDate();
        for ($i=0, $n=count( $rows ); $i < $n; $i++)
        {
            $row = &$rows[$i];
            $row->published = $row
            ?>
            <tr class="<?php echo "row$k"; ?>">

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
                    <?php echo JHTML::_('date',  $row->date_start, JText::_('DATE_FORMAT_LC4') ); ?>
                </td>
                <td align="center" nowrap="nowrap" class="text-danger" >
                    <?php
                    $date_end= JHTML::_('date',  $row->date_end, JText::_('DATE_FORMAT_LC4') );
                    if( strtotime($row->date_end) < time()  ){
                        echo '<span class="txt-danger">'.$date_end.'</span>';
                    } else {
                        echo $date_end;
                    }
                    ?>
                </td>
                <td align="center"><?php echo JHTML::_('jobMg.level',   $row, $i)?></td>

                <td><?php echo $row->section_name; ?></td>
                <td> <?php echo $row->author ;?></td>
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
            </tr>
            <?php
            $k = 1 - $k;
        }
        ?>
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