<?php
/**
 * The widget to display the calendar data in a sidebar, and its admin UI.
 *
 * @since      0.1
 *
 * @package    bh-wp-simple-calendar
 */

namespace BrianHenryIE\WP_Simple_Calendar\Frontend;

/**
 * Class Widget
 *
 * @package BH_WP_Simple_Calendar\Frontend
 */
class Widget extends \WP_Widget {

	/**
	 * `id_base` and `classname` preserved for backward compatibility.
	 * TODO: Unsure if classname is used anywhere. id_base can be find+replaced during install.
	 *
	 * Widget constructor.
	 */
	public function __construct(
		protected Renderer $renderer,
	) {

		$id_base = 'simple_ical_widget';

		$name = 'Simple Google iCalendar Widget';

		$widget_options = array(
			'classname'   => 'Simple_iCal_Widget',
			'description' => __( 'Displays events from a public Google Calendar or other iCal source', 'bh_wp_simple_calendar' ),
		);

		parent::__construct( $id_base, $name, $widget_options );

		$this->renderer = $renderer;
	}



	/**
	 * Front-end display of widget.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 *
	 * @see WP_Widget::widget()
	 */
	public function widget( $args, $instance ) {

		$html = '';

		$html .= $args['before_widget'];

		if ( isset( $instance['title'] ) ) {

			$title = apply_filters( 'widget_title', $instance['title'], $instance );
			$html .= $args['before_title'] . $title . $args['after_title'];
		}

		echo $html;

		$formatting = array();

		$formatting['dflg'] = ( isset( $instance['date_format'] ) ) ? $instance['date_format'] : 'l jS \of F';

		/** @var Parsed_Event[] $data */
		$data = $this->get_data( $instance );

		$html = $this->api->render( $data, $formatting );

		echo $html;

		echo '<br class="clear" />';
		echo $args['after_widget'];
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 * @see WP_Widget::update()
	 */
	public function update( $new_instance, $old_instance ) {

		$instance          = $old_instance;
		$instance['title'] = wp_strip_all_tags( $new_instance['title'] );

		$instance['calendar_id'] = htmlspecialchars( $new_instance['calendar_id'] );

		$instance['event_period'] = $new_instance['event_period'];
		if ( is_numeric( $new_instance['event_period'] ) && $new_instance['event_period'] > 1 ) {
			$instance['event_period'] = $new_instance['event_period'];
		} else {
			$instance['event_period'] = 366;
		}

		$instance['event_count'] = $new_instance['event_count'];
		if ( is_numeric( $new_instance['event_count'] ) && $new_instance['event_count'] > 1 ) {
			$instance['event_count'] = $new_instance['event_count'];
		} else {
			$instance['event_count'] = 5;
		}
		// Using strip_tags because it can start with space or contain more classe seperated by spaces.
		$instance['date_format'] = wp_strip_all_tags( $new_instance['date_format'] );

		return $instance;
	}

	/**
	 * Outputs the settings update form.
	 *
	 * @see WP_Widget::form()
	 * @param array $instance Previously saved values from database.
	 * @return string
	 */
	public function form( $instance ) {

		$defaults = $this->api->get_defaults();

		$instance = wp_parse_args( (array) $instance, $defaults );

		$instance = $this->api->validate_input( $instance );

		$html = '';

		$var   = 'title';
		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id( 'title' ) . '">' . __( 'Title:', 'simple_ical' ) . '</label>';
		$html .= '<input class="widefat" id="' . $this->get_field_id( 'title' ) . '" ';
		$html .= ' name="' . $this->get_field_name( 'title' ) . '" type="text" value="' . esc_attr( $instance['title'] ) . '"/>';
		$html .= '</p>';

		$var   = 'calendar_id';
		$html .= '<p>';
		$html .= ' <label for="' . $this->get_field_id( 'calendar_id' ) . '">' . __( 'Calendar ID, or iCal URL:', 'simple_ical' ) . '</label>';
		$html .= ' <input class="widefat" id="' . $this->get_field_id( 'calendar_id' ) . '"';
		$html .= ' name="' . $this->get_field_name( 'calendar_id' ) . '" type="text"';
		$html .= ' value="' . esc_attr( $instance['calendar_id'] ) . '"/>';
		$html .= ' </p>';

		$var   = 'event_count';
		$html .= ' <p>';
		$html .= ' <label for="' . $this->get_field_id( 'event_count' ) . '">' . __( 'Number of events displayed:', 'simple_ical' ) . '</label>';
		$html .= ' <input class="widefat" id="' . $this->get_field_id( 'event_count' ) . '"';
		$html .= ' name="' . $this->get_field_name( 'event_count' ) . '" type="text"';
		$html .= ' value="' . esc_attr( $instance['event_count'] ) . '"/>';
		$html .= ' </p>';

		$car   = 'event_period';
		$html .= ' <p>';
		$html .= ' <label for="' . $this->get_field_id( 'event_period' ) . '">' . __( 'Number of days after today with events displayed:', 'simple_ical' ) . '</label>';
		$html .= ' <input class="widefat" id="' . $this->get_field_id( 'event_period' ) . '"';
		$html .= ' name="' . $this->get_field_name( 'event_period' ) . '" type="text"';
		$html .= ' value="' . esc_attr( $instance['event_period'] ) . '"/>';
		$html .= ' </p>';

		$var   = 'dateformat';
		$html .= ' <p>';
		$html .= ' <label for="' . $this->get_field_id( $var ) . '">' . __( 'Date format first line:', 'simple_ical' ) . '</label>';
		$html .= ' <input class="widefat" id="' . $this->get_field_id( $var ) . '"';
		$html .= ' name="' . $this->get_field_name( $var ) . '" type="text"';
		$html .= ' value="' . esc_attr( $instance[ $var ] ) . '"/>';
		$html .= ' </p>';

		// TODO: Try to link to an anchor on the WordPress.org page.
		$html .= ' <p>';
		$html .= '<a href="' . admin_url( 'admin.php?page=simple_ical_info' ) . '" target="_blank">';
		$html .= __( 'Need help?', 'simple_ical' );
		$html .= '</a>';
		$html .= '</p>';

		$allowed_html = array(
			'p'     => array(),
			'label' => array( 'for' => array() ),
			'input' => array(
				'class' => array(),
				'id'    => array(),
				'name'  => array(),
				'type'  => array(),
				'value' => array(),
			),
			'a'     => array(
				'href'   => array(),
				'target' => array(),
			),
		);

		echo wp_kses( $html, $allowed_html );

		return '';
	}

}
