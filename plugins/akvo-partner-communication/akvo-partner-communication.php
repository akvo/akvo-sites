<?php
/*
  Plugin Name: akvo-partner-communication
  Version: 1.0
  Author: Uthpala Sandirigama
  Description: This plugin will read the Dutch Wash Alliances Pojects(partner of Akvo Org) data from their API
  and stores them to the DB. And also provide functionalities for reading them back.
 *
 */

require_once 'AkvoPartnerCommunication.php';

/* Runs when plugin is activated */
function apc_plugin_activated () {
	$oApc = new AkvoPartnerCommunication();
    $oApc->install();
    
}
register_activation_hook(__FILE__, 'apc_plugin_activated');

/* Runs on plugin deactivation */
function apc_plugin_deactivated () {
	$oApc = new AkvoPartnerCommunication();
	$oApc->uninstall();
}
register_deactivation_hook(__FILE__, 'apc_plugin_deactivated');

if (is_admin()) {
	/* Call the html code */
	add_action('admin_menu', 'AkvoPartnerCommunication::addMenuToAdminMenu');
    add_action('init', 'AkvoPartnerCommunication::addPostTypes');
    $oApc = new AkvoPartnerCommunication();
    add_action( 'add_meta_boxes', array(&$oApc,'addMetaboxes') );
    add_action( 'save_post', 'AkvoPartnerCommunication::saveCountryforPost' );
} else {

}

//google map
function showMap($sCountry='',$iZoom=0) {
	if (class_exists("AkvoPartnerCommunication")) {

		$oApc = new AkvoPartnerCommunication();
		$oProjects = $oApc->getAllProjectsData();
		$sMapScripts = $oApc->displayMap($oProjects,$sCountry,$iZoom);
		echo $sMapScripts;
	}
}




?>