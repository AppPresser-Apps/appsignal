<?php // @codingStandardsIgnoreLine
/**
 * An interface for registration objects to implement.
 *
 * @package AppPresser Onesignal
 */

namespace AppPresser\OneSignal;

/**
 * Interface for registration objects to implement.
 */
interface RegistrationInterface {
	/**
	 * Determines if the object should be registered.
	 *
	 * @return bool True if the object should be registered, false otherwise.
	 */
	public function can_register();

	/**
	 * Registration method for the object.
	 *
	 * @return void
	 */
	public function register();
}
