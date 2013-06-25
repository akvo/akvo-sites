<?php
/*
  Plugin Name: akvo-profile-fields
  Version: 1.0
  Author: Eveline Sparreboom
  Description: This plugin will help set-up new akvo partner websites.
 *
 */
require_once 'classes/profilefields.php';
// Add new taxonomy, NOT hierarchical (like tags)
  
add_action( 'show_user_profile', 'AkvoProfileFields::add_extra_meta_fields' );
add_action( 'edit_user_profile', 'AkvoProfileFields::add_extra_meta_fields' );
add_action( 'personal_options_update', 'AkvoProfileFields::save_extra_meta_fields' );
add_action( 'edit_user_profile_update', 'AkvoProfileFields::save_extra_meta_fields' );

?>
