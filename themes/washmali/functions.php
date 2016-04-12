<?php
/* 
    Created on : 23-Feb-2015, 18:39:41
    Author     : Rumeshkumar	
*/

////////////////////////////////////////////////////////////////////
// Theme Information
////////////////////////////////////////////////////////////////////

    $themename = "washmali";
    $developer_uri = "http://akvoblocks.com";
    $shortname = "dm";
    $version = '1.29';
    load_theme_textdomain( 'washmali', get_template_directory() . '/languages' );

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
	wp_enqueue_style('bxslider-style', get_stylesheet_directory_uri() . '/css/jquery.bxslider.css');
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

}

add_action( 'wp_enqueue_scripts', 'theme_enqueue_scripts' );
function theme_enqueue_scripts() {
	wp_enqueue_script('bxslider-js', get_stylesheet_directory_uri() . '/js/jquery.bxslider.min.js', array( 'jquery' ));    

}

add_action( 'widgets_init', 'washmali_register_sidebar' );
function washmali_register_sidebar() {   		
	register_sidebar(
	array(
		'name' => 'About page sidebar',
		'id' => 'about-sidebar',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h2>',
		'after_title' => '</h2>',
	));
}


// add custome post types to search 
function addCustomTypetoSearch($query)
{
  if ($query->is_search)
  { 
	  $aPostTypes = get_post_types(array('public' => true, 'exclude_from_search' => false));	  
	  $aPostTypes[] = 'project_update';	  
	  $query->set('post_type', $aPostTypes);	
  }
  return ($query);
}
add_filter('pre_get_posts', 'addCustomTypetoSearch');


function washmali_breadcrumb() {
    global $post;
    echo '<ol class="breadcrumb">';
    if (!is_home()) {
        echo '<li><a href="';
        echo get_option('home');
        echo '">';
        echo 'Page d’accueil';
        echo '</a></li>';
		if(is_search()) {
			echo '</li><li>';
			echo 'Recherche';
			echo '</li>';
		} else if (is_category() || is_single()) {
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
//add_filter('the_search_query', 'dj_addCustomType');

//function my_theme_styles() {
//    global $wp_styles;
//    $parentOriginalHandle = 'twentythirteen-style';
//    $parentNewHandle = 'parent-style';
//
//    // Deregister our style.css which was enqueued by the parent theme; we want
//    // to control the versioning ourself.
//    $parentStyleVersion = $wp_styles->registered[$parentOriginalHandle]->ver;
//    $parentDeps = $wp_styles->registered[$parentOriginalHandle]->deps;
//    wp_deregister_style($parentOriginalHandle);
//
//    // Enqueue the parent theme's style.css with whatever version it used instead
//    // of @import-ing it in the child theme's style.css
//    wp_register_style($parentNewHandle, get_template_directory_uri() . '/style.css',
//        $parentDeps, $parentStyleVersion);
//
//    // Enqueue our style.css with our own version
//    $themeVersion = wp_get_theme()->get('Version');
//    wp_enqueue_style($parentOriginalHandle, get_stylesheet_directory_uri() . '/style.css',
//        [$parentNewHandle], $themeVersion);
//}
//
//// Run this action action the parent theme has enqueued its styles.
//add_action('wp_enqueue_scripts', 'my_theme_styles', 20);

////////////////////////////////////////////////////////////////////
// Theme Information
////////////////////////////////////////////////////////////////////

//    $themename = "washmali";
//    $developer_uri = "http://akvoblocks.com";
//    $shortname = "dm";
//    $version = '1.00';
//    load_theme_textdomain( 'washmali', get_template_directory() . '/languages' );