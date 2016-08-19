<?php
class PostDumperTest extends TestCase
{
    /**
     * @var \Zawntech\WordPress\IO\IODataDumper
     */
    public $dumper;

    public function setUp()
    {
        $this->dumper = new \Zawntech\WordPress\IO\IODataDumper;
    }
    
    public function testCanGetPostTypes()
    {
        $postTypes = $this->dumper->getPostTypes();
    }

    public function testCanDumpData()
    {
        // Build json, get list.
        $jsonList = $this->dumper->exportToJson();

        // The list should not be empty.
        $this->assertNotEmpty( $jsonList );

        //dump( $jsonList );
    }

    public function testCanPurgeJsonFiles()
    {
        // Purge files.
        $this->dumper->purgeFiles();

        $this->assertEmpty( $this->dumper->getJsonList() );
    }
}