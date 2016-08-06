<?php
namespace Zawntech\WordPress\Traits;

trait ValidateParametersTrait
{
    /**
     * Verify that an array has all of a set of required keys
     * @param array $args
     * @param array $requiredKeys
     * @throws \Exception
     */
    protected function _validateRequiredKeys( $args = [], $requiredKeys = [] )
    {
        // Check if the supplied arguments array is an object.
        if ( is_object($args) )
        {
            // Cast $args as array.
            $args = (array) $args;
        }

        // Do nothing if there are no required keys.
        if ( [] === $requiredKeys || empty( $requiredKeys ) )
        {
            return;
        }

        // Loop through supplied arguments.
        foreach( $args as $arg )
        {
            if ( ! in_array( $arg, $requiredKeys ) )
            {
                // Get class name.
                $className = static::class;

                // Throw exception.
                throw new \Exception("Argument {$arg} missing in {$className}");
            }
        }
    }
}