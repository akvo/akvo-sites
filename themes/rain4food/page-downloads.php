<?php get_header(); ?>

<?php get_template_part('template-part', 'head'); ?>

<?php get_template_part('template-part', 'topnav'); ?>

<!-- start content container -->
<div class="row dmbs-content">
    <div class="col-md-12 dmbs-main">
		<div class="post-wrapper">
			<?php 
			if ( have_posts() ) :
				while ( have_posts() ) : the_post();
					the_content();
				endwhile;
			endif; 
			?>

			<?php //dynamic_sidebar('ambassadors-downloads-area');?>	
		</div>
	</div>
</div>

<?php get_footer(); ?>