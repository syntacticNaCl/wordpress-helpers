<?php
namespace Zawntech\WordPress\MetaBoxes;

/**
 * An extensible class for hooking Posts Pivot Meta Boxes.
 * Class PostsPivotMetaBox
 * @package Zawntech\WordPress\PostsPivot
 */
class PostsPivotMetaBox extends MetaBoxAbstract
{
    /**
     * @var string Primary post type key.
     */
    protected $postType;

    /**
     * @var string Related type key.
     */
    protected $relatedType;

    /**
     * @var bool If true, then the meta box will show an option for automatically creating and relating posts of
     * the related type. If false, the option does not appear.
     */
    protected $enableRelatedPostsCreator = true;

    protected $labels = [
        'related_post_singular' => 'Post',
        'related_post_plural' => 'Posts'
    ];

    protected function printCreateRelatedPostForm()
    {
        // Start PHP buffer.
        ob_start();

        // Declare form ID.
        $formId = $this->id . '-create-related-post-form';

        ?>
        <hr>

        <div id="<?= $formId ?>">
            <ko-input params="
                label: '<?= $this->labels['related_post_singular']; ?>  Title',
                placeholder: '<?= $this->labels['related_post_singular']; ?> Title',
                value: creator.post_title
            "></ko-input>

            <ko-textarea params="
                label: '<?= $this->labels['related_post_singular']; ?> Content',
                placeholder: '<?= $this->labels['related_post_singular']; ?> content...',
                value: creator.post_content
            "></ko-textarea>

            <ko-button params="
                text: 'Create <?= $this->labels['related_post_singular']; ?>',
                busyText: 'Creating <?= $this->labels['related_post_singular']; ?>',
                class: 'btn btn-success',
                click: function(){ creator.submit(this) }
            ">
            </ko-button>
        </div>

        <hr>
        <?php
        return ob_get_clean();
    }

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

    public function render(\WP_Post $post)
    {
        echo view('admin.meta-boxes.posts-pivot-meta-box', [

            // Prepare an options object to be passed to the
            // PostsPivoterViewModel constructor in the view.
            'options' => [
                'elementId' => $this->id,
                'postId' => (integer) $post->ID,
                'postType' => $this->postType,
                'relatedType' => $this->relatedType,
                'relatedPostsCreator' => $this->enableRelatedPostsCreator,
            ],

            'relatedPostsForm' => $this->printCreateRelatedPostForm(),

            'labels' => $this->labels
        ]);
    }

    protected function validateClass()
    {
        parent::validateClass();

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

        // Set view model.
        $this->viewModel = WORDPRESS_HELPERS_URL . 'assets/js/view-models/posts-pivoter-meta-box-view-model.js';

        // Register the meta box (hooks to post type defined in $this->postType).
        add_action( 'add_meta_boxes', [$this, 'register'] );
    }
}