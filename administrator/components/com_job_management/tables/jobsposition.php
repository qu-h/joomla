<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class JTableJobsPosition extends JTable
{
    var $id	= null;
    var $name	= "";
    var $creator	= null;
    var $company = 0;
    var $created = "";
    var $status = 1;

    function __construct( &$_db )
    {
        parent::__construct( '#__jobmanagement_position', 'id', $_db );
    }
}
?>
