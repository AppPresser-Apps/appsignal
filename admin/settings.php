<?php

namespace AppSignal\Admin;

/**
 * Settings class
 */
class Settings {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Register hooks
	 */
	private function hooks() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
	}

	/**
	 * Add menu
	 */
	public function add_menu() {
		$page_hook_suffix = add_submenu_page(
			'options-general.php',
			'AppSignal',
			'AppSignal',
			'manage_options',
			'appsignal-options',
			array( $this, 'render_page' ),
			25
		);

		add_action( "admin_print_scripts-{$page_hook_suffix}", array( $this, 'settings_assets' ) );
	}

	/**
	 * Enqueue assets
	 */
	public function settings_assets() {
		wp_enqueue_style( 'appsignal-settings', APPPRESSER_ONESIGNAL_URL . 'build/index.css', array( 'wp-components' ) );
		wp_enqueue_script( 'appsignal-settings', APPPRESSER_ONESIGNAL_URL . 'build/index.js', array( 'wp-api', 'wp-i18n', 'wp-components', 'wp-element' ), APPSIGNAL_VERSION, true );
	}


	/**
	 * Register REST API routes for CMB options
	 */
	public function register_rest_routes() {
		error_log( 'AppSignal: Registering REST API routes' );
		
		// Get options endpoint
		register_rest_route( 'appsignal/v1', '/options', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_options' ),
			'permission_callback' => array( $this, 'check_permissions' ),
		));

		// Update options endpoint
		register_rest_route( 'appsignal/v1', '/options', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'update_options' ),
			'permission_callback' => array( $this, 'check_permissions' ),
		));

		// Get roles endpoint
		register_rest_route( 'appsignal/v1', '/roles', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_roles' ),
			'permission_callback' => array( $this, 'check_permissions' ),
		));

		// Get post types endpoint
		register_rest_route( 'appsignal/v1', '/post-types', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_post_types' ),
			'permission_callback' => array( $this, 'check_permissions' ),
		));

		// Get segments endpoint
		register_rest_route( 'appsignal/v1', '/segments', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_segments' ),
			'permission_callback' => array( $this, 'check_permissions' ),
		));

		// Send test message endpoint
		register_rest_route( 'appsignal/v1', '/test-message', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'send_test_message' ),
			'permission_callback' => array( $this, 'check_permissions' ),
		));
	}

	/**
	 * Check permissions for REST API access
	 */
	public function check_permissions() {
		return function_exists( 'appsignal_can_access' ) ? appsignal_can_access() : current_user_can( 'manage_options' );
	}

	/**
	 * Get CMB options via REST API
	 */
	public function get_options() {
		$options = get_option( 'appp_onesignal', array() );
		return rest_ensure_response( $options );
	}

	/**
	 * Update CMB options via REST API
	 */
	public function update_options( $request ) {
		$params = $request->get_json_params();

		// If no JSON params, try regular params (for wp.apiRequest compatibility)
		if ( empty( $params ) ) {
			$params = $request->get_params();
		}

		// Remove WordPress internal parameters
		unset( $params['_wpnonce'], $params['_wp_http_referer'] );

		error_log( 'AppSignal update_options received params: ' . print_r( $params, true ) );

		if ( empty( $params ) ) {
			return new \WP_Error( 'no_data', 'No data provided', array( 'status' => 400 ) );
		}

		$current_options = get_option( 'appp_onesignal', array() );
		$updated_options = array_merge( $current_options, $params );
		
		$result = update_option( 'appp_onesignal', $updated_options );
		
		error_log( 'AppSignal update_option result: ' . ( $result ? 'success' : 'failed' ) );
		
		return rest_ensure_response( $updated_options );
	}

	/**
	 * Get WordPress roles for REST API
	 */
	public function get_roles() {
		global $wp_roles;
		$roles = array();
		
		foreach ( $wp_roles->roles as $role => $details ) {
			$roles[] = array(
				'value' => $role,
				'label' => $details['name']
			);
		}
		
		return rest_ensure_response( $roles );
	}

	/**
	 * Get post types for REST API
	 */
	public function get_post_types() {
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		$options = array();
		
		foreach ( $post_types as $post_type ) {
			$options[] = array(
				'value' => $post_type->name,
				'label' => $post_type->labels->singular_name
			);
		}
		
		return rest_ensure_response( $options );
	}

	/**
	 * Get OneSignal segments for REST API
	 */
	public function get_segments() {
		$options = get_option( 'appp_onesignal', array() );
		
		if ( empty( $options['onesignal_app_id'] ) || empty( $options['onesignal_rest_api_key'] ) ) {
			return rest_ensure_response( array(
				array( 'value' => 'all', 'label' => 'All Subscribers' )
			));
		}

		$api = new \AppPresser\OneSignal\API( $options['onesignal_app_id'], $options['onesignal_rest_api_key'] );
		$segments = $api->get_segments();

		if ( empty( $segments ) ) {
			return rest_ensure_response( array(
				array( 'value' => 'all', 'label' => 'All Subscribers' )
			));
		}

		$segment_options = array( array( 'value' => 'All', 'label' => 'All Subscribers' ) );
		foreach ( $segments as $segment ) {
			$segment_options[] = array(
				'value' => $segment['name'],
				'label' => $segment['name']
			);
		}

		return rest_ensure_response( $segment_options );
	}

	/**
	 * Send test message via REST API
	 */
	public function send_test_message( $request ) {
		$params = $request->get_json_params();

		// If no JSON params, try regular params (for wp.apiRequest compatibility)
		if ( empty( $params ) ) {
			$params = $request->get_params();
		}

		// Remove WordPress internal parameters
		unset( $params['_wpnonce'], $params['_wp_http_referer'] );

		error_log( 'AppSignal send_test_message received params: ' . print_r( $params, true ) );

		$message = isset( $params['message'] ) ? sanitize_text_field( $params['message'] ) : '';

		if ( empty( $message ) ) {
			error_log( 'AppSignal send_test_message: No message provided' );
			return new \WP_Error( 'no_message', 'Message is required', array( 'status' => 400 ) );
		}

		$options = get_option( 'appp_onesignal', array() );

		error_log( 'AppSignal send_test_message options: ' . print_r( $options, true ) );
		if ( empty( $options['onesignal_app_id'] ) || empty( $options['onesignal_rest_api_key'] ) ) {
			error_log( 'AppSignal send_test_message: OneSignal not configured properly' );
			return new \WP_Error( 'not_configured', 'OneSignal is not properly configured', array( 'status' => 400 ) );
		}

		error_log( 'AppSignal send_test_message: Creating API instance and sending message: ' . $message );
		$api = new \AppPresser\OneSignal\API( $options['onesignal_app_id'], $options['onesignal_rest_api_key'] );
		$response = $api->send_message( $message, '', '', array( 'image' => '' ) );

		error_log( 'AppSignal send_test_message API response: ' . ( $response ? 'success' : 'failed' ) );

		if ( $response ) {
			return rest_ensure_response( array( 'success' => true, 'message' => 'Test message sent successfully!' ) );
		} else {
			return new \WP_Error( 'send_failed', 'Failed to send test message', array( 'status' => 500 ) );
		}
	}

	/**
	 * Render settings page
	 */
	public function render_page() {
		echo '<div id="appsignal"></div>';
	}
}

new \AppSignal\Admin\Settings();
