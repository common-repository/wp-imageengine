<?php
/**
 * Plugin Name: WP ImageEngine
 * Plugin URI: http://www.scientiamobile.com/page/WP-ImageEngine
 * Description: WP ImageEngine is an intelligent image CDN for optimizing, compressing and resizing images. ImageEngine will enhance your responsive images by enabling support for HTTP/2, automatic webp conversion and Client Hints. For improved performance and reduced image byte size for mobile devices, WURFL by ScientiaMobile, is used.
 * Version: 1.4.4.1
 * Author: ScientiaMobile
 * Author URI: http://www.scientiamobile.com/about
 * Text Domain: imageengine
 * License: GPL2
 */
if(!defined('ABSPATH')){
	exit;
}

define( 'IMGENG_VERSION', '1.4.3');
define( 'IMGENG_PATH', plugin_dir_path( __FILE__ ) );
define( 'IMGENG_URL', plugin_dir_url( __FILE__ ) );
define( 'IMGENG_LITE_REGISTRATION_URL','https://scientiamobile.com/imageengine/inquiry');
define( 'IMGENG_REGISTRATION_URL','https://scientiamobile.com/imageengine/inquiry');

if( is_admin() ) {
	require( IMGENG_PATH . '/assets.php' ); // Assets for options page
	require( IMGENG_PATH . '/options.php' ); // Admin options page
}
require( IMGENG_PATH . '/functions.php' ); // Mixed functions
