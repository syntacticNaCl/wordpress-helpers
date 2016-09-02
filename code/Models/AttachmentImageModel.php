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

        // Get image urls.
        $this->urlFull = wp_get_attachment_image_src( $attachmentId, 'full' );
        $this->urlLarge = wp_get_attachment_image_src( $attachmentId, 'large' );
        $this->urlMedium = wp_get_attachment_image_src( $attachmentId, 'medium' );
        $this->urlThumbnail = wp_get_attachment_image_src( $attachmentId, 'thumbnail' );
    }
}