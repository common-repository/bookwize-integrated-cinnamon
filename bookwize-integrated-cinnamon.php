<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.bookwize.com/
 * @since      1.6
 * @package           Bookwize Integrated Cinnamon
 *
 * @wordpress-plugin
 * Plugin Name:       Bookwize Cinnamon
 * Plugin URI:        https://www.bookwize.com/
 * Description:       Integarted Bookwize Plugin (Cinnamon)
 * Version:           2.5
 * Author:            Bookwize
 * Author URI:        https://www.bookwize.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       hp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'BW_PAGE_TYPE_INTEGRATED', 'integrated' );
define( 'BW_PUBLIC_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bookwize-integrated-cinnamon-activator.php
 */
function activate_bookwize() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bookwize-integrated-cinnamon-activator.php';
	Bookwize_Integrated_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bookwize-integrated-cinnamon-deactivator.php
 */
function deactivate_bookwize() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bookwize-integrated-cinnamon-deactivator.php';
	Bookwize_Integrated_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_bookwize' );
register_deactivation_hook( __FILE__, 'deactivate_bookwize' );

/**
 * Add links in plugin page
 */
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'bookwize_action_links' );
function bookwize_action_links( $links ) {
	$links[] = '<a href="' . esc_url( get_admin_url( null, 'options-general.php?page=bookwize-integrated-cinnamon' ) ) . '">Settings</a>';

	return $links;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bookwize-integrated-cinnamon.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since      1.4
 */
function run_bookwize() {

	$plugin = new Bookwize_Integrated();

	return $plugin->run();
}

add_action( 'plugins_loaded', function () {
	global $bw;
	$bw = run_bookwize();
} );


global $bw;
function bw() {
	/**
	 * @var $bw Bookwize_Api
	 */
	global $bw;

	return $bw;
}