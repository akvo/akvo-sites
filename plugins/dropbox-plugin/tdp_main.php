<?php
/*
Plugin Name: The dropbox plugin
Plugin URI: http://software.o-o.ro/dropbox-plugin-for-wordpress/
Description: Dropbox in wordpress. This version is held together with duct tape and chewing gum, but it works.
Version: 0.105
Author: Andrew M
Author URI: http://software.o-o.ro


READ THIS:
Abandon all hope ye who enter here.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.*/

function tdp_options()
{
    add_options_page("The Dropbox Plugin", "TDP Options", 'activate_plugins', 'dropbox-plugin/tdp_options.php');
}
add_action('admin_menu', 'tdp_options');
add_action( 'admin_init', 'tdp_init' );
add_action( 'wp_footer' , 'tdp_link' );
add_shortcode('dropbox', 'show_dropbox');
add_action('init', 'tdp_elephant_sandwitch');



function tdp_init(){
	register_setting( 'tdp-opt', 'tdp_mail' );
	register_setting( 'tdp-opt', 'tdp_pass' );
	register_setting( 'tdp-opt', 'tdp_dir' );
	register_setting( 'tdp-opt', 'tdp_cred' );
	register_setting( 'tdp-opt', 'tdp_date' );
	register_setting( 'tdp-opt', 'tdp_size' );

}

function tdp_link()
{if(get_option('tdp_cred')!=1){echo'<a href="http://software.o-o.ro" alt="Software, projects and code"> <img src="';bloginfo('wpurl');echo '/wp-content/plugins/dropbox-plugin/cred.jpg"> </a>'; }
}

function gettill($string,$separator)
{return substr(strrchr($string, $separator), 1);}


function fixspaces($string)
{return str_replace("%2F", "/", rawurlencode($string));}

function array_orderby()
{
    $args = func_get_args();
    $data = array_shift($args);
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            foreach ($data as $key => $row)
                $tmp[$key] = $row[$field];
            $args[$n] = $tmp;
            }
    }
    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    return array_pop($args);
}

function placenames($fh,$itm,$alnav,$aldw,$pnme_sepa){
$ou="";
$temp=str_ireplace($fh,"",$itm["path"]);
  $ou.="<td>";
  if(($itm["is_dir"] && $alnav=="true")||(!$itm["is_dir"] && $aldw=="true"))
	$ou.="<a href='".$pnme_sepa."g=".$temp."'>".trim(strrchr($temp, "/"),"/")."</a>";
  else $ou.=trim(strrchr($temp, "/"),"/");
$ou.="</td>";
return $ou;}

function showicon($thing)
{$temp=""; $pth=dirname(__FILE__) . "/images";$pth2=WP_PLUGIN_URL ."/dropbox-plugin/images";
if ($thing['is_dir']){
if(file_exists($pth."/folder.png")) 
$temp.= "<img src='$pth2/folder.png' />";
}
  else{$ext=gettill($thing["path"], ".");
if(file_exists($pth."/".$ext.".png")) 
$temp.= "<img src='$pth2/" . $ext . ".png' />";
 else if(file_exists($pth."/default.png")) $temp.= "<img src='$pth2/default.png' />";
}

return $temp;
}

function tdp_elephant_sandwitch()
{session_start();
$allowdownload=$_SESSION['tdp_allowdownload'];
$allownavigate=$_SESSION['tdp_allownavigate'];
if(isset($_GET['g'])&&($allowdownload=="true")&&($allownavigate=="true"||($allownavigate="false" && strrpos($_GET['g'],"/")==0)))
{$sometemp=$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
$firsttemp='g=';
$secondtemp=strpos($sometemp, $firsttemp);
if($secondtemp!==false)$anothertemp=substr($sometemp, 0, $secondtemp-1);
else $anothertemp=$sometemp;
$lasttemp=strpos($_SESSION['tdp_check'], $firsttemp);
if($lasttemp!==false)$lasttemp=substr($_SESSION['tdp_check'], 0, $lasttemp-1);
else $lasttemp=$_SESSION['tdp_check'];

if($lasttemp!=$anothertemp)die("if you see this you were probably not playing nice, if you were, this error comes from the dropbox plugin");
$consumerKey=get_option('tdp_mail');
$consumerSecret=get_option('tdp_pass');
$forcedhome=$_SESSION['tdp_forcedhome'];
require_once'dropbox.php';
$dropbox = new Dropbox($consumerKey, $consumerSecret);
if(get_option('tdp_tokens')){
$temptok=get_option('tdp_tokens');
$dropbox->setOAuthToken($temptok['oauth_token']);
$dropbox->setOAuthTokenSecret($temptok['oauth_token_secret']);

 $gimmeit=$forcedhome.$_GET['g'];

  
 $info = $dropbox->metadata("$gimmeit",1000,false,true,false);
   $filename=gettill($gimmeit,"/");
   
if (!$info['is_dir']){
  header("Content-Type: ".$info["mime_type"]);
  header("Content-Disposition: attachment; filename=\"".$filename."\"");
 
 $temp=$dropbox->filesGet("$gimmeit",false);
echo base64_decode($temp["data"]);
exit();}


}

}}

