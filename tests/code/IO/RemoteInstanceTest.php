<?php
class RemoteInstanceTest extends TestCase
{
    public function testCanCreateRemoteInstance()
    {
        $url = 'http://wordpress-helpers.wp';
        $key = 'e20daaf9a0e7b3a7f6d6d66524ab85a1';
        $remote = new \Zawntech\WordPress\IO\RemoteInstance($url, $key);

        // Try to connect
        $this->assertTrue( $remote->canConnect() );
    }
}