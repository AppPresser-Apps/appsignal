<?php
/**
 * Options for AppPresser OneSignal
 *
 * @package  AppPresser OneSignal
 */

namespace AppPresser\OneSignal;

class Options implements RegistrationInterface {

	/**
	 * Get the plugin options.
	 *
	 * @return array The plugin options.
	 */
	public function get_options() {
		return get_option( self::OPTION_NAME, array() );
	}
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
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_ajax_appsignal_send_test_message', array( $this, 'ajax_send_test_message' ) );
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
				'position'     => 100,
				'save_button'  => esc_html__( 'Save Settings', 'apppresser-onesignal' ),
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
				'name'    => esc_html__( 'Access', 'apppresser-onesignal' ),
				'desc'    => esc_html__( 'Choose user roles that can access push notifications.', 'apppresser-onesignal' ),
				'id'      => 'onesignal_access',
				'type'    => 'multicheck',
				'options' => $this->get_roles(),
			)
		);

			/*
		* Github Access Token
		*/
		$cmb_options->add_field(
			array(
				'name' => esc_html__( 'Github Personal Access token', 'apppresser-onesignal' ),
				'desc' => esc_html__( 'Token required for plugin updates. ', 'apppresser-onesignal' ),
				'id'   => 'github_access_token',
				'type' => 'text',
			)
		);

		/*
		* Post Types Auto Push
		*/
		$cmb_options->add_field(
			array(
				'name' => esc_html__( 'Post Push', 'apppresser-onesignal' ),
				'desc' => esc_html__( 'Choose post types to add push metabox.', 'apppresser-onesignal' ),
				'id'   => 'post_types_auto_push',
				'type' => 'multicheck',
				'options' => $this->get_post_types(),
			)
		);

		$cmb_options->add_field(
			array(
				'name' => esc_html__( 'Testing', 'apppresser-onesignal' ),
				'desc' => esc_html__( 'Send notifications to testing segment. ', 'apppresser-onesignal' ),
				'id'   => 'onesignal_testing',
				'type' => 'checkbox',
			)
		);

		/*
		* OneSignal Segments
		*/
		$cmb_options->add_field(
				array(
						'name'    => esc_html__( 'Default Segments', 'apppresser-onesignal' ),
						'desc'    => esc_html__( 'Select the default segments to send notifications to. This can be overridden on a per-post basis.', 'apppresser-onesignal' ),
						'id'      => 'onesignal_segments',
						'type'    => 'multicheck',
						'options' => $this->get_segments_options(),
						'default' => array( 'All' ),
				)
		);

		$cmb_options->add_field(
			array(
				'name'       => esc_html__( 'Test Message', 'apppresser-onesignal' ),
				'desc'       => esc_html__( 'Send a test message to all subscribers.', 'apppresser-onesignal' ),
				'id'         => 'onesignal_message',
				'type'       => 'text',
				'save_field' => false, // Don't save this field to options
				'after'      => '<button type="button" class="button button-secondary" id="send-test-message">' . esc_html__( 'Send Test Message', 'apppresser-onesignal' ) . '</button>',
			)
		);

	}

	/**
	 * Message callback for the metabox.
	 *
	 * @return void
	 */
	public function message_cb( \CMB2 $cmb2, array $args = array() ) {
		// This function is intentionally left empty as we don't want to send any message on save
		// Test messages are now only sent via the AJAX handler when the Send Test Message button is clicked
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

	/**
	 * Get WP post types and format for cmb options.
	 */
	public function get_post_types() {
		$post_types = get_post_types( array(
				'public' => true,
		), 'objects' );

		$options = array();
		foreach ( $post_types as $post_type ) {
				$options[ $post_type->name ] = $post_type->labels->singular_name;
		}

		return $options;
	}

	    /**
     * Get OneSignal segments and format for cmb options.
     */
    public function get_segments_options() {
			$options = get_option( self::OPTION_NAME );

			if ( empty( $options['onesignal_app_id'] ) || empty( $options['onesignal_rest_api_key'] ) ) {
					return array( 'all' => 'All Subscribers' );
			}

			$api      = new API( $options['onesignal_app_id'], $options['onesignal_rest_api_key'] );
			$segments = $api->get_segments();

			if ( empty( $segments ) ) {
					return array( 'all' => 'All Subscribers' );
			}

			$segment_options = array( 'All' => 'All Subscribers' );
			foreach ( $segments as $segment ) {
					$segment_options[ $segment['name'] ] = $segment['name'];
			}

			return $segment_options;
	}

	/**
	 * Enqueue admin scripts and styles.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'appsignal-admin', plugins_url( 'assets/js/admin.js', dirname( __FILE__ ) ), array( 'jquery' ), '1.0.0', true );
		wp_localize_script( 'appsignal-admin', 'appsignal', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'appsignal_send_test_message' ),
		));
	}

	/**
	 * AJAX handler for sending test messages.
	 */
	public function ajax_send_test_message() {
		check_ajax_referer( 'appsignal_send_test_message', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Unauthorized' );
		}

		$message = isset( $_POST['message'] ) ? sanitize_text_field( $_POST['message'] ) : '';

		if ( empty( $message ) ) {
			wp_send_json_error( 'Message is required' );
		}

		$options = get_option( self::OPTION_NAME );

		if ( empty( $options['onesignal_app_id'] ) || empty( $options['onesignal_rest_api_key'] ) ) {
			wp_send_json_error( 'OneSignal is not properly configured' );
		}

		$api_class = new API( $options['onesignal_app_id'], $options['onesignal_rest_api_key'] );
		$response  = $api_class->send_message( $message, '', '', array( 'image' => '' ) );

		if ( $response ) {
			wp_send_json_success( 'Message successfully sent!' );
		} else {
			wp_send_json_error( 'Failed to send message' );
		}
	}
}
