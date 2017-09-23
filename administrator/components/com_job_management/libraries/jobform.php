<?php
class JHTMLJobForm extends  JHTML
{

    function JHTMLJobForm()
    {
    }

    static function datetime($name="date_val",$label="Date",$value="",$inline=0){
        include JPATH_COMPONENT_ADMINISTRATOR.'/views/ui/datetime.php';
    }

    static function inputdate($name="date_val",$label="Date",$value="",$inline=0,$taskUpdate=null){
        $value_format = $value;
        if( strlen($value) > 0 ){
            $value_format = date("Y-m-d",strtotime($value));
            $value = date("d/m/Y",strtotime($value));
        }


        include JPATH_COMPONENT_ADMINISTRATOR.'/views/ui/date.php';
    }

    static function level($name="level",$label="Date",$value="",$inline=0){
        $options[] = JHTML::_('select.option', 'normal', JText::_( 'Bình Thường' ), 'id', 'title');
        $options[] = JHTML::_('select.option', 'emergency', JText::_( 'Khẩn Cấp' ), 'id', 'title');
        $input_html = JHTML::_('select.genericlist',  $options, $name, 'class="form-control custom-select" size="1" ', 'id', 'title', $value);
        include JPATH_COMPONENT_ADMINISTRATOR.'/views/ui/formgroup.php';
    }

    static function JobsFrontQuery($set_where = array()){
        $db			=& JFactory::getDBO();

        $date_today = date("Y-m-d");
        $date_from = JRequest::getCmd( 'date_from' );

        if( strlen($date_from) < 1 ){
            $date_from =  date("Y-m-d",strtotime($date_today." -15 days"));
        }
        JRequest::setVar( 'date_from',$date_from );
        $date_to = JRequest::getCmd( 'date_to');
        if( strlen($date_to) < 1 ){
            $date_to =  date("Y-m-d",strtotime($date_today." +15 days"));
        }
        JRequest::setVar( 'date_to',$date_to );

        //$where[] = '(j.status > 0 OR j.status = -1)';
        $where = $set_where;
        $where[] = "(j.date_end <= '$date_to' AND j.date_start >= '$date_from' )";

        $query = 'SELECT j.*, g.title AS section_name, v.name AS author' .
            ' FROM #__jobmanagement_job AS j' .
            ' LEFT JOIN #__jobmanagement_group AS g ON g.id = j.groupid' .
            ' LEFT JOIN #__users AS v ON v.id = j.creator'
            .' LEFT JOIN #__jobmanagement_company AS c ON c.id = g.company';
        $order = " ORDER BY j.date_start";

        $user		= & JFactory::getUser();
        $uid = $user->get('id');
        $UidMapTable = & JTable::getInstance('JobsUidMap');

        if( JHTMLJobMg::isViewAll() == true ) {
            $db->setQuery("SELECT group_id FROM " . $UidMapTable->_tbl . " WHERE `group` = 'company' AND `uid` = $uid");

            if (!$db->query()) {
                JError::raiseError(500, $db->getErrorMsg());
                return false;
            }
            $company_ids = array_values($db->loadObjectList());

            $company_ids = array_map(function ($object) {
                return $object->group_id;
            }, $db->loadObjectList());
            if (count($company_ids) > 0) {
                $where[] = "(g.company IN (" . implode(",", $company_ids) . ") OR j.creator=$uid)";
            }
        } else if ( JHTMLJobMg::isViewGroup() == true ){
            $group_ids = JHTMLJobMg::UidMapGroupIds("group",$uid);

            if (count($group_ids) > 0) {
                $where[] = "(j.groupid IN (" . implode(",", $group_ids) . ") OR j.creator=$uid)";
            }

        } else {
            $db->setQuery("SELECT group_id FROM ".$UidMapTable->_tbl." WHERE `group` = 'job' AND `uid` = $uid");

            if (!$db->query())
            {
                JError::raiseError( 500, $db->getErrorMsg() );
                return false;
            }
            $job_ids = array_values($db->loadObjectList());
            $job_ids = array_map(function ($object) { return $object->group_id; }, $db->loadObjectList());
            if( count($job_ids) > 0 ){
                $where[]= "(j.id IN (".implode(",",$job_ids).") OR j.creator=$uid)";
            } else {
                $where[]= "j.id < 1 OR j.creator=$uid";
            }
        }

        $where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');

        //$where .= " ";
        return $query .=$where . $order;

    }

    static function companys($name="company_id",$label="Company",$value="",$inline=0,$taskUpdate=true){
        $input_html = JHTMLJobMg::companySelect($value,$taskUpdate,$name,false);
        include JPATH_COMPONENT_ADMINISTRATOR.'/views/ui/formgroup.php';
    }

    static function groups($name="group_id",$label="Group",$value="",$inline=0,$taskUpdate=true){
        $input_html = JHTMLJobMg::groupSelect($value,$taskUpdate,$name,false);
        include JPATH_COMPONENT_ADMINISTRATOR.'/views/ui/formgroup.php';
    }

    static function jobStatus($name="group_id",$label="Group",$value="",$inline=0,$taskUpdate=null){
        $options[] = JHTML::_('select.option', '', JText::_( '-- Trạng thái --' ), 'id', 'title');
        $options[] = JHTML::_('select.option', 'finished', JText::_( 'Hoàn thành' ), 'id', 'title');
        $options[] = JHTML::_('select.option', 'late', JText::_( 'Hoàn thành trễ' ), 'id', 'title');
        $options[] = JHTML::_('select.option', 'not_finished', JText::_( 'Chưa hoàn thành' ), 'id', 'title');

        $onchange = $taskUpdate ? ' onchange="javascript: submitbutton(\'updateformval\');" ' : null;
        $input_html = JHTML::_('select.genericlist',  $options, $name, 'class="form-control custom-select" size="1" '.$onchange, 'id', 'title', $value);
        include JPATH_COMPONENT_ADMINISTRATOR.'/views/ui/formgroup.php';
    }
}