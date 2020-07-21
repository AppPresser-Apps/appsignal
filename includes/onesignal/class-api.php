<?php
/**
 * API class for AppPresser OneSignal
 *
 * @package  AppPresser OneSignal
 */

namespace AppPresser\OneSignal;

class API {
	/**
	 * Holds the App ID.
	 *
	 * @var string
	 */
	private $app_id = null;

	/**
	 * Class constructor.
	 *
	 * @return void
	 */
	public function __construct( string $app_id ) {
		$this->app_id = $app_id;
	}

	/**
	 * Sends a push notificiation using the OneSignal API.
	 *
	 * @param string $message The message to send.
	 * @param array  $options Options for sending the message.
	 * @return void
	 */
	public function send_message( string $message, array $options = [] ) {
		
	}
}
