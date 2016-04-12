<?php $ids = array();
$featured_cat = get_option('quadro_feat_cat');
$featured_num = (int) get_option('quadro_featured_num');
?>

<div id="featured">
    <?php query_posts("pagename=home");
	while (have_posts()) : the_post(); ?>
		
        <div id="iDivMap"  style="width: 470px; height: 240px; float: left;"></div>
                <?php
                   $sURL = get_field('url');
        $sLink = ($sURL) ? $sURL : get_permalink() ; 
//                if (function_exists('showMap')) {
//                    showMap();
//                }
		
		showTempMap();
                ?>


		<div class="featured-content">
			<h1 class="titles-featured">
				<a href="<?php echo $sLink; ?>" rel="bookmark" title="<?php printf(esc_attr__('Permanent Link to %s','Quadro'), the_title()) ?>">
					<?php the_title(); ?>
				</a>
			</h1>
			<?php truncate_post(510); ?>
			<div style="clear: both;"></div>

			<div class="readmore">
				<a href="<?php echo $sLink; ?>" rel="bookmark" title="<?php printf(esc_attr__('Permanent Link to %s','Quadro'), the_title()) ?>"><?php esc_html_e('Read More','Quadro'); ?></a>
			</div>
		</div> <!-- end #featured-content -->

		<div style="clear: both;"></div>
    <?php $ids[] = $post->ID;
	endwhile; wp_reset_query(); ?>
</div> <!-- end #featured -->