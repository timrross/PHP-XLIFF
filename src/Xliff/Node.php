<?php
/**
 * PHP XLIFF Parser
 * https://github.com/automattic/PHP-XLIFF.
 *
 * A PHP library designed to help with reading and writing XLIFF documents. For more information on the current
 * (as of April 2005) XLIFF specification, see:
 *
 * http://docs.oasis-open.org/xliff/xliff-core/v2.0/os/xliff-core-v2.0-os.html
 */

/*
    The MIT License (MIT)

    Copyright (c) 2015 Automattic, Inc

    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
    documentation files (the "Software"), to deal in the Software without restriction, including without limitation
    the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
    and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in all copies or substantial portions
    of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING
    BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
    NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
    DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

namespace Timrross\Xliff;

use DOMDocument;

/**
 * Parent class for nodes in the XLIFF document.
 */
class Node
{
    protected $tag_name_to_class_mapping = array(
        'xliff' => 'Document',
        'file' => 'File',
        'skeleton' => 'Skeleton',
        'group' => 'Group',
        'unit' => 'Unit',
        'segment' => 'Segment',
        'ignorable' => 'Ignorable',
        'notes' => 'Notes',
        'note' => 'Note',
        'originalData' => 'OriginalData',
        'data' => 'Data',
        'source' => 'Node',
        'target' => 'Node',
    );

    /**
     * Holds element's attributes.
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * Holds child nodes that can be repeated inside this node.
     * For example, an xliff document can have multiple "file" nodes.
     *
     * @var Array[tag-name][0..n]=XliffNode
     */
    protected $containers = array();

    /**
     * Indicate which child nodes are supported.
     *
     * @var Array[tag-name]=>Xliff Class
     */
    protected $supported_containers = array();

    /**
     * Holds child nodes that can be presented only once inside this node.
     * For example, "trans-unit" element can have only one "source" node.
     *
     * @var Array[tag-name]=XliffNode
     */
    protected $leaf_nodes = array();

    /**
     * Indicate which child nodes are supported.
     *
     * @var Array[tag-name]=>Xliff Class
     */
    protected $supported_leaf_nodes = array();

    /**
     * Node's text, NULL if none.
     *
     * @var string|null
     */
    protected $text_content;

    /**
     * Node's tag name.
     *
     * @var string
     */
    protected $tag_name = '';

    public function __construct($tag_name = null)
    {
        if (is_string($tag_name)) {
            $this->set_tag_name($tag_name);
        }

        foreach ($this->supported_containers as $name => $class) {
            $this->containers[$name] = array();
        }
    }

    /**
     * @return string
     */
    public function get_tag_name()
    {
        return isset($this->tag_name) ? $this->tag_name : false;
    }

    /**
     * @param string $tag_name
     *
     * @return XliffNode
     */
    public function set_tag_name($tag_name)
    {
        $this->tag_name = $tag_name;

        return $this;
    }

    /**
     * Returns the attribute value, FALSE if attribute missing.
     *
     * @param string $name
     *
     * @return Ambigous <boolean, string> -
     */
    public function get_attribute($attribute_name)
    {
        return isset($this->attributes[$attribute_name]) ? $this->attributes[$attribute_name] : false;
    }

    /**
     * Sets an attribute.
     *
     * @param string $name
     * @param string $value
     *
     * @throws Exception
     *
     * @return XliffNode
     */
    public function set_attribute($attribute_name, $value)
    {
        if (!is_string($value) && !is_numeric($value)) {
            throw new Exception('Attribute must be text!');
        }
        $this->attributes[$attribute_name] = trim((string) $value);

        return $this;
    }

    /**
     * Set multiple attributes from a key=>value array.
     *
     * @param array $attr_array
     *
     * @return XliffNode
     */
    public function set_attributes($attribute_array)
    {
        foreach ($attribute_array as $key => $value) {
            $this->set_attribute($key, $value);
        }

        return $this;
    }

    /**
     * @return Ambigous <string, NULL>
     */
    public function get_text_content()
    {
        return isset($this->text_content) ? $this->text_content : false;
    }

