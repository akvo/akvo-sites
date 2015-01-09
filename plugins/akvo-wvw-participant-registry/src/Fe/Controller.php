<?php
namespace Akvo\WvW\ParticipantRegistry\Fe;

use Akvo\WvW\ParticipantRegistry as AWPR;
use Akvo\WvW\ParticipantRegistry\Common\Model\Dao\ParticipantRegistry as Dao_Reg;
/**
 * Description of Controller
 *
 * @author Jayawi Perera
 */
class Controller {

	public function initialise () {

		$this->registerShortCodes();
        if(isset($_GET['update_participants_coords']) && $_GET['update_participants_coords']!=''){
            $this->getCoords($_GET['update_participants_coords']);
        }
	}

	public function registerShortCodes () {

		add_shortcode(AWPR\Config::SHORTCODE_FORM, array($this, 'displayForm'));
		add_shortcode(AWPR\Config::SHORTCODE_MAP, array($this, 'displayMap'));
		add_shortcode(AWPR\Config::SHORTCODE_LIST, array($this, 'displayList'));
        
	}

	public function displayForm () {

		$oFormController = new Controller\Form();
		$oFormController->initialise();
		return $oFormController->page();

	}

	public function displayMap () {

		$oMapController = new Controller\Map();
		$oMapController->initialise();
		return $oMapController->page();

	}
    
	public function displayList () {

		$oListController = new Controller\Participantlist();
		$oListController->initialise();
		return $oListController->page();

	}
    
    public function getCoords($iIdOrg){
        $oDaoRegistry = new Dao_Reg();
        $aSchools = $oDaoRegistry->fetchBySupportPoint($iIdOrg);
        //var_dump($aSchools);
        $i=0;
        foreach($aSchools AS $aSchool){
            if($aSchool['latitude']!=' ')continue;
            $i++;
            $sLatitude = \Akvo\WvW\ParticipantRegistry\Config::OPTION_VALUE_GOOGLE_MAPS_DEFAULT_CENTER_POINT_LATITUDE;
            $sLongitude = \Akvo\WvW\ParticipantRegistry\Config::OPTION_VALUE_GOOGLE_MAPS_DEFAULT_CENTER_POINT_LONGITUDE;
				
            $sGoogleMapsGeocodeBaseUrl = 'http://maps.google.com/maps/api/geocode/json?sensor=false&address=';
            $sAddressForGeocode = $aSchool['address'] . ',' . $aSchool['postal_code'] . ' ' . $aSchool['city'] . ', The Netherlands';
    //				$sAddressForGeocode = $aFormValues['textAddress'] . ',' . $aFormValues['textCity'] . ', The Netherlands';
            $sGoogleMapsGeocodeUrl = $sGoogleMapsGeocodeBaseUrl . urlencode($sAddressForGeocode);
            $rCurl = curl_init();
            curl_setopt($rCurl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($rCurl, CURLOPT_URL, $sGoogleMapsGeocodeUrl);
            $mCurlResponse = curl_exec($rCurl);
            if ($mCurlResponse !== FALSE) {
                $oCurlResponse = json_decode($mCurlResponse);
                $sCurlResponseStatus = $oCurlResponse->status;

                if ($sCurlResponseStatus == "OK") {
                    $aCurlResponseResults = $oCurlResponse->results;
                    $aCurlResponseResult = $aCurlResponseResults[0];
                    if(isset($aCurlResponseResult->geometry->location)) {

                        $oLocation = $aCurlResponseResult->geometry->location;
                        $sLatitude = $oLocation->lat;
                        $sLongitude = $oLocation->lng;

                    }
                }

            }
            $aSchool['latitude']=$sLatitude;
            $aSchool['longitude']=$sLongitude;
            $oDaoRegistry->update($aSchool, $aSchool['id']);
            var_dump($aSchool);
            if($i==20)break;
        }
        return;
        
        
    }

}