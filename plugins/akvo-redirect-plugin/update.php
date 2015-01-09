<html><?php
 
require_once(dirname( __FILE__ ) . '../../../../wp-blog-header.php');
 $options = get_option('akvoredirect_opts');
$formdata=$_POST;
if(isset($formdata) && count($formdata)>0){
   
    update_option('akvoredirect_opts', $formdata);
    //echo '1';
}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
</html>
