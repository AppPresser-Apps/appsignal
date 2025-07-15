<?php 

namespace AppPresser\OneSignal;

/**
 * Class for handling push notification metabox for posts.
 */
class PostMetabox {

	/**
	 * The options instance.
	 *
	 * @var Options
	 */
	private $options;

	/**
	 * Constructor.
	 *
	 * @param Options $options The options instance.
	 */
	public function __construct( Options $options ) {
		$this->options = $options;
	}

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
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post_notification' ) );
	}

	/**
	 * Adds the metabox to selected post types.
	 *
	 * @return void
	 */
	/**
	 * Check if the current screen is using the block editor.
	 *
	 * @return bool
	 */
	private function is_block_editor() {
		if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) {
			return true;
		}

		$current_screen = get_current_screen();
		if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
			return true;
		}

		return false;
	}

	public function add_meta_boxes() {
		// Don't show the metabox in the block editor
		if ( $this->is_block_editor() ) {
			return;
		}

		$options = appsig_get_option('all');
		$post_types = isset( $options['post_types_auto_push'] ) ? (array) $options['post_types_auto_push'] : array();

		if ( empty( $post_types ) ) {
			return;
		}

		foreach ( $post_types as $post_type ) {
			add_meta_box(
				'appsignal_push_notification',
				esc_html__( 'Push Notification', 'apppresser-onesignal' ),
				array( $this, 'render_meta_box' ),
				$post_type,
				'side',
				'high'
			);
		}
	}

	/**
	 * Renders the metabox content.
	 *
	 * @param \WP_Post $post The post object.
	 * @return void
	 */
	public function render_meta_box( $post ) {
		// Add nonce for security.
		wp_nonce_field( 'appsignal_push_notification_nonce', 'appsignal_push_notification_nonce' );

		// Get the saved meta value.
		$send_notification = get_post_meta( $post->ID, 'appsignal_send_notification', true );
		$title = get_post_meta( $post->ID, 'appsignal_notification_title', true );
		$message = get_post_meta( $post->ID, 'appsignal_notification_message', true );
		?>
		<div class="misc-pub-section">
			<div class="">
				<div class="components-base-control components-checkbox-control">
					<div class="components-base-control__field">
						<span class="">
							<input 
								id="appsignal-send-notification" 
								class="" 
								type="checkbox" 
								name="appsignal_send_notification" 
								value="1" 
								<?php checked( $send_notification, '1' ); ?>
							>
							<label class="components-checkbox-control__label" for="appsignal-send-notification">
							<?php esc_html_e( 'Send push notification', 'apppresser-onesignal' ); ?>
						</label>
						</span>
		
						<p class="components-checkbox-control__help">
							<?php esc_html_e( 'A notification will be sent when this post is updated.', 'apppresser-onesignal' ); ?>
						</p>
					</div>

					<div class="components-base-control__field">
						<label for="appsignal-notification-title" class="components-checkbox-control__label">Title</label>
						<input type="text" id="appsignal-notification-title" name="appsignal_notification_title" value="<?php echo esc_attr( $title ); ?>" maxlength="30">
						<p class="components-checkbox-control__help">Max input 30 characters</p>
					</div>

					<div class="components-base-control__field">
						<label for="appsignal-notification-message" class="components-checkbox-control__label">Message</label>
						<textarea id="appsignal-notification-message" name="appsignal_notification_message" maxlength="60"><?php echo esc_textarea( $message ); ?></textarea>
						<p class="components-checkbox-control__help">Max input 60 characters</p>
					</div>
				</div>

				<div class="components-base-control__field components-base-control__field--button">
					<button type="button" class="components-button is-primary button-secondary" id="appsignal-send-notification-button">Send Notification</button>
				</div>
			</div>
		</div>
		<style>
			.components-checkbox-control__label {
				font-weight: 500;
				margin-bottom: 4px;
			}
			.components-checkbox-control__help {
				margin: 4px 0 0 0;
				color: #646970;
				font-size: 12px;
				line-height: 1.4;
			}
			.components-base-control__field {
					display: flex;
					flex-direction: column;
					margin-bottom: 1em;
			}

			.components-base-control__field .components-text-control__input {
					width: 100%;
			}
			.components-base-control__field--button {
				margin-top: 1.3em;
				margin-bottom: 0px;
			}
			.components-button {
				background: transparent !important;
			}
			.components-button.is-primary:hover:not(:disabled) {
				color: var(--wp-components-color-accent, #0073aa) !important;
			}
			.components-checkbox-control__input[type=checkbox]:checked {
				background-color: var(--wp-components-color-accent_inverted, #fff) !important;
			}
		</style>

		<script>
			document.addEventListener('DOMContentLoaded', function() {
				// 1. Select the button element using its unique ID
				const sendNotificationButton = document.getElementById('appsignal-send-notification-button');

				// 2. Check if the button exists to avoid errors
				if (sendNotificationButton) {
					// 3. Add a click event listener
					sendNotificationButton.addEventListener('click', function(event) {
						// Prevent the form from submitting, which is the button's default behavior
						event.preventDefault();

					// add a confirmation dialog
					if (!confirm('Are you sure you want to send this notification?')) {
						return;
					}

						sendNotification();
					});
				}
			});

			async function sendNotification() {

				const title = document.getElementById('appsignal-notification-title').value;
				const message = document.getElementById('appsignal-notification-message').value;

				if (!title || !message) {
					alert('Please fill in both title and message.');
					return;
				}

				const api_url = '<?php echo esc_url_raw( rest_url() ); ?>';
				const nonce = '<?php echo wp_create_nonce( 'wp_rest' ); ?>';

				const response = await fetch(
					`${api_url}appsignal/v1/send`,
					{
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							'X-WP-Nonce': nonce,
						},
						body: JSON.stringify({
							title,
							message,
							post_id: <?php echo $post->ID; ?>,
						}),
					}
				);

				if (!response.ok) {
					alert('Failed to send notification.');
					return;
				}

				alert('Notification sent successfully!');
			}
		</script>
		<?php
	}

	/**
	 * Handles saving the meta box data and sending the push notification.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save_post_notification( $post_id ) {
		// Check if our nonce is set.
		if ( ! isset( $_POST['appsignal_push_notification_nonce'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( sanitize_key( $_POST['appsignal_push_notification_nonce'] ), 'appsignal_push_notification_nonce' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Don't send for revisions.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		$send_notification = isset( $_POST['appsignal_send_notification'] ) && '1' === $_POST['appsignal_send_notification'];
		$title = isset( $_POST['appsignal_notification_title'] ) ? sanitize_text_field( $_POST['appsignal_notification_title'] ) : '';
		$message = isset( $_POST['appsignal_notification_message'] ) ? sanitize_textarea_field( $_POST['appsignal_notification_message'] ) : '';

		// Update the meta field to reflect the checkbox state.
		if ( $send_notification ) {
			update_post_meta( $post_id, 'appsignal_send_notification', '1' );
		} else {
			delete_post_meta( $post_id, 'appsignal_send_notification' );
		}

		update_post_meta( $post_id, 'appsignal_notification_title', $title );
		update_post_meta( $post_id, 'appsignal_notification_message', $message );

		// Only send notification if the box was checked for this update and the post is published.
		if ( $send_notification && 'publish' === get_post_status( $post_id ) ) {
			$this->send_push_notification( $post_id );
		}
	}

	/**
	 * Sends a push notification using the OneSignal API.
	 *
	 * @param int $post_id The ID of the post to send a notification for.
	 */
	public function send_push_notification( $post_id ) {


		if ( ! get_post_meta( $post_id, 'appsignal_send_notification', true ) ) {
			return;
		}

		// Get custom title and message from post meta
		$custom_title   = get_post_meta( $post_id, 'appsignal_notification_title', true );
		$custom_message = get_post_meta( $post_id, 'appsignal_notification_message', true );

		if ( empty( $custom_title ) || empty( $custom_message ) ) {
			return;
		}

		// Prepare notification data
		$header  = $custom_title;
		$message = $custom_message;
		$url     = get_permalink( $post_id );

		// Send the notification using the helper function
		appsig_send_message_all(
			$message,
			$header,
			'',
			[ 'data' => array( 'post_id' => $post_id ) ]	
		);

		// Remove the meta value to prevent sending the notification again.
		delete_post_meta( $post_id, 'appsignal_send_notification' );

	}
}
