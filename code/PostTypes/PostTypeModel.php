<?php
namespace Zawntech\WordPress\PostTypes;

abstract class PostTypeModel
{
    public $postId;
    public $title;
    public $slug;

    /**
     * PostTypeModel constructor.
     * @param $postId
     */
    public function __construct($postId)
    {
        // Get post.
        $post = get_post($postId);

        // No post?
        if ( null === $post ) {
            return;
        }

        // Set post ID.
        $this->postId = $postId;

        // Set name.
        $this->title = $post->post_title;
        $this->slug = $post->post_name;
    }
}