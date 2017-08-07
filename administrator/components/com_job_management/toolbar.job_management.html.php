<?php

defined( '_JEXEC' ) or die( 'Restricted access' );

class TOOLBAR_JobManagement
{
	function _EDIT($edit)
	{
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		$cid = intval($cid[0]);

		$text = ( $edit ? JText::_( 'Sửa' ) : JText::_( 'Thêm mới' ) );

        $page_title = "Danh sách tất cả công việc";
        $task = JRequest::getCmd('task');
        switch ($task){
            case "addgroup":
                $page_title = "Nhóm Công việc";
                $item = "group";
                break;
            default:
                $page_title = "Công việc";
                $item = "";
                break;
        }

		JToolBarHelper::title( JText::_( $page_title ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );

		JToolBarHelper::save($item."save");
		JToolBarHelper::apply($item."apply");
		if ( $edit ) {
			JToolBarHelper::cancel( $item."cancel", 'Close' );
		} else {
			JToolBarHelper::cancel($item);
		}
	}

	function _MOVE()
	{
		JToolBarHelper::title( JText::_( 'Move Articles' ), 'move_f2.png' );
		JToolBarHelper::custom( 'movesectsave', 'save.png', 'save_f2.png', 'Save', false );
		JToolBarHelper::cancel();
	}


    function _GROUP()
    {
        JToolBarHelper::title( JText::_( 'Nhóm công việc' ), 'article.png' );
        JToolBarHelper::addNewX("addgroup");
        JToolBarHelper::trash("removegroup");
    }

	function _DEFAULT()
	{
		global $filter_state;

		$page_title = "Danh sách tất cả công việc";
        $task = JRequest::getCmd('task');
        switch ($task){
            case "group":
                $page_title = "Danh sách nhóm công việc";
                $item = "group";
                break;
            default:
                $page_title = "Danh sách công việc";
                $item = "";
                break;
        }
		JToolBarHelper::title( JText::_( $page_title ), 'article.png' );
//		if ($filter_state == 'A' || $filter_state == NULL) {
//			JToolBarHelper::unarchiveList();
//		}
//		if ($filter_state != 'A') {
//			JToolBarHelper::archiveList();
//		}
		//JToolBarHelper::publishList();
		//JToolBarHelper::unpublishList();
		//JToolBarHelper::customX( 'movesect', 'move.png', 'move_f2.png', 'Move' );
		//JToolBarHelper::customX( 'copy', 'copy.png', 'copy_f2.png', 'Copy' );
        JToolBarHelper::addNewX($item."add","Thêm mới");
        JToolBarHelper::trash($item."remove",'Xóa');
		//JToolBarHelper::preferences('com_content', '550');
		//JToolBarHelper::help( 'screen.content' );
	}
}