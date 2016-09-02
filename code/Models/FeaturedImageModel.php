<?php
namespace Zawntech\WordPress\Models;

class FeaturedImageModel
{
    /**
     * @var int The post ID passed through the constructor.
     */
    public $postId;

    /**
     * @var int|string The featured image post ID.
     */
    public $featuredImageId;

    /**
     * @var false|string Public URL to full size
     */
    public $urlFull;

    /**
     * @var false|string Public URL to 'post-thumbnail' size.
     */
    public $urlThumbnail;

    /**
     * @var bool
     */
    protected $hasFeaturedImage = false;

    /**
     * @return bool
     */
    public function hasFeaturedImage()
    {
        return $this->hasFeaturedImage;
    }

    /**
     * Get featured image data for a given post ID.
     * FeaturedImageModel constructor.
     * @param $postId
     */
    public function __construct($postId)
    {
        // Assign the post ID internally.
        $this->postId = $postId;

        // Get post thumbnail ID.
        $thumbnailId = get_post_thumbnail_id( $postId );

        // No thumbnail ID for this post ID.
        if ( '' === $thumbnailId ) {
            return;
        }

        // Assign the featured image ID.
        $this->featuredImageId = $thumbnailId;
        $this->hasFeaturedImage = true;

        // Assign the URL.
        $this->urlFull = get_the_post_thumbnail_url( $postId, 'full' );
        $this->urlThumbnail = get_the_post_thumbnail_url( $postId, 'thumbnail' );
    }
}