<?php
/**
 * Immutable data object representing a calendar event for use in templates.
 *
 * Decouples the frontend templates from the external ICal\Event library class.
 *
 * @package brianhenryie/bh-wp-simple-calendar
 */

namespace BrianHenryIE\WP_Simple_Calendar\API;

use BrianHenryIE\WP_Simple_Calendar\ICal\Event;
use BrianHenryIE\WP_Simple_Calendar\ICal\ICal;
use DateTimeImmutable;

/**
 * Immutable DTO representing a single calendar event.
 */
readonly class Calendar_Event {

	/**
	 * @param string             $summary                The event title/summary.
	 * @param string             $status                 The event status (e.g. CONFIRMED, TENTATIVE, CANCELLED, POSTPONED).
	 * @param DateTimeImmutable  $start_time             The event start time (timezone-adjusted).
	 * @param ?DateTimeImmutable $end_time               The event end time (timezone-adjusted).
	 * @param string|null        $url                    The event URL, if any.
	 * @param string|null        $description            The event description, if any.
	 * @param string|null        $location               The event location, if any.
	 * @param string|null        $uid                    The unique event identifier.
	 * @param bool               $is_recurring           Whether this event is part of a recurring series.
	 * @param string|null        $recurrence_rule        The raw RRULE string (e.g. "FREQ=WEEKLY;BYDAY=TU").
	 * @param string|null        $recurrence_description Human-readable recurrence (e.g. "Every Tuesday").
	 */
	public function __construct(
		public string $summary,
		public string $status,
		public DateTimeImmutable $start_time,
		public ?DateTimeImmutable $end_time,
		public ?string $url = null,
		public ?string $description = null,
		public ?string $location = null,
		public ?string $uid = null,
		public bool $is_recurring = false,
		public ?string $recurrence_rule = null,
		public ?string $recurrence_description = null,
	) {
	}

	/**
	 * Create a Calendar_Event from an ICal\Event.
	 *
	 * @param Event $ical_event The parsed iCal event.
	 * @param ICal  $ical       The ICal instance (for date conversion).
	 *
	 * @return self
	 */
	public static function from_ical_event( Event $ical_event, ICal $ical ): self {
		$summary = $ical_event->summary ?? '';
		$status  = $ical_event->status ?? '';

		// Clean summary: remove status markers and collapse whitespace.
		$summary = str_replace( "!{$status}!", '', $summary );
		$summary = preg_replace( '/\s+/', ' ', $summary );
		$summary = trim( $summary );

		$start_datetime = $ical->iCalDateToDateTime( $ical_event->dtstart );
		$end_datetime   = empty( $ical_event->dtend ) ? null : $ical->iCalDateToDateTime( $ical_event->dtend );

		$start_time = DateTimeImmutable::createFromInterface( $start_datetime )->setTimezone( wp_timezone() );
		$end_time   = is_null( $end_datetime ) ? null : DateTimeImmutable::createFromInterface( $end_datetime )->setTimezone( wp_timezone() );

		// Detect recurring events via the RRULE property.
		$rrule        = $ical_event->rrule ?? null;
		$is_recurring = ! empty( $rrule );

		// Generated recurrence instances also have rrule_array[2] set.
		if ( ! $is_recurring ) {
			$rrule_array  = $ical_event->rrule_array ?? null;
			$is_recurring = is_array( $rrule_array ) && isset( $rrule_array[2] ) && ICal::RECURRENCE_EVENT === $rrule_array[2];
			// For generated instances, the RRULE is in rrule_array[0] or the parent event.
			if ( $is_recurring && ! empty( $rrule_array[0] ) ) {
				$rrule = $rrule_array[0];
			}
		}

		$recurrence_description = null;
		if ( $is_recurring && ! empty( $rrule ) ) {
			$recurrence_description = self::parse_rrule_to_description( $rrule );
		}

		return new self(
			summary: $summary,
			status: $status,
			start_time: $start_time,
			end_time: $end_time,
			url: $ical_event->url ?? null,
			description: $ical_event->description,
			location: $ical_event->location,
			uid: $ical_event->uid ?? null,
			is_recurring: $is_recurring,
			recurrence_rule: $rrule,
			recurrence_description: $recurrence_description,
		);
	}

	/**
	 * Parse an RRULE string into a human-readable description.
	 *
	 * Handles common cases: FREQ, INTERVAL, BYDAY, BYMONTHDAY, BYMONTH, COUNT, UNTIL.
	 *
	 * @param string $rrule The RRULE string (e.g. "FREQ=WEEKLY;BYDAY=TU,TH;INTERVAL=2").
	 * @return string Human-readable description (e.g. "Every 2 weeks on Tuesday, Thursday").
	 */
	public static function parse_rrule_to_description( string $rrule ): string {
		$parts = array();
		foreach ( explode( ';', $rrule ) as $part ) {
			$kv = explode( '=', $part, 2 );
			if ( count( $kv ) === 2 ) {
				$parts[ $kv[0] ] = $kv[1];
			}
		}

		$day_names = array(
			'MO' => 'Monday',
			'TU' => 'Tuesday',
			'WE' => 'Wednesday',
			'TH' => 'Thursday',
			'FR' => 'Friday',
			'SA' => 'Saturday',
			'SU' => 'Sunday',
		);

		$month_names = array(
			1  => 'January',
			2  => 'February',
			3  => 'March',
			4  => 'April',
			5  => 'May',
			6  => 'June',
			7  => 'July',
			8  => 'August',
			9  => 'September',
			10 => 'October',
			11 => 'November',
			12 => 'December',
		);

		$freq     = $parts['FREQ'] ?? '';
		$interval = isset( $parts['INTERVAL'] ) ? (int) $parts['INTERVAL'] : 1;

		$freq_words = array(
			'DAILY'   => array( 'day', 'days' ),
			'WEEKLY'  => array( 'week', 'weeks' ),
			'MONTHLY' => array( 'month', 'months' ),
			'YEARLY'  => array( 'year', 'years' ),
		);

		if ( ! array_key_exists( $freq, $freq_words ) ) {
			return $rrule;
		}

		$unit = $freq_words[ $freq ];

		if ( 1 === $interval ) {
			$description = 'Every ' . $unit[0];
		} else {
			$description = "Every {$interval} {$unit[1]}";
		}

		// BYDAY (e.g. "TU,TH" or "1MO" for first Monday).
		if ( ! empty( $parts['BYDAY'] ) ) {
			$days       = explode( ',', $parts['BYDAY'] );
			$day_labels = array();
			foreach ( $days as $day ) {
				// Strip ordinal prefix (e.g. "1MO" → "MO").
				$day_code = preg_replace( '/^-?\d+/', '', $day );
				$ordinal  = preg_replace( '/[A-Z]+$/', '', $day );

				$name = $day_names[ $day_code ] ?? $day_code;
				if ( '' !== $ordinal ) {
					$name = self::ordinal( (int) $ordinal ) . ' ' . $name;
				}
				$day_labels[] = $name;
			}
			$description .= ' on ' . implode( ', ', $day_labels );
		}

		// BYMONTHDAY (e.g. "15" for the 15th of the month).
		if ( ! empty( $parts['BYMONTHDAY'] ) ) {
			$days         = explode( ',', $parts['BYMONTHDAY'] );
			$description .= ' on the ' . implode( ', ', array_map( fn( $d ) => self::ordinal( (int) $d ), $days ) );
		}

		// BYMONTH (e.g. "1,6" for January and June).
		if ( ! empty( $parts['BYMONTH'] ) ) {
			$months       = explode( ',', $parts['BYMONTH'] );
			$description .= ' in ' . implode( ', ', array_map( fn( $m ) => $month_names[ (int) $m ] ?? $m, $months ) );
		}

		return $description;
	}

	/**
	 * Convert an integer to an ordinal string (1st, 2nd, 3rd, etc.).
	 *
	 * @param int $number The number to convert.
	 * @return string The ordinal string.
	 */
	protected static function ordinal( int $number ): string {
		$suffix = array( 'th', 'st', 'nd', 'rd' );
		$mod    = abs( $number ) % 100;

		$s = $suffix[ ( $mod - 20 ) % 10 ] ?? $suffix[ $mod ] ?? $suffix[0];

		return $number . $s;
	}

	/**
	 * Convert this event to an associative array, suitable for JSON serialization.
	 *
	 * @return array<string, mixed>
	 */
	public function to_array(): array {
		return array(
			'summary'               => $this->summary,
			'status'                => $this->status,
			'startTime'             => $this->start_time->format( 'c' ),
			'endTime'               => $this->end_time?->format( 'c' ),
			'url'                   => $this->url,
			'description'           => $this->description,
			'location'              => $this->location,
			'uid'                   => $this->uid,
			'isRecurring'           => $this->is_recurring,
			'recurrenceRule'        => $this->recurrence_rule,
			'recurrenceDescription' => $this->recurrence_description,
		);
	}
}
