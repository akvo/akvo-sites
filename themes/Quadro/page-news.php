<?php
/*
Template Name: News Page
*/
?>
<?php get_header(); ?>

<div id="container">
	<div id="container2">
		<div id="left-div">

    <?php
global $wp_query;
		query_posts(array('post_type'=> 'news','posts_per_page'=>0));
        if ( have_posts() ) {
            while ( have_posts() ) {
                the_post();
                ?>
<div id="iDivNewsBox">
<div class="newswidget">
<?php
                $classtext = 'no-border';
                $titletext = get_the_title();
                
                $thumbnail = get_thumbnail(271,167,$classtext,$titletext,$titletext,true,'Featured');
                $thumb = $thumbnail["thumb"];
                if($thumb==''){
                    $thumb = catch_that_image();
                }
                $thumb = '/wp-content/plugins/akvo-site-config/classes/thumb.php?src='.$thumb.'&w=271&h=167&zc=1&q=100';
                //$thumb = get_field('logo');
                $sLink = get_field('url'); ?>
                <div class="cDivNewsPostImageTag"></div>
                <div class="cDivNewsImage">
                <?php if($thumb <> '') { ?>
                    <div class="cDivBlogPostImageWrapper">
                                            <div class="cDivBlogPostImage">
                                            
                        <a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>">
							<img src="<?php echo $thumb; ?>" />
                        </a>
                    </div>
                                        </div>
                <?php }; ?>

                 </div>
        <div class="cDivNewsText">
            <h2><a href="<?php the_permalink();?>"><?php the_title(); ?></a></h2>
            <div class="cDivBlogPostDate">
                <?php echo date('M d, Y',  strtotime(get_the_date())); ?>
            </div>
            <?php the_excerpt(); ?>
            <div class="readmore">
				<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(esc_attr__('Permanent Link to %s','Quadro'), the_title()) ?>"><?php esc_html_e('Read More','Quadro'); ?></a>
			</div>
        </div>
        <br style="clear:both" />
</div>
    </div>
                <?php  
            }
            }?>
        

			

			
			
		</div> <!-- end #left-div -->
<?php get_sidebar(); ?>
	</div> <!-- end #container2 -->



</div> <!-- end #container -->
<?php get_footer(); ?>
</body>
</html>