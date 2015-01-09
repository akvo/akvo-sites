<?php
namespace KwgPress\Validate;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Abstract
 *
 * @author Jayawi Perera
 */
abstract class ExistsAbstract extends \Zend_Validate_Abstract {

	const ERROR_NO_RECORD_FOUND = 'noRecordFound';
    const ERROR_RECORD_FOUND    = 'recordFound';

	protected $_messageTemplates = array(
        self::ERROR_NO_RECORD_FOUND => "No record matching '%value%' was found",
        self::ERROR_RECORD_FOUND    => "A record matching '%value%' was found",
    );

	protected $_sTable = '';

	protected $_sField = '';

	protected $_aExclude = null;

	public function __construct (array $aOptions) {

		if (!array_key_exists('table', $aOptions)) {
            require_once 'Zend/Validate/Exception.php';
            throw new \Zend_Validate_Exception('Table or Schema option missing!');
        }

		if (!array_key_exists('field', $aOptions)) {
            require_once 'Zend/Validate/Exception.php';
            throw new \Zend_Validate_Exception('Field option missing!');
        }

		 $this->setTable($aOptions['table']);
		 $this->setField($aOptions['field']);

		 if (isset($aOptions['exclude'])) {
			 $this->setExclude($aOptions['exclude']);
		 }
	}

	public function getTable () {
		return $this->_sTable;
	}
	public function setTable ($sTable) {
		$this->_sTable = (string) $sTable;
		return $this;
    }

	public function getField () {
        return $this->_sField;
    }
	public function setField ($sField) {
		$this->_sField = (string) $sField;
		return $this;
    }

	public function getExclude () {
		return $this->_aExclude;
	}
	public function setExclude ($aExclude) {
		$this->_aExclude = $aExclude;
	}

	protected function _query ($mValue) {

		global $wpdb;

		$sQuery = 'SELECT * FROM `' . $this->getTable() . '` WHERE `' . $this->getField() . '` = %s';
		$aParameters = array($mValue);

		$aExclude = $this->getExclude();
		if (!is_null($aExclude)) {
			$sQuery .= ' AND `' . $aExclude['field'] . '` <> %s';
			array_push($aParameters, $aExclude['value']);
		}

		$sPreparedQuery = $wpdb->prepare($sQuery, $aParameters);

		$mResult = $wpdb->get_results($sPreparedQuery, ARRAY_A);

		return $mResult;

    }

}