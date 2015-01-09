<?php
/*
Plugin Name: KWGPress - Core
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: Allows administrators to create and use forms on the site.
Version: 1.0.0
Author: Jayawi Perera
Author URI: http://wp.jayawi.com
License: GPL2
*/
/*  Copyright 2013  Jayawi Perera  (email : jayawiperera@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
require_once 'autoloader.php';

function is_kwgpress_core_active () {
	return true;
}

// Removes the WordPress Update prompt in Admin
// Taken from http://wordpress.org/plugins/disable-wordpress-core-update/
add_filter('pre_site_transient_update_core', function () {
	return null;
});

add_action('activated_plugin', function () {

	$sThisPluginFile = basename(dirname(__FILE__)) . '/' . basename(__FILE__);

	$aActivePlugins = get_option('active_plugins');

	// Key of KWGPress Core Plugin
	$iThisPluginKey = array_search($sThisPluginFile, $aActivePlugins);

	if ($iThisPluginKey === FALSE) {

		// KWGPress Core Plugin is not among the active plugins
		// - do nothing...

	} else {

		$bReArrangeRequired = true;

		// Plugin entries that are found prior to the first occurrence of a
		// KWGPress Plugin
		// - they make the first part of the new plugin list
		$aNewActivePluginPart1 = array();
		// KWGPress Core Plugin entry
		// - this makes the second part of the new plugin list since it needs to
		//   appear before any other KWGPress Plugin
		$aNewActivePluginPart2 = array();
		// All other remaining plugin entries
		// - if KWGPress Core is 'initially' amongst this group, we move it
		$aNewActivePluginPart3 = array();

		$iFirstKwgPressOccurrenceKey = null;

		// Iterate through the Plugin List while searching for the first
		// occurrence of a KWGPress Plugin
		foreach ($aActivePlugins as $iKey => $sActivePlugin) {

			if (is_null($iFirstKwgPressOccurrenceKey)) {
				// A KWGPress Plugin has not been found yet
				// - we need to keep checking for one

				if (stripos($sActivePlugin, 'kwgpress-') !== FALSE) {
					// First occurrence of a KWGPress Plugin found
					$iFirstKwgPressOccurrenceKey = $iKey;

					if ($iFirstKwgPressOccurrenceKey == $iThisPluginKey) {
						// The first occurrence of a KWGPress Plugin *is* the
						// KWGPress Core and thus we do not have to re-arrange
						$bReArrangeRequired = false;
						break;

					} else {
						// We should place the first occurrence of a KWGPress
						// Plugin in the *last part* of the new plugin list
						$aNewActivePluginPart3[] = $sActivePlugin;

					}

				} else {
					// We add all Plugin entries before the first occurrence of
					// a KWGPress Plugin to the *first part* of the new plugin
					// list
					$aNewActivePluginPart1[] = $sActivePlugin;

				}

			} else {

				if ($iKey == $iThisPluginKey) {
					// This entry is the KWGPress Core entry
					// - we need to place this in the *middle part* of the new
					//   plugin list
					$aNewActivePluginPart2[] = $sActivePlugin;

				} else {
					// This can be any plugin entry but since the order for
					// these does not matter we add them to the *last part* of
					// the new plugin list
					$aNewActivePluginPart3[] = $sActivePlugin;

				}

			}

		}

		if ($bReArrangeRequired) {
			// Merge all the parts of the New Plugin List and save it
			$aNewActivePluginList = array_merge($aNewActivePluginPart1, $aNewActivePluginPart2, $aNewActivePluginPart3);
			update_option('active_plugins', $aNewActivePluginList);

		}
	}

});