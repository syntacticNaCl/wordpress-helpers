<?php
namespace Zawntech\WordPress\MetaBoxes;

class AttachmentMetaBox
{
    /**
     * @var string A specified meta key for relating this attachment.
     */
    protected $metaKey;

    /**
     * @var string Metabox element ID.
     */
    protected $id;

    /**
     * @var string Metabox title
     */
    protected $title;

    /**
     * @var string Primary post type key.
     */
    protected $postType;

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

    /**
     * Hook the metabox to $this->postType.
     */
    public function register()
    {
        // Register the metabox to the class's $postType.
        add_meta_box(
            $this->id,
            $this->title,
            [$this, '_render'],
            $this->postType,
            'normal'
        );
    }

    protected function getAttachmentPreload($postId)
    {
        // Get post meta.
        $meta = get_post_meta($postId, $this->metaKey, true);

        if ( false === strpos($meta, ',') )
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
                'postType' => $this->postType,
                'multiple' => $this->multipleAttachments,
                'attachmentType' => $this->attachmentType,
                'attachmentButtonText' => $this->attachmentButtonText,
                'attachmentPreload' => $this->getAttachmentPreload($post->ID)
            ],

            'metaKey' => $this->metaKey
        ]);
    }

    public function _render($post)
    {
        // Is a view model assigned to this meta box?
        if ( $this->viewModel )
        {
            // Enqueue the view model javascript.
            wp_enqueue_script(
                md5($this->id . $this->title) . '-view-model',
                $this->viewModel,
                ['jquery', 'knockout'],
                null,
                true
            );
        }

        echo $this->getNonceField();

        // Call the extending class's render function.
        $this->render($post);
    }

    protected function getNonceAction()
    {
        return md5( $this->id . $this->title );
    }

    protected function getNonceName()
    {
        return 'nonce_' . $this->getNonceAction();
    }

    protected function getNonceField()
    {
        return wp_nonce_field( $this->getNonceAction() , $this->getNonceName(), true, false );
    }

    protected function verifyNonce($nonce)
    {
        return wp_verify_nonce( $nonce, $this->getNonceAction() );
    }

    protected function validateClass()
    {
        // Class name.
        $class = static::class;

        // Verify that a post type is specified (ie, which post types this metabox
        // should be hook).
        if ( ! $this->postType )
        {
            throw new \Exception("No \$postType specified in class {$class}.");
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