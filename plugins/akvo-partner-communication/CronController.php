<?php

/**
 * CronController
 *
 *
 * @author Uthpala Sandirigama <damsirini.uthpala@gmail.com>
 */
//echo "yo";
require_once '../../../wp-config.php';
//require_once '/wp-content/plugins/dwa-project-detail-reader/ProjectUpdateLogic.php';


require_once 'AkvoPartnerCommunication.php';
$iOrganisationID = (isset($_GET['id_organisation'])) ? $_GET['id_organisation'] : null;
$oAPC = new AkvoPartnerCommunication();
$iLimit = 100 + (int)date('z') + (int)date('G');
var_dump($iLimit);
$sUpdateUrl = "http://www.akvo.org/api/v1/project_update/?format=json&limit=".$iLimit."&project__partnerships__organisation=";
//$sUpdateUrl = "http://www.akvo.org/api/v1/project_update/?format=json&distinct=true&project__partnerships__organisation=";
echo $sUpdateUrl.'<br />';
$aPartnerData = $oAPC->readURLsForCronJob($iOrganisationID);

//$aSimplifiedArray = $aPartnerData[];
shuffle($aPartnerData);
//var_dump($aPartnerData); 
//die();
foreach ($aPartnerData as $oOrgId) {
    echo '<h1>'.$oOrgId->organisation_id.'</h1>';
    $iLimit = 1000 + (int)date('z') + (int)date('G');
	$sUrl = AkvoPartnerCommunication::API_URL_FOR_PROJECTS . $oOrgId->organisation_id. '&limit='.$iLimit;
	$sPrefix = $oOrgId->prefix;
	$aProjectData = $oAPC->readProjectDetails($sUrl);
    
    $oAPC->flushProjectDetails($sPrefix);
    $oAPC->saveProjectDetails($aProjectData, $sPrefix);
    $oAPC->saveProjectPartners($aProjectData, $sPrefix);
    var_dump(count($aProjectData)); 
}
foreach ($aPartnerData as $oOrgId) {
    echo '<h1>'.$oOrgId->organisation_id.'</h1>';
	$sPrefix = $oOrgId->prefix;
	
    $sUrl = $sUpdateUrl . $oOrgId->organisation_id;
	$aProjectData = $oAPC->readProjectDetails($sUrl);
	// Run Query to Fetch all <prefix>_project_updates
	$aProjectUpdateLog = $oAPC->readProjectUpdatesFromDb($sPrefix);
    
	// Iterate through results and rebuild new array with 'update' id as the array key
	$aProjectUpdates = array();
	if (sizeof($aProjectUpdateLog) == 0) {

	} else {
		foreach ($aProjectUpdateLog as $oUpdate) {
			$aProjectUpdates[$oUpdate['update_id']] = array('last_updated' => $oUpdate['last_updated'], 'post_id' => $oUpdate['post_id'], 'id' => $oUpdate['id']);
		}
	}
	$aProjectUpdateKeys = array_keys($aProjectUpdates);
    $aUpdatedThisRun = array();
	//$aProjectData = array_reverse($aProjectData, true);
     
//    die();
	// Extract all keys (array_keys)
	foreach ($aProjectData as $oProj) {

		$iAkvoUpdateId = $oProj['id'];
		$bSave = false;
		$iPostToUpdate = null;
		if (in_array($iAkvoUpdateId, $aProjectUpdateKeys)) {
			// Do a Update if the update time is different

			$oLastUpdatedInDb = new DateTime($aProjectUpdates[$iAkvoUpdateId]['last_updated']);
			$oLastUpdatedInApi = new DateTime($oProj['time_last_updated']);

			if ($oLastUpdatedInDb < $oLastUpdatedInApi) {
				$bSave = true;
				$iPostToUpdate = $aProjectUpdates[$iAkvoUpdateId]['post_id'];
			}
		} elseif(!in_array($iAkvoUpdateId, $aUpdatedThisRun)) {
			$aUpdatedThisRun[]=$iAkvoUpdateId;
			$bSave = true;
		}

		if ($bSave) {
			
			$sProjectId = $oProj['project'];
			$aProjectId = explode("/", $sProjectId);
			$iProjectId = $aProjectId[4];
			//$sImageFile=null;
			$aPosts = array(
				'post_date' => date("Y-m-d H:i:s",strtotime($oProj['time'])), //The time post was made.
				'post_date_gmt' => date("Y-m-d H:i:s",strtotime($oProj['time'])), //The time post was made, in GMT.
				'post_title' => $oProj['title'], //The title of your post.
				'post_content' => $oProj['text'], //The full text of the post.
				'post_name' => urlencode($oProj['title']),
				'post_type' => 'project_update',
				'post_modified' => date("Y-m-d H:i:s",strtotime($oProj['time_last_updated'])),
				'post_modified_gmt' => date("Y-m-d H:i:s",strtotime($oProj['time_last_updated']))
			);

			if (is_null($iPostToUpdate)) {
				echo 'yo';
                $iInsertedPostId = $oAPC->saveProjectUpdates($aPosts, $sPrefix);
				if (!is_null($oProj['photo']) && $oProj['photo'] != '') {
					// Image Resize and Save
					$sImageFile = "http://www.akvo.org" . $oProj['photo'];
					$sImageDest = $oAPC->imageResize($sImageFile);

					//$iInsertedPostId = $oAPC->saveProjectUpdates($aPosts, $sPrefix);
                    echo $iInsertedPostId;
					$oAPC->saveImageAttachment($sImageDest, $sPrefix, $iInsertedPostId);
					$oAPC->saveImageMeta($sPrefix,$sImageFile, $iInsertedPostId);
				} elseif(!is_null($oProj['video'])){
                    $sImageFile = $oAPC->getVideoImageUrl($oProj['video']);
                    if($sImageFile){
                        $sImageDest = $oAPC->imageResize($sImageFile);

                       //$iInsertedPostId = $oAPC->saveProjectUpdates($aPosts, $sPrefix, $iPostToUpdate);

                       $oAPC->saveImageAttachment($sImageDest, $sPrefix, $iInsertedPostId);
					$oAPC->saveImageMeta($sPrefix,$sImageFile, $iInsertedPostId);
                    }
                } else {
					//$iInsertedPostId = $oAPC->saveProjectUpdates($aPosts, $sPrefix);
				}

				$aProjectUpdateLogEntryData = array(
					'update_id' => $iAkvoUpdateId,
					'project_id' => $iProjectId,
					'post_id' => $iInsertedPostId,
					'last_updated' => date('Y-m-d H:i:s', strtotime($oProj['time_last_updated'])),
				);
				$oAPC->saveProjectUpdateLogEntry($sPrefix, $aProjectUpdateLogEntryData);
			} else {
				echo 'no';
				if (!is_null($oProj['photo']) && $oProj['photo'] != '') {
					// Image Resize and Save
					$sImageFile = "http://www.akvo.org" . $oProj['photo'];
					$sImageDest = $oAPC->imageResize($sImageFile);

					$oAPC->saveProjectUpdates($aPosts, $sPrefix, $iPostToUpdate);
                    $oAPC->saveImageAttachment($sImageDest, $sPrefix, $iPostToUpdate);
					$oAPC->saveImageMeta($sPrefix,$sImageFile, $iPostToUpdate);
					
				} elseif(!is_null($oProj['video'])){
                    $sImageFile = $oAPC->getVideoImageUrl($oProj['video']);
                    if($sImageFile){
                        $sImageDest = $oAPC->imageResize($sImageFile);

                        $oAPC->saveProjectUpdates($aPosts, $sPrefix, $iPostToUpdate);

                        $oAPC->saveImageAttachment($sImageDest, $sPrefix, $iPostToUpdate);
						$oAPC->saveImageMeta($sPrefix,$sImageFile, $iPostToUpdate);
                    }
                } else {
					$oAPC->saveProjectUpdates($aPosts, $sPrefix, $iPostToUpdate);
				}

				// To Do: Add Project Update Log Entry updating code
				$iIdToUpdate = $aProjectUpdates[$iAkvoUpdateId]['id'];

				$aProjectUpdateLogEntryData = array(
					'update_id' => $iAkvoUpdateId,
					'project_id' => $iProjectId,
					'post_id' => $iPostToUpdate,
					'last_updated' => date('Y-m-d H:i:s', strtotime($oProj['time_last_updated'])),
				);
				$oAPC->saveProjectUpdateLogEntry($sPrefix, $aProjectUpdateLogEntryData, $iIdToUpdate);
			}
		}






//
//		if (is_null($aProjectUpdates)) {
//			//insert to project updates table
//			//TODO
//		} else {
//			if (array_key_exists($oProj->id, $aProjectUpdates)) {
//				$oLastUpdatedInDb = new DateTime($aProjectUpdates[$oProj->id]);
//				$oLastUpdatedInApi = new DateTime(strtotime($oProj->time_last_updated));
//
//				if ($oLastUpdatedInDb < $oLastUpdatedInApi) {
//					//update
//					$oAPC->updateProjectUpdates($sPrefix, $oProj->id, $aPosts);
//					//image resize and save
//					$sImageFile = "http://www.akvo.org" . $oProj['photo'];
//					$sNewImgName = urlencode($oProj['title']);
//					$sImageDest = $oAPC->imageResize($sImageFile, $sNewImgName, 100, 100);
//					$oAPC->saveImageAttachment($sImageDest);
//				}
//
//
//			}
//		}
	}
}
?>
