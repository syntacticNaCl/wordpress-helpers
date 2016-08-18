<?php
class LoggerTest extends TestCase
{
    public function testCanLogData()
    {
        \Zawntech\WordPress\Utility\Logger::log('Hello, world!');
    }
}