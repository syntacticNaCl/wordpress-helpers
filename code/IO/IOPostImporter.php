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
     * @var string This post importer's session ID.
     */
    public $sessionId;

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
    
    // Does the post have a featured image?
    public function getFeaturedImagePostId()
    {
        // Featured image key.
        $key = '_thumbnail_id';
        
        // If there's no post meta, return false.
        if ( empty( $this->postMeta ) )
        {
            return false;
        }
        
        // Loop through post meta.
        foreach( $this->postMeta as $item )
        {
            // Match meta key.
            if ( $key === $item->meta_key )
            {
                // Get the thumbnail ID.
                $thumbnailId = (int) $item->meta_value;
            }
        }
        
        // No thumbnail found.
        if ( ! isset( $thumbnailId ) ) {
            return false;
        }

        // Return the thumbnail ID.
        return $thumbnailId;
    }

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

    /**
     * @return bool
     */
    public function hasFeaturedImage()
    {
        return $this->getFeaturedImagePostId() !== false;
    }

    public function updateFeaturedImage()
    {
        // Do nothing if this post has no featured image.
        if ( ! $this->hasFeaturedImage() )
        {
            return;
        }
        
        // Get this post's featured image.
        $featuredImage = $this->getFeaturedImageData();
        
        // Import the media to this WordPress instance.
        $newMediaId = MediaImporter::import( $featuredImage->url, $featuredImage->title );

        // The post ID for which we need to reassign the featured image.
        $postId = $this->originalPostId;

        // Update the post thumbnail, which will assign the newly
        // download media to this post as its featured image.
        return set_post_thumbnail( $postId, $newMediaId );
    }

    /**
     * @return IOMediaData
     */
    public function getFeaturedImageData()
    {
        // Get the featured image post ID.
        $imagePostId = $this->getFeaturedImagePostId();

        // Load the image data.
        $imagePost = new IOPostImporter();
        $imagePost->viaPostId( $this->sessionId, $imagePostId );

        return new IOMediaData( $imagePost );
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
        $this->sessionId = $sessionId;

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

    /**
     * Loads the post via the JSON file stored at {sessionId}/posts/{postId}.json.
     * @param $sessionId
     * @param $postId
     * @throws \Exception
     */
    public function viaPostId($sessionId, $postId)
    {
        // Assign the session ID internally.
        $this->sessionId = $sessionId;

        // Assign original post ID internally.
        $this->originalPostId = $postId;

        // Load from file.
        $this->filename = "{$this->originalPostId}.json";

        // Root to /uploads/io-data/import/{sessionId}/posts/{postId}.json
        $this->files->useCustomPath("io-data/import/{$sessionId}/posts");

        // Load data from json file.
        $this->load();
    }

    public function __construct()
    {
        // Make a file manager.
        $this->files = new FileManager;
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