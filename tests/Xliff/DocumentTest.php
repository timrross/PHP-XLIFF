<?php

namespace Timrross\Xliff;

use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    public function test_xliff_document_creation()
    {
        $xliff = new Document();
        $this->assertEquals('Timrross\Xliff\Document', get_class($xliff));
        $this->assertEquals('xliff', $xliff->get_tag_name());
    }
}
