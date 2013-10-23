<?php
/*
Plugin Name: Simple Newsletter Signup
Author: Grant James Kimball
Version: 1.5
Author URI: http://www.YourLocalWebmaster.com
Plugin URI: http://www.yourlocalwebmaster.com/plugins/simple-newsletter-signup/
Description: Easily add a newsletter subscription form to your site and start collecting email addresses to use with MailChimp, Constant contact or other third-party bulk-mailer program! Complete with email management admin panel. Web development is affordable @ <a href="http://www.yourlocalwebmaster.com">Your Local Webmaster.com</a>
*/
?>
<?php
add_action('admin_menu','simple_newsletter_menu_tlwm');
add_filter('widget_text', 'do_shortcode');

// if there is a delete query, do it before they do anything else!
add_action('admin_init','snsf_prep_admin');
function snsf_prep_admin(){

if(isset($_POST['snsf-checkbox-value'])){
	
	if($_POST['snsf-the-do-action'] == 'delete'){
$snsf_sql = "delete FROM snsf_subscribe WHERE id in (";
		foreach($_POST['snsf-checkbox-value'] as $snsf_box){
			$snsf_sql .= $snsf_box.",";
		}
		
		
		$snsf_sql = substr_replace($snsf_sql ,"",-1);
		$snsf_sql .= ")";
		
		mysql_query($snsf_sql);
	}

	if(isset($_POST['snsf-the-do-action']) && $_POST['snsf-the-do-action'] == 'edit'){
	//edit em
	echo "edit em!";	
	}
	
}
	
}

register_activation_hook(__FILE__,'snsf_install');
function snsf_install(){
#create database table	
$snsf_install_sql = "CREATE TABLE snsf_subscribe(
id int not null auto_increment,
subscriber varchar(50),
email varchar(50),
primary key(id)
);";

$snsf_install_sql_options = "CREATE TABLE snsf_subscribe_options(
option_name varchar(50),
option_value varchar(50),
primary key(option_name)
);";


if(mysql_query($snsf_install_sql) && mysql_query($snsf_install_sql_options)){
	if(mysql_query("REPLACE INTO snsf_subscribe_options(option_name,option_value) values('snsf-style','default.css')") && mysql_query("REPLACE INTO snsf_subscribe_options(option_name,option_value) values('snsf-custom-style','false')") ){
		
		return true;
		
	}
	}
else{
	return false;
}
	
}
### ENQUEUE FRONT END SCRIPTS
add_action('init','snsf_call_dependents');
function snsf_call_dependents(){
$snsf_style = mysql_query("SELECT option_value FROM snsf_subscribe_options WHERE option_name ='snsf-style' LIMIT 1;");
$snsf_style = mysql_fetch_row($snsf_style);
$snsf_style = $snsf_style[0];
wp_register_style('snsf_stylesheet',plugins_url('css/'.$snsf_style,__FILE__));
wp_register_script('snsf_javascript',plugins_url('simple-newsletter-signup.js',__FILE__),array('jquery'),'2.5.1');
}

add_action('wp_enqueue_scripts','snsf_scripts');
function snsf_scripts(){
wp_enqueue_script('snsf_javascript');	
wp_enqueue_style('snsf_stylesheet');
}
## ENQUEUE ADMIN SCRIPTS
add_action('init','snsf_call_admin_dependents');
function snsf_call_admin_dependents(){
wp_register_script('snsf_admin_javascript',plugins_url('simple-newsletter-signup-admin.js',__FILE__),array('jquery'),'2.5.1');
//wp_register_script('snsf_admin_colorpicker_region',plugins_url('third-party/colorpicker/js/ui.colorpicker-en.js',__FILE__));
//wp_register_script('snsf_admin_colorpicker',plugins_url('third-party/colorpicker/js/colorpicker.js',__FILE__));

//wp_register_style('snsf_admin_colorpicker_style',plugins_url('third-party/colorpicker/css/colorpicker.css',__FILE__));	
}

