<?php
/*
Plugin Name: Error Log Monitor
Plugin URI: http://w-shadow.com/blog/2012/07/25/error-log-monitor-plugin/
Description: Adds a Dashboard widget that displays the last X lines from your PHP error log, and can also send you email notifications about newly logged errors.
Version: 1.1
Author: Janis Elsts
Author URI: http://w-shadow.com/
*/

require dirname(__FILE__) . '/scb/load.php';
require dirname(__FILE__) . '/Elm/PhpErrorLog.php';
require dirname(__FILE__) . '/Elm/DashboardWidget.php';
require dirname(__FILE__) . '/Elm/Plugin.php';

scb_init('error_log_monitor_init');

function error_log_monitor_init() {
	new Elm_Plugin(__FILE__);
}
