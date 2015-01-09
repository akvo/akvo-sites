<?php
/**
 * Plugin Name: Zimmerman & Zimmerman partner map plug-in
 * Plugin URI: 
 * Description: Partner map to show partners
 * Version: 1.1
 * Author: Zimmerman & Zimmerman
 * Author URI: 
 * License: 
 */





function load_partner_map( $atts ) {
    include(plugin_dir_path( __FILE__ ) . 'partners-map.php');
}
add_shortcode( 'zz-partner-map', 'load_partner_map' );




// add_filter( 'page_template', 'wpa3396_page_template' );
// function wpa3396_page_template( $page_template )
// { 
//     if ( is_page( 'partner-page' ) ) {
//         $page_template = plugin_dir_path( __FILE__ ) . 'partners-map.php';
//     }
//     return $page_template;
// }


// function get_partner_post_type_template($single_template) {
//      global $post;

//      if ($post->post_type == 'partner-page') {
//           $single_template = dirname( __FILE__ ) . '/partners-map.php';
//      }
//      return $single_template;
// }



function zz_partners_map_ajax() {

  $include_path = plugin_dir_path( __FILE__ ) . 'partners-ajax-call.php';
  include($include_path);
  die();
}
add_action('wp_ajax_zz_partners_map_ajax', 'zz_partners_map_ajax');
add_action('wp_ajax_nopriv_zz_partners_map_ajax', 'zz_partners_map_ajax');


function zz_partners_list() {

  $include_path = plugin_dir_path( __FILE__ ) . 'partners-list-html.php';
  include($include_path);
  die();
}
add_action('wp_ajax_zz_partners_list', 'zz_partners_list');
add_action('wp_ajax_nopriv_zz_partners_list', 'zz_partners_list');

function zz_partners_search() {

  $include_path = plugin_dir_path( __FILE__ ) . 'partners-ajax-search.php';
  include($include_path);
  die();
}
add_action('wp_ajax_zz_partners_search_ajax', 'zz_partners_search');
add_action('wp_ajax_nopriv_zz_partners_search_ajax', 'zz_partners_search');


//add_filter( "single_template", "get_partner_post_type_template" );

// Register Custom Post Type
function zz_network_map() {

  $labels = array(
    'name'                => _x( 'Partners', 'Post Type General Name', 'Zimmerman' ),
    'singular_name'       => _x( 'Partner', 'Post Type Singular Name', 'Zimmerman' ),
    'menu_name'           => __( 'Partner map', 'Zimmerman' ),
    'parent_item_colon'   => __( 'Parent Partner:', 'Zimmerman' ),
    'all_items'           => __( 'All Partners', 'Zimmerman' ),
    'view_item'           => __( 'View Partner', 'Zimmerman' ),
    'add_new_item'        => __( 'Add New Partner', 'Zimmerman' ),
    'add_new'             => __( 'Add New', 'Zimmerman' ),
    'edit_item'           => __( 'Edit Partner', 'Zimmerman' ),
    'update_item'         => __( 'Update Partner', 'Zimmerman' ),
    'search_items'        => __( 'Search Partner', 'Zimmerman' ),
    'not_found'           => __( 'Not found', 'Zimmerman' ),
    'not_found_in_trash'  => __( 'Not found in Trash', 'Zimmerman' ),
  );
  $args = array(
    'label'               => __( 'partners', 'Zimmerman' ),
    'description'         => __( 'Partner page post type', 'Zimmerman' ),
    'labels'              => $labels,
    'supports'            => array( ),
    'hierarchical'        => false,
    'public'              => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_nav_menus'   => true,
    'show_in_admin_bar'   => true,
    'menu_position'       => 5,
    'menu_icon'           => '',
    'can_export'          => true,
    'has_archive'         => false,
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'capability_type'     => 'page',
  );
  register_post_type( 'partner', $args );

}

// Hook into the 'init' action
add_action( 'init', 'zz_network_map', 0 );



