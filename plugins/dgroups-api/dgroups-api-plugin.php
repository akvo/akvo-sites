<?php
/**
Plugin Name: Dgroups API plugin
Plugin URI: http://kominski.net
Description: Dgroups API plugin developed for akvo sites
Author: Eveline Sparreboom
Version: 1
*/

global $dgroups_api_plugin_options;
require_once 'dgroups-api-class.php';
$dgroups_api_plugin_options = get_option( 'dgroups_api_plugin_options' );
/** admin menu actions
  * add the top level menu and register the submenus.
  */ 
function dgroups_api_admin_actions(){
	
	add_menu_page('Dgroups API', 'Dgroups API', 'manage_options', 'dgroups-api', 'dgroups_api_settings' );
	
}

/**
 * Load plugin settings page
 */
function dgroups_api_settings(){
    require_once 'settings_page.php';
}
/** include needed javascript scripts based on current page
  *  @param string
  */
function enqueue_dgroups_api_scripts( $requested_page ){

}

/**
 * include needed styles
 */
function enqueue_dgroups_api_styles( $requested_page ){
}
/**
 * load widgets
 */
require_once('dgroups-api-widget.php');
require_once('dgroups-login-widget.php');
function dgroups_api_register_widgets() {
	register_widget( 'DgroupsApiWidget' );
	register_widget( 'DgroupsLoginWidget' );
}

add_action( 'widgets_init', 'dgroups_api_register_widgets' );
/**
 * register admin menu 
 */
add_action('admin_menu', 'dgroups_api_admin_actions');

/**
 * include plugin js and css.
 */
add_action('admin_enqueue_scripts', 'enqueue_dgroups_api_scripts');
add_action('admin_print_styles', 'enqueue_dgroups_api_styles' );


?>