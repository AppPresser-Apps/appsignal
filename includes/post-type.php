<?php
/**
 * Registers the Push Notification Custom Post Type and handles its metaboxes and sending functionality.
 *
 * @package AppSignal
 * @since   1.0.0
 */

use AppPresser\OneSignal;

/**
 * Registers the 'push_notification' custom post type.
 *
 * @since 1.0.0
 */
function push_post_type() {

	$labels = array(
		'name'                  => _x( 'Notifications', 'Post Type General Name', 'appsignal' ),
		'singular_name'         => _x( 'Notification', 'Post Type Singular Name', 'appsignal' ),
		'menu_name'             => __( 'Notifications', 'appsignal' ),
		'name_admin_bar'        => __( 'Notification', 'appsignal' ),
		'archives'              => __( 'Notification Archives', 'appsignal' ),
		'attributes'            => __( 'Notification Attributes', 'appsignal' ),
		'parent_item_colon'     => __( 'Parent Notification:', 'appsignal' ),
		'all_items'             => __( 'All Notifications', 'appsignal' ),
		'add_new_item'          => __( 'Add New Notification', 'appsignal' ),
		'add_new'               => __( 'Add New', 'appsignal' ),
		'new_item'              => __( 'New Notification', 'appsignal' ),
		'edit_item'             => __( 'Edit Notification', 'appsignal' ),
		'update_item'           => __( 'Update Push Notification', 'appsignal' ),
		'view_item'             => __( 'View Push Notification', 'appsignal' ),
		'view_items'            => __( 'View Push Notifications', 'appsignal' ),
		'search_items'          => __( 'Search Push Notification', 'appsignal' ),
		'not_found'             => __( 'Not found', 'appsignal' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'appsignal' ),
		'featured_image'        => __( 'Featured Image', 'appsignal' ),
		'set_featured_image'    => __( 'Set featured image', 'appsignal' ),
		'remove_featured_image' => __( 'Remove featured image', 'appsignal' ),
		'use_featured_image'    => __( 'Use as featured image', 'appsignal' ),
		'insert_into_item'      => __( 'Insert into notification', 'appsignal' ),
		'uploaded_to_this_item' => __( 'Uploaded to this notification', 'appsignal' ),
		'items_list'            => __( 'Notification list', 'appsignal' ),
		'items_list_navigation' => __( 'Notification list navigation', 'appsignal' ),
		'filter_items_list'     => __( 'Filter notification list', 'appsignal' ),
	);
	$args   = array(
		'label'               => __( 'Notification', 'appsignal' ),
		'description'         => __( 'For sending notifications to users.', 'appsignal' ),
		'labels'              => $labels,
		'supports'            => array( 'title' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 10,
		'menu_icon'           => 'dashicons-megaphone',
		'show_in_admin_bar'   => false,
		'show_in_nav_menus'   => false,
		'can_export'          => false,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'page',
		'show_in_rest'        => false,
	);
	register_post_type( 'push_notification', $args );

}
add_action( 'init', 'push_post_type', 0 );


/**
 * Defines the metabox and field configurations for the 'push_notification' post type.
 *
 * @since 1.0.0
 */
function appp_push_metaboxes() {

	$cmb = new_cmb2_box(
		array(
			'id'           => 'push_metabox',
			'title'        => esc_html__( 'Notification Details', 'appsignal' ),
			'object_types' => array( 'push_notification' ),
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true, // Show field names on the left.
		)
	);

	$cmb->add_field(
		array(
			'name' => esc_html__( 'Title', 'appsignal' ),
			'desc' => esc_html__( 'The main title for the push notification.', 'appsignal' ),
			'id'   => 'push_title',
			'type' => 'text',
		)
	);

	$cmb->add_field(
		array(
			'name' => esc_html__( 'Subtitle', 'appsignal' ),
			'desc' => esc_html__( 'An optional subtitle for the push notification.', 'appsignal' ),
			'id'   => 'push_subtitle',
			'type' => 'text',
		)
	);

	$cmb->add_field(
		array(
			'name'    => esc_html__( 'Message', 'appsignal' ),
			'desc'    => esc_html__( 'The main content of the push notification.', 'appsignal' ),
			'id'      => 'push_message',
			'type'    => 'textarea_small',
			'default' => '',
		)
	);

	$cmb->add_field(
		array(
			'name'         => esc_html__( 'Image', 'appsignal' ),
			'desc'         => esc_html__( 'Upload an image to display with the push notification. JPG or PNG only.', 'appsignal' ),
			'id'           => 'push_image',
			'type'         => 'file',
			'options'      => array(
				'url' => false, // Hide the text input field.
			),
			'text'         => array(
				'add_upload_file_text' => esc_html__( 'Add Image', 'appsignal' ),
			),
			'query_args'   => array(
				'type' => array(
					'image/jpeg',
					'image/png',
				),
			),
			'preview_size' => 'small', // Image size to use when rendering the preview.
		)
	);

	$cmb->add_field(
		array(
			'name'    => esc_html__( 'Send To', 'appsignal' ),
			'desc'    => esc_html__( 'Choose who receives this push notification.', 'appsignal' ),
			'id'      => 'send_to',
			'type'    => 'select',
			'options' => array(
				'all'   => esc_html__( 'All Users', 'appsignal' ),
				'admin' => esc_html__( 'Administrators', 'appsignal' ),
			),
		)
	);

	$cmb->add_field(
		array(
			'name'    => esc_html__( 'Launch Options', 'appsignal' ),
			'desc'    => esc_html__( 'Choose what happens when a push notification is clicked.', 'appsignal' ),
			'id'      => 'launch_option',
			'type'    => 'select',
			'options' => array(
				'none'      => esc_html__( 'Nothing', 'appsignal' ),
				'push_url'  => esc_html__( 'Launch URL', 'appsignal' ),
				'push_post' => esc_html__( 'Deeplink to Post', 'appsignal' ),
			),
		)
	);

	$cmb->add_field(
		array(
			'name'       => esc_html__( 'Launch URL', 'appsignal' ),
			'desc'       => esc_html__( 'This URL will launch the in-app browser. Use for external links.', 'appsignal' ),
			'id'         => 'push_url',
			'type'       => 'text_url',
			'attributes' => array(
				'data-conditional-id'    => 'launch_option',
				'data-conditional-value' => 'push_url',
			),
		)
	);

	$post_types = apply_filters( 'appsignal_deeplink_post_types', array( 'post' ) );

	$cmb->add_field(
		array(
			'name'       => esc_html__( 'Deeplink Post', 'appsignal' ),
			'desc'       => esc_html__( 'This post will open when the push notification is clicked.', 'appsignal' ),
			'id'         => 'push_post',
			'type'       => 'post_ajax_search',
			'attributes' => array(
				'data-conditional-id'    => 'launch_option',
				'data-conditional-value' => 'push_post',
			),
			'query_args' => array(
				'post_type'      => $post_types,
				'posts_per_page' => -1,
			),
		)
	);

}
add_action( 'cmb2_admin_init', 'appp_push_metaboxes' );


/**
 * Adds a "Send Push" button and accompanying script to the metabox.
 *
 * @since 1.0.0
 *
 * @param int          $post_id The ID of the current post.
 * @param CMB2_Metabox $cmb     The CMB2 metabox object.
 */
function appp_cmb2_send_push( $post_id, $cmb ) {

	// Note: Inline styles and scripts are not a best practice. Consider moving these to enqueued assets.
	?>
	<style>
		#appp-send-push-wrap {
			display: flex;
			align-items: center;
			justify-content: space-between;
			border-top: 1px solid #e9e9e9;
			padding-top: 20px;
			padding-bottom: 10px;
			margin-top: -13px;
		}
	</style>

	<div id="appp-send-push-wrap">
		<div>
			<input type="checkbox" id="push-check" name="push-check" />
			<label for="push-check"> <?php esc_html_e( 'I want to send this push notification.', 'appsignal' ); ?></lable>
		</div>
		<div id="appp-send-push-btn"></div>
	</div>

	<script>
		jQuery(document).ready(function($) {
			// Create the 'Send Push' button.
			const pushBTN = $('<button/>',
			{
				text: '<?php esc_html_e( 'Send Push', 'appsignal' ); ?>',
				class: 'button button-primary',
				click: function (e) {
					e.preventDefault();

					// Get all the necessary values from the form fields.
					const title = $('#push_title').val();
					const subTitle = $('#push_subtitle').val();
					const message = $('#push_message').val();
					const image = $('#push_image').val();
					const url = $('#push_url').val();
					const send_to = $('#send_to').val();
					const option = $('#launch_option').val();
					const post = $('#push_post').val();
					const checked = $('#push-check').is(':checked');

					// For debugging purposes.
					// console.log(title, subTitle, message, checked, option);

					// Ensure the confirmation checkbox is checked.
					if ( checked ) {

						// A message is required to send a notification.
						if ( !message || '' === message ) {
							alert('<?php esc_html_e( 'Message is required.', 'appsignal' ); ?>');
							$('#push_message').focus();
							return;
						}

						// Disable the button to prevent multiple submissions.
						$(pushBTN).prop("disabled",true);

						// Perform the AJAX request to send the push notification.
						$.ajax({
							url : '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
							type : 'post',
							data : {
								action : 'appp_send_push',
								security: '<?php echo esc_attr( wp_create_nonce( 'push-nonce' ) ); ?>',
								post_id : '<?php echo esc_attr( $post_id ); ?>',
								push_message: message,
								push_header: title,
								push_image: image,
								push_url: url,
								push_post: post,
								push_subtitle: subTitle,
								launch_option: option,
								send_to: send_to
							},
							success : function( response ) {
								// Re-enable the button and uncheck the box on success.
								$(pushBTN).prop("disabled",false);
								$('#push-check').prop('checked', false);
								alert('<?php esc_html_e( 'Push notification sent!', 'appsignal' ); ?>');
							},
							error: function() {
								$(pushBTN).prop("disabled",false);
								alert('<?php esc_html_e( 'There was an error sending the push notification.', 'appsignal' ); ?>');
							}
						});

					} else {
						alert('<?php esc_html_e( 'Please check the box to confirm you want to send a push.', 'appsignal' ); ?>');
						$('#push-check').focus();
						return;
					}
				}
			});

			// Append the button to the container.
			$('#appp-send-push-btn').append(pushBTN);
		});
	</script>
	<?php
}
add_action( 'cmb2_after_post_form_push_metabox', 'appp_cmb2_send_push', 10, 2 );

