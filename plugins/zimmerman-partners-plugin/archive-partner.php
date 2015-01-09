<?php get_header();
?>

<div style="background-color: #c4beb7;">

<div id="zz-partners">
	<div id="zz-partner-filters">
		
		<div id="zz-partner-country-filters" class="zz-partner-filter-block">
			<div class="zz-partner-filter-title">Country</div>
			<div class="zz-partner-filter-dropdown">

				<select id="zz-partner-countries-dropdown" name="zz-partner-countries-dropdown" class="form-control">
					<?php 
					$terms = get_terms("partner_countries");
					 if ( !empty( $terms ) && !is_wp_error( $terms ) ){
					 	echo '<option value="0">Select...</option>';
					     foreach ( $terms as $term ) {
					     	echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
					       //echo '<div class="map-filter-country" style="display:inline-block;width: 100%; height: 40px; color: white; font-size: 16px;padding-left:10%;"><input name="countries[]" type="checkbox" style="display:inline-block;margin-right:4px;" value="' . $term->slug . '">' . $term->name . '</div>';
					        
					     }
					 }
				 	?>
				</select>

			</div>
			<div class="zz-partner-filter-selected">

			</div>
		</div>

		<div id="zz-partner-type-filters" class="zz-partner-filter-block">
			<div class="zz-partner-filter-title">Type</div>
			<div class="zz-partner-filter-dropdown">
			
			<select id="zz-partner-types-dropdown" name="zz-partner-types-dropdown" class="form-control">
				<?php 
				$terms = get_terms("partner_types");
				 if ( !empty( $terms ) && !is_wp_error( $terms ) ){
				 	echo '<option value="0">Select...</option>';
				     foreach ( $terms as $term ) {
				     	echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
				     }
				 }
			 	?>
			</select>


			<?php /*
				<div id="zz-partner-types-dropdown" class="zz-partners-filter-dropdown" name="zz-partner-types-dropdown">
					
					$terms = get_terms("partner_types");
					if ( !empty( $terms ) && !is_wp_error( $terms ) ){
						echo '<div class="zz-partner-option-default" value="0">Select...<div class="zz-partner-option-default-icon"></div></div>';
						echo '<div class="zz-partner-option-hidden">';
					    foreach ( $terms as $term ) {
					    	echo '<div class="zz-partner-option-type" name="' . $term->name . '" value="' . $term->slug . '"><div class="zz-partner-option-name">' . $term->name . '</div><div class="zz-partner-option-glyph"></div></div>';
					    }
					    echo '</div>';
					} 
		 		</div>
				*/
			?>
			</div>
			<div class="zz-partner-filter-selected">



			</div>
		</div>
	</div>
	<div id="zz-partner-main-bar">
		<div id="zz-partner-map-wrapper">

			<div id="zz-partner-map">


			</div>

		</div>

		<a href="#" id="zz-partners-show-organisation-in-list">Show organisations in list</a>

		<div id="zz-partner-list">

		</div>
	</div>
	

</div>

<div id="zz-partners-below-map">

</div>

</div>

<script>

var zz_partner_base_url = "<?php echo plugins_url('/zimmerman-partners-plugin/'); ?>";
var zz_partners_base_url = "<?php echo site_url(); ?>"; 
</script>

<?php 

wp_register_style( 'leafletcss', 'http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.css' );
wp_register_style( 'zz_partners_css', plugins_url("/zimmerman-partners-plugin/css/zz_partners.css") );
wp_register_script( 'leafletjs', 'http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.js' );
wp_register_script( 'jqueryui', get_template_directory_uri() . '/js/jquery-ui.js' );
wp_register_script( 'zz_partners', plugins_url("/zimmerman-partners-plugin/js/partners.js") );
wp_register_script( 'bouncemarker', plugins_url("/zimmerman-partners-plugin/js/bouncemarker.js") );
wp_register_script( 'zoom_js', "//api.tiles.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v0.0.2/Leaflet.fullscreen.min.js");
wp_register_style( 'zoom_css', "//api.tiles.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v0.0.2/leaflet.fullscreen.css");

wp_enqueue_style('zoom_css');
wp_enqueue_script('jquery');
wp_enqueue_script('leafletjs');
wp_enqueue_script('zoom_js');
wp_enqueue_script('jqueryui');
wp_enqueue_script('zz_partners');
wp_enqueue_script('bouncemarker');
wp_enqueue_style('leafletcss');
wp_enqueue_style('zz_partners_css');



get_footer(); ?>


