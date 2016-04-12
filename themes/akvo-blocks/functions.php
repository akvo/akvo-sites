<?php

////////////////////////////////////////////////////////////////////
// Theme Information
////////////////////////////////////////////////////////////////////

    $themename = "AkvoBlocks";
    $developer_uri = "http://akvoblocks.com";
    $shortname = "dm";
    $version = '1.29';
    load_theme_textdomain( 'akvoblocksbootstrap3', get_template_directory() . '/languages' );

////////////////////////////////////////////////////////////////////
// include Theme-options.php for Admin Theme settings
////////////////////////////////////////////////////////////////////

   include 'theme-options.php';

////////////////////////////////////////////////////////////////////
// Include shortcodes.php for Bootstrap Shortcodes
////////////////////////////////////////////////////////////////////

    include 'shortcodes.php';

////////////////////////////////////////////////////////////////////
// Enqueue Styles (normal style.css and bootstrap.css)
////////////////////////////////////////////////////////////////////
    function akvoblocksbootstrap3_theme_stylesheets()
    {
        wp_register_style('bootstrap.css', get_template_directory_uri() . '/css/bootstrap.css', array(), '1', 'all' );
        wp_enqueue_style( 'bootstrap.css');
        wp_enqueue_style( 'stylesheet', get_stylesheet_uri(), array(), '1', 'all' );
        wp_register_style('style-md.css', get_stylesheet_directory_uri() . '/css/style-md.css', array(), '1', 'all' );
        wp_enqueue_style( 'style-md.css');
        wp_register_style('style-sm.css', get_stylesheet_directory_uri() . '/css/style-sm.css', array(), '1', 'all' );
        wp_enqueue_style( 'style-sm.css');
        wp_register_style('style-xs.css', get_stylesheet_directory_uri() . '/css/style-xs.css', array(), '1', 'all' );
        wp_enqueue_style( 'style-xs.css');
        
    }
    add_action('wp_enqueue_scripts', 'akvoblocksbootstrap3_theme_stylesheets');

//Editor Style
add_editor_style('css/editor-style.css');
add_action('init', 'akvoblocksbootstrap3_custom_init');
function akvoblocksbootstrap3_custom_init() {
	add_post_type_support( 'page', 'excerpt' );
}
////////////////////////////////////////////////////////////////////
// Register Bootstrap JS with jquery
////////////////////////////////////////////////////////////////////
    function akvoblocksbootstrap3_theme_js()
    {
        global $version;
        wp_enqueue_script('theme-js', get_template_directory_uri() . '/js/bootstrap.js',array( 'jquery' ),$version,false );
    }
    add_action('wp_enqueue_scripts', 'akvoblocksbootstrap3_theme_js');

////////////////////////////////////////////////////////////////////
// Add Title Parameters
////////////////////////////////////////////////////////////////////

    function akvoblocksbootstrap3_wp_title( $title, $sep ) { // Taken from Twenty Twelve 1.0
        global $paged, $page;

        if ( is_feed() )
            return $title;

        // Add the site name.
        $title .= get_bloginfo( 'name' );

        // Add the site description for the home/front page.
        $site_description = get_bloginfo( 'description', 'display' );
        if ( $site_description && ( is_home() || is_front_page() ) )
            $title = "$title $sep $site_description";

        // Add a page number if necessary.
        if ( $paged >= 2 || $page >= 2 )
            $title = "$title $sep " . sprintf( __( 'Page %s', 'akvoblocksbootstrap3' ), max( $paged, $page ) );

        return $title;
    }
    add_filter( 'wp_title', 'akvoblocksbootstrap3_wp_title', 10, 2 );

////////////////////////////////////////////////////////////////////
// Register Custom Navigation Walker include custom menu widget to use walkerclass
////////////////////////////////////////////////////////////////////

    require_once('lib/wp_bootstrap_navwalker.php');
    require_once('lib/bootstrap-custom-menu-widget.php');

////////////////////////////////////////////////////////////////////
// Register Menus
////////////////////////////////////////////////////////////////////

        register_nav_menus(
            array(
                'main_menu' => 'Main Menu',
                'footer_menu' => 'Footer Menu'
            )
        );

