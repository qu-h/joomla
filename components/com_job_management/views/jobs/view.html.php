<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport( 'joomla.application.component.view');
class jobmgViewjobs extends JView
{
    function display($tpl = null)
    {
        global $mainframe, $option;
        $db			=& JFactory::getDBO();

        $where = ['(j.status > 0 OR j.status = -1)'];

        $fillter_job_status = JRequest::getVar( 'job_status');
        if( strlen($fillter_job_status) > 0 ){
            switch ($fillter_job_status){
                case 'finished':
                    $where[] = 'j.status = -1';
                    $where[] = 'DATE(j.date_end) >= DATE(j.modified)';
                    break;
                case 'late':
                    $where[] = 'j.status = -1';
                    $where[] = 'DATE(j.date_end) < DATE(j.modified)';
                    break;
                case 'not_finished':
                    $where[] = 'j.status > -1';
                    break;
            }
        } else {
            $job_status = JRequest::getVar( 'status');
            if( is_null($job_status) ){
                $job_status = '0,1';
            }
            $where[] = "j.status IN ($job_status)";
        }


        $fillter_company_id = JRequest::getVar( 'company_id');
        if( intval($fillter_company_id) > 0 ){
            $where[] = 'j.companyid = '.intval($fillter_company_id);
        }
        $this->assignRef('company_id',	$fillter_company_id);

        $fillter_group_id = JRequest::getVar( 'group_id');
        if( intval($fillter_group_id) > 0 ){
            $where[] = 'j.groupid = '.intval($fillter_group_id);
        }
        $this->assignRef('group_id',	$fillter_group_id);

        $order = "";
        $query = JHTML::_('JobForm.JobsFrontQuery',$where);

        $db->setQuery($query);
        if (!$db->query())
        {
            JError::raiseError( 500, $db->getErrorMsg() );
            return false;
        }

        $total = $db->loadResult();

        jimport('joomla.html.pagination');
        $limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart	= $mainframe->getUserStateFromRequest('global.limitstart', 'limitstart', 0, 'int');
        $limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );
        $pagination = new JPagination($total, $limitstart, $limit);


        $db->setQuery($query, $pagination->limitstart, $pagination->limit);

        $this->assignRef('jobs',	$db->loadObjectList());
        $this->assignRef('date_from',	JRequest::getVar( 'date_from'));
        $this->assignRef('date_to',	JRequest::getVar( 'date_to'));

        $this->assignRef('job_status',	$fillter_job_status);

        parent::display();
    }

}
?>