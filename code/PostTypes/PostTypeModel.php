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
     * @var string The extending meta class to instantiate for this model.
     */
    protected $metaClass;

    /**
     * @var int The post ID.
     */
    public $postId;

    /**
     * @var string post_title
     */
    public $title;

    /**
     * @var string post_name
     */
    public $slug;

    /**
     * @var PostMeta
     */
    public $meta;

    /**
     * @var string Filtered post_content.
     */
    public $content;

    /**
     * @var string Prefiltered post_content.
     */
    public $contentRaw;

    /**
     * @var FeaturedImageModel
     */
    public $featuredImage;

    /**
     * @var \WP_Post
     */
    public $post;

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

        // Assign the core WP_Post internally and post ID.
        $this->post = $post;
        $this->postId = $postId;

        // Set model properties.
        $this->title = $post->post_title;
        $this->slug = $post->post_name;
        $this->content = apply_filters('the_content', $post->post_content);
        $this->contentRaw = $post->post_content;
        $this->permalink = get_permalink( $post );

        // If the extending models are configured to load featured images.
        if ( $this->options['loadFeaturedImage'] )
        {
            $this->featuredImage = new FeaturedImageModel( $postId );
        }

        // Autoload meta.
        if ( $this->options['loadMeta'] )
        {
            // Verify that a meta class definition is provided.
            if ( ! $this->metaClass )
            {
                // Reference the extending post type model.
                $staticClassName = static::class;
                throw new \Exception("No PostMeta class defined in class {$staticClassName}.");
            }

            // Reference class name.
            $className = $this->metaClass;

            // Instantiate post meta.
            $this->meta = new $className($postId);
        }
    }   
}