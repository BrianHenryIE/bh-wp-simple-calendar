<?php


namespace BrianHenryIE\WP_Simple_Calendar\API;

use BrianHenryIE\WP_Simple_Calendar\API_Interface;

class API implements API_Interface {

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
	public function get_upcoming_events( $calendar_id, $period, $count ) {

		// Google calendar ids take the form of an email address
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

		// parse_ics()
		$ical = new ICal();
		$ical->initString( $calendar_ics );

		// $rangeStart = 'now'; //new \DateTime('now', new \DateTimeZone( $ical->calendarTimeZone() ));
		 $rangeEnd             = new \DateTime( 'now', new \DateTimeZone( $ical->calendarTimeZone() ) );
				 $dateInterval = \DateInterval::createFromDateString( "$period days" );
		 $rangeEnd->add( $dateInterval );

		/** @var Event[] $events */
		$events = $ical->eventsFromRange( 'now', $rangeEnd->format( 'Y-m-d' ) );
		// $ical->eventsFromInterval()

		$events = array_slice( $events, 0, $count );

		// TODO: Add filter for sanitizing the events

		// e.g. remove [SABA] from the beginning of all the titles
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
	 * @param string $calendar_url
	 *
	 * @return string|null
	 */
	protected function get_calendar_ics( $calendar_url ) {

		$calendar_ics = $this->get_calendar_from_cache( $calendar_url );

		if ( is_null( $calendar_ics ) ) {

			$calendar_ics = $this->fetch_remote_calendar( $calendar_url );

		}

		return $calendar_ics;
	}


	/**
	 * Fetches data from Google Calendar.
	 *
	 * In theory, this will never be invoked from the frontend, since the admin will add the calendar,
	 * then it will always exist in cache in future.
	 *
	 * @param string $calendar_url Remote URL of ics file to fetch.
	 *
	 * @return string|null
	 */
	protected function fetch_remote_calendar( $calendar_url ) {

		// TODO: Use the HTTP cache header to check has it been updated.

		$http_data = wp_remote_get( $calendar_url );

		if ( is_wp_error( $http_data ) ) {
			error_log( 'Simple Calendar: ' . $http_data->get_error_message() );

			return null;
		}

		if ( ! is_array( $http_data ) || ! array_key_exists( 'body', $http_data ) ) {

			error_log( 'not array, no body in http' );
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
	 * @param string $calendar_id
	 *
	 * @return string The Google Calendar ICS file URL.
	 * @throws \Exception
	 */
	protected function get_calendar_url( $calendar_id ) {

		// If it's already a URL, return it.
		if ( false !== filter_var( $calendar_id, FILTER_VALIDATE_URL ) ) {
			return $calendar_id;
		}

		// Calendar IDs are in email format.
		if ( is_email( urldecode( $calendar_id ) ) ) {
			return 'https://calendar.google.com/calendar/ical/' . urlencode( $calendar_id ) . '/public/basic.ics';
		}

		// Maybe it's not set yet.

		// log

		throw new \Exception( 'Error parsing calendar URL' );
	}


	/**
	 * To be called hourly by cron to fetch the most recent events for all calendars.
	 *
	 * TODO: On a site with many, many calendars, this could exceed the PHP timeout.
	 */
	public function update_caches() {

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
	 * @param string $calendar_url
	 * @param int    $post_id
	 */
	public function add_post_ref_to_calendar_cache( $calendar_url, $post_id ) {

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
	 *
	 *
	 * Unfortunately we won't know the calendar URL.
	 *
	 * @param int $post_id
	 */
	public function remove_post_ref_from_calendar_cache( $post_id ) {

		$cached_calendars = get_option( self::CACHED_CALENDARS_OPTION_NAME, array() );

		$updated = false;

		$calendars_no_longer_on_any_post = array();

		foreach ( $cached_calendars as $calendar_url => $post_id_array ) {

			$index = array_search( $post_id, $post_id_array, true );

			if ( $index !== false ) {

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
	 * @param $calendar_url
	 *
	 * @return string
	 */
	public function get_calendar_cache_option_name( $calendar_url ) {

		return substr( self::CACHED_CALENDARS_OPTION_PREFIX . strrev( rawurlencode( $calendar_url ) ), 0, 191 );

	}

	/**
	 * Get a calendar's ics file last saved to cache.
	 *
	 * @param $calendar_url
	 *
	 * @return string|null
	 */
	protected function get_calendar_from_cache( $calendar_url ) {

		$calendar_cache_key = $this->get_calendar_cache_option_name( $calendar_url );

		return get_option( $calendar_cache_key, null );
	}


	/**
	 * Update the cache with the new calendar ics string.
	 *
	 * @param string $calendar_url
	 * @param string $calendar_ics_content
	 *
	 * @return bool Was the calendar updated?
	 */
	protected function save_calendar_to_cache( $calendar_url, $calendar_ics_content ) {

		$calendar_cache_key = $this->get_calendar_cache_option_name( $calendar_url );

		return update_option( $calendar_cache_key, $calendar_ics_content );

	}

}
