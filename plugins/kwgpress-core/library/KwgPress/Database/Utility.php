<?php
namespace KwgPress\Database;

use KwgPress\Configuration as KwgPConfig;
/**
 * Description of Utility
 *
 * @author Jayawi Perera
 */
class Utility {

	public static function transactionBegin () {

		global $wpdb;

		$wpdb->query('START TRANSACTION;');

	}

	public static function transactionCommit () {

		global $wpdb;

		$wpdb->query('COMMIT;');

	}

	public static function transactionRollback () {

		global $wpdb;

		$wpdb->query('ROLLBACK;');

	}

	public static function getTableName ($sPlugin, $sTableIdentifier) {

		global $wpdb;

		$sTableName = null;

		$sConfiguredTableName = KwgPConfig::get($sPlugin, 'database_tables.' . $sTableIdentifier);
		if (!is_null($sConfiguredTableName)) {
			$sTableName = $wpdb->prefix . $sConfiguredTableName;
		}

		return $sTableName;
	}

}