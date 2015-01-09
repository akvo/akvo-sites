<?php get_header(); ?>

<?php get_template_part('template-part', 'head'); ?>

<?php get_template_part('template-part', 'topnav'); ?>


<!-- start content container -->
<div class="row dmbs-content">

    

    <div class="col-md-12 dmbs-main">

        <?php // theloop
        if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            <?php
            $sidebarTop = AkvoBlocks_Sidebars_Meta::getValue('top');
            $sidebarRight = AkvoBlocks_Sidebars_Meta::getValue('right');
            if($sidebarTop){
                echo do_shortcode('[otw_is sidebar='.$sidebarTop.']');
            }
            ?>
            <div class="post-wrapper">
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
