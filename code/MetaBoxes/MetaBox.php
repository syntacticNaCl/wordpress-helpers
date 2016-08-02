<?php
namespace Zawntech\WordPress\MetaBoxes;

/**
 * Usage:
 *
 * 1) Extend the MetaBox class, ie: class ExampleMetaBox extends MetaBox {}.
 *
 * 2) Override the following protected properties:
 *    - $id (a string for element ID)
 *
 *    - $title (the title of the meta box)
 *
 *    - $postTypes (an array of post type keys to which this meta box will be attached)
 *
 *    - $metaKeys (an array of post meta keys associated with this meta box, used to automatically save form
 *      data whose name attributes match the meta key. For example, if 'some_key' is in the $metaKeys array
 *      and <input name="some_key"> is in the metabox render() view, "some_key" will automatically update).
 *
 *    - $viewModel (a string or array of strings linking javascripts we should enqueue for this meta box)
 *
 *    - $viewModelPreloadVar (a string, if defined, will expose this meta box's preload data)
 *
 * 3) Override the render() method to print desired template.
 *
 * Class MetaBox
 * @package Zawntech\WordPress
 */
class MetaBox extends MetaBoxAbstract
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