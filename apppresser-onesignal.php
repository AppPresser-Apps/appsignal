<?php
/**
 * AppPresser
 *
 * @package   AppPresser
 * @copyright Copyright(c) 2019, AppPresser LLC
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 *
 * Plugin Name: AppSignal
 * Plugin URI: https://apppresser.com
 * Description: AppPresser OneSignal Push Notifications
 * Version: 1.0.6
 * Author: AppPresser
 * Author URI: https://apppresser.com
 * License: GPL2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: apppresser-onesignal
 * Domain Path: languages
 */

define( 'APPPRESSER_ONESIGNAL_DIR', trailingslashit( dirname( __FILE__ ) ) );

// Load the composer autoloader if it exists.
if ( file_exists( APPPRESSER_ONESIGNAL_DIR . 'vendor/autoload.php' ) ) {
	require_once APPPRESSER_ONESIGNAL_DIR . 'vendor/autoload.php';
}

// Include required files.
require_once APPPRESSER_ONESIGNAL_DIR . 'vendor/CMB2-conditional-logic/cmb2-conditional-logic.php';
require_once APPPRESSER_ONESIGNAL_DIR . 'vendor/CMB2-field-ajax-search/cmb2-field-ajax-search.php';
require_once APPPRESSER_ONESIGNAL_DIR . 'includes/class-registration-interface.php';
require_once APPPRESSER_ONESIGNAL_DIR . 'includes/class-options.php';
require_once APPPRESSER_ONESIGNAL_DIR . 'includes/class-api.php';

require_once APPPRESSER_ONESIGNAL_DIR . 'includes/functions.php';
require_once APPPRESSER_ONESIGNAL_DIR . 'includes/hooks.php';

require_once APPPRESSER_ONESIGNAL_DIR . 'includes/post-type.php';

if ( is_admin() ) {
	$options_page = new AppPresser\OneSignal\Options();

	// Register the options page if necessary.
	if ( $options_page->can_register() ) {
		$options_page->register();
	}


	function appsig_updater() {

		$access_token = appsig_get_option('github_access_token');
	
		require 'vendor/plugin-update/plugin-update-checker.php';
		$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
			'https://github.com/AppPresser-Apps/AppSignal',
			__FILE__,
			'appsignal'
		);
		
		//Set the branch that contains the stable release.
		$myUpdateChecker->setBranch('master');
		
		//Optional: If you're using a private repository, specify the access token like this:
		//$myUpdateChecker->setAuthentication( $access_token );

		$myUpdateChecker->getVcsApi()->enableReleaseAssets();
	}
	appsig_updater();
}


/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 * @param  string $key     Options array key
 * @param  mixed  $default Optional default value
 * @return mixed           Option value
 */
function appsig_get_option( $key = '', $default = false ) {
	if ( function_exists( 'cmb2_get_option' ) ) {
		// Use cmb2_get_option as it passes through some key filters.
		return cmb2_get_option( 'appp_onesignal', $key, $default );
	}

	// Fallback to get_option if CMB2 is not loaded yet.
	$opts = get_option( 'appp_onesignal', $default );

	$val = $default;

	if ( 'all' == $key ) {
		$val = $opts;
	} elseif ( is_array( $opts ) && array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
		$val = $opts[ $key ];
	}

	return $val;
}

