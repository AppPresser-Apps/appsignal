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
	private $options = array();

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
		add_action( 'cmb2_admin_init', array( $this, 'add_options_page' ) );
	}

	/**
	 * Add options page
	 */
	public function add_options_page() {
		/**
		 * Registers options page menu item and form.
		 */
		$cmb_options = \new_cmb2_box(
			array(
				'id'           => self::OPTION_NAME . '_metabox',
				'title'        => esc_html__( 'AppSignal', 'apppresser-onesignal' ),
				'object_types' => array(
					'options-page',
				),
				'option_key'   => self::OPTION_NAME,
				'icon_url'     => 'dashicons-megaphone',
				'capability'   => 'manage_options',
				'position'     => 1,
				'save_button'  => esc_html__( 'Save settings and send message', 'apppresser-onesignal' ),
				'message_cb'   => array( $this, 'message_cb' ),
			)
		);

		/*
		* Options fields ids only need
		* to be unique within this box.
		* Prefix is not needed.
		*/
		$cmb_options->add_field(
			array(
				'name' => esc_html__( 'OneSignal App ID', 'apppresser-onesignal' ),
				'desc' => esc_html__( 'The App ID for OneSignal.', 'apppresser-onesignal' ),
				'id'   => 'onesignal_app_id',
				'type' => 'text',
			)
		);

		$cmb_options->add_field(
			array(
				'name' => esc_html__( 'OneSignal REST Key', 'apppresser-onesignal' ),
				'desc' => esc_html__( 'The OneSignal REST Key.', 'apppresser-onesignal' ),
				'id'   => 'onesignal_rest_api_key',
				'type' => 'text',
			)
		);

		$cmb_options->add_field(
			array(
				'name' => esc_html__( 'Test Message', 'apppresser-onesignal' ),
				'desc' => esc_html__( 'Message to send as a push notification through the OneSignal API. FOR TESTING ONLY.', 'apppresser-onesignal' ),
				'id'   => 'onesignal_message',
				'type' => 'textarea',
			)
		);

		$cmb_options->add_field(
			array(
				'name'    => esc_html__( 'Access', 'apppresser-onesignal' ),
				'desc'    => esc_html__( 'Choose user roles that can access push notifications.', 'apppresser-onesignal' ),
				'id'      => 'onesignal_access',
				'type'    => 'multicheck',
				'options' => $this->get_roles(),
			)
		);

			/*
		* Options fields ids only need
		* to be unique within this box.
		* Prefix is not needed.
		*/
		$cmb_options->add_field(
			array(
				'name' => esc_html__( 'Github Presonal Access token', 'apppresser-onesignal' ),
				'desc' => esc_html__( 'Token required for plugin updates.', 'apppresser-onesignal' ),
				'id'   => 'github_access_token',
				'type' => 'text',
			)
		);
	}

	/**
	 * Message callback for the metabox.
	 *
	 * @return void
	 */
	public function message_cb( \CMB2 $cmb2, array $args = array() ) {
		$options = get_option( self::OPTION_NAME );

		if ( empty( $options['onesignal_app_id'] ) || empty( $options['onesignal_rest_api_key'] ) || empty( $options['onesignal_message'] ) ) {
			return $cmb2;
		}

		// Attempt to send the message through the OneSignal API.
		$api_class = new API( $options['onesignal_app_id'], $options['onesignal_rest_api_key'] );
		$response  = $api_class->send_message( $options['onesignal_message'] );

		// Set the message.
		if ( $response ) {
			add_settings_error( self::OPTION_NAME . '-notices', 'success', esc_html__( 'Message successfully sent!', 'apppresser-onesignal' ), 'updated' );

			// Update the option.
			unset( $options['onesignal_message'] );
			update_option( self::OPTION_NAME, $options );
		} else {
			add_settings_error( self::OPTION_NAME . '-notices', 'error', esc_html__( 'Message failed to be sent!', 'apppresser-onesignal' ), 'error' );
		}
	}

	/**
	 * Get WP user roles and format for cmb options.
	 */
	public function get_roles() {

		global $wp_roles;

		$roles = $wp_roles->roles;

		foreach ( $roles as $role => $value ) {
			$roles[ $role ] = $value['name'];
		}

		return $roles;

	}
}

