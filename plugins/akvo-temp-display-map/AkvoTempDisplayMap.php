<?php


class AkvoTempDisplayMap {
	
	CONST API_PROJECT_MAP_LOCATION = 'http://rsr.akvo.org/rest/v1/project_map_location/?';
	CONST API_PROJECT_UPDATES = 'http://rsr.akvo.org/rest/v1/project_update/?';
	CONST API_PROJECTS = 'http://rsr.akvo.org/rest/v1/project/?';
	
	const TBL_PARTNERDETAILS = "partner_details";
	const TBL_PROJECTUPDATELOG = "project_update_log";
	
	public function __construct() {

	}
	
//	public function readURLsForCronJob($iOrganisationID = null) {
//		
//		global $wpdb;
//		
//		$sTableName = self::TBL_PARTNERDETAILS;
//		
//		$sOrganisationWhere = '';
//		if(!is_null($iOrganisationID)) {
//			$sOrganisationWhere.=' AND organisation_id = '.$iOrganisationID;
//		}
//		
//		$aProjectURLs = $wpdb->get_results("SELECT organisation_id,rsr_keywords,prefix,data_url FROM $sTableName WHERE STATUS = '1'".$sOrganisationWhere, ARRAY_A);
//		
//		return $aProjectURLs;
//	}
	
	public function getOrganizationsDetails($iOrganisationID = null) {
		
		global $wpdb;
		
		$sTableName = self::TBL_PARTNERDETAILS;
		
		$sOrganisationWhere = '';
		if(!is_null($iOrganisationID)){
			$sOrganisationWhere.=' AND organisation_id = '.$iOrganisationID;
		}
		
		$aOrganizationsDetails = $wpdb->get_results("SELECT organisation_id,rsr_keywords,prefix,data_url FROM $sTableName WHERE STATUS = '1'".$sOrganisationWhere, ARRAY_A);
		
		return $aOrganizationsDetails;
	}
	
	public function buildURL($sURLFor, $sParamValue = '') {

		global $wpdb;
		//get Org ID		
		//$oOptions = $wpdb->get_row("SELECT organisation_id FROM partner_details WHERE `prefix` = '$wpdb->prefix'");
		//Build URL params
		$sAPIURL = '';
		$sURL = '';
		$sParams = 'limit=500&format=json';
		
		switch($sURLFor) {
			case 'projects_by_org':
				$sAPIURL = self::API_PROJECTS;
				$sParams .= '&partners='. $sParamValue;
				break;
			case 'projects_by_keywords':
				$sAPIURL = self::API_PROJECTS;
				$sParams .= '&keywords='. $sParamValue;
				break;
			case 'project_map_locations':
				$sAPIURL = self::API_PROJECT_MAP_LOCATION;
				$sParams .= '&location_target__partners='. $sParamValue;
				break;
			case 'project_updates':
				$sAPIURL = self::API_PROJECT_UPDATES;				
				break;
		}				
		
		$sURL = $sAPIURL . $sParams;
		
		return $sURL;
	}
	
	public function buildURLForKeyword($sURLFor, $sOrganisationkeyword = '', $sAdditionalParams = '') {		

		global $wpdb;
		//get Org ID		
		//$oOptions = $wpdb->get_row("SELECT organisation_id FROM partner_details WHERE `prefix` = '$wpdb->prefix'");
		//Build URL params
		$sAPIURL = '';
		$sURL = '';
		$sOrgKeywordParams = '';
		$sParams = 'limit=500&format=json';
		
		switch($sOrganisationkeyword) {
			case 'rain4food':
				$sOrgKeywordParams .= '&keywords=2';
				break;
			default:
				$sOrgKeywordParams .= '';
				break;
		}
		
		switch($sURLFor) {
			case 'projects':
				$sAPIURL = self::API_PROJECTS;
				$sParams .= $sOrgKeywordParams;
				break;
			case 'project_map_locations':
				$sAPIURL = self::API_PROJECT_MAP_LOCATION;
				$sParams .= $sAdditionalParams;
				break;
			case 'project_updates':
				$sAPIURL = self::API_PROJECT_UPDATES;
				$sParams .= $sAdditionalParams;
				break;
		}				
		
		$sURL = $sAPIURL . $sParams;
		
		return $sURL;
	}
	
	public function buildProjectLocationsURL($iOrgID) {		

		global $wpdb;
		//get Org ID		
		//$oOptions = $wpdb->get_row("SELECT organisation_id FROM partner_details WHERE `prefix` = '$wpdb->prefix'");
		//Build URL params
		$sParams = 'limit=500&format=json&location_target__partners='. $iOrgID;
		
		$sURL = self::API_PROJECT_MAP_LOCATION . $sParams;
		
		return $sURL;
	}
	
	public function buildProjectUpdatesURL($sProjectId) {
		
		global $wpdb;
		
		//Build URL params
		$sParams = 'limit=500&format=json&project='. $sProjectId;
		
		$sURL = self::API_PROJECT_UPDATES . $sParams;
		
		return $sURL;
	}

	public function displayMap($aProjectsLocations,$sCountry='',$iZoom=0) {
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
		
		foreach ($aProjectsLocations as $aProject) {
			
			$sScript .= "  var infoWindow = new google.maps.InfoWindow;";
			$sLink= $sReadMoreLink.$aProject['project_id'];
			$sWindowContent = $aProject['title'].'<br /><a href="'.$sLink.'" target="_blank" >read more</a>';			
			$sScript .= "  infoWindow.setContent('".addslashes($sWindowContent)."');";
			$sScript .= "  var markerPos=new google.maps.LatLng(" . $aProject['latitude'] . "," . $aProject['longitude'] . ");";
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
	
//	public function fetchData($sUrl) {
//			
//		$curl = curl_init($sUrl);
//
//		curl_setopt_array($curl, array(
//			CURLOPT_RETURNTRANSFER => true,
//			CURLOPT_HTTPHEADER => array('Authorization: Token c0bfa661f9323f56fee5c6453896345ed5d664fd')
//		));
//
//		$sResult = curl_exec($curl);	
//
//		curl_close($curl);
//
//		$aResult = json_decode($sResult, true);
//
//		return $aResult['results'];
//	}
	
	public function fetchData($sUrl) {
			
		$curl = curl_init($sUrl);

		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => array('Authorization: Token c0bfa661f9323f56fee5c6453896345ed5d664fd')
		));

		$sResult = curl_exec($curl);
		
		$aInfo = curl_getinfo($curl);

		curl_close($curl);
		
		$aResponse = array();
		
		if ($sResult === FALSE) {
			
			//return "cURL Error: " . curl_error($curl);
			$aResponse['message'] = 'error';
						
		} else {
			
			$aResult = json_decode($sResult, true);
			
			$aResponse['message'] = 'success';
			$aResponse['response'] = $aResult;
			$aResponse['info'] = $aInfo;
		}
		 
		return $aResponse;
	}
	
	public function fetchProjectUpdateData($sUrl) {
			
		$curl = curl_init($sUrl);

		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => array('Authorization: Token c0bfa661f9323f56fee5c6453896345ed5d664fd')
		));

		$sResult = curl_exec($curl);	

		curl_close($curl);

		$aResult = json_decode($sResult, true);

		//return $aResult['results'];
		
		return $aResult;
	}
	
	public function readProjectUpdatesFromDb() {
		
		global $wpdb;
		$sUpdatesTableName = $wpdb->prefix . self::TBL_PROJECTUPDATELOG;
		$aProjectUpdates = $wpdb->get_results("SELECT id, project_id, update_id, post_id, last_updated FROM $sUpdatesTableName", ARRAY_A);
		return $aProjectUpdates;
	}
	
	public function truncateTbl($sTableName) {
		
		global $wpdb;				
		
		$sQuery = "TRUNCATE TABLE " . $wpdb->prefix . $sTableName;
		
		$wpdb->query($sQuery);
		
		//$wpdb->flush();
	}
	
//	public function truncateProjectLocationTbl($sPrefix) {
//		
//		global $wpdb;
//		
//		$sTableName = $sPrefix . "project_locations";
//		$sQuery = "TRUNCATE TABLE " . $sTableName;
//		$wpdb->query($sQuery);
//	}
	
//	public function truncateProjectsTbl($sPrefix) {
//		
//		global $wpdb;
//		
//		$sTableName = $sPrefix . "projects";
//		$sQuery = "TRUNCATE TABLE " . $sTableName;
//		$wpdb->query($sQuery);
//	}
	
//	public function truncateProjectUpdateLogTbl($sPrefix) {
//		
//		global $wpdb;
//		
//		$sTableName = $sPrefix . "project_update_log";
//		$sQuery = "TRUNCATE TABLE " . $sTableName;
//		$wpdb->query($sQuery);
//	}
	
	public function saveBudget($iTotalBudget) {
		
		global $wpdb;
		
		$wpdb->update('partner_details',array('funds'=>$iTotalBudget),array('prefix'=>$wpdb->prefix));
	}
	
	public function saveProjectsLocations($aProjectsLocations) {
	
		global $wpdb;
		
		$sTableName = $wpdb->prefix . "project_locations";
		
		foreach($aProjectsLocations as $aProjects){
			$aInputLocation = array(
				'project_id' => $aProjects['project']['id'],
				'longitude' => $aProjects['longitude'],
				'latitude' => $aProjects['latitude'],
				'country' => $aProjects['country']['name']
			);
			
			$wpdb->insert($sTableName,$aInputLocation);
			
		}
	}
	
	public function saveProjectLocation($aProjectLocation) {
	
		global $wpdb;
		
		$sTableName = $wpdb->prefix . "project_locations";				
			
		$wpdb->insert($sTableName,$aProjectLocation);
		
	}
	
//	public function saveProjects($aProjectsLocations, $sPrefix) {
//	
//		global $wpdb;
//		$aProjectID = array();
//		
//		$sTableName = $sPrefix . "projects";
//		
//		foreach($aProjectsLocations as $aProjects){						
//			
//			if(in_array($aProjects['project']['id'], $aProjectID)) {
//				continue;
//			}
//			
//			$aInput = array(
//				'project_id' => $aProjects['project']['id'],
//				'title' => $aProjects['project']['title'],														
//				'longitude' => $aProjects['longitude'],
//				'latitude' => $aProjects['latitude'],   
//				'country' => $aProjects['country']['name']
//			);                		
//			
//			$wpdb->insert($sTableName, $aInput);
//			
//			$aProjectID[] = $aProjects['project']['id'];
//		}				
//	}
	
	public function saveProjects($aProjects) {
	
		global $wpdb;
		$sPrefix = $wpdb->prefix;
		//$aProjectID = array();
		
		$sTableName = $sPrefix . "projects";
		
		foreach($aProjects as $aProject){						
			
			$aInput = array(
				'project_id' => $aProject['id'],
				'title' => $aProject['title']				
			);                		
			
			$wpdb->insert($sTableName, $aInput);						
		}
				
	}
	
	public function saveProject($aProject) {
	
		global $wpdb;
		$sPrefix = $wpdb->prefix;		
		
		$sTableName = $sPrefix . "projects";
		
		$wpdb->insert($sTableName, $aProject);								
				
	}
	
	public function saveProjectUpdates($aPosts, $iPostToUpdate = null) {
		
		global $wpdb;
		$sPrefix = $wpdb->prefix;
		$sPostTableName = $sPrefix . "posts";
		$iAffectedPostId = $iPostToUpdate;

		if (is_null($iPostToUpdate)) {			
			
			$wpdb->insert($sPostTableName, $aPosts);			
			$iAffectedPostId = $wpdb->insert_id;
		} else {
			
			$wpdb->update($sPostTableName,$aPosts,array('ID'=>$iPostToUpdate));
		}

		return $iAffectedPostId;
	}
	
	public function saveProjectUpdateLogEntry($aProjectUpdateLogEntryData, $iIdToUpdate = null) {
		
		global $wpdb;
		$sPrefix = $wpdb->prefix;
		$sUpdatesTableName = $sPrefix . 'project_update_log';

		if (is_null($iIdToUpdate)) {

			$wpdb->insert($sUpdatesTableName, $aProjectUpdateLogEntryData);
		} else {

			$wpdb->query("UPDATE " . $sUpdatesTableName . " SET `last_updated` = '" . $aProjectUpdateLogEntryData['last_updated'] . "' WHERE `id` = " . $iIdToUpdate);
		}
	}
	
	public function saveImageMeta($sFilename, $iPostId) {
		
		global $wpdb;
		$sPrefix = $wpdb->prefix;
		$aAttachmentData = array(
			'post_id' => $iPostId,
			'meta_key' => 'enclosure',
			'meta_value' => $sFilename
		);
		
		$sPostMetaTableName = $sPrefix . 'postmeta';
		
		$wpdb->delete($sPostMetaTableName, array('post_id'=>$iPostId,'meta_key'=>'enclosure'));
		
		$wpdb->insert($sPostMetaTableName, $aAttachmentData);
		
	}
	
	public function getProjectsAndLocations() {
		
		global $wpdb;				
				
		$sQuery = "SELECT pl.*,p.title FROM " . $wpdb->prefix . "projects" . " p JOIN " . $wpdb->prefix . "project_locations" . " pl ON pl.project_id = p.project_id WHERE pl.longitude != ''";
		
		$aResults = $wpdb->get_results($sQuery,ARRAY_A);
		
		return $aResults;
	}

	public function getProjects($sFields) {
		
		global $wpdb;				
		
		$sQuery = 'SELECT ' . $sFields . ' FROM ' . $wpdb->prefix . 'projects';
		
		$aResults = $wpdb->get_results($sQuery,ARRAY_A);
		
		return $aResults;
	}
	
	public function getProjectLocations($sFields) {
		
		global $wpdb;				
		
		$sQuery = 'SELECT ' . $sFields . ' FROM ' . $wpdb->prefix . 'project_locations';
		
		$aResults = $wpdb->get_results($sQuery,ARRAY_A);
		
		return $aResults;
	}
	
	public function getProjectsUpdateLog() {
		
		global $wpdb;
				
		$sQuery = 'SELECT id, project_id, update_id, post_id, last_updated FROM '. $wpdb->prefix . 'project_update_log';
		$aProjectsUpdateLog = $wpdb->get_results($sQuery, ARRAY_A);
		
		return $aProjectsUpdateLog;
	}
	
	public function deleteExistingProjectUpdates($sPrefix) {
		
		global $wpdb;
		
		$sQuery = 'DELETE FROM '. $sPrefix . 'posts WHERE '. $sPrefix . 'posts.ID IN (SELECT post_id FROM '. $sPrefix . 'project_update_log)';
		
		$iRowsAffected = $wpdb->query($sQuery);
		
		return $iRowsAffected;
	}
	
//	public function getPost($iID) {
//		
//		global $wpdb;
//		
//		$sQuery = 'SELECT * FROM '. $wpdb->prefix . 'posts WHERE ID = ' . $iID;
//		
//		$aProjectsUpdateLog = $wpdb->get_results($sQuery, ARRAY_A);
//		
//		return $aProjectsUpdateLog;
//	}
}
?>