add_action('admin_print_styles','snsf_admin_styles');
function snsf_admin_styles(){
//wp_enqueue_style('snsf_admin_colorpicker_style');
}

add_action('admin_enqueue_scripts','snsf_admin_scripts');
function snsf_admin_scripts(){
wp_enqueue_script('snsf_admin_javascript');	
//wp_enqueue_script('snsf_admin_colorpicker_region');
//wp_enqueue_script('snsf_admin_colorpicker');

	
}
## END ENQUEUE ADMIN SCRIPTS


function simple_newsletter_menu_tlwm(){
	add_menu_page('Simple Newsletter Page','My Subscriptions','manage_options','simple-newsletter-signup','simple_newsletter_page');
	add_options_page( 'Settings', 'Settings', 'manage_options', 'simple-newsletter-signup', 'snsf_options_page' );
}

function snsf_options_page(){
if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
if(isset($_POST['snsf-style'])){	
	$change_style = "REPLACE INTO snsf_subscribe_options(option_name,option_value) VALUES('snsf-style','".mysql_real_escape_string($_POST['snsf-style'])."')";
	mysql_query($change_style);
	$current_style = $_POST['snsf-style'];
}
else{
	$current_style = "SELECT option_value FROM snsf_subscribe_options WHERE option_name = 'snsf-style'";
	$current_style = mysql_query($current_style);
	$current_style = mysql_fetch_row($current_style);
	$current_style = $current_style[0];
}
	
	?>
<div class="wrap">
<h3>Settings</h3>
<form name="snsf-style-form" method="post" action="">
<table>
<thead><tr><td>Theme</td><td> <select name="snsf-style">
<?php
if($snsf_open_css = opendir(dirname(__FILE__).'/css')){
while (false !== ($entry = readdir($snsf_open_css))) {
	if($entry !== '.' && $entry !== '..'){
 ?>
 <option value="<?php echo $entry;?>" <?php if($current_style == $entry){?> selected="selected" <?php } ?>><?php echo str_replace('.css','',ucfirst($entry));?></option>
 <?php       
    }
}
}
?>
</select></td><td> <input type="submit" class="button" value="Save" /></td></tr></table>
</form>

<!--
<form name="custom-styles" action="" method="post">
<table>
<thead><th colspan="2" align="left">Custom Style</th></thead>
<tr><td><label for="enable-custom-styles">Enable</label></td><td><input type="checkbox" name="enable-custom-styles" /></td>
<tr><td>Background Color</td><td><input name="background-color" type="text" class="colorpickerInput" /></td>
<tr><td>Font Color</td><td><input type="text" name="font-color"  class="colorpickerInput"  /></td>
<tr><td>Link Color</td><td><input type="text" name="link-color" class="colorpickerInput"  /></td>
<tr><td>Link Hover</td><td><input type="text" name="link-hover"  class="colorpickerInput"  /></td>
<tr><td></td><td><input type="submit" value="Save" class="button" /></td>
</table>
</form>

--->
</div>
    <?php
}

