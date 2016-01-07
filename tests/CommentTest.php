<?php

class CommentTest extends FunctionalTest {

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

	public function testOnBeforeWrite() {
		$this->markTestSkipped('TODO');
	}

    /*
    When a parent comment is deleted, remove the children
     */
	public function testOnBeforeDelete() {
		$comment = $this->objFromFixture('Comment', 'firstComA');

        $child = new Comment();
        $child->Name = 'Fred Bloggs';
        $child->Comment = 'Child of firstComA';
        $child->write();
        $comment->ChildComments()->add($child);
        $this->assertEquals(1, $comment->ChildComments()->count());

        $commentID = $comment->ID;
        $childCommentID = $child->ID;

        $comment->delete();

        // assert that the child comment has been deleted
        $this->assertFalse(DataObject::get_by_id('Comment', $commentID));
        $this->assertFalse(DataObject::get_by_id('Comment', $childCommentID));
	}

	public function testRequireDefaultRecords() {
		$this->markTestSkipped('TODO');
	}

	public function testLink() {
		$comment = $this->objFromFixture('Comment', 'thirdComD');
        $this->assertEquals('CommentableItem_Controller#comment-'.$comment->ID,
            $comment->Link());
        $this->assertEquals($comment->ID, $comment->ID);
	}

	public function testPermalink() {
		$comment = $this->objFromFixture('Comment', 'thirdComD');
        $this->assertEquals('comment-' . $comment->ID, $comment->Permalink());
	}

    /*
    Test field labels in 2 languages
     */
	public function testFieldLabels() {
        $locale = i18n::get_locale();
		i18n::set_locale('fr');
        $comment = $this->objFromFixture('Comment', 'firstComA');
        $labels = $comment->FieldLabels();
        $expected = array(
            'Name' => 'Nom de l\'Auteur',
            'Comment' => 'Commentaire',
            'Email' => 'Email',
            'URL' => 'URL',
            'BaseClass' => 'Base Class',
            'Moderated' => 'Modéré?',
            'IsSpam' => 'Spam?',
            'ParentID' => 'Parent ID',
            'AllowHtml' => 'Allow Html',
            'SecretToken' => 'Secret Token',
            'Depth' => 'Depth',
            'Author' => 'Author Member',
            'ParentComment' => 'Parent Comment',
            'ChildComments' => 'Child Comments',
            'ParentTitle' => 'Parent',
            'Created' => 'Date de publication'
        );
        i18n::set_locale($locale);
        $this->assertEquals($expected, $labels);
        $labels = $comment->FieldLabels();
        $expected = array(
            'Name' => 'Author Name',
            'Comment' => 'Comment',
            'Email' => 'Email',
            'URL' => 'URL',
            'BaseClass' => 'Base Class',
            'Moderated' => 'Moderated?',
            'IsSpam' => 'Spam?',
            'ParentID' => 'Parent ID',
            'AllowHtml' => 'Allow Html',
            'SecretToken' => 'Secret Token',
            'Depth' => 'Depth',
            'Author' => 'Author Member',
            'ParentComment' => 'Parent Comment',
            'ChildComments' => 'Child Comments',
            'ParentTitle' => 'Parent',
            'Created' => 'Date posted'

        );
        $this->assertEquals($expected, $labels);
	}

	public function testGetOption() {
		$this->markTestSkipped('TODO');
	}

	public function testGetParent() {
        $comment = $this->objFromFixture('Comment', 'firstComA');
        $item = $this->objFromFixture('CommentableItem', 'first');
        $parent = $comment->getParent();
        $this->assertEquals($item, $parent);
	}

	public function testGetParentTitle() {
        $comment = $this->objFromFixture('Comment', 'firstComA');
        $title = $comment->getParentTitle();
		$this->assertEquals('First', $title);
	}

	public function testGetParentClassName() {
		$comment = $this->objFromFixture('Comment', 'firstComA');
        $className = $comment->getParentClassName();
        $this->assertEquals('CommentableItem', $className);
	}

	public function testCastingHelper() {
		$this->markTestSkipped('TODO');
	}

	public function testGetEscapedComment() {
		$this->markTestSkipped('TODO');
	}

