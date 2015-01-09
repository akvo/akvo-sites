<?php 
	/*
		Template Name: Project update Page
	*/
?>
<?php get_header(); ?>

<?php get_template_part('template-part', 'head'); ?>

<?php get_template_part('template-part', 'topnav'); ?>


<!-- start content container -->
<div class="row dmbs-content">

    

    <div class="col-md-12 dmbs-main">

        <?php
        $args['post_type']='project_update';
        $args['posts_per_page']='12';
        global $wp_query;
        $page = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $temp = $wp_query;
        $wp_query = null;
        $wp_query = new WP_Query();
        $args['paged']=$page;
        $posts = $wp_query->query($args);
        if ( $posts ) : foreach ($posts AS $post ) : 
            get_template_part('includes/entry');
        endforeach;endif;
        ?>
        <br style="clear:both;" />
        <div class="pagination">
		<?php 
        
        if(get_previous_posts_link()){
            $slabel = (get_current_blog_id()==11) ? '&laquo; Vorige Pagina' : '&laquo; Previous Page' ;
            echo get_previous_posts_link($slabel);
        }
        if(get_next_posts_link()){
            $slabel = (get_current_blog_id()==11) ? 'Volgende Pagina &raquo;' : 'Next Page &raquo;' ;
            echo get_next_posts_link($slabel);
        } 
        
        ?>

    </div>

    

</div>
<!-- end content container -->

<?php get_footer(); ?>
