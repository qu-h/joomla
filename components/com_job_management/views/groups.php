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

$ordering = ($lists['order'] == 'section_name' || $lists['order'] == 'cc.title' || $lists['order'] == 'c.ordering');
JHTML::_('behavior.tooltip');



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
            <th width="5">
                <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
            </th>
            <th class="title">
                <?php echo JHTML::_('grid.sort',   'Title', 'c.title', @$lists['order_Dir'], @$lists['order'] ); ?>
            </th>

            <th  class="title" width="8%" nowrap="nowrap">
                <?php echo JHTML::_('grid.sort',   'Author', 'author', @$lists['order_Dir'], @$lists['order'] ); ?>
            </th>
            <th align="center" width="10">
                <?php echo JHTML::_('grid.sort',   'Date', 'c.created', @$lists['order_Dir'], @$lists['order'] ); ?>
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

            $link 	= 'index.php?option=com_job_management&c=group&task=edit&cid[]='. $row->id;

            $checked 	= JHTML::_('grid.checkedout',   $row, $i );
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
                    } else if ($row->state == -1) {
                        echo htmlspecialchars($row->title, ENT_QUOTES, 'UTF-8');
                        echo ' [ '. JText::_( 'Archived' ) .' ]';
                    } else {
                        ?>
                        <a href="<?php echo JRoute::_( $link ); ?>">
                            <?php echo htmlspecialchars($row->title, ENT_QUOTES); ?></a>
                        <?php
                    }
                    ?>
                </td>
                <td> <?php echo JHTML::_('jobMg.author',   $row)?></td>
                <td nowrap="nowrap">
                    <?php echo JHTML::_('date',  $row->created, JText::_('DATE_FORMAT_LC4') ); ?>
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
    <input type="hidden" name="returntask" value="group" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="redirect" value="<?php echo $redirect;?>" />
    <input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>