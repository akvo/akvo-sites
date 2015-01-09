<?php 
	/*
		Template Name: Local alliances Page
	*/
?>
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
                do_shortcode('[otw_is sidebar='.$sidebarTop.']');
            }
            ?>
            <div id="featured" class="post-wrapper">
                <div id="iDivMap" class="cDivFlexibleContainer col-md-6"></div>
                    <?php
                        $sCountry = get_post_meta($post->ID,'country',true);
                        $sCountry = ($sCountry=='none') ? '' : $sCountry;
                        $aCategories = wp_get_post_categories($post->ID);
                    if (function_exists('showMap')) {
                        showMap($sCountry);
                    }
                    ?>

                    <div class="featured-content col-md-6">
                        <h1 class="titles-featured">
                            <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(esc_attr__('Permanent Link to %s', 'Quadro'), the_title()) ?>">
                                <?php the_title(); ?>
                            </a>
                        </h1>
                        <?php  //truncate_post(510); //the_excerpt();
      //truncate_post(510);
                        echo get_the_excerpt();
                            //the_excerpt();
                        ?>
                        <div style="clear: both;"></div>


                    </div> <!-- end #featured-content -->
                    <br style="clear:both;" />
            </div>
            <div class="post-wrapper">
                <h2 class="page-header"><?php the_title() ;?></h2>
                <?php the_content(); ?>
                <?php wp_link_pages(); ?>
                <?php comments_template(); ?>
            </div>
        
        <?php endwhile; ?>
        <?php
                if($sCountry!='' || count($aCategories)>0){
               
            ?>
                <div id="projectupdates" class="fullwidth" >
            <?php 
            $tabOptions['showTabs']=true;
                $tabOptions['showUpdates']=true;
                $tabOptions['country']=$sCountry;
                sort($aCategories,SORT_NUMERIC);
                if(count($aCategories)>0)$tabOptions['categories']=$aCategories;
                get_template_part('includes/tabs');
                ?>
                </div>
        <?php } ?>
        <?php else: ?>

            <?php get_404_template(); ?>

        <?php endif; ?>

    </div>

    

</div>
<!-- end content container -->

<?php get_footer(); ?>
