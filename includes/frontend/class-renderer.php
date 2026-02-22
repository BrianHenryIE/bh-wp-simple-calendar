<?php
/**
 * Should this be called "Calendar"?
 *
 * @package brianhenryie/bh-wp-simple-calendar
 */

namespace BrianHenryIE\WP_Simple_Calendar\Frontend;

use BrianHenryIE\WP_Simple_Calendar\API_Interface;
use BrianHenryIE\WP_Simple_Calendar\Settings_Interface;

class Renderer {

	/**
	 * @var array{title:string, calendar_id:string, event_count:int, event_period:int, date_format:string} $defaults
	 */
	protected array $defaults;

	/**
	 * @param API_Interface $api
	 *
	 * @throws \Exception
	 */
	public function __construct(
		protected API_Interface $api,
		protected Settings_Interface $settings,
	) {
		$this->defaults = array(
			'title'        => __( 'Events', 'bh_wp_simple_calendar' ),
			'calendar_id'  => '',
			'event_count'  => 10,
			'event_period' => 92,
			'date_format'  => 'l jS \of F',
		);
	}

	/**
	 * @param string $calendar_id
	 * @param int    $period In days.
	 * @param int    $count
	 */
	public function get_html( string $calendar_id, $period, int $count ) {

		$events = $this->api->get_upcoming_events( $calendar_id, $period, $count );

		$template = realpath( __DIR__ . '/../../templates/frontend/calendar-template-1.php' );

		// $template = get_template_part( 'simple-calendar', $template );

		$template = apply_filters( 'simple_calendar_template', $template, $calendar_id, $period, $count );

		if ( ! file_exists( $template ) ) {
			if ( is_admin() ) {
				return 'Template file does not exist at ' . $template;
			} else {
				return null;
			}
		}

		ob_start();

		include $template;

		$html = ob_get_clean();

		return $html;
	}

	/**
	 *
	 * @param array $data
	 * @param array $formatting
	 */
	public function render( $data, $formatting ): string {

		// TODO: wp_parse_args with defaults
		// Then filter
		// Then validate

		$dflg = $formatting['dflg'];
		$html = '';

		$id = $data['id'];

		// TODO:
		// $id was being pulled from the widget name
		// Which was being created from the plugin slug
		// So backward compati=ability must be preserved.

		if ( ! empty( $data ) && is_array( $data ) ) {

			date_default_timezone_set( get_option( 'timezone_string' ) );
			$html    .= '<ul class="list-group simple-google-calendar-block">';
			$prevdate = '';

			// TODO get_template_part();
			$custom_template = get_stylesheet_directory() . '/simple-google-calendar-block-item-template.php';

			// TODO Add filter here.

			if ( file_exists( $custom_template ) ) {
				$template = $custom_template;
			} else {
				$template = plugin_dir_path( $this->settings->get_plugin_basename() ) . '/templates/frontend/partials/item-template.php';
			}

			ob_start();

			foreach ( $data as $index => $event ) {

				$idlist = explode( '@', esc_attr( $event->uid ) );

				include $template;
			}

			$html .= ob_end_clean();

			$html .= '</ul>';
		}

		$allowed_html = array(
			'ul'   => array( 'class' => array() ),
			'li'   => array( 'class' => array() ),
			'br'   => array(),
			'a'    => array(
				'class'       => array(),
				'data-target' => array(),
				'href'        => array(),
			),
			'div'  => array(
				'class' => array(),
				'id'    => array(),
			),
			'span' => array( 'class' => array() ),
		);

		return wp_kses( $html, $allowed_html );
	}

	/**
	 * Returns an array of invalid inputs, with suggestions.
	 *
	 * I.e. to display in the admin UI what needs to be addressed.
	 *
	 * @param array{title:string, calendar_id:string, event_period:string, event_count:string, date_format:string} $args
	 *
	 * @return array<string, string> Errors.
	 */
	public function validate_settings( array $args ): array {

		$errors = array();

		// Title can be empty, so only consider it a validation error when a title was supplied.
		if ( ! empty( $args['title'] ) ) {
			$title = wp_strip_all_tags( $args['title'] );

			if ( $title !== $args['title'] ) {
				$errors['title'] = $title;
			}
		}

		$calendar_id = htmlspecialchars( $args['calendar_id'] );

		// TODO: it would be nice to urlfetch this and see is it a valid calendar.
		if ( $calendar_id !== $args['calendar_id'] ) {
			$errors['calendar_id'] = empty( $calendar_id ) ? 'Your icalendar address' : $calendar_id;
		}

		$event_period = abs( intval( $args['event_period'] ) );

		if ( $event_period !== $args['event_period'] ) {
			$errors['event_period'] = empty( $event_period ) ? $this->defaults['event_period'] : $event_period;
		}

		$event_count = abs( intval( $args['event_count'] ) );

		if ( $event_count !== $args['event_count'] ) {
			$errors['event_count'] = empty( $event_count ) ? $this->defaults['event_count'] : $event_count;
		}

		// Using strip_tags because it can start with space or contain more classes seperated by spaces.
		$date_format = wp_strip_all_tags( $args['date_format'] );

		if ( $date_format !== $args['date_format'] ) {
			$errors['date_format'] = empty( $date_format ) ? $this->defaults['date_format'] : $date_format;
		}

		return $errors;
	}
}
