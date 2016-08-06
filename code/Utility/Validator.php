<?php
namespace Zawntech\WordPress\Utility;

class Validator
{
    /**
     * @var string
     */
    protected static $error;

    /**
     * Verify that an array has all of a set of required keys
     * @param array $args
     * @param array $requiredKeys
     * @return boolean
     */
    public static function hasRequiredKeys( $args = [], $requiredKeys = [] )
    {
        // Get keys.
        $keys = array_keys( $args );

        // Loop through supplied arguments.
        foreach( $keys as $arg )
        {
            if ( ! in_array( $arg, $requiredKeys ) )
            {
                // Set error message.
                static::$error = "Argument key: '$arg' is not supplied.";

                // Return boolean.
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public static function getError()
    {
        return static::$error;
    }
}