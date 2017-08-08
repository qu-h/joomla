<?php
class JHTMLJobMg extends  JHTML{
    static $level = array(
        "normal"=>"Bình Thường",
        "emergency"=>"Khẩn Cấp"
    );
    function level( &$row){
        $txt = "";
        if( isset($row->level) ){
            if( array_key_exists($row->level,self::$level) ){
                $txt = self::$level[$row->level];
            }
        }
        return $txt;
    }
    function author(&$row){
        $user	=& JFactory::getUser();
        if ( $user->authorize( 'com_users', 'manage' ) ) {

            $linkA 	= 'index.php?option=com_users&task=edit&cid[]='. $row->creator;
            $author = '<a href="'. JRoute::_( $linkA ) .'" title="'. JText::_( 'Edit User' ) .'">'. $row->author .'</a>';
        } else {
            $author = $row->author;
        }
        return $author;
    }

    function PublishSelelect($selected=null,$name="status"){
        $options = array();
        $options[] = (object)array("val"=>1,"title"=>JText::_( 'Publish' ));
        $options[] = (object)array("val"=>0,"title"=>JText::_( 'Unpublish' ));
        return JHTML::_('select.genericlist',  $options, $name, 'class="form-control custom-select" size="1"', 'val', 'title', $selected);
    }


    function AuthorSelect($inputname="filter_authorid",$selected_id=0){
        $query = 'SELECT u.id, u.name' .
            ' FROM #__users AS c' .
            ' LEFT JOIN #__users AS u ON u.id = c.created_by' .
            ' GROUP BY u.id' .
            ' ORDER BY u.name, u.id';
        $authors[] = JHTML::_('select.option', '0', '- '.JText::_('Select Author').' -', 'id', 'name');
        $db	    = & JFactory::getDBO();
        $db->setQuery($query);
        $authors = array_merge($authors, $db->loadObjectList());
        return JHTML::_('select.genericlist',  $authors, $inputname, 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'created_by', 'name', $selected_id);

    }

    function groupSelect($selected_id=-1,$submit=false,$name=NULL){
        $groups[] = JHTML::_('select.option', '-1', JText::_( '-- Chọn nhóm Công việc --' ), 'id', 'title');

        $query = 'SELECT g.id, g.title' .
            ' FROM #__jobmanagement_group AS g' .
            ' WHERE g.status > 0'.
            ' ORDER BY g.title';
        $db	= & JFactory::getDBO();
        $db->setQuery($query);
        $groups = array_merge($groups, $db->loadObjectList());
        //return $groups;

        if( is_null($name) ){
            $name = "groupid";
        }
        $javascript = $submit ? 'onchange="document.adminForm.submit();"' : NULL;
        return JHTML::_('select.genericlist',  $groups, $name, 'class="form-control custom-select" size="1" '.$javascript, 'id', 'title', intval($selected_id));
    }

    function SelectMultiUsers($selected=array(),$name="uid",$job_id=0){
        $query = 'SELECT u.name, u.username, u.id AS uid, g.name AS groupname' .
            ' FROM #__users AS u'

            . ' INNER JOIN #__core_acl_aro AS aro ON aro.value = u.id'
            . ' INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = aro.id'
            . ' INNER JOIN #__core_acl_aro_groups AS g ON g.id = gm.group_id'
            .' GROUP BY u.id'
            .' ORDER BY u.name, u.id';
        $db	    = & JFactory::getDBO();
        $db->setQuery($query);
        $users = $db->loadObjectList();

        if( empty($selected) && $job_id > 0){
            $selected = array();
            $get_uids_query = "SELECT uid FROM #__jobmanagement_job_user WHERE job_id = $job_id";
            $db->setQuery($get_uids_query);

            $uids = $db->loadObjectList();

            if( !empty($uids) ) foreach ($uids AS $u){
                $selected[] = $u->uid;
            }
        }

        include_once JPATH_BASE.DS.'components/com_job_management/views/ui/SelectMultiUsers.php';
    }

    function UidCount(&$row){
        $count = 0;
        if( $row->id > 0 ){
            $job_id = $row->id;
            $get_uids_query = "SELECT uid FROM #__jobmanagement_job_user WHERE job_id = $job_id";
            $db	    = & JFactory::getDBO();
            $db->setQuery($get_uids_query);

            $count = count($db->loadObjectList());
        }
        return $count;
    }

    function ReplyCount(&$row){
        $count = 0;
        if( $row->id > 0 ){
            $job_id = $row->id;
            $query = "SELECT * FROM #__jobmanagement_reply WHERE job_id = $job_id";
            $db	    = & JFactory::getDBO();
            $db->setQuery($query);
            if (!$db->query())
            {
                JError::raiseError( 500, $db->getErrorMsg() );
                return false;
            }
            $count = count($db->loadObjectList());
        }
        return $count;
    }

    function ReplyJobTitle($job_id=0){
        $title = "";

        $get_uids_query = "SELECT * FROM #__jobmanagement_job WHERE id = $job_id";
        $db	    = & JFactory::getDBO();
        $db->setQuery($get_uids_query);
        if (!$db->query())
        {
            JError::raiseError( 500, $db->getErrorMsg() );
            return false;
        }
        $row = $db->loadObject();


        if( !empty($row) ){
            $title = "<span class='jobtitle'>".$row->title."</span>";
        }
        return $title;
    }

    function GetUserDetail($uid=0){
        $txt = "";
        $query = 'SELECT u.name, u.username, u.id AS uid, g.name AS groupname' .
            ' FROM #__users AS u'

            . ' INNER JOIN #__core_acl_aro AS aro ON aro.value = u.id'
            . ' INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = aro.id'
            . ' INNER JOIN #__core_acl_aro_groups AS g ON g.id = gm.group_id'

            .' WHERE u.id = '.$uid
            .' GROUP BY u.id'
            .' ORDER BY u.name, u.id';
        $db	    = & JFactory::getDBO();
        $db->setQuery($query);
        if (!$db->query())
        {
            JError::raiseError( 500, $db->getErrorMsg() );
            return false;
        }
        $user = $db->loadObject();
        if( !empty($user) ){
            $txt = $user->name;
        }

        return $txt;
    }
}