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

        $query = "DELETE FROM #__jobmanagement_".$object."_user WHERE ".$object."_id =". $link_id;
        $db->setQuery($query);
        if (!$db->query())
        {
            JError::raiseError( 500, $db->getErrorMsg() );
            return false;
        }

        $userSelect = JRequest::getVar( 'users', array(), 'post', 'array' );

        if( !empty($userSelect) ){
            foreach ($userSelect AS $u){
                $add_uid_query = "INSERT INTO `#__jobmanagement_".$object."_user` (`id`, `".$object."_id`, `uid`) VALUES (0, $link_id,$u)";
                $db->setQuery($add_uid_query);
                if (!$db->query())
                {
                    JError::raiseError( 500, $db->getErrorMsg() );
                    return false;
                }
            }
        }
    }

}