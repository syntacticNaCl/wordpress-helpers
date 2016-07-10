<?php
namespace Zawntech\WordPress\PostsPivot;

class PostsPivotMetaBox
{
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
     * @var string Related type key.
     */
    protected $relatedType;

    /**
     * @var string Public URL to posts pivoter view model javascript.
     */
    protected $viewModel = WORDPRESS_HELPERS_URL . 'assets/js/view-models/posts-pivoter-meta-box-view-model.js';
    
    protected $viewModelPreloadVar;

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

    public function render($post)
    {
        $relatedPosts = PostsPivot::getRelatedPostIdsByPostType( $post->ID, $this->relatedType );

        echo view('admin.post-types.pivots.posts-pivot-meta-box', [

            // Prepare an options object to be passed to the
            // PostsPivoterViewModel constructor in the view.
            'options' => [
                'elementId' => $this->id,
                'postId' => (integer) $post->ID,
                'postType' => $this->postType,
                'relatedType' => $this->relatedType
            ],
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

        // The type of related posts we want to associate.
        if ( ! $this->relatedType ) {
            throw new \Exception("No \$relatedType specified in class {$class}.");
        }
    }

    public function __construct()
    {
        // Verify the extending class is correctly defined.
        $this->validateClass();

        // Register the meta box (hooks to post type defined in $this->postType).
        add_action( 'add_meta_boxes', [$this, 'register'] );
    }
}