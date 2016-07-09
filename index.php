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

// Include global helper functions.
require_once __DIR__ . '/code/Functions.php';

/**
 * Define absolute path to plugin directory.
 */
define( 'WORDPRESS_HELPERS_DIR', __DIR__ . '/' );

/**
 * Define public URL to plugin directory.
 */
define( 'WORDPRESS_HELPERS_URL', plugin_dir_url( __FILE__ ) );

// Enqueue admin javascripts.
new \Zawntech\WordPress\AdminScripts;

// Bootstrap the plugin.
new \Zawntech\WordPress\PluginBootstrapper;