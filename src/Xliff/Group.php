<?php

namespace Timrross\Xliff;

/**
 * Concrete class for group tag.
 */
class Group extends Node
{
    protected $tag_name = 'group';
    protected $supported_containers = array(
        'notes' => 'Notes',
        'group' => 'Group',
        'unit' => 'Unit',
    );
}
