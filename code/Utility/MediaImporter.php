<?php
namespace Zawntech\WordPress\Utility;

/**
 * A utility for importing remote media into the WordPress media library.
 * Class MediaImporter
 * @package Zawntech\WordPress\Utility
 */
class MediaImporter
{
    /**
     * @var string File name, determined after URL response is returned..
     */
    protected static $fileName;

    /**
     * @var string Mime content type, determined after URL response is returned.
     */
    protected static $contentType;

    /**
     * @var string /wp-content/uploads/YYYY/MM/downloaded-file.ext
     */
    protected static $uploadPath;

    /**
     * Downloads a given URL into the current /wp-content/uploads/YYYY/MM folder.
     * @param $url
     * @return string
     */
    protected static function download($url)
    {
        // Check path.
        $basePath = wp_upload_dir()['path'];

        // Make HTTP
        $http = new \WP_Http();

        // Get URL.
        $response = $http->get($url, [
            'timeout' => 300
        ]);

        // Set file name internally.
        static::$fileName = substr( $url, strrpos($url, '/') + 1 );
        static::$contentType = $response['headers']['content-type'];
        static::$uploadPath = $basePath . '/' . static::$fileName;

        // Store the file.
        file_put_contents( static::$uploadPath, $response['body'] );

        // Return the upload path.
        return static::$uploadPath;
    }

    /**
     * Downloads a URL into the WordPress Media Library.
     * @param $url
     * @param null $title
     * @param int $attachTo
     * @return integer WordPress Post ID.
     */
    public static function import($url, $title=null, $attachTo=0)
    {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // Download the URL.
        $uploadPath = static::download($url);

        $attachment = [
            'post_mime_type' => static::$contentType,
            'post_title'     => $title ?: static::$fileName,
            'post_content'   => '',
            'post_status'    => 'publish'
        ];

        // Insert the attachment.
        $attach_id = wp_insert_attachment( $attachment, $uploadPath, $attachTo );
        
        // Generate the metadata for the attachment, and update the database record.
        $attach_data = wp_generate_attachment_metadata( $attach_id, $uploadPath );

        // Update meta data.
        wp_update_attachment_metadata( $attach_id, $attach_data );

        return $attach_id;
    }
}