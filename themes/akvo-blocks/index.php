<?php get_header(); ?>

<?php get_template_part('template-part', 'head'); ?>

<?php get_template_part('template-part', 'topnav'); ?>

<!-- start content container -->
<div class="row dmbs-content homerow">

    <?php //left sidebar ?>
    

    <div class="col-xs-12 dmbs-main">
        <?php 
        //display home areas
        for($i=1;$i<=3;$i++){
            ?>
        <div class="cDivHomeArea cDivHomeArea<?php echo $i; ?>">
            <div class="tag"></div>
            <?php dynamic_sidebar('home-area-'.$i);?>
        </div>
        
            <?php
        }
        ?><br style="clear:both;" />
        <?php
//                $latestvideo = AkvoSiteConfig::getLatestVideo('RAINfoundation');
//                $post = (object)$latestvideo;
//                get_template_part('includes/entry');
                global $wpdb;
                $post = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."posts WHERE post_type='news' AND post_status='publish' ORDER BY post_date DESC");
                get_template_part('includes/entry');
                ?>
                <?php
                $post = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."posts WHERE post_type='post' AND post_status='publish' ORDER BY post_date DESC");
                get_template_part('includes/entry');
                
                // Restore original Query & Post Data
		wp_reset_query();
		wp_reset_postdata();
                $args['post_type']='project_update';
                $args['posts_per_page']='1';
                $query = new WP_Query();
                $updates = $query->query($args);
                if ( $updates ) : foreach ($updates AS $post ) :
                    get_template_part('includes/entry');
                endforeach;endif;
			?>    
        <div class="cDivHomeArea cDivHomeArea4">
            <div class="tag"></div>
            <?php dynamic_sidebar('home-area-4');?>
        </div>
   </div>

   

</div>
<!-- end content container -->

<?php get_footer(); ?>

