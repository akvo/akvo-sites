<?php 
	/*
		Template Name: Ambassadors Video Page
	*/
?>
<?php get_header(); ?>

<?php get_template_part('template-part', 'head'); ?>

<?php get_template_part('template-part', 'topnav'); ?>


<!-- start content container -->
<div class="row dmbs-content">

    

    <div class="col-md-12 dmbs-main">
        
        <div class="cDivAmbassadorArea cDivAmbassadorArea1">
            <?php dynamic_sidebar('ambassadors-area-1');?>
            <br style="clear:both;" />
        </div>
            
        
        <?php // theloop
        if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            
            
            <div class="post-wrapper ambassadorvideos">
                <h2 class="page-header"><?php the_title() ;?></h2>
                <?php the_content(); ?>
                <?php wp_link_pages(); ?>
                <?php comments_template(); ?>
            </div>
        <?php endwhile; ?>
        <?php else: ?>

            <?php get_404_template(); ?>

        <?php endif; ?>
        
    </div>

    

</div>
<!-- end content container -->

<?php get_footer(); ?>
