<?php

class CommentTest extends SapphireTest {

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

	public function testGetSecurityToken() {
		$this->markTestSkipped('TODO');
	}

	public function testRequireDefaultRecords() {
		$this->markTestSkipped('TODO');
	}

	public function testLink() {
		$comment = $this->objFromFixture('Comment', 'thirdComD');
        $this->assertEquals('CommentableItem_Controller#comment-'.$comment->ID,
            $comment->Link());
        $this->assertEquals(8, $comment->ID);
	}

	public function testPermalink() {
		$this->markTestSkipped('TODO');
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
		$this->markTestSkipped('TODO');
	}

	public function testCanCreate() {
		$this->markTestSkipped('TODO');
	}

	public function testCanView() {
		$this->markTestSkipped('TODO');
	}

	public function testCanEdit() {
		$this->markTestSkipped('TODO');
	}

	public function testCanDelete() {
		$this->markTestSkipped('TODO');
	}

	public function testGetMember() {
		$this->markTestSkipped('TODO');
	}

	public function testGetAuthorName() {
		$this->markTestSkipped('TODO');
	}

	public function testActionLink() {
		$this->markTestSkipped('TODO');
	}

	public function testDeleteLink() {
		$this->markTestSkipped('TODO');
	}

	public function testSpamLink() {
		$this->markTestSkipped('TODO');
	}

	public function testHamLink() {
		$this->markTestSkipped('TODO');
	}

	public function testApproveLink() {
		$this->markTestSkipped('TODO');
	}

	public function testMarkSpam() {
		$this->markTestSkipped('TODO');
	}

	public function testMarkApproved() {
		$this->markTestSkipped('TODO');
	}

	public function testMarkUnapproved() {
		$this->markTestSkipped('TODO');
	}

	public function testSpamClass() {
		$this->markTestSkipped('TODO');
	}

	public function testGetTitle() {
		$this->markTestSkipped('TODO');
	}

	public function testGetCMSFields() {
		$this->markTestSkipped('TODO');
	}

	public function testPurifyHtml() {
		$this->markTestSkipped('TODO');
	}

	public function testGetHtmlPurifierService() {
		$this->markTestSkipped('TODO');
	}

	public function testGravatar() {
		$this->markTestSkipped('TODO');
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

	public function test__construct() {
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

}
