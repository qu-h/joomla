<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JobManagementController extends JController
{
    var $com = "com_job_management";
	function element()
	{

		$model	= &$this->getModel( 'element' );
		$view	= &$this->getView( 'element');
		$view->setModel( $model, true );


		$view->display();
	}

	function viewGroups(){
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
        $lists['authorid'] = self::AuthorSelect("filter_authorid",$filter_authorid);
        // state filter
        $lists['status'] = JHTML::_('grid.state', $filter_state, 'Published', 'Unpublished');


        include_once JPATH_COMPONENT.DS.'views/groups.php';
    }

    function formGroup($edit)
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

    function saveGroup()
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
                $mainframe->redirect('index.php?option=com_job_management&task=groupedit&cid[]='.$row->id, $msg);
                break;

            case 'groupsave' :
            default :
                $msg = JText::sprintf('Successfully Saved Job Group', $row->title);
                $mainframe->redirect('index.php?option=com_job_management&task=group', $msg);
                break;
        }
    }
    function removeGroup()
    {
        global $mainframe;

        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );

        // Initialize variables
        $db			= & JFactory::getDBO();

        $cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
        $option		= JRequest::getCmd( 'option' );
        $return		= "group";
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

        $msg = JText::sprintf('Item(s) removed', count($cid));
        $mainframe->redirect('index.php?option='.$option.'&task='.$return, $msg);
    }





    function viewJobs()
    {

        global $mainframe;

        // Initialize variables
        $db			=& JFactory::getDBO();
        $filter		= null;

        // Get some variables from the request
        $sectionid			= JRequest::getVar( 'sectionid', -1, '', 'int' );
        //$groupid			= JRequest::getVar( 'groupid', -1, '', 'int' );
        $redirect			= $sectionid;
        $option				= JRequest::getCmd( 'option' );
        $context			= 'com_job_management.viewcontent';
        $filter_order		= $mainframe->getUserStateFromRequest( $context.'filter_order',		'filter_order',		'',	'cmd' );
        $filter_order_Dir	= $mainframe->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'',	'word' );
        $filter_state		= $mainframe->getUserStateFromRequest( $context.'filter_state',		'filter_state',		'',	'word' );
        $catid				= $mainframe->getUserStateFromRequest( $context.'catid',			'catid',			0,	'int' );
        $filter_authorid	= $mainframe->getUserStateFromRequest( $context.'filter_authorid',	'filter_authorid',	0,	'int' );
        //$filter_sectionid	= $mainframe->getUserStateFromRequest( $context.'filter_sectionid',	'filter_sectionid',	-1,	'int' );
        $filter_groupid	= $mainframe->getUserStateFromRequest( $context.'filter_groupid',	'filter_groupid',	-1,	'int' );
        $search				= $mainframe->getUserStateFromRequest( $context.'search',			'search',			'',	'string' );
        if (strpos($search, '"') !== false) {
            $search = str_replace(array('=', '<'), '', $search);
        }
        $search = JString::strtolower($search);

        $limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart	= $mainframe->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');

        // In case limit has been changed, adjust limitstart accordingly
        $limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );


        $where[] = 'j.status != -2';


        if ($filter_order == 'c.ordering') {
            $order = ' ORDER BY g.title';
        }

        /*
         * Add the filter specific information to the where clause
         */

        if ($filter_groupid >= 0) {
            $where[] = 'j.groupid = ' . (int) $filter_groupid;
        }

        if ($filter_authorid > 0) {
            $where[] = 'j.creator = ' . (int) $filter_authorid;
        }
        // Content state filter
        if ($filter_state) {
            if ($filter_state == 'P') {
                $where[] = 'j.status = 1';
            } else {
                if ($filter_state == 'U') {
                    $where[] = 'j.status = 0';
                } else if ($filter_state == 'A') {
                    $where[] = 'j.status = -1';
                } else {
                    $where[] = 'j.status != -2';
                }
            }
        }
        // Keyword filter
        if ($search) {
            $where[] = '(LOWER( g.title ) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false ) .
                ' OR g.id = ' . (int) $search . ')';
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

        // Get the articles
        $query = 'SELECT j.*, g.title AS section_name, v.name AS author' .
            ' FROM #__jobmanagement_job AS j' .
            ' LEFT JOIN #__jobmanagement_group AS g ON g.id = j.groupid' .
            ' LEFT JOIN #__users AS v ON v.id = j.creator' .
            $where .
            $order;
        $db->setQuery($query, $pagination->limitstart, $pagination->limit);
        $rows = $db->loadObjectList();

        // If there is a database query error, throw a HTTP 500 and exit
        if ($db->getErrorNum()) {
            JError::raiseError( 500, $db->stderr() );
            return false;
        }

        $lists['authorid'] = self::AuthorSelect("filter_authorid",$filter_authorid);
        // state filter
        $lists['status'] = JHTML::_('grid.state', $filter_state, 'Published', 'Unpublished');
        $lists['groupid'] = self::groupSelect($filter_groupid,true,"filter_groupid");
        // table ordering
        $lists['order_Dir']	= $filter_order_Dir;
        $lists['order']		= $filter_order;

        // search filter
        $lists['search'] = $search;

        include_once JPATH_COMPONENT.DS.'views/jobs.php';
    }

    function formJob($edit)
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
		$groupid		= 0;

		// Create and load the content table row
		$row = & JTable::getInstance('JobManagementJob');
		if($edit)
			$row->load($id);

		if ($id) {
			if ($row->status < 0) {
				$mainframe->redirect('index.php?option='.$this->com, JText::_('You cannot edit an archived item'));
			}
		}

		if ($groupid == 0) {
			$where = ' WHERE groupid NOT LIKE "%com_%"';
		} else {
			$where = ' WHERE groupid = '. $db->Quote( $groupid );
		}


		if ($id)
		{
			$row->checkout($user->get('id'));


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

			$query = 'SELECT COUNT(content_id)' .
					' FROM #__content_frontpage' .
					' WHERE content_id = '. (int) $row->id;
			$db->setQuery($query);
			$row->frontpage = $db->loadResult();
		}
		else
		{
        	$sectionid =JRequest::getInt('groupid');
			$createdate =& JFactory::getDate();
			$row->groupid = $sectionid;
			$row->version = 0;
			$row->status = 1;
			$row->creator = '';
			$row->created = $createdate->toUnix();
			$row->modified = $nullDate;
			$row->modifier = '';
		}
