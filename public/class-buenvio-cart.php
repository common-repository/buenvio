<?php

use GuzzleHttp\Client;

/**
 * The Cart-facing functionality of the plugin.
 *
 * @link       https://inspira.do
 * @since      1.0.0
 *
 * @package    Buenvio
 * @subpackage Buenvio/Cart
 */

/**
 * The Cart-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the Cart-facing stylesheet and JavaScript.
 *
 * @package    Buenvio
 * @subpackage Buenvio/Cart
 * @author     Inspira Punto Do <hola@inspira.do>
 */
class Buenvio_Cart {

	var $client;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct() {
	}

	/**
	 * Register the stylesheets for the Cart-facing side of the site.
	 *
	 * @since    1.0.0
	 */

	function add_shipping_fields( $fields ) {
        $fields['shipping_phone'] = array(
            'label'         => 'Teléfono',
            'required'      => true,
            'class'         => array( 'form-row-wide' ),
            'validate'      => array( 'number' ),
		);
		
        $fields['shipping_email'] = array(
            'label'         => 'Correo',
            'required'      => true,
            'class'         => array( 'form-row-wide' ),
            'validate'      => array( 'email' ),
        );
        return $fields;
    }

    function admin_shipping_fields( $fields ) {
        $fields['email'] = array(
            'label'         => 'Correo'
		);
		
        $fields['phone'] = array(
            'label'         => 'Teléfono'
        );
        return $fields;
    }

	/**
	 * Limit the cities to the ones allowed on our service
	 *
	 * @since    1.0.0
	 */
	function override_checkout_city_fields( $fields ) {
		// Define here in the array your desired cities (Here an example of cities)
		$option_cities = [
			'' => __( 'Seleccionar ciudad' ),
			'Santiago de los Caballeros' => 'Santiago de los Caballeros',
			'San Pedro De Macorís' => 'San Pedro De Macorís',
			'La Romana' => 'La Romana',
			'La Altagracia' => 'La Altagracia',
			'San Cristóbal' => 'San Cristóbal',
			'San Francisco de Macorís' => 'San Francisco de Macorís',
			'Boca Chica - Este' => 'Boca Chica - Este',
			'San Felipe - Puerto Plata' => 'San Felipe - Puerto Plata',
			'Boca Chica - Oeste' => 'Boca Chica - Oeste',
			'Santa Cruz de Barahona' => 'Santa Cruz de Barahona',
			'Baní' => 'Baní',
			'San Juan de la Maguana' => 'San Juan de la Maguana',
			'Bonao' => 'Bonao',
			'Moca' => 'Moca',
			'Azua de Compostela' => 'Azua de Compostela',
			'Cotuí' => 'Cotuí',
			'Santa Cruz de El Seibo' => 'Santa Cruz de El Seibo',
			'Jarabacoa' => 'Jarabacoa',
			'Nagua' => 'Nagua',
			'Santa Bárbara de Samaná' => 'Santa Bárbara de Samaná',
			'Tamboril' => 'Tamboril',
			'Mao' => 'Mao',
			'Esperanza' => 'Esperanza',
			'Pedro Brand' => 'Pedro Brand',
			'Sosúa' => 'Sosúa',
			'Hato Mayor del Rey' => 'Hato Mayor del Rey',
			'Constanza' => 'Constanza',
			'Villa Bisonó' => 'Villa Bisonó',
			'Salcedo' => 'Salcedo',
			'Villa Altagracia' => 'Villa Altagracia',
			'Las Matas de Farfán' => 'Las Matas de Farfán',
			'Monte Plata' => 'Monte Plata',
			'Yamasá' => 'Yamasá',
			'San Ignacio de Sabaneta' => 'San Ignacio de Sabaneta',
			'San José de Las Matas' => 'San José de Las Matas',
			'San Antonio de Guerra' => 'San Antonio de Guerra',
			'San José de Ocoa' => 'San José de Ocoa',
			'La Vega' => 'La Vega',
			'Santo Domingo Este' => 'Santo Domingo Este',
			'Santo Domingo Norte' => 'Santo Domingo Norte',
			'Santo Domingo Oeste' => 'Santo Domingo Oeste',
			'Santo Domingo Distrito Nacional' => 'Santo Domingo Distrito Nacional'
		];
	
		$fields['billing']['billing_city']['type'] = 'select';
		$fields['billing']['billing_city']['options'] = $option_cities;
		$fields['shipping']['shipping_city']['type'] = 'select';
		$fields['shipping']['shipping_city']['options'] = $option_cities;
	
		return $fields;
	}
}
