<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class JTableJobsUidMap extends JTable
{
    var $id	= null;
    var $group = 0;
    var $group_id = 0;
    var $uid = 0;

    function __construct( &$_db )
    {
        parent::__construct( '#__jobmanagement_uid_map', 'id', $_db );
    }
}
?>
