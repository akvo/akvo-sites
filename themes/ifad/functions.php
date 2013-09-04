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
            'Title'=>'title',
            'Community members'=>'cmembers',
            'Commmunity contributions'=>'cdiscussions',
            'Wiki visitors'=>'wvisitors',
            'Wiki articles'=>'warticles',
            'Shared documents'=>'sdocuments'
        );
	function __construct() {
		// Instantiate the parent object
		parent::__construct( false, 'IFAD data widget' );
	}

	function widget( $args, $instance ) {
		// Widget output
        global $wpdb;
        extract($instance);
        $projects = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."projects" );
        //if($query != '')$search_query=$newstr;
        ?>
				
            <div id="text-2" class="sidebar-box widget_text">			
                <div class="textwidget"><h2><?php echo $title; ?></h2>
                <div class="akvodata">
                    <?php foreach($this->aFields AS $label=> $value){
                        if($value=='title')continue;?>
                    <label><?php echo $label; ?>:</label><span><?php echo $$value; ?></span><br>
                    <?php } ?>
                    <label>Projects:</label><span><?php echo $projects;?></span>
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
//        $aFields=array(
//            'Title'=>'title',
//            'Community members'=>'cmembers',
//            'Commmunity discussions'=>'cdiscussions',
//            'Wiki visitors'=>'wvisitors',
//            'Wiki articles'=>'warticles',
//            'Shared documents'=>'sdocuments'
//        );
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
