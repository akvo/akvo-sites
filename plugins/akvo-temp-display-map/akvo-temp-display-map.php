<?php

/*
  Plugin Name: akvo-temp-display-map
  Version: 1.0
  Author: Rumeshkumar
  Description: This is a temporary plugin to display maps
 *
 */

require_once 'AkvoTempDisplayMap.php';

function showTempMap($sCountry='',$iZoom=0) {
	
	$oATDM = new AkvoTempDisplayMap();
	
	//fetch data from project tbl
	$aProjects = $oATDM->getProjectsAndLocations();
	
	//display map
	$sMapScripts = $oATDM->displayMap($aProjects, $sCountry, $iZoom);
	
	echo $sMapScripts;
}

?>