    /**
     * @param string $textContent
     *
     * @return XliffNode
     */
    public function set_text_content($text_content)
    {
        $this->text_content = $text_content;

        return $this;
    }

    /**
     * Append a new node to this element.
     *
     * @param XliffNode $node - node to append
     *
     * @return XliffNode - this node
     */
    public function append_node(Node $node)
    {
        $tag_name = $node->get_tag_name();
        if (isset($this->supported_containers[$tag_name])) {
            $this->containers[$tag_name][] = $node;
        } elseif (isset($this->supported_leaf_nodes[$tag_name])) {
            $this->leaf_nodes[$tag_name] = $node;
        } else {
            return false;
        }

        return $this;
    }

    /**
     * Allow calling $node->tag_name($new=FALSE)
     * Supports the following methods:.
     *
     * 1. $node->tag_name(TRUE) - create a new node for "tag_name" and return the new node
     * 2. $node->tag_name() - fetch the last added node for "tag_name", FALSE if none
     *
     * //On the following, notice that tag names are in plural formation...
     * 3. $node->tag_names() - return an array of tag_name nodes
     */
    public function __call($tag_name, $args)
    {
        $append = is_array($args) && $args[0] === true ? true : false;

        if (!empty($this->supported_containers[$tag_name])) {
            if ($append) {
                $class = '\\Timrross\\Xliff\\' . $this->supported_containers[$tag_name];
                $this->containers[$tag_name][] = new $class($tag_name);
            }

            return end($this->containers[$tag_name]);
        } elseif (!empty($this->supported_leaf_nodes[$tag_name])) {
            if ($append) {
                $class = '\\Timrross\\Xliff\\' . $this->supported_leaf_nodes[$tag_name];
                $this->leaf_nodes[$tag_name] = new $class();
                $this->leaf_nodes[$tag_name]->set_tag_name($tag_name);
            }

            return !empty($this->leaf_nodes[$tag_name]) ? $this->leaf_nodes[$tag_name] : false;
        }
        throw new Exception(get_class($this).' does not support '.$tag_name.' elements.');
    }

    /**
     * Export this node to a DOM object.
     *
     * @param DOMDocument $doc - parent DOMDocument must be provided
     *
     * @return DOMElement
     */
    public function to_DOM_element(DOMDocument $dom)
    {
        $element = $dom->createElement($this->get_tag_name());

        // set attributes on the new element
        foreach ($this->attributes as $name => $value) {
            $element->setAttribute($name, $value);
        }

        // build its immediate (leaf) children
        foreach ($this->leaf_nodes as $node) {
            $element->appendChild($node->to_DOM_element($dom));
        }

        // build tree structures below
        foreach ($this->containers as $container) {
            foreach ($container as $node) {
                $element->appendChild($node->to_DOM_element($dom));
            }
        }

        // insert any text content into the element
        $text = $this->get_text_content();
        if (is_string($text)) {
            $text_node = $dom->createTextNode($text);
            $element->appendChild($text_node);
        }

        return $element;
    }

    /**
     * Convert DOM element to Xliff_Node structure.
     *
     * @param DOMNode $element
     *
     * @throws Exception
     *
     * @return string|Xliff_Node
     */
    public static function from_DOM_element(DOMNode $element)
    {
        if ($element instanceof DOMText) {
            return $element->nodeValue;
        }

        // check if tag is supported
        if (self::$tag_name_to_class_mapping[$element->tagName]) {
            $class = self::$tag_name_to_class_mapping[$element->tagName];
        } else {
            $class = 'Node';
        }

        $xliff_node = new $class($element->tagName);

        // import attributes
        foreach ($element->attributes as $attrNode) {
            $xliff_node->set_attribute($attrNode->nodeName, $attrNode->nodeValue);
        }

        // continue to nested nodes
        foreach ($element->childNodes as $child) {
            $result = self::from_DOM_Element($child);
            if (is_string($result)) {
                $xliff_node->set_text_content($result);
            } else {
                $xliff_node->append_node($result);
            }
        }

        return $xliff_node;
    }
}
