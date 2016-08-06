<?php
namespace Zawntech\WordPress\Utility;

class Validator
{
    /**
     * Verify that an array has all of a set of required keys
     * @param array $args
     * @param array $requiredKeys
     * @return boolean
     */
    public static function hasRequiredKeys( $args = [], $requiredKeys = [] )
    {
        // Check if the supplied arguments array is an object.
        if ( is_object($args) )
        {
            // Cast $args as array.
            $args = (array) $args;
        }

        // Loop through supplied arguments.
        foreach( $args as $arg )
        {
            if ( ! in_array( $arg, $requiredKeys ) )
            {
                return false;
            }
        }

        return true;
    }
}