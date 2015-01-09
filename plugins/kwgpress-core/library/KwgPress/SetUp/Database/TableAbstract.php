<?php
namespace KwgPress\Setup\Database;

use KwgPress\Configuration as KwgPConfig;
use KwgPress\Database\Utility as KwgPDatabaseUtility;
use KwgPress\Version\Utility as KwgPVersionUtility;

/**
 * Description of Table
 *
 * @author Jayawi Perera
 */
abstract class TableAbstract {

	public function getTableName () {

		return KwgPDatabaseUtility::getTableName($this->getPlugin(), $this->getTableKey());

	}

	public function setup () {

		// Get Prior Installed Version
		$sPriorInstalledVersion = get_option(KwgPConfig::get($this->getPlugin(), 'wp_option_names.version'), '0.0.0');
		$aComparisonsAllowed = array(
			KwgPVersionUtility::VERSION_COMPARISON_NEWER,
//			KwgPVersionUtility::VERSION_COMPARISON_EQUAL,
		);

		$aChanges = $this->getChanges();
		if (is_array($aChanges) && !empty($aChanges)) {
			
			foreach ($aChanges as $sVersion => $sMethod) {

				if (in_array(KwgPVersionUtility::compareVersions($sPriorInstalledVersion, $sVersion), $aComparisonsAllowed)) {

					$this->$sMethod();

				}

			}

		}


	}

	abstract protected function getPlugin();
	abstract protected function getTableKey();
	abstract protected function getChanges();

}