function show_dropbox($atts)
 {
extract(shortcode_atts(array(
'home'=>'',
'separator'=>'~',
'hometext'=>'home',
'dirsfirst'=>'true',
'orderby'=>'path',
'orderdir'=>-1,
'showdirs' => "true",
'dateformat'=>'l jS \of F Y h:i:s A',
'allowdownload'=>"true", 
'allownavigate'=>"true",
'happyerror'=>'Something exploded! Refresh or go back and try again',
'columns'=>'INDSD',
'asctxt'=>'&#x25b2;',
'desctxt'=>'&#x25bc;',
'ordering'=>"true",
'allowupload'=>"false")
, $atts));

$forcedhome=$home;

$happyerror=$happyerror;
$letuserplaywithdetails=$ordering;

$_SESSION['tdp_forcedhome']=$forcedhome;
$_SESSION['tdp_allowdownload']=$allowdownload;
$_SESSION['tdp_allownavigate']=$allownavigate;
$_SESSION['tdp_check']=$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];


$output="";
$consumerKey=get_option('tdp_mail');
$consumerSecret=get_option('tdp_pass');
$pagename="";

$pagename=get_bloginfo('siteurl').$_SERVER['REQUEST_URI'];
//$pagename = get_bloginfo('siteurl');
if($tdp_sps=strrpos($pagename,"?g="))$pagename=substr($pagename,0,$tdp_sps);
else if($tdp_sps=strrpos($pagename,"&g="))$pagename=substr($pagename,0,$tdp_sps);
if(!strpos($pagename,"?"))$tdp_fancysep="?";
else $tdp_fancysep="&";


