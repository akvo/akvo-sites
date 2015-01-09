<?php get_header(); ?>

<div id="container">
	<div id="container2">
		<div id="left-div">
			<?php
			global $wp_query;
			$args = array_merge($wp_query->query_vars, array('posts_per_archive_page'=>6,'post_type' => array('post','project_update','news','page')));
            
			//query_posts($args);
            $wp_query = new WP_Query($args);
            //akvo_debug_dump($wp_query->query);
			if (have_posts()) : while (have_posts()) : the_post();
					?>
					<?php get_template_part('includes/entry'); ?>
				<?php endwhile; ?>
				<div style="clear: both;"></div>
				<?php if (function_exists('wp_pagenavi')) {
					wp_pagenavi();
				} else {
					?>
					<?php get_template_part('includes/navigation'); ?>
				<?php } ?>
			<?php else : ?>
			<?php get_template_part('includes/no-results'); ?>
		<?php endif; ?>
		</div> <!-- end #left-div -->
<?php get_sidebar(); ?>
	</div> <!-- end #container2 -->



</div> <!-- end #container -->
<?php get_footer(); ?>
</body>
</html>