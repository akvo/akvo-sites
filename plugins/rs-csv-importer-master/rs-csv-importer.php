<?php
/*
Plugin Name: Really Simple CSV Importer
Plugin URI: http://wordpress.org/plugins/really-simple-csv-importer/
Description: Import posts, categories, tags, custom fields from simple csv file.
Author: Takuro Hishikawa, wokamoto
Author URI: https://en.digitalcube.jp/
Text Domain: rs-csv-importer
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
Version: 0.6.2
*/

error_reporting(E_ERROR);

function wp_exist_post_by_title($title_str) {
global $wpdb;
return $wpdb->get_row("SELECT * FROM wp_posts WHERE post_title = '" . $title_str . "'", 'ARRAY_A');
}
if ( !defined('WP_LOAD_IMPORTERS') )
	return;

// Load Importer API
require_once ABSPATH . 'wp-admin/includes/import.php';

if ( !class_exists( 'WP_Importer' ) ) {
	$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
	if ( file_exists( $class_wp_importer ) )
		require_once $class_wp_importer;
}



// Load Helpers
require dirname( __FILE__ ) . '/rs-csv-helper.php';
require dirname( __FILE__ ) . '/wp_post_helper/class-wp_post_helper.php';

/**
 * CSV Importer
 *
 * @package WordPress
 * @subpackage Importer
 */
if ( class_exists( 'WP_Importer' ) ) {
class RS_CSV_Importer extends WP_Importer {
	
	/** Sheet columns
         * 
         * This class will be used to import csv files from Rain4Food.
         * 
         * Most important is the function process_post
         * 
	* @value array
	*/
	public $column_indexes = array();
	public $column_keys = array();
        
        
        

 	// User interface wrapper start
	function header() {
		echo '<div class="wrap">';
		screen_icon();
		echo '<h2>'.__('Import partner CSV', 'rs-csv-importer').'</h2>';
	}

	// User interface wrapper end
	function footer() {
		echo '</div>';
	}
	
	// Step 1
	function greet() {
		echo '<p>'.__( 'Choose a partner CSV (.csv) file to upload, then click Upload file and import.', 'rs-csv-importer' ).'</p>';
		
		echo '<p>'.__( 'Requirements:', 'rs-csv-importer' ).'</p>';
		echo '<ol>';
		echo '<li>'.__( 'Choose in Excel -->File --> Save as CSV', 'rs-csv-importer' ).'</li>';
		echo '<li>'.sprintf( __( 'You must use field delimiter as "%s"', 'rs-csv-importer'), RS_CSV_Helper::DELIMITER ).'</li>';
		
		echo '</ol>';
		echo '<p>'.__( 'Download example CSV files:', 'rs-csv-importer' );
		echo ' <a href="'.plugin_dir_url( __FILE__ ).'sample/partner_example_csv.csv">'.__( 'csv', 'rs-csv-importer' ).'</a>,';
		
		
		echo '</p>';
		wp_import_upload_form( add_query_arg('step', 1) );
	}

	// Step 2
	function import() {
		$file = wp_import_handle_upload();

		if ( isset( $file['error'] ) ) {
			echo '<p><strong>' . __( 'Sorry, there has been an error.', 'rs-csv-importer' ) . '</strong><br />';
			echo esc_html( $file['error'] ) . '</p>';
			return false;
		} else if ( ! file_exists( $file['file'] ) ) {
			echo '<p><strong>' . __( 'Sorry, there has been an error.', 'rs-csv-importer' ) . '</strong><br />';
			printf( __( 'The export file could not be found at <code>%s</code>. It is likely that this was caused by a permissions problem.', 'rs-csv-importer' ), esc_html( $file['file'] ) );
			echo '</p>';
			return false;
		}
		
		$this->id = (int) $file['id'];
		$this->file = get_attached_file($this->id);
		$result = $this->process_posts();
		if ( is_wp_error( $result ) )
			return $result;
	}
	
	/**
	* Insert post and postmeta using wp_post_helper.
	*
	* More information: https://gist.github.com/4084471
	*
	* @param array $post
	* @param array $meta
	* @param array $terms
	* @param string $thumbnail The uri or path of thumbnail image.
	* @param bool $is_update
	* @return int|false Saved post id. If failed, return false.
	*/
	public function save_post($post,$meta,$terms,$thumbnail,$is_update) {
		$ph = new wp_post_helper($post);
		
		foreach ($meta as $key => $value) {
			$is_cfs = 0;
			$is_acf = 0;
			$cfs_prefix = 'cfs_';
			if (strpos($key, $cfs_prefix) === 0) {
				$ph->add_cfs_field( substr($key, strlen($cfs_prefix)), $value );
				$is_cfs = 1;
			} else {
				if (function_exists('get_field_object')) {
					if (strpos($key, 'field_') === 0) {
						$fobj = get_field_object($key);
						if (is_array($fobj) && isset($fobj['key']) && $fobj['key'] == $key) {
							$ph->add_field($key,$value);
							$is_acf = 1;
						}
					}
				}
			}
			if (!$is_acf && !$is_cfs)
				$ph->add_meta($key,$value,true);
		}

		foreach ($terms as $key => $value) {
			$ph->add_terms($key, $value);
		}
		
		if ($thumbnail) $ph->add_media($thumbnail,'','','',true);
		
		if ($is_update)
			$result = $ph->update();
		else
			$result = $ph->insert();
		
		unset($ph);
		
		return $result;
	}

	// process Whole Network in the world_Andes from Rain.csv
	function process_posts() {
		$h = new RS_CSV_Helper;

		$handle = $h->fopen($this->file, 'r');
		if ( $handle == false ) {
			echo '<p><strong>'.__( 'Failed to open file.', 'rs-csv-importer' ).'</strong></p>';
			wp_import_cleanup($this->id);
			return false;
		}
		
		$is_first = true;
                
                echo '<h3>Importeer Log</h3>';
		
		echo '<ol>';
                $data = $h->fgetcsv($handle);

                //The CSV file exists of 2 different type of headers.
                //Example: Activities is the main category
                //         Exists out of Private Financing, NGO etc.
                //         this->parse_columns will be used to the existing elements
                //The main filters are stored with the parse_header_columns function
                $h->parse_header_columns($this, $data);
                
                if (count($h->top_header_array)>100){
                    echo '<p><strong>This file has the wrong format. This program does not see any next lines</strong></p>';
                }
		while (($data = $h->fgetcsv($handle)) !== FALSE) {
                        //if it is the first record, than we will save the main taxonomies
			if ($is_first) {


                                //here we parse the second row of header information. These are the concrete elements of the main categories. 
                                //check also example above
				$h->parse_columns( $this, $data );
				$is_first = false;
                                
                                //Type value == 'Type' --> Organisation Type
                                foreach($data as $key => $value){
                                   if($h->top_header_array[$key]=='Type'){
                                       wp_insert_term(
                                            $value, // the term 
                                            'partner_types', // the taxonomy
                                            array(
                                              'description'=> '',
                                              'slug' => $h->slugify($value)
                                              
                                            )
                                       );
                                       
                                  //Type value == 'Activities' --> Activity     
                                   }  elseif ($h->top_header_array[$key] == 'Activities') {
                                       wp_insert_term(
                                            $value, // the term 
                                            'partner_activities', // the taxonomy
                                            array(
                                              'description'=> '',
                                              'slug' => $h->slugify($value)
                                              
                                            )
                                       );
                                   //Type value == 'Themes' --> Theme     
                                   } elseif ($h->top_header_array[$key] == 'Themes') {
                                       wp_insert_term(
                                            $value, // the term 
                                            'partner_themes', // the taxonomy
                                            array(
                                              'description'=> '',
                                              'slug' => $h->slugify($value)
                                              
                                            )
                                       );
                                   //Type value == 'Geo Graphical Focus' --> Geo focus
                                   } elseif($h->top_header_array[$key] == 'Geographical focus'){
                                       wp_insert_term(
                                            $value, // the term 
                                            'partner_geo_focus_region', // the taxonomy
                                            array(
                                              'description'=> '',
                                              'slug' => $h->slugify($value)
                                              
                                            )
                                       );
                                }
                                
                                   }
			} else {
                                //here we will save the instance variables. Like the partner data with the correct fields and related taxonomies
                                
                                //check if a partner post exists, otherwise we will update the item

								$curpost = wp_exist_post_by_title(esc_sql($data[0]));
                                if(!empty($curpost)){
                                    $post_id = $curpost['ID'];
                                }else{
                                   $post_id = wp_insert_post(array (
                                    'post_type' => 'partner',
                                    'post_title' => $data[0],
                                    'post_content' => $data[0],
                                    'post_status' => 'publish',
                                    'comment_status' => 'closed',   // if you prefer
                                    'ping_status' => 'closed',      // if you prefer
                                )); 
                                }
                                echo '<li>Partner title: '.$data[0];
                                //loop through the data fields and relate the correct taxonomie
                                foreach($data as $key=>$value){
                                    if ($value == 'x'){
                                        //get the term_id from the second top header
                                        $term_id = $h->slugify($h->top_second_header_array[$key]);
                                        //save taxonomies related to the type header
                                        if($h->top_header_array[$key]=='Type'){
                                             wp_set_object_terms($post_id, $term_id, 'partner_types', true);
                                             echo  ', type: '.$term_id.', ';
                                        //save taxonomies related to the type activities
                                        }elseif ($h->top_header_array[$key] == 'Activities') {
                                            wp_set_object_terms($post_id, $term_id, 'partner_activities', true);
                                            echo  ', activities: '.$term_id.', ';
                                       //save taxonomies related to Themes     
                                        }elseif ($h->top_header_array[$key] == 'Themes'){
                                            wp_set_object_terms($post_id, $term_id, 'partner_themes', true);
                                            echo  ', themes: '.$term_id.', ';
                                        }
                                        //save taxonomies related to the Geographical Focus
                                        elseif($h->top_header_array[$key] == 'Geographical focus'){
                                            wp_set_object_terms($post_id, $term_id, 'partner_geo_focus_region', true);
                                            echo  ', geo focus: '.$term_id.', ';
                                        }
                                        
                                    }
                                    //save field data for Head Office
                                    if($h->top_second_header_array[$key] == 'Head Office'){
                                        add_metadata('post', $post_id, 'partner_head_office', $value);
                                    }
                                    //save field data for Countries
                                    if($h->top_second_header_array[$key] == 'Countries'){
                                        add_metadata('post', $post_id, 'partner_geo_focus_country', $value);
                                    }
                                    
                                    //save field data for the Web Address
                                    if($h->top_second_header_array[$key] == 'Web Address'){
                                        add_metadata('post', $post_id, 'partner_website', $value);
                                    }
                                    
                                    //save field data ISO2 of the head office
                                    if($h->top_second_header_array[$key] == 'ISO2 Head Office'){
                                        add_metadata('post', $post_id, 'partner_head_office_iso2', $value);
                                        if(isset($value)){
                                            
                                            //here we decode the iso table file for the long- and latitude files
                                            $json = json_decode($this->json_iso2, true);
                                            add_metadata('post', $post_id, 'partner_longitude', $json[$value]['longitude']);
                                            add_metadata('post', $post_id, 'partner_latitude', $json[$value]['latitude']);
                                            
                                            
                                            $json_country_names = json_decode($this->json_iso2_names, true);
                                            
                                            //here we get the country name for the ISO2 code and relate the correct country to the partner
                                            foreach($json_country_names as $k=>$v){
                                                if($v['iso'] == $value){
                                                    wp_insert_term(
                                                        $v['name'], // the term 
                                                        'partner_countries', // the taxonomy
                                                        array(
                                                                'description'=> '',
                                                                'slug' => $h->slugify($v['name'])
                                              
                                                    ));
                                                    wp_set_object_terms($post_id, $v['name'], 'partner_countries', true);
                                                }
                                            }
                                            
//                                                                               die();

                                        }
                                    }
                                    

                                }
                                echo '</li>';
                                
			}
		}
		
		echo '</ol>';

		$h->fclose($handle);
		
		wp_import_cleanup($this->id);
		
		echo '<h3>'.__('All Done.', 'rs-csv-importer').'</h3>';
	}

	// dispatcher
	function dispatch() {
		$this->header();
		
		if (empty ($_GET['step']))
			$step = 0;
		else
			$step = (int) $_GET['step'];

		switch ($step) {
			case 0 :
				$this->greet();
				break;
			case 1 :
				check_admin_referer('import-upload');
				set_time_limit(0);
				$result = $this->import();
				if ( is_wp_error( $result ) )
					echo $result->get_error_message();
				break;
		}
		
		$this->footer();
	}
        
	public $json_iso2 = '{"AD": {"latitude": "42.30", "longitude": "1.30"},
    "AE": {"latitude": "24.00", "longitude": "54.00"},
    "AF": {"latitude": "33.00", "longitude": "65.00"},
    "AG": {"latitude": "17.03", "longitude": "-61.48"},
    "AI": {"latitude": "18.15", "longitude": "-63.10"},
    "AL": {"latitude": "41.00", "longitude": "20.00"},
    "AM": {"latitude": "40.00", "longitude": "45.00"},
    "AN": {"latitude": "12.15", "longitude": "-68.45"},
    "AO": {"latitude": "-12.30", "longitude": "18.30"},
    "AR": {"latitude": "-34.00", "longitude": "-64.00"},
    "AS": {"latitude": "-14.20", "longitude": "-170.00"},
    "AT": {"latitude": "47.20", "longitude": "13.20"},
    "AU": {"latitude": "-27.00", "longitude": "133.00"},
    "AX": {"latitude": "60.338", "longitude": "20.273"},
    "AW": {"latitude": "12.30", "longitude": "-69.58"},
    "AZ": {"latitude": "40.30", "longitude": "47.30"},
    "BA": {"latitude": "44.00", "longitude": "18.00"},
    "BB": {"latitude": "13.10", "longitude": "-59.32"},
    "BD": {"latitude": "24.00", "longitude": "90.00"},
    "BE": {"latitude": "50.50", "longitude": "4.00"},
    "BF": {"latitude": "13.00", "longitude": "-2.00"},
    "BG": {"latitude": "43.00", "longitude": "25.00"},
    "BH": {"latitude": "26.00", "longitude": "50.33"},
    "BI": {"latitude": "-3.30", "longitude": "30.00"},
    "BJ": {"latitude": "9.30", "longitude": "2.15"},
    "BL": {"latitude": "17.90", "longitude": "-62.85"},
    "BM": {"latitude": "32.20", "longitude": "-64.45"},
    "BN": {"latitude": "4.30", "longitude": "114.40"},
    "BO": {"latitude": "-17.00", "longitude": "-65.00"},
    "BR": {"latitude": "-10.00", "longitude": "-55.00"},
    "BS": {"latitude": "24.15", "longitude": "-76.00"},
    "BT": {"latitude": "27.30", "longitude": "90.30"},
    "BU": {"latitude": "22.00", "longitude": "98.00"},
    "BW": {"latitude": "-22.00", "longitude": "24.00"},
    "BY": {"latitude": "53.00", "longitude": "28.00"},
    "BZ": {"latitude": "17.15", "longitude": "-88.45"},
    "CA": {"latitude": "60.00", "longitude": "-95.00"},
    "CC": {"latitude": "-12.30", "longitude": "96.50"},
    "CD": {"latitude": "0.00", "longitude": "25.00"},
    "CF": {"latitude": "7.00", "longitude": "21.00"},
    "CG": {"latitude": "-1.00", "longitude": "15.00"},
    "CH": {"latitude": "47.00", "longitude": "8.00"},
    "CI": {"latitude": "8.00", "longitude": "-5.00"},
    "CK": {"latitude": "-21.14", "longitude": "-159.46"},
    "CL": {"latitude": "-30.00", "longitude": "-71.00"},
    "CM": {"latitude": "6.00", "longitude": "12.00"},
    "CN": {"latitude": "35.00", "longitude": "105.00"},
    "CO": {"latitude": "4.00", "longitude": "-72.00"},
    "CR": {"latitude": "10.00", "longitude": "-84.00"},
    "CU": {"latitude": "21.30", "longitude": "-80.00"},
    "CV": {"latitude": "16.00", "longitude": "-24.00"},
    "CX": {"latitude": "-10.30", "longitude": "105.40"},
    "CY": {"latitude": "35.00", "longitude": "33.00"},
    "CZ": {"latitude": "49.45", "longitude": "15.30"},
    "DE": {"latitude": "51.00", "longitude": "9.00"},
    "DJ": {"latitude": "11.30", "longitude": "43.00"},
    "DK": {"latitude": "56.00", "longitude": "10.00"},
    "DM": {"latitude": "15.25", "longitude": "-61.20"},
    "DO": {"latitude": "19.00", "longitude": "-70.40"},
    "DZ": {"latitude": "28.00", "longitude": "3.00"},
    "EC": {"latitude": "-2.00", "longitude": "-77.30"},
    "EG": {"latitude": "27.00", "longitude": "30.00"},
    "EH": {"latitude": "24.30", "longitude": "-13.00"},
    "ER": {"latitude": "15.00", "longitude": "39.00"},
    "ES": {"latitude": "40.00", "longitude": "-4.00"},
    "ET": {"latitude": "8.00", "longitude": "38.00"},
    "EW": {"latitude": "59.00", "longitude": "26.00"},
    "FI": {"latitude": "64.00", "longitude": "26.00"},
    "FJ": {"latitude": "-18.00", "longitude": "175.00"},
    "FK": {"latitude": "-51.45", "longitude": "-59.00"},
    "FM": {"latitude": "6.55", "longitude": "158.15"},
    "FO": {"latitude": "62.00", "longitude": "-7.00"},
    "FR": {"latitude": "47.06", "longitude": "2.17"},
    "GA": {"latitude": "-1.00", "longitude": "11.45"},
    "GB": {"latitude": "54.00", "longitude": "-2.00"},
    "GD": {"latitude": "12.07", "longitude": "-61.40"},
    "GE": {"latitude": "42.00", "longitude": "43.30"},
    "GF": {"latitude": "4.00", "longitude": "-53.00"},
    "GG": {"latitude": "49.28", "longitude": "-2.35"},
    "GH": {"latitude": "8.00", "longitude": "-2.00"},
    "GI": {"latitude": "36.08", "longitude": "-5.21"},
    "GL": {"latitude": "72.00", "longitude": "-40.00"},
    "GM": {"latitude": "13.28", "longitude": "-16.34"},
    "GN": {"latitude": "11.00", "longitude": "-10.00"},
    "GP": {"latitude": "16.15", "longitude": "-61.35"},
    "GQ": {"latitude": "2.00", "longitude": "10.00"},
    "GR": {"latitude": "39.00", "longitude": "22.00"},
    "GS": {"latitude": "-54.30", "longitude": "-37.00"},
    "GT": {"latitude": "15.30", "longitude": "-90.15"},
    "GU": {"latitude": "13.28", "longitude": "144.47"},
    "GW": {"latitude": "12.00", "longitude": "-15.00"},
    "GY": {"latitude": "5.00", "longitude": "-59.00"},
    "HK": {"latitude": "22.15", "longitude": "114.10"},
    "HM": {"latitude": "-53.06", "longitude": "72.31"},
    "HN": {"latitude": "15.00", "longitude": "-86.30"},
    "HR": {"latitude": "45.10", "longitude": "15.30"},
    "HT": {"latitude": "19.00", "longitude": "-72.25"},
    "HU": {"latitude": "47.00", "longitude": "20.00"},
    "ID": {"latitude": "-5.00", "longitude": "120.00"},
    "IE": {"latitude": "53.00", "longitude": "-8.00"},
    "IL": {"latitude": "31.30", "longitude": "34.45"},
    "IM": {"latitude": "54.15", "longitude": "-4.30"},
    "IN": {"latitude": "20.00", "longitude": "77.00"},
    "IQ": {"latitude": "33.00", "longitude": "44.00"},
    "IR": {"latitude": "32.00", "longitude": "53.00"},
    "IS": {"latitude": "65.00", "longitude": "-18.00"},
    "IT": {"latitude": "42.50", "longitude": "12.50"},
    "JE": {"latitude": "49.15", "longitude": "-2.10"},
    "JM": {"latitude": "18.15", "longitude": "-77.30"},
    "JO": {"latitude": "31.00", "longitude": "36.00"},
    "JP": {"latitude": "36.00", "longitude": "138.00"},
    "KA": {"latitude": "48.00", "longitude": "68.00"},
    "KE": {"latitude": "1.00", "longitude": "38.00"},
    "KG": {"latitude": "41.00", "longitude": "75.00"},
    "KH": {"latitude": "13.00", "longitude": "105.00"},
    "KI": {"latitude": "1.25", "longitude": "173.00"},
    "KM": {"latitude": "-12.10", "longitude": "44.15"},
    "KN": {"latitude": "17.20", "longitude": "-62.45"},
    "KP": {"latitude": "40.00", "longitude": "127.00"},
    "KR": {"latitude": "37.00", "longitude": "127.30"},
    "KW": {"latitude": "29.30", "longitude": "45.45"},
    "KY": {"latitude": "19.30", "longitude": "-80.30"},
    "LA": {"latitude": "18.00", "longitude": "105.00"},
    "LB": {"latitude": "33.50", "longitude": "35.50"},
    "LC": {"latitude": "13.53", "longitude": "-60.58"},
    "LI": {"latitude": "47.16", "longitude": "9.32"},
    "LK": {"latitude": "7.00", "longitude": "81.00"},
    "LR": {"latitude": "6.30", "longitude": "-9.30"},
    "LS": {"latitude": "-29.30", "longitude": "28.30"},
    "LT": {"latitude": "56.00", "longitude": "24.00"},
    "LU": {"latitude": "49.45", "longitude": "6.10"},
    "LV": {"latitude": "57.00", "longitude": "25.00"},
    "LY": {"latitude": "25.00", "longitude": "17.00"},
    "MA": {"latitude": "32.00", "longitude": "-5.00"},
    "MC": {"latitude": "43.44", "longitude": "7.24"},
    "MD": {"latitude": "47.00", "longitude": "29.00"},
    "ME": {"latitude": "42.30", "longitude": "19.18"},
    "MG": {"latitude": "-20.00", "longitude": "47.00"},
    "MH": {"latitude": "9.00", "longitude": "168.00"},
    "MK": {"latitude": "41.50", "longitude": "22.00"},
    "ML": {"latitude": "17.00", "longitude": "-4.00"},
    "MN": {"latitude": "46.00", "longitude": "105.00"},
    "MO": {"latitude": "22.10", "longitude": "113.33"},
    "MP": {"latitude": "15.12", "longitude": "145.45"},
    "MQ": {"latitude": "14.40", "longitude": "-61.00"},
    "MR": {"latitude": "20.00", "longitude": "-12.00"},
    "MS": {"latitude": "16.45", "longitude": "-62.12"},
    "MT": {"latitude": "35.50", "longitude": "14.35"},
    "MU": {"latitude": "-20.17", "longitude": "57.33"},
    "MV": {"latitude": "3.15", "longitude": "73.00"},
    "MW": {"latitude": "-13.30", "longitude": "34.00"},
    "MX": {"latitude": "23.00", "longitude": "-102.00"},
    "MY": {"latitude": "2.30", "longitude": "112.30"},
    "MZ": {"latitude": "-18.15", "longitude": "35.00"},
    "NA": {"latitude": "-22.00", "longitude": "17.00"},
    "NC": {"latitude": "-21.30", "longitude": "165.30"},
    "NE": {"latitude": "16.00", "longitude": "8.00"},
    "NF": {"latitude": "-29.02", "longitude": "167.57"},
    "NG": {"latitude": "10.00", "longitude": "8.00"},
    "NI": {"latitude": "13.00", "longitude": "-85.00"},
    "NL": {"latitude": "52.30", "longitude": "5.45"},
    "NO": {"latitude": "62.00", "longitude": "10.00"},
    "NP": {"latitude": "28.00", "longitude": "84.00"},
    "NR": {"latitude": "-0.32", "longitude": "166.55"},
    "NU": {"latitude": "-19.02", "longitude": "-169.52"},
    "NZ": {"latitude": "-41.00", "longitude": "174.00"},
    "OM": {"latitude": "21.00", "longitude": "57.00"},
    "PA": {"latitude": "9.00", "longitude": "-80.00"},
    "PE": {"latitude": "-10.00", "longitude": "-76.00"},
    "PF": {"latitude": "-15.00", "longitude": "-140.00"},
    "PG": {"latitude": "-6.00", "longitude": "147.00"},
    "PH": {"latitude": "13.00", "longitude": "122.00"},
    "PK": {"latitude": "30.00", "longitude": "70.00"},
    "PL": {"latitude": "52.00", "longitude": "20.00"},
    "PM": {"latitude": "46.50", "longitude": "-56.20"},
    "PN": {"latitude": "-25.04", "longitude": "-130.06"},
    "PR": {"latitude": "18.15", "longitude": "-66.30"},
    "PS": {"latitude": "32.00", "longitude": "35.15"},
    "PT": {"latitude": "39.30", "longitude": "-8.00"},
    "PW": {"latitude": "7.30", "longitude": "134.30"},
    "PY": {"latitude": "-23.00", "longitude": "-58.00"},
    "QA": {"latitude": "25.30", "longitude": "51.15"},
    "RE": {"latitude": "-21.06", "longitude": "55.36"},
    "RO": {"latitude": "46.00", "longitude": "25.00"},
    "RS": {"latitude": "44.00", "longitude": "21.00"},
    "RU": {"latitude": "60.00", "longitude": "100.00"},
    "RW": {"latitude": "-2.00", "longitude": "30.00"},
    "SA": {"latitude": "25.00", "longitude": "45.00"},
    "SB": {"latitude": "-8.00", "longitude": "159.00"},
    "SC": {"latitude": "-4.35", "longitude": "55.40"},
    "SD": {"latitude": "15.00", "longitude": "30.00"},
    "SE": {"latitude": "62.00", "longitude": "15.00"},
    "SG": {"latitude": "1.22", "longitude": "103.48"},
    "SH": {"latitude": "-15.56", "longitude": "-5.42"},
    "SI": {"latitude": "46.07", "longitude": "14.49"},
    "SJ": {"latitude": "71.00", "longitude": "-8.00"},
    "SK": {"latitude": "48.40", "longitude": "19.30"},
    "SL": {"latitude": "8.30", "longitude": "-11.30"},
    "SM": {"latitude": "43.46", "longitude": "12.25"},
    "SN": {"latitude": "14.00", "longitude": "-14.00"},
    "SO": {"latitude": "10.00", "longitude": "49.00"},
    "SR": {"latitude": "4.00", "longitude": "-56.00"},
    "ST": {"latitude": "1.00", "longitude": "7.00"},
    "SV": {"latitude": "13.50", "longitude": "-88.55"},
    "SX": {"latitude": "18.05", "longitude": "-63.57"},
    "SY": {"latitude": "35.00", "longitude": "38.00"},
    "SZ": {"latitude": "-26.30", "longitude": "31.30"},
    "TC": {"latitude": "21.45", "longitude": "-71.35"},
    "TD": {"latitude": "15.00", "longitude": "19.00"},
    "TG": {"latitude": "8.00", "longitude": "1.10"},
    "TH": {"latitude": "15.00", "longitude": "100.00"},
    "TJ": {"latitude": "39.00", "longitude": "71.00"},
    "TK": {"latitude": "-9.00", "longitude": "-172.00"},
    "TM": {"latitude": "40.00", "longitude": "60.00"},
    "TN": {"latitude": "34.00", "longitude": "9.00"},
    "TO": {"latitude": "-20.00", "longitude": "-175.00"},
    "TP": {"latitude": "-8.50", "longitude": "125.55"},
    "TR": {"latitude": "39.00", "longitude": "35.00"},
    "TT": {"latitude": "11.00", "longitude": "-61.00"},
    "TV": {"latitude": "-8.00", "longitude": "178.00"},
    "TW": {"latitude": "23.30", "longitude": "121.00"},
    "TZ": {"latitude": "-6.00", "longitude": "35.00"},
    "UA": {"latitude": "49.00", "longitude": "32.00"},
    "UG": {"latitude": "1.00", "longitude": "32.00"},
    "UM": {"latitude": "0.13", "longitude": "-176.28"},
    "US": {"latitude": "38.00", "longitude": "-97.00"},
    "UY": {"latitude": "-33.00", "longitude": "-56.00"},
    "UZ": {"latitude": "41.00", "longitude": "64.00"},
    "VA": {"latitude": "41.54", "longitude": "12.27"},
    "VC": {"latitude": "13.15", "longitude": "-61.12"},
    "VE": {"latitude": "8.00", "longitude": "-66.00"},
    "VG": {"latitude": "18.30", "longitude": "-64.30"},
    "VI": {"latitude": "18.20", "longitude": "-64.50"},
    "VN": {"latitude": "16.00", "longitude": "106.00"},
    "VU": {"latitude": "-16.00", "longitude": "167.00"},
    "WF": {"latitude": "-13.18", "longitude": "-176.12"},
    "WK": {"latitude": "19.17", "longitude": "166.39"},
    "WS": {"latitude": "-13.35", "longitude": "-172.20"},
    "YE": {"latitude": "15.00", "longitude": "48.00"},
    "YT": {"latitude": "-12.50", "longitude": "45.10"},
    "ZA": {"latitude": "-29.00", "longitude": "24.00"},
    "ZM": {"latitude": "-15.00", "longitude": "30.00"},
    "ZW": {"latitude": "-20.00", "longitude": "30.00"},
    "TL": {"latitude": "-8.711486", "longitude": "125.634765"},
    "AQ": {"latitude": "-82.862752", "longitude": "-135.000000"},
    "BQ": {"latitude": "17.636260", "longitude": "-63.234022"},
    "CW": {"latitude": "12.169570", "longitude": "-68.990020"},
    "EE": {"latitude": "58.595272", "longitude": "25.013607"},
    "IO": {"latitude": "-7.334756", "longitude": "72.424233"},
    "KZ": {"latitude": "48.019573", "longitude": "66.923684"},
    "MF": {"latitude": "18.082550", "longitude": "-63.052251"},
    "MM": {"latitude": "21.913965", "longitude": "95.956223"},
    "SS": {"latitude": "7.088628", "longitude": "30.629883"},
    "TF": {"latitude": "-49.280366", "longitude": "69.348557"},
    "XK": {"latitude": "42.602636", "longitude": "20.902977"}
}';
        
        public $json_iso2_names = '[
  {
    "iso":"AF",
    "name":"Afghanistan"
  },
  {
    "iso":"AL",
    "name":"Albania"
  },
  {
    "iso":"DZ",
    "name":"Algeria"
  },
  {
    "iso":"AS",
    "name":"American Samoa"
  },
  {
    "iso":"AD",
    "name":"Andorra"
  },
  {
    "iso":"AO",
    "name":"Angola"
  },
  {
    "iso":"AI",
    "name":"Anguilla"
  },
  {
    "iso":"AQ",
    "name":"Antarctica"
  },
  {
    "iso":"AG",
    "name":"Antigua and Barbuda"
  },
  {
    "iso":"AR",
    "name":"Argentina"
  },
  {
    "iso":"AM",
    "name":"Armenia"
  },
  {
    "iso":"AW",
    "name":"Aruba"
  },
  {
    "iso":"AU",
    "name":"Australia"
  },
  {
    "iso":"AT",
    "name":"Austria"
  },
  {
    "iso":"AZ",
    "name":"Azerbaijan"
  },
  {
    "iso":"BS",
    "name":"Bahamas"
  },
  {
    "iso":"BH",
    "name":"Bahrain"
  },
  {
    "iso":"BD",
    "name":"Bangladesh"
  },
  {
    "iso":"BB",
    "name":"Barbados"
  },
  {
    "iso":"BY",
    "name":"Belarus"
  },
  {
    "iso":"BE",
    "name":"Belgium"
  },
  {
    "iso":"BZ",
    "name":"Belize"
  },
  {
    "iso":"BJ",
    "name":"Benin"
  },
  {
    "iso":"BM",
    "name":"Bermuda"
  },
  {
    "iso":"BT",
    "name":"Bhutan"
  },
  {
    "iso":"BO",
    "name":"Bolivia"
  },
  {
    "iso":"BA",
    "name":"Bosnia and Herzegovina"
  },
  {
    "iso":"BW",
    "name":"Botswana"
  },
  {
    "iso":"BV",
    "name":"Bouvet Island"
  },
  {
    "iso":"BR",
    "name":"Brazil"
  },
  {
    "iso":"BQ",
    "name":"British Antarctic Territory"
  },
  {
    "iso":"IO",
    "name":"British Indian Ocean Territory"
  },
  {
    "iso":"VG",
    "name":"British Virgin Islands"
  },
  {
    "iso":"BN",
    "name":"Brunei"
  },
  {
    "iso":"BG",
    "name":"Bulgaria"
  },
  {
    "iso":"BF",
    "name":"Burkina Faso"
  },
  {
    "iso":"BI",
    "name":"Burundi"
  },
  {
    "iso":"KH",
    "name":"Cambodia"
  },
  {
    "iso":"CM",
    "name":"Cameroon"
  },
  {
    "iso":"CA",
    "name":"Canada"
  },
  {
    "iso":"CT",
    "name":"Canton and Enderbury Islands"
  },
  {
    "iso":"CV",
    "name":"Cape Verde"
  },
  {
    "iso":"KY",
    "name":"Cayman Islands"
  },
  {
    "iso":"CF",
    "name":"Central African Republic"
  },
  {
    "iso":"TD",
    "name":"Chad"
  },
  {
    "iso":"CL",
    "name":"Chile"
  },
  {
    "iso":"CN",
    "name":"China"
  },
  {
    "iso":"CX",
    "name":"Christmas Island"
  },
  {
    "iso":"CC",
    "name":"Cocos [Keeling] Islands"
  },
  {
    "iso":"CO",
    "name":"Colombia"
  },
  {
    "iso":"KM",
    "name":"Comoros"
  },
  {
    "iso":"CG",
    "name":"Congo - Brazzaville"
  },
  {
    "iso":"CD",
    "name":"Congo - Kinshasa"
  },
  {
    "iso":"CK",
    "name":"Cook Islands"
  },
  {
    "iso":"CR",
    "name":"Costa Rica"
  },
  {
    "iso":"HR",
    "name":"Croatia"
  },
  {
    "iso":"CU",
    "name":"Cuba"
  },
  {
    "iso":"CY",
    "name":"Cyprus"
  },
  {
    "iso":"CZ",
    "name":"Czech Republic"
  },
  {
    "iso":"CI",
    "name":"Côte d’Ivoire"
  },
  {
    "iso":"DK",
    "name":"Denmark"
  },
  {
    "iso":"DJ",
    "name":"Djibouti"
  },
  {
    "iso":"DM",
    "name":"Dominica"
  },
  {
    "iso":"DO",
    "name":"Dominican Republic"
  },
  {
    "iso":"NQ",
    "name":"Dronning Maud Land"
  },
  {
    "iso":"DD",
    "name":"East Germany"
  },
  {
    "iso":"EC",
    "name":"Ecuador"
  },
  {
    "iso":"EG",
    "name":"Egypt"
  },
  {
    "iso":"SV",
    "name":"El Salvador"
  },
  {
    "iso":"GQ",
    "name":"Equatorial Guinea"
  },
  {
    "iso":"ER",
    "name":"Eritrea"
  },
  {
    "iso":"EE",
    "name":"Estonia"
  },
  {
    "iso":"ET",
    "name":"Ethiopia"
  },
  {
    "iso":"FK",
    "name":"Falkland Islands"
  },
  {
    "iso":"FO",
    "name":"Faroe Islands"
  },
  {
    "iso":"FJ",
    "name":"Fiji"
  },
  {
    "iso":"FI",
    "name":"Finland"
  },
  {
    "iso":"FR",
    "name":"France"
  },
  {
    "iso":"GF",
    "name":"French Guiana"
  },
  {
    "iso":"PF",
    "name":"French Polynesia"
  },
  {
    "iso":"TF",
    "name":"French Southern Territories"
  },
  {
    "iso":"FQ",
    "name":"French Southern and Antarctic Territories"
  },
  {
    "iso":"GA",
    "name":"Gabon"
  },
  {
    "iso":"GM",
    "name":"Gambia"
  },
  {
    "iso":"GE",
    "name":"Georgia"
  },
  {
    "iso":"DE",
    "name":"Germany"
  },
  {
    "iso":"GH",
    "name":"Ghana"
  },
  {
    "iso":"GI",
    "name":"Gibraltar"
  },
  {
    "iso":"GR",
    "name":"Greece"
  },
  {
    "iso":"GL",
    "name":"Greenland"
  },
  {
    "iso":"GD",
    "name":"Grenada"
  },
  {
    "iso":"GP",
    "name":"Guadeloupe"
  },
  {
    "iso":"GU",
    "name":"Guam"
  },
  {
    "iso":"GT",
    "name":"Guatemala"
  },
  {
    "iso":"GG",
    "name":"Guernsey"
  },
  {
    "iso":"GN",
    "name":"Guinea"
  },
  {
    "iso":"GW",
    "name":"Guinea-Bissau"
  },
  {
    "iso":"GY",
    "name":"Guyana"
  },
  {
    "iso":"HT",
    "name":"Haiti"
  },
  {
    "iso":"HM",
    "name":"Heard Island and McDonald Islands"
  },
  {
    "iso":"HN",
    "name":"Honduras"
  },
  {
    "iso":"HK",
    "name":"Hong Kong SAR China"
  },
  {
    "iso":"HU",
    "name":"Hungary"
  },
  {
    "iso":"IS",
    "name":"Iceland"
  },
  {
    "iso":"IN",
    "name":"India"
  },
  {
    "iso":"ID",
    "name":"Indonesia"
  },
  {
    "iso":"IR",
    "name":"Iran"
  },
  {
    "iso":"IQ",
    "name":"Iraq"
  },
  {
    "iso":"IE",
    "name":"Ireland"
  },
  {
    "iso":"IM",
    "name":"Isle of Man"
  },
  {
    "iso":"IL",
    "name":"Israel"
  },
  {
    "iso":"IT",
    "name":"Italy"
  },
  {
    "iso":"JM",
    "name":"Jamaica"
  },
  {
    "iso":"JP",
    "name":"Japan"
  },
  {
    "iso":"JE",
    "name":"Jersey"
  },
  {
    "iso":"JT",
    "name":"Johnston Island"
  },
  {
    "iso":"JO",
    "name":"Jordan"
  },
  {
    "iso":"KZ",
    "name":"Kazakhstan"
  },
  {
    "iso":"KE",
    "name":"Kenya"
  },
  {
    "iso":"KI",
    "name":"Kiribati"
  },
  {
    "iso":"KW",
    "name":"Kuwait"
  },
  {
    "iso":"KG",
    "name":"Kyrgyzstan"
  },
  {
    "iso":"LA",
    "name":"Laos"
  },
  {
    "iso":"LV",
    "name":"Latvia"
  },
  {
    "iso":"LB",
    "name":"Lebanon"
  },
  {
    "iso":"LS",
    "name":"Lesotho"
  },
  {
    "iso":"LR",
    "name":"Liberia"
  },
  {
    "iso":"LY",
    "name":"Libya"
  },
  {
    "iso":"LI",
    "name":"Liechtenstein"
  },
  {
    "iso":"LT",
    "name":"Lithuania"
  },
  {
    "iso":"LU",
    "name":"Luxembourg"
  },
  {
    "iso":"MO",
    "name":"Macau SAR China"
  },
  {
    "iso":"MK",
    "name":"Macedonia"
  },
  {
    "iso":"MG",
    "name":"Madagascar"
  },
  {
    "iso":"MW",
    "name":"Malawi"
  },
  {
    "iso":"MY",
    "name":"Malaysia"
  },
  {
    "iso":"MV",
    "name":"Maldives"
  },
  {
    "iso":"ML",
    "name":"Mali"
  },
  {
    "iso":"MT",
    "name":"Malta"
  },
  {
    "iso":"MH",
    "name":"Marshall Islands"
  },
  {
    "iso":"MQ",
    "name":"Martinique"
  },
  {
    "iso":"MR",
    "name":"Mauritania"
  },
  {
    "iso":"MU",
    "name":"Mauritius"
  },
  {
    "iso":"YT",
    "name":"Mayotte"
  },
  {
    "iso":"FX",
    "name":"Metropolitan France"
  },
  {
    "iso":"MX",
    "name":"Mexico"
  },
  {
    "iso":"FM",
    "name":"Micronesia"
  },
  {
    "iso":"MI",
    "name":"Midway Islands"
  },
  {
    "iso":"MD",
    "name":"Moldova"
  },
  {
    "iso":"MC",
    "name":"Monaco"
  },
  {
    "iso":"MN",
    "name":"Mongolia"
  },
  {
    "iso":"ME",
    "name":"Montenegro"
  },
  {
    "iso":"MS",
    "name":"Montserrat"
  },
  {
    "iso":"MA",
    "name":"Morocco"
  },
  {
    "iso":"MZ",
    "name":"Mozambique"
  },
  {
    "iso":"MM",
    "name":"Myanmar [Burma]"
  },
  {
    "iso":"NA",
    "name":"Namibia"
  },
  {
    "iso":"NR",
    "name":"Nauru"
  },
  {
    "iso":"NP",
    "name":"Nepal"
  },
  {
    "iso":"NL",
    "name":"Netherlands"
  },
  {
    "iso":"AN",
    "name":"Netherlands Antilles"
  },
  {
    "iso":"NT",
    "name":"Neutral Zone"
  },
  {
    "iso":"NC",
    "name":"New Caledonia"
  },
  {
    "iso":"NZ",
    "name":"New Zealand"
  },
  {
    "iso":"NI",
    "name":"Nicaragua"
  },
  {
    "iso":"NE",
    "name":"Niger"
  },
  {
    "iso":"NG",
    "name":"Nigeria"
  },
  {
    "iso":"NU",
    "name":"Niue"
  },
  {
    "iso":"NF",
    "name":"Norfolk Island"
  },
  {
    "iso":"KP",
    "name":"North Korea"
  },
  {
    "iso":"VD",
    "name":"North Vietnam"
  },
  {
    "iso":"MP",
    "name":"Northern Mariana Islands"
  },
  {
    "iso":"NO",
    "name":"Norway"
  },
  {
    "iso":"OM",
    "name":"Oman"
  },
  {
    "iso":"PC",
    "name":"Pacific Islands Trust Territory"
  },
  {
    "iso":"PK",
    "name":"Pakistan"
  },
  {
    "iso":"PW",
    "name":"Palau"
  },
  {
    "iso":"PS",
    "name":"Palestinian Territories"
  },
  {
    "iso":"PA",
    "name":"Panama"
  },
  {
    "iso":"PZ",
    "name":"Panama Canal Zone"
  },
  {
    "iso":"PG",
    "name":"Papua New Guinea"
  },
  {
    "iso":"PY",
    "name":"Paraguay"
  },
  {
    "iso":"YD",
    "name":"Peoples Democratic Republic of Yemen"
  },
  {
    "iso":"PE",
    "name":"Peru"
  },
  {
    "iso":"PH",
    "name":"Philippines"
  },
  {
    "iso":"PN",
    "name":"Pitcairn Islands"
  },
  {
    "iso":"PL",
    "name":"Poland"
  },
  {
    "iso":"PT",
    "name":"Portugal"
  },
  {
    "iso":"PR",
    "name":"Puerto Rico"
  },
  {
    "iso":"QA",
    "name":"Qatar"
  },
  {
    "iso":"RO",
    "name":"Romania"
  },
  {
    "iso":"RU",
    "name":"Russia"
  },
  {
    "iso":"RW",
    "name":"Rwanda"
  },
  {
    "iso":"RE",
    "name":"Réunion"
  },
  {
    "iso":"BL",
    "name":"Saint Barthélemy"
  },
  {
    "iso":"SH",
    "name":"Saint Helena"
  },
  {
    "iso":"KN",
    "name":"Saint Kitts and Nevis"
  },
  {
    "iso":"LC",
    "name":"Saint Lucia"
  },
  {
    "iso":"MF",
    "name":"Saint Martin"
  },
  {
    "iso":"PM",
    "name":"Saint Pierre and Miquelon"
  },
  {
    "iso":"VC",
    "name":"Saint Vincent and the Grenadines"
  },
  {
    "iso":"WS",
    "name":"Samoa"
  },
  {
    "iso":"SM",
    "name":"San Marino"
  },
  {
    "iso":"SA",
    "name":"Saudi Arabia"
  },
  {
    "iso":"SN",
    "name":"Senegal"
  },
  {
    "iso":"RS",
    "name":"Serbia"
  },
  {
    "iso":"CS",
    "name":"Serbia and Montenegro"
  },
  {
    "iso":"SC",
    "name":"Seychelles"
  },
  {
    "iso":"SL",
    "name":"Sierra Leone"
  },
  {
    "iso":"SG",
    "name":"Singapore"
  },
  {
    "iso":"SK",
    "name":"Slovakia"
  },
  {
    "iso":"SI",
    "name":"Slovenia"
  },
  {
    "iso":"SB",
    "name":"Solomon Islands"
  },
  {
    "iso":"SO",
    "name":"Somalia"
  },
  {
    "iso":"ZA",
    "name":"South Africa"
  },
  {
    "iso":"GS",
    "name":"South Georgia and the South Sandwich Islands"
  },
  {
    "iso":"KR",
    "name":"South Korea"
  },
  {
    "iso":"ES",
    "name":"Spain"
  },
  {
    "iso":"LK",
    "name":"Sri Lanka"
  },
  {
    "iso":"SD",
    "name":"Sudan"
  },
  {
    "iso":"SR",
    "name":"Suriname"
  },
  {
    "iso":"SJ",
    "name":"Svalbard and Jan Mayen"
  },
  {
    "iso":"SZ",
    "name":"Swaziland"
  },
  {
    "iso":"SE",
    "name":"Sweden"
  },
  {
    "iso":"CH",
    "name":"Switzerland"
  },
  {
    "iso":"SY",
    "name":"Syria"
  },
  {
    "iso":"ST",
    "name":"São Tomé and Príncipe"
  },
  {
    "iso":"TW",
    "name":"Taiwan"
  },
  {
    "iso":"TJ",
    "name":"Tajikistan"
  },
  {
    "iso":"TZ",
    "name":"Tanzania"
  },
  {
    "iso":"TH",
    "name":"Thailand"
  },
  {
    "iso":"TL",
    "name":"Timor-Leste"
  },
  {
    "iso":"TG",
    "name":"Togo"
  },
  {
    "iso":"TK",
    "name":"Tokelau"
  },
  {
    "iso":"TO",
    "name":"Tonga"
  },
  {
    "iso":"TT",
    "name":"Trinidad and Tobago"
  },
  {
    "iso":"TN",
    "name":"Tunisia"
  },
  {
    "iso":"TR",
    "name":"Turkey"
  },
  {
    "iso":"TM",
    "name":"Turkmenistan"
  },
  {
    "iso":"TC",
    "name":"Turks and Caicos Islands"
  },
  {
    "iso":"TV",
    "name":"Tuvalu"
  },
  {
    "iso":"UM",
    "name":"U.S. Minor Outlying Islands"
  },
  {
    "iso":"PU",
    "name":"U.S. Miscellaneous Pacific Islands"
  },
  {
    "iso":"VI",
    "name":"U.S. Virgin Islands"
  },
  {
    "iso":"UG",
    "name":"Uganda"
  },
  {
    "iso":"UA",
    "name":"Ukraine"
  },
  {
    "iso":"SU",
    "name":"Union of Soviet Socialist Republics"
  },
  {
    "iso":"AE",
    "name":"United Arab Emirates"
  },
  {
    "iso":"GB",
    "name":"United Kingdom"
  },
  {
    "iso":"US",
    "name":"United States"
  },
  {
    "iso":"ZZ",
    "name":"Unknown or Invalid Region"
  },
  {
    "iso":"UY",
    "name":"Uruguay"
  },
  {
    "iso":"UZ",
    "name":"Uzbekistan"
  },
  {
    "iso":"VU",
    "name":"Vanuatu"
  },
  {
    "iso":"VA",
    "name":"Vatican City"
  },
  {
    "iso":"VE",
    "name":"Venezuela"
  },
  {
    "iso":"VN",
    "name":"Vietnam"
  },
  {
    "iso":"WK",
    "name":"Wake Island"
  },
  {
    "iso":"WF",
    "name":"Wallis and Futuna"
  },
  {
    "iso":"EH",
    "name":"Western Sahara"
  },
  {
    "iso":"YE",
    "name":"Yemen"
  },
  {
    "iso":"ZM",
    "name":"Zambia"
  },
  {
    "iso":"ZW",
    "name":"Zimbabwe"
  },
  {
    "iso":"AX",
    "name":"Åland Islands"
  }
]';
	
}

// setup importer
$rs_csv_importer = new RS_CSV_Importer();

register_importer('csv', __('CSV', 'rs-csv-importer'), __('Import posts, categories, tags, custom fields from simple csv file.', 'rs-csv-importer'), array ($rs_csv_importer, 'dispatch'));

} // class_exists( 'WP_Importer' )
