<?php
class PostDumperTest extends TestCase
{
    /**
     * @var \Zawntech\WordPress\IO\PostDumper
     */
    public $dumper;

    public function setUp()
    {
        $this->dumper = new \Zawntech\WordPress\IO\PostDumper;
    }
    public function testCanGetPostTypes()
    {
        $postTypes = $this->dumper->getPostTypes();
        //dd( $postTypes );
        $this->assertGreaterThan( 0, count($postTypes) );
    }

    public function testCanDumpPostTypes()
    {
        $this->dumper->dump();
        $list = $this->dumper->getJsonList();
        //dd( $list );
        $this->assertGreaterThan( 0, count($list) );
    }
}