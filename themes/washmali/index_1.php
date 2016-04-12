<?php get_header(); ?>

<?php get_template_part('template-part', 'head'); ?>

<?php get_template_part('template-part', 'topnav'); ?>

<!--slide show--> 
<?php
	
$aSliderArgs = array(
	'post_type' => 'slider',
	'post_status' => 'publish',
	'posts_per_page' => '-1',
	'nopaging' => true
);

$oTheQuerySlider = new WP_Query( $aSliderArgs ); ?>

<?php if ( $oTheQuerySlider->have_posts() ) : ?>

<div class="cDivSliderContainer">
	<ul class="bxslider">	
	<?php while ( $oTheQuerySlider->have_posts() ) : $oTheQuerySlider->the_post(); ?>
		<li>
			<?php echo get_the_post_thumbnail(get_the_ID(), 'full'); ?>		  
		  <div class="cDivSliderTextContainer">
			  <div class="cDivSliderTitle"><?php the_title(); ?></div>
			  <div class="cDivSliderText">
				  <?php the_excerpt(); ?>
			  </div>
			  <div class="cDivSliderReadMoreBtn"><a href="#" class="btn btn-default cAReadMoreBtn">Savoir plus</a></div>
		  </div>
		</li>
	<?php endwhile; ?>
	</ul>
</div>

<?php wp_reset_postdata(); ?>
<?php endif; ?>

<?php // query for the about page
$oAboutPage = new WP_Query( 'pagename=about' );
if ( $oAboutPage->have_posts() ) : ?>
<div class="cDivAboutBoxContainer">
	<div class="col-md-8">
		<div class="cDivAboutBox">
			<?php while ( $oAboutPage->have_posts() ) : $oAboutPage->the_post(); ?>
				<h2><?php the_title(); ?></h2>
				<div class="cDivAboutBoxText">
					<?php $sAboutBoxText = get_the_content(); 
					echo mb_strimwidth($sAboutBoxText, 0, 1200, '...');  ?>
				</div>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>	
				<div class="cDivSliderReadMoreBtn"><a href="/about" class="btn btn-default cAReadMoreBtn">Savoir plus</a></div>
		</div>
	</div>
<?php endif; ?>
	<div class="col-md-4">
		<div class="cDivPartnerLogoContainer">
			<?php //get_sidebar('about'); ?>	
			<h2>Partners</h2>
			<div class="cDivSeparator"></div>
			<div class="cDivPartnerLogoWrapper">
				<?php 
					$aPartnersArgs = array(
						'category_name'=> 'partners',
						'order' => 'DESC',
						'posts_per_page' => 3,
						'post_limits' => 3
					);
					$oPartnerSideBar = new WP_Query($aPartnersArgs);
					
					if ( $oPartnerSideBar->have_posts() ) : while ( $oPartnerSideBar->have_posts() ) : $oPartnerSideBar->the_post();
						$thumb =  get_field('logo');  ?>
						<div class="cDivPartnerLogo">
							<a href="<?php echo get_field('url'); ?>"><img src="<?php echo $thumb; ?>" class="img-responsive" /></a>
						</div>
					<?php endwhile;
					endif;
					wp_reset_postdata();
				?>				
			</div>			
			<div class="cDivSliderReadMoreBtn"><a href="/about" class="btn btn-default cAReadMoreBtn">Savoir plus</a></div>
		</div>				
	</div>
</div>
<br style="clear:both;" />
<!-- RSR feeds -->
<?php
$aLatestProjectArgs = array(
	'post_type' => 'project_update',
	'post_status' => 'publish',
	'posts_per_page' => '3',	
	'post_limits' => 3
	
);

$oTheQueryLatestProject = new WP_Query( $aLatestProjectArgs );

if ( $oTheQueryLatestProject->have_posts() ) : 
?>
<div class="cDivRSRFeedsContainer">
	<?php $iPostCount = 0;
	while ( $oTheQueryLatestProject->have_posts() ) : $oTheQueryLatestProject->the_post(); ?>
	<div class="col-md-4 <?php if($iPostCount < 2) : echo 'cMarginRight30'; endif; ?>">
		<div class="cDivLatestProject">
			<div class="cDivLatestProjectImg">
				<div class="cDivLatestProjectRibbon"></div>								
				<?php 
				$sAttachmentLink = get_post_meta($post->ID, 'enclosure', true);
				if($sAttachmentLink!='') {
					$sImgSrc = get_template_directory_uri().'/lib/thumb.php?src='.$sAttachmentLink.'&w=288&h=191&zc=1&q=100';
				}
				//get the project Id to read more link (link to akvo.org site)
				$sReadMoreLink = "http://washmali.akvoapp.org/en/project/";
				$oProjectId = $wpdb->get_results("SELECT project_id,update_id FROM " . $wpdb->prefix . "project_update_log WHERE post_id = ".$post->ID);
				foreach ($oProjectId as $iId){
					$iProjectId = $iId->project_id;
					$iUpdateId = $iId->update_id;
				}
				$sReadMoreLink = $sReadMoreLink.$iProjectId.'/update/'.$iUpdateId;
				if(!@getimagesize($sImgSrc))$sImgSrc='';
				if($sImgSrc==''){
					$sImgSrc = get_stylesheet_directory_uri().'/images/placeholder.jpg';
				} ?>
				<img class="img-responsive" src="<?php echo $sImgSrc; ?>" />				
			</div>
			<div class="cDivLatestProjectTextContainer">
				<div class="cDivLatestProjectTitle">
					 <?php the_title(); ?>
				</div>
				<div class="cDivLatestProjectLaunchDate">
					<?php echo get_the_date(); ?>
				</div>
				<div class="cDivSeparator"></div>
				<div class="cDivLatestProjectText">
					<?php $sLatestProjectText = get_the_content(); 
					echo mb_strimwidth($sLatestProjectText, 0, 100, '...');  ?>
				</div>
				<div class="cDivSliderReadMoreBtn"><a href="<?php echo $sReadMoreLink; ?>" class="btn btn-default cAReadMoreBtn">Savoir plus</a></div>
			</div>
		</div>
	</div>
	<?php $iPostCount++; endwhile; ?>
	<br style="clear:both;" />	
</div>
<div class="cDivSeeAllBtn"><a href="/project-updates" class="btn btn-default cASeeAllBtn">Savoir tous</a></div>
<?php wp_reset_postdata(); endif; ?>

<!-- data and map -->
<div class="cDivMapWrapper">
<?php 
$aMapsArgs = array(
	'category_name'=> 'maps',
	'order' => 'DESC',
	'posts_per_page' => 3,
	'post_limits' => 3
);
$oMaps = new WP_Query($aMapsArgs);

if ( $oMaps->have_posts() ) : while ( $oMaps->have_posts() ) : $oMaps->the_post(); ?>
	<div class="cDivMapContainer col-md-12 cMarginBottom20">
		<div class="cDivMapiFrameWrapper col-md-4">
			<div class="cDivDataandMapRibbon"></div>
			<div class="cDivMap">
				<?php $sCartoDBMapLink =  get_field('cartodb_link'); ?>
				<!--<iframe width='100%' height='100%' frameborder='0' src='<?php //echo $sCartoDBMapLink; ?>' allowfullscreen webkitallowfullscreen mozallowfullscreen oallowfullscreen msallowfullscreen></iframe>-->				
			</div>			
		</div>
		<div class="cDivMapTextContainer col-md-8">
			<div class="cDivMapTitle"><?php the_title(); ?></div>
			<div class="cDivMapText"><?php the_excerpt(); ?></div>
			<div class="cDivSliderReadMoreBtn">				
				<a href="/data-and-map/#<?php echo $post->post_name; ?>" class="btn btn-default cAReadMoreBtn">Savoir plus</a>
			</div>
		</div>
	</div>
<?php endwhile; ?>
<?php endif; ?>
	
</div>	
<div class="cDivSeeAllBtn"><a href="/data-and-map" class="btn btn-default cASeeAllBtn">Savoir tous</a></div>

<?php get_footer(); ?>

<script type="text/javascript">
	
	   jQuery('.bxslider').bxSlider({
		   auto: true,		   
		   controls: false,
		   pager: true
	   });
	
</script>