<?php
namespace Zawntech\WordPress\Users;

class UserModel
{
    public $userId;

    public $login;
    public $email;
    public $firstName;
    public $lastName;

    public function __construct($userId)
    {
        $this->userId = $userId;

        // Get user data by ID.
        $userData = get_user_by( 'ID', $userId );


        // Assign data internally.
        $this->login = $userData->user_login;
        $this->email = $userData->user_email;
        $this->firstName = $userData->first_name;
        $this->lastName = $userData->last_name;
    }
}