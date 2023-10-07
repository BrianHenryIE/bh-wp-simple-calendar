<?php
/**
 * Fired during plugin deactivation
 *
 * @package    brianhenryie/bh-wp-simple-calendar
 */

namespace BrianHenryIE\WP_Simple_Calendar\WP_Includes;

/**
 * Unschedule the cron job.
 */
class Deactivator {

	/**
	 * Remove the cron job which updates the caches.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate(): void {
		wp_unschedule_hook( Cron::UPDATE_CACHES_CRON_JOB );
	}
}
