<div class="wrap">
<h2>The Dropbox Plugin</h2>

<form method="post" action="options.php">
<?php settings_fields('tdp-opt');  ?>
<table class="form-table">
<tr valign="top">
<th scope="row">Consummer key</th>
<td><input type="text" name="tdp_mail" value="<?php echo get_option('tdp_mail'); ?>" /></td>
</tr>

<tr valign="top">
<th scope="row">Consummer secret</th>
<td><input type="text" name="tdp_pass" value="<?php echo get_option('tdp_pass'); ?>" /></td>
</tr>

<tr valign="top">
<td><input type="checkbox" name="tdp_cred" value="1" <?php if( get_option('tdp_cred')=="1" || !get_option('tdp_cred')) echo "checked=\"1\"" ?> /> Do not include backlink(bottom of page)
</td>
</tr>

</table>

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>


<a href="<?php echo get_bloginfo('wpurl').'/wp-content/plugins/dropbox-plugin/conn.php'; ?>"  target=”_blank”>Click here after you saved everything to connect to your Dropbox</a></th>
<br/>

</form>



</div>
