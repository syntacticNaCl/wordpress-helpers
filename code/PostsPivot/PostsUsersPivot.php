<?php
namespace Zawntech\WordPress\PostsPivot;

/**
 * Establish relationships between USERS and POSTS.
 * Class PostUsersPivot
 * @package Zawntech\WordPress\PostsPivot
 */
class PostsUsersPivot
{
    /**
     * The posts_users pivot table name.
     */
    const TABLE_NAME = 'posts_users_pivot';

    /**
     * Determine if the database tables have been installed.
     * @return bool
     */
    public static function isInstalled()
    {
        global $wpdb;

        // Get database table name.
        $tableName = static::getTableName();

        // Prepare SQL string.
        $sql = "SELECT 1 FROM `{$tableName}` LIMIT 1";

        // If the table exists, we will get a result higher than 0.
        return count( $wpdb->get_results($sql) ) > 0;
    }

    /**
     * @return false|int
     */
    public static function install()
    {
        global $wpdb;

        // Get sql file.
        $sql = file_get_contents( WORDPRESS_HELPERS_DIR . 'assets/sql/posts-users-pivot-table.sql' );

        // Replace table name in the sql.
        $sql = str_replace( '{tablename}', static::getTableName(), $sql );

        // Install table.
        $result = $wpdb->query( $sql );

        return $result;
    }

    /**
     * Get the pivot posts table name.
     * @return string
     */
    public static function getTableName()
    {
        global $wpdb;
        return $wpdb->prefix . static::TABLE_NAME;
    }

    /**
     * Determines if a relationship exists between a post and user via postId, userId
     * @param $postId integer WP Post ID
     * @param $userId integer WP User ID
     * @return bool true if a relationship exists between the post and user, otherwise false.
     */
    public static function relationshipExists($postId, $userId)
    {
        global $wpdb;

        // Get table name.
        $tableName = static::getTableName();

        // Prepare SQL
        $sql = $wpdb->prepare(
            "SELECT * FROM {$tableName} WHERE ( post_id = %d AND user_id = %d )", $postId, $userId
        );

        return count( $wpdb->get_results($sql) ) > 0;
    }

    /**
     * Attach a user to a post.
     * @param $postId
     * @param $userId
     * @return bool|false|int
     */
    public static function attach($postId, $userId)
    {
        // Do nothing if the relationship already exists.
        if ( static::relationshipExists($postId, $userId) )
        {
            return false;
        }

        global $wpdb;

        // Get table name.
        $tableName = static::getTableName();

        // Prepare SQL.
        $sql = "INSERT INTO {$tableName} (post_id, user_id, attributes) values (%d, %d, %s);";

        $sql = $wpdb->prepare( $sql, $postId, $userId, '');

        return $wpdb->query($sql);
    }

    /**
     * Detach a user from a post.
     * @param $postId
     * @param $userId
     * @return array|null|object
     */
    public static function detach($postId, $userId)
    {
        global $wpdb;

        // Get table name.
        $tableName = static::getTableName();

        // Prepare SQL.
        $sql = "DELETE FROM {$tableName} WHERE ( post_id = %d AND user_id = %d )";

        $sql = $wpdb->prepare( $sql, $postId, $userId );

        return $wpdb->get_results($sql);
    }

    /**
     * Get a post's related pivot user IDs.
     * @param $postId
     * @return array
     */
    public static function getPostUserIds($postId)
    {
        global $wpdb;

        // Get table name.
        $tableName = static::getTableName();

        // Prepare SQL
        $sql = $wpdb->prepare("SELECT * FROM {$tableName} WHERE post_id = %d;", $postId);

        // Get results.
        $results = $wpdb->get_results($sql);

        // Declare $posts as an array for output.
        $relatedUserIds = [];

        // Loop through results, determine the post IDs we need to return.
        foreach( $results as $result )
        {
            $relatedUserIds[] = $result->user_id;
        }

        return $relatedUserIds;
    }

    /**
     * Get a user's related pivot post IDs.
     * @param $userId
     * @return array
     */
    public static function getUserPostIds($userId)
    {
        global $wpdb;

        // Get table name.
        $tableName = static::getTableName();

        // Prepare SQL
        $sql = $wpdb->prepare("SELECT * FROM {$tableName} WHERE user_id = %d;", $userId);

        // Get results.
        $results = $wpdb->get_results($sql);

        // Declare an array to hold this user's related post IDs.
        $relatedPostIds  = [];

        // Loop through results, determine the post IDs we need to return.
        foreach( $results as $result )
        {
            $relatedPostIds[] = $result->post_id;
        }

        return $relatedPostIds;
    }
}