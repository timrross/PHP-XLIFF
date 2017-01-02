<?php

namespace Timrross\Xliff;

/**
 * Concrete class for segment tag.
 */
class Segment extends Node
{
    protected $tag_name = 'segment';
    protected $supported_leaf_nodes = array(
        'source' => 'Node',
        'target' => 'Node',
    );
}
