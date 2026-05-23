<?php
/**
 * Shared server-side render logic for event field blocks.
 *
 * @package brianhenryie/bh-wp-simple-calendar
 */

namespace BrianHenryIE\WP_Simple_Calendar\Frontend;

use BrianHenryIE\WP_Simple_Calendar\WP_Logger\Logger;
use DateTimeImmutable;
use DateTimeZone;
use Throwable;

/**
 * Renders event field blocks using block context.
 */
class Event_Field_Renderer {
	private const MIDNIGHT_TIME = '00:00:00';

	/**
	 * Render an event field block.
	 *
	 * @param \WP_Block|object $block      The block instance with context.
	 * @param array            $attributes The block attributes.
	 * @param string           $field_type The field type (title, date, description, location, status, recurrence, url).
	 * @param string           $tag        The HTML tag to wrap the content in.
	 * @return string The rendered HTML.
	 */
	public static function render( object $block, array $attributes, string $field_type, string $tag = 'div' ): string {

		try {

			$context_map = array(
				'title'       => 'simple-calendar/eventSummary',
				'date'        => 'simple-calendar/eventStartTime',
				'description' => 'simple-calendar/eventDescription',
				'location'    => 'simple-calendar/eventLocation',
				'status'      => 'simple-calendar/eventStatus',
				'recurrence'  => 'simple-calendar/eventRecurrenceDescription',
				'url'         => 'simple-calendar/eventUrl',
			);

			$context_key = $context_map[ $field_type ] ?? '';
			$value       = $block->context[ $context_key ] ?? '';

			if ( empty( $value ) ) {
				return '';
			}

			// Field-specific rendering.
			switch ( $field_type ) {
				case 'title':
					$link_to_url = $attributes['linkToUrl'] ?? true;
					$event_url   = $block->context['simple-calendar/eventUrl'] ?? null;
					$text        = esc_html( $value );

					if ( $link_to_url && $event_url ) {
						$text = '<a href="' . esc_url( $event_url ) . '">' . $text . '</a>';
					}
					$value = $text;
					break;

				case 'date':
					$date_format = $attributes['dateFormat'] ?? 'l F j';
					$time_format = $attributes['timeFormat'] ?? 'H:i';
					$end_time    = $block->context['simple-calendar/eventEndTime'] ?? null;
					$show_end    = $attributes['showEndTime'] ?? false;

					$start            = new DateTimeImmutable( $value );
					$end              = $end_time ? new DateTimeImmutable( $end_time ) : null;
					$duration_seconds = $end ? $end->getTimestamp() - $start->getTimestamp() : 0;
					$is_all_day       = $end && ( $duration_seconds > 0 ) && ( 0 === ( $duration_seconds % DAY_IN_SECONDS ) );
					$is_multi_day     = $is_all_day && $duration_seconds > DAY_IN_SECONDS;

					if ( $is_all_day ) {
						// All-day events are stored at midnight UTC by the iCal library, then
						// converted to wp_timezone() on construction. If that conversion moves
						// the time off midnight, render in UTC to recover the original calendar date.
						if ( self::MIDNIGHT_TIME !== $start->format( 'H:i:s' ) ) {
							$formatted = wp_date( $date_format, $start->getTimestamp(), new DateTimeZone( 'UTC' ) );
						} else {
							$formatted = wp_date( $date_format, $start->getTimestamp() );
						}
						if ( false === $formatted ) {
							$logger = Logger::instance();
							$logger->error(
								'wp_date() failed: format={date_format}, timestamp={start_timestamp}',
								array(
									'date_format'     => $date_format,
									'start_timestamp' => $start->getTimestamp(),
								)
							);
							$value = '';
						} else {
							$value = $formatted;
							if ( $show_end && $is_multi_day ) {
								// iCal DTEND for all-day events is exclusive; subtract one day for the last displayed day.
								$last_day_ts = $end->getTimestamp() - DAY_IN_SECONDS;
								if ( self::MIDNIGHT_TIME !== $end->format( 'H:i:s' ) ) {
									$end_formatted = wp_date( $date_format, $last_day_ts, new DateTimeZone( 'UTC' ) );
								} else {
									$end_formatted = wp_date( $date_format, $last_day_ts );
								}
								if ( false !== $end_formatted ) {
									$value .= ' – ' . $end_formatted;
								}
							}
						}
					} else {
						$value = wp_date( $date_format . ', ' . $time_format, $start->getTimestamp() );

						if ( $show_end && $end ) {
							$value .= ' – ' . wp_date( $time_format, $end->getTimestamp() );
						}
					}
					break;

				case 'description':
					$value = wp_kses_post( $value );
					break;

				case 'location':
					$full_address = $value;

					foreach ( $attributes['locationRegexes'] ?? array() as $entry ) {
						$regex       = $entry['regex'] ?? '';
						$replacement = $entry['replacement'] ?? '';

						if ( '' === $regex ) {
							continue;
						}

						$value = preg_replace( '/' . $regex . '/', $replacement, $value )
							?? ( function () use ( $value, $regex ) {
								// Invalid regex — leave $value unchanged and continue.
								$logger = Logger::instance();
								$logger->warning(
									'Regex failed: {regex} {error_no} {error_message}',
									array(
										'regex'         => $regex,
										'error_no'      => preg_last_error(),
										'error_message' => preg_last_error_msg(),
									)
								);
								return $value;
							} );
					}

					$value = esc_html( $value );

					if ( ! empty( $attributes['linkToMaps'] ) ) {
						$maps_url = 'https://www.google.com/maps/search/' . rawurlencode( $full_address );
						$value    = '<a href="' . esc_url( $maps_url ) . '" target="_blank" rel="noopener noreferrer">' . $value . '</a>';
					}
					break;

				case 'status':
					$value = esc_html( ucfirst( strtolower( $value ) ) );
					break;

				case 'recurrence':
					$is_recurring = $block->context['simple-calendar/eventIsRecurring'] ?? false;
					if ( ! $is_recurring ) {
						return '';
					}
					$value = esc_html( $value );
					break;

				case 'url':
					$text  = $attributes['linkText'] ?? $value;
					$value = '<a href="' . esc_url( $value ) . '">' . esc_html( $text ) . '</a>';
					break;
			}

			$css_class          = 'simple-calendar-event-' . $field_type;
			$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $css_class ) );

			return '<' . $tag . ' ' . $wrapper_attributes . '>' . $value . '</' . $tag . '>';

		} catch ( Throwable $t ) {

			$logger = Logger::instance();
			$logger->error(
				'Event_Field_Renderer::render failed for field_type={field_type}',
				array(
					'exception'  => $t,
					'field_type' => $field_type,
				)
			);

			return current_user_can( 'manage_options' )
				? esc_html( $t->getMessage() )
				: '';
		}
	}
}
