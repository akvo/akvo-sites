<?php require '../../../wp-blog-header.php';
if(current_user_can("activate_plugins")){
require_once 'dropbox.php';

session_start();
$consumerKey=get_option('tdp_mail');
$consumerSecret=get_option('tdp_pass');
$dropbox = new Dropbox($consumerKey, $consumerSecret);





if (isset($_SESSION['tdp_progress']))  $progress = $_SESSION['tdp_progress'];
else     $progress = 1;

switch($progress) {
    case 1 : $temp=$dropbox->oAuthRequestToken();
     $_SESSION['tdp_progress'] = 2;$_SESSION['tdp_tokens'] = $temp;
//$tdp_tokens = $oauth->getRequestToken();
$here = 'http' . ((!empty($_SERVER['HTTPS'])) ? 's' : '') . "://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
$dropbox->oAuthAuthorize($temp['oauth_token'],$here);

       
        die();
    case 2 :
    $tmp=$_SESSION['tdp_tokens'];
$dropbox->setOAuthTokenSecret($tmp["oauth_token_secret"]);
    $tokens=$dropbox->oAuthAccessToken($tmp['oauth_token']);

      $_SESSION['tdp_tokens'] = $tokens;
        
 
      delete_option("tdp_tokens");
      add_option("tdp_tokens", $_SESSION['tdp_tokens'], '', 'no');
unset($_SESSION['tdp_progress']);
        echo'<br/> Everything should be working now. You may close this window :)

<script language="JavaScript" type="text/javascript">
  window.close();
</script>';
        break;
default:unset($_SESSION['tdp_progress']); break;
}
}




?>
