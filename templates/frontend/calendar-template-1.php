<?php
/**
 * This is the default calendar template used by the plugin.
 * It is included by class-renderer.php.
 *
 * TODO Allow overriding via WordPress's get_template function.
 *
 * @var $events BH_WP_Simple_Calendar\ICal\Event[];
 */
?>

<style>
	ul.calendar-list {
		list-style: none;
		margin-left: 0;
		font-family: "Inter var", -apple-system, BlinkMacSystemFont, "Helvetica Neue", Helvetica, sans-serif;

	}

	li.ical-event{
		margin-top: 5px;
		margin-bottom: 10px;
		margin-left:0;
		clear:both;
		padding-bottom: 10px;
	}

	li.ical-event .title {
		line-height: 1.2;
		font-size: 1.2em;
		font-weight: 500;
		color: #3f2414;
	}

	li.ical-event a {
		text-decoration: none;
	}

	li.ical-event.postponed {

		text-decoration: line-through;
	}

	li.ical-event.postponed a .date {

		text-decoration: line-through;
	}


	li.ical-event .date {
		font-family: Georgia, "Times New Roman", Times, serif;
		line-height: 23px;
		white-space: nowrap;
		float:right;
		opacity: 0.5;
		font-size: 0.9em;
		color:  rgb(63, 36, 20);
	}


</style>

<ul class="calendar-list">
<?php

foreach ( $events as $event ) {

	include __DIR__ . '/calendar-item-template-1.php';

}
?>
</ul>

