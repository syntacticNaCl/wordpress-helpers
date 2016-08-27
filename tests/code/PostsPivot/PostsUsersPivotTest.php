<?php
use Zawntech\WordPress\PostsPivot\PostsUsersPivot;

class PostsUsersPivotTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    // We should get a boolean value when checking the isInstalled function.
    public function testCanTestInstallationStatus()
    {
        $status = PostsUsersPivot::isInstalled();
        $this->assertInternalType( 'boolean', $status );
    }

    // We should get TRUE.
    public function testCanInstallTable()
    {
        $this->assertTrue( PostsUsersPivot::install() );
    }

    // We can establish a relationship.
    public function testCanAttachUserToPost()
    {
        PostsUsersPivot::attach(1, 1);
        $this->assertTrue( PostsUsersPivot::relationshipExists(1, 1) );
    }

    // We can remove the relationship.
    public function testCanDetachUserFromPost()
    {
        PostsUsersPivot::detach(1, 1);
        $this->assertFalse( PostsUsersPivot::relationshipExists(1, 1) );
    }

    // Can we get a post's user pivots?
    public function testCanGetPostUsers()
    {
        $postId = 5;
        $userId = 1;
        PostsUsersPivot::attach($postId, $userId);
        $this->assertTrue( in_array( $userId, PostsUsersPivot::getPostUserIds($postId) ) );
    }

    // Can we get a user's post pivots?
    public function testCanGetUserPosts()
    {
        $postId = 5;
        $userId = 1;
        PostsUsersPivot::attach($postId, $userId);
        $this->assertTrue( in_array( $postId, PostsUsersPivot::getUserPostIds($userId) ) );
    }
}