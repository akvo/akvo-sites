<?php
/*
Template Name: Blog Page
*/
?>
<?php
$et_ptemplate_settings = array();
$et_ptemplate_settings = maybe_unserialize( get_post_meta($post->ID,'et_ptemplate_settings',true) );

$fullwidth = isset( $et_ptemplate_settings['et_fullwidthpage'] ) ? (bool) $et_ptemplate_settings['et_fullwidthpage'] : false;

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
		<div  class="cDivBlogPageContainer">
			<?php //if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

					<ul class="cUlBlogCats">
						<li id="iLiAllBlogPosts">
							<a>All Blog Posts</a>
						</li>
						<li id="iLiEnvPosts">
							<a>Environmental</a>
						</li>
						<li id="iLiFinancePosts">
							<a>Finance</a>
						</li>
						<li id="iLiTechPosts">
							<a>Technology</a>
						</li>
					</ul>

			<?php //endwhile; endif; ?>
			<div id="all">
				<div class="cDivBlogPosts">
					<ul class="cUlBlogPosts">
							<?php

							$sQuery = "SELECT * FROM $wpdb->posts WHERE post_type='post' AND post_status='publish' ORDER BY post_date DESC";
							$oResults = $wpdb->get_results($sQuery);

//							$cat_query='';
//							if ( !empty($blog_cats) ) $cat_query = 'cat=' . implode(",", $blog_cats);
//							query_posts($cat_query);
//							$i=0;
//							if (have_posts()) : while (have_posts()) : the_post();

							foreach ($oResults as $post){
								$postid = $post->ID;
								$title = $post->post_title;
								$date = date('M d, Y',  strtotime($post->post_date));
								$width = 275;
								$height = 165;
								$classtext = 'no-border';
								$thumbnail = get_thumbnail($width, $height, $classtext, $title, $title, false, 'Featured');
								$thumb = $thumbnail["thumb"];
								//$i++;
								?>
								<li class="cLiBlogPost">
									<div class="cDivBlogPostImageTag"></div>
									<div class="cDivBlogPostImageWrapper">
										<div class="cDivBlogPostImage">
											<?php print_thumbnail($thumb, $thumbnail["use_timthumb"], $title, $width, $height, $classtext); ?>
										</div>
									</div>

									<div class ="cDivBlogPostTitle">
									<h2>
										<a href="<?php echo esc_url(get_permalink($postid)); ?>" title="<?php echo esc_attr($title); ?>">
											<?php echo esc_html($title); ?>
										</a>
									</h2>
									</div>

									<div class="cDivBlogPostDate">
										<?php echo $date; ?>
									</div>
									<br />

									<div class="cDivBlogPostTextContent">
										<?php
										$sContent = $post->post_content;
										echo textClipper($sContent, 200);
										?>
									</div>

									<div class="cDivReadmore">
										<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(esc_attr__('Permanent Link to %s', 'Quadro'), the_title()) ?>"><?php esc_html_e('Read More', 'Quadro'); ?></a>
									</div>

								</li>
							<?php
							} //end of foreach
							////  endwhile; endif; ?>
						</ul>
					</div>
			</div>

			<div id="env">

				<div class="cDivBlogPosts">
					<ul class="cUlBlogPosts">
							<?php

							$sQuery = "SELECT * FROM $wpdb->posts WHERE post_type='post' AND post_status='publish' AND
												`ID` = (SELECT `ID` FROM $wpdb->posts
													LEFT JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id)
													LEFT JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
													LEFT JOIN $wpdb->terms ON ( $wpdb->term_taxonomy.term_id =  $wpdb->terms.term_id)
													WHERE $wpdb->terms.slug = 'environmental')
										ORDER BY post_date DESC";
							$oResults = $wpdb->get_results($sQuery);

							/*$cat_query='';
							if ( !empty($blog_cats) ) $cat_query = 'cat=' . implode(",", $blog_cats);
							query_posts($cat_query);
							$i=0;
							if (have_posts()) : while (have_posts()) : the_post();*/
							foreach ($oResults as $post){
								$postid = $post->ID;
								$title = $post->post_title;
								$date = date('M d, Y',  strtotime($post->post_date));
								$width = 275;
								$height = 165;
								$classtext = 'no-border';
								$thumbnail = get_thumbnail($width, $height, $classtext, $title, $title, false, 'Featured');
								$thumb = $thumbnail["thumb"];
								?>
								<li class="cLiBlogPost">
									<div class="cDivBlogPostImageTag"></div>
									<div class="cDivBlogPostImageWrapper">
										<div class="cDivBlogPostImage">
											<?php print_thumbnail($thumb, $thumbnail["use_timthumb"], $title, $width, $height, $classtext); ?>
										</div>
									</div>


									<div class ="cDivBlogPostTitle">
									<h2>
										<a href="<?php echo esc_url(get_permalink($postid)); ?>" title="<?php echo esc_attr($title); ?>">
											<?php echo esc_html($title); ?>
										</a>
									</h2>
									</div>

									<div class="cDivBlogPostDate">
										<?php echo $date; ?>
									</div>
									<br />

									<div class="cDivBlogPostTextContent">
										<?php
										$sContent = $post->post_content;
										echo textClipper($sContent, 200);
										?>
									</div>

									<div class="cDivReadmore">
										<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(esc_attr__('Permanent Link to %s', 'Quadro'), the_title()) ?>"><?php esc_html_e('Read More', 'Quadro'); ?></a>
									</div>

								</li>
							<?php
							}
							////endwhile; endif; ?>
						</ul>
					</div>

			</div>

			<div id="finance">

				<div class="cDivBlogPosts">
					<ul class="cUlBlogPosts">
							<?php

							$sQuery = "SELECT * FROM $wpdb->posts WHERE post_type='post' AND post_status='publish' AND
												`ID` = (SELECT `ID` FROM $wpdb->posts
													LEFT JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id)
													LEFT JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
													LEFT JOIN $wpdb->terms ON ( $wpdb->term_taxonomy.term_id =  $wpdb->terms.term_id)
													WHERE $wpdb->terms.slug = 'finance')
										ORDER BY post_date DESC";
							$oResults = $wpdb->get_results($sQuery);

							/*$cat_query='';
							if ( !empty($blog_cats) ) $cat_query = 'cat=' . implode(",", $blog_cats);
							query_posts($cat_query);
							$i=0;
							if (have_posts()) : while (have_posts()) : the_post();*/
							foreach ($oResults as $post){
								$postid = $post->ID;
								$title = $post->post_title;
								$date = date('M d, Y',  strtotime($post->post_date));
								$width = 275;
								$height = 165;
								$classtext = 'no-border';
								$thumbnail = get_thumbnail($width, $height, $classtext, $title, $title, false, 'Featured');
								$thumb = $thumbnail["thumb"];
								?>
								<li class="cLiBlogPost">
									<div class="cDivBlogPostImageTag"></div>
									<div class="cDivBlogPostImageWrapper">
										<div class="cDivBlogPostImage">
											<?php print_thumbnail($thumb, $thumbnail["use_timthumb"], $title, $width, $height, $classtext); ?>
										</div>
									</div>


									<div class ="cDivBlogPostTitle">
									<h2>
										<a href="<?php echo esc_url(get_permalink($postid)); ?>" title="<?php echo esc_attr($title); ?>">
											<?php echo esc_html($title); ?>
										</a>
									</h2>
									</div>

									<div class="cDivBlogPostDate">
										<?php echo $date; ?>
									</div>
									<br />

									<div class="cDivBlogPostTextContent">
										<?php
										$sContent = $post->post_content;
										echo textClipper($sContent, 200);
										?>
									</div>

									<div class="cDivReadmore">
										<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(esc_attr__('Permanent Link to %s', 'Quadro'), the_title()) ?>"><?php esc_html_e('Read More', 'Quadro'); ?></a>
									</div>

								</li>
							<?php
							}
							////endwhile; endif; ?>
						</ul>
					</div>

			</div>

			<div id="tech">

				<div class="cDivBlogPosts">
					<ul class="cUlBlogPosts">
							<?php

							$sQuery = "SELECT * FROM $wpdb->posts WHERE post_type='post' AND post_status='publish' AND
												`ID` = (SELECT `ID` FROM $wpdb->posts
													LEFT JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id)
													LEFT JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
													LEFT JOIN $wpdb->terms ON ( $wpdb->term_taxonomy.term_id =  $wpdb->terms.term_id)
													WHERE $wpdb->terms.slug = 'technology')
										ORDER BY post_date DESC";
							$oResults = $wpdb->get_results($sQuery);

