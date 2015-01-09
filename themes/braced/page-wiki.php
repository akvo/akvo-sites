<?php 
	/*
		Template Name: BRACED Wiki Page
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
				<div class="post-wrapper no_sidebar">
					<h1 class="titles">
						<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(esc_attr__('Permanent Link to %s','Quadro'), the_title()) ?>">
							<?php the_title(); ?>
						</a>
					</h1>
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
                <div class="post-wrapper no_sidebar">
                    <h1 id="akvopedia-title"></h1>
                    <!--<a href="#" id="akvopedia-home-link">Back to Rainwater Harvesting portal</a>-->
                    <div style="background-color: #fff; -moz-border-radius: 2px; -webkit-border-radius: 2px; border: 1px solid #d8d8d8; padding: 2px;">
                    <table border="0" cellpadding="0" cellspacing="1" style="width: 100%; background-color: #fff">
                    <tbody><tr>
                    <td><center> <font size="3" color="#555555"> <b>Rainwater Harvesting TOOLS</b> <font color="#8C8C8C">- simple methods applicable to project planning</font></font> </center>
                    </td></tr></tbody></table>
                     </div>
                    <div style="background-color: #efefef; text-align: center; -moz-border-radius: 2px; -webkit-border-radius: 2px; border: 2px solid #DEDEDE; padding: 3px;">
                        <table cellpadding="3" cellspacing="0" width="100%">

                        <tbody><tr>
                        <td colspan="5" style="background-color:#efefef;">
                        </td></tr>
                        <tr>
                        <td style="background:#efefef;"><div class="center"><div class="floatnone"><a target="_blank" href="http://akvopedia.org/wiki/3R_(Recharge,_Retention_%26_Reuse)" title="3R (Recharge, Retention &amp; Reuse)"><img alt="3R icon.png" src="http://akvopedia.org/s_wiki/images/thumb/d/d8/3R_icon.png/80px-3R_icon.png" width="80" height="81" srcset="/s_wiki/images/d/d8/3R_icon.png 1.5x, /s_wiki/images/d/d8/3R_icon.png 2x"></a></div></div>
                        </td>
                        <td style="background:#efefef;"><div class="center"><div class="floatnone"><a target="_blank" href="http://akvopedia.org/wiki/Business_Development_-_Micro-financing" title="Business Development - Micro-financing"><img alt="Financing streams icon.png" src="http://akvopedia.org/s_wiki/images/thumb/2/2b/Financing_streams_icon.png/80px-Financing_streams_icon.png" width="80" height="80" srcset="/s_wiki/images/2/2b/Financing_streams_icon.png 1.5x, /s_wiki/images/2/2b/Financing_streams_icon.png 2x"></a></div></div>
                        </td>
                        <td style="background:#efefef;"><div class="center"><div class="floatnone"><a target="_blank" href="http://akvopedia.org/wiki/Multiple_Use_Services_(MUS)" title="Multiple Use Services (MUS)"><img alt="MUS icon.png" src="http://akvopedia.org/s_wiki/images/thumb/d/d4/MUS_icon.png/80px-MUS_icon.png" width="80" height="82" srcset="/s_wiki/images/d/d4/MUS_icon.png 1.5x, /s_wiki/images/d/d4/MUS_icon.png 2x"></a></div></div>
                        </td>
                        <td style="background:#efefef;"><div class="center"><div class="floatnone"><a target="_blank" href="http://akvopedia.org/wiki/SamSam_RWH_Tool" title="SamSam RWH Tool"><img alt="Samsam logo.png" src="http://akvopedia.org/s_wiki/images/thumb/3/3a/Samsam_logo.png/80px-Samsam_logo.png" width="80" height="81" srcset="/s_wiki/images/3/3a/Samsam_logo.png 1.5x, /s_wiki/images/3/3a/Samsam_logo.png 2x"></a></div></div>
                        </td></tr>
                        <tr>
                        <td style="background:#efefef;"><div class="center"><div class="floatnone"><a target="_blank" href="http://akvopedia.org/wiki/3R_(Recharge,_Retention_%26_Reuse)" title="3R (Recharge, Retention &amp; Reuse)"><img alt="WUMP photo small.jpg" src="http://akvopedia.org/s_wiki/images/thumb/c/c4/WUMP_photo_small.jpg/120px-WUMP_photo_small.jpg" width="120" height="115" srcset="/s_wiki/images/thumb/c/c4/WUMP_photo_small.jpg/180px-WUMP_photo_small.jpg 1.5x, /s_wiki/images/thumb/c/c4/WUMP_photo_small.jpg/240px-WUMP_photo_small.jpg 2x"></a></div></div>
                        </td>
                        <td style="background:#efefef;"><div class="center"><div class="floatnone"><a target="_blank" href="http://akvopedia.org/wiki/Business_Development_-_Micro-financing" title="Business Development - Micro-financing"><img alt="Nepal micro small.jpg" src="http://akvopedia.org/s_wiki/images/thumb/e/ed/Nepal_micro_small.jpg/120px-Nepal_micro_small.jpg" width="120" height="115" srcset="/s_wiki/images/thumb/e/ed/Nepal_micro_small.jpg/180px-Nepal_micro_small.jpg 1.5x, /s_wiki/images/thumb/e/ed/Nepal_micro_small.jpg/240px-Nepal_micro_small.jpg 2x"></a></div></div>
                        </td>
                        <td style="background:#efefef;"><div class="center"><div class="floatnone"><a target="_blank" href="http://akvopedia.org/wiki/Multiple_Use_Services_(MUS)" title="Multiple Use Services (MUS)"><img alt="RWH barrel.jpg" src="http://akvopedia.org/s_wiki/images/thumb/6/63/RWH_barrel.jpg/120px-RWH_barrel.jpg" width="120" height="115" srcset="/s_wiki/images/thumb/6/63/RWH_barrel.jpg/180px-RWH_barrel.jpg 1.5x, /s_wiki/images/thumb/6/63/RWH_barrel.jpg/240px-RWH_barrel.jpg 2x"></a></div></div>
                        </td>
                        <td style="background:#efefef;"><a target="_blank" href="http://akvopedia.org/wiki/SamSam_RWH_Tool" title="SamSam RWH Tool"><img alt="Samsam image.png" src="http://akvopedia.org/s_wiki/images/thumb/9/92/Samsam_image.png/120px-Samsam_image.png" width="120" height="115" srcset="/s_wiki/images/thumb/9/92/Samsam_image.png/180px-Samsam_image.png 1.5x, /s_wiki/images/thumb/9/92/Samsam_image.png/240px-Samsam_image.png 2x"></a>
                        </td></tr>
                        <tr>
                        <td style="background:#efefef;"><center><a target="_blank" href="http://akvopedia.org/wiki/3R_(Recharge,_Retention_%26_Reuse)" title="3R (Recharge, Retention &amp; Reuse)"> 3R (Recharge, <br>Retention &amp; Reuse)</a></center>
                        </td>
                        <td style="background:#efefef;"><center><a target="_blank" href="http://akvopedia.org/wiki/Business_Development_-_Micro-financing" title="Business Development - Micro-financing"> Business Development -<br> Micro-financing</a></center>
                        </td>
                        <td style="background:#efefef;"><center><a target="_blank" href="http://akvopedia.org/wiki/Multiple_Use_Services_(MUS)" title="Multiple Use Services (MUS)">Multiple Use Services (MUS)</a></center>
                        </td>
                        <td style="background:#efefef;"><center><a target="_blank" href="http://akvopedia.org/wiki/SamSam_RWH_Tool" title="SamSam RWH Tool">SamSam RWH Tool</a></center>
                        </td></tr>
                        </tbody></table>
                        </div>
                    <!--<div id="embedded-akvopedia"><noscript><iframe style="position: absolute; top: 4em; right:1em; left:1em; bottom:1em; height:90%; width:97%;" src="http://www.akvopedia.org/wiki/Rainwater_Harvesting"></iframe></noscript></div>-->

                </div>
			<?php
                $aCategories = wp_get_post_categories($post->ID);
                
                ?>
			<?php endwhile; endif;
                if(count($aCategories)>0){
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
    
</body>
</html>