// Register Custom Taxonomy
function zz_partner_countries() {

  $labels = array(
    'name'                       => _x( 'Countries', 'Taxonomy General Name', 'Zimmerman' ),
    'singular_name'              => _x( 'Country', 'Taxonomy Singular Name', 'Zimmerman' ),
    'menu_name'                  => __( 'Countries', 'Zimmerman' ),
    'all_items'                  => __( 'All Countries', 'Zimmerman' ),
    'parent_item'                => __( 'Parent Country', 'Zimmerman' ),
    'parent_item_colon'          => __( 'Parent Country:', 'Zimmerman' ),
    'new_item_name'              => __( 'New Country Name', 'Zimmerman' ),
    'add_new_item'               => __( 'Add New Country', 'Zimmerman' ),
    'edit_item'                  => __( 'Edit Country', 'Zimmerman' ),
    'update_item'                => __( 'Update Country', 'Zimmerman' ),
    'separate_items_with_commas' => __( 'Separate countries with commas', 'Zimmerman' ),
    'search_items'               => __( 'Search Countries', 'Zimmerman' ),
    'add_or_remove_items'        => __( 'Add or remove countries', 'Zimmerman' ),
    'choose_from_most_used'      => __( 'Choose from the most used countries', 'Zimmerman' ),
    'not_found'                  => __( 'Not Found', 'Zimmerman' ),
  );
  $args = array(
    'labels'                     => $labels,
    'hierarchical'               => true,
    'public'                     => true,
    'show_ui'                    => true,
    'show_admin_column'          => true,
    'show_in_nav_menus'          => true,
    'show_tagcloud'              => true,
  );
  register_taxonomy( 'partner_countries', array( 'partner' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'zz_partner_countries', 0 );

// Register Custom Taxonomy
function zz_partner_activities() {

  $labels = array(
    'name'                       => _x( 'Activities', 'Taxonomy General Name', 'Zimmerman' ),
    'singular_name'              => _x( 'Activity', 'Taxonomy Singular Name', 'Zimmerman' ),
    'menu_name'                  => __( 'Activities', 'Zimmerman' ),
    'all_items'                  => __( 'All Activities', 'Zimmerman' ),
    'parent_item'                => __( 'Parent Activity', 'Zimmerman' ),
    'parent_item_colon'          => __( 'Parent Activity:', 'Zimmerman' ),
    'new_item_name'              => __( 'New Activity Name', 'Zimmerman' ),
    'add_new_item'               => __( 'Add New Activity', 'Zimmerman' ),
    'edit_item'                  => __( 'Edit Activity', 'Zimmerman' ),
    'update_item'                => __( 'Update Activity', 'Zimmerman' ),
    'separate_items_with_commas' => __( 'Separate activities with commas', 'Zimmerman' ),
    'search_items'               => __( 'Search Activities', 'Zimmerman' ),
    'add_or_remove_items'        => __( 'Add or remove activities', 'Zimmerman' ),
    'choose_from_most_used'      => __( 'Choose from the most used activities', 'Zimmerman' ),
    'not_found'                  => __( 'Not Found', 'Zimmerman' ),
  );
  $args = array(
    'labels'                     => $labels,
    'hierarchical'               => true,
    'public'                     => true,
    'show_ui'                    => true,
    'show_admin_column'          => true,
    'show_in_nav_menus'          => true,
    'show_tagcloud'              => true,
  );
  register_taxonomy( 'partner_activities', array( 'partner' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'zz_partner_activities', 0 );

// Register Custom Taxonomy
function zz_partner_themes() {

  $labels = array(
    'name'                       => _x( 'Themes', 'Taxonomy General Name', 'Zimmerman' ),
    'singular_name'              => _x( 'Theme', 'Taxonomy Singular Name', 'Zimmerman' ),
    'menu_name'                  => __( 'Themes', 'Zimmerman' ),
    'all_items'                  => __( 'All Themes', 'Zimmerman' ),
    'parent_item'                => __( 'Parent Theme', 'Zimmerman' ),
    'parent_item_colon'          => __( 'Parent Theme:', 'Zimmerman' ),
    'new_item_name'              => __( 'New Theme Name', 'Zimmerman' ),
    'add_new_item'               => __( 'Add New Theme', 'Zimmerman' ),
    'edit_item'                  => __( 'Edit Theme', 'Zimmerman' ),
    'update_item'                => __( 'Update Theme', 'Zimmerman' ),
    'separate_items_with_commas' => __( 'Separate themes with commas', 'Zimmerman' ),
    'search_items'               => __( 'Search Themes', 'Zimmerman' ),
    'add_or_remove_items'        => __( 'Add or remove themes', 'Zimmerman' ),
    'choose_from_most_used'      => __( 'Choose from the most used themes', 'Zimmerman' ),
    'not_found'                  => __( 'Not Found', 'Zimmerman' ),
  );
  $args = array(
    'labels'                     => $labels,
    'hierarchical'               => true,
    'public'                     => true,
    'show_ui'                    => true,
    'show_admin_column'          => true,
    'show_in_nav_menus'          => true,
    'show_tagcloud'              => true,
  );
  register_taxonomy( 'partner_themes', array( 'partner' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'zz_partner_themes', 0 );

// Register Custom Taxonomy
function zz_partner_geo_focus_region() {

  $labels = array(
    'name'                       => _x( 'Geo focus regions', 'Taxonomy General Name', 'Zimmerman' ),
    'singular_name'              => _x( 'Geo focus region', 'Taxonomy Singular Name', 'Zimmerman' ),
    'menu_name'                  => __( 'Geo focus regions', 'Zimmerman' ),
    'all_items'                  => __( 'All Geo focus regions', 'Zimmerman' ),
    'parent_item'                => __( 'Parent Geo focus region', 'Zimmerman' ),
    'parent_item_colon'          => __( 'Parent Geo focus region:', 'Zimmerman' ),
    'new_item_name'              => __( 'New Geo focus region Name', 'Zimmerman' ),
    'add_new_item'               => __( 'Add New Geo focus region', 'Zimmerman' ),
    'edit_item'                  => __( 'Edit Geo focus region', 'Zimmerman' ),
    'update_item'                => __( 'Update Geo focus region', 'Zimmerman' ),
    'separate_items_with_commas' => __( 'Separate geo focus regions with commas', 'Zimmerman' ),
    'search_items'               => __( 'Search Geo focus regions', 'Zimmerman' ),
    'add_or_remove_items'        => __( 'Add or remove geo focus regions', 'Zimmerman' ),
    'choose_from_most_used'      => __( 'Choose from the most used geo focus regions', 'Zimmerman' ),
    'not_found'                  => __( 'Not Found', 'Zimmerman' ),
  );
  $args = array(
    'labels'                     => $labels,
    'hierarchical'               => true,
    'public'                     => true,
    'show_ui'                    => true,
    'show_admin_column'          => true,
    'show_in_nav_menus'          => true,
    'show_tagcloud'              => true,
  );
  register_taxonomy( 'partner_geo_focus_region', array( 'partner' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'zz_partner_geo_focus_region', 0 );



// Register Custom Taxonomy
function zz_partner_types() {

  $labels = array(
    'name'                       => _x( 'Types', 'Taxonomy General Name', 'Zimmerman' ),
    'singular_name'              => _x( 'Type', 'Taxonomy Singular Name', 'Zimmerman' ),
    'menu_name'                  => __( 'Types', 'Zimmerman' ),
    'all_items'                  => __( 'All Types', 'Zimmerman' ),
    'parent_item'                => __( 'Parent Type', 'Zimmerman' ),
    'parent_item_colon'          => __( 'Parent Type:', 'Zimmerman' ),
    'new_item_name'              => __( 'New Type Name', 'Zimmerman' ),
    'add_new_item'               => __( 'Add New Type', 'Zimmerman' ),
    'edit_item'                  => __( 'Edit Type', 'Zimmerman' ),
    'update_item'                => __( 'Update Type', 'Zimmerman' ),
    'separate_items_with_commas' => __( 'Separate types with commas', 'Zimmerman' ),
    'search_items'               => __( 'Search Types', 'Zimmerman' ),
    'add_or_remove_items'        => __( 'Add or remove types', 'Zimmerman' ),
    'choose_from_most_used'      => __( 'Choose from the most used types', 'Zimmerman' ),
    'not_found'                  => __( 'Not Found', 'Zimmerman' ),
  );
  $args = array(
    'labels'                     => $labels,
    'hierarchical'               => true,
    'public'                     => true,
    'show_ui'                    => true,
    'show_admin_column'          => true,
    'show_in_nav_menus'          => true,
    'show_tagcloud'              => true,
  );
  register_taxonomy( 'partner_types', array( 'partner' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'zz_partner_types', 0 );



// Register Custom Taxonomy
function zz_city_types() {

  $labels = array(
    'name'                       => _x( 'Cities', 'Taxonomy General Name', 'Zimmerman' ),
    'singular_name'              => _x( 'City', 'Taxonomy Singular Name', 'Zimmerman' ),
    'menu_name'                  => __( 'Cities', 'Zimmerman' ),
    'all_items'                  => __( 'All Cities', 'Zimmerman' ),
    'parent_item'                => __( 'Parent City', 'Zimmerman' ),
    'parent_item_colon'          => __( 'Parent City:', 'Zimmerman' ),
    'new_item_name'              => __( 'New City Name', 'Zimmerman' ),
    'add_new_item'               => __( 'Add New City', 'Zimmerman' ),
    'edit_item'                  => __( 'Edit City', 'Zimmerman' ),
    'update_item'                => __( 'Update City', 'Zimmerman' ),
    'separate_items_with_commas' => __( 'Separate cities with commas', 'Zimmerman' ),
    'search_items'               => __( 'Search Cities', 'Zimmerman' ),
    'add_or_remove_items'        => __( 'Add or remove cities', 'Zimmerman' ),
    'choose_from_most_used'      => __( 'Choose from the most used cities', 'Zimmerman' ),
    'not_found'                  => __( 'Not Found', 'Zimmerman' ),
  );
  $args = array(
    'labels'                     => $labels,
    'hierarchical'               => true,
    'public'                     => true,
    'show_ui'                    => true,
    'show_admin_column'          => true,
    'show_in_nav_menus'          => true,
    'show_tagcloud'              => true,
  );
  register_taxonomy( 'partner_cities', array( 'partner' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'zz_city_types', 0 );











/* Define the custom box */
add_action( 'add_meta_boxes', 'zz_partner_add_custom_box' );

/* Do something with the data entered */
add_action( 'save_post', 'zz_partner_save_postdata' );

/* Adds a box to the main column on the Post and Page edit screens */
function zz_partner_add_custom_box() {
  add_meta_box( 'partner_latitude', 'Latitude', 'partner_latitude_meta_box', 'partner', 'normal', 'high');
  add_meta_box( 'partner_longitude', 'Longitude', 'partner_longitude_meta_box', 'partner', 'normal', 'high');
  add_meta_box( 'partner_website', 'Website', 'partner_website_meta_box', 'partner', 'normal', 'high');
  add_meta_box( 'partner_email', 'Email', 'partner_email_meta_box', 'partner', 'normal', 'high');
  add_meta_box( 'partner_head_office', 'Head Office', 'partner_head_office_box', 'partner', 'normal', 'high');
  add_meta_box( 'partner_head_office_iso2', 'Head Office ISO2', 'partner_head_office_iso2_box', 'partner', 'normal', 'high');
  add_meta_box( 'partner_geo_focus_country', 'Geo Focus Countries', 'partner_geo_focus_country_box', 'partner', 'normal', 'high');
  
}

function partner_latitude_meta_box( $post ) {

  $field_value = get_post_meta( $post->ID, 'partner_latitude', false );
  if (!$field_value){ $field_value = array(""); }
  ?>
  <input type="text" value="<?php echo $field_value[0]; ?>" name="partner_latitude" id="partner_latitude" />  
  <?php
}

function partner_longitude_meta_box( $post ) {

  $field_value = get_post_meta( $post->ID, 'partner_longitude', false );
  if (!$field_value){ $field_value = array(""); }
  ?>
  <input type="text" value="<?php echo $field_value[0]; ?>" name="partner_longitude" id="partner_longitude" />  
  <?php
}


function partner_website_meta_box( $post ) {

  $field_value = get_post_meta( $post->ID, 'partner_website', false );
  if (!$field_value){ $field_value = array(""); }
  ?>
  <input type="text" value="<?php echo $field_value[0]; ?>" name="partner_website" id="partner_website" />  
  <?php
}

function partner_email_meta_box( $post ) {

  $field_value = get_post_meta( $post->ID, 'partner_email', false );
  if (!$field_value){ $field_value = array(""); }
  ?>
  <input type="text" value="<?php echo $field_value[0]; ?>" name="partner_email" id="partner_email" />  
  <?php
}

function partner_head_office_box( $post ) {

  $field_value = get_post_meta( $post->ID, 'partner_head_office', false );
  if (!$field_value){ $field_value = array(""); }
  ?>
  <input type="text" value="<?php echo $field_value[0]; ?>" name="partner_head_office" id="partner_head_office" />  
  <?php
}

function partner_head_office_iso2_box( $post ) {

  $field_value = get_post_meta( $post->ID, 'partner_head_office_iso2', false );
  if (!$field_value){ $field_value = array(""); }
  ?>
  <input type="text" value="<?php echo $field_value[0]; ?>" name="partner_head_office_iso2" id="partner_head_office_iso2" />  
  <?php
}

function partner_geo_focus_country_box( $post ) {

  $field_value = get_post_meta( $post->ID, 'partner_geo_focus_country', false );
  if (!$field_value){ $field_value = array(""); }
  ?>
  <input type="text" value="<?php echo $field_value[0]; ?>" name="partner_geo_focus_country" id="partner_geo_focus_country" />  
  <?php
}




// /* Prints the box content */
// function service_column2_meta_box( $post ) {

//   // Use nonce for verification
//   wp_nonce_field( plugin_basename( __FILE__ ), 'rain_noncename' );

//   $field_value = get_post_meta( $post->ID, 'service_column2', false );
//   wp_editor( $field_value[0], 'service_column2' );
// }






/* When the post is saved, saves our custom data */
function zz_partner_save_postdata( $post_id ) {

  // verify if this is an auto save routine. 
  // If it is our form has not been submitted, so we dont want to do anything
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return;

  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times
  if ( ( isset ( $_POST['zz_partner_noncename'] ) ) && ( ! wp_verify_nonce( $_POST['zz_partner_noncename'], plugin_basename( __FILE__ ) ) ) )
      return;

  // Check permissions
  if ( ( isset ( $_POST['post_type'] ) ) && ( 'page' == $_POST['post_type'] )  ) {
    if ( ! current_user_can( 'edit_page', $post_id ) ) {
      return;
    }    
  }
  else {
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
      return;
    }
  }

  // OK, we're authenticated: we need to find and save the data
  if ( isset ( $_POST['partner_latitude'] ) ) {
    update_post_meta( $post_id, 'partner_latitude', $_POST['partner_latitude'] );
  }

  if ( isset ( $_POST['partner_longitude'] ) ) {
    update_post_meta( $post_id, 'partner_longitude', $_POST['partner_longitude'] );
  }

  if ( isset ( $_POST['partner_website'] ) ) {
    update_post_meta( $post_id, 'partner_website', $_POST['partner_website'] );
  }

  if ( isset ( $_POST['partner_email'] ) ) {
    update_post_meta( $post_id, 'partner_email', $_POST['partner_email'] );
  }
  if ( isset ( $_POST['partner_geo_focus_country'] ) ) {
    update_post_meta( $post_id, 'partner_geo_focus_country', $_POST['partner_geo_focus_country'] );
  }
  if ( isset ( $_POST['partner_head_office'] ) ) {
    update_post_meta( $post_id, 'partner_head_office', $_POST['partner_head_office'] );
  }
  
  if ( isset ( $_POST['partner_head_office_iso2'] ) ) {
    update_post_meta( $post_id, 'partner_head_office_iso2', $_POST['partner_head_office_iso2'] );
  }

}




add_filter('is_protected_meta', 'zz_partners_protected_meta_filter', 10, 2);

function zz_partners_protected_meta_filter($protected, $meta_key) {

  $protected_meta_fields = array(
    "partner_latitude_meta_box",
    "partner_longitude_meta_box",
    "partner_website_meta_box",
    "partner_email_meta_box",
    "partner_geo_focus_country_box",
    "partner_head_office_box",  
    "partner_head_office_iso2_box",  
  );
 
  if (in_array($meta_key, $protected_meta_fields)){

    return true;
  } else {
    return false;
  }
}

include(plugin_dir_path( __FILE__ ) . 'partners-settings.php');





















class PageTemplater {

    /**
         * A Unique Identifier
         */
     protected $plugin_slug;

        /**
         * A reference to an instance of this class.
         */
        private static $instance;

        /**
         * The array of templates that this plugin tracks.
         */
        protected $templates;


        /**
         * Returns an instance of this class. 
         */
        public static function get_instance() {

                if( null == self::$instance ) {
                        self::$instance = new PageTemplater();
                } 

                return self::$instance;

        } 

        /**
         * Initializes the plugin by setting filters and administration functions.
         */
        private function __construct() {

                $this->templates = array();


                // Add a filter to the attributes metabox to inject template into the cache.
                add_filter(
          'page_attributes_dropdown_pages_args',
           array( $this, 'register_project_templates' ) 
        );


                // Add a filter to the save post to inject out template into the page cache
                add_filter(
          'wp_insert_post_data', 
          array( $this, 'register_project_templates' ) 
        );


                // Add a filter to the template include to determine if the page has our 
        // template assigned and return it's path
                add_filter(
          'template_include', 
          array( $this, 'view_project_template') 
        );


                // Add your templates to this array.
                $this->templates = array(
                        'partners-template.php'     => 'Partner map template',
                );
        
        } 


        /**
         * Adds our template to the pages cache in order to trick WordPress
         * into thinking the template file exists where it doens't really exist.
         *
         */

        public function register_project_templates( $atts ) {

                // Create the key used for the themes cache
                $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

                // Retrieve the cache list. 
        // If it doesn't exist, or it's empty prepare an array
                $templates = wp_get_theme()->get_page_templates();
                if ( empty( $templates ) ) {
                        $templates = array();
                } 

                // New cache, therefore remove the old one
                wp_cache_delete( $cache_key , 'themes');

                // Now add our template to the list of templates by merging our templates
                // with the existing templates array from the cache.
                $templates = array_merge( $templates, $this->templates );

                // Add the modified cache to allow WordPress to pick it up for listing
                // available templates
                wp_cache_add( $cache_key, $templates, 'themes', 1800 );

                return $atts;

        } 

        /**
         * Checks if the template is assigned to the page
         */
        public function view_project_template( $template ) {

                global $post;

                if (!isset($this->templates[get_post_meta( 
          $post->ID, '_wp_page_template', true 
        )] ) ) {
          
                        return $template;
            
                } 

                $file = plugin_dir_path(__FILE__). get_post_meta( 
          $post->ID, '_wp_page_template', true 
        );
        
                // Just to be safe, we check if the file exist first
                if( file_exists( $file ) ) {
                        return $file;
                } 
        else { echo $file; }

                return $template;

        } 


} 

add_action( 'plugins_loaded', array( 'PageTemplater', 'get_instance' ) );

?>