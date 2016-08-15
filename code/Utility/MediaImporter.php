<?php
namespace Zawntech\WordPress\Utility;

/**
 * A utility for importing remote media into the WordPress media library.
 * Class MediaImporter
 * @package Zawntech\WordPress\Utility
 */
class MediaImporter
{
    protected static $fileName;

    /**
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

        // File path
        $path = $basePath . '/' . $response['filename'];

        // Set file name internally.
        static::$fileName = $response['filename'];

        // Store the file.
        file_put_contents( $path, $response['body'] );

        // Return the upload path.
        return $path;
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
            'post_mime_type' => wp_check_filetype($uploadPath)['type'],
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