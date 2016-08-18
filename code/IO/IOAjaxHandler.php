<?php
namespace Zawntech\WordPress\IO;

use Zawntech\WordPress\Utility\Ajax;

class IOAjaxHandler
{
    public function dump_posts()
    {
        echo json_encode("hello world");
    }

    public function update_settings()
    {
        // Validate data.
        if ( ! isset( $_POST['settings']['securityKey'] ) )
        {
            throw new \Exception('Cannot update options, no security key supplied.');
        }

        // Update the security key.
        $newKey = SecurityKey::setKey($_POST['settings']['securityKey']);

        echo json_encode( $newKey );
    }

    public function reset_security_key()
    {
        echo json_encode( SecurityKey::setKey() );
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

    protected $securityKeyLabel = 'securityKey';

    protected function getSecurityKey()
    {
        return SecurityKey::getKey();
    }

    /**
     * Verify security token.
     */
    protected function verifyPublicAccess()
    {
        // Is the security key specified in the request string?
        if ( ! isset( $_GET[$this->securityKeyLabel] ) )
        {
            echo json_encode('Error: No security key supplied.');
            exit;
        }

        // Is the access key valid?
        if ( $_GET[$this->securityKeyLabel] !== $this->getSecurityKey() )
        {
            echo json_encode('Error: Invalid security key supplied.');
            exit;
        }
    }

    /**
     * Verify security nonce.
     */
    protected function verifyPrivateAccess()
    {
        // Verify that a nonce was supplied.
        if ( ! isset( $_POST['nonce'] ) )
        {
            echo json_encode('Error: No security nonce supplied.');
            exit;
        }

        // Verify the supplied nonce is correct.
        if ( ! wp_verify_nonce( $_POST['nonce'], 'io-update-settings' ) )
        {
            echo json_encode('Error: Invalid security key supplied.');
            exit;
        }
    }
    
    public function __construct()
    {
        // Define ajax calls.
        $publicAjaxCalls = [
            'dump_posts',

        ];

        // Define private ajax calls.
        $privateAjaxCalls =[
            'update_settings',
            'reset_security_key'
        ];

        // Loop through defined ajax calls.
        foreach( $publicAjaxCalls as $function )
        {
            // Bind a closure.
            add_action( "wp_ajax_nopriv_io_{$function}", function() use ($function)
            {
                Ajax::setHeaders();
                $this->verifyPublicAccess();
                $this->$function();
                exit;
            });
        }

        foreach( $privateAjaxCalls as $function )
        {
            // Bind a closure.
            add_action( "wp_ajax_io_{$function}", function() use ($function)
            {
                Ajax::setHeaders();
                $this->verifyPrivateAccess();
                $this->$function();
                exit;
            });
        }
    }
}