function simple_newsletter_page(){
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

if (@fopen(WP_PLUGIN_URL.'/simple-newsletter-signup/snsf_export.php', "r")){
	$snsf_allow_export = true;
	include('snsf_export.php');
}
else{
$snsf_allow_export = true;	
}

/* Instantiate class */

require_once('simple-newsletter-signup-pager.php');
$simple_p = new Simple_Newsletter_Signup_Pager;
 
/* Show many results per page? */
$simple_limit = 30;
 
/* Find the start depending on $_GET['page'] (declared if it's null) */
$simple_start = $simple_p->findStart($simple_limit);
 
/* Find the number of rows returned from a query; Note: Do NOT use a LIMIT clause in this query */
$simple_count = mysql_num_rows(mysql_query("SELECT id,subscriber,email FROM snsf_subscribe"));
 
/* Find the number of pages based on $count and $limit */
$simple_pages = $simple_p->findPages($simple_count, $simple_limit);
 
/* Now we use the LIMIT clause to grab a range of rows */
$simple_result = mysql_query("SELECT id,subscriber,email FROM snsf_subscribe LIMIT ".$simple_start.", ".$simple_limit);
//echo "SELECT post_title FROM wp_posts LIMIT ".$start.", ".$limit;
/* Now get the page list and echo it */
$simple_pagelist = $simple_p->pageList($_GET['start'], $simple_pages);


 
/* Or you can use a simple "Previous | Next" listing if you don't want the numeric page listing */
//$next_prev = $p->nextPrev($_GET['page'], $pages);
//echo $next_prev;
/* From here you can do whatever you want with the data from the $result link. */
	?>
<div class="wrap">
<h2>Simple Newsletter Signup</h2>

<div class="tablenav top"><br />
<div class="alignleft actions">
<select name="snsf-action-form" id="snsf-action-form">
<option value="0" selected="selected">Bulk Actions&nbsp;&nbsp;&nbsp;</option>
<option value='delete'>Delete</option>
<!--<option value='edit'>Edit</option>-->
</select>
<input type="submit" class="button-secondary action" name="snsf-perform-action" id="snsf-perform-action" value="Apply" />
</div>
<div class="tablenav-pages">
<span class="displaying-num"><?php echo $simple_count;?> items</span>
<span class="pagination-links"><?php echo $simple_pagelist;?></span>
</div>
</div><form name="snsf-actions-form" id="snsf-actions-form" method="post" action="">
<input type="hidden" name="snsf-the-do-action" id="snsf-the-do-action" />
<table class="wp-list-table widefat pages" cellpadding="2" cellspacing="">
  <thead>
    <tr>
      <th width="20" class="check-column"><input type="checkbox" id="snsf-all-checkboxes" /></th>
      <th>ID</th>
      <th width="300">Subscriber</th>
      <th>Email</th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th width="20"> </th>
      <th>ID</th>
      <th>Subscriber</th>
      <th>Email</th>
    </tr>
  </tfoot>
  <tbody>
    <?php // START the for each 
   if($simple_count < 1){
	?>
    <tr>
      <td colspan="4" align="center">There Are No Subscribers!</td>
    </tr>
    <?php 
   }
   else{
	   $snsf_check_the_rows = 0;
	 ?>
     
     <?php
    while($simple_rows = mysql_fetch_array($simple_result)){ ?>
    <tr <?php if($snsf_check_the_rows % 2 == 0){ ?>bgcolor="#999797"<?php } ?>>
      <td><input type="checkbox" name="snsf-checkbox-value[]" class="all-checkable" value="<?php echo $simple_rows[0];?>" /></td>
      <td><?php echo $simple_rows[0]; ?></td>
      <td><?php echo $simple_rows[1];?></td>
      <td><?php echo $simple_rows[2];?></td>
    </tr>
    <?php $snsf_check_the_rows++; } // END the for each
   } ?>
   
  </tbody>
</table></form>
<div class="tablenav bottom"><div class="alignleft actions"><form>
<select name="snsf_action">
<option value="0" selected="selected">Bulk Actions&nbsp;&nbsp;&nbsp;</option>
<option value='delete'>Delete</option>
</select>
<input type="submit" class="button-secondary action" name="snsf-perform-action" value="Apply" />
</form>
</div>
<div class="tablenav-pages">
<span class="displaying-num"><?php echo $simple_count;?> items</span>
<span class="pagination-links">
<?php echo $simple_pagelist;?>
</span>
</div>
</div>
</div>
<?php
}


add_action('init', 'snsf_sessions', 1);
function snsf_sessions() {
    if(!session_id()) {
        session_start();
    }
}



