		
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
                        <?php
                        $sLng = ICL_LANGUAGE_CODE;
                        $sCopyrightsSlug = ($sLng==='fr') ? 'fr/licensing-and-copyrights' : 'licensing-and-copyrights';
                        ?>
                         <a href="/<?php echo $sCopyrightsSlug;?>">Copyright</a>. Some rights reserved.
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
<!-- Piwik -->
<script type="text/javascript">
  var _paq = _paq || [];
  _paq.push(["setDocumentTitle", document.domain + "/" + document.title]);
  _paq.push(["setCookieDomain", "*.rain4food.net"]);
  _paq.push(["trackPageView"]);
  _paq.push(["enableLinkTracking"]);

  (function() {
    var u=(("https:" == document.location.protocol) ? "https" : "http") + "://analytics.akvo.org/";
    _paq.push(["setTrackerUrl", u+"piwik.php"]);
    _paq.push(["setSiteId", "13"]);
    var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript";
    g.defer=true; g.async=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);
  })();
</script>
<!-- End Piwik Code -->