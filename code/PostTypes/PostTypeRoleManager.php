<?php
namespace Zawntech\WordPress\PostTypes;

class PostTypeRoleManager
{
    /**
     * @var PostType
     */
    private $postType;

    /**
     * @return array
     */
    public function _getAdminCapabilities()
    {
        return $this->postType->getCustomCapabilities();
    }

    /**
     * @return array
     */
    public function _getEditorCapabilities()
    {
        return $this->postType->getCustomCapabilities();
    }

    /**
     * @return array
     */
    public function _getAuthorCapabilities()
    {
        // Declare an output array for our author capabilities.
        $output = [];

        // Declare the capability keys we want.
        $authorKeys = [
            'edit_public_posts',
            'publish_posts',
            'delete_published_posts',
            'edit_posts',
            'delete_posts'
        ];

        // Get custom capabilities.
        $capabilities = $this->postType->getCustomCapabilities();

        // Loop through capabilities.
        foreach( $capabilities as $capabilityKey => $capability )
        {
            // Is this capability in our defined $authorKeys array?
            if ( in_array( $capabilityKey, $authorKeys ) )
            {
                // Push this custom capability to the output array.
                $output[$capabilityKey] = $capability;
            }
        }

        // Add the ability to upload files.
        $output['upload_files'] = 'upload_files';

        // Return prepared array.
        return $output;
    }

    /**
     * Hook this post type's custom capabilities.
     * @param array $customRoleMap A key pair map of of custom role keys to default
     * WordPress role keys, for example, if you have a custom role of [{ROLE_KEY} => {DEFAULT_WP_ROLE}, ...]
     * Valid default keys for mapping: 'administrator', 'editor', 'author'
     * @throws \Exception
     */
    public function addCustomCapabilities( $customRoleMap = [] )
    {
        // Get our capabilities by roles.
        $map = [
            'administrator' => $this->_getAdminCapabilities(),
            'editor' => $this->_getEditorCapabilities(),
            'author' => $this->_getAuthorCapabilities()
        ];

        // Process the supplied custom post type map.
        if ( ! empty( $customRoleMap ) )
        {
            // A list of valid roles to check against.
            $validDefaultRoles = array_keys( $map );

            // Loop through the custom role map.
            foreach( $customRoleMap as $customKey => $defaultKey )
            {
                // Clean key.
                $defaultKey = strtolower( $defaultKey );

                // Check if this is a valid map.
                if ( ! in_array( $defaultKey, $validDefaultRoles ) ) {
                    throw new \Exception("Invalid default WordPress role '{$defaultKey}' supplied for default key in custom role map!");
                }

                // Push mapping.
                $map[$customKey] = $map[$defaultKey];
            }
        }

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