//        $groups[] = JHTML::_('select.option', '-1', JText::_( 'Select Group' ), 'id', 'title');
//
//        $query = 'SELECT g.id, g.title' .
//            ' FROM #__jobmanagement_group AS g' .
//            ' WHERE g.status > 0'.
//            ' ORDER BY g.title';
//        $db->setQuery($query);
        $lists['groupid'] = self::groupSelect($row->groupid);
        //$lists['groupid'] = JHTML::_('select.genericlist',  $groups, 'groupid', 'class="inputbox" size="1"', 'id', 'title', intval($row->groupid));


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

        //JobManagementView::form($row, $contentSection, $lists, $sectioncategories, $option, $form);
        JRequest::setVar( 'hidemainmenu', 1 );

        jimport('joomla.html.pane');
        JFilterOutput::objectHTMLSafe( $row );

        $db		= &JFactory::getDBO();
        $editor = &JFactory::getEditor();
        $pane	= &JPane::getInstance('sliders', array('allowAllClose' => true));

        JHTML::_('behavior.tooltip');

        include_once JPATH_COMPONENT.DS.'views/job.php';
	}

	function saveJob()
	{
		global $mainframe;

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Initialize variables
		$db		= & JFactory::getDBO();
		$user		= & JFactory::getUser();
		$dispatcher 	= & JDispatcher::getInstance();

		$task		= JRequest::getCmd( 'task' );

		$row = & JTable::getInstance('JobManagementJob');
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
		$row->status	= JRequest::getVar( 'status', 0, '', 'int' );
		$details		= JRequest::getVar( 'detail', null, 'post', 'array' );

		if (is_array($details))
		{
			$txt = array ();
			foreach ($details as $k => $v) {
			    if( property_exists($row,$k) ){
			        $row->$k = $v;
                } else {
                    $txt[] = "$k=$v";
                }
			}
			$row->attribs = implode("\n", $txt);
		}

		// Make sure the data is valid
		if (!$row->check()) {
			JError::raiseError( 500, $db->stderr() );
			return false;
		}

		// Increment the content version number
		$row->version++;

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
			case 'apply' :
				$msg = JText::sprintf('SUCCESSFULLY SAVED CHANGES TO Job', $row->title);
				$mainframe->redirect('index.php?option='.$this->com.'&task=edit&cid[]='.$row->id, $msg);
				break;

			case 'save' :
			default :
				$msg = JText::sprintf('Successfully Saved Job', $row->title);
				$mainframe->redirect('index.php?option='.$this->com, $msg);
				break;
		}
	}

	function changeContent( $state = 0 )
	{
		global $mainframe;

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Initialize variables
		$db		= & JFactory::getDBO();
		$user	= & JFactory::getUser();

		$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		$option	= JRequest::getCmd( 'option' );
		$task	= JRequest::getCmd( 'task' );
		$rtask	= JRequest::getCmd( 'returntask', '', 'post' );
		if ($rtask) {
			$rtask = '&task='.$rtask;
		}

		if (count($cid) < 1) {
			$redirect	= JRequest::getVar( 'redirect', '', 'post', 'int' );
			$action		= ($state == 1) ? 'publish' : ($state == -1 ? 'archive' : 'unpublish');
			$msg		= JText::_('Select an item to') . ' ' . JText::_($action);
			$mainframe->redirect('index.php?option='.$option.$rtask.'&sectionid='.$redirect, $msg, 'error');
		}

		// Get some variables for the query
		$uid	= $user->get('id');
		$total	= count($cid);
		$cids	= implode(',', $cid);

		$query = 'UPDATE #__content' .
				' SET state = '. (int) $state .
				' WHERE id IN ( '. $cids .' ) AND ( checked_out = 0 OR (checked_out = '. (int) $uid .' ) )';
		$db->setQuery($query);
		if (!$db->query()) {
			JError::raiseError( 500, $db->getErrorMsg() );
			return false;
		}

		if (count($cid) == 1) {
			$row = & JTable::getInstance('content');
			$row->checkin($cid[0]);
		}

		switch ($state)
		{
			case -1 :
				$msg = JText::sprintf('Item(s) successfully Archived', $total);
				break;

			case 1 :
				$msg = JText::sprintf('Item(s) successfully Published', $total);
				break;

			case 0 :
			default :
				if ($task == 'unarchive') {
					$msg = JText::sprintf('Item(s) successfully Unarchived', $total);
				} else {
					$msg = JText::sprintf('Item(s) successfully Unpublished', $total);
				}
				break;
		}

		$cache = & JFactory::getCache('com_content');
		$cache->clean();

		// Get some return/redirect information from the request
		$redirect	= JRequest::getVar( 'redirect', $row->sectionid, 'post', 'int' );

		$mainframe->redirect('index.php?option='.$option.$rtask.'&sectionid='.$redirect, $msg);
	}

	function removeJobs()
	{
		global $mainframe;

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Initialize variables
		$db			= & JFactory::getDBO();

		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$option		= JRequest::getCmd( 'option' );
		$return		= JRequest::getCmd( 'returntask', '', 'post' );
		$nullDate	= $db->getNullDate();

		JArrayHelper::toInteger($cid);

		if (count($cid) < 1) {
			$msg =  JText::_('Select an item to delete');
			$mainframe->redirect('index.php?option='.$option, $msg, 'error');
		}

		// Removed content gets put in the trash [state = -2] and ordering is always set to 0
		$state		= '-2';
		$ordering	= '0';

		// Get the list of content id numbers to send to trash.
		$cids = implode(',', $cid);

		// Update articles in the database
		$query = 'UPDATE #__jobmanagement_job' .
				' SET status = '.(int) $state .
				' WHERE id IN ( '. $cids. ' )';
		$db->setQuery($query);
		if (!$db->query())
		{
			JError::raiseError( 500, $db->getErrorMsg() );
			return false;
		}

		$cache = & JFactory::getCache('com_content');
		$cache->clean();

		$msg = JText::sprintf('Item(s) sent to the Trash', count($cid));
		$mainframe->redirect('index.php?option='.$option.'&task='.$return, $msg);
	}

	/**
	* Cancels an edit operation
	*/
	function cancelContent()
	{
		global $mainframe;

		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Initialize variables
		$db	= & JFactory::getDBO();

		$row = & JTable::getInstance('content');
		$row->bind(JRequest::get('post'));
		$row->checkin();

		$mainframe->redirect('index.php?option=com_job_management');
	}


	/**
	* Form for moving item(s) to a different section and category
	*/
	function moveSection()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Initialize variables
		$db			=& JFactory::getDBO();

		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$sectionid	= JRequest::getVar( 'sectionid', 0, '', 'int' );

		JArrayHelper::toInteger($cid);

		if (count($cid) < 1) {
			$msg = JText::_('Select an item to move');
			$mainframe->redirect('index.php?option=com_content', $msg, 'error');
		}

		//seperate contentids
		$cids = implode(',', $cid);
		// Articles query
		$query = 'SELECT a.title' .
				' FROM #__content AS a' .
				' WHERE ( a.id IN ( '. $cids .' ) )' .
				' ORDER BY a.title';
		$db->setQuery($query);
		$items = $db->loadObjectList();

		$query = 'SELECT CONCAT_WS( ", ", s.id, c.id ) AS `value`, CONCAT_WS( " / ", s.title, c.title ) AS `text`' .
				' FROM #__sections AS s' .
				' INNER JOIN #__categories AS c ON c.section = s.id' .
				' WHERE s.scope = "content"' .
				' ORDER BY s.title, c.title';
		$db->setQuery($query);
		$rows[] = JHTML::_('select.option', "0, 0", JText::_('UNCATEGORIZED'));
		$rows = array_merge($rows, $db->loadObjectList());
		// build the html select list
		$sectCatList = JHTML::_('select.genericlist',  $rows, 'sectcat', 'class="inputbox" size="8"', 'value', 'text', null);

		ContentView::moveSection($cid, $sectCatList, 'com_content', $sectionid, $items);
	}

	/**
	* Save the changes to move item(s) to a different section and category
	*/
	function moveSectionSave()
	{
		global $mainframe;

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Initialize variables
		$db			= & JFactory::getDBO();
		$user		= & JFactory::getUser();

		$cid		= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$sectionid	= JRequest::getVar( 'sectionid', 0, '', 'int' );
		$option		= JRequest::getCmd( 'option' );

		JArrayHelper::toInteger($cid, array(0));

		$sectcat = JRequest::getVar( 'sectcat', '', 'post', 'string' );
		$sectcat = explode(',', $sectcat);
		$newsect = (int) @$sectcat[0];
		$newcat = (int) @$sectcat[1];

		if ((!$newsect || !$newcat) && ($sectcat !== array('0', ' 0'))) {
			$mainframe->redirect("index.php?option=com_content&sectionid=$sectionid", JText::_('An error has occurred'));
		}

		// find section name
		$query = 'SELECT a.title' .
				' FROM #__sections AS a' .
				' WHERE a.id = '. (int) $newsect;
		$db->setQuery($query);
		$section = $db->loadResult();

		// find category name
		$query = 'SELECT a.title' .
				' FROM #__categories AS a' .
				' WHERE a.id = '. (int) $newcat;
		$db->setQuery($query);
		$category = $db->loadResult();

		$total	= count($cid);
		$cids		= implode(',', $cid);
		$uid		= $user->get('id');

		$row = & JTable::getInstance('content');
		// update old orders - put existing items in last place
		foreach ($cid as $id)
		{
			$row->load(intval($id));
			$row->ordering = 0;
			$row->store();
			$row->reorder('catid = '.(int) $row->catid.' AND state >= 0');
		}

		$query = 'UPDATE #__content SET sectionid = '.(int) $newsect.', catid = '.(int) $newcat.
				' WHERE id IN ( '.$cids.' )' .
				' AND ( checked_out = 0 OR ( checked_out = '.(int) $uid.' ) )';
		$db->setQuery($query);
		if (!$db->query())
		{
			JError::raiseError( 500, $db->getErrorMsg() );
			return false;
		}

		// update new orders - put items in last place
		foreach ($cid as $id)
		{
			$row->load(intval($id));
			$row->ordering = 0;
			$row->store();
			$row->reorder('catid = '.(int) $row->catid.' AND state >= 0');
		}

		if ($section && $category) {
			$msg = JText::sprintf('Item(s) successfully moved to Section', $total, $section, $category);
		} else {
			$msg = JText::sprintf('ITEM(S) SUCCESSFULLY MOVED TO UNCATEGORIZED', $total);
		}

		$mainframe->redirect('index.php?option='.$option.'&sectionid='.$sectionid, $msg);
	}

	function accessMenu($access)
	{
		global $mainframe;

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Initialize variables
		$db		= & JFactory::getDBO();

		$cid	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$option	= JRequest::getCmd( 'option' );
		$cid	= $cid[0];

		// Create and load the article table object
		$row = & JTable::getInstance('content');
		$row->load($cid);
		$row->access = $access;

		// Ensure the article object is valid
		if (!$row->check()) {
			JError::raiseError( 500, $row->getError() );
			return false;
		}

		// Store the changes
		if (!$row->store()) {
			JError::raiseError( 500, $row->getError() );
			return false;
		}

		$cache = & JFactory::getCache('com_content');
		$cache->clean();

		$mainframe->redirect('index.php?option='.$option);
	}

	function insertPagebreak()
	{
		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('PGB ARTICLE PAGEBRK'));
		ContentView::insertPagebreak();
	}

	private function groupSelect($selected_id=-1,$submit=false,$name=NULL){
        $groups[] = JHTML::_('select.option', '-1', JText::_( 'Select Group' ), 'id', 'title');

        $query = 'SELECT g.id, g.title' .
            ' FROM #__jobmanagement_group AS g' .
            ' WHERE g.status > 0'.
            ' ORDER BY g.title';
        $db				= & JFactory::getDBO();
        $db->setQuery($query);
        $groups = array_merge($groups, $db->loadObjectList());
        //return $groups;

        if( is_null($name) ){
            $name = "groupid";
        }
        $javascript = $submit ? 'onchange="document.adminForm.submit();"' : NULL;
        return JHTML::_('select.genericlist',  $groups, $name, 'class="inputbox" size="1" '.$javascript, 'id', 'title', intval($selected_id));
    }

    private function AuthorSelect($inputname="filter_authorid",$selected_id=0){
        $query = 'SELECT c.created_by, u.name' .
            ' FROM #__content AS c' .
            ' INNER JOIN #__sections AS s ON s.id = c.sectionid' .
            ' LEFT JOIN #__users AS u ON u.id = c.created_by' .
            ' WHERE c.state <> -1' .
            ' AND c.state <> -2' .
            ' GROUP BY u.id' .
            ' ORDER BY u.name, u.id';
        $authors[] = JHTML::_('select.option', '0', '- '.JText::_('Select Author').' -', 'created_by', 'name');
        $db	    = & JFactory::getDBO();
        $db->setQuery($query);
        $authors = array_merge($authors, $db->loadObjectList());
        return JHTML::_('select.genericlist',  $authors, $inputname, 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'created_by', 'name', $selected_id);

    }
}
