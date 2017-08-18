<?php
defined('_JEXEC') or die('Restricted access');

//require_once(JPATH_COMPONENT.DS.'controller.php');
//require_once(JPATH_COMPONENT.DS.'helpers'.DS.'query.php');
//require_once(JPATH_COMPONENT.DS.'helpers'.DS.'route.php');

// Component Helper
jimport('joomla.application.component.helper');
JHTML::script("moment.min.js","components/com_job_management/assets/js/");
JHTML::script("jquery.3.2.1.min.js","components/com_job_management/assets/js/");
//JHTML::script("jquery-1.8.3.min.js","components/com_job_management/assets/js/");


JHTML::stylesheet("css/bootstrap.min.css","components/com_job_management/assets/");


JHTML::stylesheet("css/font-awesome.min.css","components/com_job_management/assets/");

JHTML::script("tether.min.js","components/com_job_management/assets/js/");
JHTML::script("bootstrap.min.js","components/com_job_management/assets/js/");


//JHTML::script("bootstrap-datepicker.min.js","components/com_job_management/assets/datepicker/js/");
//JHTML::stylesheet("bootstrap-datepicker3.min.css","components/com_job_management/assets/datepicker/css/");

JHTML::script("bootstrap-datetimepicker.js","components/com_job_management/assets/datetimepicker/js/");
JHTML::stylesheet("bootstrap.3.0.0.min.css","components/com_job_management/assets/css/");
JHTML::stylesheet("bootstrap-datetimepicker.css","components/com_job_management/assets/datetimepicker/css/");


JHTML::stylesheet("css/style.css","components/com_job_management/assets/");
JHTML::script("js/script.js","components/com_job_management/assets/");



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
if( !class_exists("JHTMLJobForm") ){
    require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'libraries'.DS.'jobform.php' );
}


$user	=& JFactory::getUser();
$user_position = array();
if ( $user->get('guest')) {
    $controllerName = "JobMgControllerJob";
    $task = "login";
} else {
    $user_position = JHTMLJobMg::LoadRole();
}

$controller = new $controllerName();
$controller->frontend = true;
$controller->execute( $task );
$controller->redirect();
