<?php
namespace Zawntech\WordPress;

class ListTableFilters
{
    protected $postType;

    protected $columns = [
    ];

    protected $unset = [];

    protected function printDeclaredColumns(&$columns)
    {
        foreach( $this->columns as $columnKey => $label )
        {
            $columns[$columnKey] = $label;
        }
    }

    public function header($columns)
    {
        // Remove columns defined in $unset.
        foreach( $this->unset as $column )
        {
            unset( $columns[$column] );
        }

        // Print the columns defined in $columns.
        $this->printDeclaredColumns($columns);

        return $columns;
    }

    public function columns($columnName, $postId)
    {
        // Extend in sub classes.
    }

    public function sortableColumns($columns)
    {
        return $columns;
    }

    /**
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

        add_action( 'pre_get_posts', [$this, 'preQuery'], 1 );

        // Hook columns into the quick editor.
        $quickEditor = QuickEditor::getInstance();

        foreach( $this->columns as $key => $label )
        {
            $quickEditor->addColumn($key, $label);
        }
    }
}