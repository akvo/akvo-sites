<?php
namespace KwgPress;
/**
 * Description of Configuration
 *
 * @author Jayawi Perera
 */
class Configuration {

	public static $oInstance = null;

	protected static $_aPluginConfiguration = array();

	public static function getInstance () {

		if (is_null(self::$oInstance)) {
			self::$oInstance = new self;
		}

		return self::$oInstance;
	}

	public function addConfiguration ($sPlugin, $aConfiguration, $aOptions = array()) {

		if (!isset(self::$_aPluginConfiguration[$sPlugin])) {
			self::$_aPluginConfiguration[$sPlugin] = $aConfiguration;
		}

	}

	public static function get ($sPlugin, $mOption) {

		if (is_null(self::$_aPluginConfiguration) || empty(self::$_aPluginConfiguration)) {
			return null;
		}
		if (!isset(self::$_aPluginConfiguration[$sPlugin])) {
			return null;
		}

		if (!isset($mOption) || is_null($mOption)) {
			return null;
		}

		if (!is_array($mOption)) {
			if (is_string($mOption)) {
				$mOption = explode('.', $mOption);
			} else {
				$mOption = array($mOption);
			}

		}

		return self::traverse($mOption, self::$_aPluginConfiguration[$sPlugin]);
	}

	protected static function traverse ($aOption, $mConfig) {
		$sCurrentOption = array_shift($aOption);
		if (isset($mConfig[$sCurrentOption])) {
			$mConfig = $mConfig[$sCurrentOption];
			if (empty($aOption)) {
				return $mConfig;
			} else {
				return self::traverse($aOption, $mConfig);
			}
		} else {
			return null;
		}
	}

}