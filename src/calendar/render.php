<?php
/**
 * Server-side render for the simple-calendar/calendar block.
 *
 * Fetches events from all configured calendar URLs and renders the inner blocks
 * once per event, providing event data via block context.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner block content (the event-template markup).
 * @var WP_Block $block      The block instance.
 *
 * @package brianhenryie/bh-wp-simple-calendar
 */

use BrianHenryIE\WP_Simple_Calendar\API\Calendar_Event;

// Get the API instance from the DI container.
$api = apply_filters( 'simple_calendar_api_instance', null );

if ( ! $api instanceof \BrianHenryIE\WP_Simple_Calendar\API_Interface ) {
	return;
}

$calendar_urls = $attributes['calendarUrls'] ?? array();
$event_count   = $attributes['eventCount'] ?? 10;
$event_period  = $attributes['eventPeriod'] ?? 92;

if ( empty( $calendar_urls ) ) {
	return;
}

// Fetch and merge events from all calendar URLs.
$all_events = array();
foreach ( $calendar_urls as $url ) {
	$events = $api->get_upcoming_events( $url, $event_period, $event_count );
	if ( is_array( $events ) ) {
		$all_events = array_merge( $all_events, $events );
	}
}

// Sort by start time.
usort( $all_events, fn( Calendar_Event $a, Calendar_Event $b ) => $a->start_time <=> $b->start_time );

// Limit to requested count.
$all_events = array_slice( $all_events, 0, $event_count );

if ( empty( $all_events ) ) {
	return;
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'simple-calendar-block' ) );

$output  = '<div ' . $wrapper_attributes . '>';
$output .= '<ul class="simple-calendar-event-list">';

foreach ( $all_events as $event ) {
	// Render the inner blocks (event-template) with this event's context.
	$event_context = array(
		'simple-calendar/eventSummary'               => $event->summary,
		'simple-calendar/eventStatus'                => $event->status,
		'simple-calendar/eventStartTime'             => $event->start_time->format( 'c' ),
		'simple-calendar/eventEndTime'               => $event->end_time?->format( 'c' ),
		'simple-calendar/eventUrl'                   => $event->url,
		'simple-calendar/eventDescription'           => $event->description,
		'simple-calendar/eventLocation'              => $event->location,
		'simple-calendar/eventUid'                   => $event->uid,
		'simple-calendar/eventIsRecurring'           => $event->is_recurring,
		'simple-calendar/eventRecurrenceDescription' => $event->recurrence_description,
	);

	$output .= '<li class="simple-calendar-event-item">';

	// Render inner blocks with the event context.
	foreach ( $block->inner_blocks as $inner_block ) {
		$output .= ( new WP_Block( $inner_block->parsed_block, $event_context ) )->render();
	}

	$output .= '</li>';
}

$output .= '</ul>';
$output .= '</div>';

echo $output;
