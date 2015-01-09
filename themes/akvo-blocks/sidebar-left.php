<?php
    global $dm_settings;
    if ($dm_settings['left_sidebar'] != 0) : ?>
    <div class="col-md-<?php echo $dm_settings['left_sidebar_width']; ?> dmbs-left">
        <?php dynamic_sidebar( 'Default Left Sidebar' ); ?>
    </div>
<?php endif; ?>