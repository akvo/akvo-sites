<div id="zz-partners-wrapper">

	<div id="zz-partners">
		<div id="zz-partner-filters">

			<div id="zz-partner-countries-filters" class="zz-partner-filter-block">
				<div class="zz-partner-filter-title">Country</div>
				<div class="zz-partner-filter-dropdown">

					<select data-filter="countries" id="zz-partner-countries-dropdown" name="zz-partner-countries-dropdown" class="form-control zz-filter-selection">
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
                    
            <div id="zz-partner-activities-filters" class="zz-partner-filter-block">
				<div class="zz-partner-filter-title">Activities</div>
				<div class="zz-partner-filter-dropdown">

					<select data-filter="activities" id="zz-partner-activities-dropdown" name="zz-partner-activities-dropdown" class="form-control zz-filter-selection">
						<?php 
						$terms = get_terms("partner_activities");
						 if ( !empty( $terms ) && !is_wp_error( $terms ) ){
						 	echo '<option value="0">Select...</option>';
						     foreach ( $terms as $term ) {
						     	echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
						 
						     }
						 }
					 	?>
					</select>

				</div>
				<div class="zz-partner-filter-selected">

				</div>
			</div><!--
                    
-->                    <div id="zz-partner-themes-filters" class="zz-partner-filter-block">
				<div class="zz-partner-filter-title">Themes</div>
				<div class="zz-partner-filter-dropdown">

					<select data-filter="themes" id="zz-partner-themes-dropdown" name="zz-partner-themes-dropdown" class="form-control zz-filter-selection">
						<?php 
						$terms = get_terms("partner_themes");
						 if ( !empty( $terms ) && !is_wp_error( $terms ) ){
						 	echo '<option value="0">Select...</option>';
						     foreach ( $terms as $term ) {
						     	echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
						
						        
						     }
						 }
					 	?>
					</select>

				</div>
				<div class="zz-partner-filter-selected">

				</div>
			</div>
			<div id="zz-partner-geographical_focus-filters" class="zz-partner-filter-block">
				<div class="zz-partner-filter-title">Geographical focus</div>
				<div class="zz-partner-filter-dropdown">

					<select data-filter="geographical_focus" id="zz-partner-geo-dropdown" name="zz-partner-geo-dropdown" class="form-control zz-filter-selection">
						<?php 
						$terms = get_terms("partner_geo_focus_region");
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

			<div id="zz-partner-types-filters" class="zz-partner-filter-block">
				<div class="zz-partner-filter-title">Type</div>
				<div class="zz-partner-filter-dropdown">
				
				<select data-filter="types" id="zz-partner-types-dropdown" name="zz-partner-types-dropdown" class="form-control zz-filter-selection">
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
					<div class="row">
					  <div class="col-md-4">
					  </div>
					  <div class="col-md-8 hidden-xs">

			            <ul class="no-bullets map-menu">

			              <li class="view-button"><a href="javascript:void(0)" class="primary-button mapview"><i class="fa fa-fw globe-icon globe-icon-blue"></i> map view</a></li>
			              <li class="view-button"><a href="javascript:void(0)" class="secondary-button listview"><i class="glyphicon glyphicon-list"></i>list view</a></li>
			            </ul>
			          </div>
			        </div>

					<div id="zz-partner-no-partners-found"></div>
				</div>
			</div>

			<div id="zz-partner-list-view-buttons" class="row">
			  <div class="col-md-4">
			  </div>
			  <div class="col-md-8 hidden-xs">

	            <ul class="no-bullets map-menu">

	              <li class="view-button"><a href="javascript:void(0)" class="primary-button mapview"><i class="fa fa-fw globe-icon"></i> map view</a></li>
	              <li class="view-button"><a href="javascript:void(0)" class="secondary-button listview"><i class="glyphicon glyphicon-list"></i>list view</a></li>
	            </ul>
	          </div>
	        </div>




			<div id="oipa-information" class="row">
			  <div class="col-md-12">
			    <div class="content-white">
			    	<a id="zz-partner-download-csv" href="<?php echo plugins_url('/zimmerman-partners-plugin/') . "csv/R4F%20partner%20CSV.csv"; ?>">Download data in CSV format</a>
			      <h3><a id="oipa-list-count" href="#"></a> Organisations</h3>
			      <div class="border"></div>
			      <div class="row">
			        <div class="col-md-6">
			          <form id="nav-top-search" role="search" method="get" action="/search/">
			            <div class="input-group">
			              <input id="oipa-list-search" type="search" name="q" class="form-control focusedInput" placeholder="Search...">
			              <span class="input-group-btn">
			              <button type="submit" class="btn btn-default focusedInput"><i class="glyphicon glyphicon-search"></i></button>
			              </span>
			            </div>
			          </form>
			        </div>
			      </div>
			      <br/>

			    </div>
			 
					<div id="oipa-list-wrapper" class="large-row-table">
					    <table class="table table-striped table-hover zz-partner-outter-table">
					      <tbody id="oipa-list">
					        <?php include(plugin_dir_path( __FILE__ ) . 'partners-list-html.php'); ?>
					      </tbody>
					    </table>
				    </div>

				<div class="content-white">
					<div id="oipa-list-pagination">
				    	
					</div>

			    </div>
			  </div>
			</div>

		</div>
	</div>
</div>

<script>

var zz_partner_base_url = "<?php echo plugins_url('/zimmerman-partners-plugin/'); ?>";
var zz_partners_base_url = "<?php echo site_url(); ?>"; 

</script>

<?php 

wp_register_style( 'leafletcss', 'http://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.3/leaflet.css' );
wp_register_style( 'zz_partners_css', plugins_url("/zimmerman-partners-plugin/css/zz_partners.css") );
wp_register_style( 'zoom_css', "//api.tiles.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v0.0.2/leaflet.fullscreen.css");
wp_register_style( 'jqueryuicss', "http://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css");
wp_enqueue_style('leafletcss');
wp_enqueue_style('zz_partners_css');
wp_enqueue_style('jqueryuicss');
wp_enqueue_style('zoom_css');

wp_register_script( 'leafletjs', 'http://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.3/leaflet.js' );
wp_register_script( 'jqueryui', 'http://code.jquery.com/ui/1.10.4/jquery-ui.min.js' );
wp_register_script( 'bouncemarker', plugins_url("/zimmerman-partners-plugin/js/bouncemarker.js") );
wp_register_script( 'zoom_js', "//api.tiles.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v0.0.2/Leaflet.fullscreen.min.js");
wp_register_script( 'bootpag', plugins_url("/zimmerman-partners-plugin/js/jquery.bootpag.min.js") );
wp_register_script( 'zz_partners', plugins_url("/zimmerman-partners-plugin/js/partners.js") );

wp_enqueue_script('jquery');
wp_enqueue_script('leafletjs');
wp_enqueue_script('zoom_js');
wp_enqueue_script('jqueryui');
wp_enqueue_script('bouncemarker');
wp_enqueue_script('bootpag');
wp_enqueue_script('zz_partners');


?>

