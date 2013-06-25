<?php
/*
  Plugin Name: akvo-site-config
  Version: 1.0
  Author: Eveline Sparreboom
  Description: This plugin will help set-up new akvo partner websites.
 *
 */
require_once 'classes/AkvoSiteConfig.php';
require_once 'classes/youtube.php';
require_once 'classes/memberswidget.php';
require_once 'classes/newswidget.php';
require_once 'classes/aboutboxwidget.php';
require_once 'classes/flickr.php';

function asc_register_widgets() {
	register_widget( 'MembersWidget' );
	register_widget( 'NewsWidget' );
	register_widget( 'AboutboxWidget' );
}

add_action( 'widgets_init', 'asc_register_widgets' );
add_action('init', 'AkvoSiteConfig::registerNewsType');
///enable add categories to pages
add_action('admin_init', 'reg_tax');
function reg_tax() {
    register_taxonomy_for_object_type('category', 'page');
    add_post_type_support('page', 'category');
	AkvoSiteConfig::getPostsFeaturedVideo();
}


function akvo_contentblock($atts=null,$content){
    $addclass=(is_array($atts) && array_search('wide',$atts)!==false) ? 'no_sidebar' : '';
    return '</div><div class="post-wrapper '.$addclass.'">'.$content.'</div><div class="post-wrapper '.$addclass.'">';
}
add_shortcode( 'contentblock', 'akvo_contentblock' );
function akvo_newcontentblock($atts=null,$content){
    $addclass=(is_array($atts) && (array_search('wide',$atts)!==false) || array_key_exists('wide', $atts)) ? 'no_sidebar' : '';
    return '</div><div class="post-wrapper '.$addclass.'">';
}
add_shortcode( 'newcontentblock', 'akvo_newcontentblock' );

add_shortcode( 'akvoflickr', 'AkvoFlickrSlideshow::displayslideshow' );

// Get URL of first image in a post
function catch_that_image() {
    global $post, $posts, $blog_id;
    $first_img = '';
    ob_start();
    ob_end_clean();
    $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
    $first_img = $matches [1] [0];

    // no image found display default image instead
    if(empty($first_img)){
        $first_img = "";
    }
$first_img=str_replace('/wp-content/blogs.dir/'.$blog_id.'/files','/files',$first_img);
    return $first_img;
}

function catch_post_images(){
    global $post, $posts, $blog_id;
    $first_img = '';
    ob_start();
    ob_end_clean();
    $output = preg_match_all('|<img.+src=[\'"]([^\'"]+)[\'"].*>|U', $post->post_content, $matches);
    $img = $matches[1];
	foreach($img AS $k=>$v){
		$img[$k]=str_replace('/wp-content/blogs.dir/'.$blog_id.'/files','/files',$v);
	}
    // no image found display default image instead
    if(empty($img)){
        $img = "";
    }
    
    return $img;
}

function get_category_ids($aCatSlugs=array()){
    global $wpdb;
    $sSQL = "SELECT ".$wpdb->prefix."term_taxonomy.term_id
FROM   ".$wpdb->prefix."terms
  JOIN ".$wpdb->prefix."term_taxonomy USING (term_id)
WHERE  ".$wpdb->prefix."term_taxonomy.taxonomy = 'category'
   AND ".$wpdb->prefix."terms.slug IN ('".join("','",$aCatSlugs)."')";
   // echo $sSQL;
    $oResults = $wpdb->get_results($sSQL);
    $aReturn = array();
    foreach($oResults AS $cat){
        $aReturn[]=$cat->term_id;
    }
    //var_dump($aReturn);
    return $aReturn;
}
function get_tag_ids($aTagSlugs=array()){
    global $wpdb;
    $sSQL = "SELECT ".$wpdb->prefix."term_taxonomy.term_id
FROM   ".$wpdb->prefix."terms
  JOIN ".$wpdb->prefix."term_taxonomy USING (term_id)
WHERE  ".$wpdb->prefix."term_taxonomy.taxonomy = 'post_tag'
   AND ".$wpdb->prefix."terms.slug IN ('".join("','",$aTagSlugs)."')";
    //echo $sSQL;
    $oResults = $wpdb->get_results($sSQL);
    $aReturn = array();
    foreach($oResults AS $tag){
        $aReturn[]=$tag->term_id;
    }
    //var_dump($aReturn);
    return $aReturn;
}
///spider through all post types to see if video is embedded and set feature still image
// send automatic scheduled email
if ( ! wp_next_scheduled('akvo_featured_video') ) {
    wp_schedule_event( time(), 'hourly', 'akvo_featured_video' ); // hourly, daily and twicedaily
}
 
add_action( 'akvo_featured_video', 'AkvoSiteConfig::getPostsFeaturedVideo' );
?>