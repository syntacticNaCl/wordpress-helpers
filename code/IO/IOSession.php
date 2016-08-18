<?php
namespace Zawntech\WordPress\IO;

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
        if ( ! $this->sessionId )
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

        // Set created at.
        $this->createdAt = time();

        if ( $sessionId )
        {
            $this->sessionId;
        }

        // Auto start the session hash.
        $this->start();
    }

    /**
     * Save file on exit.
     */
    public function __destruct()
    {
        $this->files->put( $this->sessionId . '.json', $this );
    }
}