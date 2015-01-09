<?php


error_reporting(0);
require_once '../../../wp-config.php';
//require_once '/wp-content/plugins/dwa-project-detail-reader/ProjectUpdateLogic.php';


require_once 'AkvoPartnerCommunication.php';
$iOrganisationID = (isset($_GET['id_organisation'])) ? $_GET['id_organisation'] : null;
$oAPC = new AkvoPartnerCommunication();
$iOneWeekAgo = strtotime('-7 days');
$sUpdateUrl = "http://rsr.akvo.org/api/v1/project_update/?format=json&limit=0&time__gt=".date('Y-m-d',$iOneWeekAgo)."&project__partnerships__organisation=";

$aPartnerData = $oAPC->readURLsForCronJob($iOrganisationID);
$original_blog_id = get_current_blog_id();
//$aSimplifiedArray = $aPartnerData[];
shuffle($aPartnerData);
//var_dump($aPartnerData); 
//die();
foreach ($aPartnerData as $oOrg) {
    
    echo '<h1>'.$oOrg->organisation_id.'</h1>';
	$sPrefix = $oOrg->prefix;
	
    $sUrl = $sUpdateUrl . $oOrg->organisation_id;
    switch_to_blog(getBlogIdFromPrefix($sPrefix));
    bloginfo();
    //break;
    $aUpdateData = $oAPC->readProjectDetails($sUrl);
    $aExistingUpdates = array();
    $aProjectUpdates = array();
    $aOrgUpdates = $oAPC->readProjectUpdatesFromDb($sPrefix);
    
    foreach ($aOrgUpdates as $oUpdate) {
            $time = strtotime($oUpdate['last_updated']);
            if($time >= $iOneWeekAgo){
                $aProjectUpdates[$oUpdate['update_id']] = array('last_updated' => $oUpdate['last_updated'], 'post_id' => $oUpdate['post_id'], 'id' => $oUpdate['id']);
            }
		}
    foreach ($aUpdateData as $aUpdate) {
        $aExistingUpdates[]=$aUpdate['id'];
    }
    
    $aNotExisting = array_diff(array_keys($aProjectUpdates),$aExistingUpdates);
    
    foreach($aNotExisting AS $iDelete){
        echo '<h2>'.$iDelete.'</h2>';
        wp_delete_post($aProjectUpdates[$iDelete]['post_id']);
    }
    var_dump($aNotExisting);
    
}
switch_to_blog( $original_blog_id );
bloginfo();

function getBlogIdFromPrefix($sPrefix){
    $blogID = substr($sPrefix,(strpos($sPrefix, '_')+1),-1);
    return $blogID;
}
?>
