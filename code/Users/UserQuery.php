<?php
namespace Zawntech\WordPress\Users;

class UserQuery
{
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