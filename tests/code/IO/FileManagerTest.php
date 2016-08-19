<?php
class FileManagerTest extends TestCase
{
    public function testCanPutFile()
    {
        $files = new \Zawntech\WordPress\IO\FileManager();
        $files->useCustomPath('phpunit');
        $files->put('test.txt', 'hello world');
    }

    public function testCanGetFile()
    {
        $files = new \Zawntech\WordPress\IO\FileManager();
        $files->useCustomPath('phpunit');
        $data = $files->get('test.txt');
        $this->assertEquals('hello world', $data);
    }
}