function simple_newsletter_signup_form_shortcode( $atts, $content=null, $code="" ) {
		
		if(isset($atts['title'])){
			$form_title = $atts['title'];
		}
		else{
		$form_title = '';	
		}
        
        if(isset($atts['sendto'])){
			$sendto = $atts['sendto'];
		}else{
            $sendto = false;
        }
		
		if(isset($atts['thanks'])){
			$snsf_thank_you = $atts['thanks'];
		}
		else{
			
			$snsf_thank_you ="Thank You!";
			
		}
	if(isset($atts['button'])){
			$button_title = $atts['button'];
		}
		else{
		$button_title = 'Subscribe';	
		}
		
if(isset($atts['terms'])){
			$terms_url = $atts['terms'];
		}
		else{
		$terms_url = false;	
		}		
if(isset($atts['terms_text'])){
			$terms_text = $atts['terms_text'];
		}
		else{
		$terms_text = 'I agree to the Terms & Conditions';	
		}						
		
		
		if(array_key_exists('snsf-subscriber-email',$_POST) && !empty($_POST['snsf-subscriber-email'])){ 
				
			if(!filter_var(htmlspecialchars($_POST['snsf-subscriber-email']), FILTER_VALIDATE_EMAIL)){
				return '<span class="snsf_error">Invalid Email Address</span>';
			}
				
				#Secure input
				
				if(isset($_POST['snsf-subscriber-name'])){
					$snsf_query_name = htmlspecialchars(mysql_real_escape_string($_POST['snsf-subscriber-name']));	
				}
				else{
					$snsf_query_name = '';	
				}
				
	
			$snsf_query_email = htmlspecialchars(mysql_real_escape_string($_POST['snsf-subscriber-email']));
			
			
			
			
			#Check if email exists			
			if(mysql_num_rows(mysql_query("SELECT * FROM snsf_subscribe WHERE email = '".$snsf_query_email."' LIMIT 1")) < 1){	
				$snsf_query = "INSERT into snsf_subscribe(subscriber,email) values ('".$snsf_query_name."','".$snsf_query_email."')";
			
				if(mysql_query($snsf_query)){ 
					if($_SESSION['snsf-sent'] = true){ 
                        if($sendto){
                            mail($sendto, 'new subscription to washalliance newsletter', 'email subscriber: '.$snsf_query_email);
                        }
						return $snsf_thank_you;
						}
					
				else{ 
					return false; 
					}
				}
			
		}// end if email already exists

		else{ // if email is already in database start session anyway and do not throw error.
			if($_SESSION['snsf-sent'] = true){ 
                if($sendto){
                            mail($sendto, 'new subscription to washalliance newsletter', 'email subscriber: '.$snsf_query_email);
                        }
						return $snsf_thank_you;
						}
					}
		}
		
		
		$list = '<div id="snsf-wrapper"><form method="post" name="simple-newsletter-signup-form" id="snsf-form"><table><thead><tr><th colspan="2">'.$form_title.'</th></tr></thead><tbody><tfoot><tr><td colspan="2"><input type="submit" value="'.$button_title.'"';
		if($terms_url != false){
		$list .= ' disabled ';	
		}
		$list.=' id="snsf-submit-button" /></td></tr></tfoot>';

		if(isset($atts['name'])){
		 	$list .= '<tr><td align="right"><label for="snsf-subscriber-name">Name:</label></td><td><input type="text" name="snsf-subscriber-name" /></td></tr>';
		}
		
		   // Email input
		   
		   $list .= '<tr><td align="right"><label for="snsf-subscriber-email">Email:</label></td><td><input type="text" name="snsf-subscriber-email" /></td></tr>';
		   if(isset($terms_url) and $terms_url != false){
$list .= '<tr><td class="checkid"><input type="checkbox" name="snsf-subscriber-terms" id="snsf-checkbox" /></td><td class="termstd"><label for="snsf-subscriber-terms"><small><a href="'.$terms_url.'" target="_blank">'.$terms_text.'</a></small></label></td></tr>';			   
		   }
		   $list .= '</table></form></div>';
          


		 
		/* CHECK IF SESSION IS SET AND DO SOMETHING */ 
		  #if($_SESSION['snsf-sent'] != true){ return $list; }
		  #else{ return; }
		/* END SESSION CHECK */
		return $list;
}// end simple_newsletter_signup_form_shortcode()

