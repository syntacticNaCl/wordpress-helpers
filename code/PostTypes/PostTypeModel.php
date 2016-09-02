<?php
namespace Zawntech\WordPress\PostTypes;

use Zawntech\WordPress\Models\FeaturedImageModel;

abstract class PostTypeModel
{
    /**
     * @var array
     */
    protected $options = [
        'loadFeaturedImage' => true,
        'loadMeta' => true
    ];

    /**
     * @var 
     */
    protected $metaClass;

    public $postId;
    public $title;
    public $slug;

    /**
     * @var PostMeta
     */
    public $meta;

    /**
     * @var FeaturedImageModel
     */
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
        if ( $this->options['loadFeaturedImage'] )
        {
            $this->featuredImage = new FeaturedImageModel( $postId );
        }

        // Autoload meta.
        if ( $this->options['loadMeta'] )
        {
            // Verify that a class defintion is provided.
            if ( ! $this->metaClass )
            {
                throw new \Exception('No PostMeta class defined in class.');
            }

            // Reference class name.
            $className = $this->metaClass;

            // Instantiate post meta.
            $this->meta = new $className($postId);
        }
    }   
}