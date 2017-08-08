<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.controller' );

class JobMgControllerGroup extends JController
{
    /**
     * Constructor
     */
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

        // Initialize variables
        $db			=& JFactory::getDBO();
        $filter		= null;

        // Get some variables from the request
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
        $search				= $mainframe->getUserStateFromRequest( $context.'search',			'search',			'',	'string' );
        if (strpos($search, '"') !== false) {
            $search = str_replace(array('=', '<'), '', $search);
        }
        $search = JString::strtolower($search);

        $limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart	= $mainframe->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');

        // In case limit has been changed, adjust limitstart accordingly
        $limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );

        //$where[] = "c.state >= 0";
        $where[] = 'g.status != -2';


        // Author filter
        if ($filter_authorid > 0) {
            $where[] = 'g.creator = ' . (int) $filter_authorid;
        }
        // Content state filter
        if ($filter_state) {
            if ($filter_state == 'P') {
                $where[] = 'g.status = 1';
            } else {
                if ($filter_state == 'U') {
                    $where[] = 'g.status = 0';
                } else if ($filter_state == 'A') {
                    $where[] = 'g.status = -1';
                } else {
                    $where[] = 'g.status != -2';
                }
            }
        }
        // Keyword filter
        if ($search) {
            $where[] = '(LOWER( c.title ) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false ) .
                ' OR c.id = ' . (int) $search . ')';
        }

        // Build the where clause of the content record query
        $where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');

        // Get the total number of records
        $query = 'SELECT COUNT(*)' .
            ' FROM #__content AS c' .
            ' LEFT JOIN #__categories AS cc ON cc.id = c.catid' .
            ' LEFT JOIN #__sections AS s ON s.id = c.sectionid' .
            $where;
        $db->setQuery($query);
        $total = $db->loadResult();


        // Create the pagination object
        jimport('joomla.html.pagination');
        $pagination = new JPagination($total, $limitstart, $limit);

        $order = "";
        // Get the articles
        $query = 'SELECT g.*, v.name AS author' .
            ' FROM #__jobmanagement_group AS g' .
            ' LEFT JOIN #__users AS v ON v.id = g.creator' .
            $where .
            $order;
        $db->setQuery($query, $pagination->limitstart, $pagination->limit);
        $rows = $db->loadObjectList();

        // If there is a database query error, throw a HTTP 500 and exit
        if ($db->getErrorNum()) {
            JError::raiseError( 500, $db->stderr() );
            return false;
        }

        // search filter
        $lists['search'] = $search;
        $lists['authorid'] = JHTMLJobMg::AuthorSelect("filter_authorid",$filter_authorid);
        // state filter
        $lists['status'] = JHTML::_('grid.state', $filter_state, 'Published', 'Unpublished');


        include_once JPATH_COMPONENT.DS.'views/groups.php';
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
        $row = & JTable::getInstance('JobmanagementGroup','Table');

        if($edit)
            $row->load($id);

        if ($id) {
            $sectionid = $row->sectionid;
            if ($row->status < 0) {
                $mainframe->redirect('index.php?option='.$this->com, JText::_('You cannot edit an Job Group item'));
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

        $javascript = "onchange=\"changeDynaList( 'catid', sectioncategories, document.adminForm.sectionid.options[document.adminForm.sectionid.selectedIndex].value, 0, 0);\"";

        // build the html select list for ordering
        $query = 'SELECT ordering AS value, title AS text' .
            ' FROM #__content' .
            ' WHERE catid = ' . (int) $row->catid .
            ' AND state >= 0' .
            ' ORDER BY ordering';
        if($edit)
            $lists['ordering'] = JHTML::_('list.specificordering', $row, $id, $query, 1);
        else
            $lists['ordering'] = JHTML::_('list.specificordering', $row, '', $query, 1);

        // build the html radio buttons for published
        $lists['status'] = JHTML::_('select.booleanlist', 'status', '', $row->status);

        // Create the form
        $form = new JParameter('', JPATH_COMPONENT.DS.'models'.DS.'article.xml');

        // Details Group
        $active = (intval($row->created_by) ? intval($row->created_by) : $user->get('id'));
        $form->set('created_by', $active);


        $form->set('created', JHTML::_('date', $row->created, '%Y-%m-%d %H:%M:%S'));
        // Advanced Group
        $form->loadINI($row->attribs);

        // Metadata Group
        $form->set('description', $row->metadesc);
        $form->set('keywords', $row->metakey);
        $form->loadINI($row->metadata);

        //JobManagementView::form($row, $contentSection, $lists, $sectioncategories, $option, $form);

        JRequest::setVar( 'hidemainmenu', 1 );

        jimport('joomla.html.pane');
        JFilterOutput::objectHTMLSafe( $row );

        $db		= &JFactory::getDBO();
        $editor = &JFactory::getEditor();
        $pane	= &JPane::getInstance('sliders', array('allowAllClose' => true));

        JHTML::_('behavior.tooltip');

        include_once JPATH_COMPONENT.DS.'views/group.php';
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

        $title	= JRequest::getVar( 'title', array(), 'post', 'array');
        $option		= JRequest::getCmd( 'option' );
        $task		= JRequest::getCmd( 'task' );

        $nullDate	= $db->getNullDate();

        $row = & JTable::getInstance('JobManagementGroup',"Table");
        if (!$row->bind(JRequest::get('post'))) {
            JError::raiseError( 500, $db->stderr() );
            bug($db->stderr());die;
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
        $row->status	= JRequest::getVar( 'status', 0, '', 'int' );



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

        $dispatcher->trigger('onAfterContentSave', array(&$row, $isNew));

        switch ($task)
        {
            case 'groupapply' :
                $msg = JText::sprintf('SUCCESSFULLY SAVED CHANGES TO Job Group', $row->title);
                $mainframe->redirect('index.php?option=com_job_management&c=group&task=edit&cid[]='.$row->id, $msg);
                break;

            case 'groupsave' :
            default :
                $msg = JText::sprintf('Successfully Saved Job Group', $row->title);
                $mainframe->redirect('index.php?option=com_job_management&c=group', $msg);
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

        $cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
        $option		= JRequest::getCmd( 'option' );

        $nullDate	= $db->getNullDate();

        JArrayHelper::toInteger($cid);

        if (count($cid) < 1) {
            $msg =  JText::_('Select an Job Group to delete');
            $mainframe->redirect('index.php?option='.$option, $msg, 'error');
        }

        $cids = implode(',', $cid);

        // Update articles in the database
        $query = 'DELETE FROM  #__jobmanagement_group' .
            ' WHERE id IN ( '. $cids. ' )';
        $db->setQuery($query);
        if (!$db->query())
        {
            JError::raiseError( 500, $db->getErrorMsg() );
            return false;
        }

        $cache = & JFactory::getCache('com_content');
        $cache->clean();

        $msg = JText::sprintf('Job Group(s) removed', count($cid));
        $mainframe->redirect('index.php?option='.$option.'&c=group', $msg);
    }

    function cancel()
    {
        global $mainframe;

        JRequest::checkToken() or jexit( 'Invalid Token' );

        // Initialize variables
        $db	= & JFactory::getDBO();

        $row = & JTable::getInstance('content');
        $row->bind(JRequest::get('post'));
        $row->checkin();

        $mainframe->redirect('index.php?option=com_job_management&c=group');
    }
}