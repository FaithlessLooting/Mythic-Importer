<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://mythic-uk.co.uk
 * @since      1.0.0
 *
 * @package    Mythicuk_Product_Importer
 * @subpackage Mythicuk_Product_Importer/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Mythicuk_Product_Importer
 * @subpackage Mythicuk_Product_Importer/includes
 * @author     Matthew Dove <matthew.dove13@gmail.com>
 */
class Mythicuk_Product_Importer_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'mythicuk-product-importer',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
