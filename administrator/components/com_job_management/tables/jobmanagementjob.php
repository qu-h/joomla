<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class JTableJobManagementJob extends JTable
{
	var $id	= null;
	var $title	= '';
	var $level = "";
	var $groupid = 0;
    var $content	= '';
	var $creator	= null;
	var $created = "";
    var $modifier	= null;
    var $modified = "";

    var $date_start = "";
    var $date_end = "";
    var $status = 1;

    var $version = 0;

    var $attribs = "";
	function __construct( &$_db )
	{
		parent::__construct( '#__jobmanagement_job', 'id', $_db );
	}
}
?>
