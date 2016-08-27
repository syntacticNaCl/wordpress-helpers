<?php
namespace Zawntech\WordPress\IO\Ajax;

use Zawntech\WordPress\IO\FileManager;
use Zawntech\WordPress\IO\IOPostImporter;
use Zawntech\WordPress\IO\IOSession;
use Zawntech\WordPress\IO\RemoteInstance;
use Zawntech\WordPress\IO\SecurityKey;
use Zawntech\WordPress\PostsPivot\PostsPivot;
use Zawntech\WordPress\Utility\Ajax;

/**
 * Local AJAX functions on behalf of the local WordPress instance's client.
 *
 * Class IOAjaxLocalTrait
 * @package Zawntech\WordPress\IO\Ajax
 */
trait IOAjaxLocalTrait
{
    /**
     * Updates the IO Manager's settings (security key), returns the new key string to client.
     * @throws \Exception
     */
    public function update_settings()
    {
        // Validate data.
        if (!isset($_POST['settings']['securityKey'])) {
            throw new \Exception('Cannot update options, no security key supplied.');
        }

        // Update the security key.
        $newKey = SecurityKey::setKey($_POST['settings']['securityKey']);

        Ajax::jsonResponse( $newKey);
    }

    /**
     * Resets IO Manager security key to a random value and returns the string to client.
     */
    public function reset_security_key()
    {
        Ajax::jsonResponse( SecurityKey::setKey() );
    }

    /**
     * Tests if a given URL and security key are valid for establishing a connection to
     * a remote WordPress instance.
     */
    public function can_connect_to_remote()
    {
        $url = $_POST['remoteUrl'];
        $key = $_POST['remoteSecurityKey'];

        // Make remote.
        $remote = new RemoteInstance($url, $key);
        
        $canConnect = $remote->canConnect();
        $error = $remote->getConnectionError();

        if ( ! $error ) {
            Ajax::jsonResponse( $canConnect );
        } else {
            Ajax::jsonError( $error );
        }
    }

    /**
     * Fetches data about a remote WordPress instance.
     */
    public function get_remote_data()
    {
        $url = $_POST['remoteUrl'];
        $key = $_POST['remoteSecurityKey'];

        // Make remote.
        $remote = new RemoteInstance($url, $key);
        
        // Make remote session.
        $session = new IOSession();

        // Get instance data.
        $instanceData = $remote->getInstanceData();

        // Store to session.
        $session->instanceData = $instanceData;
        $session->remoteUrl = $url;
        $session->securityKey = $key;
        $session->save();

        // Send the session back to the client.
        Ajax::jsonResponse( $session );
    }

    /**
     * Downloads a remote data file.
     */
    public function download_remote_resource()
    {
        // Get resource url that we need to download.
        $resourceUrl = $_POST['url'];

        // Load session
        $session = new IOSession( $_POST['sessionId'] );

        // File manager.
        $files = new FileManager;
        $files->useCustomPath( 'io-data/import/' . $session->sessionId );

        $newUrl = $files->download( $resourceUrl );

        Ajax::jsonResponse( $newUrl );
    }

    /**
     * Returns an array of count, postIds, and postType.
     */
    public function get_post_manifest()
    {
        // Load session
        $session = new IOSession( $_POST['sessionId'] );

        // File manager.
        $files = new FileManager;
        $files->useCustomPath( 'io-data/import/' . $session->sessionId );

        // Post type
        $postType = $_POST['postType'];

        // Load posts data.
        $posts = $files->get( "{$postType}-posts.json", true );

        // Prepare an array of post IDs.
        $postIds = [];

        // Extract post IDs.
        foreach( $posts as $post ) {
            $postIds[] = $post->ID;
        }

        // Return count and post type.
        Ajax::jsonResponse([
            'count' => count($posts),
            'postIds' => $postIds,
            'postType' => $postType
        ]);
    }

    public function get_post_pivots()
    {
        // Load session
        $session = new IOSession( $_POST['sessionId'] );

        // File manager.
        $files = new FileManager;
        $files->useCustomPath( 'io-data/import/' . $session->sessionId );

        // Post type
        $postType = $_POST['postType'];

        // Load posts data.
        $data = $files->get( "posts_pivot.json", true );

        // Return count and post type.
        Ajax::jsonResponse( $data );
    }

    public function process_post_pivot()
    {
        // Load session
        $session = new IOSession( $_POST['sessionId'] );

        // Reference the post ID.
        $pivotData = $_POST['pivot'];

        $id1 = $pivotData['id_1'];
        $id2 = $pivotData['id_2'];

        $post1 = null;
        $post2 = null;

        // File manager.
        $files = new FileManager;
        $files->useCustomPath( 'io-data/import/' . $session->sessionId );

        // Post type
        $postType = $_POST['postType'];

        // Load posts data.
        $posts = $files->get( "{$postType}-posts.json", true );
        $postsMeta = $files->get( "{$postType}-posts-meta.json", true );

        // Declare place holders.
        $thePost = null;
        $thePostMeta = [];

        // Extract post.
        foreach( $posts as $post )
        {
            if ( $post->ID == $id1 )
            {
                $post1 = $post;
            }
            if ( $post->ID == $id2 ) {
                $post2 = $post;
            }
        }

        // Attach the new post ids.
        PostsPivot::attach( $post1->newPostId, $post2->newPostId );

        // Return response.
        Ajax::jsonResponse( true );
    }
    
    public function import_post()
    {

        // Load session
        $session = new IOSession( $_POST['sessionId'] );

        // Reference the post ID.
        $postId = $_POST['postId'];

        // File manager.
        $files = new FileManager;
        $files->useCustomPath( 'io-data/import/' . $session->sessionId );

        // Post type
        $postType = $_POST['postType'];

        // Load posts data.
        $posts = $files->get( "{$postType}-posts.json", true );
        $postsMeta = $files->get( "{$postType}-posts-meta.json", true );

        // Declare place holders.
        $thePost = null;
        $thePostMeta = [];


        // Extract post.
        foreach( $posts as $post )
        {
            if ( $post->ID == $postId )
            {
                $thePost = $post;
            }
        }

        // Extract post meta.
        foreach( $postsMeta as $meta )
        {
            if ( $postId == $meta->post_id )
            {
                $thePostMeta[] = $meta;
            }
        }
        
        // Create post importer.
        $importer = new IOPostImporter();
        $importer->viaSession( $session->sessionId, $thePost, $thePostMeta );
        $importer->import();

        Ajax::jsonResponse( $importer );
    }
}