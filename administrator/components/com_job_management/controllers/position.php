<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.controller' );

class JobMgControllerPosition extends JController
{
    function __construct($config = array())
    {
        parent::__construct($config);
        // Register Extra tasks
        $this->registerTask('add', 'edit');
        $this->registerTask('apply', 'save');
        $this->registerTask('unpublish', 'publish');
    }

    function display(){
        global $mainframe;

        $db			=& JFactory::getDBO();
        $table = & JTable::getInstance('JobsPosition');

        $filter		= null;

        $sectionid			= JRequest::getVar( 'sectionid', -1, '', 'int' );
        $redirect			= $sectionid;
        $option				= JRequest::getCmd( 'option' );
        $context			= 'com_job_management.viewcontent';
        $filter_order		= $mainframe->getUserStateFromRequest( $context.'filter_order',		'filter_order',		'',	'cmd' );
        $filter_order_Dir	= $mainframe->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'',	'word' );
        $filter_state		= $mainframe->getUserStateFromRequest( $context.'filter_state',		'filter_state',		'',	'word' );
        $catid				= $mainframe->getUserStateFromRequest( $context.'catid',			'catid',			0,	'int' );
        $filter_authorid	= $mainframe->getUserStateFromRequest( $context.'filter_authorid',	'filter_authorid',	0,	'int' );
        $filter_sectionid	= $mainframe->getUserStateFromRequest( $context.'filter_sectionid',	'filter_sectionid',	-1,	'int' );



