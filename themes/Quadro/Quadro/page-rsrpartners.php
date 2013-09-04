<?php
/*
Template Name: RSR partners Page
*/
?>
<?php get_header(); ?>

<div id="container">
	<div id="container2">
		<div id="left-div">
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<div class="post-wrapper <?php if($fullwidth) echo (' no_sidebar"');?>">
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
					
				</div> <!-- end .post-wrapper -->
			<?php endwhile; endif; ?>
    <?php
    $oPartners = AkvoPartnerCommunication::getAllProjectPartnersData();
    if($oPartners){
        foreach($oPartners AS $oPartner){
                ?>
<div id="iDivNewsBox">
<div class="newswidget">
<?php
                $classtext = 'no-border';
                $titletext = $oPartner->title;
                
                
                $thumb = '/wp-content/plugins/akvo-site-config/classes/thumb.php?src='.$oPartner->logo.'&w=271&h=167&zc=1&q=100';
                //$thumb = get_field('logo');
                $sLink = $oPartner->url; ?>
                <div class="cDivNewsImage">
                <?php if($oPartner->logo <> '') {
                    $sTextStyle='';
                    ?>
                    <div class="cDivBlogPostImageWrapper">
                                            <div class="cDivBlogPostImage">
                                            
                        <a href="<?php echo $sLink; ?>" rel="bookmark" title="<?php echo $titletext; ?>">
							<img src="<?php echo $thumb; ?>" />
                        </a>
                    </div>
                                        </div>
                <?php }else{
                    $sTextStyle='width:590px;padding:0 15px;';
                } ?>

                 </div>
        <div class="cDivNewsText" style="<?php echo $sTextStyle;?>">
            <h2><a href="<?php echo $sLink; ?>"><?php echo $titletext; ?></a></h2>
            
            <p><?php echo $oPartner->description; ?></p>
            <div class="readmore">
				<a href="<?php echo $sLink; ?>" rel="bookmark" title="<?php printf(esc_attr__('Permanent Link to %s','Quadro'), $titletext) ?>"><?php esc_html_e('Read More','Quadro'); ?></a>
			</div>
        </div>
        <br style="clear:both" />
</div>
    </div>
                <?php  
            }
            }?>
        

			

			
			
		</div> <!-- end #left-div -->
<?php get_sidebar(); ?>
	</div> <!-- end #container2 -->



</div> <!-- end #container -->
<?php get_footer(); ?>
</body>
</html>