////////////////////////////////////////////////////////////////////
// Register the Sidebar(s)
////////////////////////////////////////////////////////////////////
global $dm_settings;

        if($dm_settings['header_sidebar']){
            register_sidebar(
                array(
                'name' => 'Header',
                'id' => 'header-sidebar',
                'before_widget' => '<aside id="%1$s" class="widget %2$s">',
                'after_widget' => '</aside>',
                'before_title' => '<h3>',
                'after_title' => '</h3>',
            ));
        }
        if($dm_settings['right_sidebar']){
            register_sidebar(
                array(
                'name' => 'Default Right Sidebar',
                'id' => 'right-sidebar',
                'before_widget' => '<aside id="%1$s" class="widget %2$s">',
                'after_widget' => '</aside>',
                'before_title' => '<h3>',
                'after_title' => '</h3>',
            ));
        }
        if($dm_settings['left_sidebar']){
            register_sidebar(
            array(
                'name' => 'Default Left Sidebar',
                'id' => 'left-sidebar',
                'before_widget' => '<aside id="%1$s" class="widget %2$s">',
                'after_widget' => '</aside>',
                'before_title' => '<h3>',
                'after_title' => '</h3>',
            ));
        }
        for($i=1;$i<=4;$i++){
        register_sidebar(
            array(
                'name' => 'Home area '.$i,
                'id' => 'home-area-'.$i,
                'before_widget' => '<aside id="%1$s" class="widget %2$s">',
                'after_widget' => '</aside>',
                'before_title' => '<h3>',
                'after_title' => '</h3>',
            ));
        }
////////////////////////////////////////////////////////////////////
// Register hook and action to set Main content area col-md- width based on sidebar declarations
////////////////////////////////////////////////////////////////////

add_action( 'akvoblocksbootstrap3_main_content_width_hook', 'akvoblocksbootstrap3_main_content_width_columns');

function akvoblocksbootstrap3_main_content_width_columns () {

    global $dm_settings;

    $columns = '12';

    if ($dm_settings['right_sidebar'] != 0) {
        $columns = $columns - $dm_settings['right_sidebar_width'];
    }

    if ($dm_settings['left_sidebar'] != 0) {
        $columns = $columns - $dm_settings['left_sidebar_width'];
    }

    echo $columns;
}

function akvoblocksbootstrap3_main_content_width() {
    do_action('akvoblocksbootstrap3_main_content_width_hook');
}

////////////////////////////////////////////////////////////////////
// Add support for a featured image and the size
////////////////////////////////////////////////////////////////////

    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size(300,300, true);

////////////////////////////////////////////////////////////////////
// Adds RSS feed links to for posts and comments.
////////////////////////////////////////////////////////////////////

    add_theme_support( 'automatic-feed-links' );


////////////////////////////////////////////////////////////////////
// Set Content Width
////////////////////////////////////////////////////////////////////

if ( ! isset( $content_width ) ) $content_width = 800;

////////////////////////////////////////////////////////////////////
// Allow editor to edit widgets
////////////////////////////////////////////////////////////////////
if(is_admin()){
	$role =& get_role('editor');
	$role->add_cap('manage_options');
	
}
////////////////////////////////////////////////////////////////////
// Add metaboxes for dynamic sidebars
////////////////////////////////////////////////////////////////////
require_once('lib/akvoblocks-admin-metaboxes.php');

////////////////////////////////////////////////////////////////////
// Register Akvo Blocks widgets
////////////////////////////////////////////////////////////////////
require_once('lib/akvoblocks-featured-widget/widget.php');
require_once('lib/akvoblocks-projectsmap-widget/widget.php');
require_once('lib/akvoblocks-search-widget.php');
require_once('lib/akvoblocks-language-switcher-widget.php');


function akvoblocks_register_widgets() {
	register_widget( 'AkvoblocksFeaturedWidget' );
	register_widget( 'AkvoblocksProjectsmapWidget' );
	register_widget( 'AkvoblocksSearchWidget' );
	register_widget( 'AkvoblocksLanguageSwitcherWidget' );
}

