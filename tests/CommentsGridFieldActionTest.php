<?php

class CommentsGridFieldActionTest extends SapphireTest {

    /** @var ArrayList */
    protected $list;

    /** @var GridField */
    protected $gridField;

    /** @var Form */
    protected $form;

     public function setUp() {
        parent::setUp();
        $this->list = new DataList('GridFieldAction_Delete_Team');
        $config = CommentsGridFieldConfig::create()->addComponent(new GridFieldDeleteAction());
        $this->gridField = new CommentsGridField('testfield', 'testfield', $this->list, $config);
        $this->form = new Form(new Controller(), 'mockform', new FieldList(array($this->gridField)), new FieldList());
    }

	public function testAugmentColumns() {
        $action = new CommentsGridFieldAction();
        $record = new Comment();

        // an entry called 'Actions' is added to the columns array
        $columns = array();
        $action->augmentColumns($this->gridField, $columns);
        $expected = array('Actions');
        $this->assertEquals($expected, $columns);

        $columns = array('Actions');
        $action->augmentColumns($this->gridField, $columns);
        $expected = array('Actions');
        $this->assertEquals($expected, $columns);
	}

	public function testGetColumnAttributes() {
		$action = new CommentsGridFieldAction();
        $record = new Comment();
        $attrs = $action->getColumnAttributes($this->gridField, $record, 'Comment');
        $this->assertEquals(array('class' => 'col-buttons'), $attrs);
	}

	public function testGetColumnMetadata() {
		$this->markTestSkipped('TODO');
	}

	public function testGetColumnsHandled() {
		$this->markTestSkipped('TODO');
	}

	public function testGetColumnContent() {
		$this->markTestSkipped('TODO');
	}

	public function testGetActions() {
		$this->markTestSkipped('TODO');
	}

	public function testHandleAction() {
		$this->markTestSkipped('TODO');
	}

}
