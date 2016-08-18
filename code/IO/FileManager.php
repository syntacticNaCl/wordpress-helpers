<?php
namespace Zawntech\WordPress\IO;

/**
 * A class for working with files in the WordPress upload's directory.
 * Class FileManager
 * @package Zawntech\WordPress\IO
 */
class FileManager
{
    /**
     * @var string Working upload directory path.
     */
    protected $workingDir;

    /**
     * @var string Working upload URL.
     */
    protected $workingUrl;

    /**
     * @return $this
     */
    public function useDefaultPath()
    {
        $this->workingDir = wp_upload_dir()['path'];
        $this->workingUrl = wp_upload_dir()['url'];
        return $this;
    }

    /**
     * @param $directoryPath
     * @return $this
     */
    public function useCustomPath($directoryPath)
    {
        // Assign working directory.
        $this->workingDir = wp_upload_dir()['basedir'] . '/' . $directoryPath;
        $this->workingUrl = wp_upload_dir()['baseurl'] . '/' . $directoryPath;

        // Create the directory if it does not exist.
        if ( ! is_dir( $this->workingDir ) )
        {
            mkdir( $this->workingDir );
        }

        return $this;
    }

    /**
     * Store a file
     * @param $filename
     * @param $data mixed
     * @param null $flags File flags.
     */
    public function put($filename, $data, $flags = null)
    {
        // Prepare data.
        if ( is_array( $data ) || is_object( $data ) )
        {
            $data = json_encode( $data );
        }

        file_put_contents( $this->workingDir . '/' . $filename, $data, $flags );
    }

    /**
     * @return string Public URL to working upload directory.
     */
    public function getUrl()
    {
        return $this->workingUrl . '/';
    }

    /**
     * @return string Absolute path to working upload directory.
     */
    public function getPath()
    {
        return $this->workingDir . '/';
    }

    public function get($filename, $decodeJson = false)
    {
        // Prepare path to file.
        $path = $this->getPath() . $filename;

        // Is this a file?
        if ( ! is_file( $path ) )
        {
            return false;
        }

        // Get the data.
        $data = file_get_contents($this->getPath() . $filename);

        // Return the data.
        return $decodeJson ? json_decode( $data ) : $data;
    }

    public function __construct()
    {
        // Initialize working directory.
        if ( null === $this->workingDir )
        {
            $this->useDefaultPath();
        }
    }
}