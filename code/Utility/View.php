<?php
namespace Zawntech\WordPress\Utility;

use Philo\Blade\Blade;

class View
{
    /**
     * @var array An array of absolute paths to view directories.
     */
    protected static $views = [

    ];

    /**
     * Registers a new view directory.
     * @param $path string Absolute path
     */
    public static function addViewDirectory($path)
    {
        // If the supplied path is NOT in the class's views array.
        if ( ! in_array( $path, static::$views ) )
        {
            // Push the path.
            static::$views[] = $path;
        }
    }

    /**
     * @var static
     */
    protected static $instance;

    /**
     * @var Blade
     */
    protected $blade;

    /**
     * @return View
     */
    protected static function getInstance()
    {
        // Instantiate class.
        if ( null === static::$instance )
        {
            // Set default views path.
            static::$views = [WORDPRESS_HELPERS_DIR . '/views'];

            static::$instance = new static;

            // An array of absolute paths, each containing views.
            $views = static::$views;

            // Path to cache storage.
            $cache = __DIR__ . '/../../storage/view-cache';

            // Instantiate blade.
            static::getInstance()->blade = new Blade($views, $cache);
        }

        // Return singleton class instance
        return static::$instance;
    }

    /**
     * Renders a view.
     * @param $viewName
     * @param array $viewData
     * @return string
     */
    public static function render($viewName, $viewData = [])
    {
        return static::getInstance()
            ->blade
            ->view()
            ->make($viewName, $viewData)
            ->render();
    }
}