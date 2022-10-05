<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://mythic-uk.co.uk
 * @since             1.0.0
 * @package           Mythicuk_Product_Importer
 *
 * @wordpress-plugin
 * Plugin Name:       Mythic-UK Product Importer
 * Plugin URI:        https://mythic-uk.co.uk
 * Description:       API connectors to create woocommerce products based on TCG APIs
 * Version:           1.0.0
 * Author:            Matthew Dove
 * Author URI:        https://mythic-uk.co.uk
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mythicuk-product-importer
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MYTHICUK_PRODUCT_IMPORTER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mythicuk-product-importer-activator.php
 */
function activate_mythicuk_product_importer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mythicuk-product-importer-activator.php';
	Mythicuk_Product_Importer_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mythicuk-product-importer-deactivator.php
 */
function deactivate_mythicuk_product_importer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mythicuk-product-importer-deactivator.php';
	Mythicuk_Product_Importer_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_mythicuk_product_importer' );
register_deactivation_hook( __FILE__, 'deactivate_mythicuk_product_importer' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-mythicuk-product-importer.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_mythicuk_product_importer() {

	$plugin = new Mythicuk_Product_Importer();
	$plugin->run();

}
run_mythicuk_product_importer();




add_action('admin_menu', 'importer_menu');
 
function importer_menu(){
    add_menu_page( 'Importer Page', 'Importer Plugin', 'manage_options', 'importer-plugin', 'content_init' );
}
 
function content_init(){
    echo "<h1>Product Importer</h1>";
	?>
	<div>
		<h2>Digimon Importer (all items)</h2>
		<p style="color:red">Warning: this will take a while...</p>
	    <form method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">
			<input type="hidden" name="action" value="digimon_import" />
			<input type="submit" value="Do it!" />
    	</form>
	</div>

	<div>
		<h2>Flesh and Blood Importer (all items)</h2>
		<p style="color:red">Warning: this will take a while...</p>
	    <form method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">
			<input type="hidden" name="action" value="fleshblood_import" />
			<input type="submit" value="Do it!" />
    	</form>
	</div>

	<div>
		<h2>Pokemon Importer (by set)</h2>
		<p style="color:red">Warning: this will take a while...</p>
	    <form method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">
		<select name="sets" id="sets">
		<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.pokemontcg.io/v2/sets?orderBy=releaseDate',
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
$sets = json_decode($response);
foreach($sets->data as $set){
	echo '<option value='.$set->name.'>'.$set->name.'</option>';
}
		?>
		</select>

			<input type="hidden" name="action" value="pokemon_import" />
			<input type="submit" value="Do it!" />
    	</form>
	</div>
	<?php
}



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
	wp_set_object_terms( $post_id, array(23), 'product_cat' );
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
	wp_set_post_categories( $post_id, 23, true);
 } 
}


    wp_redirect( $_SERVER['HTTP_REFERER'] );
    exit();
}

function fnbgetdata($page){
	
	$time = time();
	$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.fabdb.net/cards?Time='.$time.'&page='.$page,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
	'Authorization: Bearer 8dc5360e898dae381afe6a71dc210b2efccefff2f8a93f48211a1872be34d945'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
$cardlist = json_decode($response);
$numpages = $cardlist->meta->last_page;
$currentpage = $cardlist->meta->current_page;
$cardlist = $cardlist->data;

return array($cardlist, $numpages, $currentpage);
}

add_action( 'admin_action_fleshblood_import', 'fleshblood_import_admin_action' );
function fleshblood_import_admin_action()
{
$currentpage = 0;
$numpages = 1;
while($currentpage < $numpages){
	[$cardlist, $numpages, $currentpage] = fnbgetdata($currentpage+1);
	foreach ($cardlist as $card_outer) {
		foreach($card_outer->printings as $card){
		$fount_post = false;
		$fount_post = post_exists( $card->name." - ".$card->sku->sku,'','','');
		if(!$fount_post){
		$item = array(
			'Name' => $card->name." - ".$card->sku->sku,
			'Description' => $card->text,
			'SKU' => $card->sku->sku,
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
		wp_set_object_terms( $post_id, array(26), 'product_cat' );
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
		wp_set_post_categories( $post_id, 26, true);
	} 
}
	}
}

    wp_redirect( $_SERVER['HTTP_REFERER'] );
    exit();
}

require(dirname(__FILE__).'/pokemonTCG.php');
