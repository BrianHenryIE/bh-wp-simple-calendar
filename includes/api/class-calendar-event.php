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

readonly class Calendar_Event {

	/**
	 * @param string            $summary     The event title/summary.
	 * @param string            $status      The event status (e.g. CONFIRMED, TENTATIVE, CANCELLED, POSTPONED).
	 * @param DateTimeImmutable $start_time  The event start time (timezone-adjusted).
	 * @param DateTimeImmutable $end_time    The event end time (timezone-adjusted).
	 * @param string|null       $url         The event URL, if any.
	 * @param string|null       $description The event description, if any.
	 * @param string|null       $location    The event location, if any.
	 * @param string|null       $uid         The unique event identifier.
	 */
	public function __construct(
		public string $summary,
		public string $status,
		public DateTimeImmutable $start_time,
		public DateTimeImmutable $end_time,
		public ?string $url = null,
		public ?string $description = null,
		public ?string $location = null,
		public ?string $uid = null,
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
		$end_datetime   = $ical->iCalDateToDateTime( $ical_event->dtend );

		$start_time = DateTimeImmutable::createFromInterface( $start_datetime )->setTimezone( wp_timezone() );
		$end_time   = DateTimeImmutable::createFromInterface( $end_datetime )->setTimezone( wp_timezone() );

		return new self(
			summary: $summary,
			status: $status,
			start_time: $start_time,
			end_time: $end_time,
			url: $ical_event->url ?? null,
			description: $ical_event->description,
			location: $ical_event->location,
			uid: $ical_event->uid ?? null,
		);
	}
}
