<?php global $dm_settings; ?>

<div class="cDivHeaderContainer">
    <div class="container">
		<?php if ($dm_settings['show_header'] != 0) : ?>
		<?php 
        $headerSideBarWidth = $dm_settings['header_sidebar_width']; 
        $headerLogoWidth = 12 -  $headerSideBarWidth;
        ?>
		<div class="row dmbs-header">
			<?php if ( get_header_image() != '' ) : ?>
			<div class="col-xs-12 col-sm-4 col-md-2">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="cALogo">
					<img src="<?php header_image(); ?>" height="<?php echo get_custom_header()->height; ?>" width="<?php echo get_custom_header()->width; ?>" alt="" class="img-responsive" />					
				</a>
			</div>
			<div class="col-xs-12 col-sm-8 col-md-6">
				<div class="cDivHeaderLogoText">
					<div class="cDivHeaderTextOne">Ministère de l’Environnement, de l'Eau et de l’Assainissement</div>
					<div class="cDivHeaderTextTwo">Direction Nationale de l'Hydraulique du Mali</div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-4 cDivSearchBoxContainer">
				<form method="get" id="searchform" action="<?php bloginfo('siteurl'); ?>">					
					<div class="cDivSearchText">
						<input type="search" required="" placeholder="Rechercher" class="" name="s" id="s">
					</div>
					<div class="cDivSearchBtn">
						<input type="submit" value="Aller!" class="btn cInputSumitBtn cFontBold" id="searchsubmit">
					</div>					
			   </form>
			</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
</div>