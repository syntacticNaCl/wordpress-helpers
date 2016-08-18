<?php
namespace Zawntech\WordPress\IO\Ajax;

use Zawntech\WordPress\IO\RemoteInstance;
use Zawntech\WordPress\IO\SecurityKey;

trait IOAjaxLocalTrait
{
    public function update_settings()
    {
        // Validate data.
        if (!isset($_POST['settings']['securityKey'])) {
            throw new \Exception('Cannot update options, no security key supplied.');
        }

        // Update the security key.
        $newKey = SecurityKey::setKey($_POST['settings']['securityKey']);

        echo json_encode($newKey);
    }

    public function reset_security_key()
    {
        echo json_encode(SecurityKey::setKey());
    }

    public function can_connect_to_remote()
    {
        $url = $_POST['remoteUrl'];
        $key = $_POST['remoteSecurityKey'];

        // Make remote.
        $remote = new RemoteInstance($url, $key);

        echo json_encode([
            'connected' => $remote->canConnect(),
            'error' => $remote->getConnectionError()
        ]);
    }

    public function get_remote_data()
    {
        $url = $_POST['remoteUrl'];
        $key = $_POST['remoteSecurityKey'];

        // Make remote.
        $remote = new RemoteInstance($url, $key);

        echo json_encode( $remote->getInstanceData() );
    }
}