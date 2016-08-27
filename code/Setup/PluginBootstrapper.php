<?php
namespace Zawntech\WordPress\Setup;

use Zawntech\WordPress\IO\IOManager;
use Zawntech\WordPress\PostsPivot\PostsPivot;
use Zawntech\WordPress\PostsPivot\PostsPivotAjaxHandler;
use Zawntech\WordPress\PostsPivot\PostsPivotHooks;
use Zawntech\WordPress\Utility\View;

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

    /**
     * Register the default views path.
     */
    public function registerViewsPath()
    {
        // Register the directory.
        View::addViewDirectory( WORDPRESS_HELPERS_DIR . '/views' );
    }

    public function __construct()
    {
        $this->registerViewsPath();

        // Load IO Manager.
        new IOManager;

        // Hook posts pivot.
        new PostsPivotHooks;

        // Load posts pivoter functionality by instantiating their respective classes.
        new PostsPivotAjaxHandler;

        // Do nothing if not administrator screen.
        if ( ! is_admin() ) {
            return;
        }

        // Verify installation.
        $this->verifyInstallation();
    }
}