<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package    brianhenryie/bh-wp-simple-calendar
 */

namespace BrianHenryIE\WP_Simple_Calendar\Admin;

use BrianHenryIE\WP_Simple_Calendar\Settings_Interface;

/**
 * Enqueues the style and the script.
 */
class Admin_Assets {
	/**
	 * Constructor
	 *
	 * @param Settings_Interface $settings The plugin basename is used to determine the URLs.
	 */
	public function __construct(
		protected Settings_Interface $settings,
	) {
	}

	/**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles(): void {
		wp_enqueue_style(
			$this->settings->get_plugin_name(),
			plugins_url( 'assets/bh-wp-simple-calendar-admin.css', $this->settings->get_plugin_basename() ),
			array(),
			$this->settings->get_plugin_version(),
			'all'
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts(): void {
		wp_enqueue_script(
			$this->settings->get_plugin_name(),
			plugins_url( 'assets/bh-wp-simple-calendar-admin.js', $this->settings->get_plugin_basename() ),
			array( 'jquery' ),
			$this->settings->get_plugin_version(),
			false
		);
	}
}
