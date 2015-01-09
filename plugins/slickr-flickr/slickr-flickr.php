<?php
/*
 * Plugin Name: Slickr Flickr
 * Plugin URI: http://www.slickrflickr.com
 * Description: Displays photos from Flickr in slideshows and galleries
 * Version: 1.44
 * Author: Russell Jamieson
 * Author URI: http://www.russelljamieson.com
 * License: GPLv2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html  
 */
define('SLICKR_FLICKR_VERSION','1.44');
define('SLICKR_FLICKR', 'slickr-flickr');
define('SLICKR_FLICKR_PLUGIN_URL', plugins_url(SLICKR_FLICKR));
define('SLICKR_FLICKR_HOME', 'http://www.slickrflickr.com');

require_once(dirname(__FILE__).'/slickr-flickr-utils.php');
require_once(dirname(__FILE__).'/slickr-flickr-'.(is_admin()?'admin':'public').'.php');
?>