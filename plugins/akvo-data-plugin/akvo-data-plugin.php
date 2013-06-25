<?php
/*
Plugin Name: Akvo data plugin
Description: Akvo data dashboard
Version: 1.0
Author: Eveline Sparreboom
Author URI: http://www.kominski.net
License: GPl2
*/

/* We need to make sure our functions can be seen! */
 
include_once dirname(__FILE__) . '/functions.php';
 
/* The following events are not saved and must be executed on each page load */
 
register_activation_hook( __FILE__, "akvodata_activated");
register_deactivation_hook( __FILE__, "akvodata_deactivated");
 
/* This action will call the function to create a menu button */
add_action('admin_menu', 'akvodata_add_menu_page');
 
/* This will load our admin panel javascript and CSS */
add_action('admin_enqueue_scripts', 'akvodata_admin_scripts');
 

 
add_action( 'widgets_init', 'akvodata_register_widgets' );
add_action( 'init', 'akvodata_widget_scripts' );
/* This shortcode allows us to run a function on the content of each post
   before it is displayed */
//$options = get_option('akvodata_opts');
//add_shortcode($options['search'], 'akvodata_replace_keyword');
?>