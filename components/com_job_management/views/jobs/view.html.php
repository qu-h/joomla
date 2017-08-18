<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport( 'joomla.application.component.view');
class jobmgViewjobs extends JView
{
    function display($tpl = null)
    {
        global $mainframe, $option;
        $db			=& JFactory::getDBO();

        $where = array('(j.status > 0 OR j.status = -1)');
        $order = "";
        $query = JHTML::_('JobForm.JobsFrontQuery',$where);

        $db->setQuery($query);
        if (!$db->query())
        {
            JError::raiseError( 500, $db->getErrorMsg() );
            return false;
        }
//        bug($db);
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

        parent::display();
    }

}
?>