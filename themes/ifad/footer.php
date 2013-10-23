		
	</div> <!-- end #wrapper2 -->
    <br style="clear:both;" />
</div> <!-- end #bg -->
<br style="clear:both;" />
    <div id="footerContainer">
        <div id="footer">
            <?php if ( is_active_sidebar( 'sidebar-footer' ) ) : ?>

                <?php dynamic_sidebar( 'sidebar-footer' ); ?>

            <?php else : ?>

                <!-- Create some custom HTML or call the_widget().  It's up to you. -->

            <?php endif; ?>
                <div id="iDivPoweredBy">
                    <div id="iDivPoweredByText">
                        Content Copyright RAIN. All rights reserved.
                    </div>
                    <div id="iDivPoweredByImage">
                        <a href="http://akvo.org">
                            <img src="http://wash-liberia.org/wp-content/themes/washliberia/images/poweredby.jpg">
                        </a>
                    </div>
                </div>
                <br style="clear:both;" />
		</div> <!-- end #footer -->
        <br style="clear:both;" />
    </div> <!-- end #footerContainer -->
</div> <!-- end of BG pattern -->
</div> <!-- end of custom wrapper -->

<?php get_template_part('includes/scripts'); ?>
<?php wp_footer(); ?>
