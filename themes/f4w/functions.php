<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function akvo_project_domain(){
    add_option( 'akvo_project_domain', 'http://f4winternational.akvoapp.org/en');
    
}
add_action('init', 'akvo_project_domain');
?>
