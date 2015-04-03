<?php
class Test_XLIFF_Library extends PHPUnit_Framework_TestCase {
	function test_xliff_node_creation() {
		$node = new Xliff_Node();
		$this->assertEquals( 'Xliff_Node', get_class( $node ) );
		$this->assertEmpty( $node->get_tag_name() );
	}

	function test_xliff_document_creation() {
		$xliff = new Xliff_Document();
		$this->assertEquals( 'Xliff_Document', get_class( $xliff ) );
		$this->assertEquals( 'xliff', $xliff->get_tag_name() );
	}
}