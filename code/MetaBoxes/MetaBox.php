<?php
namespace Zawntech\WordPress\MetaBoxes;

/**
 * Class MetaBox
 * @package Zawntech\WordPress
 */
class MetaBox extends MetaBoxInterface
{
    public function save($postId)
    {
        // Determine if this is a quick edit update.
        $isQuickEdit = isset($_POST['quick_edit_nonce']);

        // Verify the nonce.
        if (
            ! $isQuickEdit &&
            (
                ! isset( $_POST[$this->getNonceName()] ) ||
                ! $this->verifyNonce( $_POST[$this->getNonceName()] )
            )
        ) {
            return;
        }

        // Verify quick edit nonce.
        if ( $isQuickEdit )
        {
            // Get post
            $post = get_post($postId);

            // Get the nonce value.
            $nonce = $_POST['quick_edit_nonce'];

            // The nonce key is prepared via 'quick-{$postType}' (see QuickEditor)
            $nonceKey = 'quick-' . $post->post_type;

            // Verify the nonce.
            if ( ! wp_verify_nonce($nonce, $nonceKey) ) {
                return;
            }
        }

        foreach( $this->metaKeys as $key ) {
            if ( isset( $_POST[$key] ) ) {
                update_post_meta( $postId, $key, $_POST[$key] );
            }
        }
    }

    public function __construct()
    {
        $this->validateClass();
        add_action( 'save_post', [$this, 'save'] );
        add_action( 'add_meta_boxes', [$this, 'register'] );
    }
}