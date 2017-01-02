<?php

namespace Timrross\Xliff;

/**
 * Concrete class for unit tag.
 */
class Unit extends Node
{
    protected $tag_name = 'unit';
    protected $supported_containers = array(
        'notes' => 'Notes',
        'originalData' => 'OriginalData',
        'segment' => 'Segment',
        'ignorable' => 'Ignorable',
    );
}
