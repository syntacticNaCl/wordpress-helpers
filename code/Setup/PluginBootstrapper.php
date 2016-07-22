<?php
namespace Zawntech\WordPress\Setup;

use Zawntech\WordPress\PostsPivot\PostsPivot;
use Zawntech\WordPress\PostsPivot\PostsPivotAjaxHandler;

/**
 * A utility class for installing and bootstrapping the WordPress helper plugin.
 * Class PluginBootstrapper
 * @package Zawntech\WordPress
 */
class PluginBootstrapper
{
    /**
     * Installs plugin assets: posts_pivot table.
     */
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
        // Hook Posts Pivot Ajax functions.
        new PostsPivotAjaxHandler;

        // Do nothing if not administrator screen.
        if ( ! is_admin() ) {
            return;
        }

        // Verify installation.
        $this->verifyInstallation();
    }
}