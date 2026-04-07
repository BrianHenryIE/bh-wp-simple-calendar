<?php
/**
 * Server-side render for simple-calendar/event-date.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner block content.
 * @var WP_Block $block      The block instance.
 *
 * @package brianhenryie/bh-wp-simple-calendar
 */

use BrianHenryIE\WP_Simple_Calendar\Frontend\Event_Field_Renderer;

echo Event_Field_Renderer::render( $block, $attributes, 'date', 'time' );
