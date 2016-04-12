<?php global $dm_settings; ?>

<div class="cDivHeaderContainer">
    <div class="container dmbs-container">

    <?php if ($dm_settings['show_header'] != 0) : ?>
        <?php 
        $headerSideBarWidth = $dm_settings['header_sidebar_width']; 
        $headerLogoWidth = 12 -  $headerSideBarWidth;
        ?>
        <div class="row dmbs-header">

            <?php if ( get_header_image() != '') : ?>

            <?php if ( get_header_image() != '' ) : ?>
                <div class="col-md-8 dmbs-header-img">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="cALogo">
						<img src="<?php header_image(); ?>" height="<?php echo get_custom_header()->height; ?>" width="<?php echo get_custom_header()->width; ?>" alt="" class="img-responsive" />
						<!--<img src="<?php echo get_stylesheet_directory_uri() . '/images/logo_03.png' ?>" height="<?php echo get_custom_header()->height; ?>" width="<?php echo get_custom_header()->width; ?>" alt="" />-->
					</a>
					<div class="cDivHeaderLogoText">
						<div class="cDivHeaderTextOne">Ministère de l’Environnement, de l'Eau et de l’Assainissement</div>
						<div class="cDivHeaderTextTwo">Direction Nationale de l'Hydraulique du Mali</div>
					</div>
                </div>				
            <?php endif; ?>			
			<div class="col-md-4 cDivSearchBoxContainer">
				<form method="get" id="searchform" action="<?php bloginfo('siteurl'); ?>">
					<fieldset>
						<div class="col-sm-9">
							<input type="search" required="" placeholder="Rechercher" class="form-control" name="s" id="s">
						</div>
						<div class="col-sm-3">
							<input type="submit" value="Aller!" class="btn cInputSumitBtn cFontBold" id="searchsubmit">
						</div>
					</fieldset>
			   </form>
			</div>        
            <?php
                    //get_sidebar('header');
                    endif;
            ?>


        </div>

    <?php endif; ?>
</div>
</div>