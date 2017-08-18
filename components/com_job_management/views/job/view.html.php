<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport( 'joomla.application.component.view');
class jobmgViewjob extends JView
{
    function display($tpl = null)
    {

        $db		= & JFactory::getDBO();
        $job = & JTable::getInstance('JobManagementJob');
        if (!$job->bind(JRequest::get('post'))) {
            JError::raiseError( 500, $db->stderr() );
            return false;
        }
//        bug($job);die;
        $this->assignRef('job',	$job);
        parent::display("add");
    }

}
?>