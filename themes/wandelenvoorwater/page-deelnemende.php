<?php
/*
Template Name: Special posts Page
*/
?>
<?php
$et_ptemplate_settings = array();
$et_ptemplate_settings = maybe_unserialize( get_post_meta($post->ID,'et_ptemplate_settings',true) );

$fullwidth = isset( $et_ptemplate_settings['et_fullwidthpage'] ) ? (bool) $et_ptemplate_settings['et_fullwidthpage'] : false;

$et_ptemplate_blogstyle = isset( $et_ptemplate_settings['et_ptemplate_blogstyle'] ) ? (bool) $et_ptemplate_settings['et_ptemplate_blogstyle'] : false;

$et_ptemplate_showthumb = isset( $et_ptemplate_settings['et_ptemplate_showthumb'] ) ? (bool) $et_ptemplate_settings['et_ptemplate_showthumb'] : false;

$blog_cats = isset( $et_ptemplate_settings['et_ptemplate_blogcats'] ) ? (array) $et_ptemplate_settings['et_ptemplate_blogcats'] : array();
$et_ptemplate_blog_perpage = isset( $et_ptemplate_settings['et_ptemplate_blog_perpage'] ) ? (int) $et_ptemplate_settings['et_ptemplate_blog_perpage'] : 10;
?>

<?php get_header(); ?>

<div id="container">
    <div id="iDivBreadcrumb">
		<?php the_breadcrumbs();?>
		</div>
	<div id="container2">
		<div id="left-div">
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<!--div class="post-wrapper <?php // if($fullwidth) echo (' no_sidebar"');?>"-->
				<div class="post-wrapper no_sidebar">
					<h1 class="titles">
						<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(esc_attr__('Permanent Link to %s','Quadro'), the_title()) ?>">
							<?php the_title(); ?>
						</a>
					</h1>
					<div style="clear: both;"></div>
					<?php the_content(); ?>


					<?php
					//echo do_shortcode('[uthpala]');
					?>

				</div> <!-- end .post-wrapper -->
			<?php endwhile; endif; ?>

		</div> <!-- end #left-div -->
        <?php //if (!$fullwidth) get_sidebar(); ?>
	</div> <!-- end #container2 -->


</div> <!-- end #container -->
<?php get_footer(); ?>
</body>
</html>