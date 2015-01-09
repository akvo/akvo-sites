<?php
ini_set('memory_limit','64M');

// get parameters and build the query
$tax_query = array();

$search = "";
if (!empty($_REQUEST['query'])){
	$search = $_REQUEST['query'];
}

$countries = "";
if (!empty($_REQUEST['countries'])){
	$countries = $_REQUEST['countries'];
	array_push($tax_query, array(
			'taxonomy' => 'partner_countries',
			'field' => 'slug',
			'terms' => explode(",", $countries)
		)
	);
}

$types = "";
if (!empty($_REQUEST['types'])){
	$types = $_REQUEST['types'];
	array_push($tax_query, array(
			'taxonomy' => 'partner_types',
			'field' => 'slug',
			'terms' => explode(",", $types)
		)
	);
}

$cities = "";
if (!empty($_REQUEST['cities'])){
	$cities = $_REQUEST['cities'];
	array_push($tax_query, array(
			'taxonomy' => 'partner_cities',
			'field' => 'slug',
			'terms' => explode(",", $cities)
		)
	);

}
$activities = "";
if (!empty($_REQUEST['activities'])){
	$activities = $_REQUEST['activities'];
	array_push($tax_query, array(
			'taxonomy' => 'partner_activities',
			'field' => 'slug',
			'terms' => explode(",", $activities)
		)
	);
}

$themes = "";
if (!empty($_REQUEST['themes'])){
	$themes = $_REQUEST['themes'];
	array_push($tax_query, array(
			'taxonomy' => 'partner_themes',
			'field' => 'slug',
			'terms' => explode(",", $themes)
		)
	);
}

$geo = "";
if (!empty($_REQUEST['geo'])){
	$geo = $_REQUEST['geo'];
	array_push($tax_query, array(
			'taxonomy' => 'partner_geo_focus_region',
			'field' => 'slug',
			'terms' => explode(",", $geo)
		)
	);
}

$offset = 0;
if (!empty($_REQUEST['offset'])){
	$offset = $_REQUEST['offset'];
}

$limit = 0;
if (!empty($_REQUEST['limit'])){
	$limit = $_REQUEST['limit'];
}


$args = array( 'fields' => 'ids', 'post_type' => 'partner', 'posts_per_page' => 10, 'orderby' => 'title', 'order' => 'ASC', 'offset' => $offset, 'limit' => $limit);

$args["tax_query"] = $tax_query;
$args["s"] = $search;

$partner_array = array();

$loop = new WP_Query( $args );


while ( $loop->have_posts() ) : $loop->the_post();
	
	$partner_city = "unknown";
	$post_id = get_the_ID();

	$terms = wp_get_post_terms( $post_id, "partner_cities" );
	if ( !empty( $terms ) && !is_wp_error( $terms ) ){
		$partner_city = "";
		foreach($terms as $term){
			$partner_city .= $term->name . ", ";
		}
		$partner_city = substr($partner_city, 0, -2);
	}

	$partner_type = "unknown";

	$terms = wp_get_post_terms( $post_id, "partner_types" );
	if ( !empty( $terms ) && !is_wp_error( $terms ) ){
	    $partner_type = "";
		foreach($terms as $term){
			$partner_type .= $term->name . ", ";
		}
		$partner_type = substr($partner_type, 0, -2);
	}

	$partner_country = "unknown";

	$terms = wp_get_post_terms( $post_id, "partner_countries" );
	if ( !empty( $terms ) && !is_wp_error( $terms ) ){
	    $partner_country = "";
		foreach($terms as $term){
			$partner_country .= $term->name . ", ";
		}
		$partner_country = substr($partner_country, 0, -2);
	}
        
    $partner_activity = "unknown";

	$terms = wp_get_post_terms( $post_id, "partner_activities" );
	if ( !empty( $terms ) && !is_wp_error( $terms ) ){
	    $partner_activity = "";
		foreach($terms as $term){
			$partner_activity .= $term->name . ", ";
		}
		$partner_activity = substr($partner_activity, 0, -2);
	}

	$partner_themes = "unknown";

	$terms = wp_get_post_terms( $post_id, "partner_themes" );
	if ( !empty( $terms ) && !is_wp_error( $terms ) ){
	    $partner_themes = "";
		foreach($terms as $term){
			$partner_themes .= $term->name . ", ";
		}
		$partner_themes = substr($partner_themes, 0, -2);
	}

	$partner_geo_focus_region = "unknown";


	$terms = wp_get_post_terms( $post_id, "partner_activities" );
	if ( !empty( $terms ) && !is_wp_error( $terms ) ){
	    $partner_geo_focus_region = "";
		foreach($terms as $term){
			$partner_geo_focus_region .= $term->name . ", ";
		}
		$partner_geo_focus_region = substr($partner_geo_focus_region, 0, -2);
	}
	

	$partner_latitude = get_post_meta( $post_id, "partner_latitude", true );
	$partner_longitude = get_post_meta( $post_id, "partner_longitude", true );
	$partner_title = get_the_title($post_id);
	$partner_website = get_post_meta( $post_id, "partner_website", true );
	$partner_email = get_post_meta( $post_id, "partner_email", true );
	$partner_focus_countries = get_post_meta( $post_id, "partner_geo_focus_country", true );
	$partner_head_office = get_post_meta( $post_id, "partner_head_office", true);

	$partner = array(
		"partner_id" => $post_id,
		"partner_city" => $partner_city,
		"partner_type" => $partner_type,
		"partner_country" => $partner_country,
		"partner_latitude" => $partner_latitude,
		"partner_longitude" => $partner_longitude,
		"partner_title" => $partner_title,
		"partner_website" => $partner_website,
		"partner_email" => $partner_email,
		"partner_activities" => $partner_activity,
		"partner_focus_countries" => $partner_focus_countries,
		"partner_head_office" => $partner_head_office,
		"partner_themes" => $partner_themes,
		"partner_geo_focus_region" => $partner_geo_focus_region
	);

	array_push($partner_array, $partner);
	

