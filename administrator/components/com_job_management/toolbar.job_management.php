<?php

defined( '_JEXEC' ) or die( 'Restricted access' );

require_once( JApplicationHelper::getPath( 'toolbar_html' ) );

switch ($task)
{
	case 'add':
    case "addgroup":
	case 'new_content_typed':
	case 'new_content_section':
        TOOLBAR_JobManagement::_EDIT(false);
		break;
	case 'edit':
	case 'editA':
	case 'edit_content_typed':
        TOOLBAR_JobManagement::_EDIT(true);
		break;
	case 'movesect':
        TOOLBAR_JobManagement::_MOVE();
		break;

	case 'copy':
        TOOLBAR_JobManagement::_COPY();
		break;
    case "group":
        TOOLBAR_JobManagement::_GROUP();
        break;

	default:

        TOOLBAR_JobManagement::_DEFAULT();
		break;
}