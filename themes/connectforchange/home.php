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
			

			<?php if (get_option('quadro_show_tabs') == 'on'){
                $tabOptions['showTabs']=true;
                $tabOptions['showUpdates']=true;
                $tabOptions['shownoimg']=false;
                $tabOptions['categories']='all';
                $tabOptions['numposts']=6;
                get_template_part('includes/tabs');
            }  ?>

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