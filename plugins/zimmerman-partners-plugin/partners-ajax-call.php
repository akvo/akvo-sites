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



$args = array( 'fields' => 'ids', 'post_type' => 'partner', 'post_status' => 'publish', 'posts_per_page' => 999, 'orderby' => 'title', 'order' => 'ASC');

$args["tax_query"] = $tax_query;
$args["s"] = $search;


$loop = new WP_Query( $args );
$partner_array = array();


while ( $loop->have_posts() ) : $loop->the_post();


	
	$post_taxonomies = wp_get_object_terms(array(get_the_ID()), array('partner_cities', 'partner_types', 'partner_countries'));


	$partner_city = "unknown";
    $partner_city_slug = "unknown";
    $partner_type = "unknown";
    $partner_country = "unknown";
    $partner_country_slug = "unknown";
    
	foreach($post_taxonomies as $tax){
		if($tax->taxonomy == "partner_cities"){
			$partner_city = $tax->name;
			$partner_city_slug = $tax->slug;
		} else if($tax->taxonomy == "partner_types"){
			$partner_type = $tax->name;
		} else if($tax->taxonomy == "partner_countries"){
			$partner_country = $tax->name;
			$partner_country_slug = $tax->slug;
		}
	}

	$post_id = get_the_ID();
	$post_meta = get_post_meta($post_id);

	$partner_latitude = $post_meta["partner_latitude"][0];
	$partner_longitude = $post_meta["partner_longitude"][0];
	$partner_title = get_the_title($post_id);
	$partner_website = $post_meta["partner_website"][0];
	$partner_email = $post_meta["partner_email"][0];
        
    if ($partner_city_slug == 'unknown'){
        $partner_city_slug = $partner_country_slug;
        $partner_city = $partner_country;
    }

	$partner = array(
		"partner_city" => $partner_city,
		"partner_type" => $partner_type,
		"partner_country" => $partner_country,
		"partner_latitude" => $partner_latitude,
		"partner_longitude" => $partner_longitude,
		"partner_title" => $partner_title,
		"partner_website" => $partner_website,
		"partner_email" => $partner_email,
		"partner_city_slug" => $partner_city_slug
	);

	
	if (array_key_exists($partner_city, $partner_array)){
		array_push($partner_array[$partner_city], $partner);
	} else{
		$partner_array[$partner_city] = array($partner);
	}

	
	
endwhile;
wp_reset_postdata();

echo json_encode($partner_array);
?>

