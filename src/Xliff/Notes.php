<?php

namespace Timrross\Xliff;

/**
 * Concrete class for Notes tag.
 */
class Notes extends Node
{
    protected $tag_name = 'body';
    protected $supported_leaf_nodes = array(
        'note' => 'Note',
    );
}