require_once'dropbox.php';
$dropbox = new Dropbox($consumerKey, $consumerSecret);
if(get_option('tdp_tokens')){
$temptok=get_option('tdp_tokens');
$dropbox->setOAuthToken($temptok['oauth_token']);
$dropbox->setOAuthTokenSecret($temptok['oauth_token_secret']);


if(isset($_GET['g'])&&($allowdownload=="true" || $allownavigate=="true"))
{ $gimmeit=$forcedhome.$_GET['g'];
 $info = $dropbox->metadata("$gimmeit",1000,false,true,false);
 $filename=gettill($gimmeit,"/");




 if($allownavigate=="true")
$where=$_GET["g"];
else $output.=$happyerror;
}
else $where="/";



if($allownavigate=="true"){
$allthedirs=explode("/",$where);
$output.="<a href='$pagename'>$hometext</a>$separator";

$thesemany=sizeof($allthedirs);   
if($where!='/')for ($i=1;$i<$thesemany;$i++)
{$output.="<a href='$pagename";
 
$output.=($tdp_fancysep."g=");
$temp=$allthedirs[$i];
 $allthedirs[$i]=$allthedirs[$i-1]."/".$allthedirs[$i];

$output.=$allthedirs[$i]; 
$output.="'>$temp</a>$separator";

}
}
$output.="<table border='1'>";
$stuff=$dropbox->metadata($forcedhome.$where,1000,false,true,false);
$stuff=$stuff["contents"];

foreach($stuff as &$thing )
{
 
$thing['modified']=strtotime($thing['modified']);}


if(isset($_GET['o'])&& isset($_GET['d'])&& $ordering =="true")
{
switch($_GET['o'])
{case "date":$orderby="modified";break;
 case "size": $orderby="bytes"; break;
 case "type":$orderby="mime_type";break;
 default: $orderby="path"; break;
}

switch($_GET['d'])
{case "asc":$orderdir=-1;break;
 default: $orderdir=1; break;
}

}
else #you know I wonder if anybody will read this
switch($orderby)
{case "date":$orderby="modified";break;
 case "size": $orderby="bytes"; break;
 case "type":$orderby="mime_type";break;
 default: $orderby="path"; break;
}


if ($showdirs=="true" && $dirsfirst=="true") 
		if($orderdir==-1) $stuff=array_orderby( $stuff,'is_dir', SORT_DESC, $orderby, SORT_ASC);
		else $stuff=array_orderby($stuff,'is_dir', SORT_DESC, $orderby, SORT_DESC);
		
	else 
	if($orderdir==-1)$stuff=array_orderby($stuff,$orderby,SORT_ASC); 
	else $stuff=array_orderby($stuff,$orderby,SORT_DESC); 
	


$columnssplit=str_split($columns);

if($letuserplaywithdetails=="true"){$output.="<tr>";
foreach ($columnssplit as $column){
switch ($column){
case "N":$output.="<td><a href='".$pagename.$tdp_fancysep."g=$where&o=name&d=asc'>$asctxt</a><a href='".$pagename.$tdp_fancysep."g=$where&o=name&d=dsc'>$desctxt</a></td>";break;
case "S":$output.="<td><a href='".$pagename.$tdp_fancysep."g=$where&o=size&d=asc'>$asctxt</a><a href='".$pagename.$tdp_fancysep."g=$where&o=size&d=dsc'>$desctxt</a></td>";break;
case "D":$output.="<td><a href='".$pagename.$tdp_fancysep."g=$where&o=date&d=asc'>$asctxt</a><a href='".$pagename.$tdp_fancysep."g=$where&o=date&d=dsc'>$desctxt</a></td>";break;
case "I":$output.="<td><a href='".$pagename.$tdp_fancysep."g=$where&o=type&d=asc'>$asctxt</a><a href='".$pagename.$tdp_fancysep."g=$where&o=type&d=dsc'>$desctxt</a></td>";break;
}
}
$output.="</tr>";
}
foreach ( $stuff as $item)
{ if(($item["is_dir"] && $showdirs=="true")||!$item["is_dir"]){
  $output.="<tr>";
foreach ($columnssplit as $column){
switch ($column){
case "N":$output.=placenames($forcedhome,$item,$allownavigate,$allowdownload,$pagename.$tdp_fancysep);break;
case "S":$output.="<td>".$item["size"]."</td>";break;
case "D":$output.="<td>".date($dateformat,$item["modified"])."</td>";break;
case "I":$output.="<td>".showicon($item)."</td>";break;
}
  
}

$output.="</tr>";
}
}

$output.="</table>";

if($allowupload=="true")
{$output.=' <br/><form method="post" action="" enctype="multipart/form-data">
        				
            <input type="file" name="file" /><input type="submit" value="Upload" />
       </form>';
tdp_upload($forcedhome.$where);

}
}
else $output="Don't forget to connect the plugin to your dropbox";



return $output;
}


function tdp_upload($targetdir)
{ini_set('memory_limit', '32M');
if(isset($_FILES['file']['name']))
 {
   $consumerKey=get_option('tdp_mail');
$consumerSecret=get_option('tdp_pass');

require_once'dropbox.php';
$dropbox = new Dropbox($consumerKey, $consumerSecret);
$temptok=get_option('tdp_tokens');
$dropbox->setOAuthToken($temptok['oauth_token']);
$dropbox->setOAuthTokenSecret($temptok['oauth_token_secret']);

	
    try {
        if ($_FILES['file']['error'] !== UPLOAD_ERR_OK)
            throw new Exception('File was not successfully uploaded from your computer.');
       
        $tmpDir = uniqid('/tmp/theDdropboxPlugin-');
        if (!mkdir($tmpDir))
            throw new Exception('Can not create temporary directory!');
       
        if ($_FILES['file']['name'] === "")
            throw new Exception('File name error.');
           $tmpFileName=str_replace("/\0", '_', $_FILES['file']['name']);
        $tmpFile = $tmpDir.'/'.$tmpFileName;
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $tmpFile))
            throw new Exception('Can not rename uploaded file');
       
        
        $supertemp=($dropbox->filesPost($targetdir, $tmpFile));
        
        echo '<span style="color: green">File successfully uploaded to your Dropbox!</span><br/>';
    } catch(Exception $e) {
        echo '<span style="color: red">Error: ' . htmlspecialchars($e->getMessage()) . '</span>';
    }
   
    if (isset($tmpFile) && file_exists($tmpFile))
        unlink($tmpFile);
       
    if (isset($tmpDir) && file_exists($tmpDir))
        rmdir($tmpDir);
}


}

?>
