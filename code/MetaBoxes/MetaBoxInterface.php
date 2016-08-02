<?php
namespace Zawntech\WordPress\MetaBoxes;

class MetaBoxInterface
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
     * @var string|array Public URL(s) to view model javascript file(s).
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
     * @var array An array of meta keys by intended variable casting.
     */
    protected $casts = [];

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
                $value = $meta[$key][0];
            } else {
                $value = '';
            }

            // Check if this meta key exists in $this->casts.
            if ( isset( $this->casts[$key] ) )
            {
                switch( $this->casts[$key] )
                {
                    case 'boolean':
                        if ( 'true' === $value ) {
                            $value = true;
                        }
                        if ( 'false' === $value ) {
                            $value = false;
                        }
                        break;
                }
            }

            $output[$key] = $value;
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
            // If the view model is a string, enqueue the single file.
            if ( is_string( $this->viewModel ) )
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

            // If the view model is an array, iterate through it.
            if ( is_array( $this->viewModel ) )
            {
                foreach( $this->viewModel as $viewModel )
                {
                    // Enqueue the view model javascript.
                    wp_enqueue_script(
                        md5($viewModel) . '-view-model',
                        $viewModel,
                        ['jquery', 'knockout'],
                        null,
                        true
                    );
                }
            }

            // Print the line preload javascript data.
            $this->printPreloadJavascript($post->ID);
        }

        echo $this->getNonceField();

        $this->render($post);
    }

    /**
     * @param \WP_Post $post
     */
    public function render(\WP_Post $post)
    {
        echo "Override render()!";
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

        // Verify element Id.
        if ( ! $this->id )
        {
            throw new \Exception("No \$id is specified in class {$class}");
        }

        if ( ! $this->title )
        {
            throw new \Exception("No \$title is specified in class {$class}");
        }
    }
}