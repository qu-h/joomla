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
//bug("taks=$task");die;
$controller->execute( $task );
$controller->redirect();

//$controller = new JobManagementController();

//$task = JRequest::getCmd('task');
//
//switch (strtolower($task))
//{
//    case "group":
//        $controller->viewGroups();
//        break;
//    case "addgroup":
//        $controller->formGroup();
//        break;
//    case "groupedit":
//        $controller->formGroup(true);
//    case 'groupapply' :
//    case 'groupsave' :
//        $controller->saveGroup();
//        break;
//    case 'removegroup' :
//        $controller->removeGroup();
//        break;
//
//
//    case 'add'  :
//    case 'new'  :
//        $controller->formJob(false);
//        break;
//
//    case 'edit' :
//        $controller->formJob(true);
//        break;
//
//    case 'apply' :
//    case 'save' :
//        $controller->saveJob();
//        break;
//    case 'remove' :
//        $controller->removeJobs();
//		break;
//
//	case 'cancel' :
//        $controller->cancelContent();
//		break;
//
//	case 'ins_pagebreak' :
//		ContentController::insertPagebreak();
//		break;
//
//	default :
//        $controller->viewJobs();
//		break;
//}