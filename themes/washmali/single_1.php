<?php get_header(); ?>

<?php get_template_part('template-part', 'head'); ?>

<?php get_template_part('template-part', 'topnav'); ?>



<?php 
echo $post->post_type; 
var_dump(wp_get_post_categories($post->ID)); 
?>