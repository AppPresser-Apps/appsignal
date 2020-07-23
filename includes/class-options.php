<?php
/**
 * Options for AppPresser OneSignal
 *
 * @package  AppPresser OneSignal
 */

namespace AppPresser\OneSignal;

class Options implements RegistrationInterface {
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
	 * Determines if the object should be registered.
	 *
	 * @return bool True if the object should be registered, false otherwise.
	 */
	public function can_register() {
		return is_admin();
	}

	/**
	 * Registers hooks for this class.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'cmb2_admin_init', [ $this, 'add_options_page' ] );
	}

	/**
	 * Add options page
	 */
	public function add_options_page() {
		/**
		 * Registers options page menu item and form.
		 */
		$cmb_options = \new_cmb2_box(
			[
				'id'           => 'apppresser_onesignal_metabox',
				'title'        => esc_html__( 'AppPresser OneSignal', 'apppresser-onesignal' ),
				'object_types' => [ 'options-page' ],

				/*
				* The following parameters are specific to the options-page box
				* Several of these parameters are passed along to add_menu_page()/add_submenu_page().
				*/

				'option_key'   => 'apppresser_onesignal', // The option key and admin menu page slug.
				// 'icon_url'        => 'dashicons-palmtree', // Menu icon. Only applicable if 'parent_slug' is left empty.
				// 'menu_title'      => esc_html__( 'Options', 'myprefix' ), // Falls back to 'title' (above).
				// 'parent_slug'     => 'themes.php', // Make options page a submenu item of the themes menu.
				// 'capability'      => 'manage_options', // Cap required to view options-page.
				// 'position'        => 1, // Menu position. Only applicable if 'parent_slug' is left empty.
				// 'admin_menu_hook' => 'network_admin_menu', // 'network_admin_menu' to add network-level options page.
				// 'display_cb'      => false, // Override the options-page form output (CMB2_Hookup::options_page_output()).
				// 'save_button'     => esc_html__( 'Save Theme Options', 'myprefix' ), // The text for the options-page save button. Defaults to 'Save'.
			]
		);

		/*
		* Options fields ids only need
		* to be unique within this box.
		* Prefix is not needed.
		*/
		$cmb_options->add_field(
			[
				'name' => esc_html__( 'OneSignal App ID', 'apppresser-onesignal' ),
				'desc' => esc_html__( 'The App ID for OneSignal.', 'apppresser-onesignal' ),
				'id'   => 'onesignal_app_id',
				'type' => 'text',
			]
		);

		$cmb_options->add_field(
			[
				'name' => esc_html__( 'OneSignal REST Key', 'apppresser-onesignal' ),
				'desc' => esc_html__( 'The OneSignal REST Key.', 'apppresser-onesignal' ),
				'id'   => 'onesignal_rest_api_key',
				'type' => 'text',
			]
		);

		$cmb_options->add_field(
			[
				'name' => esc_html__( 'Message', 'apppresser-onesignal' ),
				'desc' => esc_html__( 'The message to send as a push notification through the OneSignal API.', 'apppresser-onesignal' ),
				'id'   => 'onesignal_message',
				'type' => 'textarea',
			]
		);
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

		<?php
		if ( $response ) :
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php echo esc_html_e( 'Message successfully sent!', 'apppresser-onesignal' ); ?></p>
			</div>
			<?php
		else :
			?>
			<div class="notice notice-error is-dismissible">
				<p><?php echo esc_html_e( 'There was an issue sending your message!', 'apppresser-onesignal' ); ?></p>
			</div>
			<?php
		endif;
	}
}
