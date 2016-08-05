<?php
namespace Zawntech\WordPress\PostsPivot;

class PostsPivotHooks
{
    /**
     * On 'delete_post', remove instances of the given post ID from
     * the posts pivot table.
     */
    public function deletePost()
    {
        add_action( 'delete_post', function($postId) {
            PostsPivot::delete($postId);
        });
    }

    public function __construct()
    {
        $this->deletePost();
    }
}