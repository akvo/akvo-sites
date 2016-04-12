<?php

//function new_excerpt_more($more) {
//       global $post;
//       return '<a class="moretag" href="'. get_permalink($post->ID) . '"> Lees verder...</a>';
//}
//add_filter('excerpt_more', 'new_excerpt_more');
$subRole = get_role( 'subscriber' );
$subRole->add_cap( 'read_private_pages' );

update_option('akvo_project_domain',"http://walkingforwater2015.akvoapp.org/en");

//function wfw_the_content_filter($content) {
//  // assuming you have created a page/post entitled 'debug'
//  $content = str_replace('Walking for Water', '<span class="cSpanBrand">Walking for Water</span>', $content);
//  // otherwise returns the database content
//  return $content;
//}
//
//add_filter( 'the_content', 'wfw_the_content_filter' );

//function set_html_content_type() {
//
//	return 'text/html';
//}
?>