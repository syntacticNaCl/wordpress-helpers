<?php
namespace Zawntech\WordPress\MetaBoxes;

/**
 * A metabox for attaching media or custom URLs to a single meta key.
 * Class AttachmentMetaBox
 * @package Zawntech\WordPress\MetaBoxes
 */
class AttachmentMetaBox extends MetaBoxInterface
{
    /**
     * @var string A specified meta key for relating this attachment.
     */
    protected $metaKey;

    /**
     * @var string Public URL to posts pivoter view model javascript.
     */
    protected $viewModel = [
        WORDPRESS_HELPERS_URL . 'assets/js/view-models/attachment-meta-box/custom-url-attachments-view-model.js',
        WORDPRESS_HELPERS_URL . 'assets/js/view-models/attachment-meta-box/wordpress-media-attachments-view-model.js',
        WORDPRESS_HELPERS_URL . 'assets/js/view-models/attachment-meta-box-view-model.js'
    ];

    /**
     * @var bool
     */
    protected $multipleAttachments = true;

    /**
     * @var string The type of attachment metabox default ('wp' or 'url').
     */
    protected $defaultType = 'wp';

    /**
     * @var string Attachment types, ie: 'image'
     */
    protected $attachmentType = '';

    /**
     * @var string
     */
    protected $attachmentButtonText = 'Set attachment';

    /**
     * @param $postId
     * @return mixed|string
     */
    protected function getAttachmentSource($postId)
    {
        $meta = get_post_meta($postId, $this->metaKey . '_type', true);
        return false === $meta ? 'wp' : $meta;
    }

    /**
     * Returns the prepared attachment preload array, containing Post IDs, thumbnails, etc.
     * @param $postId
     * @return array
     */
    protected function getAttachmentPreload($postId)
    {
        // Get post meta.
        $meta = get_post_meta($postId, $this->metaKey, true);

        // Get attachment source.
        $attachmentSource = $this->getAttachmentSource($postId);

        if ( 'url' === $attachmentSource ) {
            return json_decode($meta);
        }

        // There are no attached IDs, or this is a URL type, so return an empty array.
        if ( '' === $meta )
        {
            return [];
        }

        // If a comma is found in the $meta string, then split by comma, otherwise
        // cast the returned meta as an array.
        $postIds = false === strpos($meta, ',') ? [$meta] : explode(',', $meta);

        // Declare an array for output.
        $data = [];

        // Loop through the post Ids
        foreach( $postIds as $postID )
        {
            $postData = get_post($postID);
            $attachmentData = wp_get_attachment_metadata($postID);

            $newItem = [
                'id' => $postID,
                'title' => $postData->post_title,
                'meta' => $attachmentData,
                'filename' => basename( get_attached_file( $postID ) )
            ];

            // Get thumbnail url.
            $thumb = wp_get_attachment_thumb_url($postID);

            // Push thumbnail.
            if ( false != $thumb )
            {
                $newItem['sizes'] = [
                    'thumbnail' => [
                        'url' => wp_get_attachment_thumb_url($postID)
                    ]
                ];
            }

            // Push data.
            $data[] = $newItem;
        }

        return $data;
    }

    public function render(\WP_Post $post)
    {
        $attachmentSource = get_post_meta($post->ID, $this->metaKey . '_type', true);

        echo view('admin.meta-boxes.attachment-meta-box', [

            // Prepare an options object to be passed to the
            // PostsPivoterViewModel constructor in the view.
            'options' => [
                'elementId' => $this->id,
                'postId' => (integer) $post->ID,
                'multiple' => $this->multipleAttachments,
                'attachmentType' => $this->attachmentType,
                'attachmentButtonText' => $this->attachmentButtonText,
                'attachmentPreload' => $this->getAttachmentPreload($post->ID),
                'type' => $attachmentSource ?: $this->defaultType
            ],

            'metaKey' => $this->metaKey
        ]);
    }

    protected function validateClass()
    {
        parent::validateClass();

        // Class name.
        $class = static::class;

        // Verify that a post type is specified (ie, which post types this metabox
        // should be hook).
        if ( empty( $this->postTypes ) )
        {
            throw new \Exception("No \$postTypes specified in class {$class}.");
        }

        // A meta key is required.
        if ( ! $this->metaKey )
        {
            throw new \Exception("No metaKey is specified in class {$class}");
        }
    }

    /**
     * @param $postId integer
     */
    public function save($postId)
    {
        if ( ! isset( $_POST[$this->getNonceName()] ) || ! $this->verifyNonce( $_POST[$this->getNonceName()] ) )
        {
            return;
        }
        
        if ( isset( $_POST[$this->metaKey] ) )
        {
            // Get value.
            $value = $_POST[$this->metaKey];
            $source = $_POST[$this->metaKey . '_type'];

            // Set the value.
            update_post_meta($postId, $this->metaKey, $value);
            update_post_meta($postId, $this->metaKey . '_type', $source);
        }
    }

    public function __construct()
    {
        // Verify the extending class is correctly defined.
        $this->validateClass();

        // Register the meta box (hooks to post type defined in $this->postType).
        add_action( 'add_meta_boxes', [$this, 'register'] );

        // Save post.
        add_action( 'save_post', [$this, 'save'] );
    }
}