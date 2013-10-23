<?php 
add_action( 'admin_enqueue_scripts', 'import_epanel_javascript' );
function import_epanel_javascript( $hook_suffix ) {
	if ( 'admin.php' == $hook_suffix && isset( $_GET['import'] ) && isset( $_GET['step'] ) && 'wordpress' == $_GET['import'] && '1' == $_GET['step'] )
		add_action( 'admin_head', 'admin_headhook' );
}

function admin_headhook(){ ?>
	<script type="text/javascript">
		jQuery(document).ready(function($){
			$("p.submit").before("<p><input type='checkbox' id='importepanel' name='importepanel' value='1' style='margin-right: 5px;'><label for='importepanel'>Replace ePanel settings with sample data values</label></p>");
		});
	</script>
<?php }

add_action('import_end','importend');
function importend(){
	global $wpdb, $shortname;
	
	#make custom fields image paths point to sampledata/sample_images folder
	$sample_images_postmeta = $wpdb->get_results("SELECT meta_id, meta_value FROM $wpdb->postmeta WHERE meta_value REGEXP 'http://et_sample_images.com'");
	if ( $sample_images_postmeta ) {
		foreach ( $sample_images_postmeta as $postmeta ){
			$template_dir = get_template_directory_uri();
			if ( is_multisite() ){
				switch_to_blog(1);
				$main_siteurl = site_url();
				restore_current_blog();
				
				$template_dir = $main_siteurl . '/wp-content/themes/' . get_template();
			}
			preg_match( '/http:\/\/et_sample_images.com\/([^.]+).jpg/', $postmeta->meta_value, $matches );
			$image_path = $matches[1];
			
			$local_image = preg_replace( '/http:\/\/et_sample_images.com\/([^.]+).jpg/', $template_dir . '/sampledata/sample_images/$1.jpg', $postmeta->meta_value );
			
			$local_image = preg_replace( '/s:55:/', 's:' . strlen( $template_dir . '/sampledata/sample_images/' . $image_path . '.jpg' ) . ':', $local_image );
			
			$wpdb->update( $wpdb->postmeta, array( 'meta_value' => $local_image ), array( 'meta_id' => $postmeta->meta_id ), array( '%s' ) );
		}
	}

	if ( !isset($_POST['importepanel']) )
		return;
	
	$importOptions = 'YTo5NDp7czowOiIiO047czoxMToicXVhZHJvX2xvZ28iO3M6MDoiIjtzOjE0OiJxdWFkcm9fZmF2aWNvbiI7czowOiIiO3M6MTk6InF1YWRyb19jb2xvcl9zY2hlbWUiO3M6NDoiUGluayI7czoxNzoicXVhZHJvX2Jsb2dfc3R5bGUiO047czoxNzoicXVhZHJvX2dyYWJfaW1hZ2UiO047czoxOToicXVhZHJvX2NhdG51bV9wb3N0cyI7czoxOiI2IjtzOjIzOiJxdWFkcm9fYXJjaGl2ZW51bV9wb3N0cyI7czoxOiI1IjtzOjIyOiJxdWFkcm9fc2VhcmNobnVtX3Bvc3RzIjtzOjE6IjUiO3M6MTk6InF1YWRyb190YWdudW1fcG9zdHMiO3M6MToiNSI7czoxODoicXVhZHJvX2RhdGVfZm9ybWF0IjtzOjY6Ik0gaiwgWSI7czoxODoicXVhZHJvX3VzZV9leGNlcnB0IjtOO3M6MTY6InF1YWRyb19zaG93X3RhYnMiO3M6Mjoib24iO3M6MzM6InF1YWRyb19zaG93X3RhYmFyZWFfcmVjZW50ZW50cmllcyI7czoyOiJvbiI7czoyNzoicXVhZHJvX3Nob3dfdGFiYXJlYV9wb3B1bGFyIjtzOjI6Im9uIjtzOjM0OiJxdWFkcm9fc2hvd190YWJhcmVhX3JlY2VudGNvbW1lbnRzIjtzOjI6Im9uIjtzOjIyOiJxdWFkcm9fcmVjZW50cG9zdHNfbnVtIjtzOjE6IjYiO3M6MjQ6InF1YWRyb19wb3B1bGFyX3Bvc3RzX251bSI7czoxOiI2IjtzOjI1OiJxdWFkcm9fcmVjZW50Y29tbWVudHNfbnVtIjtzOjE6IjYiO3M6MjE6InF1YWRyb19ob21lcGFnZV9wb3N0cyI7czoxOiI4IjtzOjIxOiJxdWFkcm9fZXhsY2F0c19yZWNlbnQiO047czoxNToicXVhZHJvX2ZlYXR1cmVkIjtzOjI6Im9uIjtzOjE2OiJxdWFkcm9fZHVwbGljYXRlIjtzOjI6Im9uIjtzOjE1OiJxdWFkcm9fZmVhdF9jYXQiO3M6MTM6IlVuY2F0ZWdvcml6ZWQiO3M6MTk6InF1YWRyb19mZWF0dXJlZF9udW0iO3M6MToiMyI7czoxNjoicXVhZHJvX21lbnVwYWdlcyI7TjtzOjIzOiJxdWFkcm9fZW5hYmxlX2Ryb3Bkb3ducyI7czoyOiJvbiI7czoxNjoicXVhZHJvX2hvbWVfbGluayI7czoyOiJvbiI7czoxNzoicXVhZHJvX3NvcnRfcGFnZXMiO3M6MTA6InBvc3RfdGl0bGUiO3M6MTc6InF1YWRyb19vcmRlcl9wYWdlIjtzOjM6ImFzYyI7czoyNDoicXVhZHJvX3RpZXJzX3Nob3duX3BhZ2VzIjtzOjE6IjMiO3M6MTU6InF1YWRyb19tZW51Y2F0cyI7TjtzOjM0OiJxdWFkcm9fZW5hYmxlX2Ryb3Bkb3duc19jYXRlZ29yaWVzIjtzOjI6Im9uIjtzOjIzOiJxdWFkcm9fY2F0ZWdvcmllc19lbXB0eSI7czoyOiJvbiI7czoyOToicXVhZHJvX3RpZXJzX3Nob3duX2NhdGVnb3JpZXMiO3M6MToiMyI7czoxNToicXVhZHJvX3NvcnRfY2F0IjtzOjQ6Im5hbWUiO3M6MTY6InF1YWRyb19vcmRlcl9jYXQiO3M6MzoiYXNjIjtzOjE4OiJxdWFkcm9fc3dhcF9uYXZiYXIiO047czoyMjoicXVhZHJvX2Rpc2FibGVfdG9wdGllciI7TjtzOjE2OiJxdWFkcm9fcG9zdGluZm8yIjthOjQ6e2k6MDtzOjY6ImF1dGhvciI7aToxO3M6NDoiZGF0ZSI7aToyO3M6MTA6ImNhdGVnb3JpZXMiO2k6MztzOjg6ImNvbW1lbnRzIjt9czoxNzoicXVhZHJvX3RodW1ibmFpbHMiO3M6Mjoib24iO3M6MjQ6InF1YWRyb19zaG93X3Bvc3Rjb21tZW50cyI7czoyOiJvbiI7czoyODoicXVhZHJvX3RodW1ibmFpbF93aWR0aF9wb3N0cyI7czozOiIxMDAiO3M6Mjk6InF1YWRyb190aHVtYm5haWxfaGVpZ2h0X3Bvc3RzIjtzOjM6IjEwMCI7czoyMjoicXVhZHJvX3BhZ2VfdGh1bWJuYWlscyI7TjtzOjI1OiJxdWFkcm9fc2hvd19wYWdlc2NvbW1lbnRzIjtOO3M6Mjg6InF1YWRyb190aHVtYm5haWxfd2lkdGhfcGFnZXMiO3M6MzoiMTAwIjtzOjI5OiJxdWFkcm9fdGh1bWJuYWlsX2hlaWdodF9wYWdlcyI7czozOiIxMDAiO3M6MTY6InF1YWRyb19wb3N0aW5mbzEiO2E6NDp7aTowO3M6NjoiYXV0aG9yIjtpOjE7czo0OiJkYXRlIjtpOjI7czoxMDoiY2F0ZWdvcmllcyI7aTozO3M6ODoiY29tbWVudHMiO31zOjIzOiJxdWFkcm9fdGh1bWJuYWlsc19pbmRleCI7czoyOiJvbiI7czoyMDoicXVhZHJvX2N1c3RvbV9jb2xvcnMiO047czoxNjoicXVhZHJvX2NoaWxkX2NzcyI7TjtzOjE5OiJxdWFkcm9fY2hpbGRfY3NzdXJsIjtzOjA6IiI7czoyMToicXVhZHJvX2NvbG9yX21haW5mb250IjtzOjA6IiI7czoyMToicXVhZHJvX2NvbG9yX21haW5saW5rIjtzOjA6IiI7czoyMToicXVhZHJvX2NvbG9yX3BhZ2VsaW5rIjtzOjA6IiI7czoyODoicXVhZHJvX2NvbG9yX3BhZ2VsaW5rX2FjdGl2ZSI7czowOiIiO3M6MjE6InF1YWRyb19jb2xvcl9oZWFkaW5ncyI7czowOiIiO3M6MjY6InF1YWRyb19jb2xvcl9zaWRlYmFyX2xpbmtzIjtzOjA6IiI7czoxODoicXVhZHJvX2Zvb3Rlcl90ZXh0IjtzOjA6IiI7czoyNDoicXVhZHJvX2NvbG9yX2Zvb3RlcmxpbmtzIjtzOjA6IiI7czoyMToicXVhZHJvX3Nlb19ob21lX3RpdGxlIjtOO3M6Mjc6InF1YWRyb19zZW9faG9tZV9kZXNjcmlwdGlvbiI7TjtzOjI0OiJxdWFkcm9fc2VvX2hvbWVfa2V5d29yZHMiO047czoyNToicXVhZHJvX3Nlb19ob21lX2Nhbm9uaWNhbCI7TjtzOjI1OiJxdWFkcm9fc2VvX2hvbWVfdGl0bGV0ZXh0IjtzOjA6IiI7czozMToicXVhZHJvX3Nlb19ob21lX2Rlc2NyaXB0aW9udGV4dCI7czowOiIiO3M6Mjg6InF1YWRyb19zZW9faG9tZV9rZXl3b3Jkc3RleHQiO3M6MDoiIjtzOjIwOiJxdWFkcm9fc2VvX2hvbWVfdHlwZSI7czoyNzoiQmxvZ05hbWUgfCBCbG9nIGRlc2NyaXB0aW9uIjtzOjI0OiJxdWFkcm9fc2VvX2hvbWVfc2VwYXJhdGUiO3M6MzoiIHwgIjtzOjIzOiJxdWFkcm9fc2VvX3NpbmdsZV90aXRsZSI7TjtzOjI5OiJxdWFkcm9fc2VvX3NpbmdsZV9kZXNjcmlwdGlvbiI7TjtzOjI2OiJxdWFkcm9fc2VvX3NpbmdsZV9rZXl3b3JkcyI7TjtzOjI3OiJxdWFkcm9fc2VvX3NpbmdsZV9jYW5vbmljYWwiO047czoyOToicXVhZHJvX3Nlb19zaW5nbGVfZmllbGRfdGl0bGUiO3M6OToic2VvX3RpdGxlIjtzOjM1OiJxdWFkcm9fc2VvX3NpbmdsZV9maWVsZF9kZXNjcmlwdGlvbiI7czoxNToic2VvX2Rlc2NyaXB0aW9uIjtzOjMyOiJxdWFkcm9fc2VvX3NpbmdsZV9maWVsZF9rZXl3b3JkcyI7czoxMjoic2VvX2tleXdvcmRzIjtzOjIyOiJxdWFkcm9fc2VvX3NpbmdsZV90eXBlIjtzOjIxOiJQb3N0IHRpdGxlIHwgQmxvZ05hbWUiO3M6MjY6InF1YWRyb19zZW9fc2luZ2xlX3NlcGFyYXRlIjtzOjM6IiB8ICI7czoyNjoicXVhZHJvX3Nlb19pbmRleF9jYW5vbmljYWwiO047czoyODoicXVhZHJvX3Nlb19pbmRleF9kZXNjcmlwdGlvbiI7TjtzOjIxOiJxdWFkcm9fc2VvX2luZGV4X3R5cGUiO3M6MjQ6IkNhdGVnb3J5IG5hbWUgfCBCbG9nTmFtZSI7czoyNToicXVhZHJvX3Nlb19pbmRleF9zZXBhcmF0ZSI7czozOiIgfCAiO3M6MzA6InF1YWRyb19pbnRlZ3JhdGVfaGVhZGVyX2VuYWJsZSI7czoyOiJvbiI7czoyODoicXVhZHJvX2ludGVncmF0ZV9ib2R5X2VuYWJsZSI7czoyOiJvbiI7czozMzoicXVhZHJvX2ludGVncmF0ZV9zaW5nbGV0b3BfZW5hYmxlIjtzOjI6Im9uIjtzOjM2OiJxdWFkcm9faW50ZWdyYXRlX3NpbmdsZWJvdHRvbV9lbmFibGUiO3M6Mjoib24iO3M6MjM6InF1YWRyb19pbnRlZ3JhdGlvbl9oZWFkIjtzOjA6IiI7czoyMzoicXVhZHJvX2ludGVncmF0aW9uX2JvZHkiO3M6MDoiIjtzOjI5OiJxdWFkcm9faW50ZWdyYXRpb25fc2luZ2xlX3RvcCI7czowOiIiO3M6MzI6InF1YWRyb19pbnRlZ3JhdGlvbl9zaW5nbGVfYm90dG9tIjtzOjA6IiI7czoxNzoicXVhZHJvXzQ2OF9lbmFibGUiO047czoxNjoicXVhZHJvXzQ2OF9pbWFnZSI7czowOiIiO3M6MTQ6InF1YWRyb180NjhfdXJsIjtzOjA6IiI7fQ==';
	
	/*global $options;
	
	foreach ($options as $value) {
		if( isset( $value['id'] ) ) { 
			update_option( $value['id'], $value['std'] );
		}
	}*/
	
	$importedOptions = unserialize(base64_decode($importOptions));
	
	foreach ($importedOptions as $key=>$value) {
		if ($value != '') update_option( $key, $value );
	}
} ?>