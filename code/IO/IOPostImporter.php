<?php
namespace Zawntech\WordPress\IO;

use Zawntech\WordPress\Utility\MediaImporter;

/**
 * Class IOPostImporter
 * @package Zawntech\WordPress\IO
 */
class IOPostImporter
{
    /**
     * @var array An array describing actions taken by the importer.
     */
    public $importActions = [];

    /**
     * @var int The original WordPress Post ID.
     */
    public $originalPostId;

    /**
     * @var integer The new WordPress Post ID.
     */
    public $newPostId;

    /**
     * @var integer The original post parent ID.
     */
    public $originalPostParent;

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var \stdClass wp_posts row data
     */
    public $postData;

    /**
     * @var \stdClass wp_postmeta row data.
     */
    public $postMeta;

    /**
     * @var FileManager
     */
    protected $files;

    /**
     * @var bool
     */
    public $hasImportedPost = false;

    /**
     * Import this post as an attachment.
     */
    protected function importAttachment()
    {
        // URL to media image.
        $url = $this->postData->guid;

        // Set original post parent.
        $this->originalPostParent = $this->postData->post_parent;

        // Import to media library.
        $this->newPostId = MediaImporter::import( $url, $this->postData->post_title );

        // Set state.
        $this->hasImportedPost = true;
    }

    protected function importPostMeta()
    {
        // Ignore these meta keys.
        $ignoreKeys = [
            '_wp_attached_file',
            '_wp_attachment_metadata'
        ];

        // Loop through the meta items supplied for this post.
        foreach( $this->postMeta as $meta )
        {
            // Exclude ignored meta keys.
            if ( ! in_array( $meta->meta_key, $ignoreKeys ) )
            {
                // Meta value.
                $value = $meta->meta_value;

                // Attach the meta.
                update_post_meta( $this->newPostId, $meta->meta_key, $value );
            }
        }
    }

    protected function updateFeaturedImage()
    {
        // Loop through this post's meta items.
        foreach( $this->postMeta as $item )
        {
            // If '_thumbnail_id' is set then we need to update its ID to the new value.
            if ( '_thumbnail_id' === $item->meta_key )
            {
                // Old post id.
                $oldId = (int) $item->meta_value;

                // Now we need to determine what the new post ID is.
                $oldFileName = "{$oldId}.json";

                // Pull data from file.
                $data = $this->files->get( $oldFileName, true );

                // Get the new post ID from the data.
                $newAttachmentId = $data->newPostId;

                // Update the featured image.
                update_post_meta( $this->newPostId, '_thumbnail_id', $newAttachmentId );
            }
        }
    }

    protected function importPost()
    {
        // Set original post parent.
        $this->originalPostParent = $this->postData->post_parent;

        // Import to media library.
        $this->newPostId = wp_insert_post([
            'post_title' => $this->postData->post_title,
            'post_name' => $this->postData->post_name,
            'post_date' => $this->postData->post_date,
            'post_date_gmt' => $this->postData->post_date_gmt,
            'post_content' => $this->postData->post_content,
            'post_excerpt' => $this->postData->post_excerpt,
            'post_password' => $this->postData->post_password,
            'menu_order' => $this->postData->menu_order,
            'post_mime_type' => $this->postData->post_mime_type,
            'post_type' => $this->postData->post_type,
            'post_status' => $this->postData->post_status
        ]);

        // Set state.
        $this->hasImportedPost = true;

        $this->updateFeaturedImage();
    }
    
    public function import()
    {
        // Should we import this item?
        if ( ! $this->shouldImport() )
        {
            return false;
        }

        switch( $this->postData->post_type )
        {
            case 'attachment':
                $this->importAttachment();
                break;

            default:
                $this->importPost();
                break;
        }

        // Set post meta.
        $this->importPostMeta();
    }

    protected function load()
    {
        // Verify that the file exists before trying to load it.
        if ( ! $this->fileExists() )
        {
            throw new \Exception('Cannot load post json file, does not exist.');
        }

        // Load the data.
        $data = $this->files->get( $this->filename, true );

        // Assign data internally from file.
        $this->hasImportedPost = $data->hasImportedPost;
        $this->postData = $data->postData;
        $this->postMeta = $data->postMeta;
        $this->newPostId = $data->newPostId;
        $this->originalPostParent = $data->originalPostParent;
    }

    public function viaSession($sessionId, $postData, $postMetaData)
    {
        // Assign post data and meta internally.
        $this->postData = $postData;
        $this->postMeta = $postMetaData;
        $this->originalPostId = (int) $postData->ID;

        // Root to /uploads/io-data/import/{sessionId}/posts/{postId}.json
        $this->files->useCustomPath("io-data/import/{$sessionId}/posts");
        $this->filename = "{$this->originalPostId}.json";
    }

    /**
     * @return bool Does the {postId}.json file exist?
     */
    public function fileExists()
    {
        return file_exists( $this->getPathToFile() );
    }

    /**
     * @return string Absolute path to {postId}.json.
     */
    public function getPathToFile()
    {
        return $this->files->getPath() . $this->filename;
    }

    public function viaPostId($sessionId, $postId)
    {
        // Assign original post ID internally.
        $this->originalPostId = $postId;

        // Load from file.
        $this->filename = "{$this->originalPostId}.json";

        // Root to /uploads/io-data/import/{sessionId}/posts/{postId}.json
        $this->files->useCustomPath("io-data/import/{$sessionId}/posts");

        $this->load();
    }

    public function __construct()
    {
        // Make a file manager.
        $this->files = new FileManager;
    }

    public function addAction($message)
    {
        $this->importActions[] = $message;
    }

    protected $shouldImport = true;

    protected function shouldImport()
    {
        return $this->shouldImport;
    }

    /**
     * Save on destruct.
     */
    public function __destruct()
    {
        $this->save();
    }

    /**
     * Save to json.
     */
    public function save()
    {
        $this->files->put( $this->filename, $this );
    }
}