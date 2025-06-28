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

	switch ( $args->component_action ) {

		case 'new_message':
			$data = appsig_format_new_message( $args );

			$has_sent_notification = appsig_get_message_meta_by_value( $data->thread_id );

			if ( ! empty( $data ) && 1 === $args->is_new && ! $has_sent_notification ) {
				bp_messages_add_meta( $args->item_id, 'has_sent_notification', $data->thread_id );
				AppPresser\OneSignal\appsig_send_message( $data->subject, 'New Message', $data->recipients );
			}

			break;

		case 'new_at_mention':
			if ( 1 === $args->is_new ) {

				$sender = bp_core_get_user_displayname( $args->secondary_item_id );

				AppPresser\OneSignal\appsig_send_message( $sender . ' mentioned you.', 'New Mention', array( $args->user_id ) );
			}

			break;

		case 'update_reply':
			if ( 1 === $args->is_new ) {

				$sender = bp_core_get_user_displayname( $args->secondary_item_id );

				AppPresser\OneSignal\appsig_send_message( $sender . ' replied to you.', 'New Reply', array( $args->user_id ) );
			}

			break;

		case 'friendship_request':
			if ( 1 === $args->is_new ) {

				$sender = bp_core_get_user_displayname( $args->item_id );

				AppPresser\OneSignal\appsig_send_message( $sender . ' requested friendship.', 'New Friend Request', array( $args->user_id ) );
			}

			break;

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

	$message->recipients = appsig_get_recipients( $message->sender_id, $message->thread_id );

	return $message;

}


/**
 * Helper function to get recipients of a single message of a thread.
 *
 * @param integer $sender_id
 * @param integer $thread_id
 * @return array
 */
function appsig_get_recipients( $sender_id = 0, $thread_id = 0 ) {
	global $wpdb;

	$bp = buddypress();

	$recipients = array();

	$results = $wpdb->get_results( $wpdb->prepare( "SELECT user_id FROM {$bp->messages->table_name_recipients} WHERE thread_id = %d", $thread_id ) );

	foreach ( (array) $results as $recipient ) {
		if ( (string) $sender_id !== $recipient->user_id ) {
			$recipients[] = $recipient->user_id;
		}
	}

	return $recipients;
}


/**
 * Get message meta by value.
 *
 * @since 1.0.0
 *
 * @param  int $value the meta_value.
 * @return int/boolean The ID of the message if found, otherwise false.
 */
function appsig_get_message_meta_by_value( $value = 0 ) {
	global $wpdb;

	$bp = buddypress();

	return $wpdb->get_var( $wpdb->prepare( "SELECT * FROM {$bp->messages->table_name_meta} WHERE meta_value = %d", $value ) );
}

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
		[ 'url' => get_permalink( $post_id ) ]
	);

	// Remove the meta value to prevent sending the notification again.
	delete_post_meta( $post_id, 'appsignal_send_notification' );
}
add_action( 'publish_future_post', 'appsig_send_push_notification' );
