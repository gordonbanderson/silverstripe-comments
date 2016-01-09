<?php

class CommentingTest extends SapphireTest {

    public function setUpOnce() {
        Member::add_extension('CommentsExtension');
        parent::setUpOnce();
    }

    public function testDeprecatedMethods() {
        $methods = array('add', 'remove', 'has_commenting', 'get_config_value',
                            'set_config_value');
        foreach ($methods as $methodName) {
            try {
                if (
                    $methodName == 'get_config_value' ||
                    $methodName == 'set_config_value'
                    ) {
                    Commenting::$methodName('Member', 'keyname');
                } else {
                    Commenting::$methodName('Member');
                }


            } catch (PHPUnit_Framework_Error_Deprecated $e) {
                $expected = 'Using Commenting:' . $methodName .' is deprecated.'
                          . ' Please use the config API instead';
                $this->assertEquals($expected, $e->getMessage());
            }
        }
    }


	public function testSet_config_value() {
		$this->markTestSkipped('TODO');
	}

	public function testGet_config_value() {
		$this->markTestSkipped('TODO');
	}

	public function testConfig_value_equals() {
		$this->markTestSkipped('TODO');
	}

	public function testCan_member_post() {
		$this->markTestSkipped('TODO');
	}

}
