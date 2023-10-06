<?php
/**
 * The cron-job to keep the calendars updated.
 */

namespace BrianHenryIE\WP_Simple_Calendar\WP_Includes;

use BrianHenryIE\WP_Simple_Calendar\API\API;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Class Cron
 *
 * @package BH_WP_Simple_Calendar\WP_Includes
 */
class Cron implements LoggerAwareInterface {
	use LoggerAwareTrait;

	const UPDATE_CACHES_CRON_JOB = 'bh_wp_update_calendar_caches';

	/**
	 * @var API
	 */
	protected $api;

	/**
	 * Cron constructor.
	 *
	 * @param API    $api
	 * @param $logger
	 */
	public function __construct( $api, $logger ) {
		$this->setLogger( $logger );
		$this->api = $api;
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
