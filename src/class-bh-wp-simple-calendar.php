<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package    bh-wp-simple-calendar
 */

namespace BrianHenryIE\WP_Simple_Calendar;

use BrianHenryIE\WP_Simple_Calendar\Admin\Documentation_Page;
use BrianHenryIE\WP_Simple_Calendar\Admin\Post;
use BrianHenryIE\WP_Simple_Calendar\API\API;
use BrianHenryIE\WP_Simple_Calendar\API\Settings;
use BrianHenryIE\WP_Simple_Calendar\Frontend\Block;
use BrianHenryIE\WP_Simple_Calendar\Frontend\Renderer;
use BrianHenryIE\WP_Simple_Calendar\Frontend\Widget;
use BrianHenryIE\WP_Simple_Calendar\WP_Includes\Cron;
use BrianHenryIE\WP_Simple_Calendar\WP_Includes\I18n;
use Psr\Log\LoggerInterface;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * phpcs:disable Squiz.PHP.DisallowMultipleAssignments.Found
 */
class BH_WP_Simple_Calendar {

	/**
	 * An instance of the common functions the widget and block use.
	 *
	 * @var API
	 */
	protected API $api;

	protected LoggerInterface $logger;
	protected Settings $settings;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.2.0
	 *
	 * @param API $api Common code.
	 */
	public function __construct( $api, $settings, $logger ) {

		$this->logger   = $logger;
		$this->settings = $settings;
		$this->api      = $api;

		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_cron_hooks();

		$this->define_block_hooks();

		$this->define_frontend_hooks();
	}

	protected function define_block_hooks(): void {

		add_action( 'init', 'create_block_bh_wp_simple_calendar_block_init' );
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.2.0
	 */
	private function set_locale() {

		$plugin_i18n = new I18n();

		add_action( 'init', array( $plugin_i18n, 'load_plugin_textdomain' ) );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.2.0
	 */
	private function define_admin_hooks() {

		$documentation_page = new Documentation_Page();

		add_action( 'admin_menu', array( $documentation_page, 'add_submenu' ) );

		$plugin_post = new Post( $this->api );

		add_action( 'save_post', array( $plugin_post, 'update_cache_posts_list' ), 10, 3 );
	}

	protected function define_cron_hooks() {

		$plugin_cron = new Cron( $this->api, $this->logger );

		add_action( Cron::UPDATE_CACHES_CRON_JOB, array( $plugin_cron, 'update_calendars_caches' ) );
	}

	/**
	 * Register all of the hooks related to frontend functionality.
	 *
	 * @since    1.2.0
	 */
	private function define_frontend_hooks() {

		// $widget = new Widget( $this->api );
		//
		// add_widget( $widget );

		$renderer = new Renderer( $this->api );

		$plugin_block = new Block( $renderer, $this->settings );

		add_action( 'init', array( $plugin_block, 'register_block' ) );
	}
}