	public function testIsPreview() {
		$comment = new Comment();
        $comment->Name = 'Fred Bloggs';
        $comment->Comment = 'this is a test comment';
        $this->assertTrue($comment->isPreview());
        $comment->write();
        $this->assertFalse($comment->isPreview());
	}

	public function testCanCreate() {
		$comment = $this->objFromFixture('Comment', 'firstComA');

        // admin can create - this is always false
        $this->logInAs('commentadmin');
        $this->assertFalse($comment->canCreate());

        // visitor can view
        $this->logInAs('visitor');
        $this->assertFalse($comment->canCreate());
	}

	public function testCanView() {
		$comment = $this->objFromFixture('Comment', 'firstComA');

        // admin can view
        $this->logInAs('commentadmin');
        $this->assertTrue($comment->canView());

        // visitor can view
        $this->logInAs('visitor');
        $this->assertTrue($comment->canView());
	}

	public function testCanEdit() {
        $comment = $this->objFromFixture('Comment', 'firstComA');

        // admin can edit
		$this->logInAs('commentadmin');
        $this->assertTrue($comment->canEdit());

        // visitor cannot
        $this->logInAs('visitor');
        $this->assertFalse($comment->canEdit());
	}

	public function testCanDelete() {
		$comment = $this->objFromFixture('Comment', 'firstComA');

        // admin can delete
        $this->logInAs('commentadmin');
        $this->assertTrue($comment->canDelete());

        // visitor cannot
        $this->logInAs('visitor');
        $this->assertFalse($comment->canDelete());
	}

	public function testGetMember() {
        $this->logInAs('visitor');
		$current = Member::currentUser();
        $comment = $this->objFromFixture('Comment', 'firstComA');
        $method = $this->getMethod('getMember');

        // null case
        $member = $method->invokeArgs($comment, array());
        $this->assertEquals($current, $member);

        // numeric ID case
        $member = $method->invokeArgs($comment, array($current->ID));
        $this->assertEquals($current, $member);

        // identity case
        $member = $method->invokeArgs($comment, array($current));
        $this->assertEquals($current, $member);
	}

	public function testGetAuthorName() {
		$comment = $this->objFromFixture('Comment', 'firstComA');
        $this->assertEquals(
            'FA',
            $comment->getAuthorName()
        );
	}


    public function testLinks() {
        $comment = $this->objFromFixture('Comment', 'firstComA');
        $this->logInAs('commentadmin');

        $method = $this->getMethod('ActionLink');

        // test with starts of strings and tokens and salts change each time
        $this->assertStringStartsWith(
            '/CommentingController/theaction/'.$comment->ID,
            $method->invokeArgs($comment, array('theaction'))
        );

        $this->assertStringStartsWith(
            '/CommentingController/delete/'.$comment->ID,
            $comment->DeleteLink()
        );

        $this->assertStringStartsWith(
            '/CommentingController/spam/'.$comment->ID,
            $comment->SpamLink()
        );

        $comment->markSpam();
        $this->assertStringStartsWith(
            '/CommentingController/ham/'.$comment->ID,
            $comment->HamLink()
        );

        //markApproved
        $comment->markUnapproved();
        $this->assertStringStartsWith(
            '/CommentingController/approve/'.$comment->ID,
            $comment->ApproveLink()
        );
    }

	public function testMarkSpam() {
        $comment = $this->objFromFixture('Comment', 'firstComA');
        $comment->markSpam();
        $this->assertTrue($comment->Moderated);
        $this->assertTrue($comment->IsSpam);
	}

	public function testMarkApproved() {
		$comment = $this->objFromFixture('Comment', 'firstComA');
        $comment->markApproved();
        $this->assertTrue($comment->Moderated);
        $this->assertFalse($comment->IsSpam);
	}

	public function testMarkUnapproved() {
		$comment = $this->objFromFixture('Comment', 'firstComA');
        $comment->markApproved();
        $this->assertFalse($comment->Moderated);
	}

	public function testSpamClass() {
        $comment = $this->objFromFixture('Comment', 'firstComA');
        $this->assertEquals('notspam', $comment->spamClass());
        $comment->Moderated = false;
        $this->assertEquals('unmoderated', $comment->spamClass());
        $comment->IsSpam = true;
        $this->assertEquals('spam', $comment->spamClass());
	}

