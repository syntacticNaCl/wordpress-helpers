<?php
namespace Zawntech\WordPress;

/**
 * Class MetaBox
 * @package Zawntech\WordPress
 */
class MetaBox
{
    /**
     * @var string Meta box element ID.
     */
    protected $id;

    /**
     * @var string The metabox title as displayed in the WordPress admin.
     */
    protected $title;

    /**
     * @var array An array of post types to which this metabox should be hooked.
     */
    protected $postTypes = [];

    /**
     * @var string Public URL to view model javascript file.
     * If left empty, then no view model javascript file is enqueued.
     */
    protected $viewModel;

    /**
     * @var string Javascript preload variable name used by view model.
     * If let empty, no preload data is printed to the metabox.
     */
    protected $viewModelPreloadVar;

    /**
     * An array of meta keys this metabox supports.
     * @var array
     */
    protected $metaKeys = [];

    /**
     * Register the metabox to the post types defined in $this->postTypes.
     */
    public function register()
    {
        // Register the metabox for each associated post type.
        foreach( $this->postTypes as $postType )
        {
            add_meta_box(
                $this->id,
                $this->title,
                [$this, '_render'],
                $postType,
                'normal'
            );
        }
    }

    /**
     * @param $postId
     * @return mixed|string|void
     */
    protected function getPreloadModel($postId)
    {
        // Meta get post meta.
        $meta = get_post_meta($postId);
        $output = [];

        foreach( $this->metaKeys as $key )
        {
            if ( isset( $meta[$key] ) )
            {
                $output[$key] = $meta[$key][0];
            } else {
                $output[$key] = '';
            }
        }

        return json_encode($output);
    }

    /**
     * @param $postId
     */
    protected function printPreloadJavascript($postId)
    {
        // Do nothing if no view model preload variable specified.
        if ( ! $this->viewModelPreloadVar ) {
            return;
        }

        ?>
        <script>var <?= $this->viewModelPreloadVar; ?> = <?= $this->getPreloadModel($postId); ?>;</script>
        <?php
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

            // Print the line preload javascript data.
            $this->printPreloadJavascript($post->ID);
        }
        echo $this->getNonceField();
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

    public function save($postId)
    {
        // Determine if this is a quick edit update.
        $isQuickEdit = isset($_POST['quick_edit_nonce']);

        // Verify the nonce.
        if ( ! $isQuickEdit && ! $this->verifyNonce( $_POST[$this->getNonceName()] ) )
        {
            return;
        }

        // Verify quick edit nonce.
        if ( $isQuickEdit )
        {
            // Get post
            $post = get_post($postId);

            // Get the nonce value.
            $nonce = $_POST['quick_edit_nonce'];

            // The nonce key is prepared via 'quick-{$postType}' (see QuickEditor)
            $nonceKey = 'quick-' . $post->post_type;

            // Verify the nonce.
            if ( ! wp_verify_nonce($nonce, $nonceKey) ) {
                return;
            }
        }

        foreach( $this->metaKeys as $key ) {
            if ( isset( $_POST[$key] ) ) {
                update_post_meta( $postId, $key, $_POST[$key] );
            }
        }
    }

    public function __construct()
    {
        add_action( 'save_post', [$this, 'save'] );
        add_action( 'add_meta_boxes', [$this, 'register'] );
    }
}