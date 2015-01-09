<?php

//Add an option page
if (is_admin()) {
  add_action('admin_menu', 'tdf_menu');
  add_action('admin_init', 'tdf_register_settings');
}

function tdf_menu() {
	add_options_page('Twitter Feed','Twitter Auth','manage_options','tdf_settings','tdf_settings_output');
}

function tdf_settings() {
	$tdf = array();
	$tdf[] = array('name'=>'tdf_consumer_key','label'=>'Twitter Application Consumer Key');
	$tdf[] = array('name'=>'tdf_consumer_secret','label'=>'Twitter Application Consumer Secret');
	$tdf[] = array('name'=>'tdf_access_token','label'=>'Account Access Token');
	$tdf[] = array('name'=>'tdf_access_token_secret','label'=>'Account Access Token Secret');
	return $tdf;
}

function tdf_register_settings() {
	$settings = tdf_settings();
	foreach($settings as $setting) {
		register_setting('tdf_settings',$setting['name']);
	}
}


function tdf_settings_output() {
	$settings = tdf_settings();
	
	echo '<div class="wrap">';
	
		echo '<h2>Twitter Auth</h2>';
		
		echo '<p>Most of this configuration can found on the application overview page on the <a href="http://dev.twitter.com/apps">http://dev.twitter.com</a> website.</p>';
		echo '<p>When creating an application for this plugin, you don\'t need to set a callback location and you only need read access.</p>';
		echo '<p>You will need to generate an oAuth token once you\'ve created the application. The button for that is on the bottom of the application overview page.</p>';
		
		echo '<hr />';
		
		echo '<form method="post" action="options.php">';
		
    settings_fields('tdf_settings');
		
		echo '<table>';
			foreach($settings as $setting) {
				echo '<tr>';
					echo '<td>'.$setting['label'].'</td>';
					echo '<td><input type="text" style="width: 400px" name="'.$setting['name'].'" value="'.get_option($setting['name']).'" /></td>';
				echo '</tr>';
				
			}
		echo '</table>';
		
		submit_button();
		
		echo '</form>';
		
		
	
	echo '</div>';
	
}