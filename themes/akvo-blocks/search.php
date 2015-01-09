

<?php get_header(); ?>

<?php get_template_part('template-part', 'head'); ?>

<?php get_template_part('template-part', 'topnav'); ?>


<!-- start content container -->
<div class="row dmbs-content">

    

    <div class="col-md-12 dmbs-main">

        <?php // theloop
        if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            <?php
            get_template_part('includes/entry');
            ?>
           
        <?php endwhile; ?>
        <?php else: ?>

            <?php get_404_template(); ?>

        <?php endif; ?>

    </div>

    

</div>
<!-- end content container -->

<?php get_footer(); ?>
