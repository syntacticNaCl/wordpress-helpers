<?php
namespace Zawntech\WordPress\Taxonomies;

/**
 * Provides an extensible class for registering custom taxonomies to post type(s).
 *
 * Class Taxonomy
 * @package Zawntech\WordPress\Taxonomies
 */
class Taxonomy
{
    protected $key;
    protected $slug;
    protected $singular;
    protected $plural;
    protected $menuName;

    protected $hierarchical = false;
    protected $showUi = true;
    protected $showAdminColumn = true;
    protected $queryVar = true;

    protected $postTypes = [];

    protected function validationTaxonomy()
    {
        // Class name.
        $className = static::class;

        // Required keys.
        $keys = [
            'key', 'slug', 'singular', 'plural'
        ];

        // Loop through required keys.
        foreach( $keys as $key )
        {
            // Verify that the current iteration is specified.
            if ( null === $this->{$key} )
            {
                throw new \Exception("No \${$key} specified in class {$className}.");
            }
        }
    }

    /**
     * @return array Taxonomy labels.
     */
    protected function getLabels()
    {
        return [
            'name'              => _x( $this->singular, 'taxonomy general name' ),
            'singular_name'     => _x( $this->singular, 'taxonomy singular name' ),
            'search_items'      => __( 'Search ' . $this->plural ),
            'all_items'         => __( 'All ' . $this->plural ),
            'parent_item'       => __( 'Parent ' . $this->plural ),
            'parent_item_colon' => __( 'Parent ' . $this->plural . ':' ),
            'edit_item'         => __( 'Edit ' . $this->singular ),
            'update_item'       => __( 'Update ' . $this->singular ),
            'add_new_item'      => __( 'Add New ' . $this->singular ),
            'new_item_name'     => __( 'New ' . $this->singular . ' Name' ),
            'menu_name'         => __( $this->menuName ?: $this->plural ),
        ];
    }

    /**
     * @var bool Keep track of taxonomy registration.
     */
    protected static $registered = false;

    /**
     * Hook WordPress.
     */
    public function hook()
    {
        // If we've already hooked the taxonomy, do nothing.
        if ( static::$registered ) {
            return;
        }

        // Define arguments.
        $args = [
            'hierarchical' => $this->hierarchical,
            'labels' => $this->getLabels(),
            'show_ui' => $this->showUi,
            'show_admin_column' => $this->showAdminColumn,
            'query_var' => $this->queryVar,
            'rewrite' => ['slug' => $this->slug],
        ];

        // Loop through post types.
        foreach ($this->postTypes as $postType)
        {
            register_taxonomy($this->key, $postType, $args);
        }

        // The taxonomy has been registered.
        static::$registered = true;
    }

    public function __construct()
    {
        // Validate taxonomy configuration.
        $this->validationTaxonomy();

        // Hook WordPress.
        add_action( 'init', [$this, 'hook'] );
    }
}