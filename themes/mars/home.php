<?php get_header(); ?>
<div id="container">
	<div id="container2">
        <?php if (get_option('quadro_featured') == 'on') get_template_part('includes/featured'); ?>

		<div id="left-div">
            <?php if ( is_active_sidebar( 'news-box' ) ) : ?>
            <div id="iDivNewsBox">
                <?php dynamic_sidebar( 'news-box' ); ?>
            </div>
            <?php else : ?>

                <!-- Create some custom HTML or call the_widget().  It's up to you. -->

            <?php endif; ?>
			
                <?php 
                $args['post_type']='post';
                $args['posts_per_page']='3';
                $query = new WP_Query();
                $posts = $query->query($args);
                if ( $posts ) : foreach ($posts AS $blogpost ) : 
                    if($video && strtotime($video->date) >= strtotime($blogpost->post_date)){
                        //if video is newer than blogpost, show video first and unset video
                        $post = $video;
                        $video = null;
                        get_template_part('includes/entry');
                    }
                        $post=$blogpost;
                    
                    get_template_part('includes/entry');
                endforeach;endif;
                $latestvideo = AkvoSiteConfig::getLatestVideo('MarsSustainability'); 
                $video = (object)$latestvideo;
                //get_template_part('includes/entry');
                
                if($video){
                    //if video is older than latest three blogposts, finally show latest video
                    $post=$video;
                    get_template_part('includes/entry');
                }
                $args['post_type']='project_update';
                $args['posts_per_page']='2';
                $query = new WP_Query();
                $updates = $query->query($args);
                if ( $updates ) : foreach ($updates AS $post ) : 
                    get_template_part('includes/entry');
                endforeach;endif;
                
wp_reset_query();
wp_reset_postdata();
                
			?>

			<div style="clear: both;"></div>


			<?php  wp_reset_query(); ?>


		</div> <!-- end #left-div -->
         <?php if ( is_active_sidebar( 'sidebar-home' ) ) : ?>
            <div id="sidebar-wrapper">
                <div id="sidebar">
                <?php dynamic_sidebar( 'sidebar-home' ); ?>
                </div>
            </div>
            <?php else : ?>

                <!-- Create some custom HTML or call the_widget().  It's up to you. -->

            <?php endif; ?>
	</div> <!-- end #container2 -->



</div> <!-- end #container -->
<?php get_footer(); ?>
</body>
</html>