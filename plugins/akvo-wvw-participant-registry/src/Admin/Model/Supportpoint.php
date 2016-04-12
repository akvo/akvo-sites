<?php
namespace Akvo\WvW\ParticipantRegistry\Admin\Model;

use Akvo\WvW\ParticipantRegistry\Admin\Controller\Supportpoint\Detail as AWPRSupportpoint;
use Akvo\WvW\ParticipantRegistry\Admin\Form as AWPRAdminForm;
use Akvo\WvW\ParticipantRegistry\Config as AWPRConfig;
use Akvo\WvW\ParticipantRegistry\Common\Model\Dao\ParticipantRegistry as DaoRegistry;
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
	
	/**
	 * 
	 * @author Rumeshkumar <rumeshin@gmail.com>
	 */
	public function remove() {
		
		$oDao = new DaoRegistry();
		$aContent = array();
		$aContent['have_participant'] = false;
		$aContent['status'] = true;
		$aContent['error'] = false;
		
		if(isset($_GET['action']) && isset($_GET['id'])) {
			
			$sAction = $aContent['action'] = $_GET['action'];
			$iSupportPoint = $_GET['id'];
			// get supportpoint details
			$aSupportpoint = $oDao->fetchSupportpointById($iSupportPoint);
			if(sizeof($aSupportpoint) > 0) {
				
				$aContent['supportpoint'] = $aSupportpoint['name'];
				$aContent['supportpoint_id'] = $iSupportPoint;

				// Check if this support point has any data in akvo_wvw_participant_registry tbl.		
				$aParticipantRegistries = $oDao->fetchBySupportPoint($iSupportPoint);

				if(sizeof($aParticipantRegistries) > 0) {

					//there are data
					$aContent['has_participant'] = true;					
				}

				if($sAction == 'remove') {

					//remove participants
					foreach($aParticipantRegistries as $aParticipantRegistry) {

						$mStatusParticipantRegistry = $oDao->delete($aParticipantRegistry['id']);

						if($mStatusParticipantRegistry == false) {

							$aContent['status'] = false;
							break;
						}
					}

					if($mStatusParticipantRegistry != false) {
						//delete support point
						$mStatusSupportpoint = $oDao->deleteSupportpoint($iSupportPoint);
					}

					if($mStatusSupportpoint == false) {

						$aContent['status'] = false;
					}

				}
			} else {
				
				$aContent['error'] = true;
			}
		} else {
			
			$aContent['error'] = true;
		}
		
		return $aContent;
	}
	
}