        $limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart	= $mainframe->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');
        $limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );

        $where[] = 'p.status != -2';


        // Author filter
        if ($filter_authorid > 0) {
            $where[] = 'p.creator = ' . (int) $filter_authorid;
        }
        // Content state filter
        if ($filter_state) {
            if ($filter_state == 'P') {
                $where[] = 'p.status = 1';
            } else {
                if ($filter_state == 'U') {
                    $where[] = 'p.status = 0';
                } else if ($filter_state == 'A') {
                    $where[] = 'p.status = -1';
                } else {
                    $where[] = 'p.status != -2';
                }
            }
        }
        // Keyword filter
        $search				= $mainframe->getUserStateFromRequest( $context.'search',			'search',			'',	'string' );
        if (strpos($search, '"') !== false) {
            $search = str_replace(array('=', '<'), '', $search);
        }
        $search = JString::strtolower($search);
        if ($search) {
            $where[] = '(LOWER( p.title ) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false ) .
                ' OR p.id = ' . (int) $search . ')';
        }
        $lists['search'] = $search;

        // Build the where clause of the content record query
        $where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');
        $order = "";
        // Get the articles
        $query = 'SELECT p.*, u.name AS author, c.name AS company_name' .
            ' FROM '.$table->_tbl.' AS p' .
            ' LEFT JOIN #__users AS u ON u.id = p.creator' .
            ' LEFT JOIN #__jobmanagement_company AS c ON c.id = p.company' .
            $where;

        $db->setQuery($query);
        $total = $db->loadResult();

        jimport('joomla.html.pagination');
        $pagination = new JPagination($total, $limitstart, $limit);

        $db->setQuery($query.$order, $pagination->limitstart, $pagination->limit);
        $rows = $db->loadObjectList();

        // If there is a database query error, throw a HTTP 500 and exit
        if ($db->getErrorNum()) {
            JError::raiseError( 500, $db->stderr() );
            return false;
        }

        // search filter

        $lists['authorid'] = JHTMLJobMg::AuthorSelect("filter_authorid",$filter_authorid);
        $lists['status'] = JHTML::_('grid.state', $filter_state, 'Published', 'Unpublished');

        include_once JPATH_COMPONENT.DS.'views/positions.php';
    }

    function edit(){
        return self::form(true);
    }
    private function form($edit)
    {
        global $mainframe;

        // Initialize variables
        $db				= & JFactory::getDBO();
        $user			= & JFactory::getUser();

        $cid			= JRequest::getVar( 'cid', array(0), '', 'array' );
        JArrayHelper::toInteger($cid, array(0));
        $id				= JRequest::getVar( 'id', $cid[0], '', 'int' );
        $option			= JRequest::getCmd( 'option' );
        $nullDate		= $db->getNullDate();
        $contentSection	= '';
        $sectionid		= 0;

        // Create and load the content table row
        $row = & JTable::getInstance('JobsPosition');

        if($edit)
            $row->load($id);

        if ($id) {
            if ($row->status < 0) {
                $mainframe->redirect('index.php?option='.$this->com, JText::_('You cannot edit an Position item'));
            }
        }


        if ($id)
        {
            $query = 'SELECT name' .
                ' FROM #__users'.
                ' WHERE id = '. (int) $row->created_by;
            $db->setQuery($query);
            $row->creator = $db->loadResult();

            // test to reduce unneeded query
            if ($row->created_by == $row->modified_by) {
                $row->modifier = $row->creator;
            } else {
                $query = 'SELECT name' .
                    ' FROM #__users' .
                    ' WHERE id = '. (int) $row->modified_by;
                $db->setQuery($query);
                $row->modifier = $db->loadResult();
            }

        }
        else
        {
            if (!$sectionid && JRequest::getInt('filter_sectionid')) {
                $sectionid =JRequest::getInt('filter_sectionid');
            }

            if (JRequest::getInt('catid'))
            {
                $row->catid	 = JRequest::getInt('catid');
                $category 	 = & JTable::getInstance('category');
                $category->load($row->catid);
                $sectionid = $category->section;
            } else {
                $row->catid = NULL;
            }
            $createdate =& JFactory::getDate();
            $row->status = 1;
            $row->creator = '';
            //$row->created = $createdate->toUnix();
            $row->created = $db->getNullDate();
            $row->modified = $nullDate;
            $row->modifier = '';
        }

        // build the html radio buttons for published
        $lists['status'] = JHTML::_('select.booleanlist', 'status', '', $row->status);

        JRequest::setVar( 'hidemainmenu', 1 );

        jimport('joomla.html.pane');
        JFilterOutput::objectHTMLSafe( $row );

        $db		= &JFactory::getDBO();
        $editor = &JFactory::getEditor();
        $pane	= &JPane::getInstance('sliders', array('allowAllClose' => true));

        JHTML::_('behavior.tooltip');

        include_once JPATH_COMPONENT.DS.'views/position.php';
    }

    function save()
    {
        global $mainframe;

        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );

        // Initialize variables
        $db		= & JFactory::getDBO();
        $user		= & JFactory::getUser();
        $dispatcher 	= & JDispatcher::getInstance();
        //JPluginHelper::importPlugin('content');

        $option		= JRequest::getCmd( 'option' );
        $task		= JRequest::getCmd( 'task' );

        $nullDate	= $db->getNullDate();

        $row = & JTable::getInstance('JobsPosition');
        if (!$row->bind(JRequest::get('post'))) {
            JError::raiseError( 500, $db->stderr() );
            return false;
        }
        // sanitise id field
        $row->id = (int) $row->id;

        $isNew = true;
        // Are we saving from an item edit?
        if ($row->id) {
            $isNew = false;
            $datenow =& JFactory::getDate();
            $row->modified 		= $datenow->toMySQL();
            $row->modifier 	= $user->get('id');
        }

        $row->creator 	= $row->creator ? $row->creator : $user->get('id');

        if ($row->created && strlen(trim( $row->created )) <= 10) {
            $row->created 	.= ' 00:00:00';
        }

        $config =& JFactory::getConfig();
        $tzoffset = $config->getValue('config.offset');
        $date =& JFactory::getDate($row->created, $tzoffset);
        $row->created = $date->toMySQL();

        // Get a state and parameter variables from the request
        //$row->status	= JRequest::getVar( 'status', 0, '', 'int' );

        // Make sure the data is valid
        if (!$row->check()) {
            JError::raiseError( 500, $db->stderr() );
            return false;
        }

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
        JHTMLJobManagement::update_position_permission($row->id);
        $dispatcher->trigger('onAfterContentSave', array(&$row, $isNew));

        switch ($task)
        {
            case 'groupapply' :
                $msg = JText::sprintf('SUCCESSFULLY SAVED CHANGES TO Company', $row->title);
                $mainframe->redirect("index.php?option=$option&c=position&task=edit&cid[]=".$row->id, $msg);
                break;

            case 'groupsave' :
            default :
                $msg = JText::sprintf('Successfully Saved Company', $row->title);
                $mainframe->redirect("index.php?option=$option&c=position", $msg);
                break;
        }
    }

    function remove()
    {
        global $mainframe;

        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );

        // Initialize variables
        $db			= & JFactory::getDBO();
        $table = & JTable::getInstance('JobsPosition');
        $cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
        $option		= JRequest::getCmd( 'option' );

        $nullDate	= $db->getNullDate();

        JArrayHelper::toInteger($cid);

        if (count($cid) < 1) {
            $msg =  JText::_('Select an Company to delete');
            $mainframe->redirect('index.php?option='.$option, $msg, 'error');
        }

        $cids = implode(',', $cid);

        // Update articles in the database
        $query = 'DELETE FROM  '.$table->_tbl .' WHERE id IN ( '. $cids. ' )';
        $db->setQuery($query);
        if (!$db->query())
        {
            JError::raiseError( 500, $db->getErrorMsg() );
            return false;
        }

        $msg = JText::sprintf('Company(s) removed', count($cid));
        $mainframe->redirect('index.php?option='.$option.'&c=position', $msg);
    }

    function cancel()
    {
        JHTMLJobManagement::Cancel("position");
    }
}