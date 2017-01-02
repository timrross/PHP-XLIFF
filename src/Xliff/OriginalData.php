<?php

namespace Timrross\Xliff;

/**
 * Concrete class for originalData tag.
 */
class OriginalData extends Node
{
    protected $tag_name = 'originalData';
    protected $supported_leaf_nodes = array(
        'data' => 'Data',
    );
}
