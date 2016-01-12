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
        $this->assertEquals(4, $comment->ChildComments()->count());

        $commentID = $comment->ID;
        $childCommentID = $child->ID;

        $comment->delete();

        // assert that the new child been deleted
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

        // An orphan comment has no link
        $comment->ParentID = 0;
        $comment->write();
        $this->assertEquals('', $comment->Link());
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

        // Title from a comment with no parent is blank
        $comment->ParentID = 0;
        $comment->write();
        $this->assertEquals('', $comment->getParentTitle());
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

        $comment->ParentID = 0;
        $comment->write();
        $this->assertFalse($comment->canView());
	}

	public function testCanEdit() {
        $comment = $this->objFromFixture('Comment', 'firstComA');

        // admin can edit
		$this->logInAs('commentadmin');
        $this->assertTrue($comment->canEdit());

        // visitor cannot
        $this->logInAs('visitor');
        $this->assertFalse($comment->canEdit());

        $comment->ParentID = 0;
        $comment->write();
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

        $comment->ParentID = 0;
        $comment->write();
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

        $comment->Name = '';
        $this->assertEquals(
            '',
            $comment->getAuthorName()
        );

        $author = $this->objFromFixture('Member', 'visitor');
        $comment->AuthorID = $author->ID;
        $comment->write();
        $this->assertEquals(
            'visitor',
            $comment->getAuthorName()
        );

        // null the names, expect null back
        $comment->Name = null;
        $comment->AuthorID = 0;
        $this->assertNull($comment->getAuthorName());

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
        $this->assertTrue($comment->Moderated);
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
            'Comment',
            'Email',
            'URL',
            null, #FIXME this is suspicious
            'ParentComment_Title',
            'ParentComment_Created',
            'ParentComment_AuthorName',
            'ParentComment_EscapedComment'
        );
        $this->assertEquals($expected, $names);
    }


	public function testPurifyHtml() {
        $comment = $this->objFromFixture('Comment', 'firstComA');

		$dirtyHTML = '<p><script>alert("w00t")</script>my comment</p>';
        $this->assertEquals(
            'my comment',
            $comment->purifyHtml($dirtyHTML)
        );
	}


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
        $comment = $this->objFromFixture('Comment', 'firstComA');
		Config::inst()->update('CommentableItem', 'comments', array(
            'nested_comments' => false
        ));
        $this->assertFalse($comment->getRepliesEnabled());

        Config::inst()->update('CommentableItem', 'comments', array(
            'nested_comments' => true,
            'nested_depth' => 4
        ));
        $this->assertTrue($comment->getRepliesEnabled());

        $comment->Depth = 4;
        $this->assertFalse($comment->getRepliesEnabled());
	}

	public function testAllReplies() {
        Config::inst()->update('CommentableItem', 'comments', array(
            'nested_comments' => true,
            'nested_depth' => 4
        ));
		$comment = $this->objFromFixture('Comment', 'firstComA');
        $this->assertEquals(
            3,
            $comment->allReplies()->count()
        );
        $child = new Comment();
        $child->Name = 'Fred Smith';
        $child->Comment = 'This is a child comment';
        $child->ParentCommentID = $comment->ID;

        // spam should be returned by this method
        $child->markSpam();
        $child->write();
        $replies = $comment->allReplies();
        $this->assertEquals(
            4,
            $comment->allReplies()->count()
        );

        Config::inst()->update('CommentableItem', 'comments', array(
            'nested_comments' => false
        ));

        $this->assertEquals(0, $comment->allReplies()->count());
	}

	public function testReplies() {
        CommentableItem::add_extension('CommentsExtension');
        $this->logInWithPermission('ADMIN');
		Config::inst()->update('CommentableItem', 'comments', array(
            'nested_comments' => true,
            'nested_depth' => 4
        ));
        $comment = $this->objFromFixture('Comment', 'firstComA');
        $this->assertEquals(
            3,
            $comment->Replies()->count()
        );

        // Test that spam comments are not returned
        $childComment = $comment->Replies()->first();
        error_log('T1: Child comment ID:' . $childComment->ID);
        $childComment->IsSpam = 1;
        $childComment->write();
        $this->assertEquals(
            2,
            $comment->Replies()->count()
        );

        // Test that unmoderated comments are not returned
        //
        $childComment = $comment->Replies()->first();

        // FIXME - moderation settings scenarios need checked here
        $childComment->Moderated = 0;
        $childComment->IsSpam = 0;
        $childComment->write();
        $this->assertEquals(
            2,
            $comment->Replies()->count()
        );


        // Test moderation required on the front end
        $item = $this->objFromFixture('CommentableItem', 'first');
        $item->ModerationRequired = 'Required';
        $item->write();

        Config::inst()->update('CommentableItemDisabled', 'comments', array(
            'nested_comments' => true,
            'nested_depth' => 4,
            'frontend_moderation' => true
        ));

        error_log('-------------------');
        $comment = DataObject::get_by_id('Comment', $comment->ID);
        foreach ($comment->Replies() as $reply) {
            error_log('REPLY: SPAM=' .$reply->IsSpam . ' MODERATED='.$reply->Moderated .':' . $reply->Comment);
        }

        $this->assertEquals(
            2,
            $comment->Replies()->count()
        );

        // Turn off nesting, empty array should be returned
        Config::inst()->update('CommentableItem', 'comments', array(
            'nested_comments' => false
        ));

        $this->assertEquals(
            0,
            $comment->Replies()->count()
        );

        CommentableItem::remove_extension('CommentsExtension');
	}

	public function testPagedReplies() {
		Config::inst()->update('CommentableItem', 'comments', array(
            'nested_comments' => true,
            'nested_depth' => 4,
            'comments_per_page' => 2 #Force 2nd page for 3 items
        ));

        $comment = $this->objFromFixture('Comment', 'firstComA');
        $pagedList = $comment->pagedReplies();
        $this->assertEquals(
            2,
            $pagedList->TotalPages()
        );
        $this->assertEquals(
            3,
            $pagedList->getTotalItems()
        );
        //TODO - 2nd page requires controller
        //
         Config::inst()->update('CommentableItem', 'comments', array(
            'nested_comments' => false
        ));

        $this->assertEquals(0, $comment->PagedReplies()->count());
	}

	public function testReplyForm() {
        Config::inst()->update('CommentableItem', 'comments', array(
            'nested_comments' => false,
            'nested_depth' => 4
        ));

		$comment = $this->objFromFixture('Comment', 'firstComA');

        // No nesting, no reply form
        $form = $comment->replyForm();
        $this->assertNull($form);

        // parent item so show form
        Config::inst()->update('CommentableItem', 'comments', array(
            'nested_comments' => true,
            'nested_depth' => 4
        ));
        $form = $comment->replyForm();

        $names = array();
        foreach ($form->Fields() as $field) {
            array_push($names, $field->getName());
        }

        $this->assertEquals(
            array(
                null, #FIXME suspicious
                'ParentID',
                'ReturnURL',
                'ParentCommentID',
                'BaseClass'
            ),
            $names
        );

        // no parent, no reply form

        $comment->ParentID = 0;
        $comment->write();
        $form = $comment->replyForm();
        $this->assertNull($form);
	}

	public function testUpdateDepth() {
        Config::inst()->update('CommentableItem', 'comments', array(
            'nested_comments' => true,
            'nested_depth' => 4
        ));

        $comment = $this->objFromFixture('Comment', 'firstComA');
        $children = $comment->allReplies()->toArray();
        error_log(print_r($children,1));
        // Make the second child a child of the first
        // Make the third child a child of the second
        $reply1 = $children[0];
        $reply2 = $children[1];
        $reply3 = $children[2];
        $reply2->ParentCommentID = $reply1->ID;
        $reply2->write();
        $this->assertEquals(3, $reply2->Depth);
        $reply3->ParentCommentID = $reply2->ID;
        $reply3->write();
        $this->assertEquals(4, $reply3->Depth);
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