/*							$cat_query='';
							if ( !empty($blog_cats) ) $cat_query = 'cat=' . implode(",", $blog_cats);
							query_posts($cat_query);
							$i=0;
							if (have_posts()) : while (have_posts()) : the_post();*/
							foreach ($oResults as $post){
								$postid = $post->ID;
								$title = $post->post_title;
								$date = date('M d, Y',  strtotime($post->post_date));
								$width = 275;
								$height = 165;
								$classtext = 'no-border';
								$thumbnail = get_thumbnail($width, $height, $classtext, $title, $title, false, 'Featured');
								$thumb = $thumbnail["thumb"];
								?>
								<li class="cLiBlogPost">
									<div class="cDivBlogPostImageTag"></div>
									<div class="cDivBlogPostImageWrapper">
										<div class="cDivBlogPostImage">
											<?php print_thumbnail($thumb, $thumbnail["use_timthumb"], $title, $width, $height, $classtext); ?>
										</div>
									</div>


									<div class ="cDivBlogPostTitle">
									<h2>
										<a href="<?php echo esc_url(get_permalink($postid)); ?>" title="<?php echo esc_attr($title); ?>">
											<?php echo esc_html($title); ?>
										</a>
									</h2>
									</div>

									<div class="cDivBlogPostDate">
										<?php echo $date; ?>
									</div>
									<br />

									<div class="cDivBlogPostTextContent">
										<?php
										$sContent = $post->post_content;
										echo textClipper($sContent, 200);
										?>
									</div>

									<div class="cDivReadmore">
										<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(esc_attr__('Permanent Link to %s', 'Quadro'), the_title()) ?>"><?php esc_html_e('Read More', 'Quadro'); ?></a>
									</div>

								</li>
							<?php
							}
							////endwhile; endif; ?>
						</ul>
					</div>

			</div>

  <!-- end #recententries -->

		</div> <!-- end cDivBlogPageContainer -->
        <?php //if (!$fullwidth) get_sidebar(); ?>
	</div> <!-- end #container2 -->


</div> <!-- end #container -->
<?php get_footer(); ?>
</body>
</html>



<!--<inside code of the div class post-wrapper >-->
<!--<
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

					<?php if (get_option('quadro_show_pagescomments') == 'on') { ?>
<!--						Begin Comments Template-->
						<div class="recentposts">
							<?php comments_template('', true); ?>
						</div>
<!--						End Comments Template-->
					<?php }; ?>
<!--</div>  end .post-wrapper -->

