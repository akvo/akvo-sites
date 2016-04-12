<?php

/**
 * CronController
 *
 *
 * @author Uthpala Sandirigama <damsirini.uthpala@gmail.com>
 */

error_reporting(0);
require_once '../../../wp-config.php';
//require_once '/wp-content/plugins/dwa-project-detail-reader/ProjectUpdateLogic.php';


require_once 'AkvoPartnerCommunication.php';
$iOrganisationID = (isset($_GET['id_organisation'])) ? $_GET['id_organisation'] : null;
$oAPC = new AkvoPartnerCommunication();
$iLimit = 100 + (int)date('z') + (int)date('G');

$sUpdateUrl = "http://rsr.akvo.org/api/v1/project_update/?format=json&limit=".$iLimit."&project__";

$aPartnerData = $oAPC->readURLsForCronJob($iOrganisationID);
$sRuntime = time();
$sMailMessage = $iOrganisationID.'<br />';
$sMailMessage = 'runtime:'.  $sRuntime.'<br /><br />';
error_log($iOrganisationID);
error_log('runtime:'.  $sRuntime);
shuffle($aPartnerData);
foreach ($aPartnerData as $oOrgId) {
	
	if($oOrgId->organisation_id == '2121' || $oOrgId->organisation_id == '275' || $oOrgId->rsr_keywords == 'rain4food') {
		die('rain4food'); continue;
	}
	
	if(true) {
		continue;
	}
	
    $iLimit = 1000 + (int)date('z') + (int)date('G');
    
    if(isset($oOrgId->rsr_keywords) && $oOrgId->rsr_keywords!==''){
        echo '<h1>'.$oOrgId->rsr_keywords.' yo</h1>';
        $sUrlOption = 'keywords__label=' . $oOrgId->rsr_keywords;
    }else{
        echo '<h1>'.$oOrgId->organisation_id.'</h1>';
        $sUrlOption = 'partnerships__organisation=' . $oOrgId->organisation_id;
    }
    $sUrl = AkvoPartnerCommunication::API_URL_FOR_PROJECTS . '&' . $sUrlOption. '&limit='.$iLimit;
	echo $sUrl.'<br />';
	// example for $sUrl => http://rsr.akvo.org/api/v1/project/?format=json&partnerships__organisation=275&limit=1119
	
	$sPrefix = $oOrgId->prefix;
	$aProjectData = $oAPC->readProjectDetails($sUrl);

    $oAPC->flushProjectDetails($sPrefix);
    $oAPC->saveProjectDetails($aProjectData, $sPrefix, $sUrlOption);
    $oAPC->saveProjectPartners($aProjectData, $sPrefix);

}

///iterate through organisations
foreach ($aPartnerData as $oOrgId) {
	
	if($oOrgId->organisation_id == '2121' || $oOrgId->organisation_id == '275') {
		continue;
	}
	
	$sPrefix = $oOrgId->prefix;
	$sDuplicates='';
    if(isset($oOrgId->rsr_keywords) && $oOrgId->rsr_keywords!==''){
        echo '<h1>'.$oOrgId->rsr_keywords.'</h1>';
        $sUrlOption = 'keywords__label=' . $oOrgId->rsr_keywords;
    }else{
        echo '<h1>'.$oOrgId->organisation_id.'</h1>';
        $sUrlOption = 'partnerships__organisation=' . $oOrgId->organisation_id;
    }
    $sUrl = $sUpdateUrl . $sUrlOption;
     echo $sUrl.'<br />';
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

    // Extract all keys (array_keys)
	foreach ($aProjectData as $oProj) {

		$iAkvoUpdateId = $oProj['id'];
		$bSave = false;
		$iPostToUpdate = null;
        
        
		if (in_array($iAkvoUpdateId, $aProjectUpdateKeys)) {
			// Do an Update if the update time is different

			$oLastUpdatedInDb = new DateTime($aProjectUpdates[$iAkvoUpdateId]['last_updated']);
			$oLastUpdatedInApi = new DateTime($oProj['time_last_updated']);

			if ($oLastUpdatedInDb < $oLastUpdatedInApi) {
                $sMailMessage .= 'update id: '.$iAkvoUpdateId.'<br />';
                $sMailMessage .= 'action: update<br />';
                error_log('update id: '.$iAkvoUpdateId);
                error_log('action: update');
				$bSave = true;
				$iPostToUpdate = $aProjectUpdates[$iAkvoUpdateId]['post_id'];
			}else{
                //$sMailMessage .= 'action: duplicate<br />';
                $sDuplicates .= $iAkvoUpdateId.',';
                
            }
		} elseif(!in_array($iAkvoUpdateId, $aUpdatedThisRun)) {
            $sMailMessage .= 'update id: '.$iAkvoUpdateId.'<br />';
			$sMailMessage .= 'action: insert<br />';
            error_log('update id: '.$iAkvoUpdateId);
            error_log('action: insert');
            $aUpdatedThisRun[]=$iAkvoUpdateId;
			$bSave = true;
		}

		if ($bSave) {
			
			$sProjectId = $oProj['project'];
			$aProjectId = explode("/", $sProjectId);
			$iProjectId = $aProjectId[4];
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
                $sMailMessage .= 'do insert<br />';
                error_log('do insert');
            
                $iInsertedPostId = $oAPC->saveProjectUpdates($aPosts, $sPrefix);
				if (!is_null($oProj['photo']) && $oProj['photo'] != '') {
					// Image Resize and Save
					$sImageFile = "http://www.akvo.org" . $oProj['photo'];

                    echo $iInsertedPostId;
					$oAPC->saveImageMeta($sPrefix,$sImageFile, $iInsertedPostId);
				} elseif(!is_null($oProj['video'])){
                    $sImageFile = $oAPC->getVideoImageUrl($oProj['video']);
                    if($sImageFile){
					$oAPC->saveImageMeta($sPrefix,$sImageFile, $iInsertedPostId);
                    }
                } else {
				}

				$aProjectUpdateLogEntryData = array(
					'update_id' => $iAkvoUpdateId,
					'project_id' => $iProjectId,
					'post_id' => $iInsertedPostId,
					'last_updated' => date('Y-m-d H:i:s', strtotime($oProj['time_last_updated'])),
				);
				$oAPC->saveProjectUpdateLogEntry($sPrefix, $aProjectUpdateLogEntryData);
			} else {
				$sMailMessage .= 'do update<br />';
                error_log('do update');
            
                if (!is_null($oProj['photo']) && $oProj['photo'] != '') {
					// Image Resize and Save
					$sImageFile = "http://www.akvo.org" . $oProj['photo'];
					$oAPC->saveProjectUpdates($aPosts, $sPrefix, $iPostToUpdate);
					$oAPC->saveImageMeta($sPrefix,$sImageFile, $iPostToUpdate);
					
				} elseif(!is_null($oProj['video'])){
                    $sImageFile = $oAPC->getVideoImageUrl($oProj['video']);
                    if($sImageFile){
                        $oAPC->saveProjectUpdates($aPosts, $sPrefix, $iPostToUpdate);
						$oAPC->saveImageMeta($sPrefix,$sImageFile, $iPostToUpdate);
                    }
                } else {
					$oAPC->saveProjectUpdates($aPosts, $sPrefix, $iPostToUpdate);
				}

				$iIdToUpdate = $aProjectUpdates[$iAkvoUpdateId]['id'];

				$aProjectUpdateLogEntryData = array(
					'update_id' => $iAkvoUpdateId,
					'project_id' => $iProjectId,
					'post_id' => $iPostToUpdate,
					'last_updated' => date('Y-m-d H:i:s', strtotime($oProj['time_last_updated'])),
				);
				$oAPC->saveProjectUpdateLogEntry($sPrefix, $aProjectUpdateLogEntryData, $iIdToUpdate);
			}
            $sMailMessage .= '------------<br />';
            error_log('------------');
		}
        
            
	}
    $sMailMessage .= 'duplicates: '.$sDuplicates.'<br />';
    error_log('duplicates: '.$sDuplicates);
}
echo $sMailMessage;
//add_filter( 'wp_mail_content_type', 'set_html_content_type' );
//
////wp_mail( 'eveline@kominski.net', 'Akvo cron log', $sMailMessage );
//
//// Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
//remove_filter( 'wp_mail_content_type', 'set_html_content_type' );



function set_html_content_type() {

	return 'text/html';
}
?>
