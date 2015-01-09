    

</div>
<!-- end main container -->
</div>
<!-- end wrap container -->
<div class="container-fluid cDivFooter">
    
    <div class="container">
    <div class="col-xs-12">
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
    </div>
    </div>
</div>
<?php wp_footer(); ?>
</body>
</body>
</html>