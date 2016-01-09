<?php

class CommentsExtensionTest extends SapphireTest {

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
            'Member' => false,
        ));

        $this->requiredExtensions = array(
            'CommentableItem' => 'CommentsExtension'
        );

        // Configure this dataobject
        Config::inst()->update('CommentableItem', 'comments', array(
            'enabled_cms' => true
        ));
    }

    public function tearDown() {
        Config::unnest();
        parent::tearDown();
    }

	public function testPopulateDefaults() {
		$this->markTestSkipped('TODO');
	}

	public function testUpdateSettingsFields() {
        $this->markTestSkipped('This needs SiteTree installed');
	}

	public function testGetModerationRequired() {

        // the 3 options take precedence in this order, executed if true
        Config::inst()->update('CommentableItem', 'comments', array(
            'require_moderation_cms' => true,
            'require_moderation' => true,
            'require_moderation_nonmembers' => true
        ));

        // With require moderation CMS set to true, the value of the field
        // 'ModerationRequired' is returned
        $item = $this->objFromFixture('CommentableItem', 'first');
        $item->ModerationRequired = 'None';
        $this->assertEquals('None', $item->getModerationRequired());
        $item->ModerationRequired = 'Required';
        $this->assertEquals('Required', $item->getModerationRequired());
        $item->ModerationRequired = 'NonMembersOnly';
        $this->assertEquals('NonMembersOnly', $item->getModerationRequired());

        Config::inst()->update('CommentableItem', 'comments', array(
            'require_moderation_cms' => false,
            'require_moderation' => true,
            'require_moderation_nonmembers' => true
        ));
        $this->assertEquals('Required', $item->getModerationRequired());

        Config::inst()->update('CommentableItem', 'comments', array(
            'require_moderation_cms' => false,
            'require_moderation' => false,
            'require_moderation_nonmembers' => true
        ));
        $this->assertEquals('NonMembersOnly', $item->getModerationRequired());

        Config::inst()->update('CommentableItem', 'comments', array(
            'require_moderation_cms' => false,
            'require_moderation' => false,
            'require_moderation_nonmembers' => false
        ));
        $this->assertEquals('None', $item->getModerationRequired());
	}

	public function testGetCommentsRequireLogin() {
		Config::inst()->update('CommentableItem', 'comments', array(
            'require_login_cms' => true
        ));

        // With require moderation CMS set to true, the value of the field
        // 'ModerationRequired' is returned
        $item = $this->objFromFixture('CommentableItem', 'first');
        $item->CommentsRequireLogin = true;
        $this->assertTrue($item->getCommentsRequireLogin());
        $item->CommentsRequireLogin = false;
        $this->assertFalse($item->getCommentsRequireLogin());

        Config::inst()->update('CommentableItem', 'comments', array(
            'require_login_cms' => false,
            'require_login' => false
        ));
        $this->assertFalse($item->getCommentsRequireLogin());
        Config::inst()->update('CommentableItem', 'comments', array(
            'require_login_cms' => false,
            'require_login' => true
        ));
        $this->assertTrue($item->getCommentsRequireLogin());

	}

	public function testAllComments() {
		$this->markTestSkipped('TODO');
	}

	public function testAllVisibleComments() {
		$this->markTestSkipped('TODO');
	}

	public function testComments() {
		$this->markTestSkipped('TODO');
	}

	public function testPagedComments() {
		$this->markTestSkipped('TODO');
	}

	public function testGetCommentsConfigured() {
		$this->markTestSkipped('TODO');
	}

	public function testGetCommentsEnabled() {
		$this->markTestSkipped('TODO');
	}

	public function testGetCommentHolderID() {
        $item = $this->objFromFixture('CommentableItem', 'first');
        Config::inst()->update('CommentableItem', 'comments', array(
            'comments_holder_id' => 'commentid_test1',
        ));
        $this->assertEquals('commentid_test1', $item->getCommentHolderID());

        Config::inst()->update('CommentableItem', 'comments', array(
            'comments_holder_id' => 'commtentid_test_another',
        ));
        $this->assertEquals('commtentid_test_another', $item->getCommentHolderID());
	}


	public function testGetPostingRequiredPermission() {
		$this->markTestSkipped('TODO');
	}

	public function testCanPostComment() {
		$this->markTestSkipped('TODO');
	}

	public function testCanModerateComments() {
		$this->markTestSkipped('TODO');
	}

	public function testGetCommentRSSLink() {
	   $item = $this->objFromFixture('CommentableItem', 'first');
       $link = $item->getCommentRSSLink();
       $this->assertEquals('/CommentingController/rss', $link);
	}


	public function testGetCommentRSSLinkPage() {
		$item = $this->objFromFixture('CommentableItem', 'first');
        $page = $item->getCommentRSSLinkPage();
        $this->assertEquals(
            '/CommentingController/rss/CommentableItem/' . $item->ID,
            $page
        );
	}

	public function testCommentsForm() {
		$this->markTestSkipped('TODO');
	}

	public function testAttachedToSiteTree() {
		$this->markTestSkipped('TODO');
	}

	public function testPagedComments() {
        $item = $this->objFromFixture('CommentableItem', 'first');
        $results = $item->PagedComments()->toArray();
        error_log(print_r($results,1));

        foreach ($results as $result) {
           $result->sourceQueryParams = null;
        }

        $this->assertEquals(
            $this->objFromFixture('Comment', 'firstComA'),
            $results[0]
        );
        $this->assertEquals(
            $this->objFromFixture('Comment', 'firstComAChild1'),
            $results[1]
        );
        $this->assertEquals(
            $this->objFromFixture('Comment', 'firstComAChild2'),
            $results[2]
        );
        $this->assertEquals(
            $this->objFromFixture('Comment', 'firstComAChild3'),
            $results[3]
        );

        $this->assertEquals(4, sizeof($results));

	}

	public function testGetCommentsOption() {
		$this->markTestSkipped('TODO');
	}

	public function testUpdateModerationFields() {
		$this->markTestSkipped('TODO');
	}

	public function testUpdateCMSFields() {
		$this->markTestSkipped('TODO');
	}



    public function testDeprecatedMethods() {
        $item = $this->objFromFixture('CommentableItem', 'first');
        $methodNames = array(
            'getRssLinkPage',
            'getRssLink',
            'PageComments',
            'getPostingRequiresPermission',
            'canPost',
            'getCommentsConfigured'
        );

        foreach ($methodNames as $methodName) {
            try {
                $item->$methodName();
                $this->fail('Method ' . $methodName .' should be depracated');
            } catch (PHPUnit_Framework_Error_Deprecated $e) {
                $expected = 'CommentsExtension->' . $methodName . ' is '.
                'deprecated.';
                $this->assertStringStartsWith($expected, $e->getMessage());
            }
        }

    }

}
