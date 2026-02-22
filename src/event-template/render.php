<?php
/**
 * Server-side render for the simple-calendar/event-template block.
 *
 * Simply renders its inner blocks. The parent calendar block provides
 * event context when constructing this block for each event.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner block content.
 * @var WP_Block $block      The block instance.
 *
 * @package brianhenryie/bh-wp-simple-calendar
 */

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'simple-calendar-event-template' ) );

echo '<div ' . $wrapper_attributes . '>' . $content . '</div>';
