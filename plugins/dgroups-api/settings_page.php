<?php
global $dgroups_api_plugin_options;
if(isset($_POST['dgroups_api'])){
    update_option('dgroups_api_plugin_options', $_POST['dgroups_api']);
    $dgroups_api_plugin_options = get_option('dgroups_api_plugin_options');
}

?>
<h1>Dgroups API options</h1>
<form method="post" enctype="multipart/form-data">
    <p>
        <label for="iInputPath">API path:</label><br />
        <i>http://dgroups.org/rwsn/</i><input type="text" id="iInputPath" name="dgroups_api[path]" value="<?php echo $dgroups_api_plugin_options['path']; ?>" />
    </p>
    <p>
        <label for="iInputApiKey">API key:</label><br />
        <input type="text" id="iInputApiKey" name="dgroups_api[api_key]"  value="<?php echo $dgroups_api_plugin_options['api_key']; ?>" />
    </p>
    <p>
        <label for="iInputApiSecret">API secret:</label><br />
        <input type="text" id="iInputApiSecret" name="dgroups_api[api_secret]"  value="<?php echo $dgroups_api_plugin_options['api_secret']; ?>" />
    </p>
    
    <p class="submit">
        <input type="submit" value="Save Sidebar" name="submit" class="button">
    </p>
</form>