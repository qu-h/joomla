<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport( 'joomla.application.component.view');
class jobmgViewchartreport extends JView
{
	function display($tpl = null)
	{
		global $mainframe, $option;
        $user		= & JFactory::getUser();
        $uid = $user->get('id');

        $db			=& JFactory::getDBO();
        $query = JHTML::_('JobForm.JobsFrontQuery');
        $db->setQuery($query);
        if (!$db->query())
        {
            JError::raiseError( 500, $db->getErrorMsg() );
            return false;
        }
        $jobs = $db->loadObjectList();

        $layout = JRequest::getVar('layout',null);
        if( $layout=="pie" ){
            $this->pieChart($jobs);
        } else {
            $this->assignRef('jobs',	$jobs);
        }

		parent::display();
	}

	private function pieChart($jobs){
        $company_report = array();
        if( count($jobs) > 0 ){


            foreach ($jobs AS $j){
                $company_id = $j->companyid;
                if( !isset($company_report[$j->companyid]) ){
                    $company = & JTable::getInstance('JobmanagementCompany','Table');
                    $company->load($company_id);

                    $company_report[$company_id] = (object)array(
                        "id"=>$company_id,
                        "name"=>$company->name,
                        "closed"=>0,"publish"=>0,'removed'=>0,"unpublish"=>0,"overdate"=>0
                    );

                }
                switch ( $j->status ){
                    case 1:
                        if( strtotime($j->date_end) < strtotime(date("d-m-Y")) ){
                            $company_report[$company_id]->overdate++;
                        } else {
                            $company_report[$company_id]->publish++;
                        }

                        break;
                    case 0:
                        $company_report[$company_id]->unpublish++;
                        break;
                    case -1:
                        $company_report[$company_id]->closed++;
                        break;
                    case -2:
                        $company_report[$company_id]->removed++;
                        break;
                }

            }
        }

        if( !empty($company_report) ) foreach ($company_report AS $company){
            $total = $company->closed + $company->publish+$company->removed+$company->overdate;
            $data = array(
                array("name"=>'Đã Đóng',"y"=>round($company->closed/$total*100,2)),
                array("name"=>'Đang Làm',"y"=>round($company->publish/$total*100,2),"sliced"=> true,"selected"=> true),
                array("name"=>'Quá Hạn',"y"=>round($company->overdate/$total*100,2)),
                array("name"=>'Đã Xóa',"y"=>round($company->removed/$total*100,2)),
            );
            $company->json = str_replace('"',"'",json_encode($data,JSON_UNESCAPED_UNICODE));
//            bug($company->json);die;

        }

        $this->assignRef('date_from',	JRequest::getVar( 'date_from'));
        $this->assignRef('date_to',	JRequest::getVar( 'date_to'));
        $this->assignRef('companys',	$company_report);



        JHTML::script("highcharts.src.js","components/com_job_management/assets/highcharts/");
    }

}
?>
