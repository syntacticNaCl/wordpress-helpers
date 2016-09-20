<?php
namespace Zawntech\WordPress\PostTypes;

use Zawntech\WordPress\PostsPivot\PostsPivot;

abstract class PostTypeQuery
{
    /**
     * @var string Post type key
     */
    protected static $postType;

    /**
     * @var PostTypeModel
     */
    protected static $model;

    /**
     * @return PostTypeModel[]
     */
    public static function all()
    {
        // Get specifications.
        $query = new \WP_Query([
            'post_type' => static::$postType,
            'nopaging' => true
        ]);

        // Models
        $models = [];
        $className = static::$model;

        // Loop through models.
        foreach( $query->posts as $post )
        {
            $models[] = new $className($post->ID);
        }

        return $models;
    }

    /**
     * @param $postId
     * @return PostTypeModel
     */
    public static function getByPostId($postId)
    {
        $className = static::$model;
        return new $className($postId);
    }

    /**
     * @param $postTitle
     * @return bool|PostTypeModel
     */
    public static function getByPostTitle($postTitle)
    {
        // Get specifications.
        $query = new \WP_Query([
            'post_type' => static::$postType,
            'title' => $postTitle
        ]);

        if ( $query->post_count > 0 )
        {
            $className = static::$model;
            return new $className($query->posts[0]->ID);
        }

        return false;
    }

    /**
     * Get a collection of related post type models.
     * @param $postId
     * @param $relatedPostType
     * @param $postTypeModel
     * @return PostTypeModel[]|array|bool
     */
    public static function getRelatedPostModels($postId, $relatedPostType, $postTypeModel)
    {
        // Get the client's related person post IDs.
        $relatedPostIds = PostsPivot::getRelatedPostIdsByPostType( $postId, $relatedPostType );

        // Declare an array for output.
        $output = [];

        if ( empty( $relatedPostIds ) ) {
            return false;
        }

        // Loop through post IDs.
        foreach( $relatedPostIds as $id )
        {
            $output[] = new $postTypeModel($id);
        }

        // Return the array.
        return $output;
    }

    /**
     * @param string $order
     * @return PostTypeModel[]
     */
    public static function getAllByMenuOrder($order = 'ASC')
    {
        // Get specifications.
        $query = new \WP_Query([
            'post_type' => static::$postType,
            'nopaging' => true,
            'orderby' => 'menu_order',
            'order' => $order
        ]);

        // Models
        $models = [];
        $className = static::$model;

        // Loop through models.
        foreach( $query->posts as $post )
        {
            $models[] = new $className($post->ID);
        }

        return $models;
    }
}