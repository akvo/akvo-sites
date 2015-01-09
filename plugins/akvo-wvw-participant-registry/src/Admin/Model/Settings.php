<?php
namespace Akvo\WvW\ParticipantRegistry\Admin\Model;

use Akvo\WvW\ParticipantRegistry\Admin\Form as AWPRAdminForm;
use Akvo\WvW\ParticipantRegistry\Config as AWPRConfig;
/**
 * Description of Settings
 *
 * @author Jayawi Perera
 */
class Settings {

	public function manage () {

		$oForm = new AWPRAdminForm\Settings(AWPRAdminForm\Settings::CONTEXT_CREATE);

		if (empty($_POST)) {

			$aCenterPoint = get_option(AWPRConfig::OPTION_NAME_GOOGLE_MAPS_DEFAULT_CENTER_POINT);
			$aPopulateData = array(
				'textGoogleMapsApiKey' => get_option(AWPRConfig::OPTION_NAME_GOOGLE_MAPS_API_KEY),
				'textGoogleMapsDefaultZoomFactor' => get_option(AWPRConfig::OPTION_NAME_GOOGLE_MAPS_DEFAULT_ZOOM_FACTOR),
				'textGoogleMapsDefaultCenterPointLatitude' => $aCenterPoint['latitude'],
				'textGoogleMapsDefaultCenterPointLongitude' => $aCenterPoint['longitude'],
			);
			$oForm->populate($aPopulateData);

		} else {

			if ($oForm->isValid($_POST)) {

				$aFormValues = $oForm->getValues();

				
                $sApiKey = $aFormValues['textGoogleMapsApiKey'];
				update_option(AWPRConfig::OPTION_NAME_GOOGLE_MAPS_API_KEY, $sApiKey);

				$sDefaultZoomFactor = $aFormValues['textGoogleMapsDefaultZoomFactor'];
				update_option(AWPRConfig::OPTION_NAME_GOOGLE_MAPS_DEFAULT_ZOOM_FACTOR, $sDefaultZoomFactor);

				$aCenterPoint = array(
					'latitude' => $aFormValues['textGoogleMapsDefaultCenterPointLatitude'],
					'longitude' => $aFormValues['textGoogleMapsDefaultCenterPointLongitude'],
				);
				update_option(AWPRConfig::OPTION_NAME_GOOGLE_MAPS_DEFAULT_CENTER_POINT, $aCenterPoint);

			}

		}


		$aContent = array(
			'form' => $oForm,
		);

		return $aContent;
	}

}