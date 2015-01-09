<?php
    global $dm_settings;
    if ($dm_settings['header_sidebar'] != 0) : ?>
    <div class="col-md-<?php echo $dm_settings['header_sidebar_width']; ?>">
        <?php //get the right sidebar
        dynamic_sidebar( 'Header Sidebar' ); ?>
    </div>
<?php endif; ?>