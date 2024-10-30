<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://inspira.do
 * @since             1.0.0
 * @package           Buenvio
 *
 * @wordpress-plugin
 * Plugin Name:       Buenvio
 * Plugin URI:        https://buenvio.com/
 * Description:       Utiliza este plugin gratuito para integrar los envios con Buenvio a tu tienda woocommerce.
 * Version:           1.0.2
 * Author:            Inspira Punto Do, E.I.R.L
 * Author URI:        https://inspira.do/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       buenvio
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
define( 'BUENVIO_VERSION', '1.0.2' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-buenvio-activator.php
 */
function activate_buenvio() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-buenvio-activator.php';
	Buenvio_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-buenvio-deactivator.php
 */
function deactivate_buenvio() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-buenvio-deactivator.php';
	Buenvio_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_buenvio' );
register_deactivation_hook( __FILE__, 'deactivate_buenvio' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-buenvio-api.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-buenvio.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_buenvio() {

	$plugin = new Buenvio();
	$plugin->run();

}
run_buenvio();


add_action( 'plugins_loaded', 'init_buenvio_gateway' );
function init_buenvio_gateway() {
	require plugin_dir_path( __FILE__ ) . 'gateways/buenvio.php';
}