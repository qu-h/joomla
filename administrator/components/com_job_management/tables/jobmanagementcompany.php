<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableJobManagementCompany extends JTable
{
    var $id	= null;
    var $name	= '';
    var $creator	= null;
    var $created = "";
    var $modifier	= null;
    var $modified = "";
    var $status = 1;

    function __construct( &$_db )
    {
        parent::__construct( '#__jobmanagement_company', 'id', $_db );
    }
}
?>
