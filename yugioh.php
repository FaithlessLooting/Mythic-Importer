<?php
add_action( 'admin_action_yugioh_import', 'yugioh_import_admin_action' );
function yugioh_import_admin_action()
{
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => 'https://db.ygoprodeck.com/api/v7/cardinfo.php',
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'GET',
	));
	
	$response = curl_exec($curl);
	
	curl_close($curl);
$yugioh = json_decode($response);
$yugioh = $yugioh->data;
$category = get_term_by( 'name', 'Yu-Gi-Oh!', 'product_cat' );
$cat_id = $category->term_id;
 foreach ($yugioh as $card) {

	$slug = sanitize_title($card->name);
	foreach($card->card_sets as $single){
	$found_post = false;
	$sku = $slug."-".$single->set_code;
	$found_post = get_product_by_sku($sku);
	if(!$found_post){
	$item = array(
		'Name' => $card->name." - ".$single->set_code,
		'Description' => $card->desc,
		'SKU' => $sku,
	);
	$user_id = get_current_user(); // this has NO SENSE AT ALL, because wp_insert_post uses current user as default value
	// $user_id = $some_user_id_we_need_to_use; // So, user is selected..
	$post_id = wp_insert_post( array(
		'post_author' => $user_id,
		'post_title' => $item['Name'],
		'post_content' => $item['Description'],
		'post_status' => 'publish',
		'post_type' => "product",
	) );
	wp_set_object_terms( $post_id, 'simple', 'product_type' );
	wp_set_object_terms( $post_id, array($cat_id), 'product_cat' );
	update_post_meta( $post_id, '_visibility', 'visible' );
	update_post_meta( $post_id, '_stock_status', 'instock');
	update_post_meta( $post_id, 'total_sales', '0' );
	update_post_meta( $post_id, '_downloadable', 'no' );
	update_post_meta( $post_id, '_virtual', 'no' );
	update_post_meta( $post_id, '_regular_price', 15.00 );
	update_post_meta( $post_id, '_sale_price', '' );
	update_post_meta( $post_id, '_purchase_note', '' );
	update_post_meta( $post_id, '_featured', 'no' );
	update_post_meta( $post_id, '_weight', '' );
	update_post_meta( $post_id, '_length', '' );
	update_post_meta( $post_id, '_width', '' );
	update_post_meta( $post_id, '_height', '' );
	update_post_meta( $post_id, '_sku', $item['SKU'] );
	update_post_meta( $post_id, '_product_attributes', array() );
	update_post_meta( $post_id, '_sale_price_dates_from', '' );
	update_post_meta( $post_id, '_sale_price_dates_to', '' );
	update_post_meta( $post_id, '_price', 15.00 );
	update_post_meta( $post_id, '_sold_individually', '' );
	update_post_meta( $post_id, '_manage_stock', 'no' );
	update_post_meta( $post_id, '_backorders', 'no' );
	update_post_meta( $post_id, '_stock', '' );
	wp_set_post_categories( $post_id, $cat_id, true);

	update_field('rarity_variant', $single->set_rarity, $post_id);
	update_field('set_name', $single->set_name, $post_id);
	update_field('number', $single->set_code, $post_id);
	//update_field('year', $year, $post_id);
 } 
}
 }


    wp_redirect( $_SERVER['HTTP_REFERER'] );
    exit();
}


function get_product_by_sku( $sku ) {

    global $wpdb;

    $product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku ) );

    if ( $product_id ) return new WC_Product( $product_id );

    return null;
}