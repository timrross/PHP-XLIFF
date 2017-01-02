<?php

namespace Timrross\Xliff;

/**
 * Concrete class for ignorable tag.
 */
class Ignorable extends Node
{
    protected $tag_name = 'ignorable';
    protected $supported_leaf_nodes = array(
        'source' => 'Node',
        'target' => 'Node',
    );
}
