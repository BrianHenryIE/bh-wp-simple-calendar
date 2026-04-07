<?php

namespace BrianHenryIE\WP_Simple_Calendar;

use BrianHenryIE\WP_Simple_Calendar\API\Calendar_Event;

interface API_Interface {

	/**
	 * @return Calendar_Event[]|null
	 */
	public function get_upcoming_events( string $calendar_id, int $period, int $count ): ?array;

	public function update_caches(): void;

	public function add_post_ref_to_calendar_cache( string $calendar_url, int $post_id ): void;

	public function remove_post_ref_from_calendar_cache( int $post_id ): bool;

	/**
	 * Refresh the cache for a specific calendar URL by fetching fresh data from the remote.
	 *
	 * @param string $calendar_url The calendar URL to refresh.
	 * @return bool True if the cache was successfully refreshed, false on failure.
	 */
	public function refresh_calendar_cache( string $calendar_url ): bool;
}
