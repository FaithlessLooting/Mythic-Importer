<?php
add_action( 'admin_action_mtg_import', 'mtg_import_admin_action' );
function mtg_import_admin_action()
{
    $set = $_POST["sets"];

    $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.scryfall.com/cards/search?q=set%3A'.$set.'&page=1',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'X-Api-Key: f8fa774e-d26b-410a-a731-5c0e38781f3d'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
$data = json_decode($response);
$category = get_term_by( 'name', 'Magic the Gathering', 'product_cat' );
$cat_id = $category->term_id;
$has_more = true;
$page = 1;


while($has_more == true) {
    [$cards, $has_more, $page] = mtgGetData($page, $set);
    foreach($cards as $card){
  if($card->games != ["arena"]){
  foreach($card->finishes as $finish){
    $finish = ucfirst($finish); 
    $fount_post = false;
    $fount_post = post_exists( $card->name.' - '.$finish,'','','');
    if($fount_post == false){
    $year = explode( '-', $card->released_at);
    $year = $year[0];
  $item = array(
    'Name' => $card->name.' - '.$finish,
    'SKU' => $card->id
  );
  $user_id = get_current_user(); // this has NO SENSE AT ALL, because wp_insert_post uses current user as default value
  // $user_id = $some_user_id_we_need_to_use; // So, user is selected..
  $post_id = wp_insert_post( array(
    'post_author' => $user_id,
    'post_title' => $item['Name'],
    'post_content' => '',
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
      update_field('rarity_variant', $card->rarity, $post_id);
      update_field('set_name', $card->set_name, $post_id);
      update_field('number', $card->collector_number, $post_id);
      update_field('year', $year, $post_id);

    } 
  }

}
}
}
    wp_redirect( $_SERVER['HTTP_REFERER'] );
    exit();
}

function mtgGetData($page, $set) {
    $curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.scryfall.com/cards/search?q=set%3A'.$set.'&page='.$page,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'X-Api-Key: f8fa774e-d26b-410a-a731-5c0e38781f3d'
  ),
));

$response = curl_exec($curl);

curl_close($curl);

$data = json_decode($response);
$cards = $data->data;
$has_more = $data->has_more;
if (property_exists( $data, "next_page")){
  $page = explode( 'page=', $data->next_page);
  $page = $page[1][0]; 
}
else {
  $page = "FINISHED";
}
return array($cards, $has_more, $page);
}