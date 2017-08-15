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
        $task = JRequest::getCmd('c');
        switch ($task){
            case "group":
                $page_title = "Nhóm Công việc";
                $item = "group";
                break;
            case "reply":
                $page_title = "Thảo Luận";
                break;
            case "company":
                $page_title = "Công Ty";
                break;
            case "position":
                $page_title = "Chức Vụ";
                break;
            default:
                $page_title = "Công Việc";
                $item = "";
                break;
        }

		JToolBarHelper::title( JText::_( $page_title ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );

		JToolBarHelper::save();
		JToolBarHelper::apply();
		if ( $edit ) {
			JToolBarHelper::cancel( "cancel", 'Close' );
		} else {
			JToolBarHelper::cancel("cancel");
		}
	}

//	function _MOVE()
//	{
//		JToolBarHelper::title( JText::_( 'Move Articles' ), 'move_f2.png' );
//		JToolBarHelper::custom( 'movesectsave', 'save.png', 'save_f2.png', 'Save', false );
//		JToolBarHelper::cancel();
//	}
//
//
//    function _GROUP()
//    {
//        JToolBarHelper::title( JText::_( 'Nhóm công việc' ), 'article.png' );
//        JToolBarHelper::addNewX("addgroup");
//        JToolBarHelper::trash("removegroup");
//    }

	function _DEFAULT()
	{
        $controllerName = JRequest::getCmd( 'c', 'job' );
        if( $controllerName=="reply" ){
            return self::_EDIT(false);
        }
		$page_title = "Danh sách tất cả công việc";
        $task = JRequest::getCmd('c');
        switch ($task){
            case "group":
                $page_title = "Danh sách nhóm công việc";
                $icon = "module";
                break;
            case "position":
                $page_title = "Danh sách Chức vụ";
                $icon = "module";
                break;
            case "reply":
                $page_title = "Thảo Luận";
                $job_id = (int)JRequest::getCmd('jid');

                if( $job_id > 0  ){
                    $page_title .= JHTMLJobMg::ReplyJobTitle($job_id);
                }
                $icon = "inbox";
                break;
            default:
                $page_title = "Danh sách công việc";
                $icon = "article";
                break;
        }
		JToolBarHelper::title( JText::_( $page_title ), $icon );
        JToolBarHelper::addNewX("add","Thêm Mới");
        if( $task=="job" ){
            JToolBarHelper::custom( 'jobclose', 'default.png', 'archive_f2.png', 'Đóng', false );
        }
        JToolBarHelper::trash("remove","Xóa");
	}
}