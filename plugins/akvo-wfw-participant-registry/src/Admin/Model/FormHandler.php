<?php
namespace Akvo\WfW\ParticipantRegistry\Admin\Model;

use Akvo\WfW\ParticipantRegistry\Config as AWPRConfig;
use Akvo\WfW\ParticipantRegistry\Common\Form\Register as RegisterForm;
use Akvo\WfW\ParticipantRegistry\Common\Model\Dao\ParticipantRegistry as RegisterDao;
/**
 * Description of FormHandler
 *
 * @author Jayawi Perera
 */
class FormHandler {

	public function edit () {

		$aContent = array();

		if (!isset($_GET['id'])) {

		}
		$iId = $_GET['id'];

		$oDaoParticipantRegistry = new RegisterDao();
		$aDetail = $oDaoParticipantRegistry->fetch($iId);
		$iParticipationLastYear = \Akvo\WfW\ParticipantRegistry\Common\Model\Registry::getPastParticipationYears();

		$oForm = new RegisterForm(RegisterForm::CONTEXT_UPDATE, array('id' => 'iFormRegistrantUpdate'), array());

		if (empty($_POST)) {

			$aPopulateData = array();

			$aPopulateData['textName'] = $aDetail['name'];
			$aPopulateData['textSupportOrganisation'] = $aDetail['support_point'];
			$aPopulateData['textContactName']= $aDetail['contact_name'];
			$aPopulateData['textCountry'] = $aDetail['country'];
			$aPopulateData['textEmail'] = $aDetail['email'];
			$aPopulateData['textAddress1'] = $aDetail['address1'];
			$aPopulateData['textAddress2'] = $aDetail['address2'];
			$aPopulateData['textAddress3'] = $aDetail['address3'];
			$aPopulateData['textTotalStudents'] = $aDetail['total_students'];
			$aPopulateData['textTotalSchools'] = $aDetail['total_schools'];
			$aPopulateData['selectProject'] = $aDetail['id_project'];
			$aPopulateData['textWalkDate'] = date('d-m-Y', strtotime($aDetail['date_of_walk']));
			$aPopulateData['textComments'] = $aDetail['comments'];
			$aPopulateData['textBatch'] = $aDetail['batch'];
			$aPopulateData['textLatitude'] = $aDetail['latitude'];
			$aPopulateData['textLongitude'] = $aDetail['longitude'];

			

			$oForm->populate($aPopulateData);

		} else {

			if ($oForm->isValid($_POST)) {

				$aFormValues = $oForm->getValues();

				$sLatitude = \Akvo\WfW\ParticipantRegistry\Config::OPTION_VALUE_GOOGLE_MAPS_DEFAULT_CENTER_POINT_LATITUDE;
				$sLongitude = \Akvo\WfW\ParticipantRegistry\Config::OPTION_VALUE_GOOGLE_MAPS_DEFAULT_CENTER_POINT_LONGITUDE;
				// Try Fetching Latitude and Longitude from Google Maps API
				$sGoogleMapsGeocodeBaseUrl = 'http://maps.google.com/maps/api/geocode/json?sensor=false&address=';
				$sAddressForGeocode = $aFormValues['textAddress1'];
                if($aFormValues['textAddress2']!=='')$sAddressForGeocode.=','.$aFormValues['textAddress2'];
                if($aFormValues['textAddress3']!=='')$sAddressForGeocode.=','.$aFormValues['textAddress3'];
                $sAddressForGeocode.=','.$aFormValues['textCountry'];
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

				$aUpdateData['name'] = $aFormValues['textName'];
				$aUpdateData['support_point'] = $aFormValues['textSupportOrganisation'];
				$aUpdateData['contact_name'] = $aFormValues['textContactName'];
				$aUpdateData['country'] = $aFormValues['textCountry'];
				$aUpdateData['email'] = $aFormValues['textEmail'];
				$aUpdateData['address1'] = $aFormValues['textAddress1'];
				$aUpdateData['address2'] = $aFormValues['textAddress2'];
				$aUpdateData['address3'] = $aFormValues['textAddress3'];
				$aUpdateData['total_students'] = $aFormValues['textTotalStudents'];
				$aUpdateData['total_schools'] = $aFormValues['textTotalSchools'];
				$aUpdateData['date_of_walk'] = date('Y-m-d', strtotime($aFormValues['textWalkDate']));
				$aUpdateData['id_project'] = $aFormValues['selectProject'];
				$aUpdateData['comments'] = $aFormValues['textComments'];
				$aUpdateData['batch'] = $aFormValues['textBatch'];
				$aUpdateData['latitude'] = $sLatitude;
				$aUpdateData['longitude'] = $sLongitude;
				$aUpdateData['date_updated'] = date('Y-m-d H:i:s');
//				var_dump($aInsertData);


				$oDaoParticipantRegistry->update($aUpdateData, $iId);

//				$oForm = new RegisterForm(RegisterForm::CONTEXT_CREATE, array('id' => 'iFormParticipantRegistryRegister'), array());

			} else {



			}

		}

		$aContent['form'] = $oForm;

		return $aContent;
	}

	public function remove () {

		$aContent = array();

		if (!isset($_GET['id'])) {

		}
		$iId = $_GET['id'];

		$oDaoParticipantRegistry = new RegisterDao();
		$aDetail = $oDaoParticipantRegistry->fetch($iId);
		$iParticipationLastYear = \Akvo\WfW\ParticipantRegistry\Common\Model\Registry::getPastParticipationYears();

		$oForm = new RegisterForm(RegisterForm::CONTEXT_DELETE, array('id' => 'iFormRegistrantDelete'), array());

		if (empty($_POST)) {

			$aPopulateData = array();

			$aPopulateData['textName'] = $aDetail['name'];
			

			$oForm->populate($aPopulateData);

		} else {

			if ($oForm->isValid($_POST)) {

				$aFormValues = $oForm->getValues();
				$bStatus = $oDaoParticipantRegistry->delete($iId);

				if ($bStatus != false) {
					$aContent['redirect'] = AWPRConfig::getHomeRedirectUrl();
					$oForm = null;
				}

			} else {



			}

		}

		$aContent['form'] = $oForm;

		return $aContent;
	}

}