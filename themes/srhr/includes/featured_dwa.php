<?php
$ids = array();
$featured_cat = get_option('quadro_feat_cat');
$featured_num = (int) get_option('quadro_featured_num');
?>
<div id="featured">
	<?php
	query_posts("pagename=home");
	while (have_posts()) : the_post();
		?>
		<?php
		$width = 480;
		$height = 212;
		$classtext = 'no-border';
		$titletext = get_the_title();
        $sURL = get_field('url');
        $sLink = ($sURL) ? $sURL : get_permalink() ;
		$thumbnail = get_thumbnail($width, $height, $classtext, $titletext, $titletext, false, 'Featured');
		$thumb = $thumbnail["thumb"];
		?>

		<?php if ($thumb <> '') { ?>
			<div class="thumbnail-div-featured">
				<a href="<?php echo $sLink; ?>">
					<?php print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext); ?>
				</a>
			</div>  <!-- end .thumbnail-div-featured -->
		<?php }; ?>



		<div class="featured-content">
			<h1 class="titles-featured">
				<a href="<?php echo $sLink; ?>" rel="bookmark" title="<?php printf(esc_attr__('Permanent Link to %s', 'Quadro'), the_title()) ?>">
					<?php the_title(); ?>
				</a>
			</h1>

			<div style="clear: both;"></div>

			<div class="readmore">
				<a href="<?php echo $sLink; ?>" rel="bookmark" title="<?php printf(esc_attr__('Permanent Link to %s', 'Quadro'), the_title()) ?>"><?php esc_html_e('Read More', 'Quadro'); ?></a>
			</div>
		</div> <!-- end #featured-content -->

		<div style="clear: both;"></div>
		<?php
		$ids[] = $post->ID;
        break;
	endwhile;
	wp_reset_query();
	?>
</div> <!-- end #featured -->