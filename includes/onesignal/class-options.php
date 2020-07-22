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

		// Check to see if a message should be sent.
		$this->maybe_send_message();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'AppPresser OneSignal', 'apppresser-onesignal' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( self::OPTION_NAME );
				do_settings_sections( self::OPTION_NAME );
				submit_button( esc_html__( 'Save settings and send message', 'apppresser-onesignal' ) );
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
			'onesignal_app_id',
			esc_html__( 'OneSignal App ID', 'apppresser-onesignal' ), 
			[
				$this,
				'onesignal_app_id_callback',
			], // Callback
			self::OPTION_NAME,
			self::OPTION_NAME
		);

		add_settings_field(
			'onesignal_rest_api_key',
			esc_html__( 'OneSignal REST API KEY', 'apppresser-onesignal' ), 
			[
				$this,
				'onesignal_rest_api_key_callback',
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
	 * @return array       Sanitized settings to save.
	 */
	public function sanitize( $input ) {
		$new_input = [];

		if ( ! empty( $input['onesignal_app_id'] ) ) {
			$new_input['onesignal_app_id'] = sanitize_text_field( $input['onesignal_app_id'] );
		}

		if ( ! empty( $input['onesignal_rest_api_key'] ) ) {
			$new_input['onesignal_rest_api_key'] = sanitize_text_field( $input['onesignal_rest_api_key'] );
		}

		if ( ! empty( $input['message'] ) ) {
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
	public function onesignal_app_id_callback() {
		printf(
			'<input type="text" id="onesignal_app_id" name="appp_onesignal[onesignal_app_id]" value="%1$s" />',
			isset( $this->options['onesignal_app_id'] ) ? esc_attr( $this->options['onesignal_app_id'] ) : ''
		);
	}

	/**
	 * Callback for the API Key.
	 *
	 * @return void
	 */
	public function onesignal_rest_api_key_callback() {
		printf(
			'<input type="text" id="onesignal_rest_api_key" name="appp_onesignal[onesignal_rest_api_key]" value="%1$s" />',
			isset( $this->options['onesignal_rest_api_key'] ) ? esc_attr( $this->options['onesignal_rest_api_key'] ) : ''
		);
	}

	/**
	 * Callback for the message.
	 *
	 * @return void
	 */
	public function message_callback() {
		?>
		<textarea id="message" name="appp_onesignal[message]" rows="7" cols="50" type="textarea"></textarea>
		<?php
	}

	/**
	 * Send a message through the API and display a success message.
	 *
	 * @return void
	 */
	public function maybe_send_message() {
		// Bail early if no App ID and message.
		if ( empty( $this->options['onesignal_app_id'] ) || empty( $this->options['onesignal_rest_api_key'] ) || empty( $this->options['message'] ) ) {
			return;
		}

		// Attempt to send the message through the OneSignal API.
		$api_class = new API( $this->options['onesignal_app_id'], $this->options['onesignal_rest_api_key'] );
		$response  = $api_class->send_message( $this->options['message'] );
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php echo esc_html_e( 'Message successfully sent!', 'apppresser-onesignal' ); ?></p>
		</div>
		<?php
	}
}
