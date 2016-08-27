<?php
/**
 * We can extend the PostType class to register a new post type.
 * This file is in the global namespace, so we pull it in via
 * the full namespace path.
 */
use Zawntech\WordPress\PostTypes\PostType;

/**
 * Let's say we want to create a post type for books. To do so,
 * we create a new class extending PostType; BookPostType.
 */
class _BookPostType extends PostType
{
    /**
     * The idea is to overwrite class constants that define common components of
     * a post type, for example a post type key, like the default 'post', the post
     * type's slug rewrite, singular and plural labels, text domain.
     *
     * Let's define the constant values that make sense for a book.
     */
    const KEY = 'book';
    const SLUG = 'books';
    const SINGULAR = 'Book';
    const PLURAL = 'Books';
    const TEXT_DOMAIN = 'textdomain';
    const ENTER_TITLE_HERE = 'Book title';
    const MENU_NAME = 'Books';
}