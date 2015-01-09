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
                <div class="col-md-<?php echo $headerLogoWidth;?> dmbs-header-img">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php header_image(); ?>" height="<?php echo get_custom_header()->height; ?>" width="<?php echo get_custom_header()->width; ?>" alt="" /></a>
                </div>
            <?php endif; ?>
            <?php
                    get_sidebar('header');
                    endif;
            ?>


        </div>

    <?php endif; ?>
</div>
</div>