<?php

/**
 * The Order-facing functionality of the plugin.
 *
 * @link       https://inspira.do
 * @since      1.0.0
 *
 * @package    Buenvio
 * @subpackage Buenvio/Order
 */

/**
 * The Order-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the Order-facing stylesheet and JavaScript.
 *
 * @package    Buenvio
 * @subpackage Buenvio/Order
 * @author     Inspira Punto Do <hola@inspira.do>
 */
class Buenvio_Order {

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
    
	function register_shipped_order_status() {
		register_post_status( 'wc-shipped', array(
			'label'                     => 'Enviado',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Enviado <span class="count">(%s)</span>', 'Enviado <span class="count">(%s)</span>' )
		) );
	}

	function custom_order_status($order_statuses) {
		$order_statuses['wc-shipped'] = _x( 'Enviado', 'Estado de la ordern', 'woocommerce' ); 
		return $order_statuses;
	}

    function create_shipping_request( $order_id ) {
		if(get_post_meta($order_id, 'tracking_code', true)) {
			return;
		}

		$order = wc_get_order( $order_id );
		if( !$order->has_shipping_method('Buenvio') ) {
			return;
		}

        //SEND ORDER TO BUENVIO
        $products_cost = 0;
        $items = [];

		foreach ( $order->get_items() as $item_id => $item ) {
			$quantity = $item->get_quantity();
			$product = $item->get_product();
			$base_price = round((float)$item->get_subtotal() / $item->get_quantity(), 2);

			for($i = 0; $i < $quantity; $i++ ) {
				$items[] = [
                    'name' => $product->get_name(),
                    'weight' => ((float)$product->get_weight())* 0.002205, //TRANSFORM TO POUNDS
                    'fragile' => false,
                ];

                $products_cost += (float)$base_price;
			}
		}

		$shipping_address = $order->get_address('shipping'); 
		$phone = get_post_meta($order_id, '_shipping_phone', true);

		$buenvio_data = get_option('buenvio');
        $buenvio_response = Buenvio_API::postRequest(
            '/orders/',
            [
                'first_name' => $shipping_address['first_name'],
                'last_name' => $shipping_address['last_name'],
                'contact_information' => $phone,

                'address' => $shipping_address['address_1'],
                'address2' => $shipping_address['address_2'],
                'postal_code' => $shipping_address['postcode'],
                'city' => $shipping_address['city'],

                'packages' => json_encode($items),

                'departure_address_street' => $buenvio_data['street'],
                'departure_address_building_number' => $buenvio_data['building'],
                'departure_address_city' => $buenvio_data['city'],
                'departure_address_sector' => $buenvio_data['sector'],
            ],
		);


		if(!$buenvio_response->ok) {
			$order->update_status('pending', $buenvio_response->message);

			add_action('admin_notices', [$this, 'error_message'] );

			return;
		}

		$order->add_order_note( 'Se agregó el tracking code #:' . $buenvio_response->order_identifier . ' a la orden!' );
		update_post_meta($order_id, 'tracking_code', $buenvio_response->order_identifier );
		update_post_meta($order_id, 'tracking_url', 'https://buenvio.com/track-envio/?identificador='.$buenvio_response->order_identifier );
	}

    function cancel_shipping_request( $order_id ) {
		$tracking_code = get_post_meta($order_id, 'tracking_code', true);
		if(!$tracking_code) {
			return;
		}

		$order = wc_get_order( $order_id );
		if( !$order->has_shipping_method('Buenvio') ) {
			return;
		}

		$buenvio_data = get_option('buenvio');
        $buenvio_response = Buenvio_API::deleteRequest(
            '/orders/?order_identifier='.$tracking_code,
            [
            ],
		);

		if(!$buenvio_response->ok) {
			$order->add_order_note( '¡No se pudo cancelar el envío código: ' . $tracking_code . ' de esta orden en buenvio!' );
			return;
		}

		$order->add_order_note( '¡Se canceló el envío código: ' . $tracking_code . ' de esta orden en buenvio!' );
	}

	function error_message() {
		?>  
		<div class="notice notice-error is-dismissible">
			<p><?php _e('¡Ocurrió un error al enviar el pedido a Buenvio!'); ?></p>
		</div>
		<?php 
	}

    function action_order_details_after_order_table( $order ) {
		$tracking_url = get_post_meta($order->get_id(), 'tracking_url', true);
		$tracking_code = get_post_meta($order->get_id(), 'tracking_code', true);

		if(!$tracking_url) return;

		printf(
			"<p>Código de rastreo de Buenvio: <a href=\"%s\">%s</a></p>",
			$tracking_url,
			$tracking_code
		);
    }
}
