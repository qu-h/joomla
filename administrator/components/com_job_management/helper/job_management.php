<?php
class JHTMLJobManagement
{
	function Legend( )
	{
		?>
		<table cellspacing="0" cellpadding="4" border="0" align="center">
		<tr align="center">
			<td>
			<img src="images/publish_y.png" width="16" height="16" border="0" alt="<?php echo JText::_( 'Pending' ); ?>" />
			</td>
			<td>
			<?php echo JText::_( 'Published, but is' ); ?> <u><?php echo JText::_( 'Pending' ); ?></u> |
			</td>
			<td>
			<img src="images/publish_g.png" width="16" height="16" border="0" alt="<?php echo JText::_( 'Visible' ); ?>" />
			</td>
			<td>
			<?php echo JText::_( 'Published and is' ); ?> <u><?php echo JText::_( 'Current' ); ?></u> |
			</td>
			<td>
			<img src="images/publish_r.png" width="16" height="16" border="0" alt="<?php echo JText::_( 'Finished' ); ?>" />
			</td>
			<td>
			<?php echo JText::_( 'Published, but has' ); ?> <u><?php echo JText::_( 'Expired' ); ?></u> |
			</td>
			<td>
			<img src="images/publish_x.png" width="16" height="16" border="0" alt="<?php echo JText::_( 'Finished' ); ?>" />
			</td>
			<td>
			<?php echo JText::_( 'Not Published' ); ?> |
			</td>
			<td>
			<img src="images/disabled.png" width="16" height="16" border="0" alt="<?php echo JText::_( 'Archived' ); ?>" />
			</td>
			<td>
			<?php echo JText::_( 'Archived' ); ?>
			</td>
		</tr>
		<tr>
			<td colspan="10" align="center">
			<?php echo JText::_( 'Click on icon to toggle state.' ); ?>
			</td>
		</tr>
		</table>
		<?php
	}

	static function update_users_link($object="job",$link_id=0){
        $db		= & JFactory::getDBO();

        $query = "DELETE FROM #__jobmanagement_uid_map WHERE `group` = '".$object."' AND group_id =". $link_id;
        $db->setQuery($query);
        if (!$db->query())
        {
            JError::raiseError( 500, $db->getErrorMsg() );
            return false;
        }

        $userSelect = JRequest::getVar( 'users', array(), 'post', 'array' );

        if( !empty($userSelect) ){
            foreach ($userSelect AS $u){
                $add_uid_query = "INSERT INTO `#__jobmanagement_uid_map` (`id`, `group`, `group_id`, `uid`) VALUES (0, '$object', $link_id,$u)";
                $db->setQuery($add_uid_query);
                if (!$db->query())
                {
                    JError::raiseError( 500, $db->getErrorMsg() );
                    return false;
                }
            }
        }
    }

    function update_position_permission($position_id=0){
        $db		= & JFactory::getDBO();
        $table = & JTable::getInstance('JobsPermission');
        $position_id = (int)$position_id;
        if( $position_id < 1 )
            return false;

        $query = "UPDATE  ".$table->_tbl." SET `value` = '0' WHERE `position_id` = $position_id";
        $db->setQuery($query);
        if (!$db->query())
        {
            JError::raiseError( 500, $db->getErrorMsg() );
            return false;
        }

        $userSelect = JRequest::getVar( 'role', array(), 'post', 'array' );

        if( !empty($userSelect) ){
            foreach ($userSelect AS $u){
                $db->setQuery("SELECT * FROM ".$table->_tbl." WHERE `position_id` = $position_id AND role='$u' ");
                if( $db->loadResult() > 0 ){
                    $db->setQuery("UPDATE  ".$table->_tbl." SET `value` = '1' WHERE `position_id` = $position_id AND role='$u' ");
                } else {
                    $db->setQuery("INSERT INTO ".$table->_tbl." (`id`, `position_id`, `role` , value) VALUES (0, $position_id,'$u', 1)");
                }

                if (!$db->query())
                {
                    JError::raiseError( 500, $db->getErrorMsg() );
                    return false;
                }
            }
        }
    }

    static function Cancel($sub_controller = "job"){
        global $mainframe;
        JRequest::checkToken() or jexit( 'Invalid Token' );
        $mainframe->redirect("index.php?option=com_job_management&c=$sub_controller");
    }
}