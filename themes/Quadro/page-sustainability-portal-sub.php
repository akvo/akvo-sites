<?php 
	/*
		Template Name: Sustainability portal Sub Page
	*/
?>

<?php get_header(); ?>

<div id="container">
    <div id="iDivBreadcrumb">
		<?php the_breadcrumbs();?>
		</div>
	<div id="container2">
		<div id="left-div">
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <?php
                    //get custom fields
                    //get custom fields
                    $oHeaderImage = get_field('header_image');
                    $sPackageBtnText = get_field('package_button_text');
                    $sPackageBtnLink = get_field('package_button_link');
                    $sLinkFinancial = get_field('page_link_financial_wheel_item');
                    $sLinkInstitutional = get_field('page_link_institutional_item');
                    $sLinkEnvironmental = get_field('page_link_environmental_wheel_item');
                    $sLinkTechnical = get_field('page_link_technical_wheel_item');
                    $sLinkSocial = get_field('page_link_social_wheel_item');
                    $sBlue = get_field('blue_block_content');
                    $sOrange = get_field('orange_block_content');
                    $sExplanation = get_field('explanation_block_content');
                    $parent_id = $post->post_parent;
                    while ($parent_id) {
                        $parentLink = get_permalink($parent_id);
                        break;
                    }
//                    var_dump($oHeaderImage);
                ?>
            <div class="cDivSustainabilityHeader">
                <div class="cDivHeaderImage">
                    <img src="<?php echo $oHeaderImage['url'];?>" class="cImgHeader" />
                </div>
                <div id="rsr" class="cDivFietsImage">
                    
                </div>
                <a href="<?php echo $parentLink; ?>" class="cAportalHome">Back to main portal</a>
                <div class="cDivTitle"><?php the_title(); ?></div>
                
                <div class="cDivPackageBanner">
                    <a href="<?php echo $sPackageBtnLink; ?>"><?php echo $sPackageBtnText;?></a>
                </div>
                <br style="clear:both;" />
            </div>
            <?php if($sBlue): ?>
            <div class="cDivBlueBlock">
                <?php echo $sBlue; ?>
            </div>
            <?php endif; ?>
            <?php if($sExplanation): ?>
            <div class="post-wrapper no_sidebar"><?php echo $sExplanation; ?></div>
            <?php endif; ?>
            <?php if($sOrange): ?>
            <div class="cDivOrangeBlock"><?php echo $sOrange; ?></div>
            <?php endif; ?>
				<div class="post-wrapper no_sidebar">
					
					<div style="clear: both;"></div>
					
					<?php if (get_option('quadro_page_thumbnails') == 'on') { ?>
					
						<?php $thumb = ''; 	  

						$width = (int) get_option('quadro_thumbnail_width_pages');
						$height = (int) get_option('quadro_thumbnail_height_pages');
						$classtext = '';
						$titletext = get_the_title();
						
						$thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext);
						$thumb = $thumbnail["thumb"]; ?>
						
						<?php if($thumb <> '') { ?>
							<div style="float: left; margin: 10px 10px 10px 0px;">
								<?php print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext , $width, $height, $classtext); ?>
							</div>
						<?php }; ?>
							
					<?php }; ?>
					
					<?php the_content(); ?>
					<div style="clear: both;"></div>
				
					<?php wp_link_pages(array('before' => '<p><strong>'.esc_html__('Pages','Quadro').':</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
					<?php edit_post_link(esc_html__('Edit this page','Quadro')); ?>
					
					<?php if (get_option('quadro_show_pagescomments') == 'on') { ?>
						<!--Begin Comments Template-->
						<div class="recentposts">
							<?php comments_template('', true); ?>
						</div>
						<!--End Comments Template-->
					<?php }; ?>
				</div> <!-- end .post-wrapper -->
			<?php
                $aCategories = wp_get_post_categories($post->ID);
                
                ?>
			<?php endwhile; endif;
                if(count($aCategories)>0){
                    $sSustainabilityItem = get_term($aCategories[0],'category')->slug;
            ?>
                <div class="fullwidth_blogs" >
                    <?php 
                    $tabOptions['showTabs']=true;
                        $tabOptions['showUpdates']=false;
                        sort($aCategories,SORT_NUMERIC);
                        $tabOptions['categories']=$aCategories;
                        get_template_part('includes/tabs');
                        ?>
                </div>
                <?php } ?>
		</div> <!-- end #left-div -->
	</div> <!-- end #container2 -->
	
	</div> <!-- end #container -->
<?php get_footer(); ?>
    <!-- CSS file -->
    <link type="text/css" rel="stylesheet" href="<?php echo get_template_directory_uri();?>/js/wiel/qtip.min.css" />
    <script type="text/javascript" src="<?php echo get_template_directory_uri();?>/js/wiel/qtip.min.js"></script>
    <script type="text/javascript" src="<?php echo get_template_directory_uri();?>/js/wiel/raphael.js"></script>
    <script type="text/javascript" src="<?php echo get_template_directory_uri();?>/js/wiel/<?php echo $sSustainabilityItem; ?>.js"></script>
    <!--<script type="text/javascript" src="financial.js"></script>-->
    <!--<script type="text/javascript" src="institutional.js"></script>-->
    <!--<script type="text/javascript" src="environmental.js"></script>-->
    <!--<script type="text/javascript" src="technical.js"></script>-->
    <!--<script type="text/javascript" src="social.js"></script>-->
    <script type="text/javascript">
        var links = {
            financial: '<?php echo $sLinkFinancial; ?>',
            institutional: '<?php echo $sLinkInstitutional; ?>',
            environmental: '<?php echo $sLinkEnvironmental; ?>',
            technical: '<?php echo $sLinkTechnical; ?>',
            social: '<?php echo $sLinkSocial; ?>'
        };
        var $ = jQuery;
        $(function(){
        for (var i = 0, len = rsrGroups.length; i < len; i++) {
            for (var j = 0, len2 = rsrGroups[i].length; j < len2; j++) {
                rsrGroups[i][j].glow({
                    width:7,
                    opacity:0.2
                });
            }
            
        }
        for (var i = 0, len = bollen.length; i < len; i++) {
        var el = bollen[i];
        var id=el.data('id');    
        
            //var inactive = el.attrs.fill;
            el.mouseover(function() {
                
                this.attr({
                    cursor: 'pointer'
                });
                this.toFront();
                this.animate({
                    fill : '#FF6E01'
                }, 200);
                
            })
            .mouseout(function() {
                
                this.animate({
                    fill : '#ffffff'
                }, 200);
                
            })
            .mousedown(function(){
                document.location.href = links[this.data('id')];
            });
            addTip(el.node,id);

        }
        function addTip(node, region){
            
            
            $(node).qtip({
                        content: {
                            text:region.charAt(0).toUpperCase() + region.slice(1)+' Sustainability'
                        },
                        position: {
//                            viewport: $('.cDivSustainabilityHeader'),
//                            my: 'top center',
//                            at: 'bottom center',
                            target: $(node),
				viewport: jQuery(window)
                        },
                        style: {
                            classes: 'qtip-light qtipWiel',
                            tip: {
                                corner: true
                            }
                        }
                        ,
//                        style: {
//                            classes: 'ui-tooltip-custom',
//                            widget:false
//                        },
                        hide: {
                            fixed: true // Helps to prevent the tooltip from hiding ocassionally when tracking!
                        }
                    });

            }
        });
    </script>
</body>
</html>
