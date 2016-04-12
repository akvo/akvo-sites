<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$subRole = get_role( 'subscriber' );
$subRole->add_cap( 'read_private_pages' );


function akvo_project_domain(){
    add_option( 'akvo_project_domain', 'http://simavi.akvoapp.org/en');
}
add_action('init', 'akvo_project_domain');

/*
 * A user with user name "gnwp" is created so that GNWP can share the login details 
 * with its stakeholders to give access to dropbox (www.gnwp.nl/secure). 
 * User with username "gnwp" can access admin section. 
 * So that user can edit profile info as well. The following code is 
 * added to restrict access to profile page for that user (ID - 416)
 */
function gnwp_restrict_gnwp_user() {
    //if (!current_user_can('administrator')) {
	$current_user = wp_get_current_user();
	if($current_user->ID == 416) {
		remove_menu_page( 'profile.php' );
		remove_submenu_page( 'users.php', 'profile.php' );
		if(IS_PROFILE_PAGE === true ) {		
			wp_die( 'Please contact your administrator to have your profile information changed.' );
		}
    }
}
add_action('admin_init', 'gnwp_restrict_gnwp_user', 1);


?>
