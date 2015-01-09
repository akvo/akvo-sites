<?php
namespace Akvo\WvW\ParticipantRegistry;
/**
 * Description of Config
 *
 * @author Jayawi Perera
 */
class Config {

	const SHORTCODE_FORM = 'akvo_wvw_pr_form';
	const SHORTCODE_MAP = 'akvo_wvw_pr_map';
	const SHORTCODE_LIST = 'akvo_wvw_pr_list';

	const OPTION_NAME_GOOGLE_MAPS_API_KEY = 'akvo_wvw_google_maps_api_key';
	const OPTION_VALUE_GOOGLE_MAPS_API_KEY = 'AIzaSyAfTGXnvVyrBRlva1g42zO63-_bIJtfF5U';

	const OPTION_NAME_GOOGLE_MAPS_DEFAULT_ZOOM_FACTOR = 'akvo_wvw_google_maps_default_zoom_factor';
	const OPTION_VALUE_GOOGLE_MAPS_DEFAULT_ZOOM_FACTOR = '8';

	const OPTION_NAME_GOOGLE_MAPS_DEFAULT_CENTER_POINT = 'akvo_wvw_google_maps_default_center_point';
	const OPTION_VALUE_GOOGLE_MAPS_DEFAULT_CENTER_POINT_LATITUDE = '52.3731';
	const OPTION_VALUE_GOOGLE_MAPS_DEFAULT_CENTER_POINT_LONGITUDE = '4.8922';

	const CAPABILITY_GENERAL_NAME = 'akvo_wvw_pr_cap_general';

	const RECAPTCHA_PUBLIC_KEY = '6LcNvOcSAAAAAImPKordoP4NUZlIEemksqFJOIBZ';
	const RECAPTCHA_PRIVATE_KEY = '6LcNvOcSAAAAAPpTYyl2DC3m66fWRcc3PAEMV7kE';

	public static function getFormViewScriptBasePaths () {
		return array(
			AkvoWvwParticipantRegistry_Plugin_Dir . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Fe' .DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR,
			AkvoWvwParticipantRegistry_Plugin_Dir . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Admin' .DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR,
		);
	}

	public static function getHomeRedirectUrl () {

		return menu_page_url(\Akvo\WvW\ParticipantRegistry\Admin\Controller\Home::MENU_SLUG, false);

	}

}