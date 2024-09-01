<?php

use AppPresser\OneSignal;

/**
 * Push notification post type.
 *
 * @return void
 */
function push_post_type() {

	$labels = array(
		'name'                  => _x( 'Push Notifications', 'Post Type General Name', 'appsignal' ),
		'singular_name'         => _x( 'Push Notification', 'Post Type Singular Name', 'appsignal' ),
		'menu_name'             => __( 'Push Notice', 'appsignal' ),
		'name_admin_bar'        => __( 'Push Notice', 'appsignal' ),
		'archives'              => __( 'Push Archives', 'appsignal' ),
		'attributes'            => __( 'Push Attributes', 'appsignal' ),
		'parent_item_colon'     => __( 'Parent push:', 'appsignal' ),
		'all_items'             => __( 'All Push', 'appsignal' ),
		'add_new_item'          => __( 'Add New Push', 'appsignal' ),
		'add_new'               => __( 'Add Push', 'appsignal' ),
		'new_item'              => __( 'New Push', 'appsignal' ),
		'edit_item'             => __( 'Edit Push', 'appsignal' ),
		'update_item'           => __( 'Update Push', 'appsignal' ),
		'view_item'             => __( 'View Push', 'appsignal' ),
		'view_items'            => __( 'View Push', 'appsignal' ),
		'search_items'          => __( 'Search Push', 'appsignal' ),
		'not_found'             => __( 'Not found', 'appsignal' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'appsignal' ),
		'featured_image'        => __( 'Featured Image', 'appsignal' ),
		'set_featured_image'    => __( 'Set featured image', 'appsignal' ),
		'remove_featured_image' => __( 'Remove featured image', 'appsignal' ),
		'use_featured_image'    => __( 'Use as featured image', 'appsignal' ),
		'insert_into_item'      => __( 'Insert into push', 'appsignal' ),
		'uploaded_to_this_item' => __( 'Uploaded to this push', 'appsignal' ),
		'items_list'            => __( 'Push list', 'appsignal' ),
		'items_list_navigation' => __( 'Push list navigation', 'appsignal' ),
		'filter_items_list'     => __( 'Filter push list', 'appsignal' ),
	);
	$args   = array(
		'label'               => __( 'Push Notification', 'appsignal' ),
		'description'         => __( 'Push Notification', 'appsignal' ),
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
 * Define the metabox and field configurations.
 */
function appp_push_metaboxes() {

	/**
	 * Initiate the metabox
	 */
	$cmb = new_cmb2_box(
		array(
			'id'           => 'push_metabox',
			'title'        => __( 'Notification', 'appsignal' ),
			'object_types' => array( 'push_notification' ),
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true, // Show field names on the left
		// 'cmb_styles' => false, // false to disable the CMB stylesheet
		// 'closed'     => true, // Keep the metabox closed by default
		)
	);

	$cmb->add_field(
		array(
			'name' => __( 'Title', 'cmb2' ),
			'desc' => __( '', 'appsignal' ),
			'id'   => 'push_title',
			'type' => 'text',
		)
	);

	$cmb->add_field(
		array(
			'name' => __( 'Subtitle', 'cmb2' ),
			'desc' => __( '', 'appsignal' ),
			'id'   => 'push_subtitle',
			'type' => 'text',
		)
	);

	$cmb->add_field(
		array(
			'name' => __( 'Message', 'cmb2' ),
			'desc' => __( '', 'appsignal' ),
			'id'   => 'push_message',
			'type' => 'textarea_small',
		)
	);

	$cmb->add_field(
		array(
			'name'         => 'Image',
			'desc'         => 'Upload an image to display with Push Notification. .jpg or .png only',
			'id'           => 'push_image',
			'type'         => 'file',
			'options'      => array(
				'url' => false,
			),
			'text'         => array(
				'add_upload_file_text' => 'Add Image',
			),
			// query_args are passed to wp.media's library query.
			'query_args'   => array(
				// Or only allow jpg, or png images.
				'type' => array(
					'image/jpeg',
					'image/png',
				),
			),
			'preview_size' => 'small',
		)
	);

	$cmb->add_field(
		array(
			'name'    => __( 'Send To', 'appsignal' ),
			'desc'    => __( 'Choose who receives a push notification.', 'appsignal' ),
			'id'      => 'send_to',
			'type'    => 'select',
			'options' => array(
				'all'   => __( 'All', 'appsignal' ),
				'admin' => __( 'Administrators', 'appsignal' ),
			),
		)
	);

	$cmb->add_field(
		array(
			'name'    => __( 'Launch Options', 'appsignal' ),
			'desc'    => __( 'Choose what happens when a push notification is clicked.', 'appsignal' ),
			'id'      => 'launch_option',
			'type'    => 'select',
			'options' => array(
				'none'      => __( 'Nothing', 'appsignal' ),
				'push_url'  => __( 'Launch URL', 'appsignal' ),
				'push_post' => __( 'Deeplink Post', 'appsignal' ),
			),
		)
	);

	$cmb->add_field(
		array(
			'name'       => __( 'Launch URL', 'cmb2' ),
			'desc'       => __( 'This url will launch the in-app browser. Use for extranl links.', 'appsignal' ),
			'id'         => 'push_url',
			'type'       => 'text',
			'attributes' => array(
				'data-conditional-id'    => 'launch_option',
				'data-conditional-value' => 'push_url',
			),
		// 'show_on_cb' => 'cmb2_hide_if_no_cats', // function should return a bool value
		// 'sanitization_cb' => 'my_custom_sanitization', // custom sanitization callback parameter
		// 'escape_cb'       => 'my_custom_escaping',  // custom escaping callback parameter
		// 'on_front'        => false, // Optionally designate a field to wp-admin only
		// 'repeatable'      => true,
		)
	);

	$post_types = apply_filters( 'appsignal_deeplink_post_types', array( 'post' ) );

	$cmb->add_field(
		array(
			'name'       => __( 'Deeplink Post', 'cmb2' ),
			'desc'       => __( 'This post will open when push notifiction is clicked.', 'appsignal' ),
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
 * Add submit push button to bottom of metabox.
 *
 * @param  integar             $post_id
 * @param  CMB2_Metabox object $$cmb      cmb object
 */
function appp_cmb2_send_push( $post_id, $cmb ) {

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
			<input type="checkbox" id="push-check" />
			<label> I want to send a push.</lable>
		</div>
		<div id="appp-send-push-btn"></div>

	</div>

	<script>

		const pushBTN = jQuery('<button/>',
		{
			text: 'Send Push',
			class: 'button button-primary',
			click: function (e) {
				e.preventDefault();

				const title = jQuery('#push_title').val();
				const subTitle = jQuery('#push_subtitle').val();
				const message = jQuery('#push_message').val();
				const image = jQuery('#push_image').val();
				const url = jQuery('#push_url').val();
				const send_to = jQuery('#send_to').val();
				const option = jQuery('#launch_option').val();
				const post = jQuery('#push_post').val();
				const checked = jQuery('#push-check').is(':checked');

				console.log(title, subTitle, message, checked, option);

				if ( checked ) {

					if ( !message || '' === message ) {
						alert('Message is required.');
						jQuery('#push_message').focus();
						return;
					}

					jQuery(pushBTN).prop("disabled",true);

					jQuery.ajax({
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
							jQuery(pushBTN).prop("disabled",false);
							jQuery('#push-check').prop('checked', false);
						}
					});

				} else {
					alert('Please check "I want to send a push".');
					jQuery('#push-check').focus();
					return;
				}
			}
		});

		jQuery('#appp-send-push-btn').append(pushBTN).end();

	</script>

	<?php
}
add_action( 'cmb2_after_post_form_push_metabox', 'appp_cmb2_send_push', 10, 2 );

/**
 * Send push callback function.
 *
 * @return void
 */
function appp_send_push() {

	check_ajax_referer( 'push-nonce', 'security' );

	if ( ! $_POST && ! isset( $_POST['push_message'] ) ) {
		return;
	}

	$post_id       = $_POST['post_id'] ?? '';
	$push_message  = $_POST['push_message'];
	$push_header   = $_POST['push_header'] ?? '';
	$push_subtitle = $_POST['push_subtitle'] ?? '';
	$push_image    = $_POST['push_image'] ?? '';
	$push_url      = $_POST['push_url'] ?? '';
	$push_post     = $_POST['push_post'] ?? 0;

	$launch_option = $_POST['launch_option'] ?? 'nothing';
	$send_to       = $_POST['send_to'] ?? 'all';

	$options = array();

	switch ( $launch_option ) {
		case 'push_url':
			$options['url'] = $push_url;
			break;
		case 'push_post':
			$post                        = get_post( $push_post );
			$post_type                   = 'post' === $post->post_type ? $post->post_type . 's' : $post->post_type;
			$options['data']['deeplink'] = '/' . $post_type . '/' . $post->post_name;
			break;

		default:
			// code...
			break;
	}

	if ( $push_image ) {
		$options['image'] = $push_image;
	}

	switch ( $send_to ) {
		case 'all':
			$response = AppPresser\OneSignal\appsig_send_message_all( $push_message, $push_header, $push_subtitle, $options );
			break;
		case 'admin':
			$options['users'] = array_map( 'strval', appp_admin_user_ids() );

			$response = AppPresser\OneSignal\appsig_send_message( $push_message, $push_header, $push_subtitle, $options );
			break;
		default:
			// code...
			break;
	}

	die();

}
add_action( 'wp_ajax_appp_send_push', 'appp_send_push' );


/**
 * Get all admin user ID's in the DB
 *
 * @return array
 */
function appp_admin_user_ids() {

	global $wpdb;

	$wp_user_search = $wpdb->get_results( "SELECT ID, display_name FROM $wpdb->users ORDER BY ID" );

	$admin_array = array();

	foreach ( $wp_user_search as $userid ) {
		$cur_id     = $userid->ID;
		$curuser    = get_userdata( $cur_id );
		$user_level = $curuser->user_level;
		if ( $user_level >= 8 ) {
			$admin_array[] = $cur_id;
		}
	}
	return $admin_array;
}
