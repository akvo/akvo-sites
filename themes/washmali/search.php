<?php get_header(); ?>

<?php get_template_part('template-part', 'head'); ?>

<?php get_template_part('template-part', 'topnav'); ?>


<!-- start content container -->
<div class="row dmbs-content">

    

    <div class="col-md-12 dmbs-main">
		
        <?php // theloop			
        if ( have_posts() ) : ?>
		<div class="cDivSearchKeywordWrapper">
			<?php echo 'Your search keyword is <span class="cSpanSearchKeyword">'. get_search_query() . '</span> and the results are:'; ?>
		</div>
			<?php while ( have_posts() ) : the_post(); ?>
            <?php 
            //get_template_part('includes/entry');
			$aCatDetails = get_the_category();
			if($post->post_type=='project_update') {
				$sReadMoreLink = "http://washmali.akvoapp.org/en/project/";
				$oProjectId = $wpdb->get_results("SELECT project_id,update_id FROM " . $wpdb->prefix . "project_update_log WHERE post_id = ".$post->ID);
				foreach ($oProjectId as $iId){
					$iProjectId = $iId->project_id;
					$iUpdateId = $iId->update_id;
				}
				$sReadMoreLink = $sReadMoreLink.$iProjectId.'/update/'.$iUpdateId;
			} elseif ($aCatDetails[0]->cat_name == 'partners') {
				
				$sReadMoreLink = '/about';
			
			} elseif ($aCatDetails[0]->cat_name == 'data' || $aCatDetails[0]->cat_name == 'maps') {
				
				$sReadMoreLink = '/data-and-map';
				
			} else {
				
				$sReadMoreLink = get_permalink(get_the_ID());
			} 
            ?>
           	
				
			<div class="cDivSearchResultItemContainer">
				<div class="cDivSearchResultItemTitle">
					 <?php the_title(); ?>
				</div>				
				<div class="cDivSearchResultItemText">
					<?php //$sLatestProjectText = get_the_content(); 
					//echo mb_strimwidth($sLatestProjectText, 0, 100, '...');  
					echo textClipper(strip_tags(get_the_content()), 200);
					?>
				</div>				
				<div class="cDivSliderReadMoreBtn"><a href="<?php echo esc_url($sReadMoreLink); ?>" class="btn btn-default cAReadMoreBtn">Savoir plus</a></div>
			</div>
			<div class="cDivSeparator"></div>
		
	
        <?php endwhile; ?>
        <?php else: ?>

            <?php //get_404_template();  ?>
			
			<div class="cDivSearchResultItemContainer">
				<div class="cDivSearchResultItemTitle">
					Aucun résultat trouvé pour le mot-clé <?php echo get_search_query(); ?> !!!. S'il vous plaît essayez avec mot clé différent. 
				</div>
			</div>

        <?php endif; ?>

    </div>

    

</div>
<!-- end content container -->

<?php get_footer(); ?>
