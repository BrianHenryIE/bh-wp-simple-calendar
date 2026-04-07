<?php
/**
 * PHPUnit bootstrap file for WP_Mock.
 *
 * @package brianhenryie/bh-wp-simple-calendar
 */

WP_Mock::setUsePatchwork( true );
WP_Mock::bootstrap();

global $plugin_root_dir;
require_once $plugin_root_dir . '/autoload.php';

require_once codecept_root_dir( 'wordpress/wp-includes/class-wp-error.php' );

define( 'DAY_IN_SECONDS', 86400 );
require_once codecept_root_dir( 'wordpress/wp-includes/class-wp-http-response.php' );
require_once codecept_root_dir( 'wordpress/wp-includes/rest-api/class-wp-rest-response.php' );
require_once codecept_root_dir( 'wordpress/wp-includes/rest-api/class-wp-rest-request.php' );
