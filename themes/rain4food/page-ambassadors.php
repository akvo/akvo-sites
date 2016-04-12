<?php 
	/*
		Template Name: Ambassadors Page
	*/
?>
<?php get_header(); ?>

<?php get_template_part('template-part', 'head'); ?>

<?php get_template_part('template-part', 'topnav'); ?>


<!-- start content container -->
<div class="row dmbs-content">

    

    <div class="col-md-12 dmbs-main">
        <?php 
        //display ambassador areas
        for($i=1;$i<=3;$i++){
            ?>
        <div class="cDivAmbassadorArea cDivAmbassadorArea<?php echo $i; ?>">
            <?php dynamic_sidebar('ambassadors-area-'.$i);?>
            <?php if($i===3){ ?>
            <br style="clear:both;" />
            <?php } ?>
        </div>
            <?php
        }
        ?>
        
        <?php // theloop
        if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            
            
            <div class="post-wrapper ambassadors col-md-8">
                <h2 class="page-header"><?php the_title() ;?></h2>
                <?php the_content(); ?>
                <?php wp_link_pages(); ?>
                <?php comments_template(); ?>
            </div>
        <?php endwhile; ?>
        <?php else: ?>

            <?php get_404_template(); ?>

        <?php endif; ?>
        <div class="col-md-4 dmbs-right cDivAmbassadorDownloads">
            <?php dynamic_sidebar('ambassadors-downloads-area');?>
        </div>
    </div>

    

</div>
<?php //dynamic_sidebar('ambassadors-downloads-area');?>
<!-- end content container -->

<?php get_footer(); ?>
