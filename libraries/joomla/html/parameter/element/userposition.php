<?php
defined('JPATH_BASE') or die();

class JElementUserposition extends JElement
{
    /**
     * Element name
     *
     * @access	protected
     * @var		string
     */
    var	$_name = 'userposition';

    function fetchElement($name, $value, &$node, $control_name)
    {
        if(!strlen($value)) {
            $conf =& JFactory::getConfig();
            $value = $conf->getValue('config.offset');
        }
        $db =& JFactory::getDBO();
        if( $this->check_table("jobmanagement_company") OR $this->check_table("jobmanagement_position") ) {
            return NULL;
        }

        $options[] = JHTML::_('select.option', '-1', JText::_( '-- Chọn Chức Vụ --' ), 'id', 'title');

        $query = 'SELECT c.id, c.name AS title' .
            ' FROM #__jobmanagement_company AS c' .
            ' WHERE c.status = 1'.
            ' ORDER BY c.name ';
        $db	= & JFactory::getDBO();
        $db->setQuery($query);
        $companys = $db->loadObjectList();
        if( $companys > 0 ){
            $company_ids = array();
            foreach ($companys AS $c){
                $company_ids[] = $c->id;
                $db->setQuery("SELECT p.id, p.name AS title FROM #__jobmanagement_position AS p WHERE p.status = 1 AND p.company = ".$c->id." ORDER BY p.name ");
                if (!$db->query())
                {
                    JError::raiseError( 500, $db->getErrorMsg() );
                    return false;
                }
                $c->id = "<OPTGROUP>";
                $options[] = $c;
                $options = array_merge($options,$db->loadObjectList() );
            }

            $db->setQuery("SELECT p.id, p.name AS title FROM #__jobmanagement_position AS p WHERE p.status = 1 AND (p.company NOT IN (".implode(",",$company_ids).") OR p.company IS NULL) ORDER BY p.name ");
            if (!$db->query())
            {
                JError::raiseError( 500, $db->getErrorMsg() );
                return false;
            }
            if( $db->loadResult() > 0 ){
                $options[] = (object)array("id"=>"<OPTGROUP>","title"=>"Công ty khác ...");
                $options = array_merge($options,$db->loadObjectList() );
            }


        }


        return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', ' class="inputbox"', 'id', 'title', $value, $control_name.$name );
    }

    private function check_table($name="user"){
        $db =& JFactory::getDBO();
        $ettd_tname = $db->getPrefix().$name;
        $query = "SHOW TABLES LIKE '$ettd_tname'";
        $db->setQuery($query);
        if ($db->getErrorNum()) {
            JError::raiseError( 500, $db->stderr() );
            return false;
        }
        return $db->loadResult() > 0 ? true : false;
    }
}
