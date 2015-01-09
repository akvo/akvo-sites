<?php 
/*
Template Name: Implementing partners Page
*/
?>
<?php
$et_ptemplate_settings = array();
$et_ptemplate_settings = maybe_unserialize( get_post_meta($post->ID,'et_ptemplate_settings',true) );

$fullwidth = isset( $et_ptemplate_settings['et_fullwidthpage'] ) ? (bool) $et_ptemplate_settings['et_fullwidthpage'] : false;
$fullwidth = true;

$et_ptemplate_blogstyle = isset( $et_ptemplate_settings['et_ptemplate_blogstyle'] ) ? (bool) $et_ptemplate_settings['et_ptemplate_blogstyle'] : false;

$et_ptemplate_showthumb = isset( $et_ptemplate_settings['et_ptemplate_showthumb'] ) ? (bool) $et_ptemplate_settings['et_ptemplate_showthumb'] : false;

$blog_cats = isset( $et_ptemplate_settings['et_ptemplate_blogcats'] ) ? (array) $et_ptemplate_settings['et_ptemplate_blogcats'] : array();
$et_ptemplate_blog_perpage = isset( $et_ptemplate_settings['et_ptemplate_blog_perpage'] ) ? (int) $et_ptemplate_settings['et_ptemplate_blog_perpage'] : 10;
?>

<?php get_header(); ?>

<div id="container">
    <div id="iDivBreadcrumb">
		<?php the_breadcrumbs();?>
		</div>
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
            $aCategories = wp_get_post_categories($post->ID);
            $args = array(
                'post_type'=> 'implementing_partner',
                'order'    => 'DESC'
            );
            query_posts( $args );
            ?>
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<div class="post-wrapper <?php if($fullwidth) echo (' no_sidebar"');?>">
					<h1 class="titles">
                        
                        <?php 
                        $sExternalVacancyURL = get_field('url');
                        $sLink = ($sExternalVacancyURL) ? $sExternalVacancyURL : get_permalink() ;
                        $sTarget = ($sExternalVacancyURL) ? "_blank" : "_self" ;
                            
                        ?>
						<a href="<?php echo $sLink; ?>" target="<?php echo $sTarget; ?>" rel="bookmark" title="<?php printf(esc_attr__('Permanent Link to %s','Quadro'), the_title()) ?>">
							<?php the_title(); ?>
						</a>
					</h1>
					<div style="clear: both;"></div>
                    
					<div>
					<?php the_content(); ?>
					
                        <a href="<?php echo $sLink; ?>" target="<?php echo $sTarget; ?>" rel="bookmark" title="<?php printf(esc_attr__('Permanent Link to %s','Quadro'), the_title()) ?>">
                                <?php esc_html_e('Read more','Quadro'); ?>
                            </a>
                        
                    </div>
                    <div style="clear: both;"></div>
					<?php wp_link_pages(array('before' => '<p><strong>'.esc_html__('Pages','Quadro').':</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
					<?php edit_post_link(esc_html__('Edit this page','Quadro')); ?>
					
				</div> <!-- end .post-wrapper -->
			<?php endwhile; endif; ?>
		</div> <!-- end #left-div -->
        <?php if (!$fullwidth) get_sidebar(); ?>
	</div> <!-- end #container2 -->
	
	
</div> <!-- end #container -->
<?php get_footer(); ?>
</body>
</html>

