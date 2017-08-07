<?php
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_COMPONENT.DS.'controller.php' );
require_once( JPATH_COMPONENT.DS.'helper.php' );
require_once (JApplicationHelper::getPath('admin_html'));
JHTML::addIncludePath( JPATH_COMPONENT.DS.'helper' );

JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_job_management'.DS.'tables');

$controller = new JobManagementController();

$task = JRequest::getCmd('task');

switch (strtolower($task))
{
    case "group":
        $controller->viewGroups();
        break;
    case "addgroup":
        $controller->formGroup();
        break;
    case "groupedit":
        $controller->formGroup(true);
    case 'groupapply' :
    case 'groupsave' :
        $controller->saveGroup();
        break;
    case 'removegroup' :
        $controller->removeGroup();
        break;


    case 'add'  :
    case 'new'  :
        $controller->formJob(false);
        break;

    case 'edit' :
        $controller->formJob(true);
        break;

    case 'apply' :
    case 'save' :
        $controller->saveJob();
        break;
    case 'remove' :
        $controller->removeJobs();
		break;

	case 'cancel' :
        $controller->cancelContent();
		break;

	case 'ins_pagebreak' :
		ContentController::insertPagebreak();
		break;

	default :
        $controller->viewJobs();
		break;
}