<?php
namespace Zawntech\WordPress\IO;

class PostDumper
{
    protected $postTypes = [];

    /**
     * @var \wpdb
     */
    protected $wpdb;

    /**
     * @var string Defines a path in the uploads directory.
     */
    protected $path = 'post-data';

    /**
     * @return array
     */
    public function getJsonList()
    {
        // Files.
        $files = array_values( array_diff( scandir( $this->files->getPath() ), [ '.', '..'] ) );

        // Loop through files.
        foreach( $files as &$file )
        {
            // Prepare full path.
            $file = $this->files->getUrl() . $file;
        }

        // Return the array.
        return $files;
    }

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

    public function dump()
    {
        // Loop through post types.
        foreach( $this->postTypes as $postType )
        {
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
    }

    public function __construct($postTypes = null)
    {
        // Setup WPDB.
        global $wpdb;
        $this->wpdb = $wpdb;

        // Load post types.
        $this->postTypes = $postTypes ?: $this->getPostTypes();

        // Initialize file manager, set path.
        $this->files = new FileManager();
        $this->files->useCustomPath( $this->path );
    }
}