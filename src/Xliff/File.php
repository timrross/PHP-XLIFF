<?php

namespace Timrross\Xliff;

/**
 * Concrete class for file tag.
 */
class File extends Node
{
    protected $tag_name = 'file';
    protected $supported_containers = array(
        'notes' => 'Notes',
        'unit' => 'Unit',
        'group' => 'Group',
    );
    protected $supported_leaf_nodes = array(
        'skeleton' => 'Skeleton',
    );
}
