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

if ( ! function_exists('get_no_image_url') )
{
    /**
     * @return string Get public URL to 'no image' image.
     */
    function get_no_image_url()
    {
        return WORDPRESS_HELPERS_URL . 'assets/img/no-image-150.png';
    }
}