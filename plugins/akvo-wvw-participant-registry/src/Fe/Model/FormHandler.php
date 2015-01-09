<?php
namespace Akvo\WvW\ParticipantRegistry\Fe\Model;

use KwgPress as KwgP;
use Akvo\WvW\ParticipantRegistry\Common\Form\Register as RegisterForm;
use Akvo\WvW\ParticipantRegistry\Common\Model\Dao\ParticipantRegistry as RegisterDao;
/**
 * Description of FormHandler
 *
 * @author Jayawi Perera
 */
class FormHandler {

	public function process () {

		$aContent = array();

		$oRecaptcha = KwgP\Recaptcha::getInstance();
		$oRecaptcha->setPublicKey(\Akvo\WvW\ParticipantRegistry\Config::RECAPTCHA_PUBLIC_KEY);
		$oRecaptcha->setPrivateKey(\Akvo\WvW\ParticipantRegistry\Config::RECAPTCHA_PRIVATE_KEY);

		$oForm = new RegisterForm(RegisterForm::CONTEXT_CREATE, array('id' => 'iFormParticipantRegistryRegister'), array('show_recaptcha' => true));

		if (empty($_POST)) {



		} else {

			$bCaptchaValid = false;
			$sRecaptchaChallenge = null;
			if (isset($_POST['recaptcha_challenge_field'])) {
				$sRecaptchaChallenge = $_POST['recaptcha_challenge_field'];
			}
			$sRecaptchaResponse = null;
			if (isset($_POST['recaptcha_response_field'])) {
				$sRecaptchaResponse = $_POST['recaptcha_response_field'];
			}

			$bCaptchaValid = $oRecaptcha->isValid($sRecaptchaChallenge, $sRecaptchaResponse);

			if ($oForm->isValid($_POST) && $bCaptchaValid) {

				$aFormValues = $oForm->getValues();

				$sParticipation = 'No';

				$iParticipationLastYear = \Akvo\WvW\ParticipantRegistry\Common\Model\Registry::getPastParticipationYears();
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
				$iBatch = $iParticipationLastYear + 1;

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

//				$aInsertData['id'] = $aFormValues[''];
				$aInsertData['name'] = $aFormValues['textName'];
				$aInsertData['address'] = $aFormValues['textAddress'];
				$aInsertData['city'] = $aFormValues['textCity'];
				$aInsertData['postal_code'] = $aFormValues['textPostalCode'];
				$aInsertData['contact_name'] = $aFormValues['textContactName'];
				$aInsertData['email'] = $aFormValues['textEmail'];
				$aInsertData['phone'] = $aFormValues['textPhone'];
				$aInsertData['participation'] = $sParticipation;
				$aInsertData['groups_grade_7'] = $aFormValues['textGrade7Groups'];
				$aInsertData['groups_grade_8'] = $aFormValues['textGrade8Groups'];
				$aInsertData['groups_grade_6_7'] = $aFormValues['textGrade67Groups'];
				$aInsertData['groups_grade_6_7_8'] = $aFormValues['textGrade678Groups'];
				$aInsertData['groups_grade_7_8'] = $aFormValues['textGrade78Groups'];
				$aInsertData['total_students'] = $aFormValues['textTotalStudents'];
				$aInsertData['support_point'] = $aFormValues['selectSupportPoint'];
				$aInsertData['date_of_walk'] = date('Y-m-d', strtotime($aFormValues['textWalkDate']));
				$aInsertData['city_of_walk'] = $aFormValues['textWalkCity'];
				$aInsertData['comments'] = $aFormValues['textComments'];
				$aInsertData['batch'] = $iBatch;
				$aInsertData['latitude'] = $sLatitude;
				$aInsertData['longitude'] = $sLongitude;
				$aInsertData['date_created'] = date('Y-m-d H:i:s');
				$aInsertData['date_updated'] = date('Y-m-d H:i:s');
//				var_dump($aInsertData);

				$oDaoParticipantRegistry = new RegisterDao();
				$oDaoParticipantRegistry->insert($aInsertData);
                $aContent['form_valid']='Bedankt voor uw registratie!<br /><br />Voor kinderen van een jongere leeftijd, hebben kinderhelden Ernst Bobbie en de Rest een nieuwe, superleuke live theatershow gemaakt -  WIJ WILLEN WATER! Op een Ernst en Bobbie-manier (avontuur, liedjes een heel veel grapjes), worden kinderen bewust gemaakt van het belang van schoon drinkwater voor ons allemaal!<br />Kijk voor de locatie bij jou in de buurt op <a href="http://www.ernstbobbie.nl">www.ernstbobbie.nl</a> .';
//				$oForm = new RegisterForm(RegisterForm::CONTEXT_CREATE, array('id' => 'iFormParticipantRegistryRegister'), array());

			} else {

				$aContent['form_error'] = 'Het formulier is niet correct ingevuld. Corrigeer de fouten en probeer het nogmaals.';

			}

		}

		$aContent['form'] = $oForm;

		return $aContent;
	}

}