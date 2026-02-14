<?php
/**
 * This file represents the HTML output for a single event.
 * It is included by calendar-template.php
 *
 * @var $event BH_WP_Simple_Calendar\ICal\Event;
 */
?>

<li class="ical-event <?php echo esc_attr( strtolower( $event->status ) ); ?>">

	<?php
	if ( ! empty( $event->summary ) ) {
		// TODO: "title" or "summary"?

		if ( isset( $event->url ) ) {
			echo '<a href="' . $event->url . '">';
			echo '<span class="title">' . $event->summary . '</span>';
			echo '</a>';
		} else {
			echo '<span class="title">' . $event->summary . '</span>';
		}
	}
	?>

	<?php

	// If the start and end days are the same, print only that day
	// If the start and end days are different, print both

	// if ( ucfirst( date_i18n( $dflg, $event->start, false ) ) !== $prevdate ) {
	// $prevdate = ucfirst( date_i18n( $dflg, $event->start, false ) );
	// $html    .= $prevdate . '<br/>';
	// }
	//
	// if ( date( 'z', $event->start ) === date( 'z', $event->end ) ) {
	// $html .= date_i18n( 'G:i ', $event->start, false );
	// }

	// HP Notice:  Trying to get property 'TZID' of non-object in /home/saba_dev/stagingsaba/wp-content/plugins/bh-wp-simple-calendar/Frontend/partials/calendar-item-template-1.php on line 42

	if ( ! is_object( $event->dtstart_array[0] ) ) {
		// error_log( json_encode( $event->dtstart_array ) );
	}

//	$timezone  = $event->dtstart_array[0]['TZID'];
	$timestamp = $event->dtstart_array[2];


	/** @var \DateTime $start_time */
	$start_time = $event->start_time;

//	$start_time = new DateTimeImmutable( $start_time , wp_timezone());
	$start_time = DateTimeImmutable::createFromFormat('U', $timestamp)->setTimezone( wp_timezone() );

	$event_date_string = $start_time->format( 'l F j, H:i' );



	if ( isset( $event->url ) ) {
		echo '<a href="' . $event->url . '">';
		echo '<span class="date">' . $event_date_string . '</span>';
		echo '</a>';
	} else {
		echo '<span class="date">' . $event_date_string . '</span>';
	}
	?>


<?php

//
// classes
//
// title
// date/time
// link
// description
// location
// repeating?


?>

</li>
