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
        return \Zawntech\WordPress\Utility\View::render($viewName, $viewData);
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

if ( ! function_exists('get_edit_post_url') )
{
    /**
     * Returns an edit post URL for a given post ID.
     * @param $postId
     * @return string
     */
    function get_edit_post_url($postId)
    {
        return admin_url() . 'post.php?post=' . $postId . '&action=edit';
    }
}