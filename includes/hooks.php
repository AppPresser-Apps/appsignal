<?php
/**
 * Hooks notification triggers for AppSignal push notification.
 *
 * @package  AppPresser OneSignal
 */

use AppPresser\OneSignal;

/**
 * Send push notification when post is scheduled to be published.
 *
 * @param int $post_id The ID of the post to send the notification for.
 */
function appsig_send_push_notification( $post_id ) {

	$post = get_post( $post_id );

	if ( 'publish' !== get_post_status( $post_id ) ) {
		return;
	}

	if ( ! get_post_meta( $post_id, 'appsignal_send_notification', true ) ) {
		return;
	}

	$custom_title   = get_post_meta( $post_id, 'appsignal_notification_title', true );
	$custom_message = get_post_meta( $post_id, 'appsignal_notification_message', true );

	// Send the notification.
	AppPresser\OneSignal\appsig_send_message_all(
		$custom_message,
		$custom_title,
		'',
		[ 'data' => array( 'post_id' => $post_id ) ]
	);

	// Remove the meta value to prevent sending the notification again.
	delete_post_meta( $post_id, 'appsignal_send_notification' );
}
add_action( 'publish_future_post', 'appsig_send_push_notification' );