add_action( 'widgets_init', 'akvoblocks_register_widgets' );

////////////////////////////////////////////////////////////////////
// Load ACF
include_once('lib/advanced-custom-fields/acf.php');
////////////////////////////////////////////////////////////////////
// Testimonials if activated
global $dm_settings;
if($dm_settings['testimonials']){
    require_once('lib/akvoblocks-testimonials-widget/init.php');
}


///textclipper
if (!function_exists('textClipper')) {

    function textClipper($mValue, $mAmount = null, $sLink = '', $bTrimToLastPunctuation = true) {
        $bAddReadMoreText = false;

        if (is_null($mAmount))
            return $mValue;

        $iCharacterAmount = strlen($mValue);

        if ($mAmount < $iCharacterAmount) {
            $mValue = substr($mValue, 0, $mAmount);

            if ($bTrimToLastPunctuation) {
                $iFinalLocation = 0;
                $iPunctuationToUse = -1;
                $aPunctuation = array(' ', '.', '!', '?');
                foreach ($aPunctuation as $iIndex => $sPunctuation) {
                    $iLocation = strrpos($mValue, $sPunctuation);
                    if ($iLocation !== false && $iLocation > $iFinalLocation) {
                        $iFinalLocation = $iLocation;
                        $iPunctuationToUse = $iIndex;
                    }
                }
                if ($iFinalLocation != 0 && $iPunctuationToUse != -1) {
                    //$sTrailingFragment = strrchr($mValue, $aPunctuation[$iPunctuationToUse]);
                    //$mValue = str_replace($sTrailingFragment, $aPunctuation[$iPunctuationToUse], $mValue);
                    $iTrailingFragmentPosition = strrpos($mValue, $aPunctuation[$iPunctuationToUse]);
                    $mValue = substr($mValue, 0, $iTrailingFragmentPosition + 1);
                }
            }
            $bAddReadMoreText = true;
        }
        /*
          if ($bAddReadMoreText) {
          if ($sLink == '')
          $mValue .= "... <a href='#' title='Coming Soon'>Read More</a>";
          else
          $mValue .= "... <a href='" . $sLink . "' title='Read More in the PDF Document' target='_blank'>Read More</a>";
          }
         */
        if ($bAddReadMoreText)
            $mValue .= "<span title='Read More'>...</span>";
        return $mValue;
    }

}
function order_combined_posts($a,$b){
    $stampA = strtotime($a->post_date);
    $stampB = strtotime($b->post_date);
    if($stampA==$stampB){
        return 0;
    }
    return ($stampA < $stampB) ? 1 : -1;
}
function the_breadcrumb() {	
    global $post;
    echo '<ol class="breadcrumb">';
    if (!is_home()) {
        echo '<li><a href="';
        echo get_option('home');
        echo '">';
        echo 'Home';
        echo '</a></li>';
        if (is_category() || is_single()) {
            echo '<li>';
            the_category(' </li><li> ');
            if (is_single()) {
                echo '</li><li>';
                the_title();
                echo '</li>';
            }
        } elseif (is_page()) {
            if($post->post_parent){
                $anc = get_post_ancestors( $post->ID );
                $title = get_the_title();
                foreach ( $anc as $ancestor ) {					
                    $output = '<li><a href="'.get_permalink($ancestor).'" title="'.get_the_title($ancestor).'">'.get_the_title($ancestor).'</a></li>';
                }
                echo $output;
                echo $title;
            } else {				
                echo '<li>'.get_the_title().'</li>';
            }
        }
    }
    elseif (is_tag()) {single_tag_title();}
    elseif (is_day()) {echo"<li>Archive for "; the_time('F jS, Y'); echo'</li>';}
    elseif (is_month()) {echo"<li>Archive for "; the_time('F, Y'); echo'</li>';}
    elseif (is_year()) {echo"<li>Archive for "; the_time('Y'); echo'</li>';}
    elseif (is_author()) {echo"<li>Author Archive"; echo'</li>';}
    elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {echo "<li>Blog Archives"; echo'</li>';}
    elseif (is_search()) {echo"<li>Search Results"; echo'</li>';}
    echo '</ol>';
}
?>