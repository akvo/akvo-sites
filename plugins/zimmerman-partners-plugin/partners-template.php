<?php 
	/*
		Template Name: Partners Page
	*/
?>
<?php get_header(); ?>

<?php get_template_part('template-part', 'head'); ?>
<?php get_template_part('template-part', 'topnav'); ?>



<?php // theloop
    if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>


<div class="row dmbs-content">
    <div class="col-md-12 dmbs-main">
        <div class="post-wrapper">
            <h2 class="page-header"><?php the_title(); ?></h2>
            <?php the_content(); ?>
        </div>  
    </div>
</div>

<!-- start content container -->
<div class="row dmbs-content">
    <div class="col-md-12 dmbs-main">

        <?php echo do_shortcode("[zz-partner-map]"); ?>

    </div>
</div>

<?php endwhile; ?>
<?php else: ?>

    <?php get_404_template(); ?>

<?php endif; ?>

<!-- end content container -->
<?php get_footer(); ?>
