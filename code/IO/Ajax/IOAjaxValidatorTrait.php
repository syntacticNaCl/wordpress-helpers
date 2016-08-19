<?php
namespace Zawntech\WordPress\IO\Ajax;

use Zawntech\WordPress\IO\SecurityKey;
use Zawntech\WordPress\Utility\Ajax;

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
            Ajax::jsonError('Error: No security key supplied.');
        }

        // Is the access key valid?
        if ( $_GET[$this->securityKeyLabel] !== SecurityKey::getKey() )
        {
            Ajax::jsonError('Error: Invalid security key supplied.');
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
            Ajax::jsonError('Error: No security nonce supplied.');
        }

        // Verify the supplied nonce is correct.
        if ( ! wp_verify_nonce( $_POST['nonce'], 'io-update-settings' ) )
        {
            Ajax::jsonError('Error: Invalid security key supplied.');
        }
    }
}