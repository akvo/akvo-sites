<?php 
	/*
		Template Name: Fullwidth Sidebars Page
	*/
?>
    <?php get_header(); ?>

<?php get_template_part('template-part', 'head'); ?>

<?php get_template_part('template-part', 'topnav'); ?>


<!-- start content container -->
<div class="row dmbs-content">

    <?php //left sidebar ?>
    <?php get_sidebar( 'left' ); ?>

    <div class="col-md-12">
        
        <?php // theloop
        if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            <?php
            $sidebarTop = AkvoBlocks_Sidebars_Meta::getValue('top');
            if($sidebarTop){
                ?>
        <div class="dynamicSideBarTop <?php echo $sidebarTop ;?> clearfix">
            <?php echo do_shortcode('[otw_is sidebar='.$sidebarTop.']'); ?>
        </div>
             <?php   
            }
            ?>
    </div>
        <div class="col-md-12 dmbs-main">

            <div class="post-wrapper">
                <h2 class="page-header"><?php the_title() ;?></h2>
                <?php the_content(); ?>
                <?php wp_link_pages(); ?>
                <?php comments_template(); ?>
            </div>
            </div>
        <?php endwhile; ?>
        <?php else: ?>

            <?php get_404_template(); ?>

        <?php endif; ?>

    

    <?php //get the right sidebar ?>
    

</div>
<!-- end content container -->

<?php get_footer(); ?>
