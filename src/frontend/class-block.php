<?php
/**
 * Functionality to register block.
 *
 * i.e. With Gutenberg.
 */

namespace BrianHenryIE\WP_Simple_Calendar\Frontend;

use BrianHenryIE\WP_Simple_Calendar\Settings_Interface;

/**
 * Class Block
 *
 * @package BH_WP_Simple_Calendar\Frontend
 */
class Block {

	public function __construct(
		protected Settings_Interface $settings,
		protected Renderer $renderer,
	) {
	}

	/**
	 * @hooked init
	 */
	public function register_block() {

		$script_handle = $this->settings->get_plugin_slug() . '-block-editor-script';
		$src           = plugins_url( 'assets/calendar.js', $this->settings->get_plugin_basename() );
		$deps          = array( 'wp-blocks', 'wp-element', 'wp-data', 'wp-editor' );

		wp_register_script(
			$script_handle,
			$src,
			$deps,
			$this->settings->get_version(),
			array( 'in_footer' => true )
		);

		// TODO: make this a constant.
		$name       = 'brianhenryie/simple-calendar';
		$attributes = array(
			'calendarId'  => array(
				'type' => 'string',
			),
			'eventCount'  => array(
				'type'    => 'integer',
				'default' => 10,
			),
			'eventPeriod' => array(
				'type'    => 'integer',
				'default' => 92,
			),
			'dateFormat'  => array(
				'type'    => 'string',
				'default' => 'l jS \of F',
			),
		);
		$args       = array(
			'editor_script'   => $script_handle,
			'render_callback' => array( $this, 'render_block' ),
			'attributes'      => $attributes,
		);

		register_block_type( $name, $args );
	}

	/**
	 * The method used by WordPress to render the Block.
	 *
	 * @param $js_args
	 *
	 * @return string
	 */
	public function render_block( $js_args ) {

		// The array passed from javascript is in camelCase, convert to snake_case.
		$args = array();
		foreach ( $js_args as $key => $value ) {
			$key          = strtolower( preg_replace( '/([a-z])([A-Z])/', '$1_$2', $key ) );
			$args[ $key ] = $value;
		}

		// TODO validate

		$validation_errors = $this->renderer->validate_settings( $args );

		// TODO: Make sure this is only shown in the editing UI.
		if ( ! empty( $validation_errors ) ) {

			if ( ! is_admin() ) {
				// Don't print anything on the frontend when there's a problem.
				return null;
			}

			$html  = 'The following properties are required:';
			$html .= '<ul>';

			foreach ( $validation_errors as $error => $suggestion ) {
				$html .= "<li><b>$error</b> <i>$suggestion</i></li>";
			}

			$html .= '</ul>';

			return $html;
		}

		// {
		// "calendarId": "https:\/\/calendar.google.com\/calendar\/ical\/3bpg24atqjbsmhdb00ilcdrj5c%40group.calendar.google.com\/public\/basic.ics",
		// "eventCount": 10,
		// "eventPeriod": 92,
		// "dateFormat": "l jS \\of F"
		// }

		$calendar_id = $args['calendar_id'];
		$count       = $args['event_count'];
		$period      = $args['event_period'];

		// TODO: Should the page/widget the calendar is on be passed here?

		$html = $this->renderer->get_html( $calendar_id, $period, $count );

		return $html;
	}
}
