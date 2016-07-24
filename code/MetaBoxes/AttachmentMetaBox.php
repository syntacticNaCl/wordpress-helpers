<?php
namespace Zawntech\WordPress\MetaBoxes;

class AttachmentMetaBox extends MetaBoxInterface
{
    /**
     * @var string A specified meta key for relating this attachment.
     */
    protected $metaKey;

    /**
     * @var string Public URL to posts pivoter view model javascript.
     */
    protected $viewModel = WORDPRESS_HELPERS_URL . 'assets/js/view-models/attachment-meta-box-view-model.js';

    /**
     * @var bool
     */
    protected $multipleAttachments = true;

    /**
     * @var string
     */
    protected $attachmentType = 'image';

    /**
     * @var string
     */
    protected $attachmentButtonText = 'Set attachment';

    protected function getAttachmentPreload($postId)
    {
        // Get post meta.
        $meta = trim( get_post_meta($postId, $this->metaKey, true) );

        if ( false === strpos($meta, ',') && '' == $meta )
        {
            return [];
        }

        // Split by comma.
        $postIds = explode(',', $meta);

        // Declare an array for output.
        $data = [];

        foreach( $postIds as $postID )
        {
            $data[] = [
                'id' => $postID,
                'sizes' => [
                    'thumbnail' => [
                        'url' => wp_get_attachment_thumb_url($postID)
                    ]
                ]
            ];
        }

        return $data;
    }

    public function render($post)
    {
        echo view('admin.meta-boxes.attachment-meta-box', [

            // Prepare an options object to be passed to the
            // PostsPivoterViewModel constructor in the view.
            'options' => [
                'elementId' => $this->id,
                'postId' => (integer) $post->ID,
                'multiple' => $this->multipleAttachments,
                'attachmentType' => $this->attachmentType,
                'attachmentButtonText' => $this->attachmentButtonText,
                'attachmentPreload' => $this->getAttachmentPreload($post->ID)
            ],

            'metaKey' => $this->metaKey
        ]);
    }

    protected function validateClass()
    {
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

            // Set the value.
            update_post_meta($postId, $this->metaKey, $value);
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