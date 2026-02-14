<?php
/**
 * This file represents the HTML output for a single event.
 * It is included by calendar-template-1.php.
 *
 * @var \BrianHenryIE\WP_Simple_Calendar\API\Calendar_Event $event
 */
?>

<li class="ical-event <?php echo esc_attr( strtolower( $event->status ) ); ?>">

	<?php
	if ( ! empty( $event->summary ) ) {

		if ( isset( $event->url ) ) {
			echo '<a href="' . esc_url( $event->url ) . '">';
			echo '<span class="title">' . esc_html( $event->summary ) . '</span>';
			echo '</a>';
		} else {
			echo '<span class="title">' . esc_html( $event->summary ) . '</span>';
		}
	}
	?>

	<?php
	$event_date_string = $event->start_time->format( 'l F j, H:i' );

	if ( isset( $event->url ) ) {
		echo '<a href="' . esc_url( $event->url ) . '">';
		echo '<span class="date">' . esc_html( $event_date_string ) . '</span>';
		echo '</a>';
	} else {
		echo '<span class="date">' . esc_html( $event_date_string ) . '</span>';
	}
	?>

</li>
