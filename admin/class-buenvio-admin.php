<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Buenvio
 * @subpackage Buenvio/admin
 * @author     Inspira.do <hola@inspira.do>
 */
class Buenvio_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	protected $options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;


        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Buenvio', 
            'Buenvio', 
            'manage_options', 
            'buenvio', 
            array( $this, 'create_admin_page' )
        );
	}
	
	/**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
		$this->options = get_option( 'buenvio' );
        ?>
        <div class="wrap">
            <h1>Configuración Buenvio</h1>
            <form method="post" action="options.php">
            <?php
                settings_fields( 'buenvio_group' );
                do_settings_sections( 'buenvio' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init() {
		register_setting(
            'buenvio_group', // Option group
            'buenvio', // Option name
            array( $this, 'validate_and_sanitize' ) // Sanitize
		);

		add_settings_section(
            'buenvio_settings_section', // ID
            'General', // Title
            array( $this, 'print_section_info' ), // Callback
            'buenvio' // Page
		);

        add_settings_field(
            'token_buenvio', // ID
            'Token Buenvio', // Title 
            array( $this, 'token_buenvio_callback' ), // Callback
            'buenvio', // Page
            'buenvio_settings_section' // Section           
        );

		add_settings_section(
            'buenvio_origin_settings', // ID
            'Detalles origden de envio', // Title
            array( $this, 'print_section_info' ), // Callback
            'buenvio' // Page
		);
        add_settings_field(
            'street', // ID
            'Calle', // Title 
            array( $this, 'street_callback' ), // Callback
            'buenvio', // Page
            'buenvio_origin_settings' // Section           
        );

        add_settings_field(
            'building', // ID
            'Número del edificio o referencia', // Title 
            array( $this, 'building_callback' ), // Callback
            'buenvio', // Page
            'buenvio_origin_settings' // Section           
        );

        add_settings_field(
            'city', // ID
            'Ciudad', // Title 
            array( $this, 'city_callback' ), // Callback
            'buenvio', // Page
            'buenvio_origin_settings' // Section           
        );

        add_settings_field(
            'sector', // ID
            'Sector', // Title 
            array( $this, 'sector_callback' ), // Callback
            'buenvio', // Page
            'buenvio_origin_settings' // Section           
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function validate_and_sanitize( $input )
    {
        $new_input = array();

        if(empty($input['token'])) {
            add_settings_error('token_error',esc_attr('token_error'),__('Debe de especificar un token.'),'error');
            add_action('admin_notices', 'print_errors');
            
            return $input;
        }

        $buenvio_response = Buenvio_API::getRequest(
            'https://api.buenvio.com/management/v1/users/verify/',
            [
                'api_token' => $input['token'],
            ]
        );

        if(!$buenvio_response->ok) {
            add_settings_error('token_error',esc_attr('token_error'),__($buenvio_response->message),'error');
            add_action('admin_notices', 'print_errors');

            return $input;
        }

        if( isset( $input['token'] ) )
            $new_input['token'] = sanitize_text_field( $input['token'] );

        if( isset( $input['city'] ) )
            $new_input['city'] = sanitize_text_field( $input['city'] );

        if( isset( $input['building'] ) )
            $new_input['building'] = sanitize_text_field( $input['building'] );

        if( isset( $input['street'] ) )
            $new_input['street'] = sanitize_text_field( $input['street'] );

        if( isset( $input['sector'] ) )
            $new_input['sector'] = sanitize_text_field( $input['sector'] );

        return $new_input;
    }

	/** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Digita los siguientes campos:';
    }


	/** 
     * Get the settings option array and print one of its values
     */
    public function token_buenvio_callback()
    {
        printf(
            '<input required type="text" id="token" name="buenvio[token]" value="%s" />',
            isset( $this->options['token'] ) ? esc_attr( $this->options['token']) : ''
        );
    }
	
	/** 
     * Get the settings option array and print one of its values
     */
    public function street_callback()
    {
        printf(
            '<input required type="text" id="token" name="buenvio[street]" value="%s" />',
            isset( $this->options['street'] ) ? esc_attr( $this->options['street']) : ''
        );
    }
	
	/** 
     * Get the settings option array and print one of its values
     */
    public function building_callback()
    {
        printf(
            '<input required type="text" id="building" name="buenvio[building]" value="%s" />',
            isset( $this->options['building'] ) ? esc_attr( $this->options['building']) : ''
        );
    }
	
	/** 
     * Get the settings option array and print one of its values
     */
    public function sector_callback()
    {
        printf(
            '<input required type="text" id="sector" name="buenvio[sector]" value="%s" />',
            isset( $this->options['sector'] ) ? esc_attr( $this->options['sector']) : ''
        );
    }
	
	/** 
     * Get the settings option array and print one of its values
     */
    public function city_callback()
    {
		// Define here in the array your desired cities (Here an example of cities)
		$option_cities = [
			'' => __( 'Seleccionar ciudad' ),
			2 => 'Santiago de los Caballeros',
			3 => 'San Pedro De Macorís',
			4 => 'La Romana',
			5=> 'La Altagracia',
			6 => 'San Cristóbal',
			7 => 'San Francisco de Macorís',
			8 => 'Boca Chica - Este',
			9 => 'San Felipe - Puerto Plata',
			10  => 'Boca Chica - Oeste',
			11 => 'Santa Cruz de Barahona',
			12 => 'Baní',
			13 => 'San Juan de la Maguana',
			14 => 'Bonao',
			15 => 'Moca',
			16 => 'Azua de Compostela',
			17 => 'Cotuí',
			18 => 'Santa Cruz de El Seibo',
			19 => 'Jarabacoa',
			20 => 'Nagua',
			21 => 'Santa Bárbara de Samaná',
			22 => 'Tamboril',
			23 => 'Mao',
			24 => 'Esperanza',
			25 => 'Pedro Brand',
			26 => 'Sosúa',
			27 => 'Hato Mayor del Rey',
			28 => 'Constanza',
			29 => 'Villa Bisonó',
			30 => 'Salcedo',
			31 => 'Villa Altagracia',
			32 => 'Las Matas de Farfán',
			33 => 'Monte Plata',
			34 => 'Yamasá',
			35 => 'San Ignacio de Sabaneta',
			36 => 'San José de Las Matas',
			37 => 'San Antonio de Guerra',
			38 => 'San José de Ocoa',
			39 => 'La Vega',
			40 => 'Santo Domingo Este',
			41 => 'Santo Domingo Norte',
			42 => 'Santo Domingo Oeste',
			43 => 'Santo Domingo Distrito Nacional'
        ];

        $current_city = isset( $this->options['city'] ) ? esc_attr( $this->options['city']) : '';
    ?>
        <select name="buenvio[city]" id="city" required>
            <?php
            foreach($option_cities AS $city_id => $city_name) {
            ?>
            <option <?php echo ($current_city == $city_id) ? 'selected' : ''; ?>  value="<?php echo $city_id; ?>"><?php echo $city_name; ?></option>
            <?php
            }
    ?>
    </select>
    <?php
    }
}
