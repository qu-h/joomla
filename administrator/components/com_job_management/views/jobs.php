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
            <th class="title">
                <?php echo JHTML::_('grid.sort',   'Title', 'c.title', @$lists['order_Dir'], @$lists['order'] ); ?>
            </th>
            <th width="1%" nowrap="nowrap">
                <?php echo JHTML::_('grid.sort',   'Published', 'c.state', @$lists['order_Dir'], @$lists['order'] ); ?>
            </th>

            <th class="title" width="8%" nowrap="nowrap">
                <?php echo JHTML::_('grid.sort',   'Job Group', 'section_name', @$lists['order_Dir'], @$lists['order'] ); ?>
            </th>

            <th  class="title" width="8%" nowrap="nowrap">
                <?php echo JHTML::_('grid.sort',   'Author', 'author', @$lists['order_Dir'], @$lists['order'] ); ?>
            </th>
            <th align="center" width="10">
                <?php echo JHTML::_('grid.sort',   'Date', 'c.created', @$lists['order_Dir'], @$lists['order'] ); ?>
            </th>

            <th width="1%" class="title">
                <?php echo JHTML::_('grid.sort',   'ID', 'c.id', @$lists['order_Dir'], @$lists['order'] ); ?>
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

            $link 	= 'index.php?option=com_job_management&task=edit&cid[]='. $row->id;

            $row->sect_link = JRoute::_( 'index.php?option=com_sections&task=edit&cid[]='. $row->sectionid );
            $row->cat_link 	= JRoute::_( 'index.php?option=com_categories&task=edit&cid[]='. $row->catid );

            $publish_up =& JFactory::getDate($row->publish_up);
            $publish_down =& JFactory::getDate($row->publish_down);
            $publish_up->setOffset($config->getValue('config.offset'));
            $publish_down->setOffset($config->getValue('config.offset'));
            if ( $now->toUnix() <= $publish_up->toUnix() && $row->state == 1 ) {
                $img = 'publish_y.png';
                $alt = JText::_( 'Published' );
            } else if ( ( $now->toUnix() <= $publish_down->toUnix() || $row->publish_down == $nullDate ) && $row->state == 1 ) {
                $img = 'publish_g.png';
                $alt = JText::_( 'Published' );
            } else if ( $now->toUnix() > $publish_down->toUnix() && $row->state == 1 ) {
                $img = 'publish_r.png';
                $alt = JText::_( 'Expired' );
            } else if ( $row->state == 0 ) {
                $img = 'publish_x.png';
                $alt = JText::_( 'Unpublished' );
            } else if ( $row->state == -1 ) {
                $img = 'disabled.png';
                $alt = JText::_( 'Archived' );
            }
            $times = '';
            if (isset($row->publish_up)) {
                if ($row->publish_up == $nullDate) {
                    $times .= JText::_( 'Start: Always' );
                } else {
                    $times .= JText::_( 'Start' ) .": ". $publish_up->toFormat();
                }
            }
            if (isset($row->publish_down)) {
                if ($row->publish_down == $nullDate) {
                    $times .= "<br />". JText::_( 'Finish: No Expiry' );
                } else {
                    $times .= "<br />". JText::_( 'Finish' ) .": ". $publish_down->toFormat();
                }
            }

            if ( $user->authorize( 'com_users', 'manage' ) ) {
                if ( $row->created_by_alias ) {
                    $author = $row->created_by_alias;
                } else {
                    $linkA 	= 'index.php?option=com_users&task=edit&cid[]='. $row->created_by;
                    $author = '<a href="'. JRoute::_( $linkA ) .'" title="'. JText::_( 'Edit User' ) .'">'. $row->author .'</a>';
                }
            } else {
                if ( $row->created_by_alias ) {
                    $author = $row->created_by_alias;
                } else {
                    $author = $row->author;
                }
            }
            $row->state = $row->status;
            $access 	= JHTML::_('grid.access',   $row, $i, $row->state );


            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td><?php echo $page->getRowOffset( $i ); ?></td>
                <td align="center"><?=JHTML::_('grid.checkedout',   $row, $i );?></td>
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

                <td align="center"><?=$access?></td>


                <td>
                    <a href="<?php echo $row->sect_link; ?>" title="<?php echo JText::_( 'Edit Group' ); ?>">
                        <?php echo $row->section_name; ?></a>
                </td>

                <td>
                    <?php echo $author; ?>
                </td>
                <td nowrap="nowrap">
                    <?php echo JHTML::_('date',  $row->created, JText::_('DATE_FORMAT_LC4') ); ?>
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