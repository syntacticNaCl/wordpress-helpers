<?php
namespace Zawntech\WordPress\Roles;

abstract class AbstractRole
{
    /**
     * Define the role key.
     */
    const ROLE_KEY = '';

    /**
     * Define role name.
     */
    const ROLE_NAME = '';

    /**
     * @return array A default set of capabilities.
     */
    public static function getCapabilities()
    {
        return [
            // Subscriber
            'read' => true
        ];
    }

    /**
     * Create role.
     */
    public static function createRole()
    {
        add_role( static::ROLE_KEY, static::ROLE_NAME, static::getCapabilities() );
    }

    /**
     * Remove role.
     */
    public static function removeRole()
    {
        remove_role( static::ROLE_KEY );
    }
}