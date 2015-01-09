<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$subRole = get_role( 'subscriber' );
$subRole->add_cap( 'read_private_pages' );


function akvo_project_domain(){
    add_option( 'akvo_project_domain', 'http://simavi.akvoapp.org/en');
}
add_action('init', 'akvo_project_domain');


?>
