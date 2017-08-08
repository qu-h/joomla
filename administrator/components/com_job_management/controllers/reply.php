<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.controller' );

class JobMgControllerReply extends JController
{
    var $frontend = false;
    function __construct($config = array())
    {
        parent::__construct($config);
        // Register Extra tasks
        $this->registerTask('add', 'edit');
        $this->registerTask('apply', 'save');
        $this->registerTask('resethits', 'save');
        $this->registerTask('unpublish', 'publish');

    }
    function display()
    {
        global $mainframe;
        $option	= JRequest::getCmd( 'option' );
        $id				= JRequest::getVar( 'jid',0, '', 'int' );

        $showSubmitButton = $this->frontend;
        include_once JPATH_COMPONENT.DS.'views/reply.php';

        if( !$this->frontend ){
            include_once JPATH_COMPONENT.DS.'views/reply_items.php';
        }


    }

    function cancel()
    {
        global $mainframe;

        JRequest::checkToken() or jexit( 'Invalid Token' );

        $mainframe->redirect('index.php?option=com_job_management&c=job');
    }

    function save()
    {
        global $mainframe;

        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );

        // Initialize variables
        $db		= & JFactory::getDBO();
        $user		= & JFactory::getUser();
        //$dispatcher 	= & JDispatcher::getInstance();

        $row = & JTable::getInstance('JobManagementReply');

        if (!$row->bind(JRequest::get('post'))) {

            JError::raiseError( 500, $db->stderr() );
            return false;
        }

        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');
        $fieldName = 'file';
        $fileError = $_FILES[$fieldName]['error'];
        $error = NULL;

        if ($fileError > 0)
        {
            switch ($fileError)
            {
                case 1:
                    $error =  JText::_( 'FILE TO LARGE THAN PHP INI ALLOWS' );
                    return;

                case 2:
                    $error =  JText::_( 'FILE TO LARGE THAN HTML FORM ALLOWS' );
                    return;

                case 3:
                    $error =  JText::_( 'ERROR PARTIAL UPLOAD' );
                    return;

                case 4:
                    $error = JText::_( 'ERROR NO FILE' );
                    return;
            }
        }

        $fileSize = $_FILES[$fieldName]['size'];
        if($fileSize > 2000000)
        {
            $error = JText::_( 'FILE BIGGER THAN 2MB' );
        }
        $fileName = $_FILES[$fieldName]['name'];
        //lose any special characters in the filename
        //$fileName = preg_replace("/[^A-Za-z0-9]/i", "-", $fileName);

//always use constants when making file paths, to avoid the possibilty of remote file inclusion
        $uploadPath = JPATH_SITE.DS.'upload'.DS.'job_management'.DS;
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }
        $uploadPath .= $fileName;

        $fileTemp = $_FILES[$fieldName]['tmp_name'];

        if(JFile::upload($fileTemp, $uploadPath))
        {
            //$error = JText::_( 'ERROR MOVING FILE' );
            $row->file = $fileName;
        }
        if( strlen($error) > 0 ){
            JError::raiseError( 500, $error );
            return;
        }

        $row->id = (int) $row->id;

        $isNew = true;

        $row->creator 	= $row->creator ? $row->creator : $user->get('id');
        if ($row->created && strlen(trim( $row->created )) <= 10) {
            $row->created 	.= ' 00:00:00';
        }

        $config =& JFactory::getConfig();
        $tzoffset = $config->getValue('config.offset');
        $date =& JFactory::getDate($row->created, $tzoffset);
        $row->created = $date->toMySQL();

        $row->job_id	= JRequest::getVar( 'jid', 0, '', 'int' );

        // Make sure the data is valid
        if (!$row->check()) {
            JError::raiseError( 500, $db->stderr() );
            return false;
        }


        $dispatcher 	= & JDispatcher::getInstance();
        $result = $dispatcher->trigger('onBeforeContentSave', array(&$row, $isNew));
        if(in_array(false, $result, true)) {
            JError::raiseError(500, $row->getError());
            return false;
        }

        // Store the content to the database
        if (!$row->store()) {
            JError::raiseError( 500, $db->stderr() );
            return false;
        }

        $dispatcher->trigger('onAfterContentSave', array(&$row, $isNew));

        $task		= JRequest::getCmd( 'task' );
        switch ($task)
        {
            case 'apply' :
                $msg = JText::sprintf('SUCCESSFULLY SAVED CHANGES TO Reply');
                $mainframe->redirect('index.php?option=com_job_management&c=reply&jid='.$row->job_id, $msg);
                break;

            case 'save' :
            default :
                $msg = JText::sprintf('Successfully add Reply', $row->title);
                $mainframe->redirect('index.php?option=com_job_management&c=job', $msg);
                break;
        }
    }
}