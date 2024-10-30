<?php

class WC_Buenvio_Shipping_Method extends WC_Shipping_Method {
    /**
     * Constructor for your shipping class
     *
     * @access public
     * @return void
     */
	function __construct( ) {
        $this->id                 = 'Buenvio'; 
        $this->method_title       = __( 'Buenvio', 'buenvio' );  
        $this->method_description = __( 'Envia tus productos de forma segura mediante buenvio!', 'buenvio' ); 
        $this->countries = [
            'DO',
        ];
        $this->availability = 'including';

        $this->init();

        $this->enabled = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'yes';
        $this->title = isset( $this->settings['title'] ) ? $this->settings['title'] : __( 'Buenvio', 'buenvio' );
    }

    /**
     * Init your settings
     *
     * @access public
     * @return void
     */
    function init() {
        // Load the settings API
        $this->init_form_fields(); 
        $this->init_settings(); 

        // Save settings in admin if you have any defined
        add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
    }

    /**
     * Define settings field for this shipping
     * @return void 
     */
    function init_form_fields() {
        
        $this->instance_form_fields = [
            'enabled' => [
                'title' => __( 'Habilitar', 'buenvio' ),
                'type' => 'checkbox',
                'description' => __( 'Habilitar envio', 'buenvio' ),
                'default' => 'yes'
            ],

            'title' => [
                'title' => __( 'Nombre', 'buenvio' ),
                'type' => 'text',
                'description' => __( 'Nombre de la forma de envío en la página', 'buenvio' ),
                'default' => __( 'Buenvio', 'buenvio' )
            ],

            'token' => [
                'title' => __( 'Token', 'buenvio' ),
                'type' => 'text',
                'description' => __( 'Especifique su token de buenvio aquí', 'buenvio' ),
                'default' => __( '', 'buenvio' )
            ],
        ];
    }

    /**
     * This function is used to calculate the shipping cost. Within this function we can check for weights, dimensions and other parameters.
     *
     * @access public
     * @param mixed $package
     * @return void
     */
    public function calculate_shipping( $package = array() ) {
        $products_cost = 0;
        $items = [];

        $buenvio_data = get_option('buenvio');

		if ( count( $package['contents'] ) > 0 ) {
			foreach ( $package['contents'] as $item_id => $values ) {

                for($i = 0; $i < $values['quantity']; $i++) {
                    $items[] = [
                        'name' => $values['data']->get_name(),
                        'weight' => $values['data']->get_weight() * 0.002205, //TRANSFORM TO POUNDS
                        'fragile' => false,
                    ];
                }

                $products_cost += $values['line_subtotal'] + $values['line_tax'];
			}
        }

        $buenvio_response = Buenvio_API::postRequest(
            '/orders/quote/',
            [
                'first_name' => 'FN',
                'last_name' => 'LN',
                'contact_information' => '8098098099',

                'address' => $package['destination']['address_1'],
                'address2' => $package['destination']['address_2'],
                'postal_code' => $package['destination']['postcode'],
                'city' => $package['destination']['city'],

                'packages' => json_encode($items),

                'departure_address_street' => $buenvio_data['street'],
                'departure_address_building_number' => $buenvio_data['building'],
                'departure_address_city' => $buenvio_data['city'],
                'departure_address_sector' => $buenvio_data['sector'],
            ]
        );

        foreach($buenvio_response->rates AS $buenvio_rate) {
            $rate = array(
                'id' => $buenvio_rate->service_code,
                'label' => $buenvio_rate->service_name,
                'cost' => round($buenvio_rate->total_price, 2)
            );
        
            $this->add_rate( $rate );
        }
    }

    function is_taxable() {
        return false;
    }
}

function add_buenvio_shipping_method( $methods ) {
    $methods[] = 'WC_Buenvio_Shipping_Method';
    return $methods;
}
add_filter( 'woocommerce_shipping_methods', 'add_buenvio_shipping_method' );