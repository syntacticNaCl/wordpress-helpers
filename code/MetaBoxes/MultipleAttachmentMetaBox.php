<?php
namespace Zawntech\WordPress\MetaBoxes;
use Zawntech\WordPress\PostMetaAttachments\PostMetaAttachmentQuery;

/**
 * A metabox for attaching media or custom URLs across multiple meta keys.
 * Class AttachmentMetaBox
 * @package Zawntech\WordPress\MetaBoxes
 */
class MultipleAttachmentMetaBox extends MetaBoxAbstract
{
    /**
     * @var array The class takes an options array to initialize individual
     */
    protected $options = [
        // An example option:
        // [
        //     'key' => '',
        //     'label' => '',
        //     'multiple' => true,
        //     'type' => '',
        // ]
    ];

    protected $defaultType = 'wp';

    protected $defaultMultiple = true;

    protected function setViewModels()
    {
        $this->viewModel = [
            WORDPRESS_HELPERS_URL . 'assets/js/view-models/attachment-meta-box/custom-url-attachments-view-model.js',
            WORDPRESS_HELPERS_URL . 'assets/js/view-models/attachment-meta-box/wordpress-media-attachments-view-model.js',
            WORDPRESS_HELPERS_URL . 'assets/js/view-models/multiple-attachment-meta-box-view-model.js'
        ];
    }


    protected function getAttachmentSource($postId, $metaKey)
    {
        $meta = get_post_meta($postId, $metaKey . '_type', true);
        return false === $meta ? 'wp' : $meta;
    }

    protected function getOptionsPreload($postId)
    {
        foreach( $this->options as &$option )
        {
            $option['preload'] = PostMetaAttachmentQuery::getAttachmentModel($postId, $option['key']);
            $option['sourceType'] = PostMetaAttachmentQuery::getAttachmentType($postId, $option['key']);
        }

        return $this->options;
    }

    public function render(\WP_Post $post)
    {
        echo view('admin.meta-boxes.multiple-attachment-meta-box', [

            // Prepare an options object to be passed to the
            // PostsPivoterViewModel constructor in the view.
            'options' => [
                'elementId' => $this->id,
                'postId' => (integer) $post->ID,
                'options' => $this->getOptionsPreload($post->ID)
            ],
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
        if ( empty( $this->options) )
        {
            throw new \Exception("No input options are specified for class {$class}");
        }

        // Define required option keys:
        $requiredKeys = ['key', 'label'];

        // Validate options.
        foreach( $this->options as $key => $option )
        {
            // Check required keys.
            foreach( $requiredKeys as $requiredKey )
            {
                if ( ! in_array( $requiredKey, $requiredKeys ) )
                {
                    throw new \Exception("Each option must have a '{$requiredKey}' value defined.");
                }
            }

            // Set defaults.
            if ( ! isset( $option['type'] ) || '' === $option['type'] )
            {
                // Set the default option type.
                $this->options[$key]['type'] = $this->defaultType;
            }

            // Set defaults.
            if ( ! isset( $option['multiple'] ) )
            {
                // Set the default option type.
                $this->options[$key]['multiple'] = $this->defaultMultiple;
            }
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

        // Loop through options.
        foreach( $this->options as $option )
        {
            if ( isset( $_POST[$option['key']] ) )
            {
                // Get value.
                $value = $_POST[$option['key']];
                $type = $_POST[$option['key'] . '_type'];

                // Set the value.
                update_post_meta($postId, $option['key'], $value);
                update_post_meta($postId, $option['key'] . '_type', $type);
            }
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