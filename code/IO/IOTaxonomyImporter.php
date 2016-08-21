<?php
namespace Zawntech\WordPress\IO;

/**
 * Handles importing taxonomy term data for a given post ID.
 * Class IOTaxonomyImporter
 * @package Zawntech\WordPress\IO
 */
class IOTaxonomyImporter
{
    public $postId;
    protected $terms;
    protected $termsMeta;
    protected $termsTaxonomy;
    protected $termsRelationships;

    /**
     * @return array
     */
    protected function getTaxonomyTermIds()
    {
        $taxonomyTermIds = [];

        foreach( $this->termsRelationships as $relationship )
        {
            if ( $this->postId === (int) $relationship->object_id )
            {
                // Push term id.
                $taxonomyTermIds[] = $relationship->term_taxonomy_id;
            }
        }

        // Clean array.
        return array_unique( $taxonomyTermIds );
    }

    protected function getTermIds()
    {
        $termIds = [];
        foreach( $this->termsTaxonomy as $taxonomy )
        {
            if ( in_array( $taxonomy->term_taxonomy_id, $this->getTaxonomyTermIds() ) )
            {
                $termIds[] = (int) $taxonomy->term_id;
            }
        }

        return array_unique( $termIds );
    }

    public function getTerms()
    {
        // Declare terms
        $terms = [];

        foreach( $this->terms as $term )
        {
            if ( in_array( $term->term_id, $this->getTermIds() ) )
            {
                $terms[] = $term;
            }
        }

        // Loop through terms, recombine.
        foreach( $terms as &$curTerm )
        {
            foreach( $this->termsTaxonomy as $item )
            {
                if ( $item->term_id == $curTerm->term_id )
                {
                    $curTerm->taxonomy = $item->taxonomy;
                    $curTerm->parent = (int) $item->parent;
                }
            }
        }

        $parentIds = [];

        // Check terms for missing parents.
        foreach( $terms as $term )
        {
            $parentIds[] = (int) $term->parent;
        }

        foreach( $parentIds as $parentId )
        {
            if ( ! in_array( $parentId, $parentIds ) )
            {
                throw new \Exception("Parent ID not included!");
            }
        }

        return $terms;
    }

    public function insertTerms()
    {
        // Get terms.
        $terms = $this->getTerms();

        // Define an array of remaining terms.
        $remaining = [];

        // Inserted or updated taxonomy terms.
        $inserted = [];

        // Loop through parent terms.
        foreach( $terms as $key => $term )
        {
            // Insert parent level terms.
            if ( 0 == $term->parent )
            {
                $newTerm = wp_insert_term( $term->name, $term->taxonomy, [
                    'slug' => $term->slug
                ]);

                if ( is_a( $newTerm, \WP_Error::class ) )
                {
                    if ( isset( $newTerm->errors['term_exists'] ) ) {
                        $newTerm = (array) get_term_by( 'slug', $term->slug, $term->taxonomy );
                    }
                }
                
                $terms[$key]->newTermData = $newTerm;
                
                $inserted[] = $term;
            }

            else
            {
                $remaining[] = $term;
            }
        }

        // Insert remaining terms.
        while( ! empty( $remaining ) )
        {
            // Loop through the remaining posts.
            foreach( $remaining as $key => $remainingTerm )
            {
                // Loop through the inserted terms.
                foreach( $terms as $insertedTerm )
                {
                    // Match this iteration again an inserted term.
                    if ( $insertedTerm->term_id == $remainingTerm->parent )
                    {
                        $newTerm = wp_insert_term( $remainingTerm->name, $remainingTerm->taxonomy, [
                            'slug' => $remainingTerm->slug,
                            'parent' => $insertedTerm->newTermData['term_id']
                        ]);

                        if ( is_a( $newTerm, \WP_Error::class ) )
                        {
                            if ( isset( $newTerm->errors['term_exists'] ) ) {
                                $newTerm = (array) get_term_by( 'slug', $remainingTerm->slug, $remainingTerm->taxonomy );
                            }
                        }

                        // Assign to new item.
                        $remainingTerm->newTermData = $newTerm;

                        // Push to inserted.
                        $inserted[] = $remainingTerm;
                        
                        // Unset this item from remaining.
                        unset( $remaining[$key] );
                    }
                }
            }
        }

        // Declare an array of new IDs.
        $newIds = [];

        // Loop through inserted terms.
        foreach( $inserted as $item )
        {
            // Group new IDs by taxonomy type.
            if ( ! isset( $newIds[$item->taxonomy] ) )
            {
                $newIds[$item->taxonomy] = [];
            }

            // Push the new term id to this taxonomy type array.
            $newIds[$item->taxonomy][] = (int) $item->newTermData['term_id'];
        }

        // Loop through each type.
        foreach( $newIds as $taxonomy => $ids )
        {
            // Set the post terms for this taxonomy type.
            wp_set_object_terms( $this->postId, $ids, $taxonomy, false );
        }
    }

    public function __construct($sessionId, $postId)
    {
        // Set post ID.
        $this->postId = $postId;

        // Create file manager.
        $files = new FileManager();
        $files->useCustomPath("io-data/import/{$sessionId}");

        // Load taxonomy data to object.
        $this->termsMeta = $files->get('term-meta.json', true);
        $this->terms = $files->get('terms.json', true);
        $this->termsRelationships = $files->get('term-relationships.json', true);
        $this->termsTaxonomy = $files->get('term-taxonomy.json', true);
    }
}