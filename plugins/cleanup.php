<?php
/**
 * @package Akvo Cleanup
 * @version 1.0
 */
/*
Plugin Name: Akvo Cleanup
Description: Cleanup unused data akvo sites
Author: Eveline Sparreboom
Version: 1.0
*/
require_once '../../wp-config.php';
//$attargs = array(
//            'post_type' => 'attachment',
//            'posts_per_page' => 5000,
//            'post_status' => 'any',
//            //'post_parent' => '0'
//           );
//            $att_query = new WP_Query( $attargs );
//           $attachments = $att_query->posts;
//              if ( $attachments ) {
//                 foreach ( $attachments as $attachment ) {
//                     if(
//                             preg_match("/([a-z0-9]{23})-271/i",$attachment->post_title)
//                             || preg_match("/^0-271.{4}/i",$attachment->post_title)
//                             || preg_match("/^ProjectUpdate_([0-9]{2,4})_photo_([0-9]{4})-([0-9]{2})-([0-9]{2})_([0-9]{2}).([0-9]{2}).([0-9]{2})-271/i",$attachment->post_title)
//                             //|| preg_match("/-271.{4}/i",$attachment->post_title)
//                             ){
//                     //if(strpos($attachment->post_title, '-271x167')!==false){
//                    echo '<li>';
//                    //echo wp_get_attachment_image( $attachment->ID, 'full' );
//                    echo '<p>';
//                    echo apply_filters( 'the_title', $attachment->post_title );
//                    echo '</p></li>';
//                    wp_delete_attachment( $attachment->ID, true );
//                     }
//                   }
//              }
//$args['posts_per_page']=100;
//$args['offset']=0;
//$args['post_status']='any';
//$args['post_type'] = 'project_update';
//// The Query
//$the_query = new WP_Query( $args );
//
//// The Loop
//if ( $the_query->have_posts() ) {
//	while ( $the_query->have_posts() ) {
//		$the_query->the_post();
//		$attargs = array(
//            'post_type' => 'attachment',
//            'posts_per_page' => -1,
//            'post_status' => 'any',
//            'post_parent' => get_the_ID()
//           );
//            $att_query = new WP_Query( $attargs );
//           $attachments = $att_query->posts;
//              if ( $attachments ) {
//                 foreach ( $attachments as $attachment ) {
//                    echo '<li>';
//                    echo '<p>';
//                    echo apply_filters( 'the_title', $attachment->post_title );
//                    echo '</p></li>';
//                    wp_delete_attachment( $attachment->ID, true );
//                   }
//              }
//	}
//} else {
//	// no posts found
//}

$args['posts_per_page']=500;
$args['offset']=0;
$args['post_status']='any';
$args['post_type'] = 'project_update';
// The Query
$the_query = new WP_Query( $args );

// The Loop
if ( $the_query->have_posts() ) {
	while ( $the_query->have_posts() ) {
		$the_query->the_post();
        $id = get_the_ID();
        $sEnclosure = get_post_meta($id, 'enclosure',true);
		$aEnclosure = get_post_meta($id, 'enclosure');
        akvo_debug_dump($sEnclosure);
        akvo_debug_dump($aEnclosure);
        delete_post_meta($id, 'enclosure');
        update_post_meta($id, 'enclosure', $sEnclosure);
        //break;
	}
} else {
	// no posts found
}
/* Restore original Post Data */
wp_reset_postdata();

?>