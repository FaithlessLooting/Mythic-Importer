<?php
add_action( 'admin_action_digimon_import', 'digimon_import_admin_action' );
function digimon_import_admin_action()
{
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://digimoncard.io/api-public/search.php?series=Digimon%20Card%20Game&sort=name&sortdirection=desc',
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
$digimon = json_decode($response);
//var_dump($digimon);
$category = get_term_by( 'name', 'Digimon', 'product_cat' );
$cat_id = $category->term_id;
 foreach ($digimon as $digiman) {
	$fount_post = false;
	$fount_post = post_exists( $digiman->name." - ".$digiman->cardnumber,'','','');
	if(!$fount_post){
	$item = array(
		'Name' => $digiman->name." - ".$digiman->cardnumber,
		'Description' => $digiman->soureeffect,
		'SKU' => $digiman->cardnumber,
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
 } 
}


    wp_redirect( $_SERVER['HTTP_REFERER'] );
    exit();
}
