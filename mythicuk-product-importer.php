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
	$setname = $set->name;
	echo '<option value="'.$setname.'">'.$set->name.'</option>';
}
		?>
		</select>

			<input type="hidden" name="action" value="pokemon_import" />
			<input type="submit" value="Do it!" />
    	</form>
	</div>

	<div>
		<h2>MTG Importer (by set)</h2>
		<p style="color:red">Warning: this will take a while...</p>
	    <form method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">
		<select name="sets" id="sets">
		<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.scryfall.com/sets',
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
	$setname = $set->name;
	$setcode = $set->code;
	echo '<option value="'.$setcode.'">'.$set->name.'</option>';
}
		?>
		</select>

			<input type="hidden" name="action" value="mtg_import" />
			<input type="submit" value="Do it!" />
    	</form>
	</div>

	<div>
		<h2>Yu-Gi-Oh Importer (all items)</h2>
		<p style="color:red">Warning: this will take a VERY long time...</p>
	    <form method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">
			<input type="hidden" name="action" value="yugioh_import" />
			<input type="submit" value="Do it!" />
    	</form>
	</div>


	<?php
}


require(dirname(__FILE__).'/pokemonTCG.php');
require(dirname(__FILE__).'/fnbTCG.php');
require(dirname(__FILE__).'/digimonTCG.php');
require(dirname(__FILE__).'/MTG.php');
require(dirname(__FILE__).'/yugioh.php');
