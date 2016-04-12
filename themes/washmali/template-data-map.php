<?php

/* 
 Template Name: data and map
 */
?>

<?php get_header(); ?>

<?php get_template_part('template-part', 'head'); ?>

<?php get_template_part('template-part', 'topnav'); ?>

<?php if ( have_posts() ) : ?>
<div class="row cDivMapandDataPage cBackgroundWhite cGrayBorderAllSide">
	<?php while ( have_posts() ) : the_post(); ?>
		<h2><?php the_title(); ?></h2>
		<div class="cDivSeparator"></div>
		<?php the_content(); ?>
	<?php endwhile; ?>		
</div>
<?php endif; ?>
<!-- Map section -->
<?php
$aMapsArgs = array(
	'category_name'=> 'maps',
	'order' => 'DESC',
	'posts_per_page' => -1,
	'nopaging' => true
);
$oMaps = new WP_Query($aMapsArgs);

if ( $oMaps->have_posts() ) : 
?>
<div class="cDivMapWrapper" id="iDivDMPageMapWrapper">
	<?php 
	while ( $oMaps->have_posts() ) : $oMaps->the_post(); ?>
		<div class="row cDivMapContainer cMarginBottom20 cDivMapandDataPage" id="<?php echo $post->post_name; ?>">
			<?php $sCartoDBMapLink =  get_field('cartodb_link'); 
			if($sCartoDBMapLink != "") { ?>
			<div class="cDivMapiFrameWrapper">
				<!--<div class="cDivDataandMapRibbon"></div>-->
				<div class="cDivMap">					
					<iframe width='100%' height='100%' frameborder='0' src='<?php echo $sCartoDBMapLink; ?>' allowfullscreen webkitallowfullscreen mozallowfullscreen oallowfullscreen msallowfullscreen></iframe>					
				</div>					
			</div>
			<?php } ?>
			<div class="cDivMapTextContainer">
				<div class="cDivMapTitle"><?php the_title(); ?></div>
				<div class="cDivMapText"><?php the_content(); ?></div>				
			</div>
		</div>
	<?php endwhile; ?>		
</div>
<?php endif; ?>
<!-- Data section -->
<?php
$aDataArgs = array(
	'category_name'=> 'data',
	'order' => 'DESC',
	'posts_per_page' => -1,
	'nopaging' => true
);
$oData = new WP_Query($aDataArgs);

if ( $oData->have_posts() ) : 
?>
<div class="row cDivDataWrapper cBackgroundWhite cGrayBorderAllSide">
	<h2>Data</h2>
	<div class="cDivSeparator"></div>
	<?php 
	while ( $oData->have_posts() ) : $oData->the_post(); ?>
	<div class="cDivDataContainer">
		<div class="cDivDataTitle"><?php the_title(); ?></div>
		<!--<div class="cDivDataDate"><?php //echo get_the_date(); ?></div>-->
		<div class="cDivDataText"><?php the_content(); ?></div>
		<div class="cDivDownloadBtn">				
				<?php 
				$aDocumentOne =  get_field('document_one'); 
				$aDocumentOneExt = wp_check_filetype($aDocumentOne['url']);
				$sDocumentOneExt = $aDocumentOneExt['ext'];				
//					Array ( [id] => 259 [alt] => [title] => wash_mali [caption] => [description] => [mime_type] => application/vnd.openxmlformats-officedocument.spreadsheetml.sheet [url] => http://washmali.akvofoundation.org/wp-content/uploads/sites/15/2015/03/wash_mali.xlsx )
				?>
			<div class="cDivSliderReadMoreBtn cDivDownloadXLS"><a href="<?php echo $aDocumentOne['url']; ?>" class="btn btn-default cAReadMoreBtn"><?php echo strtoupper($sDocumentOneExt); ?></a></div>

				<?php					
				$aDocumentTwo =  get_field('document_two'); 
				$aDocumentTwoExt = wp_check_filetype($aDocumentTwo['url']);
				$sDocumentTwoExt = $aDocumentTwoExt['ext'];
				if(!empty($aDocumentTwo)) {
				?>
				<div class="cDivSliderReadMoreBtn cDivDownloadCSV"><a href="<?php echo $aDocumentTwo['url']; ?>" class="btn btn-default cAReadMoreBtn"><?php echo strtoupper($sDocumentTwoExt); ?></a></div>				
				<?php } ?>
		</div>			
	</div>
	<?php endwhile; ?>
</div>
<?php endif; ?>

<?php get_footer(); ?>