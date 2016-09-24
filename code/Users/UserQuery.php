<?php
namespace Zawntech\WordPress\Users;

class UserQuery
{
    /**
     * Determines if a role is assigned to a given user.
     * @param $userId
     * @param string $role
     * @return bool
     */
    public static function userHasRole($userId, $role = '')
    {
        // Get user by ID.
        $user = get_user_by( 'ID', $userId );

        // Reference the user roles.
        $userRoles = $user->roles;

        if ( in_array( $role, $userRoles ) ) {
            return true;
        }

        return false;
    }

    /**
     * @return UserModel[]
     */
    public static function all()
    {
        global $wpdb;

        // Get all users from database.
        $sql = "SELECT * FROM {$wpdb->users};";
        $results = $wpdb->get_results( $sql );
        $output = [];

        foreach( $results as $result )
        {
            $output[] = new UserModel( $result->ID );
        }

        return $output;
    }
}