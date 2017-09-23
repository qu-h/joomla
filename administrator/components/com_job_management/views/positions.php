<?php
global $mainframe;

if( !isset($page) ){
    $page = $pagination;
}
// Initialize variables
$db		=& JFactory::getDBO();
$user	=& JFactory::getUser();
$config	=& JFactory::getConfig();
$now	=& JFactory::getDate();

//$ordering = ($lists['order'] == 'section_name' || $lists['order'] == 'cc.title' || $lists['order'] == 'c.ordering');
JHTML::_('behavior.tooltip');

$controllerName = JRequest::getCmd( 'c', 'job' );


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
            <th width="5" align="left">
                <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
            </th>
            <th class="text-left">
                <?php echo JHTML::_('grid.sort',   'Name', 'c.title', @$lists['order_Dir'], @$lists['order'] ); ?>
            </th>
            <th class="text-left" width="8%">
                <?php echo JText::_( 'Company' ); ?>
            </th>
            <th  class="text-left" width="8%" nowrap="nowrap">
                <?php echo JHTML::_('grid.sort',   'Author', 'author', @$lists['order_Dir'], @$lists['order'] ); ?>
            </th>
            <th align="center" width="10">
                <?php echo JHTML::_('grid.sort',   'Date', 'c.created', @$lists['order_Dir'], @$lists['order'] ); ?>
            </th>
            <th width="3%">
                <?php echo JHTML::_('grid.sort',   'Trạng Thái', 'g.status', @$lists['order_Dir'], @$lists['order'] ); ?>
            </th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <td colspan="15">
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

            $link 	= "index.php?option=com_job_management&c=$controllerName&task=edit&cid[]=". $row->id;
            $row->checked_out = false;
            $checked 	= JHTML::_('grid.checkedout',   $row, $i );
            $row->published = $row->status ==1;
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td>
                    <?php echo $page->getRowOffset( $i ); ?>
                </td>
                <td align="center">
                    <?php echo $checked; ?>
                </td>
                <td>
                    <?php
                    if (  JTable::isCheckedOut($user->get ('id'), $row->checked_out ) ) {
                        echo $row->title;
                    } else if ($row->status == -1) {
                        echo htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8');
                        echo ' [ '. JText::_( 'Archived' ) .' ]';
                    } else {
                        ?>
                        <a href="<?php echo JRoute::_( $link ); ?>">
                            <?php echo htmlspecialchars($row->name, ENT_QUOTES); ?></a>
                        <?php
                    }
                    ?>
                </td>
                <td>
                    <?php echo $row->company_name; ?>
                </td>
                <td> <?php echo JHTML::_('jobMg.author',   $row)?></td>
                <td nowrap="nowrap">
                    <?php echo JHTML::_('date',  $row->created, JText::_('DATE_FORMAT_LC4') ); ?>
                </td>
                <td align="center"><?php echo JHTML::_('grid.published',   $row,$i) ?></td>

            </tr>
            <?php
            $k = 1 - $k;
        }
        ?>
        </tbody>
    </table>
    <input type="hidden" name="option" value="com_job_management" />
    <input type="hidden" name="task" value="<?php echo $controllerName; ?>" />
    <input type="hidden" name="returntask" value="<?php echo $controllerName; ?>" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="redirect" value="<?php echo $redirect;?>" />
    <input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>