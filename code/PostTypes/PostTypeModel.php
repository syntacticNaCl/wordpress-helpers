<?php
namespace Zawntech\WordPress\PostTypes;

use Zawntech\WordPress\Models\FeaturedImageModel;

abstract class PostTypeModel
{
    /**
     * @var array
     */
    protected $options = [
        'loadFeaturedImage' => true
    ];

    public $postId;
    public $title;
    public $slug;
    public $featuredImage;

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

        // If the extending models are configured to load featured images.
        if ( $this->options['loadFeatureImage'] )
        {
            $this->featuredImage = new FeaturedImageModel( $postId );
        }
    }
}