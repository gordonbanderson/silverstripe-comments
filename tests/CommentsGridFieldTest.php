<?php

class CommentsGridFieldTest extends SapphireTest {
	public function testNewRow() {
	   $gridfield = new CommentsGridField('testfield', 'testfield');
       //   protected function newRow($total, $index, $record, $attributes, $content) {
       $comment = new Comment();
       $comment->Name = 'Fred Bloggs';
       $comment->Comment = 'This is a comment';
       $attr = array();
       $newRow = $gridfield->newRow(1, 1, $comment, $attr, $comment->Comment );
       $this->assertEquals('<tr>This is a comment</tr>', $newRow);
	}

}
