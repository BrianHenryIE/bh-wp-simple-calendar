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

use BrianHenryIE\WP_Simple_Calendar\Admin\Post;
use BrianHenryIE\WP_Simple_Calendar\Frontend\Block;
use BrianHenryIE\WP_Simple_Calendar\WP_Includes\Blocks;
use BrianHenryIE\WP_Simple_Calendar\WP_Includes\Cron;
use BrianHenryIE\WP_Simple_Calendar\WP_Includes\I18n;
use Psr\Container\ContainerInterface;

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
	 * Define the core functionality of the plugin.
	 *
	 * Load the dependencies, define the locale, and add the hooks and filters.
	 *
	 * @since    1.2.0
	 *
	 * @param ContainerInterface $container The DI container.
	 */
	public function __construct(
		protected ContainerInterface $container
	) {
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_cron_hooks();

		$this->define_block_hooks();

		$this->define_frontend_hooks();
	}

	protected function define_block_hooks(): void {

		/** @var Blocks $blocks */
		$blocks = $this->container->get( Blocks::class );

		add_action( 'init', array( $blocks, 'create_block_bh_wp_simple_calendar_block_init' ) );
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.2.0
	 */
	private function set_locale(): void {

		/** @var I18n $plugin_i18n */
		$plugin_i18n = $this->container->get( I18n::class );

		add_action( 'plugins_loaded', array( $plugin_i18n, 'load_plugin_textdomain' ) );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.2.0
	 */
	private function define_admin_hooks(): void {

		/** @var Post $plugin_post */
		$plugin_post = $this->container->get( Post::class );

		add_action( 'save_post', array( $plugin_post, 'update_cache_posts_list' ), 10, 3 );
	}

	protected function define_cron_hooks(): void {

		/** @var Cron $plugin_cron */
		$plugin_cron = $this->container->get( Cron::class );

		add_action( Cron::UPDATE_CACHES_CRON_JOB, array( $plugin_cron, 'update_calendars_caches' ) );
	}

	/**
	 * Register all of the hooks related to frontend functionality.
	 *
	 * @since    1.2.0
	 */
	private function define_frontend_hooks(): void {

		// $widget = new Widget( $this->api );
		// add_widget( $widget );

		/** @var Block $plugin_block */
		$plugin_block = $this->container->get( Block::class );

		add_action( 'init', array( $plugin_block, 'register_block' ) );
	}
}
