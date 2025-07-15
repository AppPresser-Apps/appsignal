<?php
/**
 * Class for handling block editor integration
 */

namespace AppPresser\OneSignal;

use WP_Error;
use WP_REST_Response;
use WP_REST_Request;

class Editor_Metabox {

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
    public function __construct() {
      $this->options = appsig_get_option('all');

        if ( ! $this->can_register() ) {
            return;
        }

        $this->register();
    }

    /**
     * Determines if the object should be registered.
     *
     * @return bool True if the object should be registered, false otherwise.
     */
    public function can_register() {
   
        return true;
    }

    /**
     * Registers hooks for this class.
     *
     * @return void
     */
    public function register() {
        add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_assets' ] );
        add_action( 'rest_api_init', array( $this, 'appsig_meta_box' ), 99 );
    }

    /**
     * Enqueue block editor assets.
     */
    public function enqueue_block_editor_assets() {
        $post_types = isset( $this->options['post_types_auto_push'] ) ? (array) $this->options['post_types_auto_push'] : [];
        $current_screen = get_current_screen();

        // Only enqueue for selected post types.
        if ( ! in_array( $current_screen->post_type, $post_types, true ) ) {
            return;
        }

        $asset_file = include APPPRESSER_ONESIGNAL_PATH . 'build/index.asset.php';

        wp_enqueue_script(
            'appsignal-editor',
            plugins_url( 'build/index.js', APPPRESSER_ONESIGNAL_PATH . 'appsignal.php' ),
            $asset_file['dependencies'],
            $asset_file['version'],
            true
        );

        wp_set_script_translations( 'appsignal-editor', 'apppresser-onesignal' );

        // Localize script with necessary data
        $options_class = new Options();
        $all_segments = $options_class->get_segments_options();
        $plugin_options = appsig_get_option('all');
        $default_segments = isset( $plugin_options['onesignal_segments'] ) ? (array) $plugin_options['onesignal_segments'] : array( 'All' );

        // Localize script with necessary data
        wp_localize_script(
            'appsignal-editor',
            'appsignalOneSignalData',
            [
                'rest_url' => rest_url(),
                'nonce' => wp_create_nonce('wp_rest'),
                'post_id' => get_the_ID(),
                'all_segments' => $all_segments,
                'default_segments' => $default_segments,
            ]
        );
    }

    /**
     * Registers the meta box.
     */
    public function appsig_meta_box() {
        $options = appsig_get_option('all');
        $post_types = isset( $options['post_types_auto_push'] ) ? (array) $options['post_types_auto_push'] : [];

        foreach ( $post_types as $post_type ) {
            register_post_meta(
                $post_type,
                'appsignal_send_notification',
                [
                    'show_in_rest'      => true,
                    'type'              => 'boolean',
                    'single'            => true,
                ]
            );

            register_post_meta(
                $post_type,
                'appsignal_notification_title',
                [
                    'show_in_rest'      => true,
                    'type'              => 'string',
                    'single'            => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ]
            );

            register_post_meta(
                $post_type,
                'appsignal_notification_message',
                [
                        'show_in_rest'      => true,
                        'type'              => 'string',
                        'single'            => true,
                        'sanitize_callback' => 'sanitize_text_field',
                ]
            );

            register_post_meta(
                $post_type,
                'appsignal_notification_segments',
                [
                    'show_in_rest' => [
                        'schema' => [
                            'type'  => 'array',
                            'items' => [
                                'type' => 'string',
                            ],
                        ],
                    ],
                    'type'         => 'array',
                    'single'       => true,
                ]
            );
        }
    }
}

new Editor_Metabox();

class AppSignal_Send_Push_API {

    /**
     * The plugin options.
     *
     * @var array
     */
    private $options;

    public function __construct() {
        $this->options = appsig_get_option('all');
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    public function register_routes() {
        register_rest_route(
            'appsignal/v1',
            '/send',
            [
                'methods'  => 'POST',
                'callback' => [ $this, 'handle_notification' ],
            ]
        );
    }

     /**
     * Handle test notification request
     */
    public function handle_notification( $request ) {
        $post_id = $request->get_param( 'post_id' );

        if ( empty( $post_id ) ) {
            return new WP_Error( 'missing_post_id', 'Post ID is required', [ 'status' => 400 ] );
        }

        // Get the post.
        $post = get_post( $post_id );
        if ( ! $post ) {
            return new WP_Error( 'post_not_found', 'Post not found', [ 'status' => 404 ] );
        }

        // Get custom title and message from post meta
        $custom_title   = get_post_meta( $post_id, 'appsignal_notification_title', true );
        $custom_message = get_post_meta( $post_id, 'appsignal_notification_message', true );

        if ( empty( $custom_title ) || empty( $custom_message ) ) {
            return new WP_Error(
                'missing_title_or_message',
                'Title or message is required',
                [ 'status' => 400 ]
            );
        }

        // Prepare notification data
        $header  = $custom_title;
        $message = $custom_message;
        $url     = get_permalink( $post );

        $segments = get_post_meta( $post_id, 'appsignal_notification_segments', true );
        if ( empty( $segments ) ) {
            $plugin_options = appsig_get_option('all');
            $segments = isset( $plugin_options['onesignal_segments'] ) ? (array) $plugin_options['onesignal_segments'] : array( 'All' );
        }

        // Send the notification using the helper function
        $result = appsig_send_message_all(
            $message,
            $header,
            '',
            $segments,
            [ 'data' => array( 'post_id' => $post_id ) ]
        );
  
        if ( empty( $result ) ) {
            return new WP_Error(
                'send_failed',
                'Failed to send notification',
                [ 'status' => 500 ]
            );
        }

        return new WP_REST_Response(
            [
                'success' => true,
                'message' => 'Notification sent successfully',
            ],
            200
        );
    }
}

new AppSignal_Send_Push_API();