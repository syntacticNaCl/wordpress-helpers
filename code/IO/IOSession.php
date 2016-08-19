<?php
namespace Zawntech\WordPress\IO;

/**
 * Class IOSession
 * @package Zawntech\WordPress\IO
 */
class IOSession
{
    /**
     * @var FileManager
     */
    protected $files;

    /**
     * @var string
     */
    public $sessionId;

    /**
     * @var string
     */
    public $remoteUrl;

    /**
     * @var string
     */
    public $securityKey;

    /**
     * @var mixed
     */
    public $instanceData;

    /**
     * @var int Timestamp
     */
    public $createdAt;

    /**
     * Start the session
     */
    public function start()
    {
        // Create a session ID if not set.
        if ( null === $this->sessionId )
        {
            // Make an md5 json of right now.
            $hash = md5( time() );

            // Assign internally.
            $this->sessionId = $hash;
        }
    }

    /**
     * IOSession constructor.
     * @param $sessionId int
     */
    public function __construct($sessionId = null)
    {
        // Instantiate FileManager.
        $this->files = new FileManager();

        // Set upload path.
        $this->files->useCustomPath('io-session');

        // If a session ID is provided, load that session from JSON.
        if ( $sessionId )
        {
            // Load file data.
            $file = $this->files->get( $sessionId . '.json', true );

            // Set data to object.
            $this->instanceData = $file->instanceData;
            $this->sessionId = $file->sessionId;
            $this->remoteUrl = $file->remoteUrl;
            $this->securityKey = $file->securityKey;
            $this->createdAt = $file->createdAt;
        }

        // Start a new session.
        else
        {
            // Set created at.
            $this->createdAt = time();

            // Auto start the session hash.
            $this->start();
        }
    }

    /**
     * Save to file.
     */
    public function save()
    {
        $this->files->put( $this->sessionId . '.json', json_encode($this) );
    }

    /**
     * Save file on exit.
     */
    public function __destruct()
    {
        $this->files->put( $this->sessionId . '.json', json_encode($this) );
    }
}