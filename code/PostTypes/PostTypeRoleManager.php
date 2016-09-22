<?php
namespace Zawntech\WordPress\PostTypes;

class PostTypeRoleManager
{
    /**
     * @var PostType
     */
    private $postType;

    public function _getAdminCapabilities()
    {
        return $this->postType->getCustomCapabilities();
    }

    /**
     * Hook this post type's custom capabilities.
     */
    public function addCustomCapabilities()
    {
        $map = [
            'administrator' => $this->_getAdminCapabilities()
        ];

        // Loop through map capabilities.
        foreach( $map as $roleKey => $capabilities )
        {
            // Get the role by key.
            $role = get_role( $roleKey );

            // Throw error if invalid role.
            if ( null === $role ) {
                throw new \Exception("Invalid role key specified: {$roleKey}!");
            }

            // Loop through capabilities.
            foreach( $capabilities as $capabilityKey => $capability )
            {
                // Add the custom capability.
                $role->add_cap( $capability );
            }
        }
    }

    /**
     * Remove this post type's custom capabilities.
     */
    public function removeCustomCapabilities()
    {
        $map = [
            'administrator' => $this->_getAdminCapabilities()
        ];

        // Loop through map capabilities.
        foreach( $map as $roleKey => $capabilities )
        {
            // Get the role by key.
            $role = get_role( $roleKey );

            // Throw error if invalid role.
            if ( null === $role ) {
                throw new \Exception("Invalid role key specified: {$roleKey}!");
            }

            // Loop through capabilities.
            foreach( $capabilities as $capabilityKey => $capability )
            {
                // Add the custom capability.
                $role->remove_cap( $capability );
            }
        }
    }

    public function __construct(PostType $postType)
    {
        // Assign the instantiating post type internally.
        $this->postType = $postType;
    }
}