<?php
/**
 * Hooks BuddyPress notification triggers for AppSignal push notification.
 *
 * @package  AppPresser OneSignal
 */

use AppPresser\OneSignal;

/**
 * Hooked to BuddyPress notification save. Process each notification type to format the push message data.
 *
 * @param object $args
 * @return void
 */
function appsig_notification_push( $args ) {

	$data = [];

	switch ( $args->component_action ) {

		case 'new_message':
			$data = appsig_format_new_message( $args );
			break;

	}
	if ( ! empty( $data ) ) {
		AppPresser\OneSignal\appsig_send_message( $data->subject, 'New Message', $data->recipients );
	}

}
add_action( 'bp_notification_after_save', 'appsig_notification_push' );

/**
 * Format new_message notification type.
 *
 * @param object $args
 * @return object BP_Messages_Message
 */
function appsig_format_new_message( $args ) {

	$message = new BP_Messages_Message( $args->item_id );

	$message->recipients = get_recipients( $message->sender_id, $message->thread_id );

	return $message;

}

/**
 * Helper function to get recipeients of a single message of a thread.
 *
 * @param integer $sender_id
 * @param integer $thread_id
 * @return array
 */
function get_recipients( $sender_id = 0, $thread_id = 0 ) {
	global $wpdb;

	$bp = buddypress();

	$recipients = [];

	$results = $wpdb->get_results( $wpdb->prepare( "SELECT user_id FROM {$bp->messages->table_name_recipients} WHERE thread_id = %d", $thread_id ) );

	foreach ( (array) $results as $recipient ) {
		if ( (string) $sender_id !== $recipient->user_id ) {
			$recipients[] = $recipient->user_id;
		}
	}

	return $recipients;
}
