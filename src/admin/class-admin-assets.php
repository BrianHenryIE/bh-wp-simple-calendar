<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    brianhenryie/bh-wp-simple-calendar
 */

namespace BrianHenryIE\WP_Simple_Calendar\Admin;

use BrianHenryIE\WP_Simple_Calendar\API\Settings;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    brianhenryie/bh-wp-simple-calendar
 *
 * @author     Brian Henry <BrianHenryIE@gmail.com>
 */
class Admin_Assets {

	protected Settings $settings;

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct( $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->settings->get_plugin_name(), plugin_dir_url( $this->settings->get_plugin_basename() ) . 'assets/bh-wp-simple-calendar-admin.css', array(), $this->settings->get_version(), 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->settings->get_plugin_name(), plugin_dir_url( $this->settings->get_plugin_basename() ) . 'assets/bh-wp-simple-calendar-admin.js', array( 'jquery' ), $this->settings->get_version(), false );

	}

}