endwhile;

foreach($partner_array as $partner) {
?>
<tr>
  <td class="col-md-6">
  	<div class="zz-expand-partner zz-list-title"><?php echo $partner["partner_title"]; ?></div>
  	<a data-id="<?php echo $partner["partner_id"]; ?>" href="#expand" class="zz-expand-partner zz-list-more-details">More details</a>
  </td>
  <td class="col-md-6">
    <table class="table no-top-border hidden-xs zz-partner-inner-table">
      <tbody>
        <tr>
          <td class="smallcap uppercase dark-blue-content col-md-12">Type: <?php echo $partner["partner_type"]; ?></td>
        </tr>
        <tr>
          <td class="smallcap uppercase dark-blue-content col-md-12">Activities: <?php echo $partner["partner_activities"]; ?></td>
        </tr>
        <tr>
          <td class="smallcap uppercase dark-blue-content col-md-12">Themes: <?php echo $partner["partner_themes"]; ?></td>
        </tr>
        <tr>
          <td class="smallcap uppercase dark-blue-content col-md-12">Focus regions: <?php echo $partner["partner_geo_focus_region"]; ?></td>
        </tr><?php /*
        <tr>
          <td class="smallcap uppercase dark-blue-content col-md-12">Contact: <?php echo $partner["partner_email"]; ?></td>
        </tr> <?php */ ?>
      </tbody>
    </table>
  </td>
</tr>

<tr class="zz-partner-more-details" data-id="<?php echo $partner["partner_id"]; ?>">
  <td colspan="2">
  	<table class="table no-top-border hidden-xs zz-partner-inner-table">
      <tbody>
        <tr>
          <td class="smallcap uppercase dark-blue-content col-md-12">Head office: <?php echo $partner["partner_head_office"]; ?></td>
        </tr>
        <tr>
          <td class="smallcap uppercase dark-blue-content col-md-12">Focus countries: <?php if(empty($partner["partner_focus_countries"])){ echo "Unkown";} else { echo $partner["partner_focus_countries"]; } ?></td>
        </tr>
        <tr>
          <td class="smallcap uppercase dark-blue-content col-md-12">Web Address: <a target="_blank" href="<?php echo $partner["partner_website"]; ?>"><?php if(empty($partner["partner_website"])){ echo "Unkown";} else { echo $partner["partner_website"]; } ?></a></td>
        </tr>
	  </tbody>
	</table>
  </td>
  <?php /*
  <td class="col-md-6">
    <div data-longitude="<?php echo $partner["partner_longitude"]; ?>" data-latitude="<?php echo $partner["partner_latitude"]; ?>" data-name="<?php echo $partner["partner_title"]; ?>" class="small-map">
    </div>
  </td> 
  */ ?>
</tr>

<?php } ?>

<tr class="hidden-tr">
    <td><input type="hidden" class="oipa-list-amount" value="<?php echo $loop->found_posts; ?>"></td>
    <td></td>
</tr>

<?php 

wp_reset_postdata();

