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

    public function tearDown() {
        Config::unnest();
        parent::tearDown();
    }

	public function testGetForeignClass() {
        $item = $this->objFromFixture('CommentableItem', 'first');

        // This is the class the Comments are related to
        $this->assertEquals('CommentableItem',
                                $item->Comments()->getForeignClass());
	}

    public function testAddNonComment() {
        $item = $this->objFromFixture('CommentableItem', 'first');
        $comments = $item->Comments();
        $this->assertEquals(4, $comments->count());
        $member = Member::get()->first();
        try {
            $comments->add($member);
            $this->fail('Should not have been able to add member to comments');
        } catch (InvalidArgumentException $e) {
            $this->assertEquals(
                'CommentList::add() expecting a Comment object, or ID value',
                $e->getMessage()
            );
        }
    }

	public function testAddComment() {
		$item = $this->objFromFixture('CommentableItem', 'first');
        $comments = $item->Comments();
        $ctr = 6;
        // set CreatedAt times to avoid inconsistencies when CreatedAt trips over
        // into the next second during tests
        $now = time();
        foreach ($comments as $comment) {
            $comment->CreatedAt = $now-$ctr*3600;
            $comment->write();
            $ctr--;
        }
        $this->assertEquals(4, $comments->count());
        $newComment = new Comment();
        $newComment->Name = 'Fred Bloggs';
        $newComment->Comment = 'This is a test comment';
        $newComment->Created = $now-3000;
        $newComment->write();
        $comments->add($newComment);

        // As a comment has been added, there should be 5 comments now
        $this->assertEquals(5, $item->Comments()->count());

        $newComment2 = new Comment();
        $newComment2->Name = 'John Smith';
        $newComment2->Comment = 'This is another test comment';
        $newComment2->CreatedAt = $now-2000;
        $newComment2->write();

        // test adding the same comment by ID
        $comments->add($newComment2->ID);

        $this->assertEquals(6, $item->Comments()->count());

        // Check the order by testing the actual comments themselves
        $actualComments = array();
        foreach ($item->Comments() as $comment) {
            array_push($actualComments, $comment->Comment);
        }
        $expected = array(
            'textFA',
            'Reply to firstComA 1',
            'Reply to firstComA 2',
            'Reply to firstComA 3',
            $newComment2->Comment,
            $newComment->Comment
        );
        $this->assertEquals($expected, $actualComments);

        $this->setExpectedException(
            'InvalidArgumentException',
            "CommentList::add() can't be called until a single foreign ID is set"
        );
        $list = new CommentList('CommentableItem');
        $list->add($newComment);
	}

	public function testRemoveComment() {
        // remove by comment
        $item = $this->objFromFixture('CommentableItem', 'first');
        $this->assertEquals(4, $item->Comments()->count());
        $comments = $item->Comments();
        $comment = $comments->first();
        $comments->remove($comment);

        // now remove by ID
        $comments = $item->Comments();
        $comment = $comments->first();
        $comments->remove($comment->ID);
        $this->assertEquals(2, $item->Comments()->count());
    }

    public function testRemoveNonComment() {
        $item = $this->objFromFixture('CommentableItem', 'first');
        $this->assertEquals(4, $item->Comments()->count());
        $comments = $item->Comments();

        // try and remove a non comment
        $member = Member::get()->first();



        try {
            $comments->remove($member);
            $this->fail('Should not have been able to remove member from comments');
        } catch (InvalidArgumentException $e) {
            $this->assertEquals(
                'CommentList::remove() expecting a Comment object, or ID',
                $e->getMessage()
            );
        }
    }

}