/**
 * AJAX callback to send the push notification.
 *
 * @since 1.0.0
 */
function appp_send_push() {

	check_ajax_referer( 'push-nonce', 'security' );

	if ( empty( $_POST['push_message'] ) ) {
		wp_send_json_error( 'Message is required.' );
	}

	// Sanitize all the POST data.
	$post_id       = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : '';
	$push_message  = sanitize_textarea_field( wp_unslash( $_POST['push_message'] ) );
	$push_header   = isset( $_POST['push_header'] ) ? sanitize_text_field( wp_unslash( $_POST['push_header'] ) ) : '';
	$push_subtitle = isset( $_POST['push_subtitle'] ) ? sanitize_text_field( wp_unslash( $_POST['push_subtitle'] ) ) : '';
	$push_image    = isset( $_POST['push_image'] ) ? esc_url_raw( wp_unslash( $_POST['push_image'] ) ) : '';
	$push_url      = isset( $_POST['push_url'] ) ? esc_url_raw( wp_unslash( $_POST['push_url'] ) ) : '';
	$push_post_id  = isset( $_POST['push_post'] ) ? absint( $_POST['push_post'] ) : 0;
	$launch_option = isset( $_POST['launch_option'] ) ? sanitize_key( $_POST['launch_option'] ) : 'nothing';
	$send_to       = isset( $_POST['send_to'] ) ? sanitize_key( $_POST['send_to'] ) : 'all';

	$options = array();

	// Set launch options for the notification.
	switch ( $launch_option ) {
		case 'push_url':
			if ( $push_url ) {
				$options['url'] = $push_url;
			}
			break;
		case 'push_post':
			if ( $push_post_id ) {
				$post      = get_post( $push_post_id );
				$post_type = 'post' === $post->post_type ? $post->post_type . 's' : $post->post_type;
				$options['data']['deeplink'] = '/' . $post_type . '/' . $post->post_name;
			}
			break;
	}

	// Add image to the notification if provided.
	if ( $push_image ) {
		$options['image'] = $push_image;
	}

	$response = null;

	// Send the notification based on the selected audience.
	switch ( $send_to ) {
		case 'all':
			$response = AppPresser\OneSignal\appsig_send_message_all( $push_message, $push_header, $push_subtitle, $options );
			break;
		case 'admin':
			$options['users'] = array_map( 'strval', appp_admin_user_ids() );
			$response         = AppPresser\OneSignal\appsig_send_message( $push_message, $push_header, $push_subtitle, $options );
			break;
	}

	if ( is_wp_error( $response ) ) {
		wp_send_json_error( $response->get_error_message() );
	} else {
		wp_send_json_success( $response );
	}

}
add_action( 'wp_ajax_appp_send_push', 'appp_send_push' );


/**
 * Get all administrator user IDs.
 *
 * This is a more modern and performant way to get users with a specific role.
 *
 * @since 1.0.0
 *
 * @return array An array of administrator user IDs.
 */
function appp_admin_user_ids() {
	$admin_users = get_users(
		array(
			'role'   => 'administrator',
			'fields' => 'ID', // Only return user IDs.
		)
	);
	return $admin_users;
}
