<?php
namespace KwgPress;

require_once realpath(dirname(__FILE__) . '/../Recaptcha/recaptchalib.php');

/**
 * Description of Recaptcha
 *
 * @author Jayawi Perera
 */
class Recaptcha {

	const ERROR_INVALID_PRIVATE_KEY = 'invalid-site-private-key';
	const ERROR_INVALID_REQUEST_COOKIE = 'invalid-request-cookie';
	const ERROR_INCORRECT_CAPTCHA = 'incorrect-captcha-sol';
	const ERROR_TIMEOUT = 'captcha-timeout';
	const ERROR_RECAPTCHA_NOT_REACHABLE = 'recaptcha-not-reachable';

	protected static $oInstance = null;

	protected static $sPublicKey = null;
	protected static $sPrivateKey = null;

	protected static $oLastResponse = null;
	protected static $sLastErrorMessage = null;
	protected static $sLastErrorCode = null;

	protected static $bUseSsl = false;

	protected function __construct () {
	}
	protected function __clone() {
	}

	/**
	 *
	 * @return Recaptcha
	 */
	public static function getInstance () {

		if (is_null(static::$oInstance)) {
			static::$oInstance = new static;
		}

		return static::$oInstance;

	}

	public function setPublicKey ($sPublicKey) {
		static::$sPublicKey = $sPublicKey;
	}

	public function setPrivateKey ($sPrivateKey) {
		static::$sPrivateKey = $sPrivateKey;
	}

	public function render ($bReturn = false) {

		$sHtml = \recaptcha_get_html(static::$sPublicKey, static::$sLastErrorCode, static::$bUseSsl);

		if ($bReturn) {
			return $sHtml;
		} else {
			echo $sHtml;
		}
	}

	public function isValid ($sChallenge, $sResponse) {

		$this->_clearError();

		static::$oLastResponse = \recaptcha_check_answer(static::$sPrivateKey, $_SERVER['REMOTE_ADDR'], $sChallenge, $sResponse);
		$bIsValid = static::$oLastResponse->is_valid;

		if (!$bIsValid) {
			$this->_setError(static::$oLastResponse->error);
		}

		return $bIsValid;

	}

	public function error ($bHumanReadable = false) {

		if ($bHumanReadable) {
			return static::$sLastErrorMessage;
		} else {
			return static::$sLastErrorCode;
		}

	}

	private function _clearError () {
		static::$sLastErrorMessage = null;
		static::$sLastErrorCode = null;
	}

	private function _setError ($sErrorCode) {

		static::$sLastErrorCode = $sErrorCode;

		$sErrorMessage = null;
		switch ($sErrorCode) {
			case self::ERROR_INVALID_PRIVATE_KEY:
				$sErrorMessage = 'Could not verify Private Key. Contact Site Administrators if error persists.';
				break;
			case self::ERROR_INVALID_REQUEST_COOKIE:
				$sErrorMessage = 'Challenge Parameter was incorrect. Contact Site Administrators if error persists.';
				break;
			case self::ERROR_INCORRECT_CAPTCHA:
				$sErrorMessage = 'Incorrect CAPTCHA Solution provided. Please try again.';
				break;
			case self::ERROR_TIMEOUT:
				$sErrorMessage = 'The CAPTCHA has expired. Please try again.';
				break;
			case self::ERROR_RECAPTCHA_NOT_REACHABLE:
				$sErrorMessage = 'reCAPTCHA Server is not reachable. Please try again.';
				break;
			default:
				$sErrorMessage = 'An unknown/unexpected error has occurred. Contact Site Administrators if error persists.';
				break;
		}
		static::$sLastErrorMessage = $sErrorMessage;

	}

}