<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function ifad_iframe($atts=null,$content){
    $addclass=(is_array($atts) && (array_search('wide',$atts)!==false) || array_key_exists('wide', $atts)) ? 'no_sidebar' : '';
    return '</div><div class="ifad_iframe '.$addclass.'">'.do_shortcode($content).'<br style="clear:both;"></div>';
}
add_shortcode( 'ifad_iframe', 'ifad_iframe' );

function register_ifad_data_widget(){
    register_widget( 'ifad_data_Widget' );
}
add_action( 'widgets_init', 'register_ifad_data_widget' );

class ifad_data_Widget extends WP_Widget {
    var $aFields = array(
//            'Title'=>'title',
//            'Community members'=>'cmembers',
//            'Commmunity contributions'=>'cdiscussions',
            'Wiki visitors'=>'wvisitors',
            'Wiki pageviews'=>'warticles',
            //'Shared documents'=>'sdocuments'
        );
	function __construct() {
		// Instantiate the parent object
		parent::__construct( false, 'IFAD data widget' );
	}

	function widget( $args, $instance ) {
		// Widget output
        global $wpdb;
        extract($instance);
        $project_updates = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."posts WHERE post_status='publish' AND post_type='project_update'" );
        $sFiles = file_get_contents('http://api.rain4food.net/files');
        $aFiles = json_decode($sFiles,true);
        $sApiKey = 'rain4food';
$sApiSecret = '77e03e882c9d4bbbb468ee26bddd36a3';
$sPath = '/rwsn/rainwater/__api/v2/stats/basic';
$sTimestamp = file_get_contents('https://dgroups.org/rwsn/rainwater/__api/v2/time');
$sHash = sha1($sPath.$sApiKey.$sApiSecret.$sTimestamp);
$sUrl = 'https://dgroups.org'.$sPath;
$crl = curl_init();

$headr = array();
$headr[] = 'Accept: application/json';
$headr[] = 'Path: '.$sPath;
$headr[] = 'Authorization: '.$sHash;
$headr[] = 'X-ECS-Api-Key: '.$sApiKey;
$headr[] = 'X-ECS-Api-RequestTime: '.$sTimestamp;
curl_setopt($crl, CURLOPT_URL, $sUrl);
curl_setopt($crl, CURLOPT_HEADER, false);
curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($crl, CURLOPT_HTTPHEADER,$headr);
$rest = @curl_exec($crl);

curl_close($crl);
$apiresult = json_decode($rest,true);
        ?>
				
            <div id="text-2" class="sidebar-box widget_text">			
                <div class="textwidget"><h2><?php echo __('Current status','AkvoSites'); ?></h2>
                <div class="akvodata">
                    <label><?php echo __('Community members','AkvoSites');?>:</label><span><?php echo $apiresult['Members'];?></span><br>
                    <label><?php echo __('Community contributions','AkvoSites');?>:</label><span><?php echo $apiresult['Contributions'];?></span><br>
                    <label><?php echo __('Member countries','AkvoSites');?>:</label><span><?php echo $apiresult['Countries'];?></span><br>
                    <label><?php echo __('Wiki visitors','AkvoSites');?>:</label><span><?php echo $instance['wvisitors'];?></span><br>
                    <label><?php echo __('Wiki pageviews','AkvoSites');?>:</label><span><?php echo $instance['warticles'];?></span><br>
                    <label><?php echo __('Shared documents','AkvoSites');?>:</label><span><?php echo $aFiles['count'];?></span><br>
                    <label><?php echo __('Project updates','AkvoSites');?>:</label><span><?php echo $project_updates;?></span>
              </div>
                </div>
		</div>
        <?php
	}

	function update( $new_instance, $old_instance ) {
		// Save widget options
        $instance = array();
        foreach ($this->aFields AS $label=>$name){
            $instance[$name] = strip_tags( $new_instance[$name] );
        }
//		$instance['query'] = strip_tags( $new_instance['query'] );
//		$instance['display_limit'] = strip_tags( $new_instance['display_limit'] );

		return $instance;
	}

	function form( $instance ) {
		// Output admin widget options form
        $title = ( isset( $instance[ 'title' ] ) ) ? $instance[ 'title' ] : 'Current status';
        $cmembers = ( isset( $instance[ 'cmembers' ] ) ) ? $instance[ 'cmembers' ] : 0;
        $cdiscussions = ( isset( $instance[ 'cdiscussions' ] ) ) ? $instance[ 'cdiscussions' ] : 0;
        $wvisitors = ( isset( $instance[ 'wvisitors' ] ) ) ? $instance[ 'wvisitors' ] : 0;
        $warticles = ( isset( $instance[ 'warticles' ] ) ) ? $instance[ 'warticles' ] : 0;
        $sdocuments = ( isset( $instance[ 'sdocuments' ] ) ) ? $instance[ 'sdocuments' ] : 0;

        ?>
        <p>
            <?php
        foreach ($this->aFields AS $label=>$name){
		?>
		
            
		<br /><label for="<?php echo $this->get_field_id( $name ); ?>"><?php _e( $label.':' ); ?></label> 
        <br /><input  id="<?php echo esc_attr($this->get_field_id($name)); ?>" name="<?php echo esc_attr($this->get_field_name($name)); ?>" type="text" value="<?php echo $$name; ?>">
		
		
		<?php 
        }
        ?>
        </p>
        <?php
	}
}


function akvo_project_domain(){
    add_option( 'akvo_project_domain', 'http://rain4food.akvoapp.org/en');
}
add_action('init', 'akvo_project_domain');

function ifad_sharedocs($atts=null,$content){
    //$addclass=(is_array($atts) && (array_search('wide',$atts)!==false) || array_key_exists('wide', $atts)) ? 'no_sidebar' : '';
    return '</div> <!-- end .post-wrapper -->
                <div class="post-wrapper no_sidebar">
                    <div id="shared_docs_container">
                        <h2>Library</h2>
                        <div class="filter">
                            <select id="contenttypes" rel="contenttype"></select>
                            <select id="themes" rel="theme"></select>
                            <select id="regions" rel="region"></select>
                            <select id="languages" rel="language"></select>
                            <input id="filesearch" type="text" name="filesearch" />
                            <button id="filterDocs" value="filter">filter</button>
                        </div>
                        <div id="api_results">
                            
                        </div>
                    </div>
                ';
}
add_shortcode( 'ifad_sharedocs', 'ifad_sharedocs' );
?>
