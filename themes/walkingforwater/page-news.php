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
$page = (get_query_var('paged')) ? get_query_var('paged') : 1;
        
		query_posts(array('post_type'=> 'news','posts_per_page'=>10,'paged'=>$page));
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
                $thumb = preg_replace('/(\w+).akvofoundation.org/i', 'akvofoundation.org', $thumb);
                if($thumb==''){
                    $thumb = catch_that_image();
                }
                if($thumb!=''){
                $thumb = '/wp-content/plugins/akvo-site-config/classes/thumb.php?src='.$thumb.'&w=271&h=167&zc=1&q=100';
                }else{
                    $thumb = '/wp-content/themes/wandelenvoorwater/images/wvw_defaultpostimage.png';
                }
                
                $sLink = get_field('url'); ?>
                <div class="cDivNewsPostImageTag"></div>
                <div class="cDivNewsImage">
                
                    <div class="cDivBlogPostImageWrapper">
                        <?php if($thumb <> '') { ?>
                                            <div class="cDivBlogPostImage">
                                            
                        <a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>">
							<img src="<?php echo $thumb; ?>" />
                        </a>
                    </div>
                        <?php }; ?>
                                        </div>
                

                 </div>
        <div class="cDivNewsText">
            <h2><a href="<?php the_permalink();?>"><?php the_title(); ?></a></h2>
            <div class="cDivBlogPostDate">
                <?php echo date('M d, Y',  strtotime(get_the_date())); ?>
            </div>
            <?php the_excerpt(); ?>
            <?php $sReadmore = (get_current_blog_id()==11) ? 'lees verder' : 'Read more';?>
            <div class="readmore">
				<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(esc_attr__('Permanent Link to %s','Quadro'), the_title()) ?>"><?php esc_html_e($sReadmore,'Quadro'); ?></a>
			</div>
        </div>
        <br style="clear:both" />
</div>
    </div>
                <?php  
            }
            }
        if(get_previous_posts_link()){
            $slabel = (get_current_blog_id()==11) ? '&laquo; Vorige Pagina' : '&laquo; Previous Page' ;
            echo get_previous_posts_link($slabel);
        }
        if(get_next_posts_link()){
            $slabel = (get_current_blog_id()==11) ? 'Volgende Pagina &raquo;' : 'Next Page &raquo;' ;
            echo get_next_posts_link($slabel);
        } 
            
            
            ?>
        

			

			
			
		</div> <!-- end #left-div -->
<?php get_sidebar(); ?>
	</div> <!-- end #container2 -->



</div> <!-- end #container -->
<?php get_footer(); ?>
</body>
</html>