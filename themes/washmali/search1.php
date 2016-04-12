<?php get_header(); ?>

<?php get_template_part('template-part', 'head'); ?>

<?php get_template_part('template-part', 'topnav'); ?>

<div class="col-md-12 cDivMapandDataPage cBackgroundWhite cGrayBorderAllSide">
	<?php
		$sKeyword = $_GET['s'];
		
		$query = new WP_Query( 's='.$sKeyword );
		if ( $query->have_posts() ) : ?>
		<div class="cDivSearchResult">
		<?php while ( $query->have_posts() ) : $query->the_post(); ?>
			<div class="cDivSearchResultItem">
				<?php the_title(); ?>
			</div>
		<?php endwhile; ?>
		</div>
		<?php endif; ?>
	
</div>