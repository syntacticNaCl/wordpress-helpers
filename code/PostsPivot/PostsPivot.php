<?php
namespace Zawntech\WordPress\PostsPivot;

/**
 * This class gives WordPress the ability to create relationships
 * between posts using post IDs.
 *
 * Class PostsPivot
 * @package Zawntech\Projection\WP
 */
class PostsPivot
{
    /**
     * The posts pivot table name.
     */
    const TABLE_NAME = 'posts_pivot';

    /**
     * Install the database table.
     */
    public static function install()
    {
        global $wpdb;
        
        // Get sql file.
        $sql = file_get_contents( WORDPRESS_HELPERS_DIR . 'assets/sql/posts-pivot-table.sql' );

        // Replace table name in the sql.
        $sql = str_replace( '{tablename}', static::getTableName(), $sql );

        // Install table.
        $wpdb->query( $sql );
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
     * Return an array of related post IDs for a given post.
     * @param $postId
     * @return array
     */
    public static function getRelatedPostIds($postId)
    {
        global $wpdb;

        // Get table name.
        $tableName = static::getTableName();

        // Prepare SQL
        $sql = $wpdb->prepare("SELECT * FROM {$tableName} WHERE id_1 = %d OR id_2 = %d", $postId, $postId);

        // Get results.
        $results = $wpdb->get_results($sql);

        // Declare $posts as an array for output.
        $relatedPostIds = [];

        // Loop through results, determine the post IDs we need to return.
        foreach( $results as $result )
        {
            $relatedPostIds[] = $postId == $result->id_1 ? $result->id_2 : $result->id_1;
        }

        return $relatedPostIds;
    }

    /**
     * @param $postId
     * @param string $postType
     * @return array
     */
    public static function getRelatedPostIdsByPostType($postId, $postType='post')
    {
        global $wpdb;

        // Get related post IDs.
        $postIds = static::getRelatedPostIds($postId);
        $postIdsString = implode( ',', $postIds );

        // Prepare SQL.
        $sql = "SELECT * FROM {$wpdb->base_prefix}posts WHERE `ID` IN ( $postIdsString ) AND `post_type` = '{$postType}'";

        // Get results.
        $results = $wpdb->get_results($sql);

        // Declare result IDs for output.
        $resultIds = [];

        // Loop through results, determine the post IDs we need to return.
        foreach( $results as $result )
        {
            $resultIds[] = $result->ID;
        }

        return $resultIds;
    }

    /**
     * Determine if a relationship exists between two posts.
     * @param $postIdA
     * @param $postIdB
     * @return bool
     */
    public static function relationshipExists($postIdA, $postIdB)
    {
        global $wpdb;

        // Get table name.
        $tableName = static::getTableName();

        // Prepare SQL
        $sql = $wpdb->prepare(
            "SELECT * FROM {$tableName} WHERE ( id_1 = %d AND id_2 = %d ) OR ( id_1 = %d AND id_2 = %d )",

            // Condition 1
            $postIdA, $postIdB,

            // Condition 2
            $postIdB, $postIdA
        );

        return count( $wpdb->get_results($sql) ) > 0;
    }

    public static function attach($postIdA, $postIdB)
    {
        // Do nothing if the relationship already exists.
        if ( static::relationshipExists($postIdA, $postIdB) )
        {
            return false;
        }

        global $wpdb;

        // Get table name.
        $tableName = static::getTableName();

        // Prepare SQL.
        $sql = "INSERT INTO {$tableName} (id_1, id_2, attributes) values (%d, %d, %s);";

        $sql = $wpdb->prepare( $sql, $postIdA, $postIdB, '' );

        return $wpdb->query($sql);
    }

    public static function detach($postIdA, $postIdB)
    {
        global $wpdb;

        // Get table name.
        $tableName = static::getTableName();

        // Prepare SQL.
        $sql = "DELETE FROM {$tableName} WHERE ( id_1 = %d AND id_2 = %d ) OR ( id_1 = %d AND id_2 = %d )";

        $sql = $wpdb->prepare( $sql, $postIdA, $postIdB, $postIdB, $postIdA );

        return $wpdb->get_results($sql);
    }
}