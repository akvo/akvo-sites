<?php
namespace Akvo\WfW\ParticipantRegistry\Admin\Model;

use Akvo\WfW\ParticipantRegistry\Admin\Controller\Supportpoint as AWPRSupportpoint;
use Akvo\WfW\ParticipantRegistry\Admin\Form as AWPRAdminForm;
use Akvo\WfW\ParticipantRegistry\Config as AWPRConfig;
use Akvo\WfW\ParticipantRegistry\Common\Model\Dao\ParticipantRegistry as DaoRegistry;
/**
 * Description of Settings
 *
 * @author Jayawi Perera
 */
class Supportpoint {

	public function manage () {
        
        $oDao = new DaoRegistry();
        $aSupportPoints = $oDao->fetchSupportpoints();
        $mRedirect = false;
		if (empty($_POST)) {
            
			

		} else {
            $sName = $_POST['supportpoint_name'];
            if(isset($sName) && $sName!=''){
                $oDao->insertSupportPoint($sName);
                $mRedirect = AWPRSupportpoint::MENU_SLUG;
            }
			

		}


		$aContent = array(
			'supportpoints' => $aSupportPoints,
			
		);
        if($mRedirect){
            $aContent['redirect']=$mRedirect;
        }
		return $aContent;
	}

}