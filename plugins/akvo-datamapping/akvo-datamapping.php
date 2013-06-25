<?php
/*
  Plugin Name: akvo-datamapping
  Version: 1.0
  Author: Eveline Sparreboom
  Description: This plugin will display graph data.
 *
 */
require_once 'classes/akvoDataMapping.php';

///enable add categories to pages

add_action('admin_init', 'reg_tax');
function reg_tax() {
    register_taxonomy_for_object_type('category', 'page');
    add_post_type_support('page', 'category');
}




?>