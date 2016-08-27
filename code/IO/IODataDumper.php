<?php
namespace Zawntech\WordPress\IO;
use Zawntech\WordPress\PostsPivot\PostsPivot;

/**
 * Extracts data from a WordPress instance.
 * Class IODataDumper
 * @package Zawntech\WordPress\IO
 */
class IODataDumper
{
    /**
     * @var array A list of post types.
     */
    protected $postTypes = [];

    /**
     * @var \wpdb
     */
    protected $wpdb;

    /**
     * @var string Defines a path in the uploads directory.
     */
    protected $path = 'io-data/export';

    /**
     * @return array A list of file names in the data dumper's storage directory.
     */
    public function getFiles()
    {
        return array_values( array_diff( scandir( $this->files->getPath() ), [ '.', '..'] ) );
    }

    /**
     * Delete files.
     */
    public function purgeFiles()
    {
        // Get files.
        $files = $this->getFiles();

        // Loop through files.
        foreach( $files as $filename )
        {
            $path = $this->files->getPath() . $filename;

            // Delete the file.
            unlink( $path );
        }
    }

    /**
     * Returns a list of URLs to individual JSON files, each
     * corresponding to a particular set of data.
     * @return array
     */
    public function getJsonList()
    {
        // Get files array.
        $files = $this->getFiles();

        // Loop through files.
        foreach( $files as &$file )
        {
            // Prepare full path.
            $file = $this->files->getUrl() . $file;
        }

        // Return the array.
        return $files;
    }

    /**
     * Returns an array of all post types found in the database.
     * @return array
     */
    public function getPostTypes()
    {
        // Prepare query.
        $sql = "SELECT DISTINCT post_type FROM {$this->wpdb->posts};";
        $postTypes = [];
        foreach( $this->wpdb->get_results( $sql ) as $row )
        {
            $postTypes[] = $row->post_type;
        }
        return $postTypes;
    }

    public $postTypeCount = [];
    public $usersCount = 0;
    public $users = [];

    /**
     * Dump posts into individualized files per post type.
     */
    protected function dumpPosts()
    {
        // Declare an array for post statistics.
        $postTypeCount = [];

        // Loop through post types.
        foreach( $this->postTypes as $postType )
        {
            // Declare this post type in the post type count array.
            if ( ! isset( $postTypeCount[$postType] ) )
            {
                $postTypeCount[$postType] = 0;
            }
            
            // Define the SQL query.
            $sql = "SELECT * from {$this->wpdb->posts} WHERE post_type = '{$postType}';";
            
            // Get data.
            $results = $this->wpdb->get_results( $sql );

            // Set a file name for this post type.
            $filename = $postType . '-posts.json';

            // Store data.
            $this->files->put( $filename, $results );

            // Prepare a list of post IDs for this post type.
            $postIds = [];

            // Extract post IDs.
            foreach( $results as $row )
            {
                $postIds[] = (int) $row->ID;

                // Bump post type count.
                $postTypeCount[$postType]++;
            }

            // Join by comma.
            $postIds = implode( ',', $postIds );

            // Define the SQL query to get this post type's posts.
            $sql = "SELECT * from {$this->wpdb->postmeta} WHERE post_id IN ( {$postIds} );";

            // Get data.
            $results = $this->wpdb->get_results( $sql );

            // Set a file name for this post type.
            $filename = $postType . '-posts-meta.json';

            // Store data.
            $this->files->put( $filename, $results );
        }

        // Assign to object.
        $this->postTypeCount = $postTypeCount;
    }

    /**
     * Grab data from a table, dump to json file.
     * @param $tableName
     * @param $filename
     */
    protected function dumpTable($tableName, $filename)
    {
        // Define the SQL query.
        $sql = "SELECT * from {$tableName};";

        // Get data.
        $results = $this->wpdb->get_results( $sql );

        // Store data.
        $this->files->put( $filename, $results );

        return $results;
    }

    public function dumpUsers()
    {
        $users = $this->dumpTable( $this->wpdb->users, 'users.json' );

        foreach( $users as $user )
        {
            $this->users[] = [
                'id' => $user->ID,
                'email' => $user->user_email,
                'login' => $user->user_login
            ];
        }
        $this->usersCount = count($users);
    }

    /**
     * @return array JSON file list.
     */
    public function exportToJson()
    {
        $this->dumpTable( $this->wpdb->terms, 'terms.json' );
        $this->dumpTable( $this->wpdb->termmeta, 'term-meta.json' );
        $this->dumpTable( $this->wpdb->term_relationships, 'term-relationships.json' );
        $this->dumpTable( $this->wpdb->term_taxonomy, 'term-taxonomy.json' );
        $this->dumpTable( $this->wpdb->options, 'options.json' );
        $this->dumpTable( $this->wpdb->base_prefix . PostsPivot::TABLE_NAME, 'posts_pivot.json' );
        $this->dumpUsers();
        $this->dumpTable( $this->wpdb->usermeta, 'user-meta.json' );
        $this->dumpPosts();

        return $this->getJsonList();
    }

    public function __construct()
    {
        // Setup WPDB.
        global $wpdb;
        $this->wpdb = $wpdb;

        // Load post types.
        $this->postTypes = $this->getPostTypes();

        // Initialize file manager, set path.
        $this->files = new FileManager();
        $this->files->useCustomPath( $this->path );
    }
}