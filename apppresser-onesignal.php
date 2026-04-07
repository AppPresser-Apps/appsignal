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
 * Version: 1.1.2
 * Stable tag: 1.1.2
 * Author: AppPresser
 * Author URI: https://apppresser.com
 * License: GPL2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: apppresser-onesignal
 * Domain Path: languages
 */

define( 'APPSIGNAL_VERSION', '1.1.2' );
define( 'APPPRESSER_ONESIGNAL_DIR', trailingslashit( __DIR__ ) );
define( 'APPPRESSER_ONESIGNAL_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'APPPRESSER_ONESIGNAL_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );

if ( file_exists( APPPRESSER_ONESIGNAL_DIR . 'vendor/autoload.php' ) ) {
	require_once APPPRESSER_ONESIGNAL_DIR . 'vendor/autoload.php';
}

// Include required files.
require_once APPPRESSER_ONESIGNAL_DIR . 'admin/functions.php';
require_once APPPRESSER_ONESIGNAL_DIR . 'includes/class-api.php';
require_once APPPRESSER_ONESIGNAL_DIR . 'includes/class-post-metabox.php';
require_once APPPRESSER_ONESIGNAL_DIR . 'includes/class-editor-metabox.php';

require_once APPPRESSER_ONESIGNAL_DIR . 'includes/functions.php';
require_once APPPRESSER_ONESIGNAL_DIR . 'includes/hooks.php';

require_once APPPRESSER_ONESIGNAL_DIR . 'includes/post-type.php';
require_once APPPRESSER_ONESIGNAL_DIR . 'admin/settings.php';

/**
 * Plugin updater. Gets new version from Github.
 */
if ( is_admin() ) {

	function appsig_updater() {

		require 'vendor/plugin-update/plugin-update-checker.php';
		$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
			'https://github.com/AppPresser-Apps/appsignal',
			__FILE__,
			'appsignal'
		);

		// Set the branch that contains the stable release.
		$myUpdateChecker->setBranch( 'master' );
		$myUpdateChecker->getVcsApi()->enableReleaseAssets();
	}
	appsig_updater();
}
