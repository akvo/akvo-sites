<?php
/*
Plugin Name: Akvo redirect plugin
Description: Redirect site to other URL
Version: 1.0
Author: Eveline Sparreboom
Author URI: http://www.kominski.net
License: GPl2
*/
add_action('admin_menu', 'akvoredirect_add_menu_page');

function akvoredirect_add_menu_page() {
    add_menu_page("Akvo redirect", "Akvo redirect", "activate_plugins", "akvoredirect", "akvoredirect_admin_settings_page");
    add_submenu_page('akvoredirect', 'settings', 'settings', 'update_core', 'akvoredirect', "akvoredirect_admin_settings_page");
}

function akvoredirect_admin_settings_page() {
    //include_once dirname(__FILE__) . '/overview.php';
     
$options = get_option('akvoredirect_opts');

?>
 
<h1>Settings</h1>
 
<form id="akvoredirect-settings" method="post" action="<?php echo plugins_url('update.php',__FILE__) ?>">
    <label>Redirect url:</label> <input name="redirect-url" value="<?php echo $options['redirect-url'] ?>" type="text" /><br />
    <label>Active:</label> <input name="redirect-active" value="1" type="checkbox" <?php if($options['redirect-active']==1)echo 'checked'; ?> /><br />
    <input type="submit" value="Update" /><span class="update-status"></span>
 
</form>

<?php
}
?>
