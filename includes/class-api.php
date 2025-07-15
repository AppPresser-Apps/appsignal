<?php
/**
 * API class for AppPresser OneSignal
 *
 * @package  AppPresser OneSignal
 */

namespace AppPresser\OneSignal;

/**
 * Class for holding integration with OneSignal logic.
 */
class API {
	/**
	 * Holds the App ID.
	 *
	 * @var string
	 */
	private $app_id = null;

	/**
	 * Holds the REST API key.
	 *
	 * @var string
	 */
	private $rest_api_key = null;


	/**
	 * Endpoint URL for OneSignal.
	 *
	 * @var string
	 */
	const ONESIGNAL_ENDPOINT_URL = 'https://onesignal.com/api/v1/notifications';

	/**
	 * Class constructor.
	 *
	 * @return void
	 */
	public function __construct( string $app_id, string $rest_api_key ) {
		$this->app_id       = $app_id;
		$this->rest_api_key = $rest_api_key;
	}

	/**
	 * Sends a push notificiation using the OneSignal API.
	 *
	 * @param string $message The message to send.
	 * @param string $header Message header.
	 * @param string $subtitle Message subtitle.
	 * @param array  $options Options for sending the message.
	 * @return mixed          API Response;
	 */
	    public function send_message( string $message, string $header = '', string $subtitle = '', array $segments = array( 'All' ), array $options = array() ) {

		$body = array(
			'app_id'             => $this->app_id,
			'included_segments'  => $segments,
			'contents'          => array(
				'en' => stripslashes( $message ),
			),
			'headings'          => array(
				'en' => stripslashes( $header ),
			),
			'subtitle'          => array(
				'en' => stripslashes( $subtitle ),
			),
		);

		// Only add image-related fields if an image is provided
		if ( ! empty( $options['image'] ) ) {
			$body['ios_attachments'] = array(
				'id1' => $options['image'],
			);
			$body['big_picture'] = $options['image'];
		}

		if ( isset( $options['url'] ) ) {
			$body['url'] = $options['url'];
		}

		if ( isset( $options['data'] ) ) {
			$body['data'] = $options['data'];
		}

		$args = array(
			'timeout'     => 60,
			'redirection' => 5,
			'blocking'    => true,
			'httpversion' => '1.0',
			'sslverify'   => false,
			'data_format' => 'body',
			'headers'     => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Basic ' . $this->rest_api_key,
			),
			'body'        => stripslashes( wp_json_encode( $body, JSON_UNESCAPED_SLASHES ) ),
		);

		$response = wp_remote_post( self::ONESIGNAL_ENDPOINT_URL, $args );
		$code     = $response['response']['code'] ?? 404;

		return 200 === $code;
	}

	/**
	 * Sends a push notificiation to a specific device or devices using the OneSignal API.
	 *
	 * @param string $message The message to send.
	 * @param string $header Message header.
	 * @param string $subtitle Message subtitle.
	 * @param array  $options Options for sending the message.
	 * @return mixed          API Response;
	 */
	public function send_message_to_device( string $message, string $header = '', string $subtitle = '', array $options = array() ) {
		$body = array(
			'app_id'                    => $this->app_id,
			'include_external_user_ids' => $options['users'] ?? array( 0 ),
			'contents'                  => array(
				'en' => stripslashes( $message ),
			),
			'headings'                  => array(
				'en' => stripslashes( $header ),
			),
			'subtitle'                  => array(
				'en' => stripslashes( $subtitle ),
			),
			'ios_attachments'           => ! empty( $options['image'] ) ? array(
				'id1' => $options['image'],
			) : array(),
			'big_picture'               => $options['image'] ?? '',
		);

		if ( isset( $options['url'] ) ) {
			$body['url'] = $options['url'];
		}

		if ( isset( $options['data'] ) ) {
			$body['data'] = $options['data'];
		}

		$args = array(
			'timeout'     => 60,
			'redirection' => 5,
			'blocking'    => true,
			'httpversion' => '1.0',
			'sslverify'   => false,
			'data_format' => 'body',
			'headers'     => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Basic ' . $this->rest_api_key,
			),
			'body'        => stripslashes( wp_json_encode( $body ) ),
		);

		$response = wp_remote_post( self::ONESIGNAL_ENDPOINT_URL, $args );
		$code     = $response['response']['code'] ?? 404;

		return 200 === $code;
	}

	/**
	 * Get all segments from the OneSignal API.
	 *
	 * @return array A list of segments.
	 */
	public function get_segments() {
		if ( ! $this->app_id || ! $this->rest_api_key ) {
			return array();
		}

		$url = "https://onesignal.com/api/v1/apps/{$this->app_id}/segments";

		$args = array(
			'timeout'     => 60,
			'redirection' => 5,
			'blocking'    => true,
			'httpversion' => '1.0',
			'sslverify'   => false,
			'headers'     => array(
				'Authorization' => 'Basic ' . $this->rest_api_key,
			),
		);

		$response = wp_remote_get( $url, $args );

		if ( is_wp_error( $response ) ) {
			return array();
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );

		if ( 200 !== $code ) {
			return array();
		}

		$data = json_decode( $body, true );

		if ( ! isset( $data['segments'] ) || ! is_array( $data['segments'] ) ) {
			return array();
		}

		return $data['segments'];
	}
}
