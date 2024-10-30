<?php

/**
 * Fired during plugin activation
 *
 * @link       http://inspira.do
 * @since      1.0.0
 *
 * @package    Buenvio
 * @subpackage Buenvio/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Buenvio
 * @subpackage Buenvio/includes
 * @author     Inspira Punto Do, E.I.R.L <hola@inspira.do>
 */
class Buenvio_Activator {
	public static function activate() {
		$active_plugins = apply_filters('active_plugins', get_option('active_plugins'));
		if (!stripos(implode($active_plugins), 'woocommerce.php')) {
			deactivate_plugins( basename( __FILE__ ) );
			wp_die('<p>Debes tener <strong>Woocommerce</strong> instalado para poder utilizar el Plugin de Buenvio.</p>','Plugin Activation Error',  array( 'response'=>200, 'back_link'=>TRUE ) );		
		}
	}
  }
