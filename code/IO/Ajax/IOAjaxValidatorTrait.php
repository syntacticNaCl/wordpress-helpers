<?php
namespace Zawntech\WordPress\IO\Ajax;

use Zawntech\WordPress\IO\SecurityKey;

trait IOAjaxValidatorTrait
{
    /**
     * @var string
     */
    protected $securityKeyLabel = 'securityKey';

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
        if ( $_GET[$this->securityKeyLabel] !== SecurityKey::getKey() )
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
}