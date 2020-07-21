<?php
/**
 * Options for AppPresser OneSignal
 *
 * @package  AppPresser OneSignal
 */

namespace AppPresser\OneSignal;

class Options {
	/**
	 * The option name to save settings.
	 *
	 * @var string
	 */
	const OPTION_NAME = 'appp_onesignal';

	/**
	 * Holds the values to be used in the fields callbacks.
	 *
	 * @var array
	 */
	private $options = [];

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_options_page' ] );
		add_action( 'admin_init', [ $this, 'page_init' ] );
	}

	/**
	 * Add options page
	 */
	public function add_options_page() {
		// This page will be under "Settings"
		add_options_page(
			esc_html__( 'AppPresser OneSignal', 'apppresser-onesignal' ),
			esc_html__( 'AppPresser OneSignal', 'apppresser-onesignal' ),
			'manage_options',
			self::OPTION_NAME,
			[
				$this,
				'create_admin_page',
			]
		);
	}

	/**
	 * Options page callback.
	 *
	 * @return void
	 */
	public function create_admin_page() {
		// Set class property
		$this->options = get_option( self::OPTION_NAME );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'AppPresser OneSignal', 'apppresser-onesignal' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( self::OPTION_NAME );
				do_settings_sections( self::OPTION_NAME );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register and add settings.
	 *
	 * @return void
	 */
	public function page_init() {
		register_setting(
			self::OPTION_NAME,
			self::OPTION_NAME,
			[
				$this,
				'sanitize',
			]
		);

		add_settings_section(
			self::OPTION_NAME,
			esc_html__( 'Settings', 'apppresser-onesignal' ),
			[
				$this,
				'print_section_info',
			],
			self::OPTION_NAME
		);  

		add_settings_field(
			'onesignal_api_key',
			esc_html__( 'OneSignal API Key', 'apppresser-onesignal' ), 
			[
				$this,
				'onesignal_api_key_callback',
			], // Callback
			self::OPTION_NAME,
			self::OPTION_NAME
		);      

		add_settings_field(
			'message',
			esc_html__( 'Message', 'apppresser-onesignal' ),
			[
				$this,
				'message_callback',
			],
			self::OPTION_NAME,
			self::OPTION_NAME
		);      
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input ) {
		$new_input = array();

		if ( isset( $input['onesignal_api_key'] ) ) {
			$new_input['onesignal_api_key'] = sanitize_text_field( $input['onesignal_api_key'] );
		}

		if ( isset( $input['message'] ) ) {
			$new_input['message'] = sanitize_text_field( $input['message'] );
		}

		return $new_input;
	}

	/**
	 * Print the Section text.
	 *
	 * @return void
	 */
	public function print_section_info() {
		esc_html_e( 'Enter your API key and Message below.', 'apppresser-onesignal' );
	}

	/** 
	 * Callback for the API Key.
	 *
	 * @return void
	 */
	public function onesignal_api_key_callback() {
		printf(
			'<input type="text" id="onesignal_api_key" name="appp_onesignal[onesignal_api_key]" value="%s" />',
			isset( $this->options['onesignal_api_key'] ) ? esc_attr( $this->options['onesignal_api_key'] ) : ''
		);
	}

	/** 
	 * Callback for the message.
	 *
	 * @return void
	 */
	public function message_callback() {
		printf(
			'<input type="text" id="message" name="my_option_name[message]" value="%s" />',
			isset( $this->options['message'] ) ? esc_attr( $this->options['message']) : ''
		);
	}
}