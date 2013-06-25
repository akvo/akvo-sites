<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'AkvoPartnerCommunication.php';

$oAPC = new AkvoPartnerCommunication();

//read the URLs from the partner details table
$aPartnerUrls = $oAPC->readURLsForCronJob();

foreach ($aPartnerUrls as $oUrl) {
	$aProjectData = $oAPC->readProjectDetails($oUrl->data_url);

	foreach ($aProjectData as $oProj) {
		$oAPC->flushProjectDetails($oUrl->prefix);
		$oAPC->saveProjectDetails($oProj, $oUrl->prefix);
		$sDestPath = $oAPC->imageResize($sImageFile, $iWidth, $iHeight);
	}
}

?>
