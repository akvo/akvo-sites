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
                $latestvideo = AkvoSiteConfig::getLatestVideo('RAINfoundation');
                $post = (object)$latestvideo;
                get_template_part('includes/entry');
                global $wpdb;
                $post = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."posts WHERE post_type='post' AND post_status='publish' ORDER BY post_date DESC");
                get_template_part('includes/entry');
                
                // Restore original Query & Post Data
		wp_reset_query();
		wp_reset_postdata();
                $args['post_type']='project_update';
                $args['posts_per_page']='2';
                $query = new WP_Query();
                $updates = $query->query($args);
                if ( $updates ) : foreach ($updates AS $post ) :
                    get_template_part('includes/entry');
                endforeach;endif;
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