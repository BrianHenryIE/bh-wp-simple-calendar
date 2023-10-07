<?php
/**
 * The main plugin functions.
 *
 * @package brianhenryie/bh-wp-simple-calendar
 */

namespace BrianHenryIE\WP_Simple_Calendar\API;

use BrianHenryIE\WP_Simple_Calendar\API_Interface;
use BrianHenryIE\WP_Simple_Calendar\ICal\Event;
use BrianHenryIE\WP_Simple_Calendar\ICal\ICal;
use BrianHenryIE\WP_Simple_Calendar\WP_Includes\Cron;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Exception;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * Mostly cache functions.
 */
class API implements API_Interface {
	use LoggerAwareTrait;

	/**
	 * Constructor
	 *
	 * @param LoggerInterface $logger A PSR-3 compliant logger.
	 */
	public function __construct(
		LoggerInterface $logger,
	) {
		$this->setLogger( $logger );
	}

	/**
	 * Stored in wp_options, an associative array of { calendar_url : post_id[] }.
	 */
	const CACHED_CALENDARS_OPTION_NAME = 'bh_wp_calendars';

	/**
	 * Each individual cached calendar is stored with the option_name beginning with...
	 */
	const CACHED_CALENDARS_OPTION_PREFIX = 'bh_wp_calendar_';

	/**
	 * The main function. Coordinates fetching a calendar either from cache or remotely,
	 * and returning it as an array of events, filtered to the # and the timeframe.
	 *
	 * @param string $calendar_id A Google calendar id or .ical URL.
	 * @param int    $period The number of days from now to show.
	 * @param int    $count The number of events to return.
	 *
	 * @return Event[]|null
	 */
	public function get_upcoming_events( string $calendar_id, int $period, int $count ): ?array {

		// Google calendar ids take the form of an email address.
		if ( is_email( $calendar_id ) ) {
			$calendar_url = $this->get_calendar_url( $calendar_id );
		} else {
			// TODO: Check http/https.
			$calendar_url = $calendar_id;
		}

		$calendar_ics = $this->get_calendar_ics( $calendar_url );

		if ( is_null( $calendar_ics ) ) {
			return null;
		}

		$ical = new ICal();
		$ical->initString( $calendar_ics );

		$range_end = new DateTime( 'now', new \DateTimeZone( $ical->calendarTimeZone() ) );

		// Default to ten days.
		$period = absint( $period ) ?: 10;
		/** @var DateInterval $date_interval */
		$date_interval = DateInterval::createFromDateString( "$period days" );
		$range_end->add( $date_interval );

		/** @var Event[] $events */
		$events = $ical->eventsFromRange( 'now', $range_end->format( 'Y-m-d' ) );
		// $ical->eventsFromInterval()

		$events = array_slice( $events, 0, $count );

		// TODO: Add filter for sanitizing the events.

		// E.g. remove [SABA] from the beginning of all the titles.
		foreach ( $events as $event ) {

			$event->summary = str_replace( '[SABA]', '', $event->summary );
			$event->summary = str_replace( "!{$event->status}!", '', $event->summary );
			$event->summary = preg_replace( '/\s+/', ' ', $event->summary );
			$event->summary = trim( $event->summary );

			$event->start_time = $ical->iCalDateToDateTime( $event->dtstart );
			$event->end_time   = $ical->iCalDateToDateTime( $event->dtend );
		}

		return $events;
	}

	/**
	 * Get the .ics file for the calendar, either from cache or from the remote url.
	 *
	 * @param string $calendar_url The URL of the calendar.
	 */
	protected function get_calendar_ics( string $calendar_url ): ?string {

		return $this->get_calendar_from_cache( $calendar_url )
			?? $this->fetch_remote_calendar( $calendar_url );
	}

	/**
	 * Fetches data from Google Calendar.
	 *
	 * In theory, this will never be invoked from the frontend, since the admin will add the calendar,
	 * then it will always exist in cache in future.
	 *
	 * @param string $calendar_url Remote URL of ics file to fetch.
	 */
	protected function fetch_remote_calendar( string $calendar_url ): ?string {

		// TODO: Use the HTTP cache header to check has it been updated.

		$http_data = wp_remote_get( $calendar_url );

		if ( is_wp_error( $http_data ) ) {
			$this->logger->error( 'Simple Calendar: ' . $http_data->get_error_message() );

			return null;
		}

		if ( ! is_array( $http_data ) || ! array_key_exists( 'body', $http_data ) ) {

			$this->logger->error( 'not array, no body in http' );
			return null;
		}

		// TODO: Check http status code.

		$calendar_ics = $http_data['body'];

		// TODO: verify it is valid ics and not an error message.

		$this->save_calendar_to_cache( $calendar_url, $calendar_ics );

		return $calendar_ics;
	}

	/**
	 * Returns the URL for fetching the calendar.
	 *
	 * If the calendar_id is already a URL, it returns directly. Otherwise, the URL is built from the calendar address
	 * entered.
	 *
	 * @param string $calendar_id A calendar ics URL or Google Calendar ID.
	 *
	 * @return string The Google Calendar ICS file URL.
	 * @throws Exception When the input is invalid.
	 */
	protected function get_calendar_url( string $calendar_id ): string {

		// If it's already a URL, return it.
		if ( false !== filter_var( $calendar_id, FILTER_VALIDATE_URL ) ) {
			return $calendar_id;
		}

		// Calendar IDs are in email format.
		if ( is_email( urldecode( $calendar_id ) ) ) {
			return 'https://calendar.google.com/calendar/ical/' . rawurlencode( $calendar_id ) . '/public/basic.ics';
		}

		// Maybe it's not set yet.

		// TODO: log.

		throw new Exception( 'Error parsing calendar URL' );
	}

