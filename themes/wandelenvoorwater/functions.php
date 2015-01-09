<?php

//function new_excerpt_more($more) {
//       global $post;
//       return '<a class="moretag" href="'. get_permalink($post->ID) . '"> Lees verder...</a>';
//}
//add_filter('excerpt_more', 'new_excerpt_more');
$subRole = get_role( 'subscriber' );
$subRole->add_cap( 'read_private_pages' );

//function redirect_users() {
//    // retrieve current user info
//    global $current_user;
//    get_currentuserinfo();
//
//    // If login user role is Subscriber
//    if ($current_user->user_level == 0) {
//	wp_redirect(home_url());
//	exit;
//    }
//}
//add_action('admin_init', 'redirect_users');
?>