<?php
/**
 * REST API endpoint for fetching calendar events.
 *
 * Used by the block editor to show real event previews.
 *
 * @package brianhenryie/bh-wp-simple-calendar
 */

namespace BrianHenryIE\WP_Simple_Calendar\WP_Includes;

use BrianHenryIE\WP_Simple_Calendar\API_Interface;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Registers the `simple-calendar/v1/events` REST endpoint.
 */
class REST_API {

	const NAMESPACE = 'simple-calendar/v1';

	/**
	 * Constructor.
	 *
	 * @param API_Interface $api The plugin API for fetching events.
	 */
	public function __construct(
		protected API_Interface $api,
	) {
	}

	/**
	 * Register the REST routes.
	 *
	 * @hooked rest_api_init
	 */
	public function register_routes(): void {
		register_rest_route(
			self::NAMESPACE,
			'/cache/refresh',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'refresh_cache' ),
				'permission_callback' => array( $this, 'permissions_check' ),
				'args'                => array(
					'calendarUrl' => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_url',
					),
				),
			),
		);

		register_rest_route(
			self::NAMESPACE,
			'/events',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_events' ),
				'permission_callback' => array( $this, 'permissions_check' ),
				'args'                => array(
					'calendarUrls' => array(
						'required'          => true,
						'type'              => 'array',
						'items'             => array( 'type' => 'string' ),
						'sanitize_callback' => function ( $urls ) {
							return array_map( 'sanitize_url', $urls );
						},
					),
					'eventCount'   => array(
						'type'              => 'integer',
						'default'           => 10,
						'sanitize_callback' => 'absint',
					),
					'eventPeriod'  => array(
						'type'              => 'integer',
						'default'           => 92,
						'sanitize_callback' => 'absint',
					),
				),
			),
		);
	}

	/**
	 * Refresh the cache for a given calendar URL.
	 *
	 * @param WP_REST_Request $request The REST request.
	 * @return WP_REST_Response
	 */
	public function refresh_cache( WP_REST_Request $request ): WP_REST_Response {
		$calendar_url = $request->get_param( 'calendarUrl' );
		$success      = $this->api->refresh_calendar_cache( $calendar_url );

		if ( $success ) {
			return new WP_REST_Response( array( 'success' => true ), 200 );
		}

		return new WP_REST_Response( array( 'success' => false ), 502 );
	}

	/**
	 * Only editors and above can fetch calendar preview data.
	 *
	 * @return bool|WP_Error
	 */
	public function permissions_check(): bool|WP_Error {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You do not have permission to access calendar events.', 'bh-wp-simple-calendar' ),
				array( 'status' => 403 ),
			);
		}
		return true;
	}

	/**
	 * Fetch events from one or more calendar URLs.
	 *
	 * @param WP_REST_Request $request The REST request.
	 * @return WP_REST_Response
	 */
	public function get_events( WP_REST_Request $request ): WP_REST_Response {
		$calendar_urls = $request->get_param( 'calendarUrls' );
		$event_count   = $request->get_param( 'eventCount' );
		$event_period  = $request->get_param( 'eventPeriod' );

		$all_events = array();

		foreach ( $calendar_urls as $url ) {
			$events = $this->api->get_upcoming_events( $url, $event_period, $event_count );
			if ( is_array( $events ) ) {
				$all_events = array_merge( $all_events, $events );
			}
		}

		// Sort by start time.
		usort( $all_events, fn( $a, $b ) => $a->start_time <=> $b->start_time );

		// Limit to requested count.
		$all_events = array_slice( $all_events, 0, $event_count );

		$data = array_map( fn( $event ) => $event->to_array(), $all_events );

		return new WP_REST_Response( $data, 200 );
	}
}
