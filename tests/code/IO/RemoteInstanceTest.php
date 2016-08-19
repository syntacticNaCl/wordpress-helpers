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
        $key = '7b52b222358253fa4ddb18952f8b84ac';
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