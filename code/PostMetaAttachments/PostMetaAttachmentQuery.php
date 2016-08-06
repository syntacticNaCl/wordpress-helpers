<?php
namespace Zawntech\WordPress\PostMetaAttachments;

class PostMetaAttachmentQuery
{
    /**
     * @param $postId
     * @param $metaKey
     * @return bool|mixed
     * @throws \Exception
     */
    public static function getAttachments($postId, $metaKey)
    {
        // Get attachments
        $attachments = static::getMultipleAttachments($postId, [$metaKey]);

        if ( count($attachments) > 0 ) {
            return $attachments[0];
        }

        return false;
    }

    /**
     * Returns an array of [[ 'value' => ..., 'type' => ... ]] meta key attachment models.
     * @param $postId
     * @param $metaKeys
     * @throws \Exception
     * @return array
     */
    public static function getMultipleAttachments($postId, $metaKeys)
    {
        // Verify that a post ID was supplied.
        if ( ! $postId || '' === $postId )
        {
            throw new \Exception("A post ID is required.");
        }

        if ( empty( $metaKeys ) )
        {
            throw new \Exception("An array of meta keys is required.");
        }

        // Declare output.
        $output = [];

        // Loop through attachment keys.
        foreach( $metaKeys as $key )
        {
            // Get the meta value.
            $metaValue = get_post_meta($postId, $key, true);

            // Get the 'type' of attachment storage (ie, 'wp' or 'url').
            $metaType = get_post_meta($postId, $key . '_type', true);

            if ( false != $metaValue && false != $metaType )
            {
                $output[] = [
                    'value' => $metaValue,
                    'type' => $metaType
                ];
            }
        }

        return $output;
    }
    
    public static function getAttachmentModel($postId, $metaKey)
    {
        $attachmentData = static::getAttachments($postId, $metaKey);

        // Get post meta.
        $meta = $attachmentData['value'];

        // Get attachment source.
        $attachmentSource = $attachmentData['type'];

        if ( 'url' === $attachmentSource ) {
            return json_decode($meta);
        }

        // There are no attached IDs, or this is a URL type, so return an empty array.
        if ( '' === $meta )
        {
            return [];
        }

        // If a comma is found in the $meta string, then split by comma, otherwise
        // cast the returned meta as an array.
        $postIds = false === strpos($meta, ',') ? [$meta] : explode(',', $meta);

        // Is first value null?
        if ( is_array($postIds) && null === $postIds[0] ) {
            return [];
        }

        // Declare an array for output.
        $data = [];


        if ( empty( $postIds ) ) {
            return [];
        }

        // Loop through the post Ids
        foreach( $postIds as $postID )
        {
            $postData = get_post($postID);
            $attachmentData = wp_get_attachment_metadata($postID);

            $newItem = [
                'id' => $postID,
                'title' => $postData->post_title,
                'meta' => $attachmentData,
                'filename' => basename( get_attached_file( $postID ) )
            ];

            // Get thumbnail url.
            $thumb = wp_get_attachment_thumb_url($postID);

            // Push thumbnail.
            if ( false != $thumb )
            {
                $newItem['sizes'] = [
                    'thumbnail' => [
                        'url' => wp_get_attachment_thumb_url($postID)
                    ]
                ];
            }

            // Push data.
            $data[] = $newItem;
        }

        return $data;
    }

    /**
     * @param $postId integer
     * @param $metaKey string
     * @param $value string
     * @param $type string
     */
    public static function setAttachments($postId, $metaKey, $value, $type)
    {
        update_post_meta($postId, $metaKey, $value);
        update_post_meta($postId, $metaKey. '_type', $type);
    }

    /**
     * @param $postId
     * @param $metaKey
     * @return mixed|string
     */
    public static function getAttachmentType($postId, $metaKey)
    {
        $meta = get_post_meta($postId, $metaKey . '_type', true);
        return false === $meta ? 'wp' : $meta;
    }
}