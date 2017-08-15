<?php
defined('_JEXEC') or die('Restricted access');

//require_once(JPATH_COMPONENT.DS.'controller.php');
//require_once(JPATH_COMPONENT.DS.'helpers'.DS.'query.php');
//require_once(JPATH_COMPONENT.DS.'helpers'.DS.'route.php');

// Component Helper
jimport('joomla.application.component.helper');

JHTML::stylesheet("css/bootstrap.min.css","components/com_job_management/assets/");
JHTML::stylesheet("css/font-awesome.min.css","components/com_job_management/assets/");
JHTML::stylesheet("css/style.css","components/com_job_management/assets/");

JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_job_management'.DS.'tables');

$controllerName = JRequest::getCmd( 'c', 'job' );
$task = JRequest::getCmd('task');
require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.$controllerName.'.php' );
$controllerName = 'JobMgController'.ucfirst($controllerName);
if( !class_exists($controllerName) ){
    $controllerName = "JobMgControllerJob";
}

if( !class_exists("JHTMLJobMg") ){
    require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'libraries'.DS.'jobMg.php' );
}
$user	=& JFactory::getUser();
$user_position = array();
if ( $user->get('guest')) {
    global $mainframe;
    $mainframe->redirect( 'index.php?option=com_user&view=login' );
} else {
    $user_position = JHTMLJobMg::LoadRole();

}

$controller = new $controllerName();
$controller->frontend = true;
$controller->execute( $task );
$controller->redirect();
