<?php
namespace Akvo\WvW\ParticipantRegistry\Admin\Model;

use Akvo\WvW\ParticipantRegistry\Config as AWPRConfig;
use Akvo\WvW\ParticipantRegistry\Common\Form\Register as RegisterForm;
use Akvo\WvW\ParticipantRegistry\Common\Model\Dao\ParticipantRegistry as RegisterDao;
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
		$iParticipationLastYear = \Akvo\WvW\ParticipantRegistry\Common\Model\Registry::getPastParticipationYears();

		$oForm = new RegisterForm(RegisterForm::CONTEXT_UPDATE, array('id' => 'iFormRegistrantUpdate'), array());

		if (empty($_POST)) {

			$aPopulateData = array();

			$aPopulateData['textName'] = $aDetail['name'];
			$aPopulateData['textAddress'] = $aDetail['address'];
			$aPopulateData['textCity'] = $aDetail['city'];
			$aPopulateData['textPostalCode'] = $aDetail['postal_code'];
			$aPopulateData['textContactName']= $aDetail['contact_name'];
			$aPopulateData['textEmail'] = $aDetail['email'];
			$aPopulateData['textPhone'] = $aDetail['phone'];
			$aPopulateData['textGrade7Groups'] = $aDetail['groups_grade_7'];
			$aPopulateData['textGrade8Groups'] = $aDetail['groups_grade_8'];
			$aPopulateData['textGrade67Groups'] = $aDetail['groups_grade_6_7'];
			$aPopulateData['textGrade678Groups'] = $aDetail['groups_grade_6_7_8'];
			$aPopulateData['textGrade78Groups'] = $aDetail['groups_grade_7_8'];
			$aPopulateData['textTotalStudents'] = $aDetail['total_students'];
			$aPopulateData['selectSupportPoint'] = $aDetail['support_point'];
			$aPopulateData['textWalkDate'] = date('d-m-Y', strtotime($aDetail['date_of_walk']));
			$aPopulateData['textWalkCity'] = $aDetail['city_of_walk'];
			$aPopulateData['textComments'] = $aDetail['comments'];
			$aPopulateData['textBatch'] = $aDetail['batch'];
			$aPopulateData['textLatitude'] = $aDetail['latitude'];
			$aPopulateData['textLongitude'] = $aDetail['longitude'];

			$sParticipation = $aDetail['participation'];
			if ($sParticipation == 'No') {
				$aPopulateData['radioParticipatedBefore'] = 'no';
			} else {
				$aPopulateData['radioParticipatedBefore'] = 'yes';

				$sParticipationYears = trim(str_replace('Yes', '', $sParticipation));
				if (strlen($sParticipationYears) > 0) {

					$sParticipationYears = trim(str_replace(array('(', ')'), '', $sParticipationYears));
					$aParticipationYears = explode(',', $sParticipationYears);
					foreach ($aParticipationYears as $iPastParticipationYear) {

						if ($iPastParticipationYear == 'Before two years ago') {

							$aPopulateData['checkboxParticipatedBeforeTheLastTwoYears'] = 1;

						} else {

							if (is_numeric($iPastParticipationYear)) {
								if ($iPastParticipationYear == $iParticipationLastYear) {
									$aPopulateData['checkboxParticipatedLastYear'] = 1;
								} else {

									if ($iPastParticipationYear == ($iParticipationLastYear - 1)) {

										$aPopulateData['checkboxParticipatedYearBeforeLast'] = 1;

									} else {

										if ($iPastParticipationYear < ($iParticipationLastYear - 1)){

											$aPopulateData['checkboxParticipatedBeforeTheLastTwoYears'] = 1;

										}

									}

								}
							}

						}

					}

				}

			}

			$oForm->populate($aPopulateData);

		} else {

			if ($oForm->isValid($_POST)) {

				$aFormValues = $oForm->getValues();

				$sParticipation = 'No';

				if ($aFormValues['radioParticipatedBefore'] == 'yes') {

					$sParticipation = 'Yes';
					$aParticipationYears = array();


					if ($aFormValues['checkboxParticipatedLastYear'] == 1) {
						$aParticipationYears[] = $iParticipationLastYear;
					}

					if ($aFormValues['checkboxParticipatedYearBeforeLast'] == 1) {
						$aParticipationYears[] = $iParticipationLastYear - 1;
					}

					if ($aFormValues['checkboxParticipatedBeforeTheLastTwoYears'] == 1) {
						$aParticipationYears[] = 'Before two years ago';
					}

					if (count($aParticipationYears) > 0) {
						$sParticipationYears = ' (' . implode(',', $aParticipationYears) . ')';
						$sParticipation .= $sParticipationYears;
					}

				}
//				$iBatch = $iParticipationLastYear + 1;
//
//				$sLatitude = \Akvo\WvW\ParticipantRegistry\Config::OPTION_VALUE_GOOGLE_MAPS_DEFAULT_CENTER_POINT_LATITUDE;
//				$sLongitude = \Akvo\WvW\ParticipantRegistry\Config::OPTION_VALUE_GOOGLE_MAPS_DEFAULT_CENTER_POINT_LONGITUDE;
                $sLatitude = \Akvo\WvW\ParticipantRegistry\Config::OPTION_VALUE_GOOGLE_MAPS_DEFAULT_CENTER_POINT_LATITUDE;
				$sLongitude = \Akvo\WvW\ParticipantRegistry\Config::OPTION_VALUE_GOOGLE_MAPS_DEFAULT_CENTER_POINT_LONGITUDE;
				// Try Fetching Latitude and Longitude from Google Maps API
				$sGoogleMapsGeocodeBaseUrl = 'http://maps.google.com/maps/api/geocode/json?sensor=false&address=';
				$sAddressForGeocode = $aFormValues['textAddress'] . ',' . $aFormValues['textPostalCode'] . ' ' . $aFormValues['textCity'] . ', The Netherlands';
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

				$aUpdateData['name'] = $aFormValues['textName'];
				$aUpdateData['address'] = $aFormValues['textAddress'];
				$aUpdateData['city'] = $aFormValues['textCity'];
				$aUpdateData['postal_code'] = $aFormValues['textPostalCode'];
				$aUpdateData['contact_name'] = $aFormValues['textContactName'];
				$aUpdateData['email'] = $aFormValues['textEmail'];
				$aUpdateData['phone'] = $aFormValues['textPhone'];
				$aUpdateData['participation'] = $sParticipation;
				$aUpdateData['groups_grade_7'] = $aFormValues['textGrade7Groups'];
				$aUpdateData['groups_grade_8'] = $aFormValues['textGrade8Groups'];
				$aUpdateData['groups_grade_6_7'] = $aFormValues['textGrade67Groups'];
				$aUpdateData['groups_grade_6_7_8'] = $aFormValues['textGrade678Groups'];
				$aUpdateData['groups_grade_7_8'] = $aFormValues['textGrade78Groups'];
				$aUpdateData['total_students'] = $aFormValues['textTotalStudents'];
				$aUpdateData['support_point'] = $aFormValues['selectSupportPoint'];
				$aUpdateData['date_of_walk'] = date('Y-m-d',strtotime($aFormValues['textWalkDate']));
				$aUpdateData['city_of_walk'] = $aFormValues['textWalkCity'];
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
		$iParticipationLastYear = \Akvo\WvW\ParticipantRegistry\Common\Model\Registry::getPastParticipationYears();

		$oForm = new RegisterForm(RegisterForm::CONTEXT_DELETE, array('id' => 'iFormRegistrantDelete'), array());

		if (empty($_POST)) {

			$aPopulateData = array();

			$aPopulateData['textName'] = $aDetail['name'];
			$aPopulateData['textAddress'] = $aDetail['address'];
			$aPopulateData['textCity'] = $aDetail['city'];
			$aPopulateData['textPostalCode'] = $aDetail['postal_code'];
			$aPopulateData['textContactName']= $aDetail['contact_name'];
			$aPopulateData['textEmail'] = $aDetail['email'];
			$aPopulateData['textPhone'] = $aDetail['phone'];
			$aPopulateData['textGrade7Groups'] = $aDetail['groups_grade_7'];
			$aPopulateData['textGrade8Groups'] = $aDetail['groups_grade_8'];
			$aPopulateData['textGrade67Groups'] = $aDetail['groups_grade_6_7'];
			$aPopulateData['textGrade678Groups'] = $aDetail['groups_grade_6_7_8'];
			$aPopulateData['textGrade78Groups'] = $aDetail['groups_grade_7_8'];
			$aPopulateData['textTotalStudents'] = $aDetail['total_students'];
			$aPopulateData['selectSupportPoint'] = $aDetail['support_point'];
			$aPopulateData['textWalkDate'] = date('Y-m-d', strtotime($aDetail['date_of_walk']));
			$aPopulateData['textWalkCity'] = $aDetail['city_of_walk'];
			$aPopulateData['textComments'] = $aDetail['comments'];
			$aPopulateData['textBatch'] = $aDetail['batch'];
			$aPopulateData['textLatitude'] = $aDetail['latitude'];
			$aPopulateData['textLongitude'] = $aDetail['longitude'];

			$sParticipation = $aDetail['participation'];
			if ($sParticipation == 'No') {
				$aPopulateData['radioParticipatedBefore'] = 'no';
			} else {
				$aPopulateData['radioParticipatedBefore'] = 'yes';

				$sParticipationYears = trim(str_replace('Yes', '', $sParticipation));
				if (strlen($sParticipationYears) > 0) {

					$sParticipationYears = trim(str_replace(array('(', ')'), '', $sParticipationYears));
					$aParticipationYears = explode(',', $sParticipationYears);
					foreach ($aParticipationYears as $iPastParticipationYear) {

						if ($iPastParticipationYear == 'Before two years ago') {

							$aPopulateData['checkboxParticipatedBeforeTheLastTwoYears'] = 1;

						} else {

							if (is_numeric($iPastParticipationYear)) {
								if ($iPastParticipationYear == $iParticipationLastYear) {
									$aPopulateData['checkboxParticipatedLastYear'] = 1;
								} else {

									if ($iPastParticipationYear == ($iParticipationLastYear - 1)) {

										$aPopulateData['checkboxParticipatedYearBeforeLast'] = 1;

									} else {

										if ($iPastParticipationYear < ($iParticipationLastYear - 1)){

											$aPopulateData['checkboxParticipatedBeforeTheLastTwoYears'] = 1;

										}

									}

								}
							}

						}

					}

				}

			}

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