<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.plugin.plugin');

class plgJobsmanagementUser extends JPlugin {

	function plgJobsUser(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}


    function &_getUser($user, $options = array())
    {
        die("jobsmanagement get user");
        $instance = new JUser();
        if ($id = intval(JUserHelper::getUserId($user['username']))) {
            $instance->load($id);
            return $instance;
        }
        die("call me");
    }

	function onBeforeStoreUser($user, $isnew)
	{
		global $mainframe;
	}

	function onAfterStoreUser($user, $isnew, $success, $msg)
	{

	}

	function onBeforeDeleteUser($user)
	{

	}

	function onAfterDeleteUser($user, $succes, $msg)
	{
		global $mainframe;

	 	// only the $user['id'] exists and carries valid information

		// Call a function in the external app to delete the user
		// ThirdPartyApp::deleteUser($user['id']);
	}

	function onLoginUser($user, $options)
	{

		$success = false;

		// Here you would do whatever you need for a login routine with the credentials
		//
		// Remember, this is not the authentication routine as that is done separately.
		// The most common use of this routine would be logging the user into a third party
		// application.
		//
		// In this example the boolean variable $success would be set to true
		// if the login routine succeeds

		// ThirdPartyApp::loginUser($user['username'], $user['password']);

		return $success;
	}


}
