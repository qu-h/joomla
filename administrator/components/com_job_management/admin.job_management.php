<?php
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_COMPONENT.DS.'controller.php' );
require_once( JPATH_COMPONENT.DS.'helper.php' );
require_once (JApplicationHelper::getPath('admin_html'));
JHTML::addIncludePath( JPATH_COMPONENT.DS.'helper' );
JHTML::addIncludePath( JPATH_COMPONENT.DS.'libraries' );
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_job_management'.DS.'tables');


JHTML::stylesheet("css/style.css","components/com_job_management/assets/");

$controllerName = JRequest::getCmd( 'c', 'job' );

$task = JRequest::getCmd('task');
require_once( JPATH_COMPONENT.DS.'controllers'.DS.$controllerName.'.php' );
$controllerName = 'JobMgController'.ucfirst($controllerName);
if( !class_exists($controllerName) ){
    $controllerName = "JobMgControllerJob";
}
if( !class_exists("JHTMLJobMg") ){
    require_once( JPATH_COMPONENT.DS.'libraries'.DS.'jobMg.php' );
}
if( !class_exists("JHTMLJobManagement") ){
    require_once( JPATH_COMPONENT.DS.'helper'.DS.'job_management.php' );
}

$controller = new $controllerName();
$controller->execute( $task );
$controller->redirect();