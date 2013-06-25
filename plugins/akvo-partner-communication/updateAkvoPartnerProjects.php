<?php
/**
 * Cron Job Script
 * Will retrieve Project Updates and store them in the Partner-specific Database Tables
 */

require_once 'AkvoPartnerCommunication.php';

$oAPC = new AkvoPartnerCommunication();
$sUpdateUrl = "http://www.akvo.org/api/v1/project_update/?project__partnerships__organisation=";
$aPartnerData = $oAPC->readURLsForCronJob();

foreach ($aPartnerData as $oOrgId) {
	$sUrl = $sUpdateUrl . $oOrgId->organisation_id;
	$sPrefix = $oOrgId->prefix;
	$aProjectData = $oAPC->readProjectDetails($sUrl);

	// Run Query to Fetch all <prefix>_project_updates
	$oProjectUpdates = $oAPC->readProjectUpdatesFromDb($sPrefix);

	// Iterate through results and rebuild new array with 'update' id as the array key
	$aProjectUpdates = array();
	if (is_null($oProjectUpdates)) {

	} else {
		foreach ($oProjectUpdates as $oUpdate) {
			$aProjectUpdates = array(
				$oUpdate->update_id => $oUpdate->last_updated
			);
		}
	}

	// Extract all keys (array_keys)
	foreach ($aProjectData as $oProj) {

		$sProjectId = $oProj->project;
		$aProjectId = explode("/", $sProjectId);
		$iProjectId = $aProjectId[4];

		$aPosts = array(
			'post_date' => date("Y-m-d H:i:s"), //The time post was made.
			'post_date_gmt' => date("Y-m-d H:i:s"), //The time post was made, in GMT.
			'post_title' => $oProj->title, //The title of your post.
			'post_content' => $oProj->text, //The full text of the post.
			'post_name' => urlencode($oProj->title),
			'post_type' => 'project_update',
			'post_modified' => date("Y-m-d H:i:s"),
			'post_modified_gmt' => date("Y-m-d H:i:s")
		);


		if (is_null($aProjectUpdates)) {
			$oAPC->saveProjectUpdates($aPosts, $sPrefix, $iProjectId);
			//image resize and save
			$sImageFile = "http://www.akvo.org" . $oProj['photo'];
			$sNewImgName = urlencode($oProj['title']);
			$sImageDest = $oAPC->imageResize($sImageFile, $sNewImgName, 100, 100);
			$oAPC->saveImageAttachment($sImageDest);

			//insert to project updates table
			//TODO
		} else {
			if (array_key_exists($oProj->id, $aProjectUpdates)) {
				$oLastUpdatedInDb = new DateTime($aProjectUpdates[$oProj->id]);
				$oLastUpdatedInApi = new DateTime(strtotime($oProj->time_last_updated));

				if ($oLastUpdatedInDb < $oLastUpdatedInApi) {
					//update
					$oAPC->updateProjectUpdates($sPrefix, $oProj->id, $aPosts);
					//image resize and save
					$sImageFile = "http://www.akvo.org" . $oProj['photo'];
					$sNewImgName = urlencode($oProj['title']);
					$sImageDest = $oAPC->imageResize($sImageFile, $sNewImgName, 100, 100);
					$oAPC->saveImageAttachment($sImageDest);
				}


			}
		}
	}
}
?>
