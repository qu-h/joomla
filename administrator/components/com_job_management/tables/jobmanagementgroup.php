<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableJobManagementGroup extends JTable
{
	var $id	= null;
	var $title	= '';
	var $creator	= null;
	var $company = 0;
	var $created = "";
    var $modifier	= null;
    var $modified = "";
    var $status = 1;

	function __construct( &$_db )
	{
		parent::__construct( '#__jobmanagement_group', 'id', $_db );
	}
}
?>
