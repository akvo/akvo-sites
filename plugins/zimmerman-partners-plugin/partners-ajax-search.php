<?php

$tax_query = array();

$args = array( 'post_type' => 'partner', 'posts_per_page' => 999, 'orderby' => 'menu_order title', 'order' => 'DESC' );

if (isset($_REQUEST['search'])){
	$args["s"] = $_REQUEST['search'];
}

$loop = new WP_Query( $args );
$partner_array = array();

while ( $loop->have_posts() ) : $loop->the_post();
	
	array_push($partner_array, array("value" => the_title("", "", false), "label" => the_title("", "", false)));
	
endwhile;
wp_reset_postdata();

echo json_encode(array("suggestions" => $partner_array));
?>

