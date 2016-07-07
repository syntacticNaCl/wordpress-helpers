<?php
namespace Zawntech\WordPress;

/**
 * Provides a utility for easily hooking into the quick editor.
 * Class QuickEditor
 * @package Zawntech\Projection\WP
 */
class QuickEditor
{
    /**
     * @var QuickEditor
     */
    protected static $instance;

    /**
     * @return QuickEditor
     */
    public static function getInstance()
    {
        if ( null === static::$instance ) {
            static::$instance = new static;
        }
        return self::$instance;
    }

    /**
     * @var array An array of column keys (meta keys) by labels.
     */
    protected $columns = [];

    /**
     * Adds a column name (which we expect to correspond to a meta key) and quick editor label.
     * @param $metaKey string
     * @param $label string
     * @return $this
     */
    public function addColumn($metaKey, $label)
    {
        $this->columns[$metaKey] = $label;
        return $this;
    }

    /**
     * Print the custom quick editor HTML.
     * @param $column_name
     * @param $post_type
     */
    public function render($column_name, $post_type)
    {
        static $printNonce = true;
        if ( $printNonce )
        {
            $printNonce = false;
            wp_nonce_field( 'quick-' . $post_type, 'quick_edit_nonce' );
        }
        ?>

        <fieldset class="inline-edit-col-left inline-edit-<?= $post_type; ?>">
            <div class="inline-edit-col column-<?php echo $column_name; ?>">
                <label class="inline-edit-group">
                    <?php
                    if ( isset( $this->columns[$column_name] ) )
                    {
                        ?>
                        <span class="title"><?= $this->columns[$column_name]; ?></span><input name="<?= $column_name ?>" />
                        <?php
                    }
                    ?>
                </label>
            </div>
        </fieldset>
    <?php
    }

    /**
     * Prints javascript to the edit post type menu page that grabs existing values by $columnName.
     * @see https://codex.wordpress.org/Plugin_API/Action_Reference/quick_edit_custom_box#Setting_Existing_Values
     * @return bool
     */
    public function javascript()
    {
        // Get the current screen
        $screen = get_current_screen();

        // Don't add script if not edit page.
        if ( false === strpos($screen->parent_file, 'edit.php') || ! isset( $_GET['post_type'] ) )
        {
            return;
        }

        ?>
        <script>
            jQuery(document).ready(function()
            {
                // Do nothing if not defined.
                if ( 'undefined' == typeof(inlineEditPost) ) {
                    return;
                }

                // we create a copy of the WP inline edit post function
                var $wp_inline_edit = inlineEditPost.edit;

                // and then we overwrite the function with our own code
                inlineEditPost.edit = function( id )
                {
                    // "call" the original WP edit function
                    // we don't want to leave WordPress hanging
                    $wp_inline_edit.apply( this, arguments );

                    // now we take care of our business

                    // get the post ID
                    var $post_id = 0;
                    if ( typeof( id ) == 'object' ) {
                        $post_id = parseInt( this.getId( id ) );
                    }

                    if ( $post_id > 0 ) {
                        // define the edit row
                        var $edit_row = jQuery( '#edit-' + $post_id );
                        var $post_row = jQuery( '#post-' + $post_id );

                        // get the data
                        <?php foreach( $this->columns as $key => $column ) : ?>
                        var $<?= $key ?> = jQuery( '.column-<?= $key ?>', $post_row ).text();
                        jQuery( ':input[name="<?= $key ?>"]', $edit_row ).val( $<?= $key ?> );
                        <?php endforeach; ?>
                    }
                };
            });
        </script>
        <?php
    }

    protected function __construct()
    {
        // Render the columns.
        add_action( 'quick_edit_custom_box', function($columnName, $postType)
        {
            // Render the column name/post type.
            $this->render($columnName, $postType);
        }, 10, 2 );

        // Hook our admin javascript (to footer, so assets are loaded at run time).
        add_action( 'admin_footer', [$this, 'javascript'], 10, 2 );
    }
}
