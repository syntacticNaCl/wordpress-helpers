<?php
namespace Zawntech\WordPress;

use Zawntech\WordPress\PostsPivot\PostsPivot;

/**
 * A utility class for installing and bootstrapping the WordPress helper plugin.
 * Class PluginBootstrapper
 * @package Zawntech\WordPress
 */
class PluginBootstrapper
{
    public function verifyInstallation()
    {
        // Check if the posts pivot table is installed.
        if ( ! PostsPivot::isInstalled() )
        {
            // Install the posts pivot table.
            PostsPivot::install();
        }
    }

    public function __construct()
    {
        // Do nothing if not administrator.
        if ( ! is_admin() ) {
            return;
        }

        // Verify installation.
        $this->verifyInstallation();
    }
}