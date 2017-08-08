<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.controller' );

class JobMgControllerJob extends JController
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


        if( $this->frontend ){
            $where[] = 'j.status = 1';
        } else if ($filter_state) {
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

        $lists['authorid'] = JHTMLJobMg::AuthorSelect("filter_authorid",$filter_authorid);
        $lists['status'] = JHTML::_('grid.state', $filter_state, 'Published', 'Unpublished');
        $lists['groupid'] = JHTMLJobMg::groupSelect($filter_groupid,true,"filter_groupid");
        $lists['order_Dir']	= $filter_order_Dir;
        $lists['order']		= $filter_order;

        // search filter
        $lists['search'] = $search;

        if( $this->frontend ){
            include_once JPATH_COMPONENT.DS.'views/jobs.php';
        } else {
            include_once JPATH_COMPONENT_ADMINISTRATOR.DS.'views/jobs.php';
        }
    }

    function edit(){
        return self::form(true);
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

        $task		= JRequest::getCmd( 'task' );

        $row = & JTable::getInstance('JobManagementJob');
        if (!$row->bind(JRequest::get('post'))) {
            JError::raiseError( 500, $db->stderr() );
            return false;
        }

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

        $query = 'DELETE FROM #__jobmanagement_job_user WHERE job_id ='. $row->id. '';
        $db->setQuery($query);
        if (!$db->query())
        {
            JError::raiseError( 500, $db->getErrorMsg() );
            return false;
        }

        $userSelect = JRequest::getVar( 'users', array(), 'post', 'array' );

        if( !empty($userSelect) ){
            foreach ($userSelect AS $u){
                $add_uid_query = "INSERT INTO `#__jobmanagement_job_user` (`id`, `job_id`, `uid`) VALUES (0, ". $row->id. ",$u)";
                $db->setQuery($add_uid_query);
                if (!$db->query())
                {
                    JError::raiseError( 500, $db->getErrorMsg() );
                    return false;
                }
            }
        }

        $dispatcher->trigger('onAfterContentSave', array(&$row, $isNew));

        switch ($task)
        {
            case 'apply' :
                $msg = JText::sprintf('SUCCESSFULLY SAVED CHANGES TO Job', $row->title);
                $mainframe->redirect('index.php?option=com_job_management&c=job&task=edit&cid[]='.$row->id, $msg);
                break;

            case 'save' :
            default :
                $msg = JText::sprintf('Successfully Saved Job', $row->title);
                $mainframe->redirect('index.php?option=com_job_management&c=job', $msg);
                break;
        }
    }

    private function form($edit)
    {
        global $mainframe;

        JHTML::stylesheet("css/bootstrap.min.css","components/com_job_management/assets/");
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
                ' WHERE id = '. (int) $row->modifier;
            $db->setQuery($query);
            $row->creator = $db->loadResult();

            // test to reduce unneeded query
            if ($row->created_by == $row->modifier) {
                $row->modifier = $row->creator;
            } else {
                $query = 'SELECT name' .
                    ' FROM #__users' .
                    ' WHERE id = '. (int) $row->modifier;
                $db->setQuery($query);
                $row->modifier = $db->loadResult();
            }
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


        // Create the form
        $form = new JParameter('', JPATH_COMPONENT.DS.'models'.DS.'job.xml');

        // Details Group
        $active = (intval($row->creator) ? intval($row->creator) : $user->get('id'));
        $form->set('creator', $active);
        $form->set('created', JHTML::_('date', $row->created, '%Y-%m-%d %H:%M:%S'));

        $form->set('level', $row->level);
        $form->set('date_start', $row->date_start);
        $form->set('date_end', $row->date_end);

        // Advanced Group
        $form->loadINI($row->attribs);

        //JobManagementView::form($row, $contentSection, $lists, $sectioncategories, $option, $form);
        JRequest::setVar( 'hidemainmenu', 1 );

        jimport('joomla.html.pane');
        JFilterOutput::objectHTMLSafe( $row );

        //$db		= &JFactory::getDBO();
        $editor = &JFactory::getEditor();
        $pane	= &JPane::getInstance('sliders', array('allowAllClose' => true));

        JHTML::_('behavior.tooltip');

        include_once JPATH_COMPONENT.DS.'views/job.php';
    }

    function view()
    {
        global $mainframe;


//        // Initialize variables
        $db				= & JFactory::getDBO();
        $id				= JRequest::getVar( 'jid', 0, '', 'int' );

        $query = 'SELECT j.*, g.title AS group_name, v.name AS author' .
            ' FROM #__jobmanagement_job AS j' .
            ' LEFT JOIN #__jobmanagement_group AS g ON g.id = j.groupid' .
            ' LEFT JOIN #__users AS v ON v.id = j.creator'
            ." WHERE j.id =$id"
            ;

        $db->setQuery($query);
        $row = $db->loadObject();

        $get_uids_query = "SELECT uid FROM #__jobmanagement_job_user WHERE job_id = ".$row->id;
        $db->setQuery($get_uids_query);

        $uids = $db->loadObjectList();

        if ($id) {
            if ($row->status < 0) {
                $mainframe->redirect('index.php?option='.$this->com, JText::_('You cannot edit an archived item'));
            }
        }

        include_once JPATH_COMPONENT.DS.'views/job_view.php';

        $id = $row->id;
        include_once JPATH_COMPONENT.DS.'views/reply.php';
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
        $mainframe->redirect('index.php?option='.$option.'&c=job&task'.$return, $msg);
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

        $mainframe->redirect('index.php?option=com_job_management&c=job');
    }

}