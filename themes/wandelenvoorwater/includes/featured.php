<?php
$ids = array();
$featured_cat = get_option('quadro_feat_cat');
$featured_num = (int) get_option('quadro_featured_num');
?>


<?php if ( is_active_sidebar( 'about-box' ) ) : ?>

                <?php dynamic_sidebar( 'about-box' ); ?>

            <?php else : ?>

                <!-- Create some custom HTML or call the_widget().  It's up to you. -->

            <?php endif; ?>

