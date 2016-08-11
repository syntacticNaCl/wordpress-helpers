<?php
namespace Zawntech\WordPress\Utility;

class ListTableFilters
{
    /**
     * @var string The post type list table to be filtered.
     */
    protected $postType;

    /**
     * @var array A key pair value of column keys => column labels.
     */
    protected $columns = [
    ];

    /**
     * @var array An array of quick editor columns keys.
     */
    protected $quickEditorColumns = [
    ];

    /**
     * @var array A list of (default) column keys to unset/remove from display.
     */
    protected $unset = [];

    /**
     * Used in an automated hook to print custom columns defined in $this->columns.
     * @param $columns
     */
    protected function printDeclaredColumns(&$columns)
    {
        foreach( $this->columns as $columnKey => $label )
        {
            $columns[$columnKey] = $label;
        }
    }

    /**
     * Automated hook to "manage_{$this->postType}_posts_columns", which unsets
     * columns defined in $this->unset, and prints the columns declared in $this->columns.
     * @param $columns
     * @return mixed
     */
    public function header($columns)
    {
        // Remove columns defined in $unset.
        foreach( $this->unset as $column )
        {
            unset( $columns[$column] );
        }

        // Print the columns defined in $columns.
        $this->printDeclaredColumns($columns);

        // Return the columns.
        return $columns;
    }

    /**
     * Automated hook to "manage_{$this->postType}_posts_custom_column",
     * this function should be overridden in extending child classes.
     * @param $columnName
     * @param $postId
     */
    public function columns($columnName, $postId)
    {
        // Extend in sub classes.
    }

    /**
     * Automated hook to "manage_edit-{$this->postType}_sortable_columns".
     * @param $columns
     * @return mixed
     */
    public function sortableColumns($columns)
    {
        return $columns;
    }

    /**
     * Hooks basic asc/desc sorting on simple meta_key orderBy queries.
     * @param $query \WP_Query
     */
    public function preQuery($query)
    {
        if ( $query->is_main_query() && ( $orderby = $query->get( 'orderby' ) ) )
        {
            if ( isset($this->columns[$orderby] ) )
            {
                $query->set( 'meta_key', $orderby );
                $query->set( 'orderby', 'meta_value' );
            }
        }
    }

    public function __construct()
    {
        // Filter column headers.
        add_filter( "manage_{$this->postType}_posts_columns" , [$this, 'header'] );

        // Hook column content.
        add_action( "manage_{$this->postType}_posts_custom_column" , [$this, 'columns'], 10, 2 );

        // Hook
        add_filter( "manage_edit-{$this->postType}_sortable_columns", [$this, 'sortableColumns'] );

        // Hook pre_get_posts to apply sort filters.
        add_action( 'pre_get_posts', [$this, 'preQuery'], 1 );

        // Hook columns into the quick editor.
        $quickEditor = QuickEditor::getInstance();

        // Hook custom columns into the QuickEditor.
        foreach( $this->columns as $key => $label )
        {
            // Check if this $key exists in the quick editor columns array.
            if ( in_array( $key, $this->quickEditorColumns ) )
            {
                $quickEditor->addColumn($key, $label);
            }
        }
    }
}