<?php

/**
 * Plugin for Akvo.org Partners to retrieve Projects and Project Updates form Akvo.org to display in the Partner Sites
 *
 * @author Uthpala Sandirigama
 */
if (!class_exists("AkvoPartnerCommunication")) {

	/**
	 *
	 */
	class AkvoPartnerCommunication {
		/**
		 *
		 */

		const API_URL_FOR_PROJECTS = 'http://rsr.akvo.org/api/v1/project/?format=json';
//		const API_URL_FOR_PROJECTS = 'http://rsr.akvo.org/api/v1/project/?format=json&partnerships__organisation=';
		//const API_URL_FOR_PROJECTS = 'http://www.akvo.org/rsr/api/projects.json/live-earth/'; // Old Url
        const API_URL_FOR_COUNTRIES = 'http://rsr.akvo.org/api/v1/country/?format=json&limit=0';
        const API_URL_FOR_LOCATIONS = 'http://rsr.akvo.org/api/v1/project_location/?format=json&limit=';
        const API_URL_FOR_PARTNERS = 'http://rsr.akvo.org/api/v1/organisation/?format=json&partnerships__project__in=';
		/**
		 *
		 */
		const API_URL_FOR_PROJECT_UPDATES = 'http://rsr.akvo.org/api/v1/project_update/?project__partnerships__organisation=';

		/**
		 *
		 */
		const TBL_PARTNERDETAILS = "partner_details";

		/**
		 *
		 */
		const TBL_PROJUPDATES = "project_update_log";
		const PROJECT_UPDATE_IMAGE_WIDTH = 271;
		const PROJECT_UPDATE_IMAGE_HEIGHT = 167;

		/**
		 *
		 */
		public function __construct() {

		}

		/**
		 * Carries out the operations that need to be done when the Plugin is Activated
		 *
		 * @global type $wpdb
		 */
		public function install() {

			$this->createPartnerDetailsTable();
			$this->createProjectsTable();
			$this->createProjectLocationsTable();
			$this->createProjectPartnersTable();

			// Make inactive Partner record, active again
			global $wpdb;
			$sTableName = self::TBL_PARTNERDETAILS;

			$aCurrentPartnerListing = $wpdb->get_results("SELECT * FROM " . $sTableName, ARRAY_A);
			if (count($aCurrentPartnerListing) > 0) {
				$wpdb->query("UPDATE " . $sTableName . " SET status = '1' WHERE prefix = '" . $wpdb->prefix . "'");
			} else {

				$sSiteUrl = get_option('siteurl');

				$aInsertData = array(
					'organisation_id' => 0,
					'site_url' => $sSiteUrl,
					'prefix' => $wpdb->prefix,
					'status' => 1,
				);

				$wpdb->insert($sTableName, $aInsertData);
			}

			$this->createProjectUpdateLogTable();
            
		}

		/**
		 * Carries out the operations that need to be done when the Plugin in De-activated
		 *
		 * @global type $wpdb
		 */
		public function uninstall() {

			global $wpdb;

			$this->flushProjectDetails($wpdb->prefix);

			// Make Partner record inactive
			$sTableName = self::TBL_PARTNERDETAILS;
			$wpdb->query("UPDATE $sTableName SET status = '0' WHERE prefix = '" . $wpdb->prefix . "'");
            $wpdb->query('DROP TABLE '.$wpdb->prefix.'projects');
		}

		/**
		 * Adds an entry to the Admin Menu
		 */
		public static function addMenuToAdminMenu() {
			add_plugins_page(
					'Akvo Partner Communication - Settings', 'APC - Settings', 'administrator', 'akvo-partner-communication-settings', 'AkvoPartnerCommunication::renderAdminSettings'
			);
		}

		/**
		 * Renders and Processes the Plugin's Settings in the Admin
		 *
		 * @global type $wpdb
		 * @return string
		 */
		public static function renderAdminSettings() {

			global $wpdb;
			$sPrefix = $wpdb->prefix;
			$sTblName = self::TBL_PARTNERDETAILS;
            
			//$sOptionValue = get_option('akvo_partner_communication');
			$oExOrgRow = $wpdb->get_row("SELECT organisation_id,rsr_keywords FROM  $sTblName WHERE prefix = '$sPrefix' AND status = 1");
			$sExOrgId = $oExOrgRow->organisation_id;
			$sExRSRkeywords = $oExOrgRow->rsr_keywords;
			$sReadUrlButton = "";

			if (!empty($_POST['optionssubmit'])) {
				//after form submit action
				
				$sOrgId = $_POST['org_id'];
				$sRSRkeywords = $_POST['rsr_keywords'];
				
				//get options
				$sSiteUrl = get_option('siteurl'); //$wpdb->get_row("SELECT option_value FROM  $sOptionsTable WHERE `option_name` LIKE 'siteurl'");
				//save new option
//				
				//save partner data (common table data)
				$oExRecords = $wpdb->get_row("SELECT prefix FROM  $sTblName WHERE prefix = '$sPrefix'");
				if (!empty($oExRecords)) {
					//update
					$wpdb->query("UPDATE $sTblName SET organisation_id = '$sOrgId', rsr_keywords='$sRSRkeywords' WHERE prefix = '$sPrefix'");
				} else {
					//insert
					$wpdb->insert($sTblName, array('organisation_id' => $sOrgId,'rsr_keywords'=>$sRSRkeywords, 'site_url' => $sSiteUrl, 'prefix' => $sPrefix, 'status' => '1'));
				}
				$sExOrgId = $sOrgId;
                $sExRSRkeywords = $sRSRkeywords;
				
                $sReadUrlButton = 'Run';
                $sCount = '';
			}

			if (!empty($_POST['runurlsubmit'])) {
				$oAkvo = new AkvoPartnerCommunication();
				$aProjectListing = $oAkvo->readProjectDetails();

                
				$oAkvo->flushProjectDetails($sPrefix);
				$oAkvo->saveProjectDetails($aProjectListing, $sPrefix);
                $oAkvo->saveProjectPartners($aProjectListing, $sPrefix);
				
                $sReadUrlButton = 'Run again';
                $sCount = count($aProjectListing).' Projects found';
                
			}


			//rendering HTML
			if (!current_user_can('manage_options')) {
				wp_die(__('You do not have sufficient permissions to access this page.'));
			}


			echo "<div>";
			echo "<h2>Project Detail Reader Options</h2>";

			echo "<form method='post' action=''>";
			wp_nonce_field('update-options');

			echo "<table width='900'>";
            echo "<tr>";
			echo "<th width='92' scope='row'>Enter Organisation ID</th>";
			echo "<td width='800'>";
			echo "<input name='org_id' type='text' id='org_id'
			value='$sExOrgId'; />
			";
			echo "</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<th width='92' scope='row'>Enter RSR keyword</th>";
			echo "<td width='800'>";
			echo "<input name='rsr_keywords' type='text' id='rsr_keywords'
			value='$sExRSRkeywords'; />
			";
			echo "</td>";
			echo "</tr>";
			echo "</table>";

			echo "<input type='hidden' name='action' value='update' />";
			echo "<input type='hidden' name='page_options' value='akvo_partner_communication' />";
            submit_button('Save Changes','primary','optionssubmit');


			echo "</form>";
			echo "</div>";
if (!empty($_POST['optionssubmit']) || !empty($_POST['runurlsubmit'])) {
			echo '
				<div>
					<h2>Read Data</h2>
					<form action="" method="post">
						';
            submit_button('Run', 'primary', 'runurlsubmit');
			echo '</form>
				</div>';
            echo $sCount;
}
		}

		/**
		 * Creates a table to be used by the Plugin which stores which Partners and their information including database table prefix
		 */
		public function createPartnerDetailsTable() {

			$sTableName = self::TBL_PARTNERDETAILS;

			$sCreateTableStatement = "
				CREATE TABLE IF NOT EXISTS " . $sTableName . " (
					id int(11) NOT NULL AUTO_INCREMENT,
					organisation_id int(11) NOT NULL,
					rsr_keywords varchar(255),
					data_url varchar(255),
					site_url varchar(255) NOT NULL,
					prefix varchar(25) NOT NULL,
					status TINYINT NOT NULL,
                    funds FLOAT( 10, 2 ) NOT NULL DEFAULT  '0.00',
                    partners INT( 11 ) NOT NULL DEFAULT  '0',
					PRIMARY KEY (id)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1"; //create statement

			require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
			dbDelta($sCreateTableStatement);
		}

		/**
		 * Creates table to store Projects belonging to the Partner
		 *
		 * @global type $wpdb
		 */
		public function createProjectsTable() {

			global $wpdb;

			$sTableName = $wpdb->prefix . "projects";

			$sCreateTableStatement = "
				CREATE TABLE IF NOT EXISTS " . $sTableName . " (
					id int(11) NOT NULL AUTO_INCREMENT,
					title varchar(250) NOT NULL,
					project_id int(11) NOT NULL,
					longitude varchar(25) NOT NULL,
					latitude varchar(25) NOT NULL,
					country varchar(255) NOT NULL,
					PRIMARY KEY  (id)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;";

			require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
			dbDelta($sCreateTableStatement);
		}
		/**
		 * Creates table to store Project locations belonging to the Partner
		 *
		 * @global type $wpdb
		 */
		public function createProjectLocationsTable() {

			global $wpdb;

			$sTableName = $wpdb->prefix . "project_locations";

			$sCreateTableStatement = "
				CREATE TABLE IF NOT EXISTS " . $sTableName . " (
					id int(11) NOT NULL AUTO_INCREMENT,
					project_id int(11) NOT NULL,
					longitude varchar(25) NOT NULL,
					latitude varchar(25) NOT NULL,
					country varchar(255) NOT NULL,
					PRIMARY KEY  (id)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;";

			require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
			dbDelta($sCreateTableStatement);
		}
		/**
		 * Creates table to store Project Partners belonging to the Partner
		 *
		 * @global type $wpdb
		 */
		public function createProjectPartnersTable() {

			global $wpdb;

			$sTableName = $wpdb->prefix . "project_partners";

			$sCreateTableStatement = "
				CREATE TABLE IF NOT EXISTS " . $sTableName . " (
					id int(11) NOT NULL AUTO_INCREMENT,
					title varchar(250) NOT NULL,
					logo varchar(250) NOT NULL,
					description TEXT NOT NULL,
					url varchar(250) NOT NULL,
					PRIMARY KEY  (id)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;";

			require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
			dbDelta($sCreateTableStatement);
		}

		/**
		 * Creates table to store records of Project Updates received and updated
		 */
		public function createProjectUpdateLogTable() {

			global $wpdb;

			$sTableName = $wpdb->prefix . self::TBL_PROJUPDATES;
			$sSql = "
				CREATE TABLE IF NOT EXISTS " . $sTableName . " (
					id int(11) NOT NULL AUTO_INCREMENT,
					project_id int(11) NOT NULL,
					update_id int(11) NOT NULL,
					post_id int(11) NOT NULL,
					last_updated datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					PRIMARY KEY (id)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1"; //create statement

			require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
			dbDelta($sSql);
		}

		/**
		 * Truncates the table of any inserted Project Data
		 *
		 * @global type $wpdb
		 */
		public function flushProjectDetails($sPrefix) {

			global $wpdb;
			$sTableName = $sPrefix . "projects";

			//flush the data
			//$sQuery = "DELETE FROM " . $sTable_name;
			$sQuery = "TRUNCATE TABLE " . $sTableName;
			$wpdb->query($sQuery);
			$sTableName = $sPrefix . "project_locations";

			//flush the data
			//$sQuery = "DELETE FROM " . $sTable_name;
			$sQuery = "TRUNCATE TABLE " . $sTableName;
			$wpdb->query($sQuery);
			$sTableName = $sPrefix . "project_partners";

			//flush the data
			//$sQuery = "DELETE FROM " . $sTable_name;
			$sQuery = "TRUNCATE TABLE " . $sTableName;
			$wpdb->query($sQuery);
		}

		/**
		 * Primary API call to fetch the list of Projects for a Partner
		 *
		 * @return array
		 */
		public function readProjectDetails($sUrl = "") {
            $iLimit = 1000 + (int)date('z') + (int)date('G');
			if ($sUrl == "") {
				$sUrl = self::API_URL_FOR_PROJECTS . '&' . $this->getProjectDetailReaderURLOption() . '&limit='.$iLimit;
			}

			$oResponse = file_get_contents($sUrl);
			$aProjectListing = json_decode($oResponse, true);

			return $aProjectListing['objects'];
		}

		/**
		 *
		 * @global type $wpdb
		 * @return string
		 */
//		public function getProjectDetailReaderURLOption() {
//			global $wpdb;
//			$table_name = self::TBL_PARTNERDETAILS;
//
//			$oOprions = $wpdb->get_row("SELECT organisation_id FROM " . $table_name . "
//											WHERE `prefix` = '$wpdb->prefix'");
//			$sProjectDetailReaderUrl = $oOprions->organisation_id;
//			return $sProjectDetailReaderUrl;
//		}
		/**
		 *
		 * @global type $wpdb
		 * @return string
		 */
		public function getProjectDetailReaderURLOption() {
			global $wpdb;
			$table_name = self::TBL_PARTNERDETAILS;

			$oOptions = $wpdb->get_row("SELECT organisation_id,rsr_keywords FROM " . $table_name . "
											WHERE `prefix` = '$wpdb->prefix'");
            if(isset($oOptions->rsr_keywords) && $oOptions->rsr_keywords!==''){
                $urlOption =  'keywords__label='.$oOptions->rsr_keywords;
            }else{
                $urlOption =  'partnerships__organisation='.$oOptions->organisation_id;
            }            
            return $urlOption;
			
		}

		/**
		 * Saves the Projects belonging to a Partner
		 *
		 * @global type $wpdb
		 */
		public function saveProjectDetails($aProjectListing, $sPrefix, $sUrlParam = null) {

			global $wpdb;

			$sTableName = $sPrefix . "projects";
			$sLocationsTableName = $sPrefix . "project_locations";
            $iTotalFunds = 0;
            $iTotalPartners = 0;
            $aPartnerUrls=array();
            //temp country import while in development
            
            $aCoordinates = $this->readLocations($sUrlParam);
			
            $aCountries = array('bangladesh','benin','ethiopia','ghana','kenya','mali','nepal','uganda');
			// Iterate through the list of Projects and Insert them
			foreach ($aProjectListing as $aProjectDetail) {
                $iTotalFunds += (floatval($aProjectDetail['funds']));
//                
                $aInput = array(
                    'title' => $aProjectDetail['title'],
					'project_id' => $aProjectDetail['id']
                );
                
                if($aProjectDetail['locations']!=null){
                    $sLocationUrl = $aProjectDetail['locations'][0];					
                    //example for $sLocationUrl => '/api/v1/project_location/6219/'
                    $aInput['country'] = $aCoordinates[$sLocationUrl]['country'];					
                    $aInput['longitude'] = $aCoordinates[$sLocationUrl]['longitude'];
                    $aInput['latitude'] = $aCoordinates[$sLocationUrl]['latitude'];                  
                }
				
				$wpdb->insert($sTableName, $aInput);
                if($aProjectDetail['locations']!=null){
                    
                    foreach($aProjectDetail['locations'] AS $sLocationUrl){
                        $aInputLocation = array(
                            'project_id' => $aProjectDetail['id'],
                            'longitude' => $aCoordinates[$sLocationUrl]['longitude'],
                            'latitude' => $aCoordinates[$sLocationUrl]['latitude'],
                            'country' => $aCoordinates[$sLocationUrl]['country']
                        );
                        if($aInputLocation['country']!=''){
                           $wpdb->insert($sLocationsTableName,$aInputLocation);
                        }
                    }
                }
			}
			
			$wpdb->update('partner_details',array('funds'=>$iTotalFunds),array('prefix'=>$sPrefix));
		}
        
        public function saveProjectPartners($aProjectListing, $sPrefix){
            global $wpdb;
            $sTableName = $sPrefix . "project_partners";
            $aPartnerIDs = array();
            // Iterate through the list of Projects and Insert them
			foreach ($aProjectListing as $aProjectDetail) {
                $aPartnerIDs[]=$aProjectDetail['id'];
                
            }
			
           
            if(count($aPartnerIDs)>0){
//                echo self::API_URL_FOR_PARTNERS.join(',',$aPartnerIDs).'<br />';
                $sPartners = file_get_contents(self::API_URL_FOR_PARTNERS.join(',',$aPartnerIDs).'&limit=0');
                $aPartners = json_decode($sPartners,true);

                $aUnique = array();
                foreach($aPartners['objects'] AS $aPartner){
                        
                        $aUnique[] = $aPartner['long_name'];
                        $aInput['title'] = $aPartner['long_name'];
                        $aInput['logo'] = ($aPartner['logo']['original']!='') ? 'http://akvo.org'.$aPartner['logo']['original'] : '';
                        $aInput['description'] = $aPartner['description'];
                        $aInput['url'] = $aPartner['url'];
                       $wpdb->insert($sTableName, $aInput);
                }
                $iTotalPartners = count($aUnique);
                $wpdb->update('partner_details',array('partners'=>$iTotalPartners),array('prefix'=>$sPrefix));

            }
        }
        
        public function readCountries(){
            $sCountries = file_get_contents(self::API_URL_FOR_COUNTRIES);
            $aCountries = json_decode($sCountries,true);
			 
            $aObjects = array();
            foreach($aCountries['objects'] AS $aCountry){
                $aObjects[$aCountry['resource_uri']]=strtolower($aCountry['name']);
            }			
            return $aObjects;
        }
        public function readLocations($sUrlParam=null){
            $aCountries = $this->readCountries();
            $iLimit = 2000 + (int)date('z') + (int)date('G');
            $sUrlParam = ($sUrlParam) ? $sUrlParam : $this->getProjectDetailReaderURLOption();

            $sLocations = file_get_contents(self::API_URL_FOR_LOCATIONS.$iLimit.'&project__'.$sUrlParam);			
            $aLocations = json_decode($sLocations,true);			
            $aObjects = array();
            foreach($aLocations['objects'] AS $aLocation){
                $aObjects[$aLocation['resource_uri']]['country']=$aCountries[$aLocation['country']];				
                $aObjects[$aLocation['resource_uri']]['latitude']=$aLocation['latitude'];
                $aObjects[$aLocation['resource_uri']]['longitude']=$aLocation['longitude'];
            }
			
            return $aObjects;
        }
		/**
		 *
		 * @global type $wpdb
		 * @return type object
		 */
		public static function getAllProjectsData() {
			global $wpdb;
			$table_name_projects = $wpdb->prefix . "projects";
			$table_name_project_locations = $wpdb->prefix . "project_locations";
			$oProjects = $wpdb->get_results("SELECT pl.*,p.title FROM " . $table_name_projects . " p JOIN " . $table_name_project_locations . " pl ON pl.project_id = p.project_id WHERE pl.longitude != ''");

			return $oProjects;
		}
		public static function getAllProjectPartnersData() {
			global $wpdb;
			$table_name = $wpdb->prefix . "project_partners";
			$oProjectPartners = $wpdb->get_results("SELECT * FROM " . $table_name . " ORDER BY title ASC");

			return $oProjectPartners;
		}

		
		/**
		 *
		 * @param type $oProjects
		 * @return string
		 */
		public function displayMap($oProjects,$sCountry='',$iZoom=0) {
			$sScript = "";

			$sScript = "<script type='text/javascript' src='http://maps.googleapis.com/maps/api/js?sensor=false'></script>";
			$sScript .= "<script type='text/javascript'>";
			$sScript .= "var bounds = new google.maps.LatLngBounds ();";
            $sScript .= "	var map = new google.maps.Map(document.getElementById('iDivMap'), {
					center: new google.maps.LatLng(0,0),
					zoom: ".$iZoom.",
					mapTypeId: 'roadmap'
					}); ";  
            if($sCountry!=''){
                //set map center to country
                $sScript .= "    var address = '".$sCountry."';";
                $sScript .= "    var geocoder = new google.maps.Geocoder();";
                $sScript .= "geocoder.geocode({";
                $sScript .= "'address': address,";
                $sScript .= "'partialmatch': true}, geocodeResult);";


                $sScript .= "function geocodeResult(results, status) {";
                $sScript .= "if (status == 'OK' && results.length > 0) {";
				
				$sScript .= "map.fitBounds(results[0].geometry.bounds);";
                if($iZoom===0 && $sCountry==='pakistan')$iZoom=2;
                if($iZoom>0)$sScript .= "map.setZoom(Math.round(parseInt(map.getZoom())+".$iZoom."));";
                $sScript .= "} else {";
                $sScript .= "alert(\"Geocode was not successful for the following reason: \" + status);";
                $sScript .= "}";
                $sScript .= "}";
            }
			
			$sProjectURL = get_option('akvo_project_domain',"http://".str_replace(' ','-',wp_get_theme()).".akvoapp.org/en");
            $sReadMoreLink = $sProjectURL. "/project/";

            foreach ($oProjects as $oProject) {
                $sScript .= "  var infoWindow = new google.maps.InfoWindow;";
                $sLink= $sReadMoreLink.$oProject->project_id;
                $sWindowContent = $oProject->title.'<br /><a href="'.$sLink.'" target="_blank" >read more</a>';
                $sScript .= "  infoWindow.setContent('".addslashes($sWindowContent)."');";
                $sScript .= "  var markerPos=new google.maps.LatLng(" . $oProject->latitude . "," . $oProject->longitude . ");";
				$sScript .= "	var marker = new google.maps.Marker({
						flat: true,
						icon: '".plugin_dir_url(__FILE__)."marker-icon.png',
						map: map,
						position:  markerPos
					});";
				$sScript .= "bindInfoWindow(marker,map,infoWindow);";
				$sScript .= "bounds.extend (markerPos);";
			}
			$sScript .= "function bindInfoWindow(marker, map, infoWindow) {";
			$sScript .= "	google.maps.event.addListener(marker, 'click', function() {
					infoWindow.open(map, marker);
				});";
			$sScript .= "}";
			if($sCountry===''){
                $sScript .= "map.fitBounds (bounds);";
            }
			$sScript .= "</script>";

			return $sScript;
		}

		/*
		 *  @global type $wpdb
		 *  @return type object
		 */

		public function readURLsForCronJob($iOrganisationID = null) {
			global $wpdb;
			$sTableName = self::TBL_PARTNERDETAILS;
            $sOrganisationWhere = '';
            if($iOrganisationID){
                $sOrganisationWhere.=' AND organisation_id = '.$iOrganisationID;
            }
			$oProjectURLs = $wpdb->get_results("SELECT organisation_id,rsr_keywords,prefix,data_url FROM $sTableName WHERE STATUS = '1'".$sOrganisationWhere);
			return $oProjectURLs;
		}

		/*
		 *  @global type $wpdb
		 *
		 */

		public function saveProjectUpdates($aPosts, $sPrefix, $iPostToUpdate = null) {
			global $wpdb;
			$sPostTableName = $sPrefix . "posts";

			$iAffectedPostId = $iPostToUpdate;

			if (is_null($iPostToUpdate)) {

				$wpdb->insert($sPostTableName, $aPosts);
                echo 'EEEEEEEEEEEEEEEE';
				//$iAffectedPostId = $wpdb->query("SELECT LAST_INSERT_ID();");
				$iAffectedPostId = $wpdb->insert_id;
                
			} else {
                $wpdb->update($sPostTableName,$aPosts,array('ID'=>$iPostToUpdate));
//				$wpdb->query("UPDATE $sPostTableName SET
//						'post_date' = " . $aPosts['post_date'] . ",
//						'post_date_gmt' = " . $aPosts['post_date_gmt'] . ",
//						'post_title' = " . $aPosts['post_title'] . ",
//						'post_content' = " . $aPosts['post_content'] . ",
//						'post_name' = " . $aPosts['post_name'] . ",
//						'post_type' = " . $aPosts['post_type'] . ",
//						'post_modified' = " . $aPosts['post_modified'] . ",
//						'post_modified_gmt' = " . $aPosts['post_modified_gmt'] . "
//						 WHERE ID = $iPostToUpdate");
			}
            
			return $iAffectedPostId;
		}

		public function readProjectUpdatesFromDb($sPrefix) {
			global $wpdb;
			$sUpdatesTableName = $sPrefix . self::TBL_PROJUPDATES;
			$oProjectUpdates = $wpdb->get_results("SELECT id, project_id, update_id, post_id, last_updated FROM $sUpdatesTableName", ARRAY_A);
			return $oProjectUpdates;
		}
        
        public function readProjectUpdatesFromDbByCountry($sCountry){
            global $wpdb;
            $sQuery = "SELECT wpul.post_id FROM ".$wpdb->prefix.self::TBL_PROJUPDATES." wpul JOIN ".$wpdb->prefix."project_locations wpp ON wpp.project_id = wpul.project_id WHERE wpp.country='".$sCountry."'";

            $oPostIDs = $wpdb->get_results($sQuery,ARRAY_A);
            $aIDs = array();
            foreach($oPostIDs AS $oPost){
                $aIDs[]=$oPost['post_id'];
            }
            return $aIDs;
        }
        public function readProjectUpdatesFromDbForTabs(){
            global $wpdb;
            //$sQuery = "SELECT wpul.post_id FROM ".$wpdb->prefix.self::TBL_PROJUPDATES." wpul ORDER BY wpul.post_id DESC";
			$sQuery = "SELECT wpul.post_id FROM ".$wpdb->prefix.self::TBL_PROJUPDATES." wpul ORDER BY wpul.last_updated DESC";

            $oPostIDs = $wpdb->get_results($sQuery,ARRAY_A);
            $aIDs = array();
            foreach($oPostIDs AS $oPost){
                $aIDs[]=$oPost['post_id'];
            }
            return $aIDs;
        }
        public static function readProjectUpdateCountry($iUpdateID){
            global $wpdb;
            $sQuery = "SELECT wpp.country FROM ".$wpdb->prefix."project_locations wpp JOIN ".$wpdb->prefix.self::TBL_PROJUPDATES." wpul ON wpp.project_id = wpul.project_id WHERE wpul.post_id='".$iUpdateID."'";
            $oCountry = $wpdb->get_results($sQuery);
            if(count($oCountry)>0){
                return $oCountry[0]->country;
            }else{
                return false;
            }
            //return $oCountry;
        }
		public function saveProjectUpdateLogEntry($sPrefix, $aData, $iIdToUpdate = null) {

			global $wpdb;

			$sUpdatesTableName = $sPrefix . self::TBL_PROJUPDATES;

			if (is_null($iIdToUpdate)) {

				$wpdb->insert($sUpdatesTableName, $aData);
			} else {

				$wpdb->query("UPDATE " . $sUpdatesTableName . " SET `last_updated` = '" . $aData['last_updated'] . "' WHERE `update_id` = " . $iIdToUpdate);
			}
		}

        public function getVideoImageUrl($sVideoUrl){
            $return = false;
            if($sVideoUrl!=''){
                //parse url and params
                $aVideoUrl = parse_url($sVideoUrl);
                $aVideoUrlParams = array();
                echo "<pre>";
                parse_str($aVideoUrl['query'],$aVideoUrlParams);

                
                if(strpos($aVideoUrl['host'], 'youtube')!==false){
                    //youtube
                    if(isset($aVideoUrlParams['v'])){
                        $return = 'http://img.youtube.com/vi/'.$aVideoUrlParams['v'].'/0.jpg';
                    }
                }elseif(strpos($aVideoUrl['host'], 'vimeo')!==false){
                    //vimeo
                    $iVideoID = str_replace('/', '', $aVideoUrl['path']);
                    $oVideo = json_decode(file_get_contents('http://vimeo.com/api/v2/video/'.$iVideoID.'.json'));
                    if(is_array($oVideo) && isset($oVideo[0]->thumbnail_large)){
                        $return = $oVideo[0]->thumbnail_large;
                    }
                }
            }

                
            echo "</pre>";
            return $return;
        }
		public function imageResize($sOriginalFile) {

			$iImageWidth = self::PROJECT_UPDATE_IMAGE_WIDTH;
			$iImageHeight = self::PROJECT_UPDATE_IMAGE_HEIGHT;

			$aFilePath = explode('/', $sOriginalFile);
			$iFilePathComponents = count($aFilePath);
			$sFileName = $aFilePath[$iFilePathComponents - 1];
			$sExt = end(explode('.',$sFileName));
			$sNewName = time().uniqid();
			$aImageDestinationPath = wp_upload_dir();
			$sImageDestinationPath = $aImageDestinationPath['path'];
			//$sFileToResize = $sImageDestinationPath . "/" . $sFileName;
			$sFileToResize = $sImageDestinationPath . "/" . $sNewName.'.'.$sExt;

			// Copy over file from Akvo.org
			file_put_contents($sFileToResize, file_get_contents($sOriginalFile));

			// Resize file to fit Partner Site size
			$sImgDestination = image_resize($sFileToResize, $iImageWidth, $iImageHeight, true);
            unlink($sFileToResize);
			echo $sImgDestination.'<br />';
			return $sImgDestination;
		}
        public function saveImageMeta($sPrefix,$sFilename, $iPostId) {
            global $wpdb;
            $aAttachmentData = array(
				'post_id' => $iPostId,
				'meta_key' => 'enclosure',
				'meta_value' => $sFilename
			);
            $sPostMetaTableName = $sPrefix . 'postmeta';
            $wpdb->delete($sPostMetaTableName, array('post_id'=>$iPostId,'meta_key'=>'enclosure'));
            $wpdb->insert($sPostMetaTableName, $aAttachmentData);

        }
		public function saveImageAttachment($sFilename, $sPrefix, $iPostId) {

			global $wpdb;
			$sPostTableName = $sPrefix . 'posts';
			$sPostMetaTableName = $sPrefix . 'postmeta';

			$sFileMimeType = wp_check_filetype(basename($sFilename), null);
			$aUploadDirectory = wp_upload_dir();
            if(!$sFileMimeType['ext'])return;
			$aAttachmentData = array(
				'post_content' => '',
				'post_title' => preg_replace('/\.[^.]+$/', '', basename($sFilename)),
				'post_status' => 'inherit',
				'post_parent' => $iPostId,
				'guid' => $aUploadDirectory['baseurl'] .'/'. _wp_relative_upload_path($sFilename),
				'post_type' => 'attachment',
				'post_mime_type' => $sFileMimeType['type'],
			);

			$wpdb->insert($sPostTableName, $aAttachmentData);
			$iAttachmentPostId = $wpdb->insert_id;
			// $attach_id = wp_insert_attachment($aAttachmentData, $sFilename, $iPostId);

			require_once(ABSPATH . 'wp-admin/includes/image.php');

			// $attach_data = wp_generate_attachment_metadata($attach_id, $sFilename);
			// Refer to: http://codex.wordpress.org/Function_Reference/wp_generate_attachment_metadata
			$aAttachmentMetaData = array(
				'width' => self::PROJECT_UPDATE_IMAGE_WIDTH,
				'height' => self::PROJECT_UPDATE_IMAGE_HEIGHT,
				'file' => _wp_relative_upload_path($sFilename),
				'hwstring_small' => "height='" . self::PROJECT_UPDATE_IMAGE_WIDTH . "' width='" . self::PROJECT_UPDATE_IMAGE_HEIGHT . "'",
				'sizes' => array(
					'thumbnail' => array(
						'file' => '',
						'width' => '',
						'height' => '',
					),
					'medium' => '',
					'large' => '',
					'post-thumbnail' => '',
					'large-feature' => '',
					'small-feature' => '',
				),
				'image_meta' => wp_read_image_metadata($sFilename),
			);

			$aPostMetaData = array(
				'post_id' => $iAttachmentPostId,
				'meta_key' => '_wp_attachment_metadata',
				'meta_value' => serialize($aAttachmentMetaData)
			);

			//wp_update_attachment_metadata($attach_id, $attach_data);
			$wpdb->insert($sPostMetaTableName, $aPostMetaData);
		}
        
      //post type updates
        public static function addPostTypes(){
            register_post_type( 'project_update',
                        array( 
                        'labels' => array(
                            'name'=> __('Project updates'),
                            'singular_name' => __('Project update')
                            ), 
                        'public' => true, 
                        'show_ui' => true,
                        'show_in_nav_menus' => false,
                        'menu_position' => null,
                            'exclude_from_search' => false,
                        'rewrite' => array(
                            'slug' => 'project-update',
                            'with_front' => FALSE,
                        ),
                        'supports' => array(
                                'title',
                                'editor',
                                'custom-fields',
                                'page-attributes',
                                'thumbnail')
                            ) 
                        );

            
            
        }  
        public static function getProjectCountries(){
            global $wpdb;
            $aCountries = $wpdb->get_results('SELECT country FROM '.$wpdb->prefix.'projects GROUP BY country');
            return $aCountries;
        }
        public static function getProjectCountriesForMetabox(){
            global $wpdb;
            $aCountries = $wpdb->get_results('SELECT country FROM '.$wpdb->prefix.'project_locations GROUP BY country');
            return $aCountries;
        }

        //Add project update archive menu to admin
        public static function addMetaboxes(){
            add_meta_box( 
                 'country_select'
                ,__( 'Project updates' )
                ,'AkvoPartnerCommunication::render_country_box_content'
                ,'page' 
                ,'side'
                ,'default'
            );
            
        }
        
        public function render_country_box_content($post) 
        {
           
            // Use nonce for verification
          wp_nonce_field( plugin_basename( __FILE__ ), 'apc_countryselect' );
          $sVal = get_post_meta($post->ID, 'country',true);
          // The actual fields for data entry
          echo '<label for="apc_country_select">';
               _e("Select the qountry which project updates should be displayed");
          echo '</label><br />';
          $aProjectData = self::getProjectCountriesForMetabox();
          echo '<select id="apc_country_select" name="apc_country_select" >';
          echo '<option value="none">none</option>';
          foreach($aProjectData AS $aProject){
              $sSelected = ($aProject->country==$sVal) ? 'selected' : '' ;
              echo '<option value="'.$aProject->country.'" '.$sSelected.'>'.$aProject->country.'</option>';
          }
          echo '</select>';

          
        }
        
        public static function saveCountryforPost($post_id){
            
            // verify if this is an auto save routine. 
              // If it is our form has not been submitted, so we dont want to do anything
              if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
                  return;
              }
              // verify this came from the our screen and with proper authorization,
              // because save_post can be triggered at other times

              if ( !wp_verify_nonce( $_POST['apc_countryselect'], plugin_basename( __FILE__ ) ) ){
                  return;
              }

              // Check permissions
              if ( 'page' == $_POST['post_type'] ) 
              {
                if ( !current_user_can( 'edit_page', $post_id ) ){
                    return;
                }
              }
              else
              {
                if ( !current_user_can( 'edit_post', $post_id ) ){
                    return;
                }
              }

              // OK, we're authenticated: we need to find and save the data

              $mydata = $_POST['apc_country_select'];
              if($mydata!=''){
                  delete_post_meta($post_id, 'country');
                  add_post_meta($post_id, 'country', $mydata,true);
              }
              
              // Do something with $mydata 
              // probably using add_post_meta(), update_post_meta(), or 
              // a custom table (see Further Reading section below)
        }
        
        public static function getUpdateImages($postID){
            global $wpdb;
            $aAttachments = $wpdb->get_results("SELECT ID FROM ".$wpdb->prefix."posts WHERE post_parent=".$postID." AND post_type='attachment'");
			return $aAttachments;
        }
	}
    
    

}