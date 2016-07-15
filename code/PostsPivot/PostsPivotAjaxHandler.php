<?php
namespace Zawntech\WordPress\PostsPivot;

class PostsPivotAjaxHandler
{
    protected function validateRequest()
    {
        header('Content-Type: application/json');
    }

    protected function prepareModels($posts)
    {
        // Declare output.
        $output = [];

        // Loop through posts.
        foreach( $posts as $post )
        {
            // Append output.
            $output[] = [
                'id' => $post->ID,
                'title' => $post->post_title,
                'thumbnail' => get_the_post_thumbnail_url( $post->ID )
            ];
        }

        // Return output.
        return $output;
    }

    public function all()
    {
        // Verify request.
        $this->validateRequest();

        // We want to get all available posts of the related post type.
        $relatedType = $_POST['relatedType'];

        $posts = new \WP_Query([
            'post_type' => $relatedType,
            'nopaging' => true
        ]);

        echo json_encode( $this->prepareModels( $posts->posts ) );
        exit;
    }

    public function get()
    {
        // Verify request.
        $this->validateRequest();

        $postId = $_POST['postId'];
        $postType = $_POST['postType'];
        $relatedType = $_POST['relatedType'];

        echo json_encode( PostsPivot::getRelatedPostIdsByPostType( $postId, $relatedType ) );
        exit;
    }

    public function attach()
    {
        // Verify request.
        $this->validateRequest();

        $postId = $_POST['postId'];
        $relatedId = $_POST['relatedId'];

        echo json_encode( PostsPivot::attach($postId, $relatedId) );
        exit;
    }

    public function detach()
    {
        // Verify request.
        $this->validateRequest();

        $postId = $_POST['postId'];
        $relatedId = $_POST['relatedId'];

        echo json_encode( PostsPivot::detach($postId, $relatedId) );
        exit;
    }

    public function create_related()
    {
        // Verify request.
        $this->validateRequest();

        $newPost = wp_insert_post([
            'post_type' => $_POST['relatedType'],
            'post_author' => get_current_user_id(),
            'post_title' => $_POST['newPost']['post_title'],
            'post_content' => $_POST['newPost']['post_content'],
            'post_status' => 'publish',
            'meta_input' => [],
        ]);

        if ( $newPost )
        {
            // Attach the model.
            PostsPivot::attach($_POST['postId'], $newPost);

            echo json_encode([
                'posts' => $this->prepareModels([get_post($newPost)])
            ]);
        }

        exit;
    }

    public function __construct()
    {
        add_action( 'wp_ajax_posts_pivot_all', [$this, 'all'] );
        add_action( 'wp_ajax_posts_pivot_get', [$this, 'get'] );
        add_action( 'wp_ajax_posts_pivot_attach', [$this, 'attach'] );
        add_action( 'wp_ajax_posts_pivot_detach', [$this, 'detach'] );
        add_action( 'wp_ajax_posts_pivot_create_related', [$this, 'create_related'] );
    }
}