<?php
/**
 * Hidden admin menu for displaying documentation.
 *
 * @since 0.7
 *
 * @package    bh-wp-simple-calendar
 *
 * @author     Bram Waasdorp <bram@waasdorpsoekhan.nl>
 * @copyright  Copyright (c)  2017 -2019, Bram Waasdorp
 * @link       https://github.com/bramwaas/wordpress-plugin-wsa-simple-google-calendar-widget
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace BrianHenryIE\WP_Simple_Calendar\Admin;

/**
 * Documentation_Page
 */
class Documentation_Page {

	/**
	 * Back-end sub menu item to display widget help page.
	 *
	 * There is no main menu item for simple_ical.
	 * This submenu is HIDDEN, however, we need to add it to create a page in the admin area.
	 *
	 * @hooked admin_menu
	 */
	public function add_submenu() {

		add_submenu_page(
			null,
			__( 'Info', 'simple_ical' ),
			__( 'Info', 'simple_ical' ),
			'read',
			'simple_ical_info',
			array( $this, 'echo_info_html' )
		);
	}

	/**
	 * Print the instructions (translatable).
	 */
	public function echo_info_html() {

		$html = '';

		$html .= '<div class="wrap">';

		$html .= sprintf( '<h2>%s</h2>', __( 'Info on Simple Google iCal Calendar Widget', 'simple_ical' ) );
		$html .= sprintf( '<p>%s</p>', __( 'Arguments for this widget:', 'simple_ical' ) );

		$html .= sprintf( '<p><strong>%s</strong></p>', __( 'Title', 'simple_ical' ) );
		$html .= sprintf( '<p>%s</p>', __( 'Title of this instance of the widget', 'simple_ical' ) );

		$html .= sprintf( '<p><strong>%s</strong></p>', __( 'Calendar ID, or iCal URL', 'simple_ical' ) );
		$html .= sprintf( '<p>%s</p>', __( 'The Google calendar ID, or the URL of te iCal file to display.</>', 'simple_ical' ) );

		$html .= sprintf( '<p><strong>%s</strong></p>', __( 'Number of events displayed', 'simple_ical' ) );
		$html .= sprintf( '<p>%s</p>', __( 'The maximum number of events to display.', 'simple_ical' ) );

		$html .= sprintf( '<p><strong>%s</strong></p>', __( 'Number of days after today with events displayed', 'simple_ical' ) );
		$html .= sprintf( '<p>%s</p>', __( 'Last date to display events in number of days after today.', 'simple_ical' ) );

		$html .= sprintf( '<p><strong>%s</strong></p>', __( 'Cache expiration time in minutes', 'simple_ical' ) );
		$html .= sprintf( '<p>%s</p>', __( 'Minimal time in minutes between reads from source.', 'simple_ical' ) );

		$html .= sprintf( '<p><strong>%s</strong></p>', __( 'Date format first line', 'simple_ical' ) );
		$html .= sprintf(
			'<p>%s<br/>%s<br/>%s</p>',
			__( 'Date format first line default: l jS \of F,', 'simple_ical' ),
			__( 'l = day of the week (Monday); j =  day of the month (25) F = name of month (december)', 'simple_ical' ),
			__( 'y or Y = Year (17 or 2017); see also php.net/manual/en/function.date.php .', 'simple_ical' )
		);

		$html .= sprintf( '<p><strong>%s</strong></p>', __( 'Suffix group class', 'simple_ical' ) );
		$html .= sprintf(
			'<p>%s,<br/>%s</p>',
			__( 'Suffix to add after css-class around the event (list-group)', 'simple_ical' ),
			__( 'start with space to keep the original class and add another class.', 'simple_ical' )
		);

		$html .= sprintf( '<p><strong>%s</strong></p>', __( 'Suffix event start class', 'simple_ical' ) );
		$html .= sprintf(
			'<p>%s,<br/>%s<br/>%s</p>',
			__( 'Suffix to add after the css-class around the event start line (list-group-item)', 'simple_ical' ),
			__( 'start with space to keep the original class and add another class.', 'simple_ical' ),
			__( 'E.g.:  py-0 with leading space; standard bootstrap 4 class to set padding top and bottom  to 0;  ml-1 to set margin left to 0.25 rem', 'simple_ical' )
		);

		$html .= sprintf( '<p><strong>%s</strong></p>', __( 'Suffix event details classs', 'simple_ical' ) );
		$html .= sprintf(
			'<p>%s,<br/>%s</p>',
			__( 'Suffix to add after the css-class around the event details link (ical_details),', 'simple_ical' ),
			__( 'start with space to keep the original class and add another class.', 'simple_ical' )
		);

		$html .= '</div>';

		$allowed_html = array(
			'h2'     => array(),
			'p'      => array(),
			'strong' => array(),
			'br'     => array(),
		);

		echo wp_kses( $html, $allowed_html );
	}
}



