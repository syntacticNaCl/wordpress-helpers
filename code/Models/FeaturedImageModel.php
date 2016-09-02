<?php
namespace Zawntech\WordPress\Models;

/**
 * Autoloads a post's featured image data (url to full, thumbnail sizes).
 * Class FeaturedImageModel
 * @package Zawntech\WordPress\Models
 */
class FeaturedImageModel
{
    /**
     * @var array Defines options for how the featured image model should behave.
     */
    protected $options = [

        // The default URL to return when the object is cast as a string.
        'defaultUrl' => 'urlFull'
    ];

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
     * @var false|string Public URL to 'medium' size.
     */
    public $urlMedium;

    /**
     * @var false|string Public URL to 'large' size.
     */
    public $urlLarge;

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
     * Returns urlFull or ''.
     * @return string
     */
    public function __toString()
    {
        // No featured image.
        if ( $this->hasFeaturedImage() ) {
            return '';
        }

        // Get the default url key.
        $urlKey = $this->options['defaultUrl'];

        // Return the url property.
        return $this->{$urlKey};
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
        $this->urlLarge = get_the_post_thumbnail_url( $postId, 'large' );
        $this->urlMedium = get_the_post_thumbnail_url( $postId, 'medium' );
        $this->urlThumbnail = get_the_post_thumbnail_url( $postId, 'thumbnail' );
    }
}