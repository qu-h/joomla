<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport( 'joomla.application.component.view');
class jobmgViewjobreport extends JView
{
    function display($tpl = null)
    {
        global $mainframe;
        $db		= & JFactory::getDBO();
        $job = & JTable::getInstance('JobManagementJob');
        if (!$job->bind(JRequest::get('post'))) {
            JError::raiseError( 500, $db->stderr() );
            return false;
        }

        $uids = $where = [];
        $fillter_company_id = JRequest::getVar( 'company_id');
        if( intval($fillter_company_id) > 0 ){
            $where[] = 'j.companyid = '.intval($fillter_company_id);
            $uids = JHTML::_('jobMg.UidMapUids',"company",intval($fillter_company_id) );
        }
        $this->assignRef('company_id',	$fillter_company_id);

        $fillter_group_id = JRequest::getVar( 'group_id');
        if( intval($fillter_group_id) > 0 ){
            $where[] = 'j.groupid = '.intval($fillter_group_id);
            $uids = JHTML::_('jobMg.UidMapUids',"group",intval($fillter_group_id) );
        }

        $this->assignRef('group_id',	$fillter_group_id);
//bug($_POST); die;
        $date_from = JRequest::getVar( 'date_from');
        if( strlen($date_from) < 4 ){
            $date_from = date('Y-m-01');
        } else {
            $date_from = date('Y-m-d',strtotime($date_from));
        }
        $where[] = "DATE(j.date_end) >=DATE('$date_from') ";
        $this->assignRef('date_from',	$date_from);

        $date_to = JRequest::getVar( 'date_to');
        if( strlen($date_to) < 4 ){
            $date_to = date('Y-m-t');
        }else {
            $date_to = date('Y-m-d',strtotime($date_to));
        }
        $where[] = "DATE(j.date_start) <=DATE('$date_to') ";
        $this->assignRef('date_to',$date_to	);

        $users = $this->usersReport($uids, $where);
        $this->assignRef('users',	$users);

        parent::display();
    }


    private function usersReport($uids=[],$set_where = [] ){
        $users = [];

        foreach ($uids AS $uid){
            $where = array_merge($set_where,["m.uid=$uid"]);
            $users[$uid] = [
                'finished'=>$this->jobCount($where,['j.status = -1','DATE(j.date_end) >= DATE(j.modified)']),
                'late'=>$this->jobCount($where,['j.status = -1','DATE(j.date_end) < DATE(j.modified)']),
                'not_finished'=>$this->jobCount($where,['j.status > -1'])
            ];
        }
        return $users;
    }

    private function jobCount($where=[],$where_more=[]){

        $db = & JFactory::getDBO();

        $query = 'SELECT j.*'
            .' FROM #__jobmanagement_job AS j'
            ." LEFT JOIN #__jobmanagement_uid_map AS m ON m.group_id = j.id AND m.group = 'job'"
        ;
        $where_query = array_merge($where,$where_more);
        if( !empty($where_query) ){
            $query .= " WHERE ".implode(' AND ', $where_query);
        }

        $db->setQuery($query);
        if (!$db->query())
        {
            JError::raiseError( 500, $db->getErrorMsg() );
            return false;
        }
        $count = $db->getNumRows();
        return is_null($count) ? 0 : $count;
    }

}
?>