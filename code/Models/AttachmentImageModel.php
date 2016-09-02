<?php
namespace Zawntech\WordPress\Models;

/**
 * Class AttachmentImageModel
 * @package Zawntech\WordPress\Models
 */
class AttachmentImageModel
{
    /**
     * @var integer
     */
    public $attachmentId;

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
     * @var string Image title (post_title)
     */
    public $title;

    /**
     * @var string Image caption (post_content)
     */
    public $caption;

    /**
     * @var string Image mime type
     */
    public $mime;

    public $sizes = [];

    public function __construct( $attachmentId )
    {
        // Get attachment ID.
        $this->attachmentId = $attachmentId;

        // Get post data.
        $post = get_post($attachmentId);

        // Set properties.
        $this->title = $post->post_title;
        $this->caption = $post->post_content;
        $this->mime = $post->post_mime_type;

        // Get data.
        $full = wp_get_attachment_image_src( $attachmentId, 'full' );
        $large = wp_get_attachment_image_src( $attachmentId, 'large' );
        $medium = wp_get_attachment_image_src( $attachmentId, 'medium' );
        $thumbnail = wp_get_attachment_image_src( $attachmentId, 'thumbnail' );

        // Assign urls.
        $this->urlFull = $full[0];
        $this->urlLarge = $large[0];
        $this->urlMedium = $medium[0];
        $this->urlThumbnail = $thumbnail[0];

        // Push sizes.
        $this->sizes[] = $full;
        $this->sizes[] = $large;
        $this->sizes[] = $medium;
        $this->sizes[] = $thumbnail;
    }
}