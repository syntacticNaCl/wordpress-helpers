<?php
namespace Zawntech\WordPress\PostTypes;

/**
 * Class PostType
 * @package Zawntech\Projection\WP
 */
class PostType
{
    const KEY = null;
    const SLUG = null;
    const SINGULAR = null;
    const PLURAL = null;
    const TEXT_DOMAIN = null;
    const ENTER_TITLE_HERE = null;
    const MENU_NAME = null;
    

    protected $searchMeta = [];

    protected $supports = ['title', 'editor', 'author', 'thumbnail'];

    /**
     * @var array MetaBox[]
     */
    protected $metaBoxes = [];

    /**
     * Verify that the post type data is correctly defined, otherwise throw an error.
     * @throws \Exception
     */
    protected function _validatePostType()
    {
        // Declare a list of required constants on extending classes.
        $requiredConstants = [
            'KEY',
            'SLUG',
            'SINGULAR',
            'PLURAL'
        ];

        // Loop through the required constants.
        foreach( $requiredConstants as $constant )
        {
            // Declare a constant key for this iteration.
            $constantKey = static::class . '::' . $constant;

            // Throw an exception if not defined.
            if ( ! defined($constantKey) ) {
                throw new \Exception('Constant "' . $constantKey . '" is not defined in class ' . static::class);
            }
        }
    }

    protected $capabilities;

    protected function registerPostType()
    {
        add_action( 'init', function()
        {
            // Define the text domain.
            $textDomain = static::TEXT_DOMAIN ?: 'text_domain';

            $singular = static::SINGULAR;
            $plural = static::PLURAL;
            
            $menuName = static::MENU_NAME ?: $plural;

            // Prepare post type labels.
            $labels = [
                'name'               => _x( $plural, 'post type general name', $textDomain ),
                'singular_name'      => _x( $singular, 'post type singular name', $textDomain ),
                'menu_name'          => _x( $menuName, 'admin menu', $textDomain ),
                'name_admin_bar'     => _x( $menuName, 'add new on admin bar', $textDomain ),
                'add_new'            => _x( 'Add New', $singular, $textDomain ),
                'add_new_item'       => __( 'Add New ' . $singular . '', $textDomain ),
                'new_item'           => __( 'New ' . $singular . '', $textDomain ),
                'edit_item'          => __( 'Edit ' . $singular . '', $textDomain ),
                'view_item'          => __( 'View ' . $singular . '', $textDomain ),
                'all_items'          => __( 'All ' . $plural . '', $textDomain ),
                'search_items'       => __( 'Search ' . $plural . '', $textDomain ),
                'parent_item_colon'  => __( 'Parent ' . $plural . ':', $textDomain ),
                'not_found'          => __( 'No ' . strtolower($plural) . ' found.', $textDomain ),
                'not_found_in_trash' => __( 'No ' . strtolower($plural) . ' found in Trash.', $textDomain )
            ];

            $args = [
                'labels'             => $labels,
                'description'        => __( 'Description.', $textDomain ),
                'public'             => true,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'show_in_menu'       => true,
                'query_var'          => true,
                'rewrite'            => ['slug' => static::SLUG],
                //'capability_type'    => [strtolower(static::SINGULAR), strtolower(static::PLURAL)],
                'has_archive'        => true,
                'hierarchical'       => false,
                'menu_position'      => null,
                'supports'           => $this->supports
            ];

            register_post_type( static::KEY, $args );
        });
    }

    /**
     * Let's us hook meta keys into the search.
     */
    public function hookSearch()
    {
        // No search meta to hook...
        if ( empty( $this->searchMeta ) ) {
            return;
        }

        // Hook search query.
        add_action( 'pre_get_posts', function($query)
        {
            /** @var $query \WP_Query */

            if ( $query->is_main_query() && $query->is_search)
            {
                // Initialize the meta query array.
                $metaQuery = [
                    'relation' => 'OR'
                ];

                // Loop through the declared search meta keys.
                foreach( $this->searchMeta as $metaKey )
                {
                    // Add this meta ket
                    $metaQuery[] = [
                        'key' => $metaKey,
                        'value' => $_GET['s'],
                        'compare' => 'LIKE',
                    ];
                }

                // Set the meta query.
                $query->set('meta_query', $metaQuery);
            }
        });
    }

    public function __construct()
    {
        // Validate the class structure.
        $this->_validatePostType();

        // Register the post type.
        $this->registerPostType();

        // Filter the 'Enter title here' text on the post editor screen.
        if ( static::ENTER_TITLE_HERE ) {
            add_filter( 'enter_title_here', function($title) {

                // Get current screen.
                $screen = get_current_screen();

                // Match the extending post type against current screen.
                if ( static::KEY === $screen->post_type )
                {
                    return static::ENTER_TITLE_HERE;
                }

                // Return title.
                return $title;
            });
        }

        // Hook search results.
        // todo: implement search
        // $this->hookSearch();

        // Are there metaboxes defined?
        if ( ! empty( $this->metaBoxes ) )
        {
            // Loop through the supplied class names.
            foreach( $this->metaBoxes as $metaBoxClass )
            {
                // Instantiate the class.
                new $metaBoxClass;
            }
        }
    }
}