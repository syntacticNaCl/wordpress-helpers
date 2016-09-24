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

if ( ! function_exists( 'ajax_url' ) )
{
    /**
     * Generate an ajax url for a given action.
     * @param $action
     * @param array $arguments
     * @return string
     */
    function ajax_url($action, $arguments = [])
    {
        // Prepare the base URL.
        $url = admin_url() . "admin-ajax.php?action={$action}";

        // If arguments were attached, append them to URL query.
        if ( ! empty( $arguments ) )
        {
            $url .= '&' . http_build_query( $arguments );
        }

        return $url;
    }
}