<?php

namespace Timrross\Xliff;

use PHPUnit\Framework\TestCase;

class NodeTest extends TestCase
{
    public function test_xliff_node_creation()
    {
        $node = new Node();
        $this->assertEquals('Timrross\\Xliff\\Node', get_class($node));
        $this->assertEmpty($node->get_tag_name());
    }
}
