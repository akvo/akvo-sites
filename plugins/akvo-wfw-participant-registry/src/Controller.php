<?php
namespace Akvo\WfW\ParticipantRegistry;

use KwgPress as KwgP;
//use KwgPressFormSetUp\Version\Manager as KwgPFSVersioNManager;
/**
 * Primary Controller for the entire Plugin
 *
 * @author Jayawi Perera
 */
class Controller {

	const KWGPRESS_CORE_FUNCTION = 'is_kwgpress_core_active';

	public static $oInstance = null;

	public static function getInstance () {

		if (is_null(self::$oInstance)) {
			self::$oInstance = new self;
		}

		return self::$oInstance;

	}

	private function _isCoreActive () {

		return function_exists(self::KWGPRESS_CORE_FUNCTION);

	}

	public function initialise () {

		register_activation_hook(AkvoWfwParticipantRegistry_Plugin_File, array('Akvo\WfW\ParticipantRegistry\Controller', 'doActivation'));
		register_deactivation_hook(AkvoWfwParticipantRegistry_Plugin_File, array('Akvo\WfW\ParticipantRegistry\Controller', 'doDeactivation'));
		register_uninstall_hook(AkvoWfwParticipantRegistry_Plugin_File, array('Akvo\WfW\ParticipantRegistry\Controller', 'doUninstall'));
		add_action('activated_plugin', array('Akvo\WfW\ParticipantRegistry\Controller', 'setToLoadLast'));

		if ($this->_isCoreActive()) {

			$this->_initForms();

			if (is_admin()) {
				// Dashboard

				$oAdminController = new Admin\Controller();
				$oAdminController->initialise();

			} else {
				// Front-end
				$oFrontEndController = new Fe\Controller();
				$oFrontEndController->initialise();

			}

		} else {

			if (is_admin()) {
				// Dashboard

				$oAdminController = new Admin\Controller();
				$oAdminController->initialiseLimited();

			} else {
				// Front-end


			}

		}

	}

	private function _initForms () {

		// Set View Script Base Paths
		KwgP\Form::setViewScriptBasePaths(Config::getFormViewScriptBasePaths());

		// Set Translation Language
//		$aTranslations = require_once AkvoWfwParticipantRegistry_Plugin_Dir . '/resources/Zend/languages/nl/Zend_Validate.php';
//		$oTranslator = new \Zend_Translate(
//			array(
//				'adapter' => 'array',
//				'content' => $aTranslations,
//				'locale' => 'nl',
//			)
//		);
//		\Zend_Validate::setDefaultTranslator($oTranslator);

	}

	public static function doActivation () {

		$oDaoRegister = new Common\Model\Dao\ParticipantRegistry();
//		$oDaoRegister->deleteTable();
		$oDaoRegister->createTable();

		add_option(Config::OPTION_NAME_GOOGLE_MAPS_API_KEY, Config::OPTION_NAME_GOOGLE_MAPS_API_KEY);
		add_option(Config::OPTION_NAME_GOOGLE_MAPS_DEFAULT_ZOOM_FACTOR, Config::OPTION_VALUE_GOOGLE_MAPS_DEFAULT_ZOOM_FACTOR);
		add_option(Config::OPTION_NAME_GOOGLE_MAPS_DEFAULT_CENTER_POINT, array('latitude' => Config::OPTION_VALUE_GOOGLE_MAPS_DEFAULT_CENTER_POINT_LATITUDE, 'longitude' => Config::OPTION_VALUE_GOOGLE_MAPS_DEFAULT_CENTER_POINT_LONGITUDE));

		get_role("administrator")->add_cap(\Akvo\WfW\ParticipantRegistry\Config::CAPABILITY_GENERAL_NAME);
		get_role("editor")->add_cap(\Akvo\WfW\ParticipantRegistry\Config::CAPABILITY_GENERAL_NAME);

	}

	public static function doDeactivation () {

		get_role("administrator")->remove_cap(\Akvo\WfW\ParticipantRegistry\Config::CAPABILITY_GENERAL_NAME);
		get_role("editor")->remove_cap(\Akvo\WfW\ParticipantRegistry\Config::CAPABILITY_GENERAL_NAME);

	}

	public static function doUninstall () {

		$oDaoRegister = new Common\Model\Dao\ParticipantRegistry();
		$oDaoRegister->deleteTable();

		delete_option(Config::OPTION_NAME_GOOGLE_MAPS_API_KEY);
		delete_option(Config::OPTION_NAME_GOOGLE_MAPS_DEFAULT_ZOOM_FACTOR);
		delete_option(Config::OPTION_NAME_GOOGLE_MAPS_DEFAULT_CENTER_POINT);

	}

	public static function setToLoadLast () {

		$sThisPluginFile = AkvoWfwParticipantRegistry_Plugin_DirFile;

		$aActivePlugins = get_option('active_plugins');
		$iPluginCount = count($aActivePlugins);

		$iThisPluginKey = array_search($sThisPluginFile, $aActivePlugins);

		if ($iThisPluginKey != ($iPluginCount - 1)) {

			array_splice($aActivePlugins, $iThisPluginKey, 1);
			$aActivePlugins[] = $sThisPluginFile;
			update_option('active_plugins', $aActivePlugins);

		}

	}

}