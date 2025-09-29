<?php

/**
 * Get allowed roles
 *
 * @return array
 */
function appsignal_get_allowed_roles() {
	$options = get_option( 'appp_onesignal', array() );
	return isset( $options['onesignal_access'] ) ? $options['onesignal_access'] : array();
}

function appsignal_can_access() {
	$allowed_roles = appsignal_get_allowed_roles();
	if ( empty( $allowed_roles ) ) {
		return current_user_can( 'manage_options' );
	}
	$user = wp_get_current_user();
	foreach ( $allowed_roles as $role ) {
		if ( in_array( $role, (array) $user->roles, true ) ) {
			return true;
		}
	}
	return false;
}

function appsignal_filter_auth_token_response( $response ) {

	error_log( print_r( $response, true ) );

	if ( ! get_option( 'appsignal_is_active' ) ) {
		return $response;
	}

	$data = array();

	$data['user'] = $response;

	$data['user']['avatar'] = get_avatar_url( get_current_user_id() );

	// Sites Data
	$site_instance = new AppSignal\API\Site();
	$sites         = $site_instance->get_site();
	$data['site']  = $sites;

	return $data;
}
add_filter( 'jwt_auth_token_before_dispatch', 'appsignal_filter_auth_token_response' );


/**
 * Get AppSignal option value
 *
 * @param string $key The option key to retrieve, or 'all' for all options
 * @return mixed The option value or array of all options
 */
function appsig_get_option( $key = 'all' ) {
	$options = get_option( 'appp_onesignal', array() );

	if ( $key === 'all' ) {
		return $options;
	}

	return isset( $options[ $key ] ) ? $options[ $key ] : null;
}
