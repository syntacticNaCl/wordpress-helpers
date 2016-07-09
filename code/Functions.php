<?php
/**
 * Global helper functions
 */

// View helper.
// view($name, $data);
if ( ! function_exists('view') )
{
    /**
     * @param $viewName
     * @param array $viewData
     * @return string
     */
    function view($viewName, $viewData = [])
    {
        return \Zawntech\WordPress\View::render($viewName, $viewData);
    }
}