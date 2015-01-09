<?php
	/*
		Template Name: Full Width Page
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
