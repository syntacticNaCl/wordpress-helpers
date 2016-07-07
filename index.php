<?php
/**
 * Hail Wanzwa
 */

/*
Plugin Name: WordPress Helpers by Zawntech
Plugin URI: http://zawntech.com
Description: Juice up your WordPress instance.
Author: Marty Eason
Version: 0.0.1
Author URI: http://zawntech.com
*/

// Declare autoload path.
$autoloadPath = __DIR__ . '/vendor/autoload.php';

// Verify that the autoloader exists.
if ( ! is_file($autoloadPath) )
{
    throw new \Exception('Composer assets not installed.');
}

// Require composer assets.
require_once $autoloadPath;

/**
 * Define absolute path to plugin directory.
 */
define( 'WORDPRESS_HELPERS_DIR', __DIR__ . '/' );

/**
 * Define public URL to plugin directory.
 */
define( 'WORDPRESS_HELPERS_URL', plugin_dir_url( __FILE__ ) );

// Activate plugin function.
register_activation_hook( __FILE__ , function() {

    // Install the PostsPivot structure.
    \Zawntech\WordPress\PostsPivot\PostsPivot::install();
});