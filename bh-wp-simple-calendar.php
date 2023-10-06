<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           BH_WP_Simple_Calendar
 *
 * @wordpress-plugin
 * Plugin Name:       Simple Calendar
 * Plugin URI:        http://github.com/BrianHenryIE/bh-wp-simple-calendar/
 * Description:       Displays iCal/.ics/Google Calendar in a block/widget/shortcode using templates.
 * Version:           1.0.0
 * Author:            Brian Henry
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bh-wp-simple-calendar
 * Domain Path:       /languages
 */

namespace BrianHenryIE\WP_Simple_Calendar;

use BrianHenryIE\WP_Simple_Calendar\API\API;
use BrianHenryIE\WP_Simple_Calendar\API\Settings;
use BrianHenryIE\WP_Simple_Calendar\WP_Logger\Logger;
use BrianHenryIE\WP_Simple_Calendar\WP_Includes\Activator;
use BrianHenryIE\WP_Simple_Calendar\WP_Includes\Deactivator;
use BrianHenryIE\WP_Simple_Calendar\WP_Logger\Logger_Settings_Interface;
use BrianHenryIE\WP_Simple_Calendar\WP_Logger\Logger_Settings_Trait;
use Psr\Log\LogLevel;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	throw new \Exception( 'WordPress required but not loaded.' );
}

require_once plugin_dir_path( __FILE__ ) . 'autoload.php';

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );
define( 'PLUGIN_NAME_BASENAME', plugin_basename( __FILE__ ) );
define( 'PLUGIN_NAME_PATH', plugin_dir_path( __FILE__ ) );
define( 'PLUGIN_NAME_URL', trailingslashit( plugins_url( plugin_basename( __DIR__ ) ) ) );

register_activation_hook( __FILE__, array( Activator::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( Deactivator::class, 'deactivate' ) );


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function instantiate_bh_wp_simple_calendar() {
	$settings = new Settings();
	$logger   = Logger::instance( $settings );

	$api = new API();

	return new BH_WP_Simple_Calendar( $api, $settings, $logger );
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and frontend-facing site hooks.
 */
$GLOBALS['bh_wp_simple_calendar'] = instantiate_bh_wp_simple_calendar();
