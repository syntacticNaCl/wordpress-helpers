<?php
class RemoteInstanceTest extends TestCase
{
    public $remoteUrl;
    public $remoteKey;

    /**
     * @var \Zawntech\WordPress\IO\RemoteInstance
     */
    public $remote;

    public function setUp()
    {
        $url = 'http://wordpress-helpers.wp';
        $key = 'f041ae71d4c922afe1ab8f90577f3a0a';
        $remote = new \Zawntech\WordPress\IO\RemoteInstance($url, $key);

        $this->remoteUrl = $url;
        $this->remoteKey = $key;
        $this->remote = $remote;
    }

    public function testCanCreateRemoteInstance()
    {
        // Try to connect
        $this->assertTrue( $this->remote->canConnect() );
    }

    public function testCanGetRemoteInstanceData()
    {
        //dump( $this->remote->getInstanceData() );
    }
}