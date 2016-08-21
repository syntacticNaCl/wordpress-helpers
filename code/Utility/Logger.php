<?php
namespace Zawntech\WordPress\Utility;

class Logger
{
    /**
     * @var static
     */
    protected static $instance;

    public static function getInstance()
    {
        if ( null === static::$instance )
        {
            static::$instance = new static;
        }
        return static::$instance;
    }

    /**
     * @var string Absolute path to /storage/logs
     */
    protected $basePath = '/storage/logs';

    /**
     * @var string Path to logger directory.
     */
    protected $loggerDir = 'default';

    /**
     * @param $path string
     */
    protected function verifyPath($path)
    {
        // Set path.
        if ( ! is_dir( $path ) )
        {
            mkdir( $path );
        }
    }

    protected function getLogFilename()
    {
        return date("Y-m-d h-i-s") . '.txt';
    }

    protected function logData($data)
    {
        // Get file path.
        $path = $this->getLoggerDirectoryPath() . '/' . $this->getLogFilename();

        // Encode data?
        if ( ! is_string( $data ) )
        {
            $data = json_encode( $data, JSON_PRETTY_PRINT );
        }

        // Store.
        file_put_contents( $path, $data, FILE_APPEND );
    }

    /**
     * @return string
     */
    protected function getLoggerDirectoryPath()
    {
        return WORDPRESS_HELPERS_DIR . $this->basePath . '/' . $this->loggerDir;
    }

    public static function log($data)
    {
        $logger = static::getInstance();

        // Path to log directory.
        $path = $logger->getLoggerDirectoryPath();

        // Verify.
        $logger->verifyPath( $path );

        // Save data.
        $logger->logData( $data );
    }
}