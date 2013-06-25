<?php
/*
  Template Name: MARS Blog Page
 */
?>
<?php
$et_ptemplate_settings = array();
$et_ptemplate_settings = maybe_unserialize(get_post_meta($post->ID, 'et_ptemplate_settings', true));

$fullwidth = isset($et_ptemplate_settings['et_fullwidthpage']) ? (bool) $et_ptemplate_settings['et_fullwidthpage'] : false;

$et_ptemplate_blogstyle = isset($et_ptemplate_settings['et_ptemplate_blogstyle']) ? (bool) $et_ptemplate_settings['et_ptemplate_blogstyle'] : false;

$et_ptemplate_showthumb = isset($et_ptemplate_settings['et_ptemplate_showthumb']) ? (bool) $et_ptemplate_settings['et_ptemplate_showthumb'] : false;

$blog_cats = isset($et_ptemplate_settings['et_ptemplate_blogcats']) ? (array) $et_ptemplate_settings['et_ptemplate_blogcats'] : array();
$et_ptemplate_blog_perpage = isset($et_ptemplate_settings['et_ptemplate_blog_perpage']) ? (int) $et_ptemplate_settings['et_ptemplate_blog_perpage'] : 10;
                ?>

<?php get_header(); ?>

<div id="container">
<div id="iDivBreadcrumb">
		<?php the_breadcrumbs();?>
		</div>
	


	<div id="container2">

        <?php
        $args['post_type']='post';
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
            echo get_previous_posts_link();
        }
        if(get_next_posts_link()){
            echo get_next_posts_link();
        }
        
        ?>
        </div>
	</div> <!-- end #container2 -->


</div> <!-- end #container -->
<?php get_footer(); ?>
</body>
</html>





