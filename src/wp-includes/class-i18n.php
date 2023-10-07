<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    brianhenryie/bh-wp-simple-calendar
 */

namespace BrianHenryIE\WP_Simple_Calendar\WP_Includes;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    brianhenryie/bh-wp-simple-calendar
 *
 * @author     Brian Henry <BrianHenryIE@gmail.com>
 */
class I18n {

	const TEXTDOMAIN = 'bh-wp-simple-calendar';

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @hooked init
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain(): void {

		load_plugin_textdomain(
			self::TEXTDOMAIN,
			false,
			plugin_basename( dirname( __DIR__, 2 ) ) . '/languages/'
		);
	}
}
