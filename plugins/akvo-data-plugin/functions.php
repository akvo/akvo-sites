<?php
/* Functions used by akvodata.php */
 
function akvodata_activated() {
    $akvodata_version = 1.0;
 
/* These options are stored in the database. */
    add_option("akvodata_version", $akvodata_version);
    add_option("akvodata_chart", array());
 
    $default_opts = array(array('year' => date('Y'),
                  'month' => date('m'),
                  'yield-with-fertilizer' => 0,
                  'yield-without-fertilizer' => 0,
                  'income-with-fertilizer' => 0,
                  'income-without-fertilizer' => 0
                  ));
    add_option("akvodata_opts", $default_opts);
}
 
function akvodata_deactivated() {
    delete_option('akvodata_opts');
    delete_option('akvodata_chart');
 
/* We don't have anything to perform but you would put your deactivation
   functions here. */
 
}
 
/* add_menu_page creates a top level menu button. If you are looking for a
   menu button to place inside Settings use add_options_page */
 
function akvodata_add_menu_page() {
    add_menu_page("Akvo data", "Akvo data", "activate_plugins", "akvodata", "akvodata_admin_overview_page");
    add_submenu_page('akvodata', 'data overview', 'data overview', 'update_core', 'akvodata', "akvodata_admin_overview_page");
    add_submenu_page('akvodata', 'add data', 'add data', 'update_core', 'akvodata-add', "akvodata_admin_add_page");
}
 
function akvodata_admin_overview_page() {
    include_once dirname(__FILE__) . '/overview.php';
}
 
function akvodata_admin_add_page() {
    include_once dirname(__FILE__) . '/add.php';
}
 
/* Functions used by admin.php */
 
function akvodata_admin_scripts() {
 
    /* Register and queue the Stylesheet */
    wp_register_style('akvodata_admin_css', plugins_url('admin.css', __FILE__));
    wp_enqueue_style('akvodata_admin_css');
 
    /* Register and queue the Javascript */
    wp_register_script('akvodata_admin_js', plugins_url('admin.js', __FILE__));
    wp_enqueue_script('akvodata_admin_js');
 
    /* Load the jQuery that is already included in WordPress */
    wp_enqueue_script('jquery');
}
 

function akvodata_widget_scripts() {
    if( is_active_widget( '', '', 'akvodata_widget' ) ) { // check if widget
        /* Register and queue the Stylesheet */
        wp_register_style('akvodata_widget_css', plugins_url('akvo-data-plugin.css', __FILE__));
        wp_enqueue_style('akvodata_widget_css');
        /* Load the jQuery that is already included in WordPress */
        wp_enqueue_script('jquery');
        /* Register and queue the Javascript */
        wp_register_script('akvodata_widget_js', plugins_url('akvo-data-plugin.js', __FILE__));
        wp_register_script('akvodata_highcharts_js', plugins_url('akvodata-highcharts.js', __FILE__));
        wp_enqueue_script('akvodata_highcharts_js');
        wp_enqueue_script('akvodata_widget_js');

        
    }
}

function akvodata_setchartdata(){
    $data = get_option('akvodata_opts');
    
    $cleandata = array();
    foreach($data AS $item){
        if(!is_array($cleandata[$item['year']]))$cleandata[$item['year']]=array();
        $cleandata[$item['year']][$item['month']]=$item;
    }
    
    $chartdata = array(
        'months'=>array(),
        'yield-with'=>array(),
        'yield-without'=>array(),
        'income-with'=>array(),
        'income-without'=>array(),
        );
    for($i=-6;$i<6;$i++){
        $str = ($i<0) ? $i.' months' : '+'.$i.' months';
        $n = date('n',strtotime($str));
        $y = date('Y',strtotime($str));
        $chartdata['months'][]=date('F',strtotime($str));
        $chartdata['yield-with'][] = (isset($cleandata[$y][$n]['yield-with-fertilizer'])) ? $cleandata[$y][$n]['yield-with-fertilizer'] : '' ;
        $chartdata['yield-without'][] = (isset($cleandata[$y][$n]['yield-without-fertilizer'])) ? $cleandata[$y][$n]['yield-without-fertilizer'] : '' ;
        $chartdata['income-with'][] = (isset($cleandata[$y][$n]['income-with-fertilizer'])) ? $cleandata[$y][$n]['income-with-fertilizer'] : '' ;
        $chartdata['income-without'][] = (isset($cleandata[$y][$n]['income-without-fertilizer'])) ? $cleandata[$y][$n]['income-without-fertilizer'] : '' ;
    }
    update_option('akvodata_chart',$chartdata);
    //return $chartdata;
}
function akvodata_getchartdata(){
    global $wpdb;
    $chartdata = get_option('akvodata_chart');
    $chartdata['funds'] = $wpdb->get_var( "SELECT funds FROM partner_details WHERE prefix = '".$wpdb->prefix."'" );
    $chartdata['projects'] = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."projects" );
    $chartdata['updates'] = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."posts WHERE post_type='project_update'" );
    return $chartdata;
}


 
/* Functions used for posts */
function akvodata_replace_keyword() {
 
    /* Retrieve our settings */
    $options = get_option('akvodata_opts');
 
    /* If we want to create more complex html code it's easier to capture the output buffer and return it */
    ob_start(); ?>
        <span class="akvodata-span" newColor="<?php echo $options['toggle'] ?>" style="color: <?php echo $options['replace-color'] ?>;"><?php echo stripslashes($options['replace']) ?></span>
<?php
    /* Return the buffer contents into a variable */
    $new_content = ob_get_contents();
 
    /* Empty the buffer without displaying it. We don't want the previous html shown */
    ob_end_clean();
 
    /* The text returned will replace our shortcode matching text */
    return $new_content;
}

function akvodata_register_widgets() {
	register_widget( 'AkvodataWidget' );
}

class AkvodataWidget extends WP_Widget {

	function AkvodataWidget() {
		// Instantiate the parent object
		parent::__construct(
	 		'akvodata_widget', // Base ID
			'Akvodata Widget', // Name
			array( 'description' => __( 'Display data dashboard'), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
        $chartdata = akvodata_getchartdata();
        
        echo $before_widget;
        ?>
    <div class="akvodatawidget">
        <h2><?php echo $title;?></h2>
        <div class="akvodata">
            <label>Farmers:</label><span><?php echo $chartdata['projects']; ?></span><br />
            <label>Donations:</label><span>&euro;<?php echo number_format($chartdata['funds'],0,',','.'); ?></span><br />
            <label>Project updates:</label><span><?php echo $chartdata['updates']; ?></span><br />
        </div>
        <div id="akvochart1" class="chart" type="line">
                            <div class="chart-legend">true</div>
                            <div class="chart-title">Yield kg/ha</div>
                            <div class="chart-year"><?php echo array_search('January',$chartdata['months']); ?></div>
                            <div class="chart-xaxis">
                                <?php 
                                foreach($chartdata['months'] AS $m){
                                ?>
                                <div><?php echo $m; ?></div>
                                <?php }?>
                            </div>
                            <div class="chart-serie" name="with fertilizer" color="#276a9f" legend="3">
                                <?php 
                                foreach($chartdata['yield-with'] AS $val){
                                ?>
                                <div><?php echo $val; ?></div>
                                <?php }?>
                            </div>
                            <div class="chart-serie" name="without fertilizer" color="#a2a2a2" legend="3">
                                <?php 
                                foreach($chartdata['yield-without'] AS $val){
                                ?>
                                <div><?php echo $val; ?></div>
                                <?php }?>
                            </div>
        </div>
        <div id="akvochart2" class="chart" type="line">
                            <div class="chart-legend">true</div>
                            <div class="chart-title">Net. income &dollar;</div>
                            <div class="chart-year"><?php echo array_search('January',$chartdata['months']); ?></div>
                            <div class="chart-xaxis">
                                <?php 
                                foreach($chartdata['months'] AS $m){
                                ?>
                                <div><?php echo $m; ?></div>
                                <?php }?>
                            </div>
                            <div class="chart-serie" name="with fertilizer" color="#276a9f" legend="3">
                                <?php 
                                foreach($chartdata['income-with'] AS $val){
                                ?>
                                <div><?php echo $val; ?></div>
                                <?php }?>
                            </div>
                            <div class="chart-serie" name="without fertilizer" color="#a2a2a2" legend="3">
                                <?php 
                                foreach($chartdata['income-without'] AS $val){
                                ?>
                                <div><?php echo $val; ?></div>
                                <?php }?>
                            </div>
        </div>
    <?php
    ?>
    </div>
    <?php
        
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}
}
 
?>