add_shortcode( 'simple_newsletter', 'simple_newsletter_signup_form_shortcode' );
function simple_newsletter_signout_form_shortcode( $atts, $content=null, $code="" ) {
		
		if(isset($atts['title'])){
			$form_title = $atts['title'];
		}
		else{
		$form_title = '';	
		}
        
        
		
		if(isset($atts['thanks'])){
			$snsf_thank_you = $atts['thanks'];
		}
		else{
			
			$snsf_thank_you ="Thank You!";
			
		}
	if(isset($atts['button'])){
			$button_title = $atts['button'];
		}
		else{
		$button_title = 'Unsubscribe';	
		}
		
				
		
		
		if(array_key_exists('snsf-subscriber-email',$_POST) && !empty($_POST['snsf-subscriber-email'])){ 
				
			if(!filter_var(htmlspecialchars($_POST['snsf-subscriber-email']), FILTER_VALIDATE_EMAIL)){
				return '<span class="snsf_error">Invalid Email Address</span>';
			}
				
				
				
	
			$snsf_query_email = htmlspecialchars(mysql_real_escape_string($_POST['snsf-subscriber-email']));
			
			
			
			
        #Check if email exists			
        if(mysql_num_rows(mysql_query("SELECT * FROM snsf_subscribe WHERE email = '".$snsf_query_email."' LIMIT 1")) >= 1){	
				$snsf_query = "DELETE FROM snsf_subscribe WHERE email = '".$snsf_query_email."'";
			
				if(mysql_query($snsf_query)){ 
					if($_SESSION['snsf-sent'] = true){ 
                        
						return $snsf_thank_you;
						}
					
				else{ 
					return false; 
					}
				}
			
		}// end if email already exists

		else{ // if email is not in database start session anyway and do not throw error.
			if($_SESSION['snsf-sent'] = true){ 
                
						return $snsf_thank_you;
						}
					}
		}
		
		
		$list = '<div id="snsf-wrapper"><form method="post" name="simple-newsletter-signup-form" id="snsf-form"><table><thead><tr><th colspan="2">'.$form_title.'</th></tr></thead><tbody><tfoot><tr><td colspan="2"><input type="submit" value="'.$button_title.'"';
		
		
		   // Email input
		   
		   $list .= '<tr><td align="right"><label for="snsf-subscriber-email">Email:</label></td><td><input type="text" name="snsf-subscriber-email" /></td></tr>';
		   
		   $list .= '</table></form></div>';
          


		 
		/* CHECK IF SESSION IS SET AND DO SOMETHING */ 
		  #if($_SESSION['snsf-sent'] != true){ return $list; }
		  #else{ return; }
		/* END SESSION CHECK */
		return $list;
}// end simple_newsletter_signup_form_shortcode()

add_shortcode( 'simple_newsletter_signout', 'simple_newsletter_signout_form_shortcode' );



// This function creates the do_shortcode for inclusion of simple_newsletter in your tempalte file.

//USEAGE
/*

<?php 
$snsf_args = array(
	"name"=>1,
	"title"=>"My New Title",
	"button"=>"My New Button",
	"terms" => "http://www.google.com",
	"terms_text" => "this is my terms text",
	"thanks" => "Thank you sooo much!"
	);
echo do_newsletter($snsf_args);
?>
*/
function do_newsletter($args){
	
	$snsf_shortcode = '[simple_newsletter';
	if(isset($args["name"])){
		$snsf_shortcode .= ' name="'.$args["name"].'"';
	}
	if(isset($args["title"])){
	$snsf_shortcode .= ' title="'.$args["title"].'"';	
	}

	if(isset($args["button"])){
	$snsf_shortcode .= ' button="'.$args["button"].'"';	
	}
	if(isset($args["terms"])){
	$snsf_shortcode .= ' terms="'.$args["terms"].'"';	
	}
	if(isset($args["terms_text"])){
	$snsf_shortcode .= ' terms_text="'.$args["terms_text"].'"';	
	}
	if(isset($args["thanks"])){
	$snsf_shortcode .= ' thanks="'.$args["thanks"].'"';	
	}
	$snsf_shortcode .= "]";
	return do_shortcode($snsf_shortcode);
}
?>