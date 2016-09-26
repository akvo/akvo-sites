<?php

error_reporting(0);

// include WP functions
require_once '../../../wp-config.php';

require_once 'AkvoTempDisplayMap.php';

		
$oATDM = new AkvoTempDisplayMap();
$sMessage = '';

$iOrganisationID = (isset($_GET['id_organisation'])) ? $_GET['id_organisation'] : null;
$sKeywords = (isset($_GET['keywords'])) ? $_GET['keywords'] : null;

//==== Projects section starts ====
//Build URL to fetch projects
$sProjectsURL = '';
if(!is_null($iOrganisationID)) {
	$sMessage .= 'Organization ID: ' . $iOrganisationID . '<br/><br/>';
	$sProjectsURL = $oATDM->buildURL('projects_by_org', $iOrganisationID);
} else if (!is_null($sKeywords)) {
	$sMessage .= 'Keywords: ' . $sKeywords . '<br/><br/>';
	$sProjectsURL = $oATDM->buildURL('projects_by_keywords', $sKeywords);
} else {
	return;
}


//$sProjectsURL = $oATDM->buildURL('projects', $iOrganisationID);
$sMessage .= 'Project API: ' . $sProjectsURL . '<br/>';

//Get data from 'projects' table
$aMLocalProjects = $oATDM->getProjects('project_id');
$aNLocalProjects = array();
foreach($aMLocalProjects as $aLocalProjects) {
	
	$aNLocalProjects[] = $aLocalProjects['project_id'];
}

$iProjectsTotal = 0;
$iTotalBudget = 0;
$aProjectPartners = array();

do {

	//fetch project data
	$aProjects = $oATDM->fetchData($sProjectsURL);
	if($aProjects['message'] != 'error' && sizeof($aProjects['response']['results'] > 0)) {

		foreach($aProjects['response']['results'] as $aProject){						
			
			//if the project already exist, do not insert.
			if(!in_array($aProject['id'], $aNLocalProjects) && $aProject['publishing_status'] == 'published') {
			
				//insert data into projects tbl
				$aProjectData = array(
					'project_id' => $aProject['id'],
					'title' => $aProject['title']				
				);				
							
				$oATDM->saveProject($aProjectData);
			}
			
			$iProjectsTotal++; 
			$iTotalBudget += $aProject['budget'];
			$aProjectPartners = array_merge($aProjectPartners, $aProject['partners']);			
		}				

		$sProjectsURL = $aProjects['response']['next'];
	} else {

		$sProjectsURL = null;
	}

} while(!is_null($sProjectsURL));	

$sMessage .= 'Total number of Projects: ' . $iProjectsTotal. ' <br/><br/>';

//Update funds column in partner_details tbl
$oATDM->saveBudget($iTotalBudget);

//==== Projects section ends ====

//==== Project partners section starts ====

//var_dump(array_unique($aProjectPartners));
//die();

//==== Project partners section ends ====

//==== Project locations section starts ====
//Build URL to fetch projects locations
$sProjectsLocationsURL = $oATDM->buildURL('project_map_locations', $iOrganisationID);
$sMessage .= 'Project Map location API: ' . $sProjectsLocationsURL . '<br/>';


//Get data from project_locations tbl
$aMLocalProjectLocations = $oATDM->getProjectLocations('*');
$aNLocalProjectLocations = array();

foreach($aMLocalProjectLocations as $aLocalProjectLocations) {
	
	$aNLocalProjectLocations[] =  array($aLocalProjectLocations['project_id'],
										$aLocalProjectLocations['longitude'],
										$aLocalProjectLocations['latitude'],
										$aLocalProjectLocations['country']
										);
}

$iProjectLocationsTotal = 0;

do {

	//fetch project location data
	$aProjectsLocations = $oATDM->fetchData($sProjectsLocationsURL);

	if($aProjectsLocations['message'] != 'error' && sizeof($aProjectsLocations['response']['results'] > 0)) {
		
		foreach($aProjectsLocations['response']['results'] as $aProjectLocation) {
			
			$bInsertProjectLocation = true;
			
			foreach($aNLocalProjectLocations as $aNLocalProjectLocation) {
				
				if($aNLocalProjectLocation[0] == $aProjectLocation['project']['id'] &&
					$aNLocalProjectLocation[1] == $aProjectLocation['longitude'] &&
					$aNLocalProjectLocation[2] == $aProjectLocation['latitude'] &&
					$aNLocalProjectLocation[3] == $aProjectLocation['country']['name']) {
			
					//Record already exist
					$bInsertProjectLocation = false;
					break;
				}								
			}
			
			if($bInsertProjectLocation) {
				
				//insert data into project locations tbl
				$aProjectLocationData = array(
					'project_id' => $aProjectLocation['project']['id'],
					'longitude' => $aProjectLocation['longitude'],
					'latitude' => $aProjectLocation['latitude'],
					'country' => $aProjectLocation['country']['name']
				);

				$oATDM->saveProjectLocation($aProjectLocationData);
			}									
			
			$iProjectLocationsTotal++;
		}				

		$sProjectsLocationsURL = $aProjectsLocations['response']['next'];
	} else {

		$sProjectsLocationsURL = null;
	}

} while(!is_null($sProjectsLocationsURL));


$sMessage .= 'Total number of Project Locations: ' . $iProjectLocationsTotal .'<br/><br/>';

//==== Project locations section ends ====

//==== Project updates section starts ====

$aProjectUpdateLog = $oATDM->readProjectUpdatesFromDb();

// Iterate through results and rebuild new array with 'update_id' as the array key
$aProjectUpdatesLocal = array();
if (sizeof($aProjectUpdateLog) > 0) {

	foreach ($aProjectUpdateLog as $aUpdate) {
		$aProjectUpdatesLocal[$aUpdate['update_id']] = array('last_updated' => $aUpdate['last_updated'], 'post_id' => $aUpdate['post_id'], 'id' => $aUpdate['id']);
	}
}
$aProjectUpdateKeys = array_keys($aProjectUpdatesLocal);

//Get data from 'projects' table
$aMLocalProjectsForUpdates = $oATDM->getProjects('project_id');

$iTotalNoProjectUpdates = 0;
$aDuplicatedProjectUpdates = array();

foreach($aMLocalProjectsForUpdates as $aProject) {

	$sProjectsUpdatesURL = $oATDM->buildProjectUpdatesURL($aProject['project_id']);						
	$sMessage .= '<br/>Project update API: ' . $sProjectsUpdatesURL . '<br/>';

	do {		

		$aProjectsUpdates = array();	
		$aProjectUpdatesIDs = array();

		//fetch all the project updates
		$aProjectsUpdates = $oATDM->fetchData($sProjectsUpdatesURL);	

		if($aProjectsUpdates['message'] != 'error' && sizeof($aProjectsUpdates['response']['results'] > 0)) {

			foreach($aProjectsUpdates['response']['results'] as $aProjectUpdate) {

				if(in_array($aProjectUpdate['id'], $aDuplicatedProjectUpdates)) {
					//There are duplicated project update IDs in the API. So avoid them inserting again.					
					continue;
				}
				
				$aPosts = array(
					'post_date' => date("Y-m-d H:i:s",strtotime($aProjectUpdate['created_at'])), //The time post was made.
					'post_date_gmt' => date("Y-m-d H:i:s",strtotime($aProjectUpdate['created_at'])), //The time post was made, in GMT.
					'post_title' => $aProjectUpdate['title'], //The title of your post.
					'post_content' => $aProjectUpdate['text'], //The full text of the post.
					'post_name' => urlencode($aProjectUpdate['title']),
					'post_type' => 'project_update',
					'post_modified' => date("Y-m-d H:i:s",strtotime($aProjectUpdate['last_modified_at'])),
					'post_modified_gmt' => date("Y-m-d H:i:s",strtotime($aProjectUpdate['last_modified_at']))
				);										

				if(in_array($aProjectUpdate['id'], $aProjectUpdateKeys)) {				
					//Project update already exist.
					//If update time is different, do an update

					$oLastUpdatedInDb = new DateTime($aProjectUpdatesLocal[$aProjectUpdate['id']]['last_updated']);
					$oLastUpdatedInAPI = new DateTime($aProjectUpdate['last_modified_at']);

					if ($oLastUpdatedInDb < $oLastUpdatedInAPI) {
						// Update time is different. Do an update.
						$iInsertedPostId = $oATDM->saveProjectUpdates($aPosts, $aProjectUpdatesLocal[$aProjectUpdate['id']]['post_id']);

						$aProjectUpdateLogEntryData = array(
							'update_id' => $aProjectUpdate['id'],
							'project_id' => $aProjectUpdate['project'],
							'post_id' => $iInsertedPostId,
							'last_updated' => date('Y-m-d H:i:s', strtotime($aProjectUpdate['last_modified_at'])),
						);

						$oATDM->saveProjectUpdateLogEntry($aProjectUpdateLogEntryData, $aProjectUpdatesLocal[$aProjectUpdate['id']]['id']);

						//Insert image of the project update
						$sImageFile = 'http://rsr.akvo.org' . $aProjectUpdate['photo'];
						$oATDM->saveImageMeta($sImageFile, $iInsertedPostId);

						$sMessage .= '(U) ' . $aProjectUpdate['id'] . ', ';
					} else {
						//Duplicate
					}

				} else {

					// Do an insert
					$iInsertedPostId = $oATDM->saveProjectUpdates($aPosts);

					$aProjectUpdateLogEntryData = array(
						'update_id' => $aProjectUpdate['id'],
						'project_id' => $aProjectUpdate['project'],
						'post_id' => $iInsertedPostId,
						'last_updated' => date('Y-m-d H:i:s', strtotime($aProjectUpdate['last_modified_at'])),
					);

					$oATDM->saveProjectUpdateLogEntry($aProjectUpdateLogEntryData);

					//Insert image of the project update
					$sImageFile = 'http://rsr.akvo.org' . $aProjectUpdate['photo'];
					$oATDM->saveImageMeta($sImageFile, $iInsertedPostId);

					$sMessage .= '(I) ' . $aProjectUpdate['id'] . ', ';
				}										

				$aDuplicatedProjectUpdates[] = $aProjectUpdate['id'];
				$iTotalNoProjectUpdates++;
			}

			$sProjectsUpdatesURL = $aProjectsUpdates['response']['next'];
			$sMessage .= 'Project update "next" URL is ' . $sProjectsUpdatesURL . '<br/>';

			$sMessage .= 'No of project updates in ' . $aProject['project_id'] . ' are ' . sizeof($aProjectsUpdates['response']['results']) . '<br/>';

		} else {

			$sProjectsUpdatesURL = null;
		}			

	} while(!is_null($sProjectsUpdatesURL));

	//sleep(2);
}

$sMessage .= 'Total number of project updates: ' . $iTotalNoProjectUpdates . '<br/><br/>';

//==== Project updates section ends ====

$sMessage .= 'Completed';

//echo $sMessage;

add_filter( 'wp_mail_content_type', 'set_html_content_type' );

wp_mail( 'rumesh@webgurus.lk', '[Akvo Cron Log] - Akvo sites', $sMessage );

// Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
remove_filter( 'wp_mail_content_type', 'set_html_content_type' );


function set_html_content_type() {

	return 'text/html';
}