	public function testGetTitle() {
        $comment = $this->objFromFixture('Comment', 'firstComA');
		$this->assertEquals(
            'Comment by FA on First',
            $comment->getTitle()
        );
	}

	public function testGetCMSFields() {
        $comment = $this->objFromFixture('Comment', 'firstComA');
        $fields = $comment->getCMSFields();
        $names = array();
        foreach ($fields as $field) {
            $names[] = $field->getName();
        }
        $expected = array(
            'Created',
            'Name',
            'Comment',
            'Email',
            'URL',
            null #FIXME this is suspicious
        );
        $this->assertEquals($expected, $names);
    }

    public function testGetCMSFieldsCommentHasAuthor() {
        $member = Member::get()->filter('FirstName', 'visitor')->first();
        $comment = $this->objFromFixture('Comment', 'firstComA');
        $comment->AuthorID = $member->ID;
        $comment->write();

        $fields = $comment->getCMSFields();
        $names = array();
        foreach ($fields as $field) {
            $names[] = $field->getName();
        }
        $expected = array(
            'Created',
            'Name',
            'AuthorMember',
            'Comment',
            'Email',
            'URL',
            null #FIXME this is suspicious
        );
        $this->assertEquals($expected, $names);
    }

    public function testGetCMSFieldsWithParentComment() {
        $comment = $this->objFromFixture('Comment', 'firstComA');

        $child = new Comment();
        $child->Name = 'John Smith';
        $child->Comment = 'This is yet another test commnent';
        $child->ParentCommentID = $comment->ID;
        $child->write();

        $fields = $child->getCMSFields();
        $names = array();
        foreach ($fields as $field) {
            $names[] = $field->getName();
        }
        $expected = array(
            'Created',
            'Name',
            'AuthorMember',
            'Comment',
            'Email',
            'URL',
            null #FIXME this is suspicious
        );
        $this->assertEquals($expected, $names);
    }

/*
	public function testPurifyHtml() {
        $comment = $this->objFromFixture('Comment', 'firstComA');

		$dirtyHTML = '<p><script>alert("w00t")</script>my comment</p>';
        $this->assertEquals(
            '',
            $comment->purifyHtml($dirtyHTML)
        );
	}
*/

	public function testGetHtmlPurifierService() {
		$this->markTestSkipped('TODO');
	}

	public function testGravatar() {
        // Turn gravatars on
        Config::inst()->update('CommentableItem', 'comments', array(
            'use_gravatar' => true
        ));
		$comment = $this->objFromFixture('Comment', 'firstComA');

        $this->assertEquals(
            'http://www.gravatar.com/avatar/d41d8cd98f00b204e9800998ecf8427e?s'.
            '=80&d=identicon&r=g',
            $comment->gravatar()
        );

        // Turn gravatars off
        Config::inst()->update('CommentableItem', 'comments', array(
            'use_gravatar' => false
        ));
        $comment = $this->objFromFixture('Comment', 'firstComA');

        $this->assertEquals(
            '',
            $comment->gravatar()
        );
	}

	public function testGetRepliesEnabled() {
		$this->markTestSkipped('TODO');
	}

	public function testAllReplies() {
		$this->markTestSkipped('TODO');
	}

	public function testReplies() {
		$this->markTestSkipped('TODO');
	}

	public function testPagedReplies() {
		$this->markTestSkipped('TODO');
	}

	public function testReplyForm() {
		$this->markTestSkipped('TODO');
	}

	public function testUpdateDepth() {
		$this->markTestSkipped('TODO');
	}

	public function testGetToken() {
		$this->markTestSkipped('TODO');
	}

	public function testMemberSalt() {
		$this->markTestSkipped('TODO');
	}

	public function testAddToUrl() {
		$this->markTestSkipped('TODO');
	}

	public function testCheckRequest() {
		$this->markTestSkipped('TODO');
	}

	public function testGenerate() {
		$this->markTestSkipped('TODO');
	}


    protected static function getMethod($name) {
        $class = new ReflectionClass('Comment');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

}
