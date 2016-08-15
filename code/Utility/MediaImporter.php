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
     * @var
     */
    protected static $fileName;
    protected static $contentType;
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
        $response = $http->get($url);

        // Set file name internally.
        static::$fileName = preg_replace('/^.+\\\\/', '', $url);
        static::$contentType = $response['headers']['content-type'];
        static::$uploadPath = $basePath . '/' . $response['filename'];

        // Store the file.
        file_put_contents( static::$uploadPath, $response['body'] );

        // Return the upload path.
        return static::$uploadPath;
    }

    /**
     * @param $url
     * @param null $title
     * @param int $attachTo
     * @return int
     */
    public static function import($url, $title=null, $attachTo=0)
    {
        // Download the URL.
        $uploadPath = static::download($url);

        $attachment = [
            'post_mime_type' => static::$contentType,
            'post_title'     => $title || static::$fileName,
            'post_content'   => '',
            'post_status'    => 'publish'
        ];

        // Insert the attachment.
        $attach_id = wp_insert_attachment( $attachment, $uploadPath, $attachTo );

        require_once( ABSPATH . 'wp-admin/includes/image.php' );

        // Generate the metadata for the attachment, and update the database record.
        $attach_data = wp_generate_attachment_metadata( $attach_id, $uploadPath );

        // Update meta data.
        wp_update_attachment_metadata( $attach_id, $attach_data );

        return $attach_id;
    }
}