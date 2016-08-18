<?php
namespace Zawntech\WordPress\IO\Ajax;

use Zawntech\WordPress\IO\IOSession;
use Zawntech\WordPress\IO\RemoteInstance;
use Zawntech\WordPress\IO\SecurityKey;
use Zawntech\WordPress\Utility\Ajax;

/**
 * Local AJAX functions on behalf of the local WordPress instance's client.
 *
 * Class IOAjaxLocalTrait
 * @package Zawntech\WordPress\IO\Ajax
 */
trait IOAjaxLocalTrait
{
    /**
     * Updates the IO Manager's settings (security key), returns the new key string to client.
     * @throws \Exception
     */
    public function update_settings()
    {
        // Validate data.
        if (!isset($_POST['settings']['securityKey'])) {
            throw new \Exception('Cannot update options, no security key supplied.');
        }

        // Update the security key.
        $newKey = SecurityKey::setKey($_POST['settings']['securityKey']);

        Ajax::jsonResponse( $newKey);
    }

    /**
     * Resets IO Manager security key to a random value and returns the string to client.
     */
    public function reset_security_key()
    {
        Ajax::jsonResponse( SecurityKey::setKey() );
    }

    /**
     * Tests if a given URL and security key are valid for establishing a connection to
     * a remote WordPress instance.
     */
    public function can_connect_to_remote()
    {
        $url = $_POST['remoteUrl'];
        $key = $_POST['remoteSecurityKey'];

        // Make remote.
        $remote = new RemoteInstance($url, $key);

        $canConnect = $remote->canConnect();
        $error = $remote->getConnectionError();

        if ( ! $error ) {
            Ajax::jsonResponse( $canConnect );
        } else {
            Ajax::jsonError( $error );
        }
    }

    /**
     * Fetches data about a remote WordPress instance.
     */
    public function get_remote_data()
    {
        $url = $_POST['remoteUrl'];
        $key = $_POST['remoteSecurityKey'];

        // Make remote.
        $remote = new RemoteInstance($url, $key);
        
        // Make remote session.
        $session = new IOSession();
        $session->start();

        // Get instance data.
        $instanceData = $remote->getInstanceData();

        // Store to session.
        $session->instanceData = $instanceData;
        $session->remoteUrl = $url;
        $session->securityKey = $key;

        // Send the session back to the client.
        Ajax::jsonResponse( $session );
    }
}