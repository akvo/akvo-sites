<?php

error_reporting(0);

// include WP functions
require_once '../../../wp-config.php';

require_once 'AkvoTempDisplayMap.php';

		
$oATDM = new AkvoTempDisplayMap();
$sMessage = '';

$iOrganisationID = (isset($_GET['id_organisation'])) ? $_GET['id_organisation'] : null;

$sMessage .= 'Organization ID: ' . $iOrganisationID . '<br/><br/>';

if(is_null($iOrganisationID)) {
	
	return;
}

// get all org data
$aPartnerData = $oATDM->getOrganizationsDetails($iOrganisationID);

//$aPartnerData = array (
//  "0" => 
//    array (
//      'organisation_id' =>  '275',
//      'rsr_keywords' =>  '',
//      'prefix' =>  'wi1_12_',
//      'data_url' => null
//	)
//);

//iterate through org data
foreach($aPartnerData as $aPartner) {
	
	$sPrefix = $aPartner['prefix'];
	
	//Build URL to fetch projects from AKVO RSR
	$sProjectsURL = $oATDM->buildURL('projects', $aPartner['organisation_id']);		
	$sMessage .= 'Project API: ' . $sProjectsURL . '<br/>';
	
	//fetch project data
	$aProjects = $oATDM->fetchData($sProjectsURL);
	
	//insert data into projects tbl
	$oATDM->truncateTbl('projects');	
	$oATDM->saveProjects($aProjects['response']['results']);
	$sMessage .= sizeof($aProjects['response']['results']). ' projects are inserted.<br/><br/>';
	
	//Build URL to fetch projects locations from AKVO RSR
	$sProjectsLocationsURL = $oATDM->buildURL('project_map_locations', $aPartner['organisation_id']);
	$sMessage .= 'Project Map location API: ' . $sProjectsLocationsURL . '<br/>';
	
	//fetch project location data
	$aProjectsLocations = $oATDM->fetchData($sProjectsLocationsURL);

	//insert data into project locations tbl
	$oATDM->truncateTbl('project_locations');
	$oATDM->saveProjectsLocations($aProjectsLocations['response']['results']);
	$sMessage .= sizeof($aProjectsLocations['response']['results']). ' project Locations are inserted.<br/>';

	$aProjectUpdateLog = $oATDM->readProjectUpdatesFromDb();
    
	// Iterate through results and rebuild new array with 'update_id' as the array key
	$aProjectUpdatesLocal = array();
	if (sizeof($aProjectUpdateLog) > 0) {
		
		foreach ($aProjectUpdateLog as $aUpdate) {
			$aProjectUpdatesLocal[$aUpdate['update_id']] = array('last_updated' => $aUpdate['last_updated'], 'post_id' => $aUpdate['post_id'], 'id' => $aUpdate['id']);
		}
	}
	$aProjectUpdateKeys = array_keys($aProjectUpdatesLocal);
	
	$iTotalNoProjectUpdates = 0;
		
	foreach($aProjects['response']['results'] as $aProject) {

		$sProjectsUpdatesURL = $oATDM->buildProjectUpdatesURL($aProject['id']);						
		$sMessage .= '<br/>Project update API:' . $sProjectsUpdatesURL . '<br/>';
		do {		
	
			//fetch all the project updates
			$aProjectsUpdates = array();	
			$aProjectUpdatesIDs = array();
			
			$aProjectsUpdates = $oATDM->fetchData($sProjectsUpdatesURL);			
			$sMessage .= 'No of project updates in ' . $aProject['id'] . ' are ' . sizeof($aProjectsUpdates['response']['results']) . '<br/>';
			$iTotalNoProjectUpdates += sizeof($aProjectsUpdates['response']['results']);
					
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
				}

				$sProjectsUpdatesURL = $aProjectsUpdates['response']['next'];
				$sMessage .= '<br/>Project update "next" URL is ' . $sProjectsUpdatesURL . '<br/>';
				
			} else {

				$sProjectsUpdatesURL = null;
			}			
			
		} while(!is_null($sProjectsUpdatesURL));
				
		//sleep(2);
	}
	
	$sMessage .= 'Total number of project updates: ' . $iTotalNoProjectUpdates . '<br/><br/>';
}

$sMessage .= 'Completed';

//echo $sMessage;

add_filter( 'wp_mail_content_type', 'set_html_content_type' );

wp_mail( 'rumesh@webgurus.lk', '[Akvo Cron Log] - Akvo sites', $sMessage );

// Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
remove_filter( 'wp_mail_content_type', 'set_html_content_type' );


function set_html_content_type() {

	return 'text/html';
}

