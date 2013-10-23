<?php get_header(); ?>

<div id="container">
    <div id="iDivBreadcrumb">
		<?php the_breadcrumbs();?>
		</div>
	<div id="container2">
		<div id="left-div">
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<div class="post-wrapper">
				
					<?php if (get_option('quadro_integration_single_top') <> '' && get_option('quadro_integrate_singletop_enable') == 'on') echo(get_option('quadro_integration_single_top')); ?>
					
					<h1 class="titles">
						<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(esc_attr__('Permanent Link to %s','Quadro'), the_title()) ?>">
							<?php the_title(); ?>
						</a>
					</h1>
					
					<?php if (get_option('quadro_postinfo2') <> '') { ?>
<!--						<div class="articleinfo"><?php esc_html_e('Posted','Quadro'); ?> <?php if (in_array('author', get_option('quadro_postinfo2'))) { ?> <?php esc_html_e('by','Quadro'); ?> <?php the_author_posts_link(); ?><?php }; ?><?php if (in_array('date', get_option('quadro_postinfo2'))) { ?> <?php esc_html_e('on','Quadro'); ?> <?php the_time(get_option('quadro_date_format')) ?><?php }; ?><?php if (in_array('categories', get_option('quadro_postinfo2'))) { ?> <?php esc_html_e('in','Quadro'); ?> <?php the_category(', ') ?><?php }; ?><?php if (in_array('comments', get_option('quadro_postinfo2'))) { ?> | <?php comments_popup_link(esc_html__('0 comments','Quadro'), esc_html__('1 comment','Quadro'), '% '.esc_html__('comments','Quadro')); ?><?php }; ?></div>-->
					<?php }; ?>
					<div style="clear: both;"></div>
					
					<?php if (get_option('quadro_thumbnails') == 'on') { ?>
						<?php $thumb = ''; 	  

						$width = (int) get_option('quadro_thumbnail_width_posts');
						$height = (int) get_option('quadro_thumbnail_height_posts');
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
					
					<?php if (get_option('quadro_integration_single_bottom') <> '' && get_option('quadro_integrate_singlebottom_enable') == 'on') echo(get_option('quadro_integration_single_bottom')); ?>		
					
					<?php if (get_option('quadro_468_enable') == 'on') { ?>
						<?php if(get_option('quadro_468_adsense') <> '') echo(get_option('quadro_468_adsense'));
						else { ?>
							<a href="<?php echo esc_url(get_option('quadro_468_url')); ?>"><img src="<?php echo esc_url(get_option('quadro_468_image')); ?>" alt="468 ad" class="foursixeight" /></a>
						<?php } ?>	
					<?php } ?>
					
					<?php if (get_option('quadro_show_postcomments') == 'on') { ?>
						<!--Begin Comments Template-->
						<?php comments_template('',true); ?>
						<!--End Comments Template-->
					<?php }; ?>
					
				</div> <!-- end .post-wrapper -->
			<?php endwhile; endif; ?>
		</div> <!-- end #left-div -->
        <?php get_sidebar(); ?>
	</div> <!-- end #container2 -->
	
</div> <!-- end .container -->
<?php get_footer(); ?>
</body>
</html>
