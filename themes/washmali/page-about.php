<?php get_header(); ?>

<?php get_template_part('template-part', 'head'); ?>

<?php get_template_part('template-part', 'topnav'); ?>

<div class="row cDivAboutPageContainer">
	<div class="col-md-8">
	<?php if ( have_posts() ) : ?>
	<?php while ( have_posts() ) : the_post(); ?>	
		<div class="cDivAboutPage cBackgroundWhite cGrayBorderAllSide">		
			<h2><?php the_title(); ?></h2>
			<?php the_content(); ?>
		</div>		
	<?php endwhile; ?>
		<div class="cDivContactForm cBackgroundWhite cGrayBorderAllSide">
			<h2>Contactez nous</h2>
			<?php //echo do_shortcode('[contact-form-7 id="253" title="Contact form 1"]'); ?>
			<?php echo do_shortcode('[contact-form-7 id="304" title="Sans titre"]'); ?>			
		</div>
	</div>
<?php endif; ?>
	<div class="cDivPartners cBackgroundWhite cGrayBorderAllSide col-md-4"> 
		<h2>Partenaires</h2>
		<div class="cDivSeparator"></div>
		<div class="cDivPartnerLogoWrapper">
		<?php 
			$aPartnersArgs = array(
				'category_name'=> 'partners',
				'order' => 'DESC',
				'posts_per_page' => -1,
				'nopaging' => true
			);
			$oPartnerSideBar = new WP_Query($aPartnersArgs);
			$iPostCount = 1;
			if ( $oPartnerSideBar->have_posts() ) : 
				$iNumberofPosts = $oPartnerSideBar->found_posts; 
				while ( $oPartnerSideBar->have_posts() ) : $oPartnerSideBar->the_post();
				$thumb =  get_field('logo');  ?>				
				<div class="cDivPartnerLogo">
					<a href="<?php echo get_field('url'); ?>"><img src="<?php echo $thumb; ?>" class="img-responsive" /></a>
				</div>				
				<div class="cDivPartnerText">
					<?php the_content(); ?>
				</div>
				<?php if($iPostCount < $iNumberofPosts) : ?>
				<div class="cDivSeparator"></div>
				<?php endif; ?>
			<?php $iPostCount++; endwhile;
			endif;
			wp_reset_postdata();
		?>
		</div>	
	</div>
</div>
<?php get_footer(); ?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             