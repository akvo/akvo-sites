<?php
namespace KwgPress\Validate;

/**
 * Description of Exists
 *
 * @author Jayawi Perera
 */

class DbNoRecordExists extends ExistsAbstract {

	public function isValid ($mValue) {

		$bValid = true;
        $this->_setValue($mValue);

		$mResult = $this->_query($mValue);
		if (!is_null($mResult) && !empty($mResult)) {
            $bValid = false;
            $this->_error(self::ERROR_RECORD_FOUND);
        }

        return $bValid;

	}

}