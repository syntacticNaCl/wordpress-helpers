<?php
namespace Zawntech\WordPress\MetaBoxes;

use Zawntech\WordPress\PostMetaAttachments\PostMetaAttachmentQuery;

/**
 * A metabox for attaching media or custom URLs to a single meta key.
 * Class AttachmentMetaBox
 * @package Zawntech\WordPress\MetaBoxes
 */
class AttachmentMetaBox extends MetaBoxAbstract
{
    /**
     * @var string A specified meta key for relating this attachment.
     */
    protected $metaKey;

    protected function setViewModels()
    {
        $this->viewModel = [
            WORDPRESS_HELPERS_URL . 'assets/js/view-models/attachment-meta-box/custom-url-attachments-view-model.js',
            WORDPRESS_HELPERS_URL . 'assets/js/view-models/attachment-meta-box/wordpress-media-attachments-view-model.js',
            WORDPRESS_HELPERS_URL . 'assets/js/view-models/attachment-meta-box-view-model.js'
        ];
    }

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
        return PostMetaAttachmentQuery::getAttachmentType($postId, $this->metaKey);
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
                'attachmentPreload' => PostMetaAttachmentQuery::getAttachmentModel($post->ID, $this->metaKey),
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
            $type = $_POST[$this->metaKey . '_type'];

            // Set attachments.
            PostMetaAttachmentQuery::setAttachments($postId, $this->metaKey, $value, $type);
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