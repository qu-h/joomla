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
JHTML::_('behavior.tooltip');
JHTML::stylesheet("css/font-awesome.min.css","components/com_job_management/assets/");

?>
<form action="" method="post" name="adminForm">

    <table>
        <tr>
            <td width="100%">
                <?php echo JText::_( 'Filter' ); ?>:
                <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($lists['search']);?>" class="text_area" onchange="document.adminForm.submit();" title="<?php echo JText::_( 'Filter by title or enter article ID' );?>"/>
                <button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
                <button onclick="document.getElementById('search').value='';this.form.getElementById('filter_sectionid').value='-1';this.form.getElementById('catid').value='0';this.form.getElementById('filter_authorid').value='0';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
            </td>
            <td nowrap="nowrap">
                <?php
                echo $lists['groupid'];
                echo $lists['authorid'];
                echo $lists['status'];
                ?>
            </td>
        </tr>
    </table>

    <table class="adminlist" cellspacing="1">
        <thead>
        <tr>
            <th width="5">
                <?php echo JText::_( 'Num' ); ?>
            </th>
            <th width="5">
                <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
            </th>
            <th class="text-left">
                <?php echo JHTML::_('grid.sort',   'Tên Công Việc', 'g.title', @$lists['order_Dir'], @$lists['order'] ); ?>
            </th>
            <th style="width: 5%;" class="text-center">
                <?php echo JText::_( 'Reply' ); ?>
            </th>
            <th class="text-center">
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

            <th width="3%">
                <?php echo JHTML::_('grid.sort',   'Trạng Thái', 'g.status', @$lists['order_Dir'], @$lists['order'] ); ?>
            </th>
            <th width="1%" class="title">Quá hạn</th>
            <th width="1%" class="title">Đã xem</th>
            <th width="1%" class="title">
                <?php echo JHTML::_('grid.sort',   'ID', 'c.id', @$lists['order_Dir'], @$lists['order'] ); ?>
            </th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <td colspan="17">
                <?php echo $page->getListFooter(); ?>
            </td>
        </tr>
        </tfoot>
        <tbody>
        <?php
        $k = 0;
        $nullDate = $db->getNullDate();
        for ($i=0, $n=count( $rows ); $i < $n; $i++)
        {
            $row = &$rows[$i];
            $row->published = $row->status ==1;
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td><?php echo $page->getRowOffset( $i ); ?></td>
                <td align="center"><?php echo JHTML::_('grid.checkedout',   $row, $i );?></td>
                <td>
                    <a href="<?php echo JRoute::_( 'index.php?option=com_job_management&task=edit&cid[]='. $row->id ); ?>">
                        <?php echo htmlspecialchars($row->title, ENT_QUOTES); ?></a>
                </td>
                <td align="center" >
                    <a href="<?php echo JRoute::_( 'index.php?option=com_job_management&c=reply&jid='. $row->id ); ?>" class="count_reply" >
                        <img src="images/message_f2.png">
                        <i><?php echo JHTML::_('jobMg.ReplyCount',   $row)?></i>
                    </a>
                </td>
                <td class="text-center" >
                    <span class="count_uid ">
                        <img src="images/users.png">
                        <i><?php echo JHTML::_('jobMg.UidCount',   $row)?></i>
                    </span>
                </td>
                <td align="center" nowrap="nowrap">
                    <?php echo JHTML::_('date',  $row->date_start, JText::_('DATE_FORMAT_LC4') ); ?>
                </td>
                <td align="center" nowrap="nowrap" class="text-danger" >
                    <?php
                    $date_end= JHTML::_('date',  $row->date_end, JText::_('DATE_FORMAT_LC4') );
                    if( strtotime($row->date_end) < time() && $row->status !=-1 ){
                        echo '<span class="txt-danger">'.$date_end.'</span>';
                    } else {
                        echo $date_end;
                    }
                    ?>
                </td>
                <td align="center"><?php echo JHTML::_('jobMg.level',   $row, $i)?></td>

                <td>
                    <a href="<?php echo JRoute::_( 'index.php?option=com_job_management&c=group&task=edit&cid[]='. $row->groupid ) ?>" title="<?php echo JText::_( 'Edit Group' ); ?>">
                        <?php echo $row->section_name; ?></a>
                </td>

                <td> <?php echo JHTML::_('jobMg.author',   $row)?>

                </td>
                <td align="center"><?php echo JHTMLJobMg::JobStatus($row,$i) ?></td>
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
                <td>
                    <?php echo $row->id; ?>
                </td>
            </tr>
            <?php
            $k = 1 - $k;
        }
        ?>
        </tbody>
    </table>
    <?php //JHTML::_('content.legend'); ?>

    <input type="hidden" name="option" value="com_job_management" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="redirect" value="<?php echo $redirect;?>" />
    <input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>