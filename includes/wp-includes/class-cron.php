<?php
/**
 * The cron-job to keep the calendars updated.
 */

namespace BrianHenryIE\WP_Simple_Calendar\WP_Includes;

use BrianHenryIE\WP_Simple_Calendar\API\API;
use BrianHenryIE\WP_Simple_Calendar\API_Interface;
use BrianHenryIE\WP_Simple_Calendar\Psr\Log\LoggerAwareInterface;
use BrianHenryIE\WP_Simple_Calendar\Psr\Log\LoggerAwareTrait;
use BrianHenryIE\WP_Simple_Calendar\Psr\Log\LoggerInterface;

/**
 * Class Cron
 *
 * @package brianhenryie/bh-wp-simple-calendar
 */
class Cron implements LoggerAwareInterface {
	use LoggerAwareTrait;

	const UPDATE_CACHES_CRON_JOB = 'bh_wp_update_calendar_caches';

	/**
	 * Cron constructor.
	 *
	 * @param API $api
	 * @param $logger
	 */
	public function __construct(
		protected API_Interface $api,
		LoggerInterface $logger
	) {
		$this->setLogger( $logger );
	}

	/**
	 *
	 *
	 * @hooked self::UPDATE_CACHES_CRON_JOB
	 */
	public function update_calendars_caches() {

		$this->logger->info( 'Starting cron job ' . self::UPDATE_CACHES_CRON_JOB );

		$this->api->update_caches();
	}
}
