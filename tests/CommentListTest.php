<?php

class CommentListTest extends FunctionalTest {

    public static $fixture_file = 'comments/tests/CommentsTest.yml';

    protected $extraDataObjects = array(
        'CommentableItem',
        'CommentableItemEnabled',
        'CommentableItemDisabled'
    );

    public function setUp() {
        parent::setUp();
        Config::nest();

        // Set good default values
        Config::inst()->update('CommentsExtension', 'comments', array(
            'enabled' => true,
            'enabled_cms' => false,
            'require_login' => false,
            'require_login_cms' => false,
            'required_permission' => false,
            'require_moderation_nonmembers' => false,
            'require_moderation' => false,
            'require_moderation_cms' => false,
            'frontend_moderation' => false,
            'frontend_spam' => false,
        ));

        // Configure this dataobject
        Config::inst()->update('CommentableItem', 'comments', array(
            'enabled_cms' => true
        ));
    }

	public function testGetForeignClass() {
        $item = $this->objFromFixture('CommentableItem', 'first');

        // This is the class the Comments are related to
        $this->assertEquals('CommentableItem',
                                $item->Comments()->getForeignClass());
	}

	public function FIXMEtestAddComment() {
		$item = $this->objFromFixture('CommentableItem', 'first');
        $comments = $item->Comments();
        $this->assertEquals(1, $comments->count());
        $newComment = new Comment();
        $newComment->Name = 'Fred Bloggs';
        $newComment->Comment = 'This is a test comment';
        $newComment->write();
        $comments->add($newComment);

        // As a comment has been added, there should be 2 comments now
        $this->assertEquals(2, $item->Comments()->count());

        $newComment2 = new Comment();
        $newComment2->Name = 'John Smith';
        $newComment2->Comment = 'This is another test comment';
        $newComment2->write();
        $comments->add($newComment2);

        // Check the order by testing the actual comments themselves
        $actualComments = array();
        foreach ($comments as $comment) {
            array_push($actualComments, $comment->Comment);
        }
        $expected = array(
            'textFA',
            $newComment->Comment,
            $newComment2->Comment
        );
        $this->assertEquals($expected, $actualComments);
	}

	public function testRemoveComment() {
		$item = $this->objFromFixture('CommentableItem', 'first');
        $this->assertEquals(1, $item->Comments()->count());
        $comments = $item->Comments();
        $comment = $comments->first();
        $comments->remove($comment);

        // 1-1 = 0
        $this->assertEquals(0, $item->Comments()->count());
	}

}
