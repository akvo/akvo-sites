<?php 
/*
Template Name: Wiki Page
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
        <div class="post-wrapper">
            <h2 id="akvopedia-title"></h2>
            <a href="#" id="akvopedia-home-link">Back to Rainwater Harvesting portal</a>
            <div id="embedded-akvopedia"><noscript><iframe style="position: absolute; top: 4em; right:1em; left:1em; bottom:1em; height:90%; width:97%;" src="http://www.akvopedia.org/wiki/Rainwater_Harvesting"></iframe></noscript></div>

        </div>
    </div>

    

</div>
<!-- end content container -->

<?php get_footer(); ?>
<script src="http://akvopedia.org/resources/akvopedia-gadget/akvopedia-gadget.js"></script>
<script>
  //<!--
    (function($, document) {
		$(document).ready(function () {
                    $('#embedded-akvopedia').akvopedia({page: 'Rainwater Harvesting', addBackAndForwardButtons: false,scrollToElement: $($('#embedded-akvopedia').get(0).parentNode)});
                    $('#embedded-akvopedia').on('akvopedia:title-updated', function(event, title) {
                        $('#akvopedia-title').html(title);
                        //$('#embedded-akvopedia .thumb.tright > div').append('<br style="clear:both;" />' );
                        $('#akvopedia-home-link').click(function(e) {
                            e.preventDefault();
                            $('#embedded-akvopedia').akvopedia('page', 'Rainwater Harvesting');
                        });
//                    $('#embedded-akvopedia td').each(function(i){
//                        $(this).attr('style','');
//                    });
//                    $('#embedded-akvopedia div,#embedded-akvopedia td').each(function(i){
//                        console.log($(this));
//                        $(this).css('background-color','#FFF');
//                        console.log($(this).css('background-color'));
//                    });
			//$('#akvopedia-title').html(title);
		    });
		});
    })(jQuery, document);
  //-->
</script>