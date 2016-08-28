<?php
namespace Zawntech\WordPress\MetaBoxes;

class PostUsersPivotMetaBox extends MetaBoxAbstract
{
    /**
     * @var bool Can this meta box attach multiple users?
     */
    protected $multipleUsers = false;

    public function render(\WP_Post $post)
    {
        echo view('admin.meta-boxes.post-users-pivot-meta-box',
        [
            'options' => [
                'elementId' => $this->id,
                'postId' => (integer) $post->ID,
                'multipleUsers' => $this->multipleUsers
            ],
        ]);
    }

    protected function validateClass()
    {
        parent::validateClass();

        // Class name.
        $class = static::class;

        if ( ! $this->postTypes )
        {
            throw new \Exception("No \$postTypes specified in class {$class}.");
        }
    }

    public function __construct()
    {
        // Verify the extending class is correctly defined.
        $this->validateClass();

        // Set the view model.
        $this->viewModel = WORDPRESS_HELPERS_URL . 'assets/js/view-models/post-users-pivoter-meta-box-view-model.js';

        // Register the meta box (hooks to post type defined in $this->postType).
        add_action( 'add_meta_boxes', [$this, 'register'] );
    }
}