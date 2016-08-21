<?php
class IOPostImporterTest extends TestCase
{
    /**
     * @var \Zawntech\WordPress\IO\IOSession
     */
    public $session;

    /**
     * @var \Zawntech\WordPress\IO\IOPostImporter
     */
    public $importer;

    public $postId;

    public function setUp()
    {
        // Session ID.
        $sessionId = 'bb1020cb1b103258c4aba5956c156a21';

        $this->postId = 1667;

        // Load the session.
        $session = new \Zawntech\WordPress\IO\IOSession($sessionId);
        
        // Inject to class.
        $this->session = $session;

        // Make the importer.
        $this->importer = new \Zawntech\WordPress\IO\IOPostImporter;
    }

    public function testCanGetPathToPostJsonFile()
    {
        // Define a post id.
        $postId = $this->postId;

        // Load importer via post id.
        $this->importer->viaPostId($this->session->sessionId, $postId);

        // 74.json should exist in the path string.
        $this->assertNotFalse( strpos( $this->importer->getPathToFile(), '1667.json' ) );

        // Log output.
        $this->log( $this->importer->getPathToFile() );
    }

    public function testCanVerifyPathToJsonFileExists()
    {
        // Define a post id.
        $postId = $this->postId;

        // Load importer via post id.
        $this->importer->viaPostId($this->session->sessionId, $postId);

        // Assert that the file exists.
        $this->assertTrue( $this->importer->fileExists() );
    }

    /**
     * Verify that when we load by $importer->viaPostId(), our stored
     * JSON data is injected back to the object.
     */
    public function testPostDataIsLoadedWhenViaPostId()
    {
        // Define a post id.
        $postId = $this->postId;

        // Load importer via post id.
        $this->importer->viaPostId($this->session->sessionId, $postId);

        // Did our data load successfully?
        $this->assertInstanceOf( stdClass::class, $this->importer->postData );
        $this->assertTrue( is_array( $this->importer->postMeta ) );
    }

    public function testCanGetThePostsFeaturedImagePostId()
    {
        // Define a post id.
        $postId = $this->postId;

        // Load importer via post id.
        $this->importer->viaPostId($this->session->sessionId, $postId);

        $postId = $this->importer->getFeaturedImagePostId();

        // Has post id?
        $this->assertGreaterThan( 0, $postId );

        $this->log( $postId );
    }

    public function testCanGetTheFeaturedPostImageData()
    {
        // Define a post id.
        $postId = $this->postId;

        // Load importer via post id.
        $this->importer->viaPostId($this->session->sessionId, $postId);

        // Get post data.
        $featuredImageData = $this->importer->getFeaturedImageData();

        // Did we get out data?
        $this->assertNotEmpty( $featuredImageData->postId );
        $this->assertNotEmpty( $featuredImageData->mime );
        $this->assertNotEmpty( $featuredImageData->url );
        $this->assertNotEmpty( $featuredImageData->urls );
    }
    
    public function testCanUpdateFeaturedImage()
    {
        // Define a post id.
        $postId = $this->postId;

        // Load importer via post id.
        $this->importer->viaPostId($this->session->sessionId, $postId);

        // Get post data.
        $featuredImageData = $this->importer->getFeaturedImageData();
        
        // Update featured image.
        $this->importer->updateFeaturedImage();
    }

    public function testCanImportPost()
    {
        $this->importer->viaPostId('bb1020cb1b103258c4aba5956c156a21', 1667);
    //    $this->importer->import();
    }
}