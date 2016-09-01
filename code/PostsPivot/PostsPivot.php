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
     * Match a multiple set of post IDs.
     * @param array $postsIds
     * @return array|bool
     */
    public static function getRelatedPostsIds($postsIds = [])
    {
        global $wpdb;

        // Get table name.
        $tableName = static::getTableName();

        // Join by comma.
        $idsString = implode( ',', $postsIds );

        // Prepare SQL
        $sql = "SELECT * FROM {$tableName} WHERE id_1 IN ( $idsString ) OR id_2 IN ( $idsString );";

        // Get results.
        $results = $wpdb->get_results($sql);

        // Declare $posts as an array for output.
        $relatedPostIds = [];

        // Loop through results, determine the post IDs we need to return.
        foreach( $results as $result )
        {
            $value = (int) ( in_array( $result->id_1, $postsIds ) ? $result->id_2 : $result->id_1 );
            $input = (int) ( ! in_array( $result->id_1, $postsIds ) ? $result->id_2 : $result->id_1 );
            $relatedPostIds[$input] = $value;
        }

        if ( empty( $relatedPostIds ) )
        {
            return false;
        }

        return $relatedPostIds;
    }

    /**
     * Return an array of related post IDs for a given post.
     * @param $postId
     * @return array|boolean
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
            $value = (int) ( $postId == $result->id_1 ? $result->id_2 : $result->id_1 );
            $relatedPostIds[] = $value;
        }

        if ( ! isset( $relatedPostIds[0] ) || ( 0 == $relatedPostIds[0] && 1 == count($relatedPostIds) ) )
        {
            return false;
        }

        return $relatedPostIds;
    }

    /**
     * Recursively get related post IDs.
     * @param $postId
     * @param $finalPostType string The desired related post type
     * @param array $throughPostTypes The list of post types through which to iterate
     * @return array
     */
    public static function getRelatedPostIdsThroughPostType($postId, $finalPostType, $throughPostTypes = ['post'])
    {
        // Declare post IDs.
        $postIds = [];

        // If $throughPostTypes is a string, convert to array.
        if ( is_string( $throughPostTypes ) )
        {
            $throughPostTypes = [$throughPostTypes];
        }

        // Iterate through post types.
        while( ! empty( $throughPostTypes ) )
        {
            // Get first post type item.
            $postType = array_shift( $throughPostTypes );

            // Get todolist IDs
            $postIds = PostsPivot::getRelatedPostIdsByPostType( $postId, $postType );

            // No post IDs, so break loop.
            if ( empty( $postIds ) ) {
                break;
            }

            // Update post ID.
            $postId = $postIds[0];
        }

        return $postIds;
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

        // No post IDs
        if ( ! $postIds )
        {
            return [];
        }

        // Join integers by comma.
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
            $resultIds[] = (integer) $result->ID;
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

    /**
     * Attaches post A to post B.
     * @param $postIdA int
     * @param $postIdB int
     * @return bool|false|int
     */
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

    /**
     * Detaches post A from post B.
     * @param $postIdA
     * @param $postIdB
     * @return array|null|object
     */
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

    /**
     * Remove instances of a post ID from either column.
     * @param $postId
     * @return array|null|object
     */
    public static function delete($postId)
    {
        global $wpdb;

        // Get table name.
        $tableName = static::getTableName();

        // Prepare SQL.
        $sql = "DELETE FROM {$tableName} WHERE ( id_1 = %d ) OR ( id_2 = %d )";

        $sql = $wpdb->prepare( $sql, $postId, $postId );

        return $wpdb->get_results($sql);
    }
}