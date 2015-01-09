<?php
namespace Akvo\WfW\ParticipantRegistry\Fe\Model;

use KwgPress as KwgP;
use Akvo\WfW\ParticipantRegistry\Common\Form\Register as RegisterForm;
use Akvo\WfW\ParticipantRegistry\Common\Model\Dao\ParticipantRegistry as RegisterDao;
/**
 * Description of FormHandler
 *
 * @author Jayawi Perera
 */
class FormHandler {

	public function process () {

		$aContent = array();

		$oRecaptcha = KwgP\Recaptcha::getInstance();
		$oRecaptcha->setPublicKey(\Akvo\WfW\ParticipantRegistry\Config::RECAPTCHA_PUBLIC_KEY);
		$oRecaptcha->setPrivateKey(\Akvo\WfW\ParticipantRegistry\Config::RECAPTCHA_PRIVATE_KEY);

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
                $iParticipationLastYear = \Akvo\WfW\ParticipantRegistry\Common\Model\Registry::getPastParticipationYears();
				
				
				$iBatch = $iParticipationLastYear + 1;

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

//				$aInsertData['id'] = $aFormValues[''];
				$aInsertData['name'] = $aFormValues['textName'];
				$aInsertData['support_point'] = $aFormValues['textSupportOrganisation'];
				$aInsertData['contact_name'] = $aFormValues['textContactName'];
				$aInsertData['country'] = $aFormValues['textCountry'];
				$aInsertData['email'] = $aFormValues['textEmail'];
				$aInsertData['address1'] = $aFormValues['textAddress1'];
				$aInsertData['address2'] = $aFormValues['textAddress2'];
				$aInsertData['address3'] = $aFormValues['textAddress3'];
				$aInsertData['total_students'] = $aFormValues['textTotalStudents'];
				$aInsertData['total_schools'] = $aFormValues['textTotalSchools'];
				$aInsertData['date_of_walk'] = date('Y-m-d', strtotime($aFormValues['textWalkDate']));
				$aInsertData['id_project'] = $aFormValues['selectProject'];
				$aInsertData['comments'] = $aFormValues['textComments'];
				$aInsertData['batch'] = $iBatch;
				$aInsertData['latitude'] = $sLatitude;
				$aInsertData['longitude'] = $sLongitude;
				$aInsertData['date_created'] = date('Y-m-d H:i:s');
				$aInsertData['date_updated'] = date('Y-m-d H:i:s');
//				var_dump($aInsertData);

				$oDaoParticipantRegistry = new RegisterDao();
				$oDaoParticipantRegistry->insert($aInsertData);
                
                $sEmailContent = 'New school registration walkingforwater.eu:<br />';
                $sEmailContent .= 'Name organisation / school: '.$aInsertData['name'].'<br />';
                $sEmailContent .= 'Name of the support center organisation: '.$aInsertData['support_point'].'<br />';
                $sEmailContent .= 'Name contact person: '.$aInsertData['contact_name'].'<br />';
                $sEmailContent .= 'Email address: '.$aInsertData['email'].'<br />';
                $sEmailContent .= 'Country: '.$aInsertData['country'].'<br />';
                $sEmailContent .= 'Postal address line 1: '.$aInsertData['address1'].'<br />';
                $sEmailContent .= 'line 2: '.$aInsertData['address2'].'<br />';
                $sEmailContent .= 'line 3: '.$aInsertData['address3'].'<br />';
                $sEmailContent .= 'How many children will participate in total: '.$aInsertData['total_students'].'<br />';
                $sEmailContent .= 'How many schools will participate: '.$aInsertData['total_schools'].'<br />';
                $sEmailContent .= 'Supported project: '.$aInsertData['id_project'].'<br />';
                $sEmailContent .= 'Planned date of event: '.$aInsertData['date_of_walk'].'<br />';
                $sEmailContent .= 'Comments: '.$aInsertData['comments'].'<br />';
                add_filter( 'wp_mail_content_type', 'set_html_content_type' );
                wp_mail( 'c.amsinger@aquaforall.org', 'New school registration walkingforwater.eu', $sEmailContent );
                // Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
                remove_filter( 'wp_mail_content_type', 'set_html_content_type' );

                $aContent['form_valid']='Thank you for your registration!';

			} else {

				$aContent['form_error'] = 'Your form input is not valid. Please correct the errors and try again';

			}

		}

		$aContent['form'] = $oForm;

		return $aContent;
	}

}