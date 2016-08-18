<?php
class RemoteInstanceTest extends TestCase
{
    public function testCanCreateRemoteInstance()
    {
        $url = 'http://wordpress-helpers.wp';
        $key = 'ccb1dba43c861a23cef7c252b4ca6f6f';
        $remote = new \Zawntech\WordPress\IO\RemoteInstance($url, $key);

        // Try to connect
        $this->assertTrue( $remote->canConnect() );
    }
}