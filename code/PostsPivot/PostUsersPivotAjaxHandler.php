<?php
namespace Zawntech\WordPress\PostsPivot;

use Zawntech\WordPress\Utility\Ajax;

class PostUsersPivotAjaxHandler
{
    protected function prepareModel($userRow)
    {
        return [
            'userId' => (int) $userRow->ID,
            'login' => $userRow->user_login,
            'email' => $userRow->user_email,
            'thumbnail' => get_avatar_url( $userRow->ID )
        ];
    }

    public function all()
    {
        global $wpdb;

        // Get all users from database.
        $sql = "SELECT * FROM {$wpdb->users};";
        $results = $wpdb->get_results( $sql );
        $output = [];

        foreach( $results as $result )
        {
            $output[] = $this->prepareModel( $result );
        }

        Ajax::jsonResponse($output);
    }

    public function get()
    {
        // Get post ID.
        $postId = $_POST['postId'];

        // Get this post's related user IDs.
        $userIds = PostsUsersPivot::getPostUserIds( $postId );

        Ajax::jsonResponse( $userIds );
    }

    public function attach()
    {
        $postId = $_POST['postId'];
        $userId = $_POST['userId'];

        // Attach user to post.
        PostsUsersPivot::attach($postId, $userId);
        
        Ajax::jsonResponse(true);
    }

    public function detach()
    {
        $postId = $_POST['postId'];
        $userId = $_POST['userId'];

        // Attach user to post.
        PostsUsersPivot::detach($postId, $userId);

        Ajax::jsonResponse(true);
    }

    public function __construct()
    {
        add_action( 'wp_ajax_post_users_pivot_all', [$this, 'all'] );
        add_action( 'wp_ajax_post_users_pivot_get', [$this, 'get'] );
        add_action( 'wp_ajax_post_users_pivot_attach', [$this, 'attach'] );
        add_action( 'wp_ajax_post_users_pivot_detach', [$this, 'detach'] );
    }
}