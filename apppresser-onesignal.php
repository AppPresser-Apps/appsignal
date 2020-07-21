<?php
/**
 * AppPresser
 *
 * @package   AppPresser
 * @copyright Copyright(c) 2019, AppPresser LLC
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 *
 * Plugin Name: AppPresser OneSignal
 * Plugin URI: https://apppresser.com
 * Description: AppPresser OneSignal Integration
 * Version: 1.0.0
 * Author: AppPresser
 * Author URI: https://apppresser.com
 * License: GPL2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: apppresser-onesignal
 * Domain Path: languages
 */

// Include and register the autoloader.
require_once 'includes/class-autoloader.php';
spl_autoload_register( '\AppPresser\OneSignal\Autoloader::autoload_classes' );

define( 'APPPRESSER_ONESIGNAL_DIR', trailingslashit( dirname( __FILE__ ) ) );

if ( is_admin() ) {
	new AppPresser\OneSignal\Options();
}
