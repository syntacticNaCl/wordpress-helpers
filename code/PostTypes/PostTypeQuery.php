<?php
namespace Zawntech\WordPress\PostTypes;

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
            $models[] = new $className($query->posts[0]->ID);
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
}