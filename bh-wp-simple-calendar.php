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
 * Version:           3.0.0
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
use BrianHenryIE\WP_Simple_Calendar\lucatume\DI52\Container;
use BrianHenryIE\WP_Simple_Calendar\WP_Logger\Logger;
use BrianHenryIE\WP_Simple_Calendar\WP_Includes\Activator;
use BrianHenryIE\WP_Simple_Calendar\WP_Includes\Deactivator;
use BrianHenryIE\WP_Simple_Calendar\WP_Logger\Logger_Settings_Interface;
use BrianHenryIE\WP_Simple_Calendar\WP_Logger\Logger_Settings_Trait;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	throw new \Exception( 'WordPress required but not loaded.' );
}

require_once plugin_dir_path( __FILE__ ) . 'autoload.php';

/**
 * Current plugin version. Using SemVer - https://semver.org
 */
define( 'BH_WP_SIMPLE_CALENDAR_VERSION', '3.0.0' );
define( 'BH_WP_SIMPLE_CALENDAR_BASENAME', plugin_basename( __FILE__ ) );
define( 'BH_WP_SIMPLE_CALENDAR_PATH', plugin_dir_path( __FILE__ ) );
define( 'BH_WP_SIMPLE_CALENDAR_URL', trailingslashit( plugins_url( plugin_basename( __DIR__ ) ) ) );

register_activation_hook( __FILE__, array( Activator::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( Deactivator::class, 'deactivate' ) );

$container = new Container();

$container->singleton(
	ContainerInterface::class,
	static function () use( $container ) {
		return $container;
	}
);

$container->bind( API_Interface::class, API::class );
$container->bind( Settings_Interface::class, Settings::class );
$container->bind( Logger_Settings_Interface::class, Settings::class );

$container->singleton(
	LoggerInterface::class,
	static function ( Container $container ) {
		return Logger::instance( $container->get( Logger_Settings_Interface::class ) );
	}
);

$container->get( BH_WP_Simple_Calendar::class );

$GLOBALS['bh_wp_simple_calendar'] = $container->get( API_Interface::class );
