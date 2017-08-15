<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class JTableJobsPermission extends JTable
{
    var $id	= null;
    var $position_id	= 0;
    var $role	= 0;
    var $creator	= null;
    var $created = "";

    function __construct( &$_db )
    {
        parent::__construct( '#__jobmanagement_permission_mapp', 'id', $_db );
    }
}
?>
