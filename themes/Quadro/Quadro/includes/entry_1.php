<?php if (get_option('quadro_blog_style') == 'false') { ?>
	<div class="home-post-wrap">
		<div class="single-entry">
			<?php
			$width = 263;
			$height = 108;
			$classtext = 'no-border';
			$titletext = get_the_title();

			$thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext);
			$thumb = $thumbnail["thumb"]; ?>

			<?php if($thumb != '' && get_option('quadro_thumbnails_index') == 'on') { ?>
				<div class="thumbnail-div" style="width: 263px; margin-bottom: 10px;">
					<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(esc_attr__('Permanent Link to %s','Quadro'), get_the_title()) ?>">
						<?php print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext); ?>
					</a>
				</div>
			<?php }; ?>

			<h2 class="titles">
				<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(esc_attr__('Permanent Link to %s','Quadro'), get_the_title()) ?>">
					<?php truncate_title(23); ?>
				</a>
			</h2>
			<?php truncate_post(290); ?>
			<div style="clear: both;"></div>

			<div class="readmore">
				<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(esc_attr__('Permanent Link to %s','Quadro'), get_the_title()) ?>"><?php esc_html_e('Read More','Quadro'); ?></a>
			</div>
		</div> <!-- end .single-entry -->
	</div> <!-- end .home-post-wrap -->
<?php } else { ?>
	<div class="post-wrapper" style="margin-bottom: 20px;">
		<h2 class="titles">
			<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(esc_attr__('Permanent Link to %s','Quadro'), get_the_title()) ?>">
				<?php the_title(); ?>
			</a>
		</h2>

		<?php if (get_option('quadro_postinfo1') <> '') { ?>
			<div class="articleinfo"><?php esc_html_e('Posted','Quadro'); ?> <?php if (in_array('author', get_option('quadro_postinfo1'))) { ?> <?php esc_html_e('by','Quadro'); ?> <?php the_author_posts_link(); ?><?php }; ?><?php if (in_array('date', get_option('quadro_postinfo1'))) { ?> <?php esc_html_e('on','Quadro'); ?> <?php the_time(get_option('quadro_date_format')) ?><?php }; ?><?php if (in_array('categories', get_option('quadro_postinfo1'))) { ?> <?php esc_html_e('in','Quadro'); ?> <?php the_category(', ') ?><?php }; ?><?php if (in_array('comments', get_option('quadro_postinfo1'))) { ?> | <?php comments_popup_link(esc_html__('0 comments','Quadro'), esc_html__('1 comment','Quadro'), '% '.esc_html__('comments','Quadro')); ?><?php }; ?></div>
		<?php }; ?>

		<div class="single-entry">
			<?php the_content(''); ?>
			<div style="clear: both;"></div>
		</div>
	</div> <!-- end .post-wrapper -->
<?php }; ?>