<?php
namespace Akvo\WvW\ParticipantRegistry\Common\Model\Dao;

/**
 * Description of ParticipantRegistry
 *
 * @author Jayawi Perera
 */
class ParticipantRegistry {

	const PARTIAL_TABLE_NAME = 'akvo_wvw_participant_registry';
	const PARTIAL_SUPPORTPOINTS_TABLE_NAME = 'akvo_wvw_participant_registry_supportpoint';

	protected $_sTableName = null;
	protected $_sTableSupportpointsName = null;

	public function __construct () {

		global $wpdb;

		if (is_null($this->_sTableName)) {
			$this->_sTableName = $wpdb->prefix . self::PARTIAL_TABLE_NAME;
			$this->_sTableSupportpointsName = $wpdb->prefix . self::PARTIAL_SUPPORTPOINTS_TABLE_NAME;
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
	public function fetchBySupportPoint ($iSupportPoint) {

		global $wpdb;
        
		$sQuery = "SELECT * FROM `" . $this->_sTableName . "` WHERE `support_point` = '" . $iSupportPoint . "'";
        
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
    
    public function fetchSupportpoints(){
        global $wpdb;
        
		$sQuery = "SELECT * FROM `" . $this->_sTableSupportpointsName . "` ORDER BY name ASC";
        
		$aResults = $wpdb->get_results($sQuery, ARRAY_A);
		return $aResults;
    }

    public function insertSupportPoint ($sName) {

		global $wpdb;

		$bStatus = $wpdb->insert($this->_sTableSupportpointsName, array('name'=>$sName));
		if ($bStatus) {
			return $wpdb->insert_id;
		}
		return $bStatus;

	}
    
	public function createTable () {

		global $wpdb;

		$sCreateStatement =
			"CREATE TABLE IF NOT EXISTS `" . $this->_sTableName . "` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				`address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				`city` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				`postal_code` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
				`contact_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				`email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				`phone` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
				`participation` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				`groups_grade_7` int(11) NOT NULL DEFAULT '0',
				`groups_grade_8` int(11) NOT NULL DEFAULT '0',
				`groups_grade_6_7` int(11) NOT NULL DEFAULT '0',
				`groups_grade_6_7_8` int(11) NOT NULL DEFAULT '0',
				`groups_grade_7_8` int(11) NOT NULL DEFAULT '0',
				`total_students` int(11) NOT NULL,
				`support_point` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				`date_of_walk` datetime NOT NULL,
				`city_of_walk` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				`comments` text COLLATE utf8_unicode_ci NOT NULL,
				`batch` VARCHAR(4) COLLATE utf8_unicode_ci NOT NULL,
				`latitude` VARCHAR(20) COLLATE utf8_unicode_ci NOT NULL,
				`longitude` VARCHAR(20) COLLATE utf8_unicode_ci NOT NULL,
				`date_created` datetime NOT NULL,
				`date_updated` datetime NOT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";

		$wpdb->query($sCreateStatement);
		$sCreateStatement =
			"CREATE TABLE IF NOT EXISTS `" . $this->_sTableSupportpointsName . "` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";

		$wpdb->query($sCreateStatement);
        $this->fillSupportPoints();


	}
    
    public function fillSupportPoints(){
        global $wpdb;
		$aSupportPoints = array(
			9999=>'(nog onbekend)',
			1=>'AMREF Flying Doctors',
			2=>'Association for Small African Projects (ASAP)',
			3=>'Daphne Foundation',
			4=>'GAiN Water for Life',
			5=>'Gemeente Gorinchem',
			7=>'Le Pont',
			8=>'Max Foundation',
			9=>'RAMRO',
			10=>'RC Aalten-Wisch',
			11=>'RC Almere New Town',
            6=>'RC Alphen-Woubrugge',
			12=>'RC Amersfoort',
			13=>'RC Bathmen de Schipbeek',
			14=>'RC Bergh',
			15=>'RC Bunnik-Zeist',
			16=>'RC Deventer & AMREF',
			17=>'RC Dokkum & ZOA',
			18=>'RC Doorn',
			19=>'RC Duiven',
			20=>"RC Eemland 't Gooi",
			21=>'RC Epe',
			22=>'RC Etten Leur',
			23=>'RC Gouda Bloemendaal',
			24=>'RC Hattem-Heerde',
			25=>'RC Houten',
			26=>'RC Leudal',
			27=>'RC Lingewaard-Bemmel & ZOA',
			28=>'RC Lopikerwaard',
			29=>'RC Maarssen-Breukelen',
			30=>'RC Maassluis',
			31=>'RC Nieuwegein',
			32=>'RC Ter Aar',
			33=>'RC Twenterand (Vroomshoop)',
			34=>'RC Utrecht - Fletiomare',
			35=>'RC Utrecht Kommerijn',
			36=>'RC Utrecht West',
			37=>'RC Veluwezoom',
			38=>'RC Vinkeveen-Abcoude',
			39=>'RC Vlaardingen & ZOA',
			40=>'RC Voorburg-Vliet',
			41=>'RC Vorden-Zutphen',
			42=>'RC Wierden',
			43=>'RC Wierden',
			44=>'RC Winterswijk',
			45=>'RC Zeist',
			46=>'RC Zevenaar',
			47=>'RC de Rottemeren',
			48=>'RCs Almelo & Ambt Almelo',
			49=>'RCs Appingedam, Delfzijl & Eems-Dollard',
			50=>'RCs Enschede',
			51=>'RCs Nijmegen & Nijmegen Zuid',
			52=>"Rotary Apeldoorn 't  Loo",
			53=>'Rotary Club District 1560 - Gelderland',
			54=>'Rotary Club District 1590 - Regio Lelystad',
			55=>'Rotary Club Reeuwijk',
			56=>'Rotary Club Rijssen',
			57=>'Rotary Club Staphorst & Simavi',
			58=>'Rotary Club Zwolle - Waterschap Groot-Salland',
			59=>'Rotary Club de Rottemeren & Simavi',
			60=>'Rotary Clubs Noord Veluwe',
			61=>'Simavi',
			62=>'Stedenband Tilburg-Same Tanzania',
			63=>'Stichting Dorcas',
			64=>'Stichting Namelok',
			65=>'Stichting Projectkoppeling Eindhoven Gedaref (SPEG)',
			66=>'Stichting Tenda Pamoja',
			67=>'Stichting Vrienden van de Sahel',
			68=>'Waterschap Zuiderzeeland',
			69=>'ZOA',
		);
        //$wpdb->query("TRUNCATE TABLE ".$this->_sTableSupportpointsName);
        foreach($aSupportPoints AS $id=>$sName){
            $wpdb->insert($this->_sTableSupportpointsName,array('id'=>$id,'name'=>$sName),array('%d','%s'));
        }
		//return $aOptions;

    }

	public function deleteTable () {

		global $wpdb;

		$sDropStatement = "DROP TABLE IF EXISTS `" . $this->_sTableName . "`;";
		$wpdb->query($sDropStatement);
		$sDropStatement = "DROP TABLE IF EXISTS `" . $this->_sTableSupportpointsName . "`;";
		$wpdb->query($sDropStatement);

	}

}