<?php

namespace Timrross\Xliff;

use DOMDocument;

/**
 * Wrapper class for Xliff documents.
 * Externally, you'll want to use this class.
 */
class Document extends Node
{
    const XMLNS = 'urn:oasis:names:tc:xliff:document:';
    const XLIFF_VER = '2.0';

    protected $tag_name = 'xliff';
    protected $version;
    protected $srcLang;
    protected $trgLang;
    protected $supported_containers = array(
        'file' => 'File',
    );

    public function __construct()
    {
        parent::__construct();
        $this->version = self::XLIFF_VER;
    }

    public function set_source_locale($locale)
    {
        $this->srcLang = $locale;

        return $this;
    }

    public function set_target_locale($locale)
    {
        $this->trgLang = $locale;

        return $this;
    }

    /**
     * Convert in-memory XLIFF representation to DOMDocument.
     *
     * @return DOMDocument
     */
    public function to_DOM()
    {
        $dom = new DOMDocument();
        $dom->formatOutput = true;

        // create the root xliff element w/all children
        $xliff_dom_element = $this->to_DOM_element($dom);

        // set some attributes on the xliff element
        $xliff_dom_element->setAttribute('xmlns', self::XMLNS.$this->version);
        $xliff_dom_element->setAttribute('version', $this->version);
        $xliff_dom_element->setAttribute('srcLang', $this->srcLang);
        $xliff_dom_element->setAttribute('trgLang', $this->trgLang);

        // append the whole enchilada to the DOM
        $dom->appendChild($xliff_dom_element);

        return $dom;
    }

    /**
     * Build in-memory XLIFF representation from DOMDocument.
     *
     * @param DOMDocument $dom
     *
     * @throws Exception
     *
     * @return Xliff_Document
     */
    public static function from_DOM(DOMDocument $dom)
    {
        if (!isset($dom->documentElement) || $dom->documentElement->tagName !== 'xliff') {
            throw new Exception('Not an XLIFF document');
        }

        return self::fromDOMElement($dom->documentElement);
    }
}
