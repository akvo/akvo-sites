<?php
require_once '../../../wp-config.php';
$tabOptions=$_GET;
$tabOptions['showTabs']=0;
$tabOptions['doquery']=true;
get_template_part('includes/tabs');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
