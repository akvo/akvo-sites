	</div> <!-- end #wrapper2 -->
    <br style="clear:both;" />
</div> <!-- end #bg -->

<!--</div>  end of BG pattern -->
<div id="iDivStickyFooterPush"></div>
</div> <!-- end of custom wrapper -->


    <div id="footerContainer">
        <div id="footer">


<?php
$aArgsInitiative = array('category_name' => 'sponsors','posts_per_page'=>-1, 'nopaging'=>true, 'tag'=> 'initiative');
$aArgsOndersteund = array('category_name' => 'sponsors','posts_per_page'=>-1, 'nopaging'=>true, 'tag'=> 'ondersteund' ); //ondersteund
$oQueryInitiative = new WP_Query($aArgsInitiative);
$oQueryOndersteund = new WP_Query($aArgsOndersteund);
?>

			<div id="iDivInitiative">
				<div class="cDivFooterHeader">initiatief van:</div>
				<?php
					if ($oQueryInitiative->have_posts()) {
						while ($oQueryInitiative->have_posts()) : $oQueryInitiative->the_post();

				?>
				<div class="cDivPartnerLogo">
					<a href="<?php the_field('url');?>"><img src="<?php the_field('logo');?>"/></a>
				</div>
				<?php
						endwhile;
					}
				?>
			</div>
			<div id="iDivPartners">
				<div class="cDivFooterHeader">partners:</div>

				<?php
					if ($oQueryOndersteund->have_posts()) {
						while ($oQueryOndersteund->have_posts()) : $oQueryOndersteund->the_post();
							//if(!is_tag('initiative')){
				?>

				<div class="cDivPartnerLogo cDivOndersteund">
					<a href="<?php the_field('url');?>"><img src="<?php the_field('logo');?>"/></a>
				</div>

					<?php
							//}
						endwhile;

					}


				?>

			</div>
			<div id="iDivAddress">
				<div class="cDivFooterHeader">Wandelen voor Water, Aqua for All</div>
				<div id="iDivAddressText"><p>Spaarneplein 2<br/>
										2515 VK  Den Haag <br/>
										Nederland <br/>
										Tel. +31 (0) 70 7200 870 <br/>
										Landelijke coördinator: Chris Amsinger <br/><br/>
										Email info@wandelenvoorwater.nl<br/>
                    <div id="iDivPoweredByImage">
                        <a href="http://akvo.org">
                            <img src="http://wash-liberia.org/wp-content/themes/washliberia/images/poweredby.jpg">
                        </a>
                    </div>                    
                    <div id="iDivPoweredByText">
                         <a href="/licensing-and-copyrights">Copyright</a>. Some rights reserved.
                    </div>
                    </p></div>
			</div>

	        <br style="clear:both;" />
		</div> <!-- end #footer -->
    </div> <!-- end #footerContainer -->


<?php get_template_part('includes/scripts'); ?>
<?php wp_footer(); ?>
