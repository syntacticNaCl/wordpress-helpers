<?php
namespace Zawntech\WordPress\PostMetaAttachments;

class PostMetaAttachmentQuery
{
    /**
     * Returns an array of [[ 'value' => ..., 'type' => ... ]] meta key attachment models.
     * @param $postId
     * @param $metaKeys
     * @throws \Exception
     * @return array
     */
    public static function getAttachments($postId, $metaKeys)
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
}