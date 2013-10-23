<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function getPartnerData(){
    global $wpdb;
    $data['funds'] = $wpdb->get_var( "SELECT funds FROM partner_details WHERE prefix = '".$wpdb->prefix."'" );
    $data['partners'] = $wpdb->get_var( "SELECT partners FROM partner_details WHERE prefix = '".$wpdb->prefix."'" );
    $data['projects'] = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."projects" );
    $data['updates'] = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."posts WHERE post_type='project_update'" );
    return $data;
}
function widget_akvo_data() {
    $aData = getPartnerData();    
	?>
        <div id="text-2" class="sidebar-box widget_text"><h2>Current status:</h2>			<div class="textwidget"><h2>Current status:</h2>
        <div class="akvodata">
            <label>Partners:</label><span><?php echo $aData['partners']; ?></span><br>
            <label>Projects:</label><span><?php echo $aData['projects']; ?></span><br>
            <label>Total budget:</label><span>€<?php echo number_format($aData['funds'],0,',','.'); ?></span><br>
            <label>Project updates:</label><span><?php echo $aData['updates']; ?></span><br>
      </div>
		</div>
		</div>
	<?php
	}
	if ( function_exists('register_sidebar_widget') )
	    wp_register_sidebar_widget('akvo-data',__('Akvo data'), 'widget_akvo_data');
	
function akvo_project_domain(){
    add_option( 'akvo_project_domain', 'http://wash-liberia.akvoapp.org/en');
}
add_action('init', 'akvo_project_domain');		
?>
