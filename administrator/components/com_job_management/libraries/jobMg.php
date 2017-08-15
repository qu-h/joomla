<?php
class JHTMLJobMg extends  JHTML{

    function JHTMLJobMg(){
        //$this->LoadRole();
    }
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

    function CompanySelelect($selected=null,$name="company",$submit=false){
        $query = 'SELECT c.id, c.name' .
            ' FROM #__jobmanagement_company AS c'
            .' WHERE c.status=1'
            .' GROUP BY c.id'
            .' ORDER BY c.name, c.id';
        $companys[] = JHTML::_('select.option', '0', '- '.JText::_('Select Company').' -', 'id', 'name');
        $db	    = & JFactory::getDBO();
        $db->setQuery($query);
        if (!$db->query())
        {
            JError::raiseError( 500, $db->getErrorMsg() );
            return false;
        }
        $companys = array_merge($companys, $db->loadObjectList());

        $javascript = $submit ? 'onchange="javascript: submitbutton(\'updateformval\');"' : NULL;

        return JHTML::_('select.genericlist',  $companys, $name, 'class="form-control custom-select" size="1" '.$javascript, 'id', 'name', $selected);

    }



    function AuthorSelect($inputname="filter_authorid",$selected_id=0){
        $query = 'SELECT u.id, u.name' .
            ' FROM #__users AS u' .
            ' GROUP BY u.id' .
            ' ORDER BY u.name, u.id';
        $authors[] = JHTML::_('select.option', '0', '- '.JText::_('Select Author').' -', 'id', 'name');
        $db	    = & JFactory::getDBO();
        $db->setQuery($query);
        if (!$db->query())
        {
            JError::raiseError( 500, $db->getErrorMsg() );
            return false;
        }
        $authors = array_merge($authors, $db->loadObjectList());
        return JHTML::_('select.genericlist',  $authors, $inputname, 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id', 'name', $selected_id);
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
        $javascript = $submit ? 'onchange="javascript: submitbutton(\'updateformval\');"' : NULL;
        return JHTML::_('select.genericlist',  $groups, $name, 'class="form-control custom-select" size="1" '.$javascript, 'id', 'title', intval($selected_id));
    }

    function SelectMultiUsers($selected=array(),$name="uid",$link_id=0,$object="job"){
        $db	    = & JFactory::getDBO();
        $groupid				= JRequest::getVar( 'groupid', 0, '', 'int' );
        $control			= JRequest::getCmd( 'c', 'job' );
        $option			= JRequest::getCmd( 'option', 'com_job_management' );
        $ids = NULL;

        if( ($control=="job" OR $control=="group")  )
        {
            $ids = array();
            switch ($control){
                case "job":
                    $group = "group";
                    break;
                case "group":
                    $group = "company";
                    break;
                default:
                    $group = null;
                    break;

            }
            if( $group != null ){
                $ids = self::UidMapUids($group,$groupid);
                if( empty($ids) ){
                    $ids[] = 0;
                }
            }

        }

        $query = 'SELECT u.name, u.username, u.id AS uid, g.name AS groupname' .
            ' FROM #__users AS u'
            . ' INNER JOIN #__core_acl_aro AS aro ON aro.value = u.id'
            . ' INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = aro.id'
            . ' INNER JOIN #__core_acl_aro_groups AS g ON g.id = gm.group_id';
        if( !empty($ids) ) {
            $query .= " WHERE u.id IN (".implode(",", $ids).")";
        }
        $query .= " GROUP BY u.id ORDER BY u.name, u.id";
        $db->setQuery($query);
        $users = $db->loadObjectList();

        if( empty($selected) && $link_id > 0){
            $selected = array();
            $get_uids_query = "SELECT uid FROM #__jobmanagement_uid_map WHERE `group`='$object' AND `group_id` = $link_id";
            $db->setQuery($get_uids_query);
            if (!$db->query())
            {
                JError::raiseError( 500, $db->getErrorMsg() );
                return false;
            }
            $uids = $db->loadObjectList();

            if( !empty($uids) ) foreach ($uids AS $u){
                $selected[] = $u->uid;
            }
        }

        include_once JPATH_BASE.DS.'components/com_job_management/views/ui/SelectMultiUsers.php';
    }

    static function UidMapUids($group="job",$group_id=0){
        $db	    = & JFactory::getDBO();
        $UidMapTable = & JTable::getInstance('JobsUidMap');

        $group_uid_query = "SELECT uid FROM ".$UidMapTable->_tbl." WHERE `group`='$group' AND `group_id` = $group_id";

        $db->setQuery($group_uid_query);
        if (!$db->query())
        {
            JError::raiseError( 500, $db->getErrorMsg() );
            return false;
        }
        $uids = array_map(function ($object) { return $object->uid; }, $db->loadObjectList());

        return $uids;
    }

    function UidMapGroupIds($group="job",$uid=0){
        $db	    = & JFactory::getDBO();
        $UidMapTable = & JTable::getInstance('JobsUidMap');

        $group_uid_query = "SELECT group_id FROM ".$UidMapTable->_tbl." WHERE `group`='$group' AND `uid` = $uid";

        $db->setQuery($group_uid_query);
        if (!$db->query())
        {
            JError::raiseError( 500, $db->getErrorMsg() );
            return false;
        }
        $group_ids = array_map(function ($object) { return $object->group_id; }, $db->loadObjectList());
        return $group_ids;
    }

    function UidCount(&$row, $type="job"){
        $count = 0;
        $types_allow = array("company",'job','group');
        if( !in_array($type,$types_allow) ){
            return $count;
        }
        if( $row->id > 0 ){
            $job_id = $row->id;
            $get_uids_query = "SELECT uid FROM #__jobmanagement_uid_map WHERE `group` = '$type' AND `group_id` = $job_id";
            $db	    = & JFactory::getDBO();
            $db->setQuery($get_uids_query);

            if (!$db->query())
            {
                JError::raiseError( 500, $db->getErrorMsg() );
                return false;
            }
            $count = count($db->loadObjectList());
        }
        return $count;
    }
    function UidGroupCount(&$row){
        $count = 0;
        if( $row->id > 0 ){
            $get_uids_query = "SELECT uid FROM #__jobmanagement_group_user WHERE group_id = ".$row->id;
            $db	    = & JFactory::getDBO();
            $db->setQuery($get_uids_query);
            if (!$db->query())
            {
                JError::raiseError( 500, $db->getErrorMsg() );
                return false;
            }
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

    function JobStatus( &$row, $i ,$prefix='')
    {
        switch ($row->status){
            case 1:
                $img = "tick.png";
                $task = "unpublish";
                $alt = JText::_( 'Published' );
                $action = JText::_( 'Unpublish Item' );
                break;
            case 0:
                $img = "publish_x.png";
                $task = "publish";
                $alt = JText::_( 'Unpublished' );
                $action = JText::_( 'Publish Item' );
                break;
            case -1:
                $img = "checked_out.png";
                $task = "";
                $alt = JText::_( 'Đã đóng' );
                $action = JText::_( 'Đã đóng' );
                break;
            default:
                $img = "checked_out.png";
                $task = null;
                $alt = null;
                $action = null;
                break;
        }


        if( strlen($action) < 1  ){
            return NULL;
        }
        $html = '<a href="javascript:void(0);" title="'. $action .'" ';
        if( strlen($task) > 0 ){
            $html .= ' onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$task .'\')" ';
        }
        $html .= ' >';
        $html .= '<img src="images/'. $img .'" border="0" alt="'. $alt .'" />';
        $html .= '</a>';
        return $html;
    }

    function JobClose(&$row, $i ,$prefix='')
    {
        switch ($row->status){
            case 1:
                $img = "tick.png";
                $task = "close";
                $alt = JText::_( 'Đóng' );
                $action = JText::_( 'Đóng công việc' );
                $icon = '<i class="fa fa-unlock"></i>';
                break;
            case -1:
                $img = "checked_out.png";
                $task = "";
                $alt = $action = JText::_( 'Đã đóng' );
                $icon = '<i class="fa fa-lock red "></i>';
                break;
            default:
                $img = "checked_out.png";
                $task = $alt = $action = $icon = null;
                break;
        }


        if( strlen($action) < 1  ){
            return NULL;
        }
        $html = '<a href="javascript:void(0);" title="'. $action .'" ';
        if( strlen($task) > 0 ){
            $html .= ' onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$task .'\')" ';
        }
        $html .= " >$icon</a>";
        return $html;
    }

    static $roles = array(
        "add"=>array("Thêm Mới Công Việc","success"),
        "close"=>array("Đóng Công Việc","warning"),
        "remove"=>array("Xóa Công Việc","danger"),
        "viewall"=>array("Xem toàn bộ Công Việc","success")
    );
    function Permission($inputname="role",$position_id=0){
        echo '<div class="row">';

        $db		= & JFactory::getDBO();
        $table = & JTable::getInstance('JobsPermission');
        $db->setQuery("SELECT * FROM ".$table->_tbl." WHERE `position_id` = $position_id");
        if (!$db->query())
        {
            JError::raiseError( 500, $db->getErrorMsg() );
            return false;
        }
        $roles = $db->loadObjectList();
        $allow = array();
        foreach ($roles AS $r){
            if( $r->value==1 ){
                $allow[] = $r->role;
            }
        }

        foreach (self::$roles AS $r=>$val){
            $color = $val[1];
            $title = $val[0];
            $checked = in_array($r,$allow) ? "checked": NULL;
            $input = '<div class="form-group has-'.$color.' col-6">
                <label class="custom-control custom-checkbox ">
                    <input type="checkbox" class="custom-control-input" value="'.$r.'" name="'.$inputname.'[]" '.$checked.'>
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">'.$title.'</span>
                </label>
            </div>';

            echo $input;
        }
        echo '</div>';
    }

    static $current_role = array();
    static function LoadRole($position_id=0){
        if( $position_id < 1 ){
            $user	=& JFactory::getUser();
            $position_id = $user->getParam("position");
        }

        if( $position_id < 1 ){
            return NULL;
        }

        $db		= & JFactory::getDBO();
        $table = & JTable::getInstance('JobsPermission');
        $db->setQuery("SELECT * FROM ".$table->_tbl." WHERE `position_id` = $position_id AND value=1");
        if (!$db->query())
        {
            JError::raiseError( 500, $db->getErrorMsg() );
            return false;
        }
        $roles = $db->loadObjectList();

        $items = array();
        foreach ($roles AS $r){
            $items[] = $r->role;
        }
        self::$current_role = $items;
        //return $items;
    }

    static function isViewAll(){
        return in_array("viewall",self::$current_role);
    }

    static function isClose(){
        return in_array("close",self::$current_role);
    }
    static function isRemove(){
        return in_array("remove",self::$current_role);
    }
    static function isAdd(){
        return in_array("add",self::$current_role);
    }
}