	/**
	 * To be called hourly by cron to fetch the most recent events for all calendars.
	 *
	 * TODO: On a site with many, many calendars, this could exceed the PHP timeout.
	 *
	 * TODO: If the calendar is no longer on any pages, remove it from the cache.
	 */
	public function update_caches(): void {

		/** @var array<string,int[]> $cached_calendars */
		$cached_calendars = get_option( self::CACHED_CALENDARS_OPTION_NAME, array() );

		$calendar_urls = array_keys( $cached_calendars );

		foreach ( $calendar_urls as $calendar_url ) {

			$calendar_ics_content = $this->fetch_remote_calendar( $calendar_url );

			if ( is_string( $calendar_ics_content ) ) {

				$this->save_calendar_to_cache( $calendar_url, $calendar_ics_content );

				// TODO: Reset/rebuild the cache on pages the calendar was updated on.

			} else {

				// There was a problem retrieving the calendar, so schedule another attempt soon.
				// Although it could be gone forever.
				wp_schedule_single_event( time() + ( 5 * MINUTE_IN_SECONDS ), Cron::UPDATE_CACHES_CRON_JOB );
			}
		}
	}

	/**
	 * Save the calendar reference with the corresponding pages/posts it is used on in the wp_options
	 * table.
	 *
	 * TODO widgets
	 * TODO: Start and stop the cron depending on the arry being empty or not.
	 *
	 * Separately, we run an occasional cron to check do these posts still have the calendars, so the cache
	 * is not being unnecessarily updated.
	 *
	 * @param string $calendar_url The calendar URL (acting as its uuid).
	 * @param int    $post_id The post ID the calendar is on.
	 */
	public function add_post_ref_to_calendar_cache( string $calendar_url, int $post_id ): void {

		$cached_calendars = get_option( self::CACHED_CALENDARS_OPTION_NAME, array() );

		if ( ! isset( $cached_calendars[ $calendar_url ] ) ) {
			$cached_calendars[ $calendar_url ] = array();
		}

		if ( ! array_search( $post_id, $cached_calendars[ $calendar_url ], true ) ) {
			$cached_calendars[ $calendar_url ][] = $post_id;
			$updated                             = true;
		}

		// Is the 'is_updated' response from this accurate for an array?
		update_option( self::CACHED_CALENDARS_OPTION_NAME, $cached_calendars );

		// If the hourly update hasn't already been scheduled, add it here.
		if ( ! wp_get_scheduled_event( Cron::UPDATE_CACHES_CRON_JOB ) ) {

			wp_schedule_event( time(), 'hourly', Cron::UPDATE_CACHES_CRON_JOB );

		} else {
			// Otherwise just add an extra one-off.
			wp_schedule_single_event( time(), Cron::UPDATE_CACHES_CRON_JOB );
		}
	}

	/**
	 * Remove the post reference from the calendar cache.
	 *
	 * Unfortunately we won't know the calendar URL.
	 *
	 * @param int $post_id WordPress post id the calendar was on.
	 */
	public function remove_post_ref_from_calendar_cache( int $post_id ): bool {

		$cached_calendars = get_option( self::CACHED_CALENDARS_OPTION_NAME, array() );

		$updated = false;

		$calendars_no_longer_on_any_post = array();

		foreach ( $cached_calendars as $calendar_url => $post_id_array ) {

			$index = array_search( $post_id, $post_id_array, true );

			if ( false !== $index ) {

				unset( $post_id_array[ $index ] );

				if ( count( $post_id_array ) > 0 ) {
					$cached_calendars[ $calendar_url ] = $post_id_array;
				} else {
					unset( $cached_calendars[ $calendar_url ] );
					$calendars_no_longer_on_any_post[] = $calendar_url;
				}
				$updated = true;
			}
		}

		if ( $updated ) {
			update_option( self::CACHED_CALENDARS_OPTION_NAME, $cached_calendars );

			foreach ( $calendars_no_longer_on_any_post as $calendar_url ) {

				$calendar_cache_key = $this->get_calendar_cache_option_name( $calendar_url );
				delete_option( $calendar_cache_key );
			}
		}

		return $updated;
	}

	/**
	 * Return the wp_options table key (option_name) for a particular calendar's cached .ics.
	 *
	 * Keys take the form bh_wp_ followed by the calendar URL url encoded and reversed, cropped to 190 characters.
	 * The prefix allows wp_option keys related to the plugin to be easily identified.
	 * The reverse URL is to preserve the most significant bits in similar URLs.
	 * The wp_options table option_name column is limited to 191 characters.
	 *
	 * @param string $calendar_url The calendar URL.
	 */
	public function get_calendar_cache_option_name( string $calendar_url ): string {

		return substr( self::CACHED_CALENDARS_OPTION_PREFIX . strrev( rawurlencode( $calendar_url ) ), 0, 191 );
	}

	/**
	 * Get a calendar's ics file last saved to cache.
	 *
	 * @param string $calendar_url The calendar URL.
	 */
	protected function get_calendar_from_cache( string $calendar_url ): ?string {

		$calendar_cache_key = $this->get_calendar_cache_option_name( $calendar_url );

		return get_option( $calendar_cache_key, null );
	}

	/**
	 * Update the cache with the new calendar ics string.
	 *
	 * @param string $calendar_url The calendar URL.
	 * @param string $calendar_ics_content The downloaded calendar ics file content.
	 *
	 * @return bool Was the calendar updated?
	 */
	protected function save_calendar_to_cache( string $calendar_url, string $calendar_ics_content ): bool {

		$calendar_cache_key = $this->get_calendar_cache_option_name( $calendar_url );

		return update_option( $calendar_cache_key, $calendar_ics_content );
	}
}
