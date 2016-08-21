<?php
class IOTaxonomyImporterTest extends TestCase
{
    public $sessionId;
    public $postId;

    public function setUp()
    {
        parent::setUp();
        $this->sessionId = 'bb1020cb1b103258c4aba5956c156a21';
        $this->postId = 3580;
    }

    public function testCanImportPostTaxonomyTerms()
    {
        // Make taxonomy importer.
        $io = new \Zawntech\WordPress\IO\IOTaxonomyImporter($this->sessionId, $this->postId);

        $io->insertTerms();
    }
}