<?php
/**
 * Functions for AppPresser OneSignal
 *
 * @package  AppPresser OneSignal
 */

namespace AppPresser\OneSignal;

/**
 * Send push data to OneSignal api.
 *
 * @param string $message
 * @param string $header
 * @param array  $user_ids
 * @return void
 */
function appsig_send_message( string $message, string $header, string $subtitle, array $options = array() ) {

	$appp_options = get_option( 'appp_onesignal' );

	if ( empty( $appp_options['onesignal_app_id'] ) || empty( $appp_options['onesignal_rest_api_key'] ) ) {
		return;
	}

	// Attempt to send the message through the OneSignal API.
	$api_class = new API( $appp_options['onesignal_app_id'], $appp_options['onesignal_rest_api_key'] );
	$response  = $api_class->send_message_to_device( $message, $header, $subtitle, $options );

	return $response;
}

/**
 * Send push data to OneSignal api.
 *
 * @param string $message
 * @param string $header
 * @param array  $user_ids
 * @return void
 */
function appsig_send_message_all( string $message, string $header, string $subtitle, $options = array() ) {

	$appp_options = get_option( 'appp_onesignal' );

	if ( empty( $appp_options['onesignal_app_id'] ) || empty( $appp_options['onesignal_rest_api_key'] ) ) {
		return;
	}

	// Attempt to send the message through the OneSignal API.
	$api_class = new API( $appp_options['onesignal_app_id'], $appp_options['onesignal_rest_api_key'] );
	$response  = $api_class->send_message( $message, $header, $subtitle, $options );

	return $response;
}
