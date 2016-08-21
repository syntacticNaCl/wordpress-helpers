<?php
namespace Zawntech\WordPress\IO;

class IOMediaData
{
    /**
     * @var IOPostImporter
     */
    protected $importer;

    /**
     * @var string Public URL to media file.
     */
    public $url;

    /**
     * @var array A list of URLs with size variants.
     */
    public $urls = [];

    public $title;

    /**
     * @var string
     */
    public $mime;

    public $postId;
    public $newPostId;

    public function __construct(IOPostImporter $importer)
    {
        // Reference the importer.
        $this->importer = $importer;

        $this->postId = $importer->originalPostId;
        $this->newPostId = $importer->newPostId;

        $this->url = $importer->postData->guid;
        $this->mime = $importer->postData->post_mime_type;
        $this->title = $importer->postData->post_title;

        $attachedFile = null;

        // Loop through the post meta.
        foreach( $importer->postMeta as $item )
        {
            // Select attachment meta data.
            if ( '_wp_attachment_metadata' === $item->meta_key )
            {
                // Decode data.
                $imageData = unserialize( $item->meta_value );
            }

            // Select attached file data.
            if ( '_wp_attached_file' === $item->meta_key )
            {
                $attachedFile = $item->meta_value;
            }
        }

        // We couldn't load the image data from meta, so this maybe a PDF or other attachment.
        if ( ! isset( $imageData ) || ! isset( $imageData['file'] ) )
        {
            // Something went wrong.
            if ( ! $attachedFile )
            {
                throw new \Exception( 'Unable to get _wp_attachment_metadata' );
            }

            else
            {
                $this->urls['none'] = $this->url;
            }

            // We're done.
            return;
        }

        // Process image attachment.
        else
        {
            // For example: '2016/07/IMG_0332.jpg'
            $relativeFilePath = $imageData['file'];

            // Trim the 'YYYY/MM' section of the relative path.
            $filename = substr( $relativeFilePath, strrpos( $relativeFilePath, '/' ) + 1 );

            // Image sizes.
            $sizes = $imageData['sizes'];

            // Base path to uploads.
            $basePath = wp_upload_dir()['basedir'] . '/';
            $baseUrl = wp_upload_dir()['baseurl'] . '/';

            // Loop through sizes.
            foreach( $sizes as $sizeKey => $sizeData )
            {
                // Append data.
                $this->urls[$sizeKey] = str_replace( $filename, $sizeData['file'], $this->url );
            }

            return;
        }
    }
}