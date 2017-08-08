<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class JTableJobManagementReply extends JTable
{
    var $id	= null;
    var $title	= "";
    var $content = "";
    var $file = "";
    var $creator	= null;
    var $created = "";
    var $job_id = 0;

    function __construct( &$_db )
    {
        parent::__construct( '#__jobmanagement_reply', 'id', $_db );
    }
}
?>
