<?php
/**
 * Renders the calendar block (via `do_blocks()`) against a mocked .ics so the event-template
 * inner blocks receive real block context from `src/calendar/render.php`.
 *
 * @package brianhenryie/bh-wp-simple-calendar
 */

namespace BrianHenryIE\WP_Simple_Calendar\Frontend;

use BrianHenryIE\WP_Simple_Calendar\API\API;
use BrianHenryIE\WP_Simple_Calendar\WPUnit_Testcase;

/**
 * @coversNothing
 */
class Calendar_Block_Wpunit_Test extends WPUnit_Testcase {

	/**
	 * Only the blocks needed by the test post content.
	 *
	 * @var string[]
	 */
	protected const BLOCK_DIRS = array( 'calendar', 'event-template', 'event-title', 'event-description' );

	/**
	 * Two weekly series with no end date, so there are always upcoming instances.
	 * They alternate (Tuesday, Thursday), so the first two displayed events are the first
	 * instance of each series and every later event is a repeat.
	 */
	protected const ICS = <<<'ICS'
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Test//Test//EN
BEGIN:VEVENT
DTSTART:20260106T180000Z
DTEND:20260106T190000Z
SUMMARY:Weekly Sync
DESCRIPTION:Weekly sync meeting description.
UID:weekly-sync@example.com
RRULE:FREQ=WEEKLY;BYDAY=TU
END:VEVENT
BEGIN:VEVENT
DTSTART:20260108T070000Z
DTEND:20260108T080000Z
SUMMARY:Weekly Run
DESCRIPTION:Weekly run description.
UID:weekly-run@example.com
RRULE:FREQ=WEEKLY;BYDAY=TH
END:VEVENT
END:VCALENDAR
ICS;

	protected function setUp(): void {
		parent::setUp();

		foreach ( self::BLOCK_DIRS as $dir ) {
			register_block_type( codecept_root_dir( 'build/' . $dir ) );
		}

		$api = new API( $this->logger );
		add_filter( 'simple_calendar_api_instance', fn() => $api );

		add_filter( 'pre_http_request', fn() => array( 'body' => self::ICS ) );
	}

	protected function tearDown(): void {
		foreach ( self::BLOCK_DIRS as $dir ) {
			unregister_block_type( 'simple-calendar/' . $dir );
		}

		remove_all_filters( 'simple_calendar_api_instance' );
		remove_all_filters( 'pre_http_request' );

		parent::tearDown();
	}

	/**
	 * Post content as saved by the block editor.
	 *
	 * @param string $description_attributes JSON attributes for the event-description block.
	 */
	protected function get_post_content( string $description_attributes = '' ): string {
		return <<<HTML
<!-- wp:simple-calendar/calendar {"calendarUrls":["https://example.com/recurring.ics"],"eventCount":4,"eventPeriod":365} -->
<div class="wp-block-simple-calendar-calendar simple-calendar-block"><!-- wp:simple-calendar/event-template -->
<div class="wp-block-simple-calendar-event-template simple-calendar-event-template"><!-- wp:simple-calendar/event-title /-->

<!-- wp:simple-calendar/event-description {$description_attributes} /--></div>
<!-- /wp:simple-calendar/event-template --></div>
<!-- /wp:simple-calendar/calendar -->
HTML;
	}

	/**
	 * Find rendered elements carrying the given field class, e.g. `simple-calendar-event-description`.
	 *
	 * @param string $html      The rendered block HTML.
	 * @param string $css_class The field's CSS class.
	 *
	 * @return string[] The inner HTML of each matching element, in document order.
	 */
	protected function get_field_elements( string $html, string $css_class ): array {
		preg_match_all( '/<[a-z0-9]+ [^>]*class="[^"]*\b' . preg_quote( $css_class, '/' ) . '\b[^"]*"[^>]*>(.*?)<\//s', $html, $matches );
		return $matches[1];
	}

	public function test_description_is_repeated_on_every_instance_by_default(): void {
		$html = do_blocks( $this->get_post_content() );

		$this->assertCount( 4, $this->get_field_elements( $html, 'simple-calendar-event-title' ) );

		$descriptions = $this->get_field_elements( $html, 'simple-calendar-event-description' );
		$this->assertCount( 4, $descriptions );
		$this->assertCount( 2, array_keys( $descriptions, 'Weekly sync meeting description.', true ) );
		$this->assertCount( 2, array_keys( $descriptions, 'Weekly run description.', true ) );
	}

	public function test_description_is_shown_only_on_first_instance_of_each_series_when_option_enabled(): void {
		$html = do_blocks( $this->get_post_content( '{"showOnlyForFirstOccurrence":true}' ) );

		// Every instance is still listed with its title.
		$this->assertCount( 4, $this->get_field_elements( $html, 'simple-calendar-event-title' ) );

		// One description per series, not per instance.
		$this->assertSame(
			array( 'Weekly sync meeting description.', 'Weekly run description.' ),
			$this->get_field_elements( $html, 'simple-calendar-event-description' )
		);

		// The descriptions are on the first two items; the repeats have none.
		preg_match_all( '/<li class="simple-calendar-event-item">(.*?)<\/li>/s', $html, $items );
		$this->assertCount( 4, $items[1] );
		$this->assertStringContainsString( 'simple-calendar-event-description', $items[1][0] );
		$this->assertStringContainsString( 'simple-calendar-event-description', $items[1][1] );
		$this->assertStringNotContainsString( 'simple-calendar-event-description', $items[1][2] );
		$this->assertStringNotContainsString( 'simple-calendar-event-description', $items[1][3] );
	}

	public function test_option_explicitly_disabled_repeats_description(): void {
		$html = do_blocks( $this->get_post_content( '{"showOnlyForFirstOccurrence":false}' ) );

		$this->assertCount( 4, $this->get_field_elements( $html, 'simple-calendar-event-description' ) );
	}
}
