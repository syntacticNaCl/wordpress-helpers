<?php
namespace Zawntech\WordPress;

class MetaBox
{
    protected $id;
    protected $title;
    protected $postTypes = [];
    protected $viewModel;
    protected $viewModelPreloadVar;

    /**
     * An array of meta keys this metabox supports.
     * @var array
     */
    protected $metaKeys = [];

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

    protected function printPreloadJavascript($postId)
    {
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
                PROJECTION_PLUGIN_URL . 'assets/js/view-models/admin/' .
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