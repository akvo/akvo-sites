<?php
namespace Akvo\WfW\ParticipantRegistry\Common\Model\Dao;

/**
 * Description of ParticipantRegistry
 *
 * @author Jayawi Perera
 */
class ParticipantRegistry {

	const PARTIAL_TABLE_NAME = 'akvo_wfw_participant_registry';

	protected $_sTableName = null;

	public function __construct () {

		global $wpdb;

		if (is_null($this->_sTableName)) {
			$this->_sTableName = $wpdb->prefix . self::PARTIAL_TABLE_NAME;
		}
	}

	public function fetch ($iId) {

		global $wpdb;

		$sQuery = "SELECT * FROM `" . $this->_sTableName . "` WHERE `id` = %d";
		$sQuery = $wpdb->prepare($sQuery , $iId);

		$aResults = $wpdb->get_row($sQuery, ARRAY_A);
		return $aResults;

	}

	public function fetchByBatch ($sBatch, $sOrderByColumn, $sOrderByDirection, $aLimit=null) {

		global $wpdb;
        
		$sQuery = "SELECT * FROM `" . $this->_sTableName . "` WHERE `batch` = '" . $sBatch . "' ORDER BY `" . $sOrderByColumn . "` " . $sOrderByDirection;
        if(!is_null($aLimit)){
            $sQuery.=' LIMIT '.$aLimit[0].','.$aLimit[1];
        }
		$aResults = $wpdb->get_results($sQuery, ARRAY_A);
		return $aResults;

	}
	

	public function fetchBatches () {

		global $wpdb;

		$sQuery = "SELECT DISTINCT(`batch`) AS `batches` FROM `" . $this->_sTableName . "` ORDER BY `batches` DESC";

		$aResults = $wpdb->get_col($sQuery);
		return $aResults;

	}

	public function insert ($aInsertData) {

		global $wpdb;

		$bStatus = $wpdb->insert($this->_sTableName, $aInsertData);
		if ($bStatus) {
			return $wpdb->insert_id;
		}
		return $bStatus;

	}
	
	public function update ($aUpdateData, $iId) {

		global $wpdb;

		$bStatus = $wpdb->update($this->_sTableName, $aUpdateData, array('id' => $iId));

		return $bStatus;

	}

	public function delete ($iId) {

		global $wpdb;

		$bStatus = $wpdb->delete($this->_sTableName, array('id' => $iId));

		return $bStatus;

	}
    
    
    
	public function createTable () {

		global $wpdb;

		$sCreateStatement =
			"CREATE TABLE IF NOT EXISTS `" . $this->_sTableName . "` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				`support_point` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				`contact_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				`country` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				`email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				`address1` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				`address2` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				`address3` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				`total_students` int(11) NOT NULL,
				`total_schools` int(11) NOT NULL,
				`date_of_walk` datetime NOT NULL,
				`id_project` int(11) NOT NULL,
				`comments` text COLLATE utf8_unicode_ci NOT NULL,
				`batch` VARCHAR(4) COLLATE utf8_unicode_ci NOT NULL,
				`latitude` VARCHAR(20) COLLATE utf8_unicode_ci NOT NULL,
				`longitude` VARCHAR(20) COLLATE utf8_unicode_ci NOT NULL,
				`date_created` datetime NOT NULL,
				`date_updated` datetime NOT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";

		$wpdb->query($sCreateStatement);
		


	}
    
    
		

	public function deleteTable () {

		global $wpdb;

		$sDropStatement = "DROP TABLE IF EXISTS `" . $this->_sTableName . "`;";
		$wpdb->query($sDropStatement